<?php

namespace Mariojgt\MasterKey\Dto;

use Illuminate\Http\Request;

class AfterRequestCodeContext
{
    public function __construct(
        public Request $request,
        public string $email
    ) {
    }
}
