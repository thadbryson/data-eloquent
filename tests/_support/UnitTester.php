<?php

declare(strict_types = 1);

use _generated\UnitTesterActions;
use Data\Boot;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class UnitTester extends \Codeception\Actor
{
    use UnitTesterActions;

    public const USERS_TABLE = [
        ['id' => 1, 'name' => 'Jim', 'age' => 22],
        ['id' => 2, 'name' => 'Timmy', 'age' => 10],
        ['id' => 3, 'name' => 'Jen', 'age' => 73],
        ['id' => 4, 'name' => 'Chad', 'age' => 40],
        ['id' => 5, 'name' => 'Zeke', 'age' => 34],
        ['id' => 6, 'name' => 'Bob', 'age' => 21],
        ['id' => 7, 'name' => 'Joe', 'age' => 64],
        ['id' => 8, 'name' => 'Judy', 'age' => 12],
    ];

    public function bootEloquent(): void
    {
        Boot::eloquent([
            'driver'   => 'mysql',
            'host'     => 'localhost',
            'database' => 'data_test',
            'username' => 'root',
            'password' => 'root',
        ]);
    }
}
