<?php

namespace Mariojgt\MasterKey\Dto;

use Illuminate\Http\Request;
use Mariojgt\MasterKey\Models\MasterKeySession;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;

class AfterApproveContext
{
    public function __construct(
        public Request $request,
        public MasterKeySession $session,
        public Response|Responsable $response
    ) {
    }
}
