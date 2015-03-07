<?php


use \Ilex\Core\Loader;

/** @var \Ilex\Route\Route $Route */

$Route->get('/', function () {
    echo('Hello world!');
});

$Route->post('/user/(any)', function ($name) {
    /** @var InputModel $Input */
    $Input = Loader::model('sys/Input');
    echo('Hello ' . $Input->post('title', 'Guest') . ' ' . $name . '!');
});
