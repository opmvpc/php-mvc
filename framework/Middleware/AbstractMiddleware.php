<?php

declare(strict_types=1);

namespace Framework\Middleware;

use Framework\Requests\MessageInterface;
use Framework\Routing\Context;

abstract class AbstractMiddleware
{
    abstract public function handle(Context $context): Context|MessageInterface;
}
