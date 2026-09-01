<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\StudyMaterial;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = StudyMaterial::with(['subject', 'uploader', 'submissions.student.user']);

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:notes,assignment,syllabus',
            'due_date' => 'nullable|date',
            'file' => 'nullable|file|max:10240',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('study_materials', 'public');
        }

        $material = StudyMaterial::create([
            'subject_id' => $request->subject_id,
            'uploaded_by' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'file_path' => $filePath,
            'due_date' => $request->due_date,
        ]);

        return response()->json(['message' => 'Study material / assignment published successfully!', 'data' => $material], 201);
    }

    public function submitAssignment(Request $request, StudyMaterial $material)
    {
        $student = $request->user()->studentProfile;

        if (! $student) {
            return response()->json(['message' => 'Only registered students can submit assignments.'], 403);
        }

        $alreadySubmitted = AssignmentSubmission::where('study_material_id', $material->id)
            ->where('student_id', $student->id)
            ->exists();

        if ($alreadySubmitted) {
            return response()->json(['message' => 'You have already submitted this assignment. Contact your faculty if you need to resubmit.'], 422);
        }

        $request->validate([
            'comments' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('assignment_submissions', 'public');
        }

        $submission = AssignmentSubmission::create([
            'study_material_id' => $material->id,
            'student_id' => $student->id,
            'file_path' => $filePath,
            'comments' => $request->comments,
            'status' => 'submitted',
        ]);

        return response()->json(['message' => 'Assignment submitted successfully!', 'data' => $submission], 201);
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

        return response()->json(['message' => 'Assignment graded successfully!', 'data' => $submission]);
    }
}
