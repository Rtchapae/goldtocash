<?php

namespace App\Domain\Users\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use App\Domain\Orders\Models\Order;
use App\Domain\Users\Models\Trace;
use App\Domain\Users\Models\Role;
use App\Domain\Users\Models\Branch;
use App\Domain\Users\Enums\UserRole;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        // legacy fields from gold2cash schema
        'last_name',
        'first_name',
        'zip',
        'state',
        'address',
        'city',
        'phone',
        'country',
        'role_id',
        'branch_id',
        'payment_method',
        'payment_method_params',
        'government_id',
        'government_id_params',
        'date_of_birth',
        'verify',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function traces(): HasMany
    {
        return $this->hasMany(Trace::class);
    }

    public function roleRelation(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function isAdmin(): bool
    {
        if (!$this->role_id) {
            return false;
        }

        if (!$this->relationLoaded('roleRelation')) {
            $this->load('roleRelation');
        }

        $role = $this->roleRelation;
        return $role && $role->name === UserRole::ADMIN->value;
    }

    public function isManager(): bool
    {
        if (!$this->role_id) {
            return false;
        }

        if (!$this->relationLoaded('roleRelation')) {
            $this->load('roleRelation');
        }

        $role = $this->roleRelation;
        return $role && $role->name === UserRole::MANAGER->value;
    }

    public function isUser(): bool
    {
        if (!$this->role_id) {
            return false;
        }

        if (!$this->relationLoaded('roleRelation')) {
            $this->load('roleRelation');
        }

        $role = $this->roleRelation;
        return $role && $role->name === UserRole::USER->value;
    }

    public function getSourceBadgesAttribute(): array
    {
        if (! Schema::hasTable('traces')) {
            return [];
        }

        try {
            $trace = $this->relationLoaded('traces') && $this->traces->isNotEmpty()
                ? $this->traces->first()
                : $this->traces()->orderByDesc('created_at')->first();

            if ($trace === null || $trace->source === null) {
                return [];
            }

            $source = is_array($trace->source) ? $trace->source : (array) $trace->source;

            $keys = ['utm_term', 'utm_medium', 'utm_source', 'utm_campaign'];

            $badges = [];

            foreach ($keys as $key) {
                $value = $source[$key] ?? null;

                if ($value === null || $value === '') {
                    continue;
                }

                $badges[] = [
                    'key' => $key,
                    'value' => $value,
                ];
            }

            return $badges;
        } catch (QueryException $e) {
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getOrdersSummaryAttribute(): string
    {
        $orders = $this->orders()->with('statusRelation')->get();

        if ($orders->isEmpty()) {
            return '0 Active / 0 Closed';
        }

        $activeCount = $orders
            ->filter(static fn (Order $order): bool =>
                $order->statusRelation !== null && $order->statusRelation->name === 'Active'
            )
            ->count();

        $closedCount = $orders
            ->filter(static fn (Order $order): bool =>
                $order->statusRelation !== null && $order->statusRelation->name === 'Closed'
            )
            ->count();

        return sprintf('%d Active / %d Closed', $activeCount, $closedCount);
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        if (!$this->relationLoaded('roleRelation')) {
            $this->load('roleRelation');
        }

        return [
            'role' => $this->roleRelation?->name ?? 'user',
            'branch_id' => $this->branch_id,
        ];
    }
}


