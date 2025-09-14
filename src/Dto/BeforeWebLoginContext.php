<?php

namespace Mariojgt\MasterKey\Dto;

use Illuminate\Http\Request;
use Mariojgt\MasterKey\Models\MasterKeySession;

class BeforeWebLoginContext
{
    public function __construct(
        public Request $request,
        public MasterKeySession $session
    ) {
    }
}
