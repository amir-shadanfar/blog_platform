<?php


if (! function_exists('dd')) {
    function dd($variable)
    {
        var_dump('<pre>', $variable);
        die();
    }
}

if (! function_exists('dump')) {
    function dump($variable)
    {
        var_dump('<pre>', $variable);
    }
}