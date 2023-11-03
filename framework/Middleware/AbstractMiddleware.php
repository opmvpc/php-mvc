<?php

namespace Framework\Middleware;

use Framework\Requests\MessageInterface;
use Framework\Routing\Context;

abstract class AbstractMiddleware
{
    abstract public function handle(Context $context): Context|MessageInterface;
}
