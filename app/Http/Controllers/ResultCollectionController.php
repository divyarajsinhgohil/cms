<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\ResultCollection;
use App\Models\Section;
use App\Models\Student;
use App\Services\AlertDispatchService;

class ResultCollectionController extends Controller
{
    public function __construct(private AlertDispatchService $alerts) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $courses = Course::all();
        $streamScoped = false;

        if ($user->isStaff() && $user->staffProfile) {
            $assignedCourses = $user->staffProfile->courses;
            if ($assignedCourses->isNotEmpty()) {
                $streamScoped = true;
                $courses = $assignedCourses;
            }
        }

        $selectedCourseId = $request->get('course_id', optional($courses->first())->id);
        if ($streamScoped && ! $courses->contains('id', (int) $selectedCourseId)) {
            $selectedCourseId = optional($courses->first())->id;
        }

        $semester = (int) $request->get('semester', 1);
        $selectedSectionId = $request->get('section_id');
        $search = trim((string) $request->get('search', ''));

        $sections = $selectedCourseId
            ? Section::where('course_id', $selectedCourseId)->where('semester', $semester)->orderBy('name')->get()
            : collect();

        $students = $this->scopedStudents($selectedCourseId, $semester, $selectedSectionId, $search);

        $collections = ResultCollection::where('semester', $semester)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        $roster = $students->map(fn ($student) => [
            'student' => $student,
            'collection' => $collections->get($student->id),
        ]);

        $collectedCount = $collections->filter->collected->count();
        $totalCount = $roster->count();
        $pendingList = $roster->reject(fn ($row) => optional($row['collection'])->collected);

        return view('exams.collection', compact(
            'courses', 'sections', 'roster', 'pendingList',
            'selectedCourseId', 'semester', 'selectedSectionId', 'search',
            'streamScoped', 'collectedCount', 'totalCount'
        ));
    }

    public function markBulk(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer',
        ]);

        $this->guardStream((int) $request->course_id);

        $students = $this->scopedStudents(
            (int) $request->course_id,
            (int) $request->semester,
            $request->get('section_id'),
            trim((string) $request->get('search', ''))
        );

        $checked = collect($request->input('collected', []))->keys()->map(fn ($id) => (int) $id);

        foreach ($students as $student) {
            $isCollected = $checked->contains($student->id);

            ResultCollection::updateOrCreate(
                ['student_id' => $student->id, 'semester' => $request->semester],
                [
                    'collected' => $isCollected,
                    'collected_at' => $isCollected ? now() : null,
                    'collected_by' => $isCollected ? Auth::id() : null,
                ]
            );
        }

        return redirect()->back()->with('success', "Result collection status updated for {$students->count()} students.");
    }

    public function remind(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'semester' => 'required|integer',
        ]);

        $students = Student::whereIn('id', $request->student_ids)->with('user')->get();

        if ($students->isNotEmpty()) {
            $this->guardStream($students->first()->course_id);
        }

        foreach ($students as $student) {
            $this->alerts->notifyResultCollectionPending($student, (int) $request->semester);
        }

        return redirect()->back()->with('success', "Reminder queued for {$students->count()} students who haven't collected their result yet.");
    }

    /**
     * The active class roster for a course+semester(+section), narrowed by search —
     * shared by index() and markBulk() so a bulk save only ever touches the students
     * actually visible in the current filtered view, never the whole class.
     */
    private function scopedStudents(?int $courseId, int $semester, ?string $sectionId, string $search)
    {
        if (! $courseId) {
            return collect();
        }

        $query = Student::where('course_id', $courseId)
            ->where('current_semester', $semester)
            ->where('status', 'Active')
            ->with('user');

        if ($sectionId === 'unassigned') {
            $query->whereNull('section_id');
        } elseif ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('enrollment_no', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        return $query->orderByRaw('roll_no IS NULL')->orderBy('roll_no')->get();
    }

    /**
     * A stream-scoped faculty member can only manage collection status for their
     * own assigned stream(s), mirroring the guard already used for attendance.
     */
    private function guardStream(int $courseId): void
    {
        $user = Auth::user();
        if ($user->isStaff() && $user->staffProfile) {
            $assignedCourseIds = $user->staffProfile->courses->pluck('id');
            if ($assignedCourseIds->isNotEmpty() && ! $assignedCourseIds->contains($courseId)) {
                abort(403, 'You are not assigned to this stream and cannot manage its result collection.');
            }
        }
    }
}
