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
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'tokenable_id')
            ->where('tokenable_type', \App\Models\User::class);
    }
}
