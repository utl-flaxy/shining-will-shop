<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function password_reset_link_is_sent()
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->postJson(route('password.email'), [
            'email' => $user->email,
        ]);

        // 実装に合わせてステータスを調整
        $response->assertStatus(200);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    /** @test */
    public function user_can_reset_password_with_valid_token()
    {
        $user = User::factory()->create();

        // トークンを作成
        $token = Password::broker()->createToken($user);

        $newPassword = 'new-password-123';

        $response = $this->postJson(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        // 実装に合わせて調整（例: 200 / 302）
        $response->assertStatus(200);

        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));
    }
}
