<?php

namespace Mariojgt\MasterKey\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mariojgt\MasterKey\Models\MasterKeyToken;
use App\Models\User;

class AuthTokenMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function testMiddlewareAuthenticatesValidToken()
    {
        $user = User::factory()->create();

        $token = MasterKeyToken::create([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'token' => 'valid-token-123',
            'name' => 'masterkey-app'
        ]);

        $response = $this->postJson('/api/web/approve', [
            'session_id' => 'test-session'
        ], [
            'Authorization' => 'Bearer valid-token-123'
        ]);

        // Should not return 401 unauthorized
        $this->assertNotEquals(401, $response->getStatusCode());
    }

    /** @test */
    public function testMiddlewareRejectsInvalidToken()
    {
        $response = $this->postJson('/api/web/approve', [
            'session_id' => 'test-session'
        ], [
            'Authorization' => 'Bearer invalid-token'
        ]);

        $response->assertStatus(401)
                ->assertJson(['message' => 'Invalid token']);
    }

    /** @test */
    public function testMiddlewareRejectsMissingToken()
    {
        $response = $this->postJson('/api/web/approve', [
            'session_id' => 'test-session'
        ]);

        $response->assertStatus(401)
                ->assertJson(['message' => 'Unauthorized']);
    }

    /** @test */
    public function testMiddlewareRejectsExpiredToken()
    {
        $user = User::factory()->create();

        $token = MasterKeyToken::create([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'token' => 'expired-token-123',
            'name' => 'masterkey-app',
            'expires_at' => now()->subDay()
        ]);

        $response = $this->postJson('/api/web/approve', [
            'session_id' => 'test-session'
        ], [
            'Authorization' => 'Bearer expired-token-123'
        ]);

        $response->assertStatus(401)
                ->assertJson(['message' => 'Invalid token']);
    }

    /** @test */
    public function testMiddlewareUpdatesLastUsedAt()
    {
        $user = User::factory()->create();

        $token = MasterKeyToken::create([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'token' => 'track-usage-token',
            'name' => 'masterkey-app',
            'last_used_at' => null
        ]);

        $this->assertNull($token->last_used_at);

        $this->postJson('/api/web/approve', [
            'session_id' => 'test-session'
        ], [
            'Authorization' => 'Bearer track-usage-token'
        ]);

        $token->refresh();
        $this->assertNotNull($token->last_used_at);
    }

    /** @test */
    public function testMiddlewareWorksWithPolymorphicRelationship()
    {
        $user = User::factory()->create();

        $token = MasterKeyToken::create([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'token' => 'polymorphic-test-token',
            'name' => 'masterkey-app'
        ]);

        // Verify the polymorphic relationship works
        $this->assertEquals($user->id, $token->tokenable->id);
        $this->assertEquals($user->email, $token->tokenable->email);
        $this->assertInstanceOf(User::class, $token->tokenable);

        // Test middleware authentication
        $response = $this->postJson('/api/web/approve', [
            'session_id' => 'test-session'
        ], [
            'Authorization' => 'Bearer polymorphic-test-token'
        ]);

        // Should pass authentication (even if it fails later due to invalid session)
        $this->assertNotEquals(401, $response->getStatusCode());
    }

    /** @test */
    public function testMiddlewareRejectsTokenWithMissingTokenable()
    {
        // Create token with non-existent user
        $token = MasterKeyToken::create([
            'tokenable_type' => User::class,
            'tokenable_id' => 99999, // Non-existent user ID
            'token' => 'orphaned-token',
            'name' => 'masterkey-app'
        ]);

        $response = $this->postJson('/api/web/approve', [
            'session_id' => 'test-session'
        ], [
            'Authorization' => 'Bearer orphaned-token'
        ]);

        $response->assertStatus(401)
                ->assertJson(['message' => 'Invalid token']);
    }
}
