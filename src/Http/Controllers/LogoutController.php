<?php

namespace Mariojgt\MasterKey\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Mariojgt\MasterKey\Events\LogoutQrScanned;

class LogoutController extends Controller
{
    public function logoutQr(Request $request)
    {
        Event::dispatch(new LogoutQrScanned(auth()->user()));
        return response()->json(['ok' => true]);
    }
}
