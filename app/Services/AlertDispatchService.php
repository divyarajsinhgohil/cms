<?php

namespace App\Services;

use App\Jobs\ProcessCmsAlertJob;
use App\Models\CmsAlert;
use App\Models\Notice;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;

class AlertDispatchService
{
    public function queueForUser(User $user, string $type, string $title, string $body, array $metadata = []): ?CmsAlert
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('cms_alerts')) {
            return null;
        }

        $alert = CmsAlert::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'metadata' => $metadata,
            'status' => 'pending',
            'queued_at' => now(),
        ]);

        ProcessCmsAlertJob::dispatch($alert->id);

        return $alert;
    }

    /** @param  Collection<int, User>|array<User>  $users */
    public function queueForUsers(Collection|array $users, string $type, string $title, string $body, array $metadata = []): int
    {
        $count = 0;
        foreach ($users as $user) {
            $this->queueForUser($user, $type, $title, $body, $metadata);
            $count++;
        }

        return $count;
    }

    public function notifyAttendanceMarked(Student $student, string $subjectName, string $date, string $status): void
    {
        $user = $student->user;
        if (! $user) {
            return;
        }

        $this->queueForUser(
            $user,
            'attendance',
            'Attendance Updated',
            "Your attendance for {$subjectName} on {$date} was marked as {$status}.",
            compact('subjectName', 'date', 'status')
        );
    }

    public function notifyFeePayment(Student $student, float $amount, string $receiptNo): void
    {
        $user = $student->user;
        if (! $user) {
            return;
        }

        $this->queueForUser(
            $user,
            'fee',
            'Fee Payment Recorded',
            "A payment of ₹".number_format($amount, 2)." was recorded. Receipt: {$receiptNo}.",
            ['amount' => $amount, 'receipt_no' => $receiptNo]
        );
    }

    public function notifyExamResult(Student $student, string $subjectName, int $total, string $grade): void
    {
        $user = $student->user;
        if (! $user) {
            return;
        }

        $this->queueForUser(
            $user,
            'result',
            'Exam Result Published',
            "Your result for {$subjectName}: {$total}/100 (Grade {$grade}).",
            compact('subjectName', 'total', 'grade')
        );
    }

    public function notifyResultCollectionPending(Student $student, int $semester): void
    {
        $user = $student->user;
        if (! $user) {
            return;
        }

        $this->queueForUser(
            $user,
            'result_collection',
            'Result Hard Copy Pending Collection',
            "Your Semester {$semester} result hard copy has not been collected yet. Please collect it from the office at the earliest.",
            compact('semester')
        );
    }

    public function notifyNoticePosted(Notice $notice): int
    {
        $users = $this->resolveNoticeAudience($notice);
        $priority = $notice->priority === 'urgent' ? 'URGENT: ' : '';

        return $this->queueForUsers(
            $users,
            'notice',
            $priority.$notice->title,
            $notice->content,
            ['notice_id' => $notice->id, 'priority' => $notice->priority]
        );
    }

    /** @return Collection<int, User> */
    private function resolveNoticeAudience(Notice $notice): Collection
    {
        $query = User::query();

        if ($notice->target_audience === 'staff') {
            return $query->where('role', 'staff')->get();
        }

        if ($notice->target_audience === 'students') {
            $studentQuery = Student::where('status', 'Active');
            if ($notice->course_id) {
                $studentQuery->where('course_id', $notice->course_id);
            }
            if ($notice->semester) {
                $studentQuery->where('current_semester', $notice->semester);
            }

            return User::whereIn('id', $studentQuery->pluck('user_id'))->get();
        }

        return $query->whereIn('role', ['student', 'staff'])->get();
    }
}
