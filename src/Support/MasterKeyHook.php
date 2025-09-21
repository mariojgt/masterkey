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
use Mariojgt\MasterKey\Dto\CreateUserContext;
use Mariojgt\MasterKey\Dto\BeforeWebLoginContext;
use Mariojgt\MasterKey\Enums\MasterKeyHookType;

class MasterKeyHook
{
    /**
     * Trigger a configured handler hook.
     * If the handler returns a Response or implements Responsable, return it to allow short-circuiting.
     * Otherwise, return null.
     *
     * @param MasterKeyHookType|string $hook
     * @param array $context
     * @return mixed|null
     */
    public static function trigger(MasterKeyHookType|string $hook, array $context = [])
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
                'hook' => $hook instanceof MasterKeyHookType ? $hook->value : $hook,
                'error' => $e->getMessage(),
            ]);
            return null;
        }

        if (!method_exists($instance, 'handleMasterKey')) {
            logger()->warning('MasterKey handler missing handle method', [
                'handler' => $handler,
                'hook' => $hook instanceof MasterKeyHookType ? $hook->value : $hook,
            ]);
            return null;
        }

        try {
            $dto = self::toDto($hook, $context);
            return $instance->handleMasterKey($hook, $dto);
        } catch (\Throwable $e) {
            logger()->error('MasterKey handler threw', [
                'handler' => $handler,
                'hook' => $hook instanceof MasterKeyHookType ? $hook->value : $hook,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    protected static function toDto(MasterKeyHookType|string $hook, array $context): ?object
    {
        // Convert string to enum if necessary for backward compatibility
        if (is_string($hook)) {
            $hook = MasterKeyHookType::tryFrom($hook);
            if (!$hook) {
                return null;
            }
        }

        return match ($hook) {
            MasterKeyHookType::BEFORE_REQUEST_CODE => new BeforeRequestCodeContext(
                $context['request'],
                $context['email']
            ),
            MasterKeyHookType::AFTER_REQUEST_CODE => new AfterRequestCodeContext(
                $context['request'],
                $context['email']
            ),
            MasterKeyHookType::BEFORE_VERIFY => new BeforeVerifyContext(
                $context['request'],
                $context['nonce'],
                $context['code']
            ),
            MasterKeyHookType::CREATE_USER => new CreateUserContext(
                $context['request'],
                $context['email'],
                $context['nonce'],
                $context['verification']
            ),
            MasterKeyHookType::AFTER_VERIFY => new AfterVerifyContext(
                $context['request'],
                $context['user'],
                $context['token'],
                $context['response']
            ),
            MasterKeyHookType::BEFORE_WEB_LOGIN => new BeforeWebLoginContext($context['request'], $context['session']),
            MasterKeyHookType::AFTER_WEB_LOGIN => new AfterWebLoginContext(
                $context['request'],
                $context['session'],
                $context['user_id'],
                $context['default_redirect']
            ),
            MasterKeyHookType::BEFORE_APPROVE => new BeforeApproveContext($context['request'], $context['session_id']),
            MasterKeyHookType::AFTER_APPROVE => new AfterApproveContext(
                $context['request'],
                $context['session'],
                $context['response']
            ),
            default => null,
        };
    }
}
