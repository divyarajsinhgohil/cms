<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Section;
use App\Models\Student;
use App\Models\StaffProfile;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::all();
        $courseId = $request->get('course_id');
        $semester = $request->get('semester');

        $sections = collect();
        $unassignedCount = 0;
        $totalActiveCount = 0;
        $eligibleStaff = collect();

        $unassignedStudents = collect();

        if ($courseId && $semester) {
            $sections = Section::where('course_id', $courseId)
                ->where('semester', $semester)
                ->withCount(['students' => fn ($q) => $q->where('status', 'Active')])
                ->with([
                    'students' => fn ($q) => $q->where('status', 'Active')->with('user')->orderBy('roll_no'),
                    'classTeacher.user',
                ])
                ->orderBy('name')
                ->get();

            $totalActiveCount = Student::where('course_id', $courseId)
                ->where('current_semester', $semester)
                ->where('status', 'Active')
                ->count();

            $unassignedStudents = Student::where('course_id', $courseId)
                ->where('current_semester', $semester)
                ->where('status', 'Active')
                ->whereNull('section_id')
                ->with('user')
                ->orderBy('roll_no')
                ->get();
            $unassignedCount = $unassignedStudents->count();

            // Class teacher candidates: prefer staff already assigned to this
            // stream, but fall back to the full faculty list if none are.
            $course = Course::find($courseId);
            $eligibleStaff = $course ? $course->assignedStaff()->with('user')->get() : collect();
            if ($eligibleStaff->isEmpty()) {
                $eligibleStaff = StaffProfile::with('user')->get();
            }
        }

        return view('sections.index', compact('courses', 'sections', 'courseId', 'semester', 'unassignedCount', 'totalActiveCount', 'unassignedStudents', 'eligibleStaff'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer|min:1|max:8',
            'name' => 'required|string|max:5',
            'capacity' => 'required|integer|min:1|max:500',
            'class_teacher_id' => 'nullable|exists:staff_profiles,id',
        ]);

        Section::create($validated);

        return redirect()->back()->with('success', "Class Section '{$validated['name']}' created successfully.");
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'capacity' => 'required|integer|min:1|max:500',
            'class_teacher_id' => 'nullable|exists:staff_profiles,id',
        ]);

        $section->update($validated);

        return redirect()->back()->with('success', "Section '{$section->name}' updated successfully.");
    }

    public function destroy(Section $section)
    {
        $name = $section->name;

        // Students in a deleted section become unassigned rather than deleted.
        Student::where('section_id', $section->id)->update(['section_id' => null]);
        $section->delete();

        return redirect()->back()->with('success', "Section '{$name}' removed. Its students are now unassigned.");
    }

    /**
     * Auto-distribute every Active student in a stream+semester across its
     * existing class sections, alphabetically by name, filling each section
     * up to its capacity before moving to the next. Any students beyond the
     * combined capacity overflow into the last section.
     */
    public function divideEqually(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer|min:1|max:8',
        ]);

        $sections = Section::where('course_id', $validated['course_id'])
            ->where('semester', $validated['semester'])
            ->orderBy('name')
            ->get();

        if ($sections->isEmpty()) {
            return redirect()->back()->with('error', 'Create at least one class section with a capacity before dividing students.');
        }

        $students = Student::where('course_id', $validated['course_id'])
            ->where('current_semester', $validated['semester'])
            ->where('status', 'Active')
            ->with('user')
            ->get()
            ->sortBy(fn ($s) => strtolower(optional($s->user)->name ?? ''))
            ->values();

        if ($students->isEmpty()) {
            return redirect()->back()->with('error', 'No active students found for this stream and semester.');
        }

        $cursor = 0;
        $summary = [];
        foreach ($sections as $section) {
            $slice = $students->slice($cursor, $section->capacity);
            foreach ($slice as $student) {
                $student->update(['section_id' => $section->id]);
            }
            $summary[$section->id] = $slice->count();
            $cursor += $slice->count();
        }

        // Overflow beyond combined capacity all lands in the last section.
        if ($cursor < $students->count()) {
            $lastSection = $sections->last();
            $overflow = $students->slice($cursor);
            foreach ($overflow as $student) {
                $student->update(['section_id' => $lastSection->id]);
            }
            $summary[$lastSection->id] += $overflow->count();
        }

        Student::regenerateRollNumbers($validated['course_id'], $validated['semester']);

        $parts = $sections->map(fn ($s) => "{$summary[$s->id]} in {$s->name}")->implode(', ');

        return redirect()->back()->with('success', "Students divided equally: {$parts}.");
    }
}
