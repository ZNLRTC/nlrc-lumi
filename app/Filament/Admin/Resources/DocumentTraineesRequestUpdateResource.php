<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use App\Models\Documents\Document;
use Filament\Tables\Actions\Action;
use Filament\Tables\Grouping\Group;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Documents\DocumentTraineesRequestUpdate;
use App\Enums\DocumentTraineesRequestUpdatesApprovalStatus;
use App\Filament\Admin\Resources\DocumentTraineesRequestUpdateResource\Pages;
use App\Filament\Admin\Resources\DocumentTraineesRequestUpdateResource\RelationManagers;

class DocumentTraineesRequestUpdateResource extends Resource
{
    protected static ?string $model = DocumentTraineesRequestUpdate::class;

    protected static ?string $modelLabel = 'Document Uploads Request Updates';

    protected static ?string $navigationGroup = 'Document';

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $activeNavigationIcon = 'heroicon-s-square-3-stack-3d';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('document_trainee.trainee.full_name')
                    ->searchable(),
                TextColumn::make('document_trainee.trainee.user.email')
                    ->searchable()
                    ->label('User email'),
                TextColumn::make('document_trainee.url')
                    ->formatStateUsing(function (string $state): string {
                        $file_name = explode('/', $state);

                        return $file_name[1];
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('File'),
                TextColumn::make('document_trainee.document.name')
                    ->searchable(),
                TextColumn::make('reason')
                    ->words(5),
                IconColumn::make('approval_status')
                    ->alignCenter()
                    ->icon(fn (DocumentTraineesRequestUpdatesApprovalStatus $state): string => match ($state) {
                        DocumentTraineesRequestUpdatesApprovalStatus::APPROVED => 'heroicon-o-check-circle',
                        DocumentTraineesRequestUpdatesApprovalStatus::DISAPPROVED => 'heroicon-o-x-circle',
                        DocumentTraineesRequestUpdatesApprovalStatus::PENDING_APPROVAL => 'heroicon-o-question-mark-circle'
                    })
                    ->tooltip(fn (DocumentTraineesRequestUpdate $record): string => 
                        $record->approval_status->value != DocumentTraineesRequestUpdatesApprovalStatus::PENDING_APPROVAL->value ? $record->approval_status->value. ' on ' .Carbon::parse($record->updated_at)->format('D, M j, Y') : ''
                    ),
                TextColumn::make('staff_user_id')
                    ->formatStateUsing(function (string $state): string {
                        $staff_user = User::where('id', (int) $state)
                            ->first();

                        return $staff_user->name;
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Checked by'),
            ])
            ->checkIfRecordIsSelectableUsing(fn (DocumentTraineesRequestUpdate $record): bool =>
                $record->approval_status->value == DocumentTraineesRequestUpdatesApprovalStatus::PENDING_APPROVAL->value
            )
            ->groups([
                Group::make('document_trainee.trainee.user.email')
                    ->label('User email'),
                Group::make('document_trainee.document.name'),
                Group::make('approval_status'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('document')
                    ->options(Document::all()->pluck('name', 'id')->toArray())
                    ->query(function (Builder $query, array $data) {
                        // REF: https://v2.filamentphp.com/tricks/use-selectfilter-on-distant-relationships
                        if (!empty($data['value'])) {
                            return $query->whereHas('document_trainee',
                                fn (Builder $query) => $query->whereHas('document',
                                    fn (Builder $query) => $query->where('id', '=', (int) $data['value'])
                                )
                            );
                        }
                    }),
                SelectFilter::make('staff_user_id')
                    ->options(User::whereIn('role_id', [1, 2])->pluck('name', 'id')->toArray())
                    ->query(function (Builder $query, array $data) {
                        // REF: https://v2.filamentphp.com/tricks/use-selectfilter-on-distant-relationships
                        if (!empty($data['value'])) {
                            return $query->whereRaw('staff_user_id IN (
                                SELECT id
                                FROM `users`
                                WHERE id = ' .(int) $data['value']. '
                            )');
                        }
                    })
                    ->label('Checked by'),
                SelectFilter::make('approval_status')
                    ->options(DocumentTraineesRequestUpdatesApprovalStatus::class)
                    ->default(DocumentTraineesRequestUpdatesApprovalStatus::PENDING_APPROVAL->value),
            ])
            ->actions([
                Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->tooltip('Approve request')
                    ->requiresConfirmation()
                    ->modalHeading('Approve request?')
                    ->modalDescription('Are you sure you would like to approve this request? The trainee will be able to remove their document and upload a new one.')
                    ->hidden(fn (DocumentTraineesRequestUpdate $document_trainees_request_update): bool =>
                        $document_trainees_request_update->approval_status->value != DocumentTraineesRequestUpdatesApprovalStatus::PENDING_APPROVAL->value
                    )
                    ->action(function (DocumentTraineesRequestUpdate $record) {
                        $record->approval_status = DocumentTraineesRequestUpdatesApprovalStatus::APPROVED;
                        $record->staff_user_id = Auth::user()->id;

                        $record->save();
                    }),
                Action::make('disapprove')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->tooltip('Disapprove request')
                    ->requiresConfirmation()
                    ->modalHeading('Disapprove request?')
                    ->modalDescription('Are you sure you would like to disapprove this request? The trainee will be notified that it\'s okay not to request an update for their document.')
                    ->hidden(fn (DocumentTraineesRequestUpdate $document_trainees_request_update): bool =>
                        $document_trainees_request_update->approval_status->value != DocumentTraineesRequestUpdatesApprovalStatus::PENDING_APPROVAL->value
                    )
                    ->action(function (DocumentTraineesRequestUpdate $record) {
                        $record->approval_status = DocumentTraineesRequestUpdatesApprovalStatus::DISAPPROVED;
                        $record->staff_user_id = Auth::user()->id;

                        $record->save();
                    }),
                Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->color('warning')
                    ->url(fn ($record) => Storage::disk('documents')->temporaryUrl($record->url, now()->addMinutes(2), ['ResponseContentDisposition' => 'attachment']))
                    // ->url(fn (DocumentTraineesRequestUpdate $document_trainees_request_update): string =>
                    //     asset('storage/' .$document_trainees_request_update->document_trainee->url)
                    // )
                    ->openUrlInNewTab()
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('approve_selected')
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->modalHeading('Approve request?')
                        ->modalDescription('Are you sure you would like to approve these requests? The trainees will be able to remove their document and upload a new one.')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->approval_status = DocumentTraineesRequestUpdatesApprovalStatus::APPROVED;
                                $record->staff_user_id = Auth::user()->id;

                                $record->save();
                            });
                        }),
                    BulkAction::make('disapprove_selected')
                        ->color('danger')
                        ->icon('heroicon-o-x-circle')
                        ->requiresConfirmation()
                        ->modalHeading('Disapprove request?')
                        ->modalDescription('Are you sure you would like to disapprove these requests? The trainees will be notified that it\'s okay not to request an update for their document.')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->approval_status = DocumentTraineesRequestUpdatesApprovalStatus::DISAPPROVED;
                                $record->staff_user_id = Auth::user()->id;

                                $record->save();
                            });
                        }),
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
            'index' => Pages\ListDocumentTraineesRequestUpdates::route('/')
        ];
    }
}
