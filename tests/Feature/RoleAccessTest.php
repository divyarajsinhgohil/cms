<?php

namespace Tests\Feature;

use App\Models\AssignmentSubmission;
use App\Models\ExamMark;
use App\Models\LeaveRequest;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Every write action must be limited to the right role. The role middleware
 * sends a blocked user back to the dashboard with an error message.
 */
class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function user(string $role): User
    {
        return User::where('role', $role)->firstOrFail();
    }

    private function assertBlocked($response): void
    {
        $response->assertRedirect(route('dashboard'))->assertSessionHas('error');
    }

    public static function managementOnlyPages(): array
    {
        return [
            'departments' => ['/academics/departments'],
            'courses' => ['/academics/courses'],
            'subjects' => ['/academics/subjects'],
            'staff (salaries)' => ['/academics/staff'],
            'marketing' => ['/marketing'],
            'fees' => ['/fees'],
            'settings' => ['/settings'],
        ];
    }

    #[DataProvider('managementOnlyPages')]
    public function test_student_and_staff_cannot_open_management_pages(string $url): void
    {
        $this->assertBlocked($this->actingAs($this->user('student'))->get($url));
        $this->assertBlocked($this->actingAs($this->user('staff'))->get($url));
    }

    #[DataProvider('managementOnlyPages')]
    public function test_management_can_open_management_pages(string $url): void
    {
        $this->actingAs($this->user('management'))->get($url)->assertOk();
    }

    public function test_student_cannot_enter_their_own_exam_marks(): void
    {
        $studentUser = User::where('role', 'student')->whereHas('studentProfile')->firstOrFail();
        $before = ExamMark::count();

        $this->assertBlocked($this->actingAs($studentUser)->post('/exams/store', [
            'student_id' => $studentUser->studentProfile->id,
            'subject_id' => Subject::first()->id,
            'semester' => 1,
            'internal_marks' => 40,
            'external_marks' => 60,
        ]));

        $this->assertSame($before, ExamMark::count());
    }

    public function test_student_cannot_grade_an_assignment(): void
    {
        $submission = AssignmentSubmission::firstOrFail();

        $this->assertBlocked($this->actingAs($this->user('student'))
            ->post("/materials/submission/{$submission->id}/grade", ['marks_obtained' => 100]));

        $this->assertNotEquals(100, (float) $submission->fresh()->marks_obtained);
    }

    public function test_staff_can_grade_an_assignment(): void
    {
        $submission = AssignmentSubmission::firstOrFail();

        $this->actingAs($this->user('staff'))
            ->post("/materials/submission/{$submission->id}/grade", ['marks_obtained' => 75])
            ->assertSessionHas('success');

        $this->assertEquals(75, (float) $submission->fresh()->marks_obtained);
    }

    public function test_student_cannot_post_notices_or_upload_materials(): void
    {
        $student = $this->user('student');

        $this->assertBlocked($this->actingAs($student)->post('/notices', [
            'title' => 'Fake holiday', 'content' => 'No college tomorrow', 'target_audience' => 'all', 'priority' => 'urgent',
        ]));
        $this->assertDatabaseMissing('notices', ['title' => 'Fake holiday']);

        $this->assertBlocked($this->actingAs($student)->post('/materials', [
            'subject_id' => Subject::first()->id, 'title' => 'Fake notes', 'type' => 'notes',
        ]));
        $this->assertDatabaseMissing('study_materials', ['title' => 'Fake notes']);
    }

    public function test_student_cannot_create_or_delete_departments(): void
    {
        $student = $this->user('student');

        $this->assertBlocked($this->actingAs($student)->post('/academics/departments', ['name' => 'Hacked Dept', 'code' => 'HCK']));
        $this->assertDatabaseMissing('departments', ['name' => 'Hacked Dept']);
    }

    public function test_student_cannot_launch_marketing_campaign(): void
    {
        $this->assertBlocked($this->actingAs($this->user('student'))
            ->post('/marketing/campaign', ['name' => 'Spam', 'template_id' => 1, 'channel' => 'WhatsApp']));

        $this->assertDatabaseMissing('marketing_campaigns', ['name' => 'Spam']);
    }

    public function test_staff_cannot_approve_leave(): void
    {
        $staff = $this->user('staff');
        $leave = LeaveRequest::create([
            'user_id' => $staff->id,
            'leave_type' => 'Casual',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'reason' => 'Test',
        ]);

        $this->assertBlocked($this->actingAs($staff)->put("/leaves/{$leave->id}/status", ['status' => 'Approved']));
        $this->assertNotSame('Approved', $leave->fresh()->status);
    }

    public function test_leave_status_must_be_a_known_value(): void
    {
        $leave = LeaveRequest::create([
            'user_id' => $this->user('staff')->id,
            'leave_type' => 'Casual',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'reason' => 'Test',
        ]);

        $this->actingAs($this->user('management'))
            ->put("/leaves/{$leave->id}/status", ['status' => '<script>alert(1)</script>'])
            ->assertSessionHasErrors('status');
    }

    public function test_student_can_only_see_their_own_marksheet(): void
    {
        $students = User::where('role', 'student')->whereHas('studentProfile')->take(2)->get();
        [$me, $other] = [$students[0], $students[1]];

        $this->actingAs($me)->get("/exams/marksheet/{$me->studentProfile->id}")->assertOk();
        $this->actingAs($me)->get("/exams/marksheet/{$other->studentProfile->id}")->assertForbidden();
    }
}
