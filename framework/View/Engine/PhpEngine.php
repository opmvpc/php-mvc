<?php

namespace Framework\View\Engine;

use App\App;
use Framework\View\View;

class PhpEngine implements EngineInterface
{
    /**
     * @var array<string, string>
     */
    protected array $layouts;

    protected View $view;

    public function __construct()
    {
        $this->layouts = [];
    }

    public function render(View $view): string
    {
        $this->view = $view;
        $data = $view->data();
        extract($data);

        ob_start();

        include $view->path();
        $contents = ob_get_contents();
        ob_end_clean();

        if (false === $contents) {
            throw new \RuntimeException('Failed to get contents of view.');
        }

        if (isset($this->layouts[$view->path()])) {
            $layout = $this->layouts[$view->path()];
            $data = \array_merge($view->data(), ['contents' => $contents]);
            $contentsWithLayout = $this->render(new View($layout, $data, $view->baseViewPath()));

            return $contentsWithLayout;
        }

        return $contents;
    }

    protected function escape(string $content): string
    {
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    protected function extends(string $template): self
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        // @phpstan-ignore-next-line
        $this->layouts[\realpath($backtrace[0]['file'])] = $template;

        return $this;
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function includes(string $template, array $data = []): void
    {
        echo $this->render(new View($template, $data, $this->view->baseViewPath()));
    }

    protected function viteAssets(): string
    {
        if ('development' === App::get()->config('app.env')) {
            // check with a http request if http://localhost:5173/resources/app.js responds
            $manifestUrl = 'http://localhost:5173/resources/app.js';
            $manifest = null;

            $manifest = @file_get_contents($manifestUrl);

            if (false === $manifest || null === $manifest) {
                throw new \RuntimeException('Vite manifest file not found. Please run "npm run dev" command.');
            }

            return <<<'HTML'
            <script type="module" src="http://localhost:5173/@vite/client"></script>
            <script type="module" src="http://localhost:5173/resources/app.js"></script>
            HTML;
        }

        $manifestPath = App::get()->basePath().'/public/assets/manifest.json';
        if (!file_exists($manifestPath)) {
            throw new \RuntimeException('Vite manifest file not found.');
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);

        $path = 'resources/main.js';
        if (!isset($manifest[$path])) {
            throw new \RuntimeException("Asset not found in Vite manifest: {$path}");
        }

        return <<<HTML
        <link rel="stylesheet" href="/assets/{$manifest[$path]['css']}">
        <script type="module" src="/assets/{$manifest[$path]['file']}"></script>
        HTML;
    }
}
