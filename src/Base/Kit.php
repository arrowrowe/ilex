<?php


namespace Ilex\Base;


class Kit
{
    public static function escape($input)
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    public static function time($time = FALSE, $format = 'Y-m-d H:i:s')
    {
        if ($time === FALSE) {
            $time = time();
        }
        return date($format, $time);
    }
}