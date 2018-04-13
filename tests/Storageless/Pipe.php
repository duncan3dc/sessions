<?php

namespace duncan3dc\SessionsTest;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

final class Pipe
{
    private $queue = [];

    public function add(MiddlewareInterface $middleware)
    {
        $this->queue[] = $middleware;
    }

    public function __invoke(RequestInterface $request)
    {
        foreach (array_reverse($this->queue) as $middleware) {
            $next = function (RequestInterface $request, ResponseInterface $response) use ($middleware, $next) {
                return $middleware($request, $response, $next);
            };
        }

        return $next($request, $response);
    }
}
