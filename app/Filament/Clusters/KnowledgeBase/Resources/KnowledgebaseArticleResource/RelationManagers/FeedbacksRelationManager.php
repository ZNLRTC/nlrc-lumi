<?php

namespace App\Filament\Clusters\KnowledgeBase\Resources\KnowledgebaseArticleResource\RelationManagers;

use DB;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

use Filament\Tables\Grouping\Group;
use App\Models\KnowledgeBase\Feedback;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class FeedbacksRelationManager extends RelationManager
{
    protected static string $relationship = 'feedbacks';

    protected static ?string $title = 'Feedback';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            // This ain't pretty but allows grouping feedback by whether it was sent before or after the latest counter reset
            ->query(
                Feedback::query()
                    ->select('kb_feedback.*', DB::raw("CASE WHEN kb_feedback.created_at < kb_articles.last_reset_at THEN 'Sent prior to the latest counter reset' ELSE 'Sent after the latest counter reset' END as feedback_group"))
                    ->join('kb_articles', 'kb_feedback.article_id', '=', 'kb_articles.id')
                    ->groupBy('kb_feedback.id', 'kb_articles.last_reset_at')
            )
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date(),
                TextColumn::make('feedback')
                    ->grow(),
            ])
            ->defaultGroup('feedback_group')
            ->groupingSettingsHidden()
            ->groups([
                Group::make('feedback_group')
                    ->titlePrefixedWithLabel(false)
            ])
            ->emptyStateHeading('No feedback about this article')
            ->emptyStateDescription('Maybe everyone\'s happy with it?')
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Delete feedback?'),
                Action::make('Mark as seen')
                    ->action(function ($record) {
                        $record->update([
                            'is_read' => !$record->is_read,
                        ]);
                    })
                    ->icon(fn ($record) => $record->is_read ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->label(fn ($record) => $record->is_read ? 'Mark as unread' : 'Mark as read')
                    ->color(fn ($record) => $record->is_read ? 'secondary' : 'primary'),
            ])
            ->bulkActions([
                // FIXME: These are disabled right now as ticking a group row causes an SQL error
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                //     BulkAction::make('toggleReadStatus')
                //         ->label('Change read status')
                //         ->action(function (Collection $records) {
                //             $records->each(function ($record) {
                //                 $record->update(['is_read' => !$record->is_read]);
                //             });
                //         })
                //         ->icon('heroicon-o-eye')
                //         ->color('primary'),
                //     ]),
            ]);
    }
}
