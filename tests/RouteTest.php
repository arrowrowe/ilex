<?php


use \Ilex\Test;


class RouteTest extends PHPUnit_Framework_TestCase
{
    public function testHelloWorld()
    {
        $this->assertEquals('Hello world!', Test::run());
    }

    public function testPost()
    {
        $this->assertEquals('Hello Guest Someone!', Test::run('/user/Someone', 'POST'));
        $this->assertEquals('Hello Mr. Someone!', Test::run('/user/Someone', 'POST', array('title' => 'Mr.')));
    }

}