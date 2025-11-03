<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_confirm_password_when_required()
    {
        $password = 'confirm-pass-123';
        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('password.confirm'), [
            'password' => $password,
        ]);

        // 200 / 204 等に合わせて調整
        $response->assertStatus(200);
    }
}
