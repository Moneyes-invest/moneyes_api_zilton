<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Hyperf\HttpServer;

use FastRoute\Dispatcher;
use Hyperf\Context\Context;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\MiddlewareInitializerInterface;
use Hyperf\Contract\OnRequestInterface;
use Hyperf\Coordinator\Constants;
use Hyperf\Coordinator\CoordinatorManager;
use Hyperf\Dispatcher\HttpDispatcher;
use Hyperf\ExceptionHandler\ExceptionHandlerDispatcher;
use Hyperf\HttpMessage\Server\Connection\SwooleConnection;
use Hyperf\HttpMessage\Server\Request as Psr7Request;
use Hyperf\HttpMessage\Server\Response as Psr7Response;
use Hyperf\HttpServer\Contract\CoreMiddlewareInterface;
use Hyperf\HttpServer\Exception\Handler\HttpExceptionHandler;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Hyperf\Utils\SafeCaller;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class Server implements OnRequestInterface, MiddlewareInitializerInterface
{
    protected array $middlewares = [];

    protected ?CoreMiddlewareInterface $coreMiddleware = null;

    protected array $exceptionHandlers = [];

    protected ?Dispatcher $routerDispatcher = null;

    protected ?string $serverName = null;

    public function __construct(
        protected ContainerInterface $container,
        protected HttpDispatcher $dispatcher,
        protected ExceptionHandlerDispatcher $exceptionHandlerDispatcher,
        protected ResponseEmitter $responseEmitter
    ) {
    }

    public function initCoreMiddleware(string $serverName): void
    {
        $this->serverName = $serverName;
        $this->coreMiddleware = $this->createCoreMiddleware();
        $this->routerDispatcher = $this->createDispatcher($serverName);

        $config = $this->container->get(ConfigInterface::class);
        $this->middlewares = $config->get('middlewares.' . $serverName, []);
        $this->exceptionHandlers = $config->get('exceptions.handler.' . $serverName, $this->getDefaultExceptionHandler());
    }

    public function onRequest($request, $response): void
    {
        try {
            CoordinatorManager::until(Constants::WORKER_START)->yield();

            [$psr7Request, $psr7Response] = $this->initRequestAndResponse($request, $response);

            $psr7Request = $this->coreMiddleware->dispatch($psr7Request);
            /** @var Dispatched $dispatched */
            $dispatched = $psr7Request->getAttribute(Dispatched::class);
            $middlewares = $this->middlewares;
            if ($dispatched->isFound()) {
                $registeredMiddlewares = MiddlewareManager::get($this->serverName, $dispatched->handler->route, $psr7Request->getMethod());
                $middlewares = array_merge($middlewares, $registeredMiddlewares);
            }

            $psr7Response = $this->dispatcher->dispatch($psr7Request, $middlewares, $this->coreMiddleware);
        } catch (Throwable $throwable) {
            // Delegate the exception to exception handler.
            $psr7Response = $this->container->get(SafeCaller::class)->call(function () use ($throwable) {
                return $this->exceptionHandlerDispatcher->dispatch($throwable, $this->exceptionHandlers);
            }, static function () {
                return (new Psr7Response())->withStatus(400);
            });
        } finally {
            // Send the Response to client.
            if (! isset($psr7Response) || ! $psr7Response instanceof ResponseInterface) {
                return;
            }
            if (isset($psr7Request) && $psr7Request->getMethod() === 'HEAD') {
                $this->responseEmitter->emit($psr7Response, $response, false);
            } else {
                $this->responseEmitter->emit($psr7Response, $response);
            }
        }
    }

    public function getServerName(): string
    {
        return $this->serverName;
    }

    /**
     * @return $this
     */
    public function setServerName(string $serverName)
    {
        $this->serverName = $serverName;
        return $this;
    }

    protected function createDispatcher(string $serverName): Dispatcher
    {
        $factory = $this->container->get(DispatcherFactory::class);
        return $factory->getDispatcher($serverName);
    }

    protected function getDefaultExceptionHandler(): array
    {
        return [
            HttpExceptionHandler::class,
        ];
    }

    protected function createCoreMiddleware(): CoreMiddlewareInterface
    {
        return make(CoreMiddleware::class, [$this->container, $this->serverName]);
    }

    /**
     * Initialize PSR-7 Request and Response objects.
     * @param mixed $request swoole request or psr server request
     * @param mixed $response swoole response or swow connection
     */
    protected function initRequestAndResponse($request, $response): array
    {
        Context::set(ResponseInterface::class, $psr7Response = new Psr7Response());

        if ($request instanceof ServerRequestInterface) {
            $psr7Request = $request;
        } else {
            $psr7Request = Psr7Request::loadFromSwooleRequest($request);
            $psr7Response->setConnection(new SwooleConnection($response));
        }

        Context::set(ServerRequestInterface::class, $psr7Request);
        return [$psr7Request, $psr7Response];
    }
}
