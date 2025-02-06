<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\Country;
use App\Models\User;
use Closure;
use DateTimeZone;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $activeNavigationIcon = 'heroicon-s-users';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereNot('id', Auth::user()->id);
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('User information')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->unique(ignoreRecord: true)
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->required()
                        ->maxLength(255)
                        ->visibleOn('create'),
                    Select::make('role_id')
                        ->relationship('role', 'name')
                        ->default(4)
                        ->rules([
                            fn (string $operation): Closure => function (string $attribute, $value, Closure $fail) use ($operation) {
                                $admin_users = User::select(['role_id'])->where('role_id', 1)
                                    ->get();

                                if ($operation === 'edit' && $admin_users->count() < 4 && $value != 1) {
                                    $fail('The system should have a minimum of 1 - 3 admins');
                                }
                            },
                        ]),
                    Select::make('timezone')
                        ->searchable()
                        ->required()
                        ->options(fn () => collect(DateTimeZone::listIdentifiers(DateTimeZone::ALL))
                            ->mapWithKeys(fn ($timezone) => [$timezone => $timezone])
                            ->toArray()
                        ),
                    Forms\Components\Toggle::make('restricted')
                        ->label('Prevent the user from logging in')
                        ->helperText('If this is on, the user is not allowed to log in and makes the account "inactive"')
                        ->onColor('success')
                        ->default(false),
                    Toggle::make('send_email')
                        ->label('Send email to the user')
                        ->helperText('If this is on, you may choose to send an email to the user to notify them that an account is created with the email provided here')
                        ->onColor('success')
                        ->default(false),
                    Forms\Components\Textarea::make('notes')
                        ->columnSpanFull(),
                ]),

            Section::make('Trainee information')
                ->hidden(fn (User $user, $context): bool => $user->role_id !== 4 || $context === 'create')
                ->description('This section is for users who are trainees')
                ->relationship('trainee')
                ->schema([
                    Fieldset::make('Details')
                    ->columns(3)
                    ->schema([
                        TextInput::make('first_name')
                            ->required()
                            ->label('First name')
                            ->maxLength(100),
                        TextInput::make('middle_name')
                            ->label('Middle name')
                            ->maxLength(100),
                        TextInput::make('last_name')
                            ->label('Last name')
                            ->maxLength(100),
                        DatePicker::make('date_of_birth')
                            ->native(true)
                            ->label('Date of Birth')
                            ->hint('MM/DD/YYYY')
                            ->before('18 years ago')
                            ->after('70 years ago'),
                        Select::make('country_of_citizenship_id')
                            ->label('Citizenship')
                            ->options(fn () => Country::orderBy('nationality')->pluck('nationality', 'id')->toArray()),
                        Radio::make('sex')
                            ->options([
                                'female' => 'Female',
                                'male' => 'Male',
                            ])
                            ->inline()
                            ->inlineLabel(false),
                    ]),

                Fieldset::make('Contact')
                    ->columns(2)
                    ->schema([
                        TextInput::make('other_email')
                            ->label('Other email addresses'),
                        TextInput::make('address')
                            ->label('Address'),
                        Select::make('country_of_residence_id')
                            ->label('Country of current residence')
                            ->options(fn () => Country::pluck('name', 'id')->toArray()),
                        TextInput::make('phone_number')
                            ->tel(),
                    ]),

                Fieldset::make('Training')
                    ->schema([
                        Select::make('agency_id')
                            ->label('Agency')
                            ->required()
                            ->relationship('agency', 'name'),
                        Toggle::make('active')
                            ->label('Active')
                            ->helperText('Only toggle this off if the trainee has quit or has been deployed.')
                            ->hintIcon('heroicon-s-question-mark-circle', tooltip: 'Toggling this off removes the trainee from their current group and drops them from training. Turning this on does not return the trainee to their previous group automatically. Do NOT use this if the trainee is on hold or waiting for a group assignment. Add a new status instead.')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('timezone')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('role.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('trainee.date_of_birth')
                    ->label('Date of Birth')
                    ->date(),
                IconColumn::make('restricted')
                    ->boolean()
                    ->tooltip('Whether the user is allowed to login')
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'success',
                        '1' => 'danger'
                    }),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->relationship('role', 'name'),
                TernaryFilter::make('restricted')
                    ->label('Restricted/unrestricted accounts')
                    ->placeholder('All')
                    ->trueLabel('Users that cannot login')
                    ->falseLabel('Users that can login')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
