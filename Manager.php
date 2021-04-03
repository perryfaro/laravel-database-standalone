<?php

namespace LaravelDatabaseStandalone;

use Illuminate\Container\Container;

class Manager extends \Illuminate\Database\Capsule\Manager
{

    /**
     * Manager constructor.
     * @param Container|null $container
     */
    public function __construct(Container $container = null)
    {
        parent::__construct($container);
        static::$instance = $this;
    }

    /**
     * @return \Illuminate\Database\Capsule\Manager
     */
    public static function getInstance(): \Illuminate\Database\Capsule\Manager
    {
        return self::$instance;
    }
}