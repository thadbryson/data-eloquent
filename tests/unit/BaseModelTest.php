<?php

declare(strict_types = 1);

namespace Tests\Unit;

use Thad\Data\Exceptions\ModelNotFound;
use Thad\Data\Services\Repository;
use InvalidArgumentException;
use Tests\Support\Stubs\DecoratorStub;
use Tests\Support\Stubs\RepositoryStub;
use Tests\Support\Stubs\UserModelCustom;
use Org\Tool\Decorator;
use UnitTester;

class BaseModelTest extends \Codeception\Test\Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function _before(): void
    {
        $this->tester->bootEloquent();
    }

    public function testMakeInstance(): void
    {
        $model = UserModelCustom::make()->makeInstance([
            'name' => 'Instance',
            'age'  => 100
        ]);

        $this->assertInstanceOf(UserModelCustom::class, $model);
        $this->assertEquals(100, $model->age);
        $this->assertEquals('Instance', $model->name);
    }

    public function testDecorator(): void
    {
        $model = new UserModelCustom;

        $model->id   = 1;
        $model->name = 'TEST';
        $model->age  = 20;

        $this->assertInstanceOf(Decorator::class, $model->decorator());

        $model->testDecorator(DecoratorStub::class);

        $this->assertInstanceOf(DecoratorStub::class, $model->decorator());

        /** @var DecoratorStub $decorator */
        $decorator = $model->decorator();

        $this->assertEquals('ID', $decorator->id);
        $this->assertTrue($decorator->name);
        $this->assertEquals(200, $decorator->age);
    }

    public function testRepository(): void
    {
        $model = new UserModelCustom;
        $this->assertInstanceOf(Repository::class, $model->repository());

        $model->testRepository(RepositoryStub::class);

        $this->assertInstanceOf(Repository::class, $model->repository());
    }

    public function testFind(): void
    {
        $this->assertEquals(UnitTester::USERS_TABLE[0], UserModelCustom::find(1)->toArray());
        $this->assertEquals(UnitTester::USERS_TABLE[1], UserModelCustom::find(2)->toArray());
        $this->assertEquals(UnitTester::USERS_TABLE[2], UserModelCustom::find(3)->toArray());
        $this->assertEquals(UnitTester::USERS_TABLE[3], UserModelCustom::find(4)->toArray());
        $this->assertEquals(UnitTester::USERS_TABLE[4], UserModelCustom::find(5)->toArray());
        $this->assertEquals(UnitTester::USERS_TABLE[5], UserModelCustom::find(6)->toArray());
        $this->assertEquals(UnitTester::USERS_TABLE[6], UserModelCustom::find(7)->toArray());
        $this->assertEquals(UnitTester::USERS_TABLE[7], UserModelCustom::find(8)->toArray());
    }

    public function testFindWithBlankIdString(): void
    {
        $this->expectExceptionObject(new InvalidArgumentException('$id cannot be a blank string.', 400));

        UserModelCustom::find('');
    }

    public function testFindValidIdParameter(): void
    {
        $this->expectExceptionObject(new InvalidArgumentException('$id has to be an integer or a string.', 400));

        UserModelCustom::find(0.1);
    }

    public function testFindNonDefaultColumns(): void
    {
        $found    = UserModelCustom::find(2, 'age');
        $expected = UnitTester::USERS_TABLE[1];

        unset($expected['name']);

        $this->assertEquals($expected, $found->toArray());
    }

    public function testFindOrNew(): void
    {
        $found = UserModelCustom::findOrNew(1, ['id' => false]);
        $this->assertEquals(UnitTester::USERS_TABLE[0], $found->toArray());

        $found = UserModelCustom::findOrNew(99, ['id' => false]);

        $this->assertFalse($found->exists);
        $this->assertEquals(['id' => false], $found->toArray());
    }

    public function testFindOrCreate(): void
    {
        $defaults = [
            'id'   => 101,
            'name' => 'NEW',
            'age'  => 0,
        ];

        $found = UserModelCustom::findOrCreate(13, $defaults);

        $this->assertTrue($found->exists);
        $this->assertEquals($defaults, $found->toArray());
    }

    public function testFindOrFail(): void
    {
        $found = UserModelCustom::findOrFail(1);
        $this->assertEquals(UnitTester::USERS_TABLE[0], $found->toArray());

        $this->expectExceptionObject(new ModelNotFound('Thad\Data could not be found.'));

        UserModelCustom::findOrFail(99);
    }

    public function testFindMany(): void
    {
        $found = UserModelCustom::findMany([0, 1, 3, 5, 7, 9, 11, 12, 13]);

        $this->assertEquals([
            0  => null,
            1  => UnitTester::USERS_TABLE[0],
            3  => UnitTester::USERS_TABLE[2],
            5  => UnitTester::USERS_TABLE[4],
            7  => UnitTester::USERS_TABLE[6],
            9  => null,
            11 => null,
            12 => null,
            13 => null,
        ], $found->toArray());

        $this->assertCount(9, $found, 'Should have 9 results, 5 should be NULL');
    }

    public function testFindManyOrNew(): void
    {
        $defaults = [
            'name' => 'NEW',
            'age'  => -1,
        ];

        $found = UserModelCustom::findManyOrNew([0, 1, 3, 5, 7, 9, 11, 12, 13], $defaults);

        $this->assertEquals([
            0  => $defaults,
            1  => UnitTester::USERS_TABLE[0],
            3  => UnitTester::USERS_TABLE[2],
            5  => UnitTester::USERS_TABLE[4],
            7  => UnitTester::USERS_TABLE[6],
            9  => $defaults,
            11 => $defaults,
            12 => $defaults,
            13 => $defaults,
        ], $found->toArray());

        $this->assertCount(9, $found, 'Should have 9 results, 5 should have the default attributes');
    }

    public function testFindManyOrFail(): void
    {
        $found = UserModelCustom::findManyOrFail([1, 3, 5, 7]);

        $this->assertEquals([
            1 => UnitTester::USERS_TABLE[0],
            3 => UnitTester::USERS_TABLE[2],
            5 => UnitTester::USERS_TABLE[4],
            7 => UnitTester::USERS_TABLE[6],
        ], $found->toArray());

        $this->assertCount(4, $found, 'All 4 results found. No Excpetion thrown.');

        $this->expectExceptionObject(new ModelNotFound('Thad\Data not found with ids: 9, 0, 11'));
        UserModelCustom::findManyOrFail([1, 3, 5, 7, 9, 0, 11]);
    }
}
