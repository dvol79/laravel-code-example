<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Symfony\Component\Serializer\Serializer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use App\Services\Log\Model\ActivityLog;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Dto\UserConfig;

/**
 * User Model
 *
 * @property int $id
 * @property string $name
 * @property string|null $lastname
 * @property string|null $bdate
 * @property int $sex
 * @property string|null $phone
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property string $role
 * @property int $status
 * @property UserConfig $config
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 *
 * @property-read ActivityLog[] $activities
 *
 * @package App\Models
 */
class User extends AuthUser implements MustVerifyEmail
{
    use HasApiTokens,
        HasFactory,
        Notifiable,
        SoftDeletes;

    private Serializer $serialiser;

    public const SEX_MAN     = 1;
    public const SEX_WOMAN   = 2;
    public const SEX_UNKNOWN = 3;

    public const ROLE_USER  = 'user';
    public const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'lastname',
        'bdate',
        'sex',
        'phone',
        'email',
        'password',
        'role',
        'config',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'bdate'             => 'date:Y-m-d',
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'sex'               => 'integer',
        'status'            => 'integer'
    ];

    /**
     * Default attributes values.
     *
     * @var array
     */
    protected $attributes = [
        'sex'    => self::SEX_UNKNOWN,
        'role'   => self::ROLE_USER,
        'status' => self::STATUS_ACTIVE
    ];

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->serialiser = app('Serializer');
    }

    /**
     * Interact with the config.
     *
     * @return Attribute
     */
    protected function config(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): UserConfig => $this->serialiser->deserialize(
                $value ?? '{}',
                UserConfig::class,
                'json'
            ),
            set: fn (UserConfig $value): string => json_encode($value->toArray())
        );
    }

    /**
     * Return sex name
     *
     * @return string
     */
    public function getSexName(): string
    {
        $names = [
            self::SEX_UNKNOWN => '-',
            self::SEX_MAN     => 'М',
            self::SEX_WOMAN   => 'Ж',
        ];

        return $names[$this->sex];
    }

    /**
     * Return activities relations
     *
     * @return HasMany
     */
    public function activities(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * A query range that includes only a specific type of user.
     *
     * @param  Builder  $query
     * @param  string $role
     * @return Builder
     */
    public function scopeByRole($query, string $role): Builder
    {
        return $query->where('role', $role);
    }
}
