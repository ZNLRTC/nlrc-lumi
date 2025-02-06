<?php

namespace App\Filament\Admin\Pages;

use Exception;
use DateTimeZone;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Auth\Authenticatable;

class EditOwnProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.edit-own-profile';

    protected static ?string $title = 'Your profile';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $profileData = [];
    public ?array $passwordData = [];
    public ?string $selectedTimezone = null;

    protected function getForms(): array
    {
        return [
            'editProfileForm',
            'editPasswordForm',
        ];
    }

    public function mount(): void
    {
        $this->selectedTimezone = Auth::user()->timezone;
        $this->fillForms();
    }

    protected function fillForms(): void
    {
        $data = $this->getUser()->attributesToArray();

        $this->editProfileForm->fill($data);
        $this->editPasswordForm->fill();
    }

    public function editProfileForm(Form $form): Form
    {
        return $form 
            ->schema([
                Section::make('Profile information')
                    ->aside()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->helperText('In contexts where your name is shown to trainees, the word "NLRC" is automatically prepended, so you should not add it here.')
                            ->required()
                            ->columnSpanFull(),
                        FileUpload::make('website_photo_path')
                            ->label('Profile photo')
                            ->helperText('This is currently not shown to trainees. Image you upload will be resized to 100 × 100 pixels. Remember to save changes after uploading.')
                            ->image()
                            ->avatar()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('100')
                            ->imageResizeTargetHeight('100')
                            ->disk('avatars_website'),
                        Select::make('timezone')
                            ->live()
                            ->afterStateUpdated(fn ($state) => $this->selectedTimezone = $state)
                            ->helperText(fn () => 'It is currently around ' . Carbon::now($this->selectedTimezone)->format('G:i ~ g:i A') . ' in the timezone you selected. Choose a different timezone if that is not your current time. Choose "Asia/Manila" if you work in the Philippines.')
                            ->options($this->getTimezoneOptions()),
                        ]),
                ])
            ->model($this->getUser())
            ->statePath('profileData');
    }

    public function editPasswordForm(Form $form): Form
    {
        return $form
            ->schema([
            Section::make('Change password')
                ->aside()
                ->description('Make sure this is secure and different from your Google Workspace, OnceHub, and FreeScout passwords.')
                ->schema([
                    TextInput::make('Current password')
                        ->required()
                        ->password()
                        ->revealable()
                        ->currentPassword(),
                    TextInput::make('password')
                        ->label('New password')
                        ->required()
                        ->password()
                        ->revealable()
                        ->rule(Password::default())
                        ->autocomplete('new-password')
                        ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                        ->live(debounce: 500)
                        ->same('passwordConfirmation'),
                    TextInput::make('passwordConfirmation')
                        ->label('Re-type new password')
                        ->password()
                        ->revealable()
                        ->required()
                        ->dehydrated(false),
                    ]),
            ])
            ->model($this->getUser())
            ->statePath('passwordData');
    }

    protected function getUser(): Authenticatable & Model
    {
        $user = Filament::auth()->user();

        if (! $user instanceof Model) {
            throw new Exception('Oh no, this needs the authenticated user object to be an Eloquent model.');
        }

        return $user;
    }

    protected function getTimezoneOptions(): array
    {
        return collect(DateTimeZone::listIdentifiers(DateTimeZone::ALL))
            ->mapWithKeys(fn ($timezone) => [$timezone => $timezone])
            ->toArray();
    }

    protected function getUpdateProfileFormActions(): array
    {
        return [
            Action::make('updateProfileAction')
                ->label('Save changes')
                ->submit('updateProfile'),
        ];
    }

    protected function getUpdatePasswordFormActions(): array
    {
        return [
            Action::make('updatePasswordAction')
                ->label('Update password')
                ->submit('updatePassword'),
        ];
    }

    public function updateProfile(): void
    {
        $data = $this->editProfileForm->getState();

        $this->handleRecordUpdate($this->getUser(), $data);
        $this->sendSuccessNotification(); 
    }

    public function updatePassword(): void
    {
        $data = $this->editPasswordForm->getState();

        $this->handleRecordUpdate($this->getUser(), $data);

        if (request()->hasSession() && array_key_exists('password', $data)) {
            request()->session()->put(['password_hash_' . Filament::getAuthGuard() => $data['password']]);
        }

        $this->editPasswordForm->fill();
        $this->sendSuccessNotification(); 
    }

    private function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        return $record;
    }

    private function sendSuccessNotification(): void
    {
        Notification::make()
            ->success()
            ->title('Profile updated')
            ->send();
        }

}
