<?php

namespace App\Filament\Clusters\KnowledgeBase\Resources\KnowledgebaseArticleResource\Pages;

use Mockery\Matcher\Not;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Clusters\KnowledgeBase\Resources\KnowledgebaseArticleResource;

class EditKnowledgebaseArticle extends EditRecord
{
    protected static string $resource = KnowledgebaseArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Delete article')
                ->icon('heroicon-o-x-mark')
                ->modalHeading('Delete the article?')
                ->modalDescription('Are you sure you want to delete this article? You cannot undo this.'),
            Action::make('Reset counters')
                ->action(function ($record) {
                    DB::transaction(function () use ($record) {
                        $record->update([
                            'helpful_count' => 0,
                            'not_helpful_count' => 0,
                            'last_reset_at' => now(),
                        ]);
                    });

                    Notification::make()
                        ->title('Counters set to 0')
                        ->icon('heroicon-o-check-circle')
                        ->iconColor('success')
                        ->send();
                })
                ->requiresConfirmation()
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->label('Reset counters')
                ->modalHeading('Reset helpful/not helpful counters?')
                ->modalDescription('This resets the helpful/not helpful counters of this article to 0. You cannot undo this. You should only do this after an overhaul to the article to see how readers react to the change. Do you want to reset the counters')
                ->modalSubmitActionLabel('Yes, reset'),
        ];
    }

    public function getTitle(): string
    {
        return 'Editing "' . $this->record->title . '"';
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
