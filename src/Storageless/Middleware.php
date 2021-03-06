<?php

namespace duncan3dc\Sessions\Storageless;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware as StoragelessMiddleware;

final class Middleware implements MiddlewareInterface
{
    /**
     * @var StoragelessMiddleware $middleware The storageless middleware doing all the hard work.
     */
    private $middleware;


    /**
     * Create a new instance.
     *
     * @param StoragelessMiddleware $middleware
     */
    public function __construct(StoragelessMiddleware $middleware)
    {
        $this->middleware = $middleware;
    }


    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->middleware->process($request, new RequestHandler($handler));
    }
}
