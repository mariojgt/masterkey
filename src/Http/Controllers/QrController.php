<?php

namespace Mariojgt\MasterKey\Http\Controllers;

use BaconQrCode\Writer;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use BaconQrCode\Renderer\ImageRenderer;
use Mariojgt\MasterKey\Support\StrUtil;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use Mariojgt\MasterKey\Models\MasterKeySession;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

class QrController extends Controller
{
    public function loginQrPage()
    {
        $sessionId = StrUtil::random(32);
        MasterKeySession::create([ 'session_id' => $sessionId ]);

        $payload = config('masterkey.qr_prefix').'web:'.$sessionId;
        $statusUrl = URL::route('masterkey.web-login-status', ['session_id' => $sessionId]);

        // Generate QR as inline SVG using BaconQrCode
        $renderer = new ImageRenderer(new RendererStyle(256), new SvgImageBackEnd());
        $writer = new Writer($renderer);
        $svg = $writer->writeString($payload);

        return View::make('masterkey::login-qr', [
            'payload' => $payload,
            'statusUrl' => $statusUrl,
            'qrSvg' => $svg,
        ]);
    }
}
