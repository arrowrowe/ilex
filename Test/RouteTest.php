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
        $this->assertEquals('Oops, 404! "/project/oops" does not exist.', Test::run('/project/oops'), 'Fail to report 404 for invalid url pattern.');
        $this->assertEquals('You\'re looking at Project-23', Test::run('/project/23'), 'Fail to call one of the controller\'s functions.');
    }

    public function testControllerIndex()
    {
        $this->assertEquals('about', Test::run('/about'));
        $this->assertEquals('about', Test::run('/about/'));
        $this->assertEquals('about', Test::run('/about//'));
        $this->assertEquals('about', Test::run('/about/index'));
        $this->assertEquals('about', Test::run('/about/index/'));
        $this->assertEquals('about', Test::run('/about/index//'));
    }

    public function testControllerFunction()
    {
        $this->assertEquals('Join tech!', Test::run('/about/join'));
        $this->assertEquals('Join tech!', Test::run('/about/join/'));
        $this->assertEquals('Join whatever!', Test::run('/about/join/whatever'));
        $this->assertEquals('Join whatever!', Test::run('/about/join/whatever/'));
        $this->assertEquals('Join whatever!', Test::run('/about/join/whatever//'));
        $this->assertEquals('Welcome to whatever, Jack!', Test::run('/about/join/whatever/', 'POST'));
        $this->assertEquals('Welcome to whatever, John!', Test::run('/about/join/whatever/', 'POST', array('name' => 'John')));
    }

    public function testControllerResolve()
    {
        $this->assertEquals('Come and play!', Test::run('/play'));
        $this->assertEquals('Come and play!', Test::run('/play/'));
        $this->assertEquals('Play No.7?', Test::run('/play/7'));
        $this->assertEquals('Sorry but "Mr.Rabbit" is not here. 404.', Test::run('/play/Mr.Rabbit'));
    }

}