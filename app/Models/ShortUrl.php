<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ShortUrl extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'original_url',
        'code',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getShortPathAttribute(): string
    {
        return route('short-urls.redirect', $this->code);
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = Str::random(7);
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Visible to an Admin: every short url created within the admin's own company.
     */
    public static function visibleToAdmin(User $admin): Builder
    {
        return self::query()->where('company_id', $admin->company_id);
    }

    /**
     * Visible to a Member: every short url created by the member themselves.
     */
    public static function visibleToMember(User $member): Builder
    {
        return self::query()->where('user_id', $member->id);
    }

    /**
     * Visible to SuperAdmin: every short url across every company.
     */
    public static function visibleToSuperAdmin(): Builder
    {
        return self::query();
    }
}
