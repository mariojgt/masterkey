<?php

namespace Mariojgt\MasterKey\Dto;

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;

class AfterVerifyContext
{
    public function __construct(
        public Request $request,
        public object $user,
        public string $token,
        public Response|Responsable $response
    ) {
    }
}
