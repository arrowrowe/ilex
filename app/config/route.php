<?php

$Route->get('/', 'Home');

$Route->get('/foundation', function () {
    Ilex\Loader::views('test/foundation', array(
            'title' => 'Foundation | Welcome!'
        ));
});
