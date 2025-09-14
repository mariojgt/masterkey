<?php

namespace Mariojgt\MasterKey\Dto;

use Illuminate\Http\Request;
use Mariojgt\MasterKey\Models\MasterKeySession;

class AfterWebLoginContext
{
    public function __construct(
        public Request $request,
        public MasterKeySession $session,
        public int $user_id,
        public string $default_redirect
    ) {
    }
}
