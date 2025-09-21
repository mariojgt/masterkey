<?php

namespace Mariojgt\MasterKey\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MasterKeyToken extends Model
{
    protected $table = 'masterkey_tokens';
    protected $fillable = ['tokenable_type', 'tokenable_id', 'token', 'name', 'last_used_at', 'expires_at'];

    /**
     * Get the tokenable model (User, Admin, etc.)
     */
    public function tokenable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Legacy method for backward compatibility
     * @deprecated Use tokenable() instead
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'tokenable_id')
            ->where('tokenable_type', \App\Models\User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at ? now()->greaterThan($this->expires_at) : false;
    }
}
