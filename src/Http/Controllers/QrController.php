<?php

namespace Mariojgt\MasterKey\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Mariojgt\MasterKey\Models\MasterKeySession;
use Mariojgt\MasterKey\Support\StrUtil;

class QrController extends Controller
{
    public function loginQrPage()
    {
        $sessionId = StrUtil::random(32);
        MasterKeySession::create([ 'session_id' => $sessionId ]);

        $payload = config('masterkey.qr_prefix').'web:'.$sessionId;
        $statusUrl = URL::route('masterkey.web-login-status', ['session_id' => $sessionId]);

        return View::make('masterkey::login-qr', [
            'payload' => $payload,
            'statusUrl' => $statusUrl,
        ]);
    }
}
