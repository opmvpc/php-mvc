<?php

describe('View Tests', function () {
    it('should render a basic view', function () {
        $view = view('basic');

        expect($view->__toString())->toBe('<h1>Hello</h1>'.PHP_EOL);
    });

    it('should render a view with a layout', function () {
        $view = view('index');
        expect($view->__toString())->toBe(<<<'HTML'
        <body>
            <header>
                <nav>
            <ul>
                <li><a href="/">Accueil</a></li>
                <li><a href="/articles">Articles</a></li>
            </ul>
        </nav>
            </header>
            <main>
                <h1>PHP MVC Framework</h1>
        <p>Welcome to the PHP MVC Framework.</p>
            </main>
        </body>

        HTML);
    });

    it('should render a view with data, a loop and a component', function () {
        $view = view('articles/index', [
            'articles' => [
                [
                    'id' => 1,
                    'title' => 'Article 1',
                    'content' => 'Content 1',
                ],
                [
                    'id' => 2,
                    'title' => 'Article 2',
                    'content' => 'Content 2',
                ],
            ],
        ]);
        expect($view->__toString())->toBe(<<<'HTML'
        <body>
            <header>
                <nav>
            <ul>
                <li><a href="/">Accueil</a></li>
                <li><a href="/articles">Articles</a></li>
            </ul>
        </nav>
            </header>
            <main>
                <h1>Articles</h1>
            <a href="/articles/1">
            <h2>Article 1</h2>
            <p>Content 1</p>
        </a>
            <a href="/articles/2">
            <h2>Article 2</h2>
            <p>Content 2</p>
        </a>
            </main>
        </body>

        HTML);
    });

    it('should render a view with data', function () {
        $view = view('articles/show', [
            'article' => [
                'id' => 1,
                'title' => 'Article 1',
                'content' => 'Content 1',
            ],
        ]);
        expect($view->__toString())->toBe(<<<'HTML'
        <body>
            <header>
                <nav>
            <ul>
                <li><a href="/">Accueil</a></li>
                <li><a href="/articles">Articles</a></li>
            </ul>
        </nav>
            </header>
            <main>
                <h1>Article 1</h1>
        <p>Content 1</p>
            </main>
        </body>

        HTML);
    });

    it('should throw an error if view is not found', function () {
        expect(function () {
            view('not_found');
        })->toThrow(new \Exception('View not found with path: not_found'));
    });
});
