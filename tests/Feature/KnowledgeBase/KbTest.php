<?php

use App\Models\User;
use Livewire\Livewire;
use App\Livewire\KB\Show;
use App\Livewire\KB\Index;
use App\Models\KnowledgeBase\Article;
use App\Models\KnowledgeBase\Category;
use App\Enums\KnowledgeBase\ArticleStatus;

describe('Knowledge base', function() {

    beforeEach(function () {

        // The factory creates random statuses and audiences so they need be set here
        $this->article = Article::factory()->create([
            'status' => ArticleStatus::PUBLISHED,
            'audiences' => ['Trainee', 'Instructor', 'Observer'],
        ]);
        $this->category = $this->article->category;
    });

    test('trainees, instructors, and observers can see the knowledge base and its Livewire components', function() {
        $trainee = createTrainee();
        $instructor = createInstructor();
        $observer = createObserver();

        $this->actingAs($trainee)
            ->get(route('kb.index'))
            ->assertSuccessful()
            ->assertSeeLivewire(Index::class);

        $this->actingAs($instructor)
            ->get(route('kb.index'))
            ->assertSuccessful()
            ->assertSeeLivewire(Index::class);

        $this->actingAs($observer)
            ->get(route('kb.index'))
            ->assertSuccessful()
            ->assertSeeLivewire(Index::class);
    });

    it('prevents roles who are not listed as audience from viewing the article', function () {
        $trainee = createTrainee();
        $instructor = createInstructor();

        $articleForTrainees = Article::factory()->create([
            'status' => ArticleStatus::PUBLISHED,
            'audiences' => ['Trainee'],
        ]);
        $category = $articleForTrainees->category;

        $this->actingAs($trainee)
            ->get(route('kb.show', ['category' => $category, 'article' => $articleForTrainees]))
            ->assertSuccessful();

        $this->actingAs($instructor)
            ->get(route('kb.show', ['category' => $category, 'article' => $articleForTrainees]))
            ->assertForbidden();
    });

    it('prevents viewing of draft articles', function () {
        $trainee = createTrainee();
        $instructor = createInstructor();
    
        $draftArticle = Article::factory()->create([
            'status' => ArticleStatus::DRAFT,
            'audiences' => ['Trainee', 'Instructor'],
        ]);
        $category = $draftArticle->category;
    
        $this->actingAs($trainee)
            ->get(route('kb.show', ['category' => $category, 'article' => $draftArticle]))
            ->assertForbidden();
    
        $this->actingAs($instructor)
            ->get(route('kb.show', ['category' => $category, 'article' => $draftArticle]))
            ->assertForbidden();
    });

    it('updates the view count', function () {
        $user1 = createTrainee();
    
        $this->actingAs($user1)
            ->get(route('kb.show', ['category' => $this->category, 'article' => $this->article]));
    
        $this->assertDatabaseHas('kb_articles', [
            'id' => $this->article->id,
            'view_count' => 1,
        ]);
    });
    
    it('can mark an article as helpful', function () {
        $user = createTrainee();
    
        // I can't get assertSessionHas() to work with Livewire --Mikko
        Livewire::actingAs($user)
            ->test(Show::class, ['category' => $this->category, 'article' => $this->article])
            ->call('markHelpful', $this->article->id, true)
            ->assertSee('Thank you for your feedback.')
            ->assertDontSee('How could we improve the article?');
    
        $this->assertDatabaseHas('kb_articles', [
            'id' => $this->article->id,
            'helpful_count' => 1,
        ]);
    });
    
    it('can mark an article as not helpful and show feedback form', function () {
        $user = createTrainee();
    
        Livewire::actingAs($user)
            ->test(Show::class, ['category' => $this->category, 'article' => $this->article])
            ->call('markHelpful', $this->article->id, false)
            ->assertSet('showFeedbackForm', true)
            ->assertSee('How could we improve the article?');
    
        $this->assertDatabaseHas('kb_articles', [
            'id' => $this->article->id,
            'not_helpful_count' => 1,
        ]);
    });
    
    test('trainees and instructors can submit feedback', function () {
        $trainee = createTrainee();
        $instructor = createInstructor();
        $observer = createObserver();

        // Test validation
        Livewire::actingAs($trainee)
            ->test(Show::class, ['category' => $this->category, 'article' => $this->article])
            ->set('feedback', '')
            ->call('submitFeedback')
            ->assertHasErrors(['feedback' => ['required']]);

        Livewire::actingAs($trainee)
            ->test(Show::class, ['category' => $this->category, 'article' => $this->article])
            ->set('feedback', 'No.')
            ->call('submitFeedback')
            ->assertHasErrors(['feedback' => ['min']]);

        Livewire::actingAs($trainee)
            ->test(Show::class, ['category' => $this->category, 'article' => $this->article])
            ->set('feedback', fake()->text(500))
            ->call('submitFeedback')
            ->assertHasErrors(['feedback' => ['max']]);
    
        // Actual feedback entry
        Livewire::actingAs($trainee)
            ->test(Show::class, ['category' => $this->category, 'article' => $this->article])
            ->set('feedback', 'This sucks.')
            ->call('submitFeedback')
            ->assertSee('Thank you for your feedback.')
            ->assertSet('showFeedbackForm', false);
    
        $this->assertDatabaseHas('kb_feedback', [
            'user_id' => $trainee->id,
            'article_id' => $this->article->id,
            'feedback' => 'This sucks.',
        ]);

        // Instructor can submit as well
        Livewire::actingAs($instructor)
            ->test(Show::class, ['category' => $this->category, 'article' => $this->article])
            ->set('feedback', 'I spotted a typo.')
            ->call('submitFeedback')
            ->assertSee('Thank you for your feedback.')
            ->assertSet('showFeedbackForm', false);
    
        $this->assertDatabaseHas('kb_feedback', [
            'user_id' => $instructor->id,
            'article_id' => $this->article->id,
            'feedback' => 'I spotted a typo.',
        ]);

        // Observer cannot submit or see the feedback
        Livewire::actingAs($observer)
            ->test(Show::class, ['category' => $this->category, 'article' => $this->article])
            ->assertDontSee('Was this article helpful?')
            ->set('feedback', 'I should be able to do this.')
            ->call('submitFeedback')
            ->assertForbidden();
    });

    it('prevents rating the article more than once during the session', function() {
        $user = createTrainee();

        Livewire::actingAs($user)
            ->test(Show::class, ['category' => $this->category, 'article' => $this->article])
            ->assertSee('Was this article helpful?')
            ->call('markHelpful', $this->article->id, true)
            ->assertSet('hasVoted', true);

        Livewire::actingAs($user)
            ->test(Show::class, ['category' => $this->category, 'article' => $this->article])
            ->assertDontSee('Was this article helpful?')
            ->assertSet('hasVoted', true);
    });

    test('authorization prevents more than two submissions per article per user', function () {
        $user = createTrainee();
    
        Livewire::actingAs($user)
            ->test(Show::class, ['category' => $this->category, 'article' => $this->article])
            ->set('feedback', 'First.')
            ->call('submitFeedback');
    
        $this->assertDatabaseHas('kb_feedback', [
            'user_id' => $user->id,
            'article_id' => $this->article->id,
            'feedback' => 'First.',
        ]);

        Livewire::actingAs($user)
            ->test(Show::class, ['category' => $this->category, 'article' => $this->article])
            ->set('feedback', 'Second.')
            ->call('submitFeedback');

        $this->assertDatabaseHas('kb_feedback', [
            'user_id' => $user->id,
            'article_id' => $this->article->id,
            'feedback' => 'Second.',
        ]);

        Livewire::actingAs($user)
            ->test(Show::class, ['category' => $this->category, 'article' => $this->article])
            ->set('feedback', 'Third.')
            ->call('submitFeedback')
            ->assertForbidden();
    });

})->group('kb');