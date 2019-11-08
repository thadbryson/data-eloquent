<?php

declare(strict_types = 1);

namespace Tests\Support\Stubs;

/**
 * Class UserModelCustom
 *
 * @property int    $id
 * @property string $name
 * @property int    $age
 */
class UserModelCustom extends UserModel
{
    public function testDecorator(string $class): self
    {
        $this->decoratorClass = $class;

        return $this;
    }

    public function testRepository(string $class): self
    {
        $this->repositoryClass = $class;

        return $this;
    }
}