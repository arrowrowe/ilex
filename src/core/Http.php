<?php

namespace Ilex\Core;


class Http
{
    public static function redirect($url)
    {
        if (ENVIRONMENT !== 'TEST') {
            header('Location: ' . $url);
        }
    }
}