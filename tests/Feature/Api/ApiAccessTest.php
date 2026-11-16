<?php

namespace Tests\Feature\Api;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiAccessTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_api_login_returns_a_token(): void
    {
        $this->postJson('/api/login', ['login' => 'admin@cms.edu', 'password' => 'password'])
            ->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'role']]);
    }

    public function test_api_login_by_name_is_rejected(): void
    {
        $admin = User::where('email', 'admin@cms.edu')->first();

        $this->postJson('/api/login', ['login' => $admin->name, 'password' => 'password'])
            ->assertStatus(422);
    }

    public function test_api_login_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/login', ['login' => 'admin@cms.edu', 'password' => 'wrong']);
        }

        $this->postJson('/api/login', ['login' => 'admin@cms.edu', 'password' => 'password'])
            ->assertStatus(429);
    }

    public function test_student_token_cannot_enter_marks(): void
    {
        $student = User::where('role', 'student')->whereHas('studentProfile')->firstOrFail();
        Sanctum::actingAs($student);

        $this->postJson('/api/exams/marks', [
            'student_id' => $student->studentProfile->id,
            'subject_id' => Subject::first()->id,
            'semester' => 1,
            'internal_marks' => 40,
            'external_marks' => 60,
        ])->assertForbidden();
    }

    public function test_unauthenticated_requests_are_rejected(): void
    {
        $this->getJson('/api/dashboard')->assertUnauthorized();
    }
}
