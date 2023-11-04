<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
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

        return new View('admin/articles/index', [
            'articles' => $articles,
        ]);
    }

    public function show(Context $context): View
    {
        $id = $context->routeParam('articleId');
        $article = Article::findOrFail($id);

        return new View('admin/articles/show', ['article' => $article]);
    }

    public function create(): View
    {
        return new View('admin/articles/create');
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

        if (!is_string($validated['title']) || !is_string($validated['content'])) {
            throw new \Exception('Article title and content must be strings');
        }

        $article = new Article(
            id: null,
            title: $validated['title'],
            content: $validated['content'],
        );

        $article->save();

        return Router::redirect('/articles');
    }

    public function destroy(Context $context): Response
    {
        $id = $context->routeParam('articleId');
        $article = Article::findOrFail($id);
        $article->delete();

        return Router::redirect('/articles');
    }
}
