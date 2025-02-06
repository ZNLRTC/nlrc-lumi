<?php

namespace App\Filament\Clusters\KnowledgeBase\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use App\Models\KnowledgeBase\Article;
use Filament\Forms\Components\Select;
use App\Models\KnowledgeBase\Feedback;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Clusters\KnowledgeBase;
use Filament\Forms\Components\TextInput;
use App\Enums\KnowledgeBase\ArticleStatus;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\MarkdownEditor;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\KnowledgeBase\Resources\KnowledgebaseArticleResource\Pages;
use App\Filament\Clusters\KnowledgeBase\Resources\KnowledgebaseArticleResource\RelationManagers;
use App\Filament\Clusters\KnowledgeBase\Resources\KnowledgebaseArticleResource\RelationManagers\FeedbacksRelationManager;

class KnowledgebaseArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $cluster = KnowledgeBase::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->minLength(4)
                            ->maxLength(255)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                if (($get('slug') ?? '') !== Str::slug($old)) {
                                    return;
                                }
                            
                                $set('slug', Str::slug($state));
                            })
                            ->live(debounce: 500),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->helperText(str('The slug is used to generate the URL for the article, e.g. *https://lumi.nlrc.ph/help/meetings/slug*. It may only contain lowercase letters, numbers, and dashes.')->inlineMarkdown()->toHtmlString())
                            ->required()
                            ->unique(table: Article::class, ignoreRecord: true)
                            ->minLength(3)
                            ->maxLength(100)
                            ->regex('/^[a-z0-9-]+$/i')
                            ->validationMessages([
                                'regex' => 'The slug may only contain letters, numbers, and dashes.',
                                'unique' => 'This slug exists already. The slug must be unique across all articles.'
                            ]),
                    ]),

                Section::make()
                    ->columns(3)
                    ->schema([
                        Select::make('audiences')
                            ->label('Audiences')
                            ->helperText('You may select more than one user group.')
                            ->options([
                                'Trainee' => 'Trainees',
                                'Instructor' => 'Instructors',
                                'Observer' => 'Observers',
                                'Staff' => 'Staff only',
                            ])
                            ->multiple()
                            ->required(),
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship(name: 'category', titleAttribute: 'name')
                            ->required()
                            ->selectablePlaceholder(false),

                        ToggleButtons::make('status')
                            ->label('Status')
                            ->helperText('Draft articles are not visible to their audience.')
                            ->grouped()
                            ->default(ArticleStatus::DRAFT)
                            ->options([
                                ArticleStatus::DRAFT->value => 'Draft',
                                ArticleStatus::PUBLISHED->value => 'Published',
                            ])
                            ->colors([
                                ArticleStatus::DRAFT->value => 'warning',
                                ArticleStatus::PUBLISHED->value => 'success',
                            ]),
                    ]),

                Section::make()
                    ->columns(1)
                    ->schema([
                        TextInput::make('summary')
                            ->label('Summary')
                            ->required()
                            ->minLength(10)
                            ->maxLength(255)
                            ->helperText('The summary is shown in the list of articles. It should be a brief description of the content.'),
                        MarkdownEditor::make('content')
                            ->label('Content')
                            ->required()
                            ->helperText('The content of the article. Use Markdown to format the text.')
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'heading',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'table',
                                'undo',
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->description('Drafts are not visible to their audiences. Trainees and instructors can vote on the helpfulness of an article if they can access it. Choosing "unhelpful" gives the user an option to leave short feedback.')
            ->groups([
                'category.name',
                'status',
            ])
            ->searchPlaceholder('Search article titles')
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->badge()
                    ->color(fn (ArticleStatus $state): string => match ($state) {
                        ArticleStatus::DRAFT => 'warning',
                        ArticleStatus::PUBLISHED => 'success',
                    }),
                TextColumn::make('audiences')
                    ->label('Audiences')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('helpful_count')
                    ->label('👍')
                    ->color('success'),
                TextColumn::make('not_helpful_count')
                    ->label('👎')
                    ->color('danger'),
                TextColumn::make('view_count')
                    ->label('Views')
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('has_unread_feedback')
                    ->label('Unread feedback')
                    ->wrapHeader()
                    ->sortable(query: function ($query, $direction) {
                        $query->addSelect([
                            'has_unread_feedback' => Feedback::selectRaw('COUNT(*)')
                                ->whereColumn('kb_feedback.article_id', 'kb_articles.id')
                                ->where('is_read', false)
                                ->limit(1)
                        ])->orderBy('has_unread_feedback', $direction);
                    })
                    ->getStateUsing(function ($record) {
                        return $record->feedbacks()->where('is_read', false)->exists() ? 'Yes' : 'No';
                    })
                    ->icon(fn ($state) => $state === 'Yes' ? 'heroicon-o-envelope' : 'heroicon-o-envelope-open')
                    ->color(fn ($state) => $state === 'Yes' ? 'danger' : 'gray')
                    ->url(fn ($record) => route('filament.admin.knowledge-base.resources.knowledgebase-articles.edit', $record) . '?activeRelationManager=0'), // This would break if there are more relation managers and their order changes
                TextColumn::make('category.name')
                    ->label('Category')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_reset_at')
                    ->label('Last counter reset')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            FeedbacksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKnowledgebaseArticles::route('/'),
            'create' => Pages\CreateKnowledgebaseArticle::route('/create'),
            'edit' => Pages\EditKnowledgebaseArticle::route('/{record}/edit'),
        ];
    }
}
