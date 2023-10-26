<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Framework\Routing\Context;
use Framework\View\View;

class ArticleController extends BaseController
{
    public function index(): View
    {
        $articles = Article::all();

        return new View('articles/index', [
            'articles' => $articles,
        ]);
    }

    public function show(Context $context): View
    {
        $id = $context->route()->params()['articleId'];
        $article = Article::findOrFail($id);

        return new View('articles/show', ['article' => $article]);
    }
}
