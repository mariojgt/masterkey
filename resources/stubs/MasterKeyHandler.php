<?php

namespace App\Helpers;

use Mariojgt\MasterKey\Contracts\MasterKeyHandler as MasterKeyHandlerContract;
use Mariojgt\MasterKey\Dto\AfterApproveContext;
use Mariojgt\MasterKey\Dto\AfterRequestCodeContext;
use Mariojgt\MasterKey\Dto\AfterVerifyContext;
use Mariojgt\MasterKey\Dto\AfterWebLoginContext;
use Mariojgt\MasterKey\Dto\BeforeApproveContext;
use Mariojgt\MasterKey\Dto\BeforeRequestCodeContext;
use Mariojgt\MasterKey\Dto\BeforeVerifyContext;
use Mariojgt\MasterKey\Dto\BeforeWebLoginContext;

class MasterKeyHandler implements MasterKeyHandlerContract
{
    // Register this class in config/masterkey.php as:
    // 'handler' => App\Helpers\MasterKeyHandler::class,

    public function handleMasterKey(string $hook, object $context = null)
    {
        switch ($hook) {
            case 'before_request_code':
                /** @var \Mariojgt\MasterKey\Dto\BeforeRequestCodeContext $context */
                // Example: restrict to certain domains or rate limit
                // if (!str_ends_with($context->email, '@example.com')) {
                //     return response()->json(['message' => 'Email not allowed'], 403);
                // }
                break;

            case 'after_request_code':
                /** @var \Mariojgt\MasterKey\Dto\AfterRequestCodeContext $context */
                // Example: audit log
                // logger()->info('Verification code requested', ['email' => $context->email]);
                break;

            case 'before_verify':
                /** @var \Mariojgt\MasterKey\Dto\BeforeVerifyContext $context */
                // Example: captcha/anti-abuse gate
                break;

            case 'after_verify':
                /** @var \Mariojgt\MasterKey\Dto\AfterVerifyContext $context */
                // Example: transform response or attach extra fields
                // return response()->json([
                //     'token' => $context->token,
                //     'user' => ['id' => $context->user->id, 'email' => $context->user->email],
                //     'roles' => $context->user->roles ?? [],
                // ]);
                break;

            case 'before_web_login':
                /** @var \Mariojgt\MasterKey\Dto\BeforeWebLoginContext $context */
                // Example: block login for suspended users
                // $session = $context->session;
                // if ($session && someCondition()) {
                //     return response()->json(['status' => 'blocked'], 403);
                // }
                break;

            case 'after_web_login':
                /** @var \Mariojgt\MasterKey\Dto\AfterWebLoginContext $context */
                // Example: override redirect
                // return '/dashboard';
                // or return ['redirect' => '/dashboard'];
                break;

            case 'before_approve':
                /** @var \Mariojgt\MasterKey\Dto\BeforeApproveContext $context */
                // Example: validate requester permissions
                break;

            case 'after_approve':
                /** @var \Mariojgt\MasterKey\Dto\AfterApproveContext $context */
                // Example: notify user or system
                break;
        }

        // Return null to continue default behavior
        return null;
    }
}
