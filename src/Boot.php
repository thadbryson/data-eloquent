<?php

declare(strict_types = 1);

namespace Data;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;
use Tool\Arr;
use Tool\Validation\Validator;

/**
 * Class Boot
 */
class Boot
{
    /**
     * @var Manager
     */
    protected static $db;

    /**
     * Add a database connection.1
     */
    public static function eloquent(array $connection, string $name = 'default'): void
    {
        $connection = Arr::defaults($connection, [
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        Validator::validate($connection, [
            'driver'    => 'required|string',
            'host'      => 'string',
            'database'  => 'required|string',
            'username'  => 'required|string',
            'password'  => 'required|string',
            'charset'   => 'required|string',
            'collation' => 'required|string',
            'prefix'    => 'string',
        ])->assert('Invalid $connection options.', 500);

        static::init()->addConnection($connection, $name);
    }

    protected static function init(): Manager
    {
        if (static::$db === null) {
            static::$db = new Manager;

            static::$db->setAsGlobal();
            static::$db->setEventDispatcher(new Dispatcher(new Container));
            static::$db->bootEloquent();
        }

        return static::$db;
    }
}
