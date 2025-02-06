<?php

namespace App\Livewire\KB;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use App\Models\KnowledgeBase\Article;
use App\Models\KnowledgeBase\Category;
use App\Models\KnowledgeBase\Feedback;
use Illuminate\Support\Facades\Session;

class Show extends Component
{
    public Category $category;
    public Article $article;
    public string $articleContentHtml;
    
    public bool $hasVoted = false;
    public bool $showFeedbackForm = false;
    
    #[Validate('required|min:5|max:255')]
    public string $feedback = '';

    public function mount(Category $category, Article $article)
    {
        $this->category = $category->load('articles');
        $this->article = $article->load('category');

        $this->hasVoted = Session::has('voted_article_' . $article->id);

        // Markdown to HTML
        $this->articleContentHtml = Str::of($this->article->content)->markdown([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    public function markHelpful($articleId, $isHelpful)
    {
        $article = Article::find($articleId);

        if ($isHelpful) {
            $article->increment('helpful_count');;
            session()->flash('postVoteMessage', 'Thank you for your feedback.');
        } else {
            $article->increment('not_helpful_count');

            // This is needed for the policy check as it needs the article id
            $feedback = new Feedback();
            $feedback->article_id = $articleId;

            if (auth()->user()->can('create', $feedback)) {
                $this->showFeedbackForm = true;
            } else {
                session()->flash('postVoteMessage', 'Thank you for your feedback.');
            }
        }

        Session::put('voted_article_' . $articleId, true);
        $this->hasVoted = true;
    }

    public function submitFeedback()
    {
        // See the comment above re: policy check
        $feedback = new Feedback();
        $feedback->article_id = $this->article->id;

        $this->authorize('create', $feedback);
        $this->validate();

        Feedback::create([
            'user_id' => auth()->id(),
            'article_id' => $this->article->id,
            'feedback' => $this->feedback,
        ]);

        $this->showFeedbackForm = false;
        session()->flash('postVoteMessage', 'Thank you for your feedback.');
    }

    public function render()
    {
        return view('livewire.k-b.show');
    }
}
