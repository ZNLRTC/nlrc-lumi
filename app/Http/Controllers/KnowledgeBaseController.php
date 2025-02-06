<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KnowledgeBase\Article;
use App\Models\KnowledgeBase\Category;

class KnowledgeBaseController extends Controller
{
    public function index(Category $category, Article $article)
    {
        $category->load('articles');
        
        return view('kb.show-index', compact('category', 'article'));
    }

    public function show(Request $request, Category $category, Article $article)
    {
        $this->authorize('view', $article);

        $article->load('category');

        $article->increment('view_count');
        
        return view('kb.show-article', compact('category', 'article'));
    }
}
