<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Framework\Requests\Response;
use Framework\Routing\Context;
use Framework\Routing\Router;
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

    public function create(): View
    {
        return new View('articles/create');
    }

    public function store(Context $context): Response
    {
        dd($context->request());

        return Router::redirect('/articles');
    }
}
