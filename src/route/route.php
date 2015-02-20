<?php

namespace Ilex\Route;


use Ilex\Loader;


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


class Route
{
    private $rules = array(
            'GET' => array(),
            'POST' => array(),
            'PUT' => array(),
        );
    private $rulesController = array();
    public $uri = '';
    public $params = array();

    public function  get($description, $handler, $function = NULL) { $this->_add_rule( 'GET', $description, $handler, $function); }
    public function post($description, $handler, $function = NULL) { $this->_add_rule('POST', $description, $handler, $function); }
    public function  put($description, $handler, $function = NULL) { $this->_add_rule( 'PUT', $description, $handler, $function); }

    private function _add_rule($method, $description, $handler, $function)
    {
        $this->rules[$method][] = new RouteRule($description, $handler, $function);
    }

    public function controller($description, $handler)
    {
        $this->rulesController[$description] = $handler;
    }

    public function resolve($uri)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $uri;
        $this->params = array();
        foreach ($this->rulesController as $description => $handler) {
            $length = strlen($description);
            if (substr($uri, 0, $length) === $description) {
                if (($this->uri = $uri = substr($uri, $length)) === FALSE) {
                    $this->uri = $uri = '/';
                }
                $index = strpos($this->uri, '/');
                if ($index === 0) {
                    if (($uri = substr($uri, 1)) === FALSE) {
                        $uri = '';
                        $index = FALSE;
                    } else {
                        $index = strpos($uri, '/');
                    }
                }
                if ($index === FALSE) {
                    $function = $uri;
                } else {
                    $function = substr($uri, 0, $index);
                    $this->params = explode('/', substr($uri, $index + 1));
                }
                ($function === '') && ($function = 'index');
                $controller = Loader::controller($handler);
                $controller->Route = $this;
                if (method_exists($controller, $method . $function)) {
                    return call_user_func_array(array($controller, $method . $function), $this->params);
                } elseif (method_exists($controller, $function)) {
                    return call_user_func_array(array($controller, $function), $this->params);
                } elseif (method_exists($controller, 'resolve')) {
                    return $controller->resolve();
                } else {
                    return Loader::error(404);
                }
            }
        }
        /** @var RouteRule $rule */
        foreach ($this->rules[$method] as $rule) {
            if ($rule->fit($this)) {
                return $rule->handle($this);
            }
        }
        return Loader::error(404);
    }

}