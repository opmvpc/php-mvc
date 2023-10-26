<?php

namespace Framework\View\Engine;

use Framework\View\View;

interface EngineInterface
{
    public function render(View $view): string;
}
