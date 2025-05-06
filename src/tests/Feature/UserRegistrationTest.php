<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_is_accessible()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_name_is_required_for_registration()
    {
        $response = $this->post('/register', [
            'name' => '', // 空のデータを送信
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください',
        ]);
    }

    public function test_email_is_required_for_registration()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => '', // 空のデータを送信
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    public function test_password_is_required_for_registration()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '', // 空のデータを送信
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    public function test_password_must_be_at_least_8_characters()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short', // 7文字未満のパスワード
            'password_confirmation' => 'short',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    public function test_password_must_be_confirmed()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123', // パスワード
            'password_confirmation' => 'different_password', // 異なる確認用パスワード
        ]);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['password' => 'パスワードと一致しません。']);
    }

    public function test_user_can_register_successfully()
    {
        $this->withoutMiddleware(\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class);

        Event::fake([
            Registered::class,
        ]);
        $userData = [
            'name' => '山田 太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $userData);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);

        $user = User::where('email', $userData['email'])->first();

        // メール認証を完了させる
        $user->markEmailAsVerified();

        // 新しいリクエストを作成して認証済みユーザーとしてアクセス
        $response = $this->actingAs($user)
            ->get(route('profile.edit'));

        // 認証されていることを確認
        $this->assertAuthenticated();

        $response->assertStatus(200);
    }
}
