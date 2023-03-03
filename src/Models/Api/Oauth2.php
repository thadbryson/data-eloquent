<?php

declare(strict_types = 1);

namespace Thad\Data\Models\Api;

use Carbon\Carbon;
use Thad\Data\BaseModel;
use Thad\Data\Models\Traits\Singleton;
use Org\Arr\Arr;

/**
 * Class Oauth2
 *
 * @property string    $code
 * @property string    $token
 * @property string    $token_refresh
 * @property int       $expires_in
 * @property Carbon    $expires_at
 * @property-read bool $is_expired
 */
abstract class Oauth2 extends BaseModel
{
    use Singleton;

    protected $table = 'oauth2';

    protected $casts = [
        'code'          => 'string',
        'token'         => 'string',
        'token_refresh' => 'string',
        'expires_in'    => 'integer',
        'expires_at'    => 'datetime'
    ];

    protected $dates = ['expires_at'];

    protected $attributes = [
        'token_refresh' => '',
    ];

    protected $meta = [];

    /**
     * Set Api Oauth2 token on latest row.
     *
     * @param $code
     * @param $token
     * @param $tokenRefresh
     * @param $expiresInSeconds
     * @return Oauth2
     */
    abstract public static function formSet($code, $token, $tokenRefresh, $expiresInSeconds);

    /**
     * @param array $meta
     * @return $this
     */
    public function setMeta(array $meta)
    {
        $this->meta = $meta;

        return $this;
    }

    public function meta(string $dot, $default = null)
    {
        return Arr::get($this->meta, $dot, $default);
    }

    protected function setExpiresInAttribute($seconds): void
    {
        $this->attributes['expires_in'] = (int) $seconds;

        $this->attributes['expires_at'] = Carbon::now()->addSeconds((int) $seconds);
    }

    protected function getIsExpiredAttribute(): bool
    {
        return $this->expires_at instanceof Carbon && $this->expires_at->isPast();
    }
}
