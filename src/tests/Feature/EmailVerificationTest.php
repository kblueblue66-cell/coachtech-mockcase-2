<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;


class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_is_sent_after_registration()
    {
        Notification::fake();

        $this->post('/register',[
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        Notification::assertSentTo(
            User::where('email','test@example.com')->first(),
            VerifyEmail::class
        );
    }

    public function test_guidance_screen_has_verification_button()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'role' => 0,
        ]);

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertStatus(200);
        $response->assertSee('認証はこちらから');
    }

    public function test_email_can_be_verified()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'role' => 0,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id,'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect('/attendance?verified=1');

        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
