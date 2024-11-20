<?php

namespace App\Traits;

use App\Services\Log\ActivityLogger;
use Illuminate\Database\Eloquent\Model;

/**
 * LoggableModel trait to log model event:
 * + create
 * + update
 * + delete
 * + restore
 *
 * @example
 * ```
 * class User extends Model
 * {
 *    use LoggableModel;
 *
 *    protected $exceptCreate  = true; // No logging create model
 *    protected $exceptUpdate  = true; // No logging update model
 *    protected $exceptDelete  = true; // No logging delete model
 *    protected $exceptRestore = true; // No logging restore model
 * }
 * ```
 */
trait LoggableModel
{
    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function (Model $model) {
            if (isset($model->exceptCreate) && $model->exceptCreate) return;

            ActivityLogger::addModelCreate($model);
        });

        static::updated(function (Model $model) {
            if (isset($model->exceptUpdate) && $model->exceptUpdate) return;

            ActivityLogger::addModelUpdate($model);
        });

        static::deleted(function (Model $model) {
            if (isset($model->exceptDelete) && $model->exceptDelete) return;

            ActivityLogger::addModelDelete($model);
        });

        if (
            in_array('Illuminate\Database\Eloquent\SoftDeletes', (class_uses(self::class)))
        ) {
            static::restored(function (Model $model) {
                if (isset($model->exceptRestore) && $model->exceptRestore) return;

                ActivityLogger::addModelRestore($model);
            });
        }
    }
}
