<?php

class RequestHandler
{
    /**
     * @var array
     */
    public $headers = [];

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

    /**
     * @param string $path
     * @param string|string[] $method
     * @param callable $callback
     * @param array $args
     */
    public function addRoute(string $path, $method, Callable $callback, array $args = [])
    {
        $methods = is_array($method) ? $method : [$method];

        foreach ($methods as $method) {
            $this->routes[$method][$path]['cb'] = $callback;
            $this->routes[$method][$path]['args'] = $args;
        }
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
        $args = array_merge(
            [
                'request' => $request,
            ],
            $args ?? [],
            $this->routes[$method][$path]['args'] ?? [],
            [$this]
        );

        if (is_callable($cb)) {
            return call_user_func_array($cb, $args);
        }

        return static::response("Not found.", 404);
    }

    public static function response($data, int $code = 200, array $headers = [])
    {
        return new \React\Http\Response(
            $code,
            $headers,
            json_encode($data)
        );
    }

}
