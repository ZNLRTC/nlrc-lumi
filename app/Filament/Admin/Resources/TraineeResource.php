<?php

namespace App\Filament\Admin\Resources;

use Filament\Tables;
use App\Models\Exams\Exam;
use App\Models\Flag\Flag;
use App\Models\Grouping\Group AS GroupModel;
use App\Models\Observer;
use App\Models\Trainee;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use App\Enums\TraineesEducation;
use App\Filament\Clusters\Exams;
use App\Filament\Admin\Resources\TraineeResource\Pages\EditTrainee;
use App\Filament\Admin\Resources\TraineeResource\Pages\ViewTrainee;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use App\Enums\TraineesMaritalStatus;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Auth;
use App\Enums\TraineesWorkExperience;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group as InfolistGroup;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Filament\Resources\RelationManagers\RelationGroup;
use App\Filament\Admin\Resources\TraineeResource\Pages;
use App\Filament\Admin\Resources\TraineeResource\Pages\TraineesProgressPage;
use App\Filament\Admin\Resources\TraineeResource\RelationManagers\ExamsRelationManager;
use App\Filament\Admin\Resources\TraineeResource\RelationManagers\MeetingsRelationManager;
use App\Filament\Admin\Resources\FlagResource\RelationManagers\FlagTraineesRelationManager;
use App\Filament\Admin\Resources\GroupResource\RelationManagers\GroupTraineesRelationManager;
use App\Filament\Admin\Resources\TraineeResource\RelationManagers\ExamAttemptsRelationManager;
use App\Filament\Admin\Resources\TraineeResource\RelationManagers\TraineesVerificationRequestRelationManager;
use App\Filament\Admin\Resources\DocumentTraineeResource\RelationManagers\DocumentsRelationManager;
use App\Models\Flag\FlagTrainee;
use Filament\Forms\Components\DatePicker;

class TraineeResource extends Resource
{
    protected static ?string $model = Trainee::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $activeNavigationIcon = 'heroicon-s-identification';

