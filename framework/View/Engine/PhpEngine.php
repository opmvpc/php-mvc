<?php

declare(strict_types=1);

namespace Framework\View\Engine;

use App\App;
use Framework\Routing\Csrf;
use Framework\Support\Session;
use Framework\Support\Str;
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
        \extract($data);

        \ob_start();

        include $view->path();
        $contents = \ob_get_contents();
        \ob_end_clean();

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
        return Str::escape($content);
    }

    protected function extends(string $template): self
    {
        $backtrace = \debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
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

    protected function viteAssets(): void
    {
        if ('development' === App::get()->config('app.env')) {
            // check with a http request if http://localhost:5173/resources/app.js responds
            $manifestUrl = 'http://localhost:5173/resources/app.js';
            $manifest = null;

            $manifest = @\file_get_contents($manifestUrl);

            if (false === $manifest) {
                throw new \RuntimeException('Vite manifest file not found. Please run "npm run dev" command.');
            }

            echo <<<'HTML'
            <script type="module" src="http://localhost:5173/@vite/client"></script>
            <script type="module" src="http://localhost:5173/resources/app.js"></script>
            HTML;

            return;
        }

        $manifestPath = App::get()->basePath().'/public/assets/manifest.json';
        if (!file_exists($manifestPath)) {
            throw new \RuntimeException('Vite manifest file not found.');
        }

        $manifestContent = file_get_contents($manifestPath);
        if (false === $manifestContent) {
            throw new \RuntimeException('Error reading Vite manifest file.');
        }

        $manifest = \json_decode($manifestContent, true);

        $path = 'resources/main.js';

        if (!\is_array($manifest) || !\is_array($manifest[$path])) {
            throw new \RuntimeException("Asset not found in Vite manifest: {$path}");
        }

        if (!\array_key_exists($path, $manifest)) {
            throw new \RuntimeException("Asset not found in Vite manifest: {$path}");
        }

        if (!\array_key_exists('css', $manifest[$path]) || !\array_key_exists('file', $manifest[$path])) {
            throw new \RuntimeException("Asset not found in Vite manifest: {$path}");
        }

        $css = $manifest[$path]['css'] ?? null;
        $file = $manifest[$path]['file'] ?? null;

        echo <<<HTML
        <link rel="stylesheet" href="/assets/{$css}">
        <script type="module" src="/assets/{$file}"></script>
        HTML;
    }

    // @phpstan-ignore-next-line
    protected function route(string $name, array $params = []): string
    {
        return App::get()->router()->route($name, $params);
    }

    protected function old(string $key, string $default = ''): string
    {
        $oldValues = Session::get('_old_inputs', []);

        

        return $oldValues[$key] ?? $default;
    }

    protected function csrf(): void
    {
        $token = Csrf::token();

        echo <<<HTML
        <input type="hidden" name="_csrf_token" value="{$token}">
        HTML;
    }
}
