<?php

namespace Mariojgt\MasterKey\Dto;

use Illuminate\Http\Request;
use Mariojgt\MasterKey\Models\MasterKeyVerification;

class CreateUserContext
{
    public function __construct(
        public Request $request,
        public string $email,
        public string $nonce,
        public MasterKeyVerification $verification
    ) {
    }
}
