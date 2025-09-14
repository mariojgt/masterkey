<?php

namespace Mariojgt\MasterKey\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterKeyToken extends Model
{
    protected $table = 'masterkey_tokens';
    protected $fillable = ['user_id','token','name','last_used_at','expires_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at ? now()->greaterThan($this->expires_at) : false;
    }
}
