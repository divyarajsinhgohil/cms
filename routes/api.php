<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AcademicController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\MarketingController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\FeeController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\TimetableController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\NoticeController;
use App\Http\Controllers\Api\HostelController;
use App\Http\Controllers\Api\GrievanceController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\CertificateController;

// Public
Route::post('/login', [AuthController::class, 'login']);

// Authenticated (Sanctum token) routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Academic Master Data (read: everyone signed-in, write: management)
    Route::get('/departments', [AcademicController::class, 'departmentsIndex']);
    Route::post('/departments', [AcademicController::class, 'storeDepartment'])->middleware('role:management');
    Route::put('/departments/{department}', [AcademicController::class, 'updateDepartment'])->middleware('role:management');
    Route::delete('/departments/{department}', [AcademicController::class, 'destroyDepartment'])->middleware('role:management');

    Route::get('/courses', [AcademicController::class, 'coursesIndex']);
    Route::post('/courses', [AcademicController::class, 'storeCourse'])->middleware('role:management');
    Route::put('/courses/{course}', [AcademicController::class, 'updateCourse'])->middleware('role:management');
    Route::delete('/courses/{course}', [AcademicController::class, 'destroyCourse'])->middleware('role:management');

    Route::get('/subjects', [AcademicController::class, 'subjectsIndex']);
    Route::post('/subjects', [AcademicController::class, 'storeSubject'])->middleware('role:management');
    Route::put('/subjects/{subject}', [AcademicController::class, 'updateSubject'])->middleware('role:management');
    Route::delete('/subjects/{subject}', [AcademicController::class, 'destroySubject'])->middleware('role:management');

    Route::get('/staff', [StaffController::class, 'index'])->middleware('role:management,staff');
    Route::post('/staff', [StaffController::class, 'store'])->middleware('role:management');
    Route::put('/staff/{staff}', [StaffController::class, 'update'])->middleware('role:management');
    Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->middleware('role:management');

    // Marketing (management only)
    Route::middleware('role:management')->group(function () {
        Route::get('/marketing/templates', [MarketingController::class, 'templates']);
        Route::post('/marketing/templates', [MarketingController::class, 'storeTemplate']);
        Route::get('/marketing/campaigns', [MarketingController::class, 'campaigns']);
        Route::post('/marketing/campaigns', [MarketingController::class, 'launchCampaign']);
        Route::get('/marketing/logs', [MarketingController::class, 'logs']);
    });

    // Student Directory & Admissions
    Route::get('/students', [StudentController::class, 'index'])->middleware('role:management,staff');
    Route::get('/students/{student}', [StudentController::class, 'show'])->middleware('role:management,staff');
    Route::post('/students', [StudentController::class, 'store'])->middleware('role:management');

    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'index'])->middleware('role:management,staff');
    Route::post('/attendance', [AttendanceController::class, 'store'])->middleware('role:management,staff');
    Route::get('/attendance/low', [AttendanceController::class, 'lowAttendance'])->middleware('role:management,staff');
    Route::get('/attendance/me', [AttendanceController::class, 'myAttendance'])->middleware('role:student');

    // Fees & Receipts
    Route::get('/fees/structures', [FeeController::class, 'structures']);
    Route::post('/fees/structures', [FeeController::class, 'storeStructure'])->middleware('role:management');
    Route::get('/fees/payments', [FeeController::class, 'payments'])->middleware('role:management,staff');
    Route::post('/fees/payments', [FeeController::class, 'collectPayment'])->middleware('role:management');
    Route::get('/fees/receipts/{payment}', [FeeController::class, 'showReceipt'])->middleware('role:management,staff');
    Route::get('/fees/receipts/{payment}/pdf', [FeeController::class, 'downloadReceiptPdf'])->middleware('role:management,staff');
    Route::get('/fees/me', [FeeController::class, 'myFees'])->middleware('role:student');

    // Exams & CGPA
    Route::get('/exams/marks', [ExamController::class, 'index'])->middleware('role:management,staff');
    Route::post('/exams/marks', [ExamController::class, 'storeMarks'])->middleware('role:management,staff');
    Route::get('/exams/marksheet/{student}', [ExamController::class, 'marksheet'])->middleware('role:management,staff');
    Route::get('/exams/marksheet/{student}/pdf', [ExamController::class, 'downloadMarksheetPdf'])->middleware('role:management,staff');
    Route::get('/exams/me', [ExamController::class, 'myMarks'])->middleware('role:student');

    // Timetables
    Route::get('/timetables', [TimetableController::class, 'index']);
    Route::post('/timetables', [TimetableController::class, 'store'])->middleware('role:management,staff');
    Route::post('/timetables/auto-generate', [TimetableController::class, 'autoGenerate'])->middleware('role:management');

    // Materials & Assignments
    Route::get('/materials', [MaterialController::class, 'index']);
    Route::post('/materials', [MaterialController::class, 'store'])->middleware('role:management,staff');
    Route::post('/materials/{material}/submit', [MaterialController::class, 'submitAssignment'])->middleware('role:student');
    Route::post('/materials/submissions/{submission}/grade', [MaterialController::class, 'gradeSubmission'])->middleware('role:management,staff');

    // Notices
    Route::get('/notices', [NoticeController::class, 'index']);
    Route::post('/notices', [NoticeController::class, 'store'])->middleware('role:management,staff');

    // Hostel Facilities & Outpasses
    Route::get('/hostel/rooms', [HostelController::class, 'rooms']);
    Route::post('/hostel/rooms', [HostelController::class, 'storeRoom'])->middleware('role:management');
    Route::get('/hostel/allotments', [HostelController::class, 'allotments'])->middleware('role:management,staff');
    Route::post('/hostel/allotments', [HostelController::class, 'allotRoom'])->middleware('role:management');
    Route::get('/hostel/outpasses', [HostelController::class, 'outpasses']);
    Route::post('/hostel/outpasses', [HostelController::class, 'storeOutpass'])->middleware('role:management,staff');

    // Complaints & Grievances
    Route::get('/complaints', [GrievanceController::class, 'index']);
    Route::post('/complaints', [GrievanceController::class, 'store']);
    Route::put('/complaints/{complaint}/status', [GrievanceController::class, 'updateStatus'])->middleware('role:management');

    // Leave Management
    Route::get('/leaves', [LeaveController::class, 'index']);
    Route::post('/leaves', [LeaveController::class, 'store']);
    Route::put('/leaves/{leave}/status', [LeaveController::class, 'updateStatus'])->middleware('role:management');

    // Printable Certificates & ID Cards
    Route::get('/certificates', [CertificateController::class, 'index'])->middleware('role:management,staff');
    Route::get('/certificates/id-card/{student}', [CertificateController::class, 'idCard']);
    Route::get('/certificates/id-card/{student}/pdf', [CertificateController::class, 'idCardPdf']);
    Route::get('/certificates/bonafide/{student}', [CertificateController::class, 'bonafide']);
    Route::get('/certificates/bonafide/{student}/pdf', [CertificateController::class, 'bonafidePdf']);
});
