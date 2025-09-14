<?php

namespace Mariojgt\MasterKey\Dto;

use Illuminate\Http\Request;

class BeforeVerifyContext
{
    public function __construct(
        public Request $request,
        public string $nonce,
        public string $code
    ) {
    }
}
