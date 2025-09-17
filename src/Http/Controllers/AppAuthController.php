<?php

namespace Mariojgt\MasterKey\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail as MailFacade;
use Mariojgt\MasterKey\Mail\VerificationCodeMail;
use Mariojgt\MasterKey\Models\MasterKeyVerification;
use Mariojgt\MasterKey\Models\MasterKeyToken;
use Mariojgt\MasterKey\Support\StrUtil;
use Mariojgt\MasterKey\Support\MasterKeyHook;
use Mariojgt\MasterKey\Enums\MasterKeyHookType;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Illuminate\Contracts\Support\Responsable;

class AppAuthController extends Controller
{
    public function requestCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = $request->input('email');

        // Hook: allow custom pre-processing/validation. Can return Response to short-circuit.
        $hookResult = MasterKeyHook::trigger(MasterKeyHookType::BEFORE_REQUEST_CODE, [
            'request' => $request,
            'email' => $email,
        ]);
        if ($hookResult instanceof SymfonyResponse || $hookResult instanceof Responsable) {
            return $hookResult;
        }

        $nonce = StrUtil::random(40);
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        MasterKeyVerification::create([
            'email' => $email,
            'nonce' => $nonce,
            'code' => $code,
        ]);

        // Optional post-create hook
        MasterKeyHook::trigger(MasterKeyHookType::AFTER_REQUEST_CODE, [
            'request' => $request,
            'email' => $email,
        ]);

        // Send the verification code via email (Mailtrap configured in .env)
        try {
            MailFacade::to($email)->send(new VerificationCodeMail($code, config('app.name', 'MasterKey')));
        } catch (\Throwable $e) {
            // Log but don't leak details to client
            logger()->error('Failed to send verification email', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'nonce' => $nonce,
            'message' => 'Verification code sent to your email if it exists.'
        ])->header('Content-Type', 'application/json');
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'nonce' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        // Hook before verify lookup
        $pre = MasterKeyHook::trigger(MasterKeyHookType::BEFORE_VERIFY, [
            'request' => $request,
            'nonce' => $data['nonce'],
            'code' => $data['code'],
        ]);
        if ($pre instanceof SymfonyResponse || $pre instanceof Responsable) {
            return $pre;
        }

        $rec = MasterKeyVerification::where('nonce', $data['nonce'])->first();
        if (!$rec || $rec->used || $rec->code !== $data['code']) {
            return response()->json(['message' => 'Invalid code'], 422);
        }

        // Hook after verify success but before user creation - allow user creation override
        $userCreationResult = MasterKeyHook::trigger(MasterKeyHookType::AFTER_VERIFY, [
            'request' => $request,
            'email' => $rec->email,
            'nonce' => $data['nonce'],
            'verification' => $rec,
        ]);

        // If hook returns a user, use it; otherwise create a default one
        if ($userCreationResult && is_object($userCreationResult) && isset($userCreationResult->id)) {
            $user = $userCreationResult;
        } else {
            // Safety check: prevent auto user creation in production unless explicitly enabled
            if (app()->environment('production') && !config('masterkey.allow_auto_user_creation', false)) {
                return response()->json([
                    'message' => 'User creation not configured. ' .
                                'Please implement user creation in your MasterKeyHandler.'
                ], 422);
            }
            
            // Fallback user creation - you should implement this in your MasterKeyHandler
            $userModel = app(\App\Models\User::class);
            $user = $userModel::firstOrCreate(['email' => $rec->email], [
                'name' => explode('@', $rec->email)[0],
                'password' => bcrypt(StrUtil::random(16)),
            ]);
        }

        $token = StrUtil::random(60);
        $expiresDays = config('masterkey.token_expires_days');
        $expiresAt = is_null($expiresDays) ? null : now()->addDays((int)$expiresDays);

        MasterKeyToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'name' => 'masterkey-app',
            'expires_at' => $expiresAt,
        ]);

        $rec->used = true;
        $rec->save();

        $response = response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
            ],
        ])->header('Content-Type', 'application/json');

        // Hook after verify success; allow response override
        $post = MasterKeyHook::trigger(MasterKeyHookType::AFTER_VERIFY, [
            'request' => $request,
            'user' => $user,
            'token' => $token,
            'response' => $response,
        ]);
        if ($post instanceof SymfonyResponse || $post instanceof Responsable) {
            return $post;
        }

        return $response;
    }
}
