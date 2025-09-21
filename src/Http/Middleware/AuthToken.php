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
        if (!$record || $record->isExpired() || !$record->tokenable) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        // Use the polymorphic relationship to get the authenticated model
        $authenticatedModel = $record->tokenable;

        // If it's a User model, use Auth::login, otherwise set in session/context
        if ($authenticatedModel instanceof \Illuminate\Foundation\Auth\User) {
            Auth::login($authenticatedModel);
        } else {
            // For non-User models, you might want to store in session or request
            $request->attributes->set('authenticated_model', $authenticatedModel);
            $request->attributes->set('authenticated_model_type', get_class($authenticatedModel));
        }

        $record->last_used_at = now();
        $record->save();

        return $next($request);
    }
}
