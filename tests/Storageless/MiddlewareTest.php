<?php

namespace duncan3dc\SessionsTest;

use duncan3dc\Sessions\SessionInterface;
use duncan3dc\Sessions\Storageless\Middleware;
use duncan3dc\Sessions\Storageless\Session;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use Zend\Stratigility\MiddlewarePipe;
use function Zend\Stratigility\middleware;

class MiddlewareTest extends TestCase
{

    private function getSession()
    {
        $middleware = SessionMiddleware::fromSymmetricKeyDefaults("mBC5v1sOKVvbdEitdSBenu59nfNfhwkedkJVNabosTw=", 60);

        return new Middleware($middleware);
    }


    public function testItWorks()
    {
        $app = new MiddlewarePipe;
        $app->pipe($this->getSession());

        $app->pipe(middleware(function (ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
            $session = $request->getAttribute(SessionInterface::class);
            $this->assertInstanceOf(SessionInterface::class, $session);

            $response = new Response;
            $response->getBody()->write(get_class($session));
            return $response;
        }));

        $request = ServerRequest::fromGlobals();
        $response = $app->handle($request);
        $this->assertSame(Session::class, (string) $response->getBody());
    }
}
