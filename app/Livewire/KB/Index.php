<?php

namespace App\Livewire\KB;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\KnowledgeBase\Article;
use App\Models\KnowledgeBase\Category;
use Illuminate\Contracts\Database\Eloquent\Builder;

class Index extends Component
{
    public $search = '';

    #[Computed]
    public function kbCategories()
    {
        $userRole = auth()->user()->role->name;

        $categories = Category::whereHas('articles', function (Builder $query) use ($userRole) {
            if (in_array($userRole, ['Admin', 'Manager', 'Staff'])) {
                $query->published();
            } else {
                $query->published()
                      ->whereJsonContains('audiences', $userRole);
            }
        })->with(['articles' => function (Builder $query) use ($userRole) {
            if (in_array($userRole, ['Admin', 'Manager', 'Staff'])) {
                $query->published();
            } else {
                $query->published()
                      ->whereJsonContains('audiences', $userRole);
            }
        }])->orderBy('name', 'asc')->get();

        // dd($categories);

        return $categories;
    }

    #[Computed]
    public function SearchResults()
    {
        if (empty($this->search)) {
            return collect();
        }
    
        $userRole = auth()->user()->role->name;
    
        $searchTerm = '%' . $this->search . '%';
    
        // There might be markdown characters in the db entry so they're ignored below
        return Article::published()
                ->when(!in_array($userRole, ['Admin', 'Manager', 'Staff']), function ($query) use ($userRole) {
                    $query->whereJsonContains('audiences', $userRole);
                })
                ->where(function ($query) use ($searchTerm) {
                    $query->where('title', 'like', $searchTerm)
                        ->orWhere('summary', 'like', $searchTerm)
                        ->orWhereRaw('LOWER(REPLACE(REPLACE(REPLACE(content, "*", ""), "~~", ""), "**", "")) LIKE ?', [strtolower($searchTerm)]);
                })
                ->with('category')
                ->take(5)
                ->get();
    }
    
    public function render()
    {
        return view('livewire.k-b.index', ['searchResults' => $this->searchResults]);
    }
}
