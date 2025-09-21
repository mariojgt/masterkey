<?php

namespace Mariojgt\MasterKey\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mariojgt\MasterKey\Models\MasterKeyToken;
use Mariojgt\MasterKey\Models\MasterKeySession;
use App\Models\User;

class PolymorphicRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function testTokenCanBeCreatedForUser()
    {
        $user = User::factory()->create();

        $token = MasterKeyToken::create([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'token' => 'test-token-123',
            'name' => 'masterkey-app'
        ]);

        $this->assertDatabaseHas('masterkey_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'token' => 'test-token-123'
        ]);

        // Test polymorphic relationship
        $this->assertInstanceOf(User::class, $token->tokenable);
        $this->assertEquals($user->id, $token->tokenable->id);
        $this->assertEquals($user->email, $token->tokenable->email);
    }

    /** @test */
    public function testTokenPolymorphicRelationshipReturnsCorrectModel()
    {
        $user = User::factory()->create(['name' => 'Test User']);

        $token = MasterKeyToken::create([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'token' => 'relationship-test-token',
            'name' => 'masterkey-app'
        ]);

        // Test the polymorphic relationship
        $retrievedModel = $token->tokenable;

        $this->assertNotNull($retrievedModel);
        $this->assertInstanceOf(User::class, $retrievedModel);
        $this->assertEquals($user->id, $retrievedModel->id);
        $this->assertEquals($user->name, $retrievedModel->name);
        $this->assertEquals($user->email, $retrievedModel->email);
    }

    /** @test */
    public function testSessionCanBeCreatedWithPolymorphicRelationship()
    {
        $user = User::factory()->create();

        $session = MasterKeySession::create([
            'session_id' => 'test-session-123',
            'status' => 'approved',
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id
        ]);

        $this->assertDatabaseHas('masterkey_sessions', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'session_id' => 'test-session-123'
        ]);

        // Test polymorphic relationship
        $this->assertInstanceOf(User::class, $session->tokenable);
        $this->assertEquals($user->id, $session->tokenable->id);
    }

    /** @test */
    public function testLegacyUserMethodStillWorks()
    {
        $user = User::factory()->create();

        $token = MasterKeyToken::create([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'token' => 'legacy-test-token',
            'name' => 'masterkey-app'
        ]);

        // Test legacy user() method
        $legacyUser = $token->user;
        $this->assertInstanceOf(User::class, $legacyUser);
        $this->assertEquals($user->id, $legacyUser->id);
    }

    /** @test */
    public function testCanRetrieveTokenByPolymorphicModel()
    {
        $user = User::factory()->create();

        $token = MasterKeyToken::create([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'token' => 'retrieval-test-token',
            'name' => 'masterkey-app'
        ]);

        // Find token by polymorphic relationship
        $foundToken = MasterKeyToken::where('tokenable_type', User::class)
                                   ->where('tokenable_id', $user->id)
                                   ->first();

        $this->assertNotNull($foundToken);
        $this->assertEquals($token->id, $foundToken->id);
        $this->assertEquals($token->token, $foundToken->token);
    }

    /** @test */
    public function testTokenExpirationWorksWithPolymorphicModels()
    {
        $user = User::factory()->create();

        // Create expired token
        $expiredToken = MasterKeyToken::create([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'token' => 'expired-poly-token',
            'name' => 'masterkey-app',
            'expires_at' => now()->subDay()
        ]);

        // Create valid token
        $validToken = MasterKeyToken::create([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'token' => 'valid-poly-token',
            'name' => 'masterkey-app',
            'expires_at' => now()->addDay()
        ]);

        $this->assertTrue($expiredToken->isExpired());
        $this->assertFalse($validToken->isExpired());

        // Both should have valid polymorphic relationships
        $this->assertInstanceOf(User::class, $expiredToken->tokenable);
        $this->assertInstanceOf(User::class, $validToken->tokenable);
    }

    /** @test */
    public function testMultipleTokensForSameModel()
    {
        $user = User::factory()->create();

        $token1 = MasterKeyToken::create([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'token' => 'multi-token-1',
            'name' => 'masterkey-app'
        ]);

        $token2 = MasterKeyToken::create([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'token' => 'multi-token-2',
            'name' => 'masterkey-web'
        ]);

        // Both tokens should point to the same user
        $this->assertEquals($user->id, $token1->tokenable->id);
        $this->assertEquals($user->id, $token2->tokenable->id);
        $this->assertEquals($token1->tokenable->id, $token2->tokenable->id);

        // But they should be different tokens
        $this->assertNotEquals($token1->token, $token2->token);
        $this->assertNotEquals($token1->name, $token2->name);
    }

    /** @test */
    public function testPolymorphicRelationshipWithNullModel()
    {
        $token = MasterKeyToken::create([
            'tokenable_type' => User::class,
            'tokenable_id' => 99999, // Non-existent user
            'token' => 'null-model-token',
            'name' => 'masterkey-app'
        ]);

        // Should return null for non-existent model
        $this->assertNull($token->tokenable);
    }
}
