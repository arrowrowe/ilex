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
                '(num)' => '([0-9]+?)',
                '(all)' => '(.+?)'
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
 * @method bool get(string $description, $handler, $function = NULL)
 * @method bool post(string $description, $handler, $function = NULL)
 * @method bool put(string $description, $handler, $function = NULL)
 * @method bool delete(string $description, $handler, $function = NULL)
 */
class Route
{
    private $uri;
    private $uris = array();
    private $method;
    private $settled = FALSE;
    private $cancelled = FALSE;
    private $result = NULL;
    private $params = array();

    public function __construct($method, $uri)
    {
        $this->method = $method;
        $this->uri = $uri;
    }

    public function result() { return $this->result; }

    public function __call($name, $arguments)
    {
        if (!$this->settled AND strtoupper($name) === $this->method) {
            return call_user_func_array(array($this, 'fit'), $arguments);
        } else {
            return FALSE;
        }
    }

    public function controller($description, $handler) { $this->settled OR $this->fitController($description, $handler); }
    public function group($description, $handler) { $this->settled OR $this->fitGroup($description, $handler); }

    public function merge($vars)
    {
        $this->params = array_merge($this->params, $vars);
    }

    private function end($result)
    {
        if ($this->cancelled) {
            $this->cancelled = FALSE;
        } else {
            $this->settled = TRUE;
            $this->result = $result;
        }
    }

    private function fit($description, $handler, $function = NULL)
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
        if (is_string($handler) OR !($handler instanceof \Closure)) {
            $this->end(
                call_user_func_array(array(
                    is_string($handler) ? Loader::controller($handler) : $handler,
                    is_null($function) ? 'index' : $function
                ), $this->params)
            );
        } elseif (is_callable($handler)) {
            $this->end(
                call_user_func_array($handler, $this->params)
            );
        }
    }

    private function getRestURI($description)
    {
        $length = strlen($description);
        if (substr($this->uri, 0, $length) !== $description) {
            return FALSE;
        } else {
            $this->uris[] = $this->uri;
            $this->uri = (
                ($uri = substr($this->uri, $length)) === FALSE ? '/' : $uri
            );
            return TRUE;
        }
    }

    public function back()
    {
        if ($this->settled) {
            return FALSE;
        } else {
            $this->_pop();
            $this->cancelled = TRUE;
            return TRUE;
        }
    }

    private function _pop()
    {
        $this->uri = array_pop($this->uris);
    }

    private function fitGroup($description, $handler)
    {
        if ($this->getRestURI($description)) {
            $this->end(call_user_func($handler, $this));
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function fitController($description, $handler)
    {
        if (!$this->getRestURI($description)) {
            return FALSE;
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
            $this->_pop();
            return FALSE;
        }

        $this->end(call_user_func_array(array($controller, $fn), $params));
        return TRUE;
    }

}