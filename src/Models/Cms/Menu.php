<?php

declare(strict_types = 1);

namespace Data\Models\Cms;

use Data\BaseModel;
use Data\Models\Traits;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Class NavMenu
 *
 * @method static self create(array $attributes)
 *
 * @property Collection $items
 * @property string     $name
 * @property string     $code
 */
abstract class Menu extends BaseModel
{
    use Traits\HasCodeTrait;

    protected $table = 'cms_nav_menus';

    protected $with = ['items'];

    abstract public function items(): HasMany;
}
