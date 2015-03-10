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

        // Just a test for `group` inside a controller's `resolve`...
        $Route->group('/play', function ($Route) {
            /** @var \Ilex\Route\Route $Route */
            $Route->get('/(num)', $this, 'view');
            $Route->back();
        });

        $Route->group('/no-back', function ($Route) {
            /** @var \Ilex\Route\Route $Route */
            $Route->get('/', function () {
                echo('No back here...');
            });
            /*
             * 404 should be handled manually here.
             * Add `$Route->get('(all)', ...)` or `$Route->get('.*')` to response.
             * Add `$Route->back()` to fallback.
             */
        });

        $Route->get('(all)', function ($url) {
            echo('Sorry but "' . substr($url, 1) . '" is not here. 404.');
        });
    }

    public function view($id)
    {
        echo('Play No.' . $id . '?');
    }
}