    public static function getEloquentQuery(): Builder
    {
        return Auth::user()->hasRole('Observer') ? parent::getEloquentQuery()->getGroupsByObserverAgency() : parent::getEloquentQuery();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Details')
                    ->columns(['md' => 2, 'lg' => 3])
                    ->schema([
                        Placeholder::make('first_name')
                            ->extraAttributes(['class' => 'uppercase'])
                            ->content(fn (Trainee $trainee): ?string => $trainee->first_name),
                        Placeholder::make('middle_name')
                            ->extraAttributes(['class' => 'uppercase'])
                            ->content(fn (Trainee $trainee): ?string => $trainee->middle_name),
                        Placeholder::make('last_name')
                            ->extraAttributes(['class' => 'uppercase'])
                            ->content(fn (Trainee $trainee): ?string => $trainee->last_name),
                        Placeholder::make('occupation')
                            ->content(fn (Trainee $trainee): ?string => $trainee->occupation),
                        Placeholder::make('field_of_work')
                            ->content(fn (Trainee $trainee): ?string => $trainee->field_of_work),
                        Placeholder::make('work_experience')
                            ->content(fn (Trainee $trainee): ?string => TraineesWorkExperience::formLabel($trainee->work_experience)),
                        Placeholder::make('date_of_birth')
                            ->extraAttributes(['title' => 'MM/DD/YYYY'])
                            ->content(fn (Trainee $trainee): ?string => Carbon::parse($trainee->date_of_birth)->format('F j, Y')),
                        Placeholder::make('sex')
                            ->content(fn (Trainee $trainee): ?string => ucfirst($trainee->sex)),
                        Placeholder::make('country_of_citizenship_id')
                            ->label('Citizenship / Nationality')
                            ->content(fn (Trainee $trainee): ?string => $trainee->countryOfCitizenship?->nationality),
                        Placeholder::make('timezone')
                            ->label('Timezone')
                            ->content(fn (Trainee $trainee): ?string => $trainee->user->timezone),
                        Placeholder::make('marital_status')
                            ->content(fn (Trainee $trainee): ?string => TraineesMaritalStatus::formLabel($trainee->marital_status)),
                        Placeholder::make('education')
                            ->content(fn (Trainee $trainee): ?string => TraineesEducation::formLabel($trainee->education)),
                        Placeholder::make('verified_requests')
                            ->label('Is trainee verified with their info?')
                            ->content(function (Trainee $trainee): ?string {
                                $trainee_verification_requests = $trainee->verified_requests;

                                if ($trainee_verification_requests->isNotEmpty()) {
                                    $most_recent_verification_request = $trainee_verification_requests->last();

                                    return $most_recent_verification_request->is_checked_by_staff == 1 && $most_recent_verification_request->is_verified == 1 ?
                                    'Verified' : 'Not yet verified';
                                } else {
                                    return 'This trainee has not made any verification requests yet';
                                }
                            })
                    ]),
                Section::make('Contact Information')
                    ->collapsed()
                    ->persistCollapsed()
                    ->columns(['md' => 2])
                    ->schema([
                        ViewField::make('profile_photo_path_image')
                            ->dehydrated(false)
                            ->view('filament.custom.trainee-profile-photo-path-field'),
                        Placeholder::make('email')
                            ->content(fn (Trainee $trainee): ?string => strtolower($trainee->user->email)),
                        Placeholder::make('address')
                            ->content(fn (Trainee $trainee): ?string => $trainee->address),
                        Placeholder::make('phone_number')
                            ->content(fn (Trainee $trainee): ?string => $trainee->phone_number),
                        Placeholder::make('country_of_residence_id')
                            ->label('Country of residence')
                            ->content(fn (Trainee $trainee): ?string => $trainee->countryOfResidence?->name)
                    ]),
                Section::make('Training')
                    ->columns(['md' => 2])
                    ->schema([
                        Select::make('agency_id')
                            ->label('Agency')
                            ->required()
                            ->selectablePlaceholder(false)
                            ->relationship('agency', 'name'),
                        ToggleButtons::make('active')
                            ->boolean()
                            ->grouped()
                            ->label('This trainee account is active')
                            ->helperText('Only choose "No" if the trainee has quit or has been deployed. Hover over the question mark for more info.')
                            ->hintIcon('heroicon-s-question-mark-circle', tooltip: 'Setting this to "No" removes the trainee from their current group and drops them from training. Switching it back to "Yes" does not return the trainee to their previous group automatically. Do NOT use this if the trainee is on hold or waiting for a group assignment. Add a new flag instead.')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateDescription('Maybe you have a filter active?')
            ->recordUrl(null)
            ->columns([
                IconColumn::make('active')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-s-check-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable(),
                TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->copyable()
                    ->copyMessage('Email copied to clipboard')
                    ->searchable(),
                TextColumn::make('activeGroup.group.group_code')
                    ->label('Group'),
                TextColumn::make('date_deployment')
                    ->date()
                    ->label('Deployment Date'),
                TextColumn::make('agency.name')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('countryOfResidence.name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('countryOfCitizenship.name')
                    ->label('Citizenship / Nationality')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->searchPlaceholder('Search (names, email, group)')
            ->filters([
                SelectFilter::make('group_id')
                    ->label('Filter by group')
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->isActive()
                                ->whereHas('activeGroups', fn (Builder $query) =>
                                    $query->whereNot('name', 'Kyl mä hoidan')
                                        ->where('groups.id', $data['value'])
                                );
                        }
                    })
                    ->options(function () {
                        $base_query = GroupModel::selectRaw('groups.id, CONCAT(group_types.code, name) AS name, groups.active')
                            ->join('group_types', 'group_types.id', 'groups.group_type_id')
                            ->isActive()
                            ->whereNot('name', 'Kyl mä hoidan');

                        if (Auth::user()->hasRole('Observer')) {
                            $observer = Observer::select(['agency_id'])->where('user_id', Auth::user()->id)
                                ->first();

                            return $base_query->clone()
                                ->where('agency_id', $observer->agency_id)
                                ->orderBy('name', 'ASC')
                                ->get()
                                ->pluck('name', 'id');
                        } else {
                            return $base_query->clone()
                                ->orderBy('name', 'ASC')
                                ->get()
                                ->pluck('name', 'id');
                        }
                    }),
                SelectFilter::make('agency')
                    ->label('Filter by agency')
                    ->relationship('agency', 'name')
                    ->hidden(fn () => Auth::user()->hasRole('Observer')),
                SelectFilter::make('status')
                    ->label('Filter by flag')
                    ->relationship('status', 'name'),
                SelectFilter::make('work_experience')
                    ->label('Filter by work experience')
                    ->options(TraineesWorkExperience::class),
                SelectFilter::make('marital_status')
                    ->label('Filter by marital status')
                    ->options(TraineesMaritalStatus::class),
                SelectFilter::make('education')
                    ->label('Filter by education')
                    ->options(TraineesEducation::class),
                Filter::make('filter_deployed')
                    ->toggle()
                    ->label('Filter deployed trainees')
                    ->query(fn (Builder $query): Builder => $query->deployedWhen('past'))
                    ->hidden(fn (): bool => Auth::user()->hasRole('Observer')),
                Filter::make('filter_deployed_upcoming')
                    ->toggle()
                    ->label('Filter upcoming trainees to be deployed')
                    ->query(fn (Builder $query): Builder => $query->deployedWhen('future'))
                    ->hidden(fn (): bool => Auth::user()->hasRole('Observer'))
            ], layout: FiltersLayout::Dropdown)
            ->actions([
                EditAction::make(),
                ViewAction::make()
            ])
            ->selectCurrentPageOnly()
            ->checkIfRecordIsSelectableUsing(fn (Trainee $record) => $record->active)
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('Add new flag')
                        ->icon('heroicon-m-flag')
                        ->modalDescription('Set the latest flag for selected trainees')
                        ->action(function (Collection $records, array $data) {
                            $flag = Flag::findOrFail($data['flag']);

                            $records->each(function (Trainee $record) use ($data) {
                                if ($data['flag'] == 1 && $data['date_deployment']) {
                                    $record->date_deployment = $data['date_deployment'];
                                    $record->save();
                                }

                                $record->status()->attach($data['flag'], [
                                    'flagged_by_id' => Auth::user()->id,
                                    'active' => 1,
                                    'description' => $data['description'],
                                    'internal_notes' => $data['internal_notes']
                                ]);
                            });

                            Notification::make()
                                ->title('Flag added')
                                ->body($flag->name. ' flag added to ' .$records->count(). ' trainees')
                                ->success()
                                ->send();
                        })
                        ->form([
                            Select::make('flag')
                                ->label('Flag')
                                ->live()
                                ->searchable()
                                ->default(1)
                                ->required()
                                ->options(fn () =>
                                    Flag::select(['id', 'name'])
                                        ->isActive()
                                        ->orderBy('name', 'ASC')
                                        ->get()
                                        ->pluck('name', 'id')
                                )
                                ->helperText(function (Get $get): string {
                                    $extra_description = '.';

                                    if ($get('flag')) {
                                        $flag = Flag::findOrFail($get('flag'));

                                        if (in_array($flag->name, ['Deployed', 'On hold', 'Quit', 'Active', 'Inactive'])) {
                                            $extra_description = '. This will affect the ' .$flag->name. ' trainee count on the Groups admin panel';
                                        }
                                    }

                                    return 'Only active flags are shown here' .$extra_description;
                                }),
                            Placeholder::make('visible_to_trainee')
                                ->label('Is visible to trainee?')
                                ->helperText('Whether this is shown to the trainee dashboard or not')
                                ->content(function (Get $get): string {
                                    if ($get('flag')) {
                                        $flag = Flag::findOrFail($get('flag'));

                                        return $flag->visible_to_trainee == 1 ? 'Visible' : 'Not Visible';
                                    } else {
                                        return 'Not Visible';
                                    }
                                }),
                            DatePicker::make('date_deployment')
                                ->label('Date of deployment')
                                ->native(false)
                                ->afterOrEqual('today')
                                ->required()
                                ->visible(function (Get $get): bool {
                                    if ($get('flag')) {
                                        $flag = Flag::find($get('flag'));

                                        return $flag->name == 'Deployed';
                                    } else {
                                        return false;
                                    }
                                }),
                            Textarea::make('description')
                                ->rows(2)
                                ->placeholder('Write any comment here that will be shown to the trainees.')
                                ->maxLength(255),
                            Textarea::make('internal_notes')
                                ->rows(2)
                                ->placeholder('Write any notes here that will only be shown to admins.')
                                ->maxLength(255),
                        ])
                        ->hidden(fn (): bool => Auth::user()->hasRole('Observer')),
                    BulkAction::make('Transfer to new group')
                        ->icon('heroicon-m-table-cells')
                        ->action(function (Collection $records, array $data) {
                            $group = GroupModel::findOrFail($data['group_id']);

                            $records->filter(function (Trainee $record) use ($data) {
                                return $record->activeGroup->group->id != $data['group_id'];
                            })->each(function (Trainee $record) use ($data) {
                                $record->group()->update(['group_trainee.active' => false]);

                                $record->group()->attach($data['group_id'], [
                                    'added_by' => Auth::user()->id,
                                    'notes' => $data['notes'],
                                    'active' => 1
                                ]);
                            });

                            Notification::make()
                                ->title('Transferred to group')
                                ->body('The trainees were transferred to ' .$group->group_code)
                                ->success()
                                ->send();
                        })
                        ->form([
                            Select::make('group_id')
                                ->options(fn () =>
                                    GroupModel::selectRaw('groups.id, CONCAT(group_types.code, name) AS name, groups.active')->isActive()
                                        ->join('group_types', 'group_types.id', 'groups.group_type_id')
                                        ->orderBy('name', 'ASC')
                                        ->get()
                                        ->pluck('name', 'id')
                                )
                                ->searchable()
                                ->loadingMessage('Loading groups...')
                                ->required()
                                ->label('New group'),
                            TextInput::make('notes')
                                ->required()
                                ->label('Reason for the move'),
                        ])
                        ->hidden(fn (): bool => Auth::user()->hasRole('Observer')),
                    BulkAction::make('assignExam')
                        ->label('Assign exam permissions')
                        ->modalDescription('This will allow the trainee to take the selected test, assessment, or exam. Instructors will be able to search for the the trainee\'s name and will be able to grade them. You cannot undo this in bulk. If you make a mistake, you have to revoke permissions manually in every trainee\'s profile.')
                        ->modalIcon('heroicon-o-document-text')
                        ->modalSubmitActionLabel('Allow selected trainees to take the selected exam')
                        ->icon('heroicon-o-document-text')
                        ->color('primary')
                        ->form([
                            Select::make('exam_id')
                                ->label('Select exam')
                                ->options(
                                    Exam::where('date', '>', Carbon::now())
                                        ->orWhereNull('date')
                                        ->pluck('name', 'id')
                                )
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            foreach ($records as $record) {
                                $record->exams()->syncWithoutDetaching([$data['exam_id']]);
                            }

                            Notification::make()
                                ->title('Exam permissions assigned')
                                ->body('The selected trainees have been assigned to the exam successfully.')
                                ->success()
                                ->send();
                        })
                        ->hidden(fn (): bool => Auth::user()->hasRole('Observer')),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Trainee Details')
                    ->schema([
                        Split::make([
                            Grid::make(2)
                                ->schema([
                                    InfolistGroup::make([
                                        TextEntry::make('full_name'),
                                        TextEntry::make('agency.name'),
                                        TextEntry::make('date_of_birth')
                                            ->badge()
                                            ->date()
                                            ->color('success'),
                                        TextEntry::make('sex'),
                                        TextEntry::make('countryOfCitizenship.nationality')
                                            ->label('Citizenship / Nationality'),
                                        IconEntry::make('active')
                                            ->label('Is active?')
                                            ->color(fn (int $state): string => match ($state) {
                                                0 => 'danger',
                                                1 => 'success'
                                            })
                                            ->icon(fn (int $state): string => match ($state) {
                                                0 => 'heroicon-o-x-circle',
                                                1 => 'heroicon-o-check-circle'
                                            }),
                                    ]),
                                    InfolistGroup::make([
                                        TextEntry::make('date_of_training_start')
                                            ->badge()
                                            ->date()
                                            ->color('info'),
                                        TextEntry::make('occupation')
                                            ->default('N/A'),
                                        TextEntry::make('field_of_work')
                                            ->default('N/A'),
                                        TextEntry::make('work_experience')
                                            ->default('N/A'),
                                        TextEntry::make('marital_status')
                                            ->default('N/A'),
                                        TextEntry::make('education')
                                            ->default('N/A')
                                    ])
                                ]),
                            ImageEntry::make('user.profile_photo_url')
                                ->circular()
                                ->hiddenLabel()
                                ->grow(false)
                        ])
                    ]),
                InfolistSection::make('Contact Details')
                    ->schema([
                        TextEntry::make('user.email')
                            ->label('Email')
                            ->default('N/A'),
                        TextEntry::make('address')
                            ->default('N/A'),
                        TextEntry::make('phone_number')
                            ->default('N/A'),
                        TextEntry::make('countryOfResidence.name'),
                        TextEntry::make('user.timezone')
                            ->label('Timezone')
                    ])
                    ->columns(2)
            ]);
    }

    public static function getRecordSubNavigation($page): array
    {
        return $page->generateNavigationItems([
            TraineesProgressPage::class,
            EditTrainee::class,
            ViewTrainee::class
        ]);
    }

    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class,
            RelationGroup::make('Exams', [
                ExamsRelationManager::class,
                ExamAttemptsRelationManager::class,
            ]),
            FlagTraineesRelationManager::class,
            GroupTraineesRelationManager::class,
            MeetingsRelationManager::class,
            TraineesVerificationRequestRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrainees::route('/'),
            'create' => Pages\CreateTrainee::route('/create'),
            'edit' => EditTrainee::route('/{record}/edit'),
            'view' => ViewTrainee::route('/{record}'),
            'progress' => TraineesProgressPage::route('/{record}/progress'),
        ];
    }
}
