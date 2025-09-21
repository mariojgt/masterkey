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
    public function getUserAttribute()
    {
        if ($this->tokenable_type === \App\Models\User::class) {
            return $this->tokenable;
        }
        return null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at ? now()->greaterThan($this->expires_at) : false;
    }
}
