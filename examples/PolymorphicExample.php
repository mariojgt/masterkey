<?php

namespace Mariojgt\MasterKey\Examples;

use Mariojgt\MasterKey\Models\MasterKeyToken;
use App\Models\User;

/**
 * Example of how to use the polymorphic MasterKey system
 * This file demonstrates how to create tokens for any model
 */
class PolymorphicExample
{
    public function createTokenForUser($userId)
    {
        $user = User::find($userId);

        return MasterKeyToken::create([
            'tokenable_type' => get_class($user),
            'tokenable_id' => $user->id,
            'token' => \Mariojgt\MasterKey\Support\StrUtil::random(60),
            'name' => 'masterkey-app',
            'expires_at' => now()->addDays(30),
        ]);
    }

    public function createTokenForAnyModel($model)
    {
        return MasterKeyToken::create([
            'tokenable_type' => get_class($model),
            'tokenable_id' => $model->id,
            'token' => \Mariojgt\MasterKey\Support\StrUtil::random(60),
            'name' => 'masterkey-app',
            'expires_at' => now()->addDays(30),
        ]);
    }

    public function getAuthenticatedModel($token)
    {
        $tokenRecord = MasterKeyToken::where('token', $token)->first();

        if (!$tokenRecord || $tokenRecord->isExpired()) {
            return null;
        }

        // This will work with any model now
        return $tokenRecord->tokenable;
    }

    public function demonstrateUsage()
    {
        // Example 1: Create token for User
        $user = User::first();
        if ($user) {
            $userToken = $this->createTokenForAnyModel($user);
            echo "Created token for User: " . $userToken->token . "\n";

            // Retrieve the authenticated model
            $authenticatedUser = $this->getAuthenticatedModel($userToken->token);
            echo "Authenticated model type: " . get_class($authenticatedUser) . "\n";
            echo "Authenticated model ID: " . $authenticatedUser->id . "\n";
        }

        // Example 2: If you had an Admin model, it would work the same way
        // $admin = Admin::first();
        // if ($admin) {
        //     $adminToken = $this->createTokenForAnyModel($admin);
        //     echo "Created token for Admin: " . $adminToken->token . "\n";
        // }
    }
}
