<?php

class RequestHandler
{
    /**
     * @var \Repository
     */
    protected $rep;

    /**
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $routes = [];

    public function __construct(Repository $repository)
    {
        $this->rep = $repository;
    }

    public function addRoute(string $path, string $method, Callable $callback, array $args = [])
    {
        $this->routes[$method][$path]['cb'] = $callback;
        $this->routes[$method][$path]['args'] = $args;
    }

    public function getResponse(\Psr\Http\Message\ServerRequestInterface $request)
    {
        $this->request = $request;

        $path = $this->request->getUri()->getPath();
        $method = strtolower($this->request->getMethod());

        $cb = $this->routes[$method][$path]['cb'] ?? null;

        if (!$cb && isset($this->routes[$method])) {
            $keys = array_keys($this->routes[$method]);
            foreach ($keys as $key) {
                preg_match('/(\/\{)([a-zA-Z\-\_]+)(\})/i', $key, $matches);
                if (isset($matches[2])) {
                    $args[$matches[2]] = substr($path, 1);
                    $path = $key;
                    break;
                }
            }
        }

        $cb = $this->routes[$method][$path]['cb'] ?? null;
        $args = array_merge(['request' => $request], $args ?? [], $this->routes[$method][$path]['args'] ?? []);

        if (is_callable($cb)) {
            return call_user_func_array($cb, $args);
        }

        return static::response("Not found.", 404);
    }

    public static function response($data, int $code = 200)
    {
        return new \React\Http\Response(
            $code,
            [
                'Content-Type' => 'application/json'
            ],
            json_encode($data)
        );
    }

}
