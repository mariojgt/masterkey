<?php

namespace Mariojgt\MasterKey\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $code;
    public string $appName;

    public function __construct(string $code, string $appName)
    {
        $this->code = $code;
        $this->appName = $appName;
    }

    public function build()
    {
        return $this
            ->subject('Your ' . $this->appName . ' verification code')
            ->view('masterkey::emails.verification_code')
            ->with([
                'code' => $this->code,
                'appName' => $this->appName,
            ]);
    }
}
