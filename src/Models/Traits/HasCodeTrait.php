<?php

declare(strict_types = 1);

namespace Data\Models\Traits;

use Data\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use function array_walk;
use function explode;

/**
 * Trait HasCodeTrait
 *
 * @mixin BaseModel
 * @property int|string $code
 */
trait HasCodeTrait
{
    public static function findOrInit($code): self
    {
        $found = static::query()
                ->where('code', $code)
                ->first() ?? static::make(['code' => $code]);

        return $found;
    }

    public static function findWithCodes(string $codes): Builder
    {
        $codes = explode(',', $codes);

        array_walk($codes, 'trim');

        return static::query()->whereIn('code', $codes);
    }

    public function isCodeSet(): bool
    {
        return empty($this->code) === false;
    }
}
