<?php

namespace Mariojgt\MasterKey\Support;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use Mariojgt\MasterKey\Dto\AfterApproveContext;
use Mariojgt\MasterKey\Dto\AfterRequestCodeContext;
use Mariojgt\MasterKey\Dto\AfterVerifyContext;
use Mariojgt\MasterKey\Dto\AfterWebLoginContext;
use Mariojgt\MasterKey\Dto\BeforeApproveContext;
use Mariojgt\MasterKey\Dto\BeforeRequestCodeContext;
use Mariojgt\MasterKey\Dto\BeforeVerifyContext;
use Mariojgt\MasterKey\Dto\BeforeWebLoginContext;

class MasterKeyHook
{
    /**
     * Trigger a configured handler hook.
     * If the handler returns a Response or implements Responsable, return it to allow short-circuiting.
     * Otherwise, return null.
     *
     * @param string $hook
     * @param array $context
     * @return mixed|null
     */
    public static function trigger(string $hook, array $context = [])
    {
        $handler = config('masterkey.handler');
        if (!$handler) {
            return null;
        }

        try {
            $instance = app($handler);
        } catch (\Throwable $e) {
            logger()->error('MasterKey handler resolution failed', [
                'handler' => $handler,
                'hook' => $hook,
                'error' => $e->getMessage(),
            ]);
            return null;
        }

        if (!method_exists($instance, 'handleMasterKey')) {
            logger()->warning('MasterKey handler missing handle method', [
                'handler' => $handler,
                'hook' => $hook,
            ]);
            return null;
        }

        try {
            $dto = self::toDto($hook, $context);
            return $instance->handleMasterKey($hook, $dto);
        } catch (\Throwable $e) {
            logger()->error('MasterKey handler threw', [
                'handler' => $handler,
                'hook' => $hook,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    protected static function toDto(string $hook, array $context): ?object
    {
        return match ($hook) {
            'before_request_code' => new BeforeRequestCodeContext($context['request'], $context['email']),
            'after_request_code' => new AfterRequestCodeContext($context['request'], $context['email']),
            'before_verify' => new BeforeVerifyContext($context['request'], $context['nonce'], $context['code']),
            'after_verify' => new AfterVerifyContext(
                $context['request'],
                $context['user'],
                $context['token'],
                $context['response']
            ),
            'before_web_login' => new BeforeWebLoginContext($context['request'], $context['session']),
            'after_web_login' => new AfterWebLoginContext(
                $context['request'],
                $context['session'],
                $context['user_id'],
                $context['default_redirect']
            ),
            'before_approve' => new BeforeApproveContext($context['request'], $context['session_id']),
            'after_approve' => new AfterApproveContext($context['request'], $context['session'], $context['response']),
            default => null,
        };
    }
}
