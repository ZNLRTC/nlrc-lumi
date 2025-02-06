<?php

namespace App\Filament\Imports;

use App\Enums\TraineesEducation;
use App\Enums\TraineesMaritalStatus;
use App\Enums\TraineesWorkExperience;
use App\Mail\ImportedNewAccountEmail;
use App\Models\Trainee;
use App\Models\User;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TraineeImporter extends Importer
{
    protected static ?string $model = Trainee::class;

    public string $current_email = ''; // Used to store trainee email before unsetting the key
    public string $password = '';

    public function getValidationMessages(): array
    {
        return [
            'sex.in' => 'The sex column must be any of the ff: f, female, m, or male.',
            'phone_number.regex' => 'The phone number column accepts all characters except letters.'
        ];
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('email') // Field in User model
                ->requiredMapping(),
            ImportColumn::make('agency') // agency_id
                ->relationship(resolveUsing: 'name')
                ->requiredMapping()
                ->rules(['required', 'exists:agencies,name']),
            ImportColumn::make('first_name')
                ->requiredMapping()
                ->rules(['required', 'min:2', 'max:255']),
            ImportColumn::make('middle_name')
                ->requiredMapping()
                ->rules(['nullable', 'min:2', 'max:255']),
            ImportColumn::make('last_name')
                ->requiredMapping()
                ->rules(['required', 'min:2', 'max:255']),
            ImportColumn::make('date_of_birth')
                ->requiredMapping()
                ->rules(['required', 'date', 'date_format:Y-m-d', 'before:today', 'after:1900-01-01']),
            ImportColumn::make('sex')
                ->requiredMapping()
                ->rules(['required', Rule::in(['female', 'male'])])
                ->castStateUsing(function (string $state): ?string {
                    if ($state == strtolower(trim('f'))) {
                        $state = 'female';
                    } else if ($state == strtolower(trim('m'))) {
                        $state = 'male';
                    }

                    $state = strtolower(trim($state));

                    return $state;
                }),
            ImportColumn::make('countryOfResidence') // country_of_residence_id
                ->relationship(resolveUsing: 'name')
                ->requiredMapping()
                ->rules(['required', 'exists:countries,name']),
            ImportColumn::make('countryOfCitizenship') // country_of_citizenship_id
                ->relationship(resolveUsing: 'nationality')
                ->requiredMapping()
                ->rules(['required', 'exists:countries,nationality']),
            ImportColumn::make('address')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('phone_number')
                ->requiredMapping()
                ->rules(['required', 'regex:/[^a-zA-Z]+$/', 'max:32']),
            ImportColumn::make('occupation')
                ->requiredMapping()
                ->rules(['required', 'min:2', 'max:64']),
            ImportColumn::make('field_of_work')
                ->requiredMapping()
                ->rules(['required', 'min:2', 'max:64']),
            ImportColumn::make('work_experience')
                ->requiredMapping()
                ->rules(['required', Rule::enum(TraineesWorkExperience::class)]),
            ImportColumn::make('marital_status')
                ->requiredMapping()
                ->rules(['required', Rule::enum(TraineesMaritalStatus::class)]),
            ImportColumn::make('education')
                ->requiredMapping()
                ->rules(['required', Rule::enum(TraineesEducation::class)]),
            /* TODO: Will comment for now
            ImportColumn::make('date_of_training_start')
                ->requiredMapping()
                ->rules(['required', 'date']),
            */
        ];
    }

    public function resolveRecord(): ?Trainee
    {
        // return Trainee::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        dump('In resolveRecord()...');
        dump('Email in iteration:', $this->data['email']);

        $this->current_email = $this->data['email'];
        $this->password = Str::password(10);
        $this->data['email'] = trim($this->data['email']);

        if ($this->data['email'] == '') {
            throw new RowImportFailedException('The email column is required.');
        }

        $has_user_with_email = User::query()
            ->where('email', $this->data['email'])
            ->first();

        if ($has_user_with_email) {
            throw new RowImportFailedException("The email {$this->data['email']} already exists!");
        } else {
            // The username part of the email address (any text before the @ symbol is limited to 64 characters)
            // If that text goes over 64 characters, the 1st condition always passes
            if (!filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new RowImportFailedException('The email column should be a valid email.');
            } else if (strlen($this->data['email']) >= 128) {
                throw new RowImportFailedException('The email column cannot exceed 128 characters.');
            } else if (!preg_match('/^(?!.*nlrc).*/', $this->data['email'])) {
                throw new RowImportFailedException('The email column should not contain "nlrc".');
            }

            $user = User::create([
                'name' => Str::random(10),
                'email' => Str::lower($this->data['email']),
                'password' => Hash::make($this->password),
                'role_id' => 4
            ]);

            unset($this->data['email']);
            unset($this->originalData['email']);

            // This variable is accessible by $this->record if it will be used in hooks
            return Trainee::create(['user_id' => $user->id]);
        }
    }


    protected function beforeValidate(): void
    {
        if ($this->data['work_experience']) {
            $sanitized_work_experience = strtolower(trim($this->data['work_experience']));
            $work_experience_index = TraineesWorkExperience::getEquivalentValueOfEnum($sanitized_work_experience);

            if ($work_experience_index) {
                $this->data['work_experience'] = $work_experience_index;
            }
        }

        if ($this->data['marital_status']) {
            $sanitized_marital_status = strtolower(trim($this->data['marital_status']));
            $marital_status_index = TraineesMaritalStatus::getEquivalentValueOfEnum($sanitized_marital_status);

            if ($marital_status_index) {
                $this->data['marital_status'] = $marital_status_index;
            }
        }

        if ($this->data['education']) {
            $sanitized_education = strtolower(trim($this->data['education']));
            $education_index = TraineesEducation::getEquivalentValueOfEnum($sanitized_education);

            if ($education_index) {
                $this->data['education'] = $education_index;
            }
        }

        dump('Final data (before validation):', $this->data);
    }

    protected function afterSave(): void
    {
        dump('In afterSave()...');

        $data = [
            'email' => $this->current_email,
            'agency' => $this->originalData['agency'],
            'password' => $this->password,
        ];

        dump($data);
        dump('==============================================');

        // Working with Mailtrap credentials
        // Queueing mails but is untested. Probably requires actual server to test
        /*
        Mail::to($this->current_email)
            ->queue(new ImportedNewAccountEmail($this->current_email, [
                'email' => $this->current_email,
                'agency' => $this->originalData['agency'],
                'password' => $this->password,
            ]));
        */
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your trainee import has completed and ' .number_format($import->successful_rows). ' ' .str('row')->plural($import->successful_rows). ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' .number_format($failedRowsCount). ' ' .str('row')->plural($failedRowsCount). ' failed to import.';
        }

        return $body;
    }
}
