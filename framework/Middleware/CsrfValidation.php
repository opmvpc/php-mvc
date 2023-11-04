<?php

declare(strict_types=1);

namespace Framework\Middleware;

use Framework\Exceptions\ServerError;
use Framework\Requests\MessageInterface;
use Framework\Routing\Context;
use Framework\Routing\Csrf;
use Framework\Routing\HttpVerb;

class CsrfValidation extends AbstractMiddleware
{
    public function handle(Context $context): Context|MessageInterface
    {
        if (HttpVerb::GET !== $context->request()->getMethod()) {
            $post = $context->postParams();
            $json = $context->jsonParams();

            if (!is_array($post)) {
                throw new ServerError('Unable to parse request body');
            }

            $csrfToken = $post['_csrf_token'] ?? $json['_csrf_token'] ?? '';

            Csrf::validate($csrfToken);
        }

        return $context;
    }
}
