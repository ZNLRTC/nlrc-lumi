<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GroupResource\Pages;
use App\Filament\Admin\Resources\GroupResource\Pages\EditGroup;
use App\Filament\Admin\Resources\GroupResource\Pages\ViewGroup;
use App\Filament\Admin\Resources\GroupResource\RelationManagers\CoursesRelationManager;
use App\Filament\Admin\Resources\GroupResource\RelationManagers\CurriculaRelationManager;
use App\Filament\Clusters\Courses;
use App\Models\Grouping\Group;
use App\Models\Grouping\GroupType;
use App\Models\Observer;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Unique;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $activeNavigationIcon = 'heroicon-s-table-cells';

    public static function getEloquentQuery(): Builder
    {
        if (Auth::user()->hasRole('Observer')) {
            $observer = Observer::select(['agency_id'])->where('user_id', Auth::user()->id)
                ->first();

            return parent::getEloquentQuery()->where('agency_id', $observer->agency_id);
        } else {
            return parent::getEloquentQuery();
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('group_type_id')
                    ->required()
                    ->label('Group prefix')
                    ->helperText('Determines the group prefix. This is prepended to the name when you save the group.')
                    ->options(fn () => 
                        GroupType::whereNot('code', 'KMH')->orderBy('code', 'ASC')
                            ->get()
                            ->pluck('code', 'id')
                    )
                    ->placeholder('Select the group prefix'),
                TextInput::make('name')
                    ->label('Group suffix')
                    ->helperText('Determines the group suffix, e.g. "22 s." This is added after the prefix to create a full group name, like "FIN22 s." when you save the group.')
                    ->required()
                    ->maxLength(64)
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule, Get $get) =>
                        $rule->where('group_type_id', $get('group_type_id'))
                            ->where('name', strtolower(trim($get('name'))))
                    ),
                DatePicker::make('date_of_start')
                    ->native(false)
                    ->required(),
                ToggleButtons::make('active')
                    ->boolean()
                    ->grouped()
                    ->label('This group is active')
                    ->helperText('If the group is not active, it does not show up listings by default. Only turn this off once the group has no active trainees.')
                    ->default(true)
                    ->required(),
                Textarea::make('notes')
                    ->label('Notes, not visible to trainees')
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Group $record): string =>
                TraineeResource::getUrl('index'). '?' .($record->id == 1 ? 
                    'activeTab=non-KMH' : // Beginner's course
                    'tableFilters[group_id][value]=' .$record->id)
            )
            ->columns([
                Split::make([
                    TextColumn::make('group_code')
                        ->label('Group name')
                        ->sortable(query: fn (Builder $query, string $direction): Builder =>
                            $query->selectRaw('groups.*, CONCAT(group_types.code, groups.name) AS group_name')
                                ->join('group_types', 'group_types.id', 'groups.group_type_id')
                                ->orderBy('group_name', $direction)
                        ),
                    Stack::make([
                        Grid::make(['default' => 5])
                            ->schema([
                                TextColumn::make('activeFlaggedTraineesCount')
                                    ->badge()
                                    ->color('info')
                                    ->tooltip('Active trainees')
                                    ->extraAttributes(['class' => 'w-[36px]'])
                                    ->grow(false),
                                TextColumn::make('inactiveFlaggedTraineesCount')
                                    ->badge()
                                    ->color('danger')
                                    ->tooltip('Inactive trainees')
                                    ->extraAttributes(['class' => 'w-[36px]'])
                                    ->grow(false),
                                TextColumn::make('deployedFlaggedTraineesCount')
                                    ->badge()
                                    ->color('success')
                                    ->tooltip('Deployed trainees')
                                    ->extraAttributes(['class' => 'w-[36px]'])
                                    ->grow(false),
                                TextColumn::make('onHoldFlaggedTraineesCount')
                                    ->badge()
                                    ->color('gray')
                                    ->tooltip('On hold trainees')
                                    ->extraAttributes(['class' => 'w-[36px]'])
                                    ->grow(false),
                                TextColumn::make('quitFlaggedTraineesCount')
                                    ->badge()
                                    ->color('warning')
                                    ->tooltip('Quit trainees')
                                    ->extraAttributes(['class' => 'w-[36px]'])
                                    ->grow(false)
                            ]),
                        ])
                        ->grow(false),
                    TextColumn::make('date_of_start')
                        ->date()
                        ->sortable()
                        ->alignCenter()
                        ->tooltip('Date the group will start')
                        ->visibleFrom('lg'),
                    TextColumn::make('updated_at')
                        ->date() // Or we could use ->dateTime()
                        ->sortable()
                        ->alignCenter()
                        ->tooltip('Date the group was last updated')
                        ->visibleFrom('sm'),
                    IconColumn::make('active')
                        ->icon(fn (string $state): string => match ($state) {
                            '0' => 'heroicon-o-x-circle',
                            '1' => 'heroicon-o-check-circle',
                        })
                        ->color(fn (string $state): string => match ($state) {
                            '0' => 'danger',
                            '1' => 'success',
                        })
                        ->grow(false)
                        ->tooltip('Whether the group is active or not')
                ])
            ])
            ->filters([
                SelectFilter::make('group_code')
                    ->relationship('group_type', 'code')
                    ->label('Only show these groups')
                    ->hidden(fn () => Auth::user()->hasRole('Observer')),
                Filter::make('active')
                    ->default()
                    ->label('Only show active groups')
                    ->query(fn (Builder $query): Builder => $query->isActive()),
            ], layout: FiltersLayout::Dropdown)
            ->actions([
                EditAction::make()
                    ->successNotification(
                        Notification::make()->success()
                            ->title('Group created')
                    ),
                ViewAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Group Details')
                    ->schema([
                        TextEntry::make('group_code')
                            ->label('Group name'),
                        TextEntry::make('date_of_start')
                            ->badge()
                            ->date()
                            ->color('success'),
                        TextEntry::make('agency.name')
                            ->hidden(fn (Group $record): bool => $record->name == 'Kyl mä hoidan'),
                        TextEntry::make('activeTraineeCount')
                            ->label('Active trainees count'),
                        IconEntry::make('active')
                            ->label('Is active?')
                            ->color(fn (int $state): string => match ($state) {
                                0 => 'danger',
                                1 => 'success'
                            })
                            ->icon(fn (int $state): string => match ($state) {
                                0 => 'heroicon-o-x-circle',
                                1 => 'heroicon-o-check-circle'
                            })
                    ])
                    ->columns(2),
                InfolistSection::make('Group Type Details')
                    ->schema([
                        TextEntry::make('group_type.code')
                            ->label('Group type'),
                        TextEntry::make('group_type.description')
                            ->label('Description')
                    ])
                    ->columns(2)
            ]);
    }

    public static function getRecordSubNavigation($page): array
    {
        return $page->generateNavigationItems([
            EditGroup::class,
            ViewGroup::class
        ]);
    }

    public static function getRelations(): array
    {
        return [
            CoursesRelationManager::class,
            CurriculaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGroups::route('/'),
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => EditGroup::route('/{record}/edit'),
            'view' => ViewGroup::route('/{record}')
        ];
    }
}
