<?php

namespace duncan3dc\Sessions\Storageless;

use duncan3dc\Sessions\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;

final class RequestHandler implements RequestHandlerInterface
{
    /**
     * @var RequestHandlerInterface $handler The handler to delegate to.
     */
    private $handler;


    /**
     * Create a new instance.
     *
     * @param RequestHandlerInterface $handler A handler to delegate to
     */
    public function __construct(RequestHandlerInterface $handler)
    {
        $this->handler = $handler;
    }


    /**
     * Wrap the storageless session from the request in our session interface.
     *
     * @param ServerRequestInterface $request The request to handle
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $storageless = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        if (!$storageless instanceof \PSR7Sessions\Storageless\Session\SessionInterface) {
            throw new \InvalidArgumentException("Request must have the session attribute set to an instance of " . \PSR7Sessions\Storageless\Session\SessionInterface::class);
        }

        $session = new Session($storageless);

        $request = $request->withAttribute(SessionInterface::class, $session);

        return $this->handler->handle($request);
    }
}
