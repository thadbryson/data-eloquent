<?php

declare(strict_types = 1);

namespace Tests\Support\Stubs;

/**
 * Class DecoratorStub
 *
 * @property-read string $id
 * @property-read bool   $name
 * @property-read int    $age
 */
class DecoratorStub extends \Org\Tool\Decorator
{
    protected function getId(): string
    {
        return 'ID';
    }

    protected function getName(): bool
    {
        return true;
    }

    protected function getAge(int $value): int
    {
        return $value * 10;
    }
}
