<?php


class PlayController extends \Ilex\Base\Controller\Base
{
    /**
     * @param \Ilex\Route\Route $Route
     */
    public function resolve($Route)
    {
        $Route->get('/', function () {
            echo('Come and play!');
        });
        $Route->get('/(num)', $this, 'view');
        $Route->get('(all)', function ($url) {
            echo('Sorry but "' . substr($url, 1) . '" is not here. 404.');
        });
    }

    public function view($id)
    {
        echo('Play No.' . $id . '?');
    }
}