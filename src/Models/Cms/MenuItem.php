<?php

declare(strict_types = 1);

namespace Data\Models\Cms;

use Data\BaseModel;
use Data\Models\Traits;
use HighSolutions\EloquentSequence\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Nav
 *
 * @property int            $menu_id
 * @property string         $name
 * @property int            $position
 * @property string         $link
 * @property bool           $is_external
 *
 * @property-read Menu|null $menu
 */
abstract class MenuItem extends BaseModel
{
    use Traits\HasCodeTrait,
        Traits\Singleton,
        Sequence;

    protected $table = 'cms_nav_items';

    protected $casts = [
        'menu_id'     => 'integer',
        'name'        => 'string',
        'is_external' => 'boolean',
        'position'    => 'integer',
        'link'        => 'string'
    ];

    protected $attributes = [
        'link'        => '',
        'is_external' => true
    ];

    abstract public function menu(): BelongsTo;

    public function sequence(): array
    {
        return [
            'group'      => 'menu_id',
            'fieldName'  => 'position',
            'orderFrom1' => true
        ];
    }
}
