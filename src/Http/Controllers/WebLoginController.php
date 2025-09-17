<?php

namespace Mariojgt\MasterKey\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Mariojgt\MasterKey\Models\MasterKeySession;
use Mariojgt\MasterKey\Support\MasterKeyHook;
use Mariojgt\MasterKey\Enums\MasterKeyHookType;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Illuminate\Contracts\Support\Responsable;

class WebLoginController extends Controller
{
    public function status(Request $request)
    {
        $sessionId = $request->query('session_id');
        $rec = MasterKeySession::where('session_id', $sessionId)->first();
        if (!$rec) {
            return response()->json(['status' => 'missing']);
        }

        if ($rec->status === 'approved' && $rec->user_id) {
            // Optional hook before logging in; can short-circuit
            $pre = MasterKeyHook::trigger(MasterKeyHookType::BEFORE_WEB_LOGIN, [
                'request' => $request,
                'session' => $rec,
            ]);
            if ($pre instanceof SymfonyResponse || $pre instanceof Responsable) {
                return $pre;
            }

            Auth::loginUsingId($rec->user_id);
            $rec->status = 'used';
            $rec->save();

            $redirect = config('masterkey.post_login_redirect');

            // Optional post-login hook; can override redirect or return a Response
            $post = MasterKeyHook::trigger(MasterKeyHookType::AFTER_WEB_LOGIN, [
                'request' => $request,
                'session' => $rec,
                'user_id' => $rec->user_id,
                'default_redirect' => $redirect,
            ]);
            if ($post instanceof SymfonyResponse || $post instanceof Responsable) {
                return $post;
            }
            if (is_string($post)) {
                $redirect = $post;
            } elseif (is_array($post) && isset($post['redirect'])) {
                $redirect = (string)$post['redirect'];
            }

            return response()->json(['status' => 'used', 'redirect' => $redirect]);
        }
        return response()->json(['status' => $rec->status]);
    }

    public function approve(Request $request)
    {
        $data = $request->validate(['session_id' => 'required|string']);
        $pre = MasterKeyHook::trigger(MasterKeyHookType::BEFORE_APPROVE, [
            'request' => $request,
            'session_id' => $data['session_id'],
        ]);
        if ($pre instanceof SymfonyResponse || $pre instanceof Responsable) {
            return $pre;
        }
        $rec = MasterKeySession::where('session_id', $data['session_id'])->first();

        if (!$rec || $rec->status !== 'pending') {
            return response()->json(['message' => 'Invalid session'], 422);
        }

        $rec->status = 'approved';
        $rec->user_id = auth()->id();
        $rec->save();

        $response = response()->json(['ok' => true]);
        $post = MasterKeyHook::trigger(MasterKeyHookType::AFTER_APPROVE, [
            'request' => $request,
            'session' => $rec,
            'response' => $response,
        ]);
        if ($post instanceof SymfonyResponse || $post instanceof Responsable) {
            return $post;
        }
        return $response;
    }
}
