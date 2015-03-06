<?php


class BasicTest extends PHPUnit_Framework_TestCase
{
    public function testBootstrap()
    {
        ob_start();
        \Ilex\Autoloader::run(__DIR__ . '/app', __DIR__ . '/runtime', '/', 'GET');
        $output = ob_get_clean();
        $this->assertEquals('Hello world!', $output);
    }
}