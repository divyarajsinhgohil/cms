<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Opens every page a role is meant to use and checks it loads (no 500, no
 * accidental "no access" redirect). Catches broken views and over-strict rules.
 */
class PageSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private const COMMON = ['/dashboard', '/materials', '/notices', '/exams', '/timetables', '/complaints', '/certificates', '/hostels', '/academics/my-syllabus', '/alerts'];

    private const STAFF = ['/attendance', '/admissions', '/academics/syllabi', '/leaves', '/exams/collection', '/whatsapp'];

    private const MANAGEMENT = ['/academics/departments', '/academics/courses', '/academics/subjects', '/academics/staff', '/sections', '/fees', '/marketing', '/broadcasts', '/settings', '/admission-applications', '/admissions/import'];

    private function assertPagesLoad(string $role, array $urls): void
    {
        $user = User::where('role', $role)->firstOrFail();
        $user->update(['must_change_password' => false]);

        foreach ($urls as $url) {
            $response = $this->actingAs($user)->get($url);
            $this->assertSame(200, $response->status(), "{$role} could not open {$url} (status {$response->status()})");
        }
    }

    public function test_student_pages_load(): void
    {
        $this->assertPagesLoad('student', self::COMMON);
    }

    public function test_staff_pages_load(): void
    {
        $this->assertPagesLoad('staff', array_merge(self::COMMON, self::STAFF));
    }

    public function test_management_pages_load(): void
    {
        $this->assertPagesLoad('management', array_merge(self::COMMON, self::STAFF, self::MANAGEMENT));
    }

    public function test_public_pages_load(): void
    {
        $this->get('/login')->assertOk();
        $this->get('/apply')->assertOk();
        $this->get('/home')->assertOk();
    }
}
