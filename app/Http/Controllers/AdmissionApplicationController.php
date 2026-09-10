<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\AdmissionApplication;
use App\Models\Course;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use App\Support\UniqueCode;

class AdmissionApplicationController extends Controller
{
    /**
     * Public — anyone can view the application form, no login required.
     */
    public function create()
    {
        $courses = Course::with('department')->orderBy('name')->get();

        return view('admission_applications.apply', compact('courses'));
    }

    /**
     * Public — submit the application. Goes into the Pending queue for admin review.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'surname' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'father_name' => 'required|string|max:150',
            'mother_name' => 'required|string|max:150',
            'gender' => 'required|in:Male,Female,Other',
            'aadhar_number' => 'required|digits:12',
            'photo' => 'required|image|max:10240',
            'aadhar_photo' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'category' => 'required|in:General,SC,ST,OBC',
            'caste_certificate' => 'required_if:category,SC,ST,OBC|nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'mobile' => 'required|string|max:20',
            'father_mobile' => 'nullable|string|max:20',
            'mother_mobile' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'home_address' => 'required|string',
            'permanent_address' => 'required|string',
            'wants_hostel' => 'nullable|boolean',
            'hostel_preference' => 'nullable|string|max:255',
            'course_id' => 'required|exists:courses,id',
        ]);

        $validated['photo_path'] = $request->file('photo')->store('admission_applications/photos', 'public');
        $validated['aadhar_photo_path'] = $request->file('aadhar_photo')->store('admission_applications/aadhar', 'public');

        if ($request->hasFile('caste_certificate')) {
            $validated['caste_certificate_path'] = $request->file('caste_certificate')->store('admission_applications/caste_certificates', 'public');
        }

        $validated['wants_hostel'] = $request->boolean('wants_hostel');
        $validated['status'] = 'Pending';

        AdmissionApplication::create($validated);

        return redirect()->route('admission-applications.create')->with('success', 'Your admission application has been submitted successfully! You will be notified once the college reviews it.');
    }

    /**
     * Admin — review queue.
     */
    public function index(Request $request)
    {
        $query = AdmissionApplication::with(['course.department', 'reviewer', 'student']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->orderByRaw("CASE WHEN status = 'Pending' THEN 0 ELSE 1 END");
        }

        $applications = $query->latest()->paginate(15)->withQueryString();

        return view('admission_applications.index', compact('applications'));
    }

    /**
     * Admin — approve: creates the real User + Student account in the requested stream.
     */
    public function approve(AdmissionApplication $application)
    {
        if ($application->status !== 'Pending') {
            return redirect()->back()->with('error', 'This application has already been reviewed.');
        }

        $course = Course::findOrFail($application->course_id);
        $department = Department::findOrFail($course->department_id);

        $user = User::create([
            'name' => $application->fullName(),
            'email' => $application->email,
            'password' => Hash::make('password'),
            'role' => 'student',
            'phone' => $application->mobile,
            'avatar' => $application->photo_path ? Storage::url($application->photo_path) : null,
            'must_change_password' => true,
        ]);

        $batchYear = now()->year;

        $student = UniqueCode::create(fn () => Student::create([
            'user_id' => $user->id,
            'enrollment_no' => sprintf(
                'CMS-%d-%s-%05d',
                $batchYear,
                strtoupper($department->code),
                UniqueCode::nextSequence(
                    Student::where('department_id', $department->id)->orderByDesc('id')->value('enrollment_no')
                )
            ),
            'department_id' => $department->id,
            'course_id' => $course->id,
            'batch_year' => $batchYear,
            'current_semester' => 1,
            'gender' => $application->gender,
            'guardian_name' => $application->father_name,
            'guardian_phone' => $application->father_mobile,
            'address' => $application->permanent_address,
        ]));

        $application->update([
            'status' => 'Approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'student_id' => $student->id,
        ]);

        // Automated WhatsApp Admission Approval Alert
        $settings = \App\Models\SiteSetting::current();
        if ($settings->whatsapp_auto_admission_approval && $application->mobile) {
            app(\App\Services\WhatsAppService::class)->sendDirectMessage(
                $application->mobile,
                "Congratulations {$application->fullName()}! Your admission application for {$course->name} has been APPROVED! Enrollment No: {$student->enrollment_no}. Welcome to our College!",
                $application->fullName(),
                'admission_approval'
            );
        }

        return redirect()->back()->with('success', "Application approved! {$application->fullName()} admitted to {$course->name} with Enrollment No: {$student->enrollment_no}");
    }

    /**
     * Admin — reject with a reason, no account created.
     */
    public function reject(Request $request, AdmissionApplication $application)
    {
        if ($application->status !== 'Pending') {
            return redirect()->back()->with('error', 'This application has already been reviewed.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $application->update([
            'status' => 'Rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->back()->with('success', "Application from {$application->fullName()} has been rejected.");
    }
}
