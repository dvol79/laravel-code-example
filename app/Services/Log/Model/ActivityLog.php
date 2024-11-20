<?php

namespace App\Services\Log\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Services\Visit\Model\Visit;
use App\Traits\HasFilter;
use App\Models\User;
use stdClass;

/**
 * This is the model class for table "activity_logs".
 *
 * @method static self byUser(int $userId)
 * @method static self byEvent(string $event)
 *
 * @property int $id
 * @property int $user_id
 * @property int $visit_id
 * @property string $event
 * @property string|null $table
 * @property string|null $key
 * @property array|null $data
 * @property int $created_at
 *
 * @property-read User $user
 * @property-read Visit $visit
 * @property Model $tableModel
 *
 * @package App\Models
 */
class ActivityLog extends Model
{
    use HasFilter;

    public const EVENT_CREATE  = 'create';
    public const EVENT_UPDATE  = 'update';
    public const EVENT_DELETE  = 'delete';
    public const EVENT_RESTORE = 'restore';

    public const EXCUDED_TABLES = [
        'personal_access_tokens',
        'password_reset_tokens',
        'password_resets',
        'activity_logs',
        'failed_jobs',
        'migrations',
        'sessions'
    ];

    public $timestamps = false;

    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $keyType = 'integer';

    protected $fillable = [
        'user_id',
        'visit_id',
        'event',
        'table',
        'key',
        'data',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'visit_id' => 'integer',
        'event' => 'string',
        'table' => 'string',
        'key' => 'string',
        'data' => 'array',
    ];

    /**
     * Return user relation
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return visit relation
     *
     * @return BelongsTo
     */
    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    /**
     * Get Table Model
     *
     * @return null|Model
     */
    public function tableModel(): ?Model
    {
        if ($this->table === null || $this->key === null) {
            return null;
        }

        return DB::table($this->table)->find($this->key);
    }

    /**
     * A query range that includes only user's data.
     *
     * @param  Builder  $query
     * @param  int $userId
     * @example $log = ActivityLog::byUser(2)->orderBy('created_at')->get();
     * @return Builder
     */
    public function scopeByUser($query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * A query range that includes only user's data.
     *
     * @param  Builder  $query
     * @param  string $event
     * @example $log = ActivityLog::byEvent(2)->orderBy('created_at')->get();
     * @return Builder
     */
    public function scopeByEvent($query, string $event): Builder
    {
        return $query->where('event', $event);
    }

    /**
     * Return accepted for logging tables array
     *
     * @return array<string, string>
     */
    public static function getTableNames(): array
    {
        $tables = DB::select('SHOW TABLES');

        $tableNames = array_map(function (stdClass $item) {
            return array_values((array)$item)[0];
        }, $tables);

        $tableNames = array_filter(
            $tableNames,
            fn($table) => !in_array($table, self::EXCUDED_TABLES)
        );

        return array_combine($tableNames, $tableNames);
    }
}
