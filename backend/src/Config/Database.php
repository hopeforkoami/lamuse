<?php

namespace App\Config;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class Database
{
    public static function init()
    {
        $capsule = new Capsule;

        $capsule->addConnection([
            'driver'    => $_ENV['DB_DRIVER'] ?? 'mysql',
            'host'      => $_ENV['DB_HOST'] ?? 'mysql',
            'database'  => $_ENV['DB_DATABASE'] ?? 'musician_storefront',
            'username'  => $_ENV['DB_USERNAME'] ?? 'user',
            'password'  => $_ENV['DB_PASSWORD'] ?? 'password',
            'charset'   => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
            'collation' => $_ENV['DB_COLLATION'] ?? 'utf8mb4_unicode_ci',
            'prefix'    => '',
        ]);

        // Set the event dispatcher used by Eloquent models... (optional)
        $capsule->setEventDispatcher(new Dispatcher(new Container));


        // Make this Capsule instance available globally via static methods... (optional)
        $capsule->setAsGlobal();

        // Setup the Eloquent ORM... (optional; Cookies, etc. will not be set)
        $capsule->bootEloquent();

        return $capsule;
    }
}
