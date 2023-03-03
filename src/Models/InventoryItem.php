<?php

declare(strict_types = 1);

namespace Thad\Data\Models;

use Thad\Data\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Item
 *
 * @property string              $name
 * @property string              $description
 * @property-read string         $qty
 * @property float               $amount
 * @property string              $amount_units
 * @property bool                $is_expired
 * @property \Carbon\Carbon|null $expires_at
 */
abstract class InventoryItem extends BaseModel
{
    use SoftDeletes;

    protected $table = 'inventory_items';

    protected $attributes = [
        'description'  => '',
        'amount'       => 0,
        'amount_units' => '',
        'expires_at'   => null,
    ];

    protected $appends = [
        'is_expired',
        'qty'
    ];

    protected $dates = [
        'expires_at'
    ];

    protected function setDescriptionAttribute($value): string
    {
        return $value ?? '';
    }

    protected function getIsExpiredAttribute(): bool
    {
        if ($this->expires_at === null) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    protected function getQtyAttribute(): string
    {
        return trim($this->amount . ' ' . $this->amount_units);
    }
}
