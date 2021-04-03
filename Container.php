<?php

namespace LaravelDatabaseStandalone;

class Container extends \Illuminate\Container\Container
{

    public function databasePath($path = '')
    {
        return 'database'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get or check the current application environment.
     *
     * @param  string $env
     * @return string|bool
     */
    public function environment()
    {
        return 'prod';
    }
}
