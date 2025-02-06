<?php

namespace App\Filament\Admin\Resources;

use App\Enums\DocumentTraineesStatus;
use App\Filament\Admin\Resources\DocumentTraineeResource\Pages;
use App\Models\Agencies\Agency;
use App\Models\Documents\DocumentTrainee;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class DocumentTraineeResource extends Resource
{
    protected static ?string $model = DocumentTrainee::class;

    protected static ?string $modelLabel = 'Document Uploads';

    protected static ?string $navigationGroup = 'Document';

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $activeNavigationIcon = 'heroicon-s-document-duplicate';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Trainee Information')
                    ->schema([
                        Placeholder::make('trainee_full_name')
                            ->label('Trainee')
                            ->content(fn (DocumentTrainee $document_trainee): string => $document_trainee->trainee->full_name),
                        Textarea::make('comments')
                            ->rows(4)
                            ->placeholder('Write any comment here that will be shown to the trainees.')
                            ->columnSpanFull()
                            ->helperText('Notes visible to the trainees.'),
                        Textarea::make('internal_notes')
                            ->rows(4)
                            ->placeholder('Write any notes here that will only be shown to admins.')
                            ->columnSpanFull()
                            ->helperText('Notes only visible to the admins.'),
                        ])
                        ->columnSpan(1)
                        ->columns(1),
                Section::make('Document Information')
                    ->schema([
                        Placeholder::make('document_id')
                            ->label('Document submitted')
                            ->content(fn (DocumentTrainee $document_trainee): string => $document_trainee->document->name),
                        Placeholder::make('status')
                            ->content(fn (DocumentTrainee $document_trainee): DocumentTraineesStatus => $document_trainee->status)
                            ->hidden(fn (DocumentTrainee $document_trainee): bool => $document_trainee->status->value == DocumentTraineesStatus::PENDING_CHECKING->value),
                        Select::make('status')
                            ->required()
                            ->helperText('Selecting re-upload needed requires the trainee to re-submit the document for approval again')
                            ->hidden(fn (DocumentTrainee $document_trainee): bool => $document_trainee->status->value != DocumentTraineesStatus::PENDING_CHECKING->value)
                            ->options(DocumentTraineesStatus::class),
                        Placeholder::make('created_at')
                            ->label('Uploaded on')
                            ->content(fn (DocumentTrainee $document_trainee): string => $document_trainee->created_at->isoFormat('LLL')),
                        Placeholder::make('updated_at')
                            ->content(fn (DocumentTrainee $document_trainee): string => $document_trainee->updated_at->isoFormat('LLL')),
                    ])
                    ->columnSpan(1)
                    ->columns(1)
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('trainee.full_name')
                    ->searchable(['first_name', 'middle_name', 'last_name'])
                    ->sortable(['first_name'])
                    ->label('Trainee'),
                TextColumn::make('trainee.agency.name')
                    ->searchable()
                    ->sortable()
                    ->label('Agency'),
                TextColumn::make('document.name')
                    ->searchable()
                    ->label('Document'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('comments')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('internal_notes')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Uploaded on'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->checkIfRecordIsSelectableUsing(fn (DocumentTrainee $record): bool =>
                $record->status->value == DocumentTraineesStatus::PENDING_CHECKING->value
            )
            ->filters([
                SelectFilter::make('name')
                    ->relationship('document', 'name')
                    ->label('Document'),
                SelectFilter::make('agency')
                    ->label('Agency')
                    ->options(fn () => Agency::pluck('name', 'id')->toArray())
                    ->query(function (Builder $query, array $data) {
                        // REF: https://v2.filamentphp.com/tricks/use-selectfilter-on-distant-relationships
                        if (!empty($data['value'])) {
                            $query->whereHas('trainee.agency', fn (Builder $query) => $query->where('id', $data['value']));
                        }
                    }),
                SelectFilter::make('status')
                    ->options(DocumentTraineesStatus::class),
                Filter::make('comments')
                    ->label('Show documents w/ comments')
                    ->toggle()
                    // OR: $query->whereNotNull('comments')->where('comments', '<>', '')
                    ->query(fn (Builder $query): Builder => $query->whereRaw('TRIM(COALESCE(comments, "")) != ""')),
                Filter::make('internal_notes')
                    ->label('Show documents w/ internal notes')
                    ->toggle()
                    // OR: $query->whereNotNull('internal_notes')->where('internal_notes', '<>', '')
                    ->query(fn (Builder $query): Builder => $query->whereRaw('TRIM(COALESCE(internal_notes, "")) != ""')),
            ])
            ->actions([
                // I commented this out since it generates a million signed URLs, most of which are not used on the listing page. Docs would be accessed via the edit page mostly. --Mikko
                // Action::make('view')
                //     ->icon('heroicon-o-eye')
                //     ->color('warning')
                //     ->url(fn ($record) => Storage::disk('documents')->temporaryUrl($record->url, now()->addMinutes(15)))
                //     ->openUrlInNewTab(),
                Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('warning')
                    ->url(fn ($record) => Storage::disk('documents')->temporaryUrl($record->url, now()->addMinutes(2), ['ResponseContentDisposition' => 'attachment'])),
                Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->tooltip('Approve document')
                    ->requiresConfirmation()
                    ->modalHeading('Approve document?')
                    ->modalDescription('Are you sure you would like to approve this document?')
                    ->hidden(fn (DocumentTrainee $document_trainee): bool =>
                        $document_trainee->status->value != DocumentTraineesStatus::PENDING_CHECKING->value
                    )
                    ->action(function (DocumentTrainee $record) {
                        $record->status = DocumentTraineesStatus::APPROVED;

                        $record->save();
                    }),
                Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->tooltip('Reject document')
                    ->requiresConfirmation()
                    ->modalHeading('Reject document?')
                    ->modalDescription('Are you sure you would like to reject this document?')
                    ->hidden(fn (DocumentTrainee $document_trainee): bool =>
                        $document_trainee->status->value != DocumentTraineesStatus::PENDING_CHECKING->value
                    )
                    ->action(function (DocumentTrainee $record) {
                        $record->status = DocumentTraineesStatus::RE_UPLOAD_NEEDED;

                        $record->save();
                    }),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('approve_selected')
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->modalHeading('Approve documents?')
                        ->modalDescription('Are you sure you would like to approve these documents?')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->status = DocumentTraineesStatus::APPROVED;
                                $record->save();
                            });
                        }),
                    BulkAction::make('reject_selected')
                        ->color('danger')
                        ->icon('heroicon-o-x-circle')
                        ->requiresConfirmation()
                        ->modalHeading('Reject documents?')
                        ->modalDescription('Are you sure you would like to reject these documents?')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->status = DocumentTraineesStatus::RE_UPLOAD_NEEDED;
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
            'index' => Pages\ListDocumentTrainees::route('/'),
            'edit' => Pages\EditDocumentTrainee::route('/{record}/edit'),
        ];
    }
}
