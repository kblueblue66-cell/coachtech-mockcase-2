<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_login_email_required()
    {
        $response = $this->post('/login',[
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }
    public function test_user_login_password_required()
    {
        $response = $this->post('/login',[
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    public function test_user_login_with_wrong_credentials()
    {
        $user = User::factory()->create([
            'email' => 'correct@example.com',
            'password' => bcrypt('password123'),
            'role' => 0,
        ]);

        $response = $this->post('/login',[
            'email' =>'wrong@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }

    public function test_admin_login_email_required()
    {
        $response = $this->post('/admin/login',[
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    public function test_admin_login_password_required()
    {
        $response = $this->post('/admin/login',[
            'email' => 'admin@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    public function test_admin_login_with_wrong_credentials()
    {
        $admin = User::factory()->create(['role' => 1]);

        $response = $this->post('/admin/login',[
            'email' => 'wrong-admin@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }

}