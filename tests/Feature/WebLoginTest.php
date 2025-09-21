<?php

namespace Mariojgt\MasterKey\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mariojgt\MasterKey\Models\MasterKeySession;
use Mariojgt\MasterKey\Models\MasterKeyToken;
use App\Models\User;

class WebLoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function testCanCheckLoginStatus()
    {
        $session = MasterKeySession::create([
            'session_id' => 'test-session-123',
            'status' => 'pending'
        ]);

        $response = $this->getJson('/api/web/status?session_id=test-session-123');

        $response->assertStatus(200)
                ->assertJson(['status' => 'pending']);
    }

    /** @test */
    public function testReturnsMissingForInvalidSession()
    {
        $response = $this->getJson('/api/web/status?session_id=invalid-session');

        $response->assertStatus(200)
                ->assertJson(['status' => 'missing']);
    }

    /** @test */
    public function testCanApproveLoginWithPolymorphicUser()
    {
        $user = User::factory()->create();
        $token = MasterKeyToken::create([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'token' => 'test-token-123',
            'name' => 'masterkey-app'
        ]);

        $session = MasterKeySession::create([
            'session_id' => 'test-session-123',
            'status' => 'pending'
        ]);

        $response = $this->postJson('/api/web/approve', [
            'session_id' => 'test-session-123'
        ], [
            'Authorization' => 'Bearer test-token-123'
        ]);

        $response->assertStatus(200)
                ->assertJson(['ok' => true]);

        // Verify session was updated with polymorphic relationship
        $session->refresh();
        $this->assertEquals('approved', $session->status);
        $this->assertEquals(User::class, $session->tokenable_type);
        $this->assertEquals($user->id, $session->tokenable_id);
    }

    /** @test */
    public function testApproveRequiresAuthentication()
    {
        $session = MasterKeySession::create([
            'session_id' => 'test-session-123',
            'status' => 'pending'
        ]);

        $response = $this->postJson('/api/web/approve', [
            'session_id' => 'test-session-123'
        ]);

        $response->assertStatus(401)
                ->assertJson(['message' => 'Unauthorized']);
    }

    /** @test */
    public function testCannotApproveInvalidSession()
    {
        $user = User::factory()->create();
        $token = MasterKeyToken::create([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'token' => 'test-token-123',
            'name' => 'masterkey-app'
        ]);

        $response = $this->postJson('/api/web/approve', [
            'session_id' => 'invalid-session'
        ], [
            'Authorization' => 'Bearer test-token-123'
        ]);

        $response->assertStatus(422)
                ->assertJson(['message' => 'Invalid session']);
    }

    /** @test */
    public function testCannotApproveAlreadyApprovedSession()
    {
        $user = User::factory()->create();
        $token = MasterKeyToken::create([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'token' => 'test-token-123',
            'name' => 'masterkey-app'
        ]);

        $session = MasterKeySession::create([
            'session_id' => 'test-session-123',
            'status' => 'approved'
        ]);

        $response = $this->postJson('/api/web/approve', [
            'session_id' => 'test-session-123'
        ], [
            'Authorization' => 'Bearer test-token-123'
        ]);

        $response->assertStatus(422)
                ->assertJson(['message' => 'Invalid session']);
    }

    /** @test */
    public function testStatusWithApprovedSessionRedirectsUser()
    {
        $user = User::factory()->create();

        $session = MasterKeySession::create([
            'session_id' => 'test-session-123',
            'status' => 'approved',
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id
        ]);

        $response = $this->getJson('/api/web/status?session_id=test-session-123');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'redirect'
                ]);

        // Verify session was marked as used
        $session->refresh();
        $this->assertEquals('used', $session->status);
    }

    /** @test */
    public function testPolymorphicTokenAuthentication()
    {
        $user = User::factory()->create();

        // Create token with polymorphic relationship
        $token = MasterKeyToken::create([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'token' => 'polymorphic-token-123',
            'name' => 'masterkey-app'
        ]);

        $session = MasterKeySession::create([
            'session_id' => 'test-session-456',
            'status' => 'pending'
        ]);

        // Test that polymorphic token works for authentication
        $response = $this->postJson('/api/web/approve', [
            'session_id' => 'test-session-456'
        ], [
            'Authorization' => 'Bearer polymorphic-token-123'
        ]);

        $response->assertStatus(200);

        // Verify the tokenable relationship works
        $this->assertEquals($user->id, $token->tokenable->id);
        $this->assertEquals($user->email, $token->tokenable->email);
        $this->assertInstanceOf(User::class, $token->tokenable);
    }

    /** @test */
    public function testExpiredTokenIsRejected()
    {
        $user = User::factory()->create();

        $token = MasterKeyToken::create([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'token' => 'expired-token-123',
            'name' => 'masterkey-app',
            'expires_at' => now()->subDay() // Expired yesterday
        ]);

        $session = MasterKeySession::create([
            'session_id' => 'test-session-789',
            'status' => 'pending'
        ]);

        $response = $this->postJson('/api/web/approve', [
            'session_id' => 'test-session-789'
        ], [
            'Authorization' => 'Bearer expired-token-123'
        ]);

        $response->assertStatus(401)
                ->assertJson(['message' => 'Invalid token']);
    }
}
