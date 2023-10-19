<?php

namespace Framework\Routing;

enum HttpVerb: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
}
