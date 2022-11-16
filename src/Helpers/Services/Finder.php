<?php

declare(strict_types = 1);

namespace Data\Helpers\Services;

use RuntimeException;
use Org\Tool\Validation\Assert;
use function class_exists;
use function get_class;

class Finder
{
    protected $model;

    protected $id;

    protected $baseClass;

    protected $siblingClass;

    public function __construct(object $model, string $id)
    {
        $this->model = $model;

        $this->setId($id);
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        $this->baseClass    = '\\Data\\Services\\' . $this->id;
        $this->siblingClass = get_class($this->model) . '\\' . $this->id;

        return $this;
    }

    public function get(string $overrideClass = null): string
    {
        // Has a set configured class?
        if ($overrideClass !== null) {
            return Assert::classExists($overrideClass, sprintf('Your configured Service class %s does not exist for Service %s.',
                $overrideClass, $this->id));
        }
        elseif ($this->existsSiblingClass()) {
            return $this->getSiblingClass();
        }
        elseif ($this->existsBaseClass()) {
            return $this->getBaseClass();
        }

        throw new RuntimeException(sprintf('Service class for "%s" could not be found.', $this->id));
    }

    public function getBaseClass(): string
    {
        return $this->baseClass;
    }

    public function existsBaseClass(): bool
    {
        return class_exists($this->baseClass);
    }

    public function getSiblingClass(): string
    {
        return $this->siblingClass;
    }

    public function existsSiblingClass(): bool
    {
        return class_exists($this->siblingClass);
    }
}
