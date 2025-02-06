<?php

namespace App\Filament\Admin\Resources\GroupResource\RelationManagers;

use App\Models\Grouping\Group;
use App\Models\Grouping\GroupTrainee;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Support\Collection;

class GroupTraineesRelationManager extends RelationManager
{
    protected static string $relationship = 'groupTrainees';

    protected static ?string $title = 'Group history';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('group_id')
                    ->relationship('group', 'group_code')
                    ->searchable()
                    ->loadingMessage('Loading groups...')
                    ->required()
                    ->label('New group')
                    ->options(function (?GroupTrainee $record, string $operation): Collection {
                        $trainee_groups = Group::selectRaw('groups.id, CONCAT(group_types.code, groups.name) AS name, groups.active')->isActive()
                            ->join('group_types', 'group_types.id', 'groups.group_type_id');

                        if ($operation === 'edit') {
                            $trainee_groups = $trainee_groups->whereNot('groups.id', $record->group_id);
                        }

                        $trainee_groups = $trainee_groups->orderBy('name', 'ASC')
                            ->get()
                            ->pluck('name', 'id');

                        return $trainee_groups;
                    }),
                TextInput::make('notes')
                    ->required()
                    ->label('Reason for the move'),
                Toggle::make('active')
                    ->label('The trainee is active in this group')
                    ->helperText('Toggle this off if this is the latest group of the trainee, but the trainee is no longer active in it. The trainee may only be active in one group.')
                    ->onColor('success')
                    ->required()
                    ->visibleOn('edit'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('group.name')
            ->description('This lists groups the trainee has been a member of. If the latest group has a green checkmark next to it, the trainee is currently in that group.')
            ->emptyStateHeading('This trainee is not a member of any group')
            ->emptyStateDescription('You can assign a group with the button on the top right.')
            ->columns([
                IconColumn::make('active')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-s-check-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'gray',
                        '1' => 'success',
                    })
                    ->grow(false),
                TextColumn::make('group.group_code')
                    ->label('Group'),
                TextColumn::make('notes')
                    ->label('Reason for the move'),
                TextColumn::make('created_at')
                    ->date()
                    ->label('Added on'),
                TextColumn::make('addedBy.name')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Transfer to another group')
                    ->modalHeading('Transfer the trainee to another group')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Transfer done')
                            ->body('The trainee has been transferred to the new group.'),
                    )
                    ->createAnother(false)
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['added_by'] = auth()->id();

                        return $data;
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading('Edit previous transfer'),
                DeleteAction::make()
                    ->modalHeading('Delete record of the previous transfer'),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
