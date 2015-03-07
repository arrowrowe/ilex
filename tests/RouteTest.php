<?php


use \Ilex\Test;


class RouteTest extends PHPUnit_Framework_TestCase
{
    public function testHelloWorld()
    {
        $this->assertEquals('Hello world!', Test::run(), 'Homepage does not come out as expected.');
    }

    public function testPost()
    {
        $this->assertEquals('Hello Guest Someone!', Test::run('/user/Someone', 'POST'), 'Post with default fails.');
        $this->assertEquals('Hello Mr. Someone!', Test::run('/user/Someone', 'POST', array('title' => 'Mr.')), 'Post fails.');
    }

}