<?php

namespace App;

use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;

class Router
{
    private $routes = [];

    public function add(string $route, string $method, callable $callback): void
    {
        $this->routes []= [$route, $method, $callback];
    }

    public function serve(Request $request, Response $response): void
    {
        foreach ($this->routes as [$route, $method, $callback]) {
            $count = preg_match_all($route, $request->server['request_uri'], $matches);

            if ($method !== $request->getMethod() || $count === 0) {
                continue;
            }

            $callback($request, $response, $matches ?: []);

            return;
        }

        $response->status(404);
        $response->end(json_encode(['error' => 'Route not found']));
    }
}