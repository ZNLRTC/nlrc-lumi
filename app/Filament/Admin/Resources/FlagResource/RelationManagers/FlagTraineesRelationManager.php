<?php

namespace App\Filament\Admin\Resources\FlagResource\RelationManagers;

use App\Models\Flag\Flag;
use App\Models\Flag\FlagTrainee;
use App\Models\Trainee;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\RelationManagers\RelationManager;

class FlagTraineesRelationManager extends RelationManager
{
    protected static string $relationship = 'flagsOfTrainee';

    protected static ?string $title = 'Flags';

    public array $flags_with_stats = ['Deployed', 'On hold', 'Quit', 'Active', 'Inactive'];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('flag_id')
                    ->relationship(name: 'flag', titleAttribute: 'name')
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

                        if ($get('flag_id')) {
                            $flag = Flag::findOrFail($get('flag_id'));

                            if (in_array($flag->name, $this->flags_with_stats)) {
                                $extra_description = '. This will affect the ' .$flag->name. ' trainee count on the Groups admin panel';
                            }
                        }

                        return 'Only active flags are shown here' .$extra_description;
                    }),
                Placeholder::make('flag.visible_to_trainee')
                    ->label('Is visible to trainee?')
                    ->helperText('Whether this is shown to the trainee dashboard or not')
                    ->content(function (Get $get): string {
                        if ($get('flag_id')) {
                            $flag = Flag::findOrFail($get('flag_id'));

                            return $flag->visible_to_trainee == 1 ? 'Visible' : 'Not Visible';
                        } else {
                            return 'Not Visible';
                        }
                    }),
                DatePicker::make('date_deployment')
                    ->label('Date of deployment')
                    ->helperText('Format: DD/MM/YYYY')
                    ->native(false)
                    ->afterOrEqual('today')
                    ->required()
                    ->rules(['date', 'date_format:Y-m-d'])
                    ->formatStateUsing(function (?FlagTrainee $record) {
                        if ($record && $record->flag) {
                            if ($record->flag->name) {
                                return $record->trainee->date_deployment;
                            }
                        }
                    })
                    ->visible(function (Get $get): bool {
                        if ($get('flag_id')) {
                            $flag = Flag::find($get('flag_id'));

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
                Toggle::make('active')
                    ->hidden(function (string $operation, ?FlagTrainee $record): bool {
                        if ($record) {
                            $most_recent_flag = Trainee::select(['id'])->where('id', $this->getOwnerRecord()->id)
                                ->first()
                                ->flagsOfTrainee
                                ->last();
                        }

                        // Ensures that the latest flag is always active
                        return $operation === 'create' || $record->id == $most_recent_flag->id;
                    })
                    ->helperText(fn (): string =>
                        'Turn this off if the matter has been resolved or the flag no longer affects the trainee'
                    )
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->emptyStateHeading('This trainee has never been flagged.')
            ->emptyStateDescription('You can add a new flag using the button below.')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Add new flag')
                    ->button()
            ])
            ->description('Set flags/statuses for this trainee. If you made a mistake, you can delete flags within 5 minutes of their creation.')
            ->columns([
                Split::make([
                    IconColumn::make('active')
                        ->boolean()
                        ->trueIcon('heroicon-s-exclamation-circle')
                        ->falseIcon('heroicon-o-x-circle')
                        ->color(fn (string $state): string => match ($state) {
                            '0' => 'gray',
                            '1' => 'success',
                            default => 'gray',
                        })
                        ->grow(false),
                    TextColumn::make('flag.name')
                        ->label('Flag')
                        ->grow(false),
                    Stack::make([
                        TextColumn::make('internal_notes')
                            ->label('Internal notes, not visible to the trainee')
                            ->icon('heroicon-o-eye-slash')
                            ->tooltip('This information is internal and not shown to the trainee'),
                        TextColumn::make('description')
                            ->label('Information shown to the trainee')
                            ->icon('heroicon-s-eye')
                            ->tooltip('This information is shown to the trainee'),
                    ]),
                    Stack::make([
                        TextColumn::make('flagged_by.name')
                            ->formatStateUsing(function ($record) {
                                if ($record->flagged_by_system) {
                                    return 'System';
                                } else if (is_null($record->flagged_by)) {
                                    return 'Deleted user';
                                } else {
                                    return $record->flagged_by->name ?? 'Deleted user';
                                }
                            })
                            ->icon(function ($record) {
                                if ($record->flagged_by_system) {
                                    return 'heroicon-o-computer-desktop';
                                } else if (is_null($record->flagged_by)) {
                                    return 'heroicon-o-user-minus';
                                } else {
                                    return 'heroicon-o-user';
                                }
                            })
                            ->tooltip('Staff member who added/updated the flag')
                            ->extraAttributes(fn ($state) => ['class' => $state == 'Deleted user' ? 'italic' : ''])
                            ->default('Deleted user'),
                        TextColumn::make('created_at')
                            ->date()
                            ->icon('heroicon-o-clock')
                            ->tooltip('Date the flag was added')
                    ])
                    ->grow(false)
                ]),
            ])
            ->groups([
                Group::make('flag.flagType.name')
                    ->label('Teams'),
                Group::make('created_at')
                    ->label('Date added')
                    ->date(),
                ])
                ->defaultGroup('flag.flagType.name')
                ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('user')
                    ->options(User::select(['id', 'name'])
                        ->whereRaw('role_id IN(1, 2)')
                        ->get()
                        ->pluck('name', 'id')
                    )
                    ->query(function (Builder $query, array $data) {
                        // REF: https://v2.filamentphp.com/tricks/use-selectfilter-on-distant-relationships
                        if (!empty($data['value'])) {
                            return $query->whereHas('flagged_by',
                                fn (Builder $query) => $query->where('id', '=', (int) $data['value'])
                            );
                        }
                    })
                    ->label('Staff Name'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add new flag')
                    ->modalHeading('Add new flag')
                    ->modalDescription('Set the latest flag for this trainee')
                    ->createAnother(false)
                    ->mutateFormDataUsing(function (array $data): array {
                        if ($data['flag_id'] == 1 && $data['date_deployment']) {
                            $trainee = Trainee::find($this->getOwnerRecord()->id);
                            $trainee->date_deployment = $data['date_deployment'];

                            $trainee->save();
                        }

                        $data['flagged_by_id'] = Auth::user()->id;

                        return $data;
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(fn (FlagTrainee $record): string => 'Edit ' .$record->flag->name. ' flag')
                    ->mutateFormDataUsing(function (array $data): array {
                        if ($data['flag_id'] == 1 && $data['date_deployment']) {
                            $trainee = Trainee::find($this->getOwnerRecord()->id);
                            $trainee->date_deployment = $data['date_deployment'];

                            $trainee->save();
                        }

                        $data['flagged_by_id'] = Auth::user()->id;

                        return $data;
                    }),
                DeleteAction::make()
                    ->modalDescription(fn (FlagTrainee $record): string => 'Are you sure you want to delete this flag? ' .($record->active == true ? 'This flag is currently active and if this is deleted, the most recent flag prior to this will be set to active.' : ''))
                    ->modalHeading(fn (FlagTrainee $record): string => 'Delete ' .$record->flag->name. ' flag?')
                    ->visible(fn (FlagTrainee $record): bool => Carbon::now() < Carbon::parse($record->created_at)->addMinutes(5))
            ])
            ->bulkActions([
                //
            ]);
    }
}
