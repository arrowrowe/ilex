<?php


class AboutController extends \Ilex\Base\Controller\Base
{
    public function index()
    {
        echo('about');
    }

    public function join($group = 'tech')
    {
        echo('Join ' . $group . '!');
    }
}