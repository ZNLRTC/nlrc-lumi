<?php

namespace App\Filament\Admin\Resources;

use App\Events\ReceiveAnnouncementEvent;
use App\Filament\Admin\Resources\AnnouncementResource\Pages;
use App\Filament\Admin\Resources\AnnouncementResource\Pages\AnnouncementRecipientsPage;
use App\Filament\Admin\Resources\AnnouncementResource\Pages\EditAnnouncement;
use App\Filament\Admin\Resources\AnnouncementResource\Pages\ViewAnnouncement;
use App\Filament\Admin\Resources\AnnouncementResource\RelationManagers;
use App\Livewire\Announcements\SendAnnouncement;
use App\Mail\SendAnnouncementEmail;
use App\Models\Announcement;
use App\Models\Grouping\Group;
use App\Models\Notification;
use App\Models\Trainee;
use App\Models\User;
use App\Notifications\AnnouncementNotification;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group as InfolistGroup;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Query\Builder AS BuilderQuery;
use Illuminate\Support\Facades\Notification as LaravelNotification;
use Illuminate\Support\Collection;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $activeNavigationIcon = 'heroicon-s-megaphone';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Announcement Information')
                            ->schema([
                                Placeholder::make('user_id')
                                    ->label('Author')
                                    ->content(fn (Announcement $record): string => $record->user->name),
                                TextInput::make('title')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                MarkdownEditor::make('description')
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->disableToolbarButtons(['attachFiles'])
                                    ->columnSpan(2),
                                FileUpload::make('thumbnail_image_path')
                                    ->helperText('Optional. This will show up as a thumbnail image when viewing announcements from non-admin panel pages. If this announcement has no image set, a default thumbnail image will be used instead.')
                                    ->label('Thumbnail image')
                                    ->disk('announcements')
                                    ->directory('/')
                                    ->visibility('public'),
                                Placeholder::make('created_at')
                                    ->label('Announced on')
                                    ->content(fn (Announcement $announcement): string => $announcement->created_at->isoFormat('LLL'))
                                    ->hidden(fn (string $operation): bool => $operation === 'create'),
                                Placeholder::make('updated_at')
                                    ->label('Updated on')
                                    ->content(fn (Announcement $announcement): string => $announcement->updated_at->isoFormat('LLL'))
                                    ->hidden(fn (string $operation): bool => $operation === 'create')
                            ]),
                        Tab::make('Send Announcement')
                            ->schema([
                                /* TODO: TO BE REMOVED ALONG WITH ITS LIVEWIRE CLASS AND VIEW ONCE FINALIZED
                                Livewire::make(SendAnnouncement::class)
                                    ->dehydrated(false)
                                    ->hidden(fn (string $operation): bool => $operation === 'create'),
                                */
                                Select::make('send_to_list')
                                    ->label('Send this announcement to')
                                    ->live()
                                    ->options([
                                        'trainees' => 'Active Trainees',
                                        'groups' => 'Active Groups w/ Active Trainees'
                                    ]),
                                Select::make('trainees_list')
                                    ->label('Trainee Recipients')
                                    ->placeholder('Select recipients')
                                    ->multiple()
                                    ->searchable()
                                    ->live()
                                    ->helperText('Only the first 20 trainees are loaded but you can still search for the recipients')
                                    ->visible(fn (Get $get): bool => $get('send_to_list') == 'trainees')
                                    ->optionsLimit(20)
                                    ->options(fn (Announcement $record): Collection =>
                                        Trainee::selectRaw('id, CONCAT(last_name, ", ", first_name) AS name')
                                            ->isActive()
                                            ->whereNotIn('id', function (BuilderQuery $query) use ($record) {
                                                return $query->select(['notifiable_id'])
                                                    ->from('notifications')
                                                    ->where('type', 'announcement-sent')
                                                    ->where('data->announcement_id', $record->id);
                                            })
                                            ->get()
                                            ->pluck('name', 'id')
                                    ),
                                Select::make('active_groups_list')
                                    ->label('Group Recipients')
                                    ->placeholder('Select recipients')
                                    ->multiple()
                                    ->searchable()
                                    ->live()
                                    ->helperText('Only the first 20 groups are loaded but you can still search for the groups')
                                    ->visible(fn (Get $get): bool => $get('send_to_list') == 'groups')
                                    ->optionsLimit(20)
                                    ->options(fn (): Collection =>
                                        Group::select(['id', 'name'])->isActive()
                                            ->orderBy('name', 'ASC')
                                            ->get()
                                            ->filter(fn ($value): bool => $value->active_trainee_count > 0)
                                            ->pluck('name', 'id')
                                    ),
                                Checkbox::make('is_priority')
                                    ->label('Mark as priority announcement?')
                                    ->helperText('Toggle this on if this announcement requires immediate attention by trainees. This will take precedence over an unprioritized latest announcement'),
                                Actions::make([
                                    Action::make('send_announcement')
                                        ->icon('heroicon-m-paper-airplane')
                                        ->color('success')
                                        ->visible(fn (Get $get): bool =>
                                            $get('send_to_list') && (count($get('trainees_list')) > 0 || count($get('active_groups_list')) > 0)
                                        )
                                        ->action(function (Get $get, Set $set, Announcement $record) {
                                            $selected_list = $get('send_to_list');

                                            $base_query = Trainee::select('trainees.id AS id', 'user_id', 'email', 'last_name', 'first_name')
                                                ->join('users', 'users.id', 'trainees.user_id');

                                            if ($selected_list == 'trainees') {
                                                $recipients_list = $base_query->clone()
                                                    ->where('trainees.active', true)
                                                    ->whereIn('trainees.id', $get('trainees_list'));

                                                $suffix = count($get('trainees_list')). ' trainee/s';
                                            } else {
                                                $notifications = Notification::select('notifiable_id')
                                                    ->where('type', 'announcement-sent')
                                                    ->where('notifiable_type', 'App\Models\Trainee')
                                                    ->where('data->announcement_id', $record->id)
                                                    ->get()
                                                    ->toArray();
                                                $trainee_ids = array_unique(array_column($notifications, 'notifiable_id'));

                                                $recipients_list = $base_query->clone()
                                                    ->join('group_trainee', 'group_trainee.trainee_id', 'trainees.id')
                                                    ->where('trainees.active', true)
                                                    ->whereIn('group_trainee.group_id', $get('active_groups_list'))
                                                    ->whereNotIn('trainees.id', $trainee_ids);

                                                $suffix = count($get('active_groups_list')). ' group/s';
                                            }

                                            $recipients_list = $recipients_list
                                                ->chunk(250, function (Collection $recipients) use ($record, $get) {
                                                    LaravelNotification::send($recipients, new AnnouncementNotification($record, $get('is_priority')));

                                                    foreach ($recipients as $trainee) {
                                                        /* TODO: TO BE REMOVED ONCE FINALIZED
                                                        Announcement::send_announcement_then_notify($record, $trainee, $get('is_priority'));
                                                        */

                                                        // NOTE: Queueing mails is currently untested
                                                        /*
                                                        Mail::to($trainee->email)->queue(new SendAnnouncementEmail($record, $trainee));

                                                        Mail::to($trainee->email)->send(new SendAnnouncementEmail($record, $trainee));
                                                        */

                                                        broadcast(new ReceiveAnnouncementEvent($trainee->user_id)); // Trigger an event
                                                    }
                                                });

                                            $set('trainees_list', []);
                                            $set('active_groups_list', []);

                                            FilamentNotification::make()
                                                ->title('Announcement sent')
                                                ->body('Announcement was sent to ' .$suffix)
                                                ->success()
                                                ->send();
                                        })
                                ])
                            ])
                            ->hidden(fn (string $operation): bool => $operation === 'create')
                    ])
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('user.name')
                    ->label('Staff Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->words(5),
                TextColumn::make('created_at')
                    ->label('Announced on')
                    ->sortable()
                    ->dateTime('M d, Y h:i:s A'),
                TextColumn::make('updated_at')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime('M d, Y h:i:s A'),
            ])
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
                            return $query->whereHas('user',
                                fn (Builder $query) => $query->where('id', '=', (int) $data['value'])
                            );
                        }
                    })
                    ->label('Staff Name'),
            ])
            ->actions([
                ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('warning')
                    ->label('View'),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotificationTitle('Announcement deleted'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Announcement Details')
                    ->schema([
                        Split::make([
                            Grid::make(2)
                                ->schema([
                                    InfolistGroup::make([
                                        TextEntry::make('user.name')
                                            ->label('Author / Staff Name'),
                                        TextEntry::make('title')
                                    ]),
                                    InfolistGroup::make([
                                        TextEntry::make('created_at')
                                            ->badge()
                                            ->date()
                                            ->color('success'),
                                        TextEntry::make('updated_at')
                                            ->badge()
                                            ->date()
                                            ->color('warning')
                                    ]),
                                    TextEntry::make('description')
                                        ->columnSpanFull()
                                ]),
                            ImageEntry::make('thumbnail_image')
                                ->hiddenLabel()
                                ->grow(false)
                        ])
                    ]),
                InfolistSection::make('Author Details')
                    ->schema([
                        TextEntry::make('user.email')
                            ->label('Email')
                            ->default('N/A'),
                        TextEntry::make('user.role.name')
                            ->label('Role'),
                        TextEntry::make('user.timezone')
                            ->label('Timezone')
                    ])
                    ->columns(2)
            ]);
    }

    public static function getRecordSubNavigation($page): array
    {
        return $page->generateNavigationItems([
            AnnouncementRecipientsPage::class,
            EditAnnouncement::class,
            ViewAnnouncement::class
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
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => EditAnnouncement::route('/{record}/edit'),
            'view' => ViewAnnouncement::route('/{record}'),
            'recipients' => AnnouncementRecipientsPage::route('/{record}/recipients'),
        ];
    }
}
