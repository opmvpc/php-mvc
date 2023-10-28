<?php

declare(strict_types=1);

namespace Framework\View\Engine;

use Framework\View\View;

interface EngineInterface
{
    public function render(View $view): string;
}
