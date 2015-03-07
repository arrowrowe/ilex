<?php


class ProjectController extends \Ilex\Base\Controller\Base
{
    public function index()
    {
        echo('See all projects.');
    }

    public function view($id)
    {
        echo('You\'re looking at Project-' . strval($id));
    }
}