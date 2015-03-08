<?php


use \Ilex\Core\Loader;

/** @var \Ilex\Route\Route $Route */

$Route->get('/', function () {
    echo('Hello world!');
});

$Route->post('/user/(any)', function ($name) {
    /** @var \Ilex\Base\Model\sys\Input $Input */
    $Input = Loader::model('sys/Input');
    echo('Hello ' . $Input->post('title', 'Guest') . ' ' . $name . '!');
});

$Route->get('/projects', 'Project');
$Route->get('/project/(num)', 'Project', 'view');
$Route->group('/planet', function ($Route) {
    $Route->get('/', function () {
        echo('Hello Cosmos!');
    });
    $Route->back();
});

$Route->controller('/about', 'About');

$Route->controller('/play', 'Play');


$Route->get('(all)', function ($url) {
    echo('Oops, 404! "' . $url . '" does not exist.');
});