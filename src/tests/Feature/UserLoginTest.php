<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_is_required_for_login()
{
    $response = $this->post('/login', [
        'email' => '', // メールアドレスを空白にする
        'password' => 'password123'
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors([
        'email' => 'メールアドレスを入力してください'
    ]);
}

    public function test_password_is_required_for_login()
{
    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => '' // パスワードを空白にする
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors([
        'password' => 'パスワードを入力してください'
    ]);
}

    public function test_user_cannot_login_with_invalid_credentials()
{
    // 無効なメールアドレスとパスワードでログインを試みる
    $response = $this->post('/login', [
        'email' => 'invalid@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors([
        'email' => 'ログイン情報が登録されていません'
    ]);
}

    public function test_user_can_login_with_correct_credentials()
{
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123')
    ]);

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'password123'
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('index'));
}

}
