<?php

namespace App\Services\Log;

use App\Services\Log\Model\ActivityLog;
use Illuminate\Database\Eloquent\Model;

/**
 * User Activity Logger
 *
 * @package App\Services
 *
 * @example for update non-trate usage:
 * ```
 * $user->fill($request->all());
 * ActivityLogger::addModelUpdate($user);
 * $user->save();
 * ```
 */
final class ActivityLogger
{
    /**
     * Add new log activity
     *
     * @param string $event
     * @param array $data
     * @return void
     */
    public static function addEvent(string $event, array $data): void
    {
        self::createLog($event, $data);
    }

    /**
     * Add new model create log
     *
     * @param Model $model
     * @return void
     */
    public static function addModelCreate(Model $model): void
    {
        self::createLog(
            ActivityLog::EVENT_CREATE,
            $model->attributesToArray(),
            $model->getTable(),
            (string)$model->getKey(),
        );
    }

    /**
     * Add new model update log
     *
     * @param Model $model
     * @return void
     */
    public static function addModelUpdate(Model $model): void
    {
        $data = [];
        $modifiedKeys = ['created_at', 'updated_at', 'deleted_at'];

        foreach($model->getDirty() as $key => $newValue){
            if (in_array($key, $modifiedKeys)) {
                continue;
            }

            $oldValue = $model->getOriginal($key);
            $data[$key] = [
                'old' => $oldValue,
                'new' => $newValue,
            ];
        }

        self::createLog(
            ActivityLog::EVENT_UPDATE,
            $data,
            $model->getTable(),
            (string)$model->getKey(),
        );
    }

    /**
     * Add new model delete log
     *
     * @param Model $model
     * @return void
     */
    public static function addModelDelete(Model $model): void
    {
        self::createLog(
            ActivityLog::EVENT_DELETE,
            $model->attributesToArray(),
            $model->getTable(),
            (string)$model->getKey(),
        );
    }

    /**
     * Add new model delete log
     *
     * @param Model $model
     * @return void
     */
    public static function addModelRestore(Model $model): void
    {
        self::createLog(
            ActivityLog::EVENT_RESTORE,
            $model->attributesToArray(),
            $model->getTable(),
            (string)$model->getKey(),
        );
    }

    /**
     * Add new log record
     *
     * @param string $event
     * @param array $data
     * @param string|null $table
     * @param string|null $key
     * @return void
     */
    private static function createLog(
        string $event,
        array  $data = [],
        ?string $table = null,
        ?string $key = null
    ): void {
        ActivityLog::create([
            'user_id'  => self::getUserId(),
            'event'    => $event,
            'table'    => $table,
            'key'      => $key,
            'data'     => $data,
        ]);
    }

    /**
     * Return current user ID
     *
     * @return int
     */
    private static function getUserId(): ?int
    {
        return auth()?->user()?->id;
    }
}
