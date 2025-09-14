<?php

namespace Mariojgt\MasterKey\Dto;

use Illuminate\Http\Request;

class BeforeApproveContext
{
    public function __construct(
        public Request $request,
        public string $session_id
    ) {
    }
}
