<?php

namespace Mariojgt\MasterKey\Tests\Stubs;

use Mariojgt\MasterKey\Contracts\MasterKeyHandler as MasterKeyHandlerContract;
use Mariojgt\MasterKey\Dto\AfterVerifyContext;
use Mariojgt\MasterKey\Enums\MasterKeyHookType;
use App\Models\User;

class TestMasterKeyHandler implements MasterKeyHandlerContract
{
    public function handleMasterKey(MasterKeyHookType $hook, object $context = null)
    {
        switch ($hook) {
            case MasterKeyHookType::CREATE_USER:
                /** @var \Mariojgt\MasterKey\Dto\CreateUserContext $context */
                if (isset($context->email)) {
                    // For testing, create or find user
                    return User::firstOrCreate(['email' => $context->email], [
                        'name' => explode('@', $context->email)[0],
                        'password' => bcrypt('test-password'),
                        'email_verified_at' => now(),
                    ]);
                }
                break;
            case MasterKeyHookType::AFTER_VERIFY:
                // Legacy support - though CREATE_USER is preferred
                if (isset($context->email)) {
                    return User::firstOrCreate(['email' => $context->email], [
                        'name' => explode('@', $context->email)[0],
                        'password' => bcrypt('test-password'),
                        'email_verified_at' => now(),
                    ]);
                }
                break;
        }

        return null;
    }
}
