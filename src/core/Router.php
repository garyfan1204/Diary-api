<?php

namespace Vivian\DiaryApi\core;  //namespace是目錄

use Exception;
use Reflection;
use ReflectionMethod;

class Router
{

    private array $routes = [];
    private Request $request;
    private Response $response;

    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get($path, $callback = null)
    {
        $this->routes["GET"][$path] = $callback;
    }

    public function post($path, $callback = null)
    {
        $this->routes["POST"][$path] = $callback;
    }

    public function put($path, $callback = null)
    {
        $this->routes["PUT"][$path] = $callback;
    }

    public function patch($path, $callback = null)
    {
        $this->routes["PATCH"][$path] = $callback;
    }

    public function delete($path, $callback = null)
    {
        $this->routes["DELETE"][$path] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;

        if ($callback) {
            try {
                if (is_array($callback)) {
                    $callback[0] = new $callback[0]();
                    $reflection = new ReflectionMethod($callback[0], $callback[1]); //reflection是找到目標的程式，取得目標類別的方法或Attribute。此處的callback是index.php中的類別及方法??
                    $middlewares = $reflection->getAttributes();
                    foreach ($middlewares as $middleware) {  //得到屬性後，可能有多個Attribute，因為是陣列，所以用foreach取每個Attribute
                        $instance = $middleware->newInstance();  //將Attribute初始化，初始化後即為一個物件
                        $instance->handle($this->request, $this->response); //物件就可執行Authorize中的handle方法
                    }
                }

                return $this->response->returnbody(call_user_func($callback, $this->request, $this->response));
            } catch (Exception $e) {
                return $this->response->returnBody(msg: $e->getMessage(), code: $e->getCode() ?? 500);  //code:500為無法預測的錯誤，例如sql壞掉
            }
        }
        $this->response->returnbody(msg: 'Not Found', code: 404);
    }
}