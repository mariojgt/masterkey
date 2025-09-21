<?php

namespace Mariojgt\MasterKey\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MasterKeySession extends Model
{
    protected $table = 'masterkey_sessions';
    protected $fillable = ['session_id', 'status', 'tokenable_type', 'tokenable_id'];

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
}
