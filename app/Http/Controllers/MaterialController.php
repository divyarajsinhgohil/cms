<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StudyMaterial;
use App\Models\AssignmentSubmission;
use App\Models\Subject;
use App\Models\Student;

class MaterialController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $streamScoped = false;
        $ownOnly = false;

        $materialsQuery = StudyMaterial::with(['subject.course', 'uploader', 'submissions.student.user']);
        $subjectsQuery = Subject::with('course');

        if ($user->isStudent()) {
            // A student only sees materials for their own stream
            $ownOnly = true;
            $courseId = optional($user->studentProfile)->course_id;
            $materialsQuery->whereHas('subject', fn ($q) => $q->where('course_id', $courseId));
            $subjectsQuery->where('course_id', $courseId);
        } elseif ($user->isStaff() && $user->staffProfile) {
            $assignedCourseIds = $user->staffProfile->courses->pluck('id')->all();
            if (count($assignedCourseIds) > 0) {
                $streamScoped = true;
                $materialsQuery->whereHas('subject', fn ($q) => $q->whereIn('course_id', $assignedCourseIds));
                $subjectsQuery->whereIn('course_id', $assignedCourseIds);
            }
        }

        $materials = $materialsQuery->latest()->get();
        $subjects = $subjectsQuery->orderBy('name')->get();

        return view('materials.index', compact('materials', 'subjects', 'streamScoped', 'ownOnly'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:notes,assignment,syllabus',
            'due_date' => 'nullable|date',
            'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png', // max 10MB
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('study_materials', 'public');
        }

        StudyMaterial::create([
            'subject_id' => $request->subject_id,
            'uploaded_by' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'file_path' => $filePath,
            'due_date' => $request->due_date,
        ]);

        return redirect()->back()->with('success', 'Study material / assignment published successfully!');
    }

    public function submitAssignment(Request $request, StudyMaterial $material)
    {
        $user = Auth::user();
        $student = $user->studentProfile;

        if (!$student) {
            return redirect()->back()->with('error', 'Only registered students can submit assignments.');
        }

        // One-time submission only — once turned in, a student cannot silently
        // overwrite it. Faculty review/grade what was actually submitted.
        $alreadySubmitted = AssignmentSubmission::where('study_material_id', $material->id)
            ->where('student_id', $student->id)
            ->exists();

        if ($alreadySubmitted) {
            return redirect()->back()->with('error', 'You have already submitted this assignment. Contact your faculty if you need to resubmit.');
        }

        $request->validate([
            'comments' => 'nullable|string',
            'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('assignment_submissions', 'public');
        }

        AssignmentSubmission::create([
            'study_material_id' => $material->id,
            'student_id' => $student->id,
            'file_path' => $filePath,
            'comments' => $request->comments,
            'status' => 'submitted',
        ]);

        return redirect()->back()->with('success', 'Assignment submitted successfully! You will not be able to resubmit — your faculty will review and grade it.');
    }

    public function gradeSubmission(Request $request, AssignmentSubmission $submission)
    {
        $request->validate([
            'marks_obtained' => 'required|numeric|min:0|max:100',
        ]);

        $submission->update([
            'marks_obtained' => $request->marks_obtained,
            'status' => 'graded',
        ]);

        return redirect()->back()->with('success', 'Submission approved and marks recorded successfully!');
    }
}
