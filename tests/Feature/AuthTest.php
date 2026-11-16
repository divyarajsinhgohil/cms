<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_user_can_log_in_with_email(): void
    {
        $this->post('/login', ['login' => 'admin@cms.edu', 'password' => 'password'])
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs(User::where('email', 'admin@cms.edu')->first());
    }

    public function test_user_can_log_in_with_phone(): void
    {
        $admin = User::where('email', 'admin@cms.edu')->first();

        $this->post('/login', ['login' => $admin->phone, 'password' => 'password']);

        $this->assertAuthenticatedAs($admin);
    }

    public function test_login_by_name_is_no_longer_allowed(): void
    {
        $admin = User::where('email', 'admin@cms.edu')->first();

        $this->post('/login', ['login' => $admin->name, 'password' => 'password'])
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_wrong_password_is_rejected(): void
    {
        $this->post('/login', ['login' => 'admin@cms.edu', 'password' => 'wrong-password'])
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_login_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['login' => 'admin@cms.edu', 'password' => 'wrong']);
        }

        $this->post('/login', ['login' => 'admin@cms.edu', 'password' => 'password'])
            ->assertStatus(429);

        $this->assertGuest();
    }

    public function test_quick_login_backdoor_route_is_gone(): void
    {
        $this->get('/quick-login/management')->assertNotFound();
        $this->assertGuest();
    }

    public function test_student_who_left_cannot_log_in(): void
    {
        $student = User::where('role', 'student')->whereHas('studentProfile')->first();
        $student->studentProfile->update(['status' => 'Left']);

        $this->post('/login', ['login' => $student->email, 'password' => 'password'])
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_first_login_forces_password_change(): void
    {
        $user = User::where('must_change_password', true)->first();

        $this->post('/login', ['login' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('password.change.show'));
    }

    public function test_new_password_must_be_strong(): void
    {
        $user = User::where('email', 'admin@cms.edu')->first();

        $this->actingAs($user)
            ->post('/change-password', ['password' => 'abc123', 'password_confirmation' => 'abc123'])
            ->assertSessionHasErrors('password');

        $this->actingAs($user)
            ->post('/change-password', ['password' => 'onlyletters', 'password_confirmation' => 'onlyletters'])
            ->assertSessionHasErrors('password');

        $this->actingAs($user)
            ->post('/change-password', ['password' => 'NewPass2026', 'password_confirmation' => 'NewPass2026'])
            ->assertRedirect(route('dashboard'));

        $this->assertTrue(Hash::check('NewPass2026', $user->fresh()->password));
    }
}
