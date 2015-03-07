<?php


use \Ilex\Test;


class RouteTest extends PHPUnit_Framework_TestCase
{
    public function testHelloWorld()
    {
        $this->assertEquals('Hello world!', Test::run());
    }
}