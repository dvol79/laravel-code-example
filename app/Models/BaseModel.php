<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Base Model Class
 *
 * @package App\Models
 */
class BaseModel extends Model
{
    /**
     * Deleted row status
     */
    const STATUS_DELETED = 0;
    /**
     * Active row status
     */
    const STATUS_ACTIVE = 1;
    /**
     * Waiting status
     */
    const STATUS_WAIT = 2;

    /**
     * Return status name
     *
     * @return string
     */
    public function getStatusName(): ?string
    {
        return data_get(static::getStatusesArray(), $this->status);
    }

    /**
     * Only active visits query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @example $active = Visit::active()->orderBy('created_at')->get();
     * @return void
     */
    public function scopeActive($query)
    {
        $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Return an array of status names
     *
     * @return array
     */
    public static function getStatusesArray(): array
    {
        return [
            self::STATUS_DELETED => 'deleted',
            self::STATUS_ACTIVE  => 'active',
            self::STATUS_WAIT    => 'wait',
        ];
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return with(new static())->getTable();
    }
}
