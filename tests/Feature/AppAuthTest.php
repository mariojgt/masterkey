<?php

namespace Mariojgt\MasterKey\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Mariojgt\MasterKey\Models\MasterKeyVerification;
use Mariojgt\MasterKey\Models\MasterKeyToken;
use Mariojgt\MasterKey\Mail\VerificationCodeMail;
use App\Models\User;

class AppAuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();

        // Clear config cache and set test handler
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        config(['masterkey.handler' => \Mariojgt\MasterKey\Tests\Stubs\TestMasterKeyHandler::class]);

        // Ensure the config is actually set
        $this->assertEquals(
            \Mariojgt\MasterKey\Tests\Stubs\TestMasterKeyHandler::class,
            config('masterkey.handler')
        );
    }

    /** @test */
    public function it_can_request_verification_code()
    {
        $email = $this->faker->email;

        $response = $this->postJson('/api/app/request-code', [
            'email' => $email
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'nonce',
                    'message'
                ]);

        // Verify verification record was created
        $this->assertDatabaseHas('masterkey_verifications', [
            'email' => $email,
            'used' => false
        ]);

        // Verify email was sent
        Mail::assertSent(VerificationCodeMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
    }

    /** @test */
    public function it_requires_valid_email_for_request_code()
    {
        $response = $this->postJson('/api/app/request-code', [
            'email' => 'invalid-email'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_can_verify_code_and_create_token_for_user()
    {
        // Create a user first
        $user = User::factory()->create(['email' => 'test@example.com']);

        // Create verification record
        $verification = MasterKeyVerification::create([
            'email' => $user->email,
            'nonce' => 'test-nonce-123',
            'code' => '123456'
        ]);

        $response = $this->postJson('/api/app/verify', [
            'nonce' => 'test-nonce-123',
            'code' => '123456'
        ]);

        if ($response->getStatusCode() !== 200) {
            dump('Response status:', $response->getStatusCode());
            dump('Response body:', $response->getContent());
        }

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'token',
                    'user' => [
                        'id',
                        'email'
                    ]
                ]);

        // Verify token was created with polymorphic relationship
        $this->assertDatabaseHas('masterkey_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'name' => 'masterkey-app'
        ]);

        // Verify verification was marked as used
        $verification->refresh();
        $this->assertTrue($verification->used);
    }

    /** @test */
    public function it_fails_verification_with_invalid_code()
    {
        MasterKeyVerification::create([
            'email' => 'test@example.com',
            'nonce' => 'test-nonce-123',
            'code' => '123456'
        ]);

        $response = $this->postJson('/api/app/verify', [
            'nonce' => 'test-nonce-123',
            'code' => '654321' // Wrong code
        ]);

        $response->assertStatus(422)
                ->assertJson(['message' => 'Invalid code']);
    }

    /** @test */
    public function it_fails_verification_with_invalid_nonce()
    {
        $response = $this->postJson('/api/app/verify', [
            'nonce' => 'invalid-nonce',
            'code' => '123456'
        ]);

        $response->assertStatus(422)
                ->assertJson(['message' => 'Invalid code']);
    }

    /** @test */
    public function it_fails_verification_with_used_code()
    {
        $verification = MasterKeyVerification::create([
            'email' => 'test@example.com',
            'nonce' => 'test-nonce-123',
            'code' => '123456',
            'used' => true // Already used
        ]);

        $response = $this->postJson('/api/app/verify', [
            'nonce' => 'test-nonce-123',
            'code' => '123456'
        ]);

        $response->assertStatus(422)
                ->assertJson(['message' => 'Invalid code']);
    }

    /** @test */
    public function it_validates_verification_request_data()
    {
        $response = $this->postJson('/api/app/verify', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['nonce', 'code']);
    }

    /** @test */
    public function it_validates_code_length()
    {
        $response = $this->postJson('/api/app/verify', [
            'nonce' => 'test-nonce',
            'code' => '123' // Too short
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['code']);
    }

    /** @test */
    public function it_can_handle_polymorphic_models()
    {
        // This test would require an actual Admin model
        // For now, we'll test with User but the principle is the same
        $user = User::factory()->create(['email' => 'admin@example.com']);

        $verification = MasterKeyVerification::create([
            'email' => $user->email,
            'nonce' => 'admin-nonce-123',
            'code' => '123456'
        ]);

        $response = $this->postJson('/api/app/verify', [
            'nonce' => 'admin-nonce-123',
            'code' => '123456'
        ]);

        $response->assertStatus(200);

        // Verify the token was created with correct polymorphic relationship
        $token = MasterKeyToken::where('tokenable_type', User::class)
                              ->where('tokenable_id', $user->id)
                              ->first();

        $this->assertNotNull($token);
        $this->assertEquals(User::class, $token->tokenable_type);
        $this->assertEquals($user->id, $token->tokenable_id);
        $this->assertEquals($user->id, $token->tokenable->id);
        $this->assertEquals($user->email, $token->tokenable->email);
    }
}
