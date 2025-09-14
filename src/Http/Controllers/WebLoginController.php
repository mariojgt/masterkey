<?php

namespace Mariojgt\MasterKey\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Mariojgt\MasterKey\Models\MasterKeySession;

class WebLoginController extends Controller
{
    public function status(Request $request)
    {
        $sessionId = $request->query('session_id');
        $rec = MasterKeySession::where('session_id', $sessionId)->first();
        if (!$rec) return response()->json(['status' => 'missing']);

        if ($rec->status === 'approved' && $rec->user_id) {
            Auth::loginUsingId($rec->user_id);
            $rec->status = 'used';
            $rec->save();
            return response()->json(['status' => 'used', 'redirect' => config('masterkey.post_login_redirect')]);
        }
        return response()->json(['status' => $rec->status]);
    }

    public function approve(Request $request)
    {
        $data = $request->validate(['session_id' => 'required|string']);
        $rec = MasterKeySession::where('session_id', $data['session_id'])->first();

        if (!$rec || $rec->status !== 'pending') {
            return response()->json(['message' => 'Invalid session'], 422);
        }

        $rec->status = 'approved';
        $rec->user_id = auth()->id();
        $rec->save();

        return response()->json(['ok' => true]);
    }
}
