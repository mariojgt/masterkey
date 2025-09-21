<?php

namespace App\Helpers;

use Mariojgt\MasterKey\Dto\AfterVerifyContext;
use Mariojgt\MasterKey\Dto\AfterApproveContext;
use Mariojgt\MasterKey\Dto\BeforeVerifyContext;
use Mariojgt\MasterKey\Enums\MasterKeyHookType;
use Mariojgt\MasterKey\Dto\AfterWebLoginContext;
use Mariojgt\MasterKey\Dto\BeforeApproveContext;
use Mariojgt\MasterKey\Dto\BeforeWebLoginContext;
use Mariojgt\MasterKey\Dto\AfterRequestCodeContext;
use Mariojgt\MasterKey\Dto\BeforeRequestCodeContext;
use Mariojgt\MasterKey\Contracts\MasterKeyHandler as MasterKeyHandlerContract;

class MasterKeyHandler implements MasterKeyHandlerContract
{
    // Register this class in config/masterkey.php as:
    // 'handler' => App\Helpers\MasterKeyHandler::class,

    public function handleMasterKey(MasterKeyHookType $hook, object $context = null)
    {
        switch ($hook) {
            case MasterKeyHookType::BEFORE_REQUEST_CODE:
                /** @var \Mariojgt\MasterKey\Dto\BeforeRequestCodeContext $context */
                // check if the email exists on the Admin model
                // $admin = Admin::where('email', $context->email)->first();
                // if (!$admin) {
                //     return response()->json(['message' => 'Email not allowed'], 403);
                // }
                break;

            case MasterKeyHookType::AFTER_REQUEST_CODE:
                /** @var \Mariojgt\MasterKey\Dto\AfterRequestCodeContext $context */
                // Example: audit log
                // logger()->info('Verification code requested', ['email' => $context->email]);
                break;

            case MasterKeyHookType::BEFORE_VERIFY:
                /** @var \Mariojgt\MasterKey\Dto\BeforeVerifyContext $context */
                // Example: captcha/anti-abuse gate
                break;

            case MasterKeyHookType::CREATE_USER:
                // If the context has an email, create or find the user/model
                // if (isset($context->email)) {
                //     // Option 1: Find the user on the admin table
                //     $user = Admin::where('email', $context->email)->first();

                //     // Return the model object so the controller can use it
                //     return $user; // or $admin, or $model
                // }
                break;
            case MasterKeyHookType::AFTER_VERIFY:
                // Implement the login logic here

                // You can also modify the response to include additional user data
                // return response()->json([
                //     'token' => $context->token,
                //     'user' => [
                //         'id' => $user->id,
                //         'email' => $user->email,
                //         'name' => $user->name,
                //         'roles' => $user->roles ?? [],
                //     ],
                // ]);
                break;

            case MasterKeyHookType::BEFORE_WEB_LOGIN:
                /** @var \Mariojgt\MasterKey\Dto\BeforeWebLoginContext $context */
                // Example: block login for suspended users
                // $session = $context->session;
                // if ($session && someCondition()) {
                //     return response()->json(['status' => 'blocked'], 403);
                // }
                break;

            case MasterKeyHookType::AFTER_WEB_LOGIN:
                /** @var \Mariojgt\MasterKey\Dto\AfterWebLoginContext $context */
                // find the user/admin by id if needed
                // $user = Admin::find($context->user_id);
                // if ($user && $user->is_suspended) {
                //     return response()->json(['status' => 'suspended'], 403);
                // }

                // Now login the user with the guard skeleton_admin
                // backendGuard()->login($user);

                break;

            case MasterKeyHookType::BEFORE_APPROVE:
                /** @var \Mariojgt\MasterKey\Dto\BeforeApproveContext $context */
                break;

            case MasterKeyHookType::AFTER_APPROVE:
                /** @var \Mariojgt\MasterKey\Dto\AfterApproveContext $context */
                // Example: notify user or system
                break;
        }

        // Return null to continue default behavior
        return null;
    }

    /**
     * Create or find user by email - customize this method for your needs
     * This is called during the verification process when a user needs to be created
     */
    private function createOrFindUser(string $email)
    {
        // Option 1: Using your User model (most common approach)
        $userModel = app(\App\Models\User::class);
        return $userModel::firstOrCreate(['email' => $email], [
            'name' => explode('@', $email)[0],
            'password' => bcrypt(\Illuminate\Support\Str::random(16)),
            // Add any other default fields your User model needs
            // 'email_verified_at' => now(),
        ]);

        // Option 2: If you want to prevent auto-creation, just find existing users
        // return $userModel::where('email', $email)->first();

        // Option 3: Custom user creation logic
        // return $userModel::firstOrCreate(['email' => $email], [
        //     'name' => $this->generateNameFromEmail($email),
        //     'password' => bcrypt(\Illuminate\Support\Str::random(16)),
        //     'role' => 'user',
        //     'status' => 'active',
        // ]);
    }

    /**
     * Create or find admin by email - example for different model
     */
    private function createOrFindAdmin(string $email)
    {
        // Example: Create/find admin from a different model
        // $adminModel = app(\App\Models\Admin::class);
        // return $adminModel::firstOrCreate(['email' => $email], [
        //     'name' => $this->generateNameFromEmail($email),
        //     'password' => bcrypt(\Illuminate\Support\Str::random(16)),
        //     'role' => 'admin',
        //     'status' => 'active',
        // ]);

        // For demonstration, fallback to User model
        return $this->createOrFindUser($email);
    }
}
