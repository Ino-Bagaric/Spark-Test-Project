<?php

class Session
{
    public static function put($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public static function remove($name)
    {
        if (self::exist($name)) {
            unset($_SESSION[$name]);
        }
    }

    public static function get($name)
    {
        return $_SESSION[$name];
    }

    public static function exist($name)
    {
        return isset($_SESSION[$name]);
    }
}
