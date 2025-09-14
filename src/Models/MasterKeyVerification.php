<?php

namespace Mariojgt\MasterKey\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKeyVerification extends Model
{
    protected $table = 'masterkey_verifications';
    protected $fillable = ['email','nonce','code','used'];
}
