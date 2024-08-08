<?php
if (!function_exists('getServerEnv')) {
    function getServerEnv($key, $default = null)
    {
        return array_key_exists($key, $_SERVER) ? $_SERVER[$key] : env($key, $default);
    }
}
