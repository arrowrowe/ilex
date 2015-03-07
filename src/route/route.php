<?php

namespace Ilex\Route;


use Ilex\Core\Loader;


/**
 * Class RouteLib
 * @package Ilex\Route
 */
class RouteLib
{
    public static function getPattern($description)
    {
        foreach (array(
                '(any)' => '([^/]+?)',
                '(num)' => '([0-9]+?)'
            ) as $k => $v) {
            $description = str_replace($k, $v, $description);
        }
        return '@^' . $description . '$@';
    }

    /**
     * @param $uri
     * @return array|string Return [$function, $params] if parameters found.
     */
    public static function getFunction($uri)
    {
        // Look for the first '/'.
        $index = strpos($uri, '/');
        // Begins with '/'.
        if ($index === 0) {
            // Cut the string
            if (($uri = substr($uri, 1)) === FALSE) {
                // Fail to cut cause the uri is '/' itself.
                $uri = '';
                $index = FALSE;
            } else {
                // Now the first '/' is excluded. Look for the fist '/' again.
                $index = strpos($uri, '/');
            }
        }
        // '/' not found.
        if ($index === FALSE) {
            $function = $uri;
            $params = array();
        } else {
            $function = substr($uri, 0, $index);
            $params = explode('/', substr($uri, $index + 1));
        }
        if ($function === '') {
            $function = 'index';
        }
        return count($params) ? array($function, $params) : $function;
    }

}


/**
 * Class RouteRule
 * @package Ilex\Route
 */
class RouteRule
{
    private $pattern;
    private $handler;
    private $function;

    public function __construct($description, $handler, $function)
    {
        $this->pattern = RouteLib::getPattern($description);
        $this->handler = $handler;
        $this->function = $function;
    }

    public function fit($route)
    {
        if (preg_match($this->pattern, $route->uri, $matches)) {
            unset($matches[0]);
            $route->params = array_merge($route->params, $matches);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function handle($route)
    {
        if (is_string($this->handler)) {
            return call_user_func_array(array(
                Loader::controller($this->handler),
                is_null($this->function) ? 'index' : $this->function
            ), $route->params);
        } elseif (is_callable($this->handler)) {
            return call_user_func_array($this->handler, $route->params);
        } else {
            return NULL;
        }
    }

}


/**
 * Class Route
 * @package Ilex\Route
 */
class Route
{
    private $rules = array();
    private $rulesController = array();
    public $uri = '';
    public $method;
    public $params = array();

    public function __construct($method = '')
    {
        $this->method = $method;
    }

    public function  get($description, $handler, $function = NULL) { $this->method === 'GET'  AND $this->_add_rule($description, $handler, $function); }
    public function post($description, $handler, $function = NULL) { $this->method === 'POST' AND $this->_add_rule($description, $handler, $function); }
    public function  put($description, $handler, $function = NULL) { $this->method === 'PUT'  AND $this->_add_rule($description, $handler, $function); }

    private function _add_rule($description, $handler, $function)
    {
        $this->rules[] = new RouteRule($description, $handler, $function);
    }

    public function controller($description, $handler)
    {
        $this->rulesController[$description] = $handler;
    }

    public function resolve($uri)
    {
        $this->uri = $uri;
        $this->params = array();
        foreach ($this->rulesController as $description => $handler) {
            $length = strlen($description);
            if (substr($uri, 0, $length) === $description) {
                if (($this->uri = substr($uri, $length)) === FALSE) {
                    $this->uri = '';
                }
                $function = RouteLib::getFunction($this->uri);
                if (is_array($function)) {
                    $this->params = $function[1];
                    $function = $function[0];
                }
                $controller = Loader::controller($handler);
                $controller->Route = $this;
                if (method_exists($controller, $this->method . $function)) {
                    return call_user_func_array(array($controller, $this->method . $function), $this->params);
                } elseif (method_exists($controller, $function)) {
                    return call_user_func_array(array($controller, $function), $this->params);
                } elseif (method_exists($controller, 'resolve')) {
                    return $controller->resolve();
                } else {
                    $this->uri = $uri;
                    continue;
                }
            }
        }
        /** @var RouteRule $rule */
        foreach ($this->rules as $rule) {
            if ($rule->fit($this)) {
                return $rule->handle($this);
            }
        }
        throw new \Exception('Page not found', 404);
    }

}