<?php

namespace App\Domain\Users\Models;

use App\Domain\Users\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'address',
        'phone',
        'email',
    ];

    public function managers(): HasMany
    {
        return $this->hasMany(User::class, 'branch_id')
            ->whereHas('roleRelation', function ($query) {
                $query->where('name', UserRole::MANAGER->value);
            });
    }
}
