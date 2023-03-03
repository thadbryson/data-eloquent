<?php

declare(strict_types = 1);

namespace Tests\Unit\Repositories;

use Thad\Data\Services\Repository;
use Tests\Support\Stubs\UserModel;
use UnitTester;

class RepositoryTest extends \Codeception\Test\Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    /**
     * @var Repository
     */
    protected $repository;

    public function _before(): void
    {
        $this->tester->bootEloquent();

        $this->repository = new Repository(new UserModel);
    }

    public function testAll(): void
    {
        $this->assertEquals(UnitTester::USERS_TABLE, $this->repository
            ->all()
            ->get()
            ->toArray());

        $this->assertEquals([
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
            ['id' => 4],
            ['id' => 5],
            ['id' => 6],
            ['id' => 7],
            ['id' => 8],
        ], $this->repository
            ->all('id')
            ->get()
            ->toArray());
    }
}
