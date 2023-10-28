<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Article;
use Framework\Requests\Response;
use Framework\Routing\Context;
use Framework\Routing\Router;
use Framework\Validator\Rules\Max;
use Framework\Validator\Rules\Min;
use Framework\Validator\Rules\Required;
use Framework\Validator\Validator;
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
        $id = $context->routeParams('articleId');
        $article = Article::findOrFail($id);

        return new View('articles/show', ['article' => $article]);
    }

    public function create(): View
    {
        return new View('articles/create');
    }

    public function store(Context $context): Response
    {
        $validated = (new Validator([
            'title' => [
                new Required(),
                new Min(3),
                new Max(255),
            ],
            'content' => [
                new Required(),
                new Min(3),
                new Max(5000),
            ],
        ]))->validate($context->postParams());

        // dd($validated);

        return Router::redirect('/articles');
    }
}
