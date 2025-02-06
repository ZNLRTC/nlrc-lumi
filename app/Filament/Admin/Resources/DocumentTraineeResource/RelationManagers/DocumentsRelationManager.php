<?php

namespace App\Filament\Admin\Resources\DocumentTraineeResource\RelationManagers;

use App\Enums\DocumentTraineesStatus;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Documents\Document;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\Documents\DocumentTrainee;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('document_id')
                    ->label('Document submitted')
                    ->content(fn (Document $document): string => $document->name),
                Placeholder::make('status')
                    ->content(fn (Document $document): string => $document->status)
                    ->hidden(fn (Document $document): bool => $document->status == DocumentTraineesStatus::PENDING_CHECKING->value),
                Select::make('status')
                    ->required()
                    ->helperText('Selecting re-upload needed requires the trainee to re-submit the document for approval again')
                    ->hidden(fn (Document $document): bool => $document->status != DocumentTraineesStatus::PENDING_CHECKING->value)
                    ->options(DocumentTraineesStatus::class),
                Placeholder::make('created_at')
                    ->label('Uploaded on')
                    ->content(fn (Document $document): string => Carbon::parse($document->document_created_at)->format('F j, Y g:i A')),
                Placeholder::make('updated_at')
                    ->content(fn (Document $document): string => Carbon::parse($document->document_updated_at)->format('F j, Y g:i A')),
                Textarea::make('comments')
                    ->rows(4)
                    ->placeholder('Write any comment here that will be shown to the trainees.')
                    ->columnSpanFull()
                    ->helperText('Notes visible to the trainees.')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label('Document'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Approved' => 'success',
                        'Pending checking' => 'warning',
                        'Re-upload needed' => 'danger'
                    }),
                TextColumn::make('comments')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('document_created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Uploaded on'),
                TextColumn::make('document_updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Updated at'),
            ])
            ->groups([Group::make('status')])
            ->defaultSort('name', 'asc')
            ->filters([
                SelectFilter::make('status')
                    ->options(DocumentTraineesStatus::class),
                Filter::make('comments')
                    ->label('Show documents w/ comments')
                    ->toggle()
                    // OR: $query->whereNotNull('comments')->where('comments', '<>', '')
                    ->query(fn (Builder $query): Builder => $query->whereRaw('TRIM(COALESCE(comments, "")) != ""'))
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->color('warning')
                    ->url(fn ($record) => Storage::disk('documents')->temporaryUrl($record->url, now()->addMinutes(2)))
                    ->openUrlInNewTab(),
                Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn ($record) => Storage::disk('documents')->temporaryUrl($record->url, now()->addMinutes(2), ['ResponseContentDisposition' => 'attachment'])),
                EditAction::make()
                    ->label(fn (Document $record): string => $record->status == DocumentTraineesStatus::PENDING_CHECKING->value ? 'Approve/Reject' : 'Edit Comments')
                    ->modalHeading('Trainee document information')
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('approve_selected')
                        ->label('Approve selected')
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $trainee_document = DocumentTrainee::where('document_id', $record->id)
                                    ->where('trainee_id', $record->trainee_id)
                                    ->first();
                                $trainee_document->status = 'Approved';
                                $trainee_document->save();
                            });
                        }),
                    BulkAction::make('reject_selected')
                        ->label('Reject selected')
                        ->color('danger')
                        ->icon('heroicon-o-x-circle')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $trainee_document = DocumentTrainee::where('document_id', $record->id)
                                    ->where('trainee_id', $record->trainee_id)
                                    ->first();
                                $trainee_document->status = 'Re-upload needed';
                                $trainee_document->save();
                            });
                        }),
                ]),
            ]);
    }
}
