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

    public function testCallingController()
    {
        $this->assertEquals('See all projects.', Test::run('/projects'), 'Fail to visit the controller\'s index page.');
        $this->assertEquals('Oops, 404!', Test::run('/project/oops'), 'Fail to report 404 for invalid url pattern.');
        $this->assertEquals('You\'re looking at Project-23', Test::run('/project/23'), 'Fail to call one of the controller\'s functions.');
    }

}