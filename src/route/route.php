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
            if (($paramRaw = substr($uri, $index + 1)) === FALSE) {
                $params = array();
            } else {
                $params = explode('/', substr($uri, $index + 1));
            }
        }
        if ($function === '') {
            $function = 'index';
        }
        return count($params) ? array($function, $params) : $function;
    }
}

/**
 * Class Route
 * @package Ilex\Route
 */
class Route
{
    private $uri;
    private $method;
    private $settled = FALSE;
    private $result = NULL;
    private $params = array();

    public function __construct($method, $uri)
    {
        $this->method = $method;
        $this->uri = $uri;
    }

    public function result() { return $this->result; }

    public function  get($description, $handler, $function = NULL) { $this->settled OR $this->method === 'GET'  AND $this->fit($description, $handler, $function); }
    public function post($description, $handler, $function = NULL) { $this->settled OR $this->method === 'POST' AND $this->fit($description, $handler, $function); }
    public function  put($description, $handler, $function = NULL) { $this->settled OR $this->method === 'PUT'  AND $this->fit($description, $handler, $function); }
    public function controller($description, $handler) { $this->settled OR $this->fitController($description, $handler); }

    public function merge($vars)
    {
        $this->params = array_merge($this->params, $vars);
    }

    private function end($result)
    {
        $this->settled = TRUE;
        $this->result = $result;
    }

    private function fit($description, $handler, $function)
    {
        if (preg_match(RouteLib::getPattern($description), $this->uri, $matches)) {
            unset($matches[0]);
            $this->merge($matches);
            $this->handle($handler, $function);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function handle($handler, $function)
    {
        if (is_string($handler)) {
            $this->end(
                call_user_func_array(array(Loader::controller($handler), is_null($function) ? 'index' : $function), $this->params)
            );
        } elseif (is_callable($handler)) {
            $this->end(
                call_user_func_array($handler, $this->params)
            );
        }
    }

    private function fitController($description, $handler)
    {
        $length = strlen($description);
        if (substr($this->uri, 0, $length) !== $description) {
            return FALSE;
        }

        $uri = $this->uri;
        if (($this->uri = substr($uri, $length)) === FALSE) {
            $this->uri = '';
        }

        $function = RouteLib::getFunction($this->uri);
        if (is_array($function)) {
            $params = $function[1];
            $function = $function[0];
        } else {
            $params = array();
        }

        $controller = Loader::controller($handler);

        if (method_exists($controller, $this->method . $function)) {
            $fn = $this->method . $function;
        } elseif (method_exists($controller, $function)) {
            $fn = $function;
        } elseif (method_exists($controller, 'resolve')) {
            $fn = 'resolve';
            $params = array($this);
        } else {
            return FALSE;
        }

        $this->end(call_user_func_array(array($controller, $fn), $params));
        return TRUE;
    }

}