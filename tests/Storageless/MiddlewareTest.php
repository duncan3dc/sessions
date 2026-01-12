<?php

namespace duncan3dc\SessionsTest\Storageless;

use Dflydev\FigCookies\SetCookie;
use duncan3dc\Sessions\SessionInterface;
use duncan3dc\Sessions\Storageless\Middleware;
use duncan3dc\Sessions\Storageless\Session;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Laminas\Stratigility\MiddlewarePipe;
use Lcobucci\JWT\Configuration as JwtConfig;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PSR7Sessions\Storageless\Http\Configuration;
use PSR7Sessions\Storageless\Http\SessionMiddleware;

use function Laminas\Stratigility\middleware;
use function method_exists;

class MiddlewareTest extends TestCase
{
    private function getSession(): Middleware
    {
        $jwt = JwtConfig::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText("mBC5v1sOKVvbdEitdSBenu59nfNfhwkedkJVNabosTw="),
        );
        if (method_exists(Configuration::class, "fromJwtConfiguration")) {
            /** @var Configuration $config */
            $config = Configuration::fromJwtConfiguration($jwt);
        } else {
            $config = new Configuration($jwt);
        }

        $middleware = new SessionMiddleware(
            $config->withCookie(
                SetCookie::create('an-example-cookie-name')
                    ->withSecure(false)
                    ->withHttpOnly(true)
                    ->withPath('/'),
            )->withIdleTimeout(60),
        );

        return new Middleware($middleware);
    }


    public function testItWorks(): void
    {
        $app = new MiddlewarePipe();
        $app->pipe($this->getSession());

        $app->pipe(middleware(function (ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
            $session = $request->getAttribute(SessionInterface::class);
            $this->assertInstanceOf(SessionInterface::class, $session);

            $response = new Response();
            $response->getBody()->write(get_class($session));
            return $response;
        }));

        $request = ServerRequest::fromGlobals();
        $response = $app->handle($request);
        $this->assertSame(Session::class, (string) $response->getBody());
    }
}
