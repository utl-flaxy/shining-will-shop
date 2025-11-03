<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function verification_notification_is_sent_when_requested()
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->postJson(route('verification.send'));

        // 200/202/302 のどれかになる場合があるので調整してください
        $response->assertStatus(200);

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /** @test */
    public function user_can_verify_email_via_signed_url()
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        // 多くの実装はリダイレクトする（302）
        $response->assertStatus(302);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
