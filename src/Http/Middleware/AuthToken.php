<?php

namespace Mariojgt\MasterKey\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mariojgt\MasterKey\Models\MasterKeyToken;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AuthToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization');
        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $token = substr($header, 7);

        $record = MasterKeyToken::query()->where('token', $token)->first();
        if (!$record || $record->isExpired() || !$record->user) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        Auth::login($record->user);
        $record->last_used_at = now();
        $record->save();

        return $next($request);
    }
}
