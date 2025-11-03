<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile'); // パスはプロジェクトに合わせて調整
        $response->assertStatus(200);
        $response->assertSee('Profile'); // 実際のビューの内容に合わせて変更
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $this->actingAs($user)->patch('/profile', [ // パスはプロジェクトに合わせて調整
            'name' => 'New Name',
            'email' => 'new@example.com',
        ])->assertRedirect(); // 遷移先に合わせて assertRedirect('/profile') 等へ変更

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'user@example.com',
        ]);

        $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => 'user@example.com',
        ])->assertRedirect();

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user)->delete('/user', [ // 実際の削除ルートを調整
            'password' => 'password',
        ])->assertRedirect();

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user)->delete('/user', [
            'password' => 'wrong-password',
        ]);

        // 失敗時の挙動に合わせてアサーションを調整（例：バリデーションエラーで302リダイレクト）
        $response->assertSessionHasErrors();
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
}
