<?php

declare(strict_types=1);

namespace WShafer\Expressive\Symfony\Router;

use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route as SymfonyRoute;
use Zend\Expressive\Router\Route;
use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Router\RouterInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class SymfonyRouteRouter implements RouterInterface
{
    /** @var RouteCollection  */
    protected $collection;


    protected $httpFoundationFactory;

    protected $requestContext;

    protected $urlMatcher;

    protected $generator;

    public function __construct(
        RouteCollection $collection,
        UrlMatcher $urlMatcher,
        UrlGenerator $generator
    ) {
        $this->collection = $collection;
        $this->urlMatcher = $urlMatcher;
        $this->generator = $generator;
    }

    public function addRoute(Route $route): void
    {
        $path = $route->getPath();
        $name = $route->getName();

        $route = new SymfonyRoute(
            $path,
            ['route' => $route],
            [],
            $route->getOptions(),
            null,
            [],
            $route->getAllowedMethods(),
            null
        );

        $this->collection->add($name, $route);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function match(Request $request): RouteResult
    {
        try {
            $match = $this->urlMatcher->match($request->getUri()->getPath());
        } catch (MethodNotAllowedException $e) {
            return RouteResult::fromRouteFailure($e->getAllowedMethods());
        } catch (ResourceNotFoundException $e) {
            return RouteResult::fromRouteFailure(null);
        }

        $route = $match['route'];
        unset($match['route'], $match['_route']);
        return RouteResult::fromRoute($route, $match);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function generateUri(string $name, array $substitutions = [], array $options = []): string
    {
        return $this->generator->generate($name, $substitutions);
    }
}
