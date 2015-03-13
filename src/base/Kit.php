<?php


namespace Ilex\Base;


class Kit
{
    public static function escape($input)
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}