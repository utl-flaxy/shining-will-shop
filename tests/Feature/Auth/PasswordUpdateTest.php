<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_update_password()
    {
        $oldPassword = 'old-pass-123';
        $user = User::factory()->create([
            'password' => bcrypt($oldPassword),
        ]);

        $this->actingAs($user);

        $newPassword = 'new-pass-456';

        $response = $this->postJson(route('password.update.auth'), [
            'current_password' => $oldPassword,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        // 200 / 204 などに合わせて調整
        $response->assertStatus(200);

        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));
    }
}
