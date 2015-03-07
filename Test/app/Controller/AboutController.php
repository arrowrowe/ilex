<?php


/**
 * Class AboutController
 *
 * @property \Ilex\Base\Model\sys\Input $Input
 */
class AboutController extends \Ilex\Base\Controller\Base
{
    protected $Input = NULL;

    public function index()
    {
        echo('about');
    }

    public function join($group = 'tech')
    {
        echo('Join ' . $group . '!');
    }

    public function postJoin($group = 'tech')
    {
        $this->load_model('sys/Input');
        echo('Welcome to ' . $group . ', ' . $this->Input->post('name', 'Jack') . '!');
    }
}