<?php

namespace App\Filament\Admin\Resources;

use App\Enums\MeetingsOnCallsMeetingStatus;
use App\Filament\Admin\Resources\MeetingsOnCallResource\Pages;
use App\Filament\Admin\Resources\MeetingsOnCallResource\RelationManagers;
use App\Models\Meetings\MeetingsOnCall;
use App\Models\User;
use Closure;
use Illuminate\Support\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MeetingsOnCallResource extends Resource
{
    protected static ?string $model = MeetingsOnCall::class;

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $activeNavigationIcon = 'heroicon-s-video-camera';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->required()
                    ->label('Instructor')
                    ->live()
                    ->options(User::select(['id', 'name'])
                        ->where('role_id', 5)
                        ->orderBy('name', 'ASC')
                        ->get()
                        ->pluck('name', 'id')
                    )
                    ->columnSpan(['md' => 1]),
                Placeholder::make('user.timezone')
                    ->label('Instructor timezone')
                    ->content(function (Get $get): string {
                        if ($get('user_id')) {
                            $user = User::findOrFail($get('user_id'));

                            return Carbon::parse($user->timezone)->format('e (P)');
                        } else {
                            return 'N/A';
                        }
                    })
                    ->columnSpan(['md' => 1]),
                TextInput::make('meeting_link')
                    ->helperText('Must start with https://meet.google.com/')
                    ->required()
                    ->startsWith(['https://meet.google.com/'])
                    ->rules(['min:25', 'max:64'])
                    ->validationMessages([
                        'starts_with' => 'The :attribute field must start with a valid Google Meet link.',
                    ])
                    ->columnSpan(['md' => 2]),
                Section::make('Start Duration')
                    ->description('Set UTC time. The values will display the correct times on the instructor\'s and trainees\' timezones')
                    ->schema([
                        DatePicker::make('start_time_meeting_date')
                            ->helperText('Format: DD/MM/YYYY')
                            ->live()
                            ->required()
                            ->afterOrEqual('today')
                            ->rules(['date', 'date_format:Y-m-d'])
                            ->label('Meeting date (start)')
                            ->columnSpan(['default' => 2, 'md' => 1, 'lg' => 2]),
                        Select::make('start_time_hours_mins')
                            ->live()
                            ->required()
                            ->label('Start time (hours:mins)')
                            ->helperText(function (Get $get): ?string {
                                if ($get('user_id') && $get('start_time_meeting_date') && $get('start_time_hours_mins') && $get('start_time_am_pm')) {
                                    $instructor = User::findOrFail($get('user_id'));

                                    $timestamp = $get('start_time_meeting_date'). ' ' .$get('start_time_hours_mins'). ' ' .$get('start_time_am_pm');

                                    $parsed_time = MeetingsOnCall::parse_utc_timestamp_to_user_timezone($timestamp, 'M j, Y h:i A', $instructor->timezone);

                                    return 'This would be ' .$parsed_time. ' in the instructor\'s timezone';
                                } else {
                                    return 'N/A';
                                }
                            })
                            ->rules([
                                fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                    $instructor = User::find($get('user_id'));

                                    $timestamp = $get('start_time_meeting_date'). ' ' .$value. ' ' .$get('start_time_am_pm');

                                    $parsed_start_time = MeetingsOnCall::parse_utc_timestamp_to_user_timezone($timestamp, 'Y-m-d h:i A', $instructor->timezone);

                                    if ($parsed_start_time < Carbon::now($instructor->timezone)) {
                                        $fail('The start time field cannot be less than the current time of the instructor\'s timezone.');
                                    }
                                }
                            ])
                            ->options(MeetingsOnCall::get_times()),
                        Select::make('start_time_am_pm')
                            ->live()
                            ->required()
                            ->label('Start time (AM / PM)')
                            ->options([
                                'am' => 'AM',
                                'pm' => 'PM',
                            ]),
                    ])
                    ->columns(['md' => 3, 'lg' => 2])
                    ->columnSpan(['lg' => 1]),
                Section::make('End Duration')
                    ->description('Set UTC time. The values will display the correct times on the instructor\'s and trainees\' timezones')
                    ->schema([
                        DatePicker::make('end_time_meeting_date')
                            ->helperText('Format: DD/MM/YYYY')
                            ->live()
                            ->required()
                            ->afterOrEqual('start_time_meeting_date')
                            ->rules(['date', 'date_format:Y-m-d'])
                            ->label('Meeting date (end)')
                            ->columnSpan(['default' => 2, 'md' => 1, 'lg' => 2]),
                        Select::make('end_time_hours_mins')
                            ->live()
                            ->required()
                            ->label('End time (hours:mins)')
                            ->helperText(function (Get $get): ?string {
                                if ($get('user_id') && $get('end_time_meeting_date') && $get('end_time_hours_mins') && $get('end_time_am_pm')) {
                                    $instructor = User::findOrFail($get('user_id'));

                                    $timestamp = $get('end_time_meeting_date'). ' ' .$get('end_time_hours_mins'). ' ' .$get('end_time_am_pm');

                                    $parsed_time = MeetingsOnCall::parse_utc_timestamp_to_user_timezone($timestamp, 'M j, Y h:i A', $instructor->timezone);

                                    return 'This would be ' .$parsed_time. ' in the instructor\'s timezone';
                                } else {
                                    return 'N/A';
                                }
                            })
                            ->rules([
                                fn (Get $get, ?MeetingsOnCall $record): Closure => function (string $attribute, $value, Closure $fail) use ($get, $record) {
                                    $timestamp_start = Carbon::parse($get('start_time_meeting_date'). ' ' .$get('start_time_hours_mins'). ' ' .$get('start_time_am_pm'), 'UTC');
                                    $timestamp_end = Carbon::parse($get('end_time_meeting_date'). ' ' .$get('end_time_hours_mins'). ' ' .$get('end_time_am_pm'), 'UTC');

                                    // Check if they overlap with existing on-call meetings between start_time and end_time fields
                                    $has_overlapping_meeting_on_call = MeetingsOnCall::where('user_id', $get('user_id'))
                                        ->whereDate('meeting_date', $get('start_time_meeting_date'))
                                        ->whereNot('meeting_status', MeetingsOnCallsMeetingStatus::CANCELLED)
                                        ->where('start_time', '<', $timestamp_end)
                                        ->where('end_time', '>', $timestamp_start);

                                    if ($record) {
                                        $has_overlapping_meeting_on_call = $has_overlapping_meeting_on_call->whereNot('id', $record['id']);
                                    }

                                    $has_overlapping_meeting_on_call = $has_overlapping_meeting_on_call->exists();

                                    if ($timestamp_end < $timestamp_start) {
                                        $fail('The end time field cannot be less than the start time.');
                                    } else if ($timestamp_end == $timestamp_start) {
                                        $fail('The end time field cannot be equal to the start time.');
                                    } else if ($timestamp_start->diff($timestamp_end)->format('%H:%I:%S') >= '02:30:00') {
                                        $fail('The start time and end time fields should only run for 2 hours or less.');
                                    } else if ($has_overlapping_meeting_on_call) {
                                        $fail('The start time and end time fields cannot overlap with existing on-call meeting(s) of this instructor for ' .$get('start_time_meeting_date'). '.');
                                    }
                                },
                            ])
                            ->options(MeetingsOnCall::get_times()),
                        Select::make('end_time_am_pm')
                            ->live()
                            ->required()
                            ->label('End time (AM / PM)')
                            ->options([
                                'am' => 'AM',
                                'pm' => 'PM',
                            ]),
                    ])
                    ->columns(['md' => 3, 'lg' => 2])
                    ->columnSpan(['lg' => 1]),
            ])->columns(['md' => 2]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Instructor'),
                TextColumn::make('meeting_link')
                    ->limit(36),
                TextColumn::make('meeting_date')
                    ->date('Y-m-d')
                    ->sortable(),
                IconColumn::make('meeting_status')
                    ->alignCenter()
                    ->icon(fn (MeetingsOnCallsMeetingStatus $state): string => match ($state) {
                        MeetingsOnCallsMeetingStatus::CANCELLED => 'heroicon-o-x-circle',
                        MeetingsOnCallsMeetingStatus::COMPLETED => 'heroicon-o-check-circle',
                        MeetingsOnCallsMeetingStatus::PENDING => 'heroicon-o-minus-circle'
                    }),
                TextColumn::make('start_time')
                    ->label('Duration (in UTC)')
                    ->formatStateUsing(fn (MeetingsOnCall $meetings_on_call) =>
                        // This is UTC already, no need to set timezone
                        Carbon::parse($meetings_on_call->start_time)->format('Y-m-d h:i A'). ' ~ ' .Carbon::parse($meetings_on_call->end_time)->format('Y-m-d h:i A')
                    )
            ])
            ->defaultSort('start_time', 'desc')
            ->filters([
                SelectFilter::make('user')
                    ->options(User::select(['id', 'name'])
                        ->where('role_id', 5)
                        ->get()
                        ->pluck('name', 'id')
                    )
                    ->query(function (Builder $query, array $data) {
                        // REF: https://v2.filamentphp.com/tricks/use-selectfilter-on-distant-relationships
                        if (!empty($data['value'])) {
                            return $query->whereHas('user',
                                fn (Builder $query) => $query->where('id', '=', (int) $data['value'])
                            );
                        }
                    })
                    ->label('Staff Name'),
                SelectFilter::make('meeting_status')
                    ->options(MeetingsOnCallsMeetingStatus::class),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn (MeetingsOnCall $meeting_on_call): bool =>
                        Carbon::now() <= Carbon::parse($meeting_on_call['start_time'])->subMinutes(30) &&
                        !in_array($meeting_on_call['meeting_status'], [MeetingsOnCallsMeetingStatus::CANCELLED, MeetingsOnCallsMeetingStatus::COMPLETED])
                    ),
                Action::make('cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->tooltip('Cancel on-call meeting')
                    ->requiresConfirmation()
                    ->modalHeading('Cancel on-call meeting?')
                    ->modalDescription('Are you sure you want to cancel the meeting? This will mark the on-call meeting as cancelled to everyone.')
                    ->visible(fn (MeetingsOnCall $meeting_on_call): bool =>
                        Carbon::now() <= Carbon::parse($meeting_on_call['start_time'])->subMinutes(30) &&
                        !in_array($meeting_on_call['meeting_status'], [MeetingsOnCallsMeetingStatus::CANCELLED, MeetingsOnCallsMeetingStatus::COMPLETED])
                    )
                    ->action(function (MeetingsOnCall $record) {
                        $record->meeting_status = MeetingsOnCallsMeetingStatus::CANCELLED;

                        $record->save();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListMeetingsOnCalls::route('/'),
            'create' => Pages\CreateMeetingsOnCall::route('/create'),
            'edit' => Pages\EditMeetingsOnCall::route('/{record}/edit'),
        ];
    }
}
