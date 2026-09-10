<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\AcademicController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\HostelController;
use App\Http\Controllers\GrievanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AdmissionApplicationController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\StudentImportController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\ResultCollectionController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\WhatsAppController;

// Public & Auth Routes
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/home', [PublicController::class, 'home'])->name('public.home');
Route::get('/locale/{locale}', [PublicController::class, 'setLocale'])->name('locale.switch');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/login/choose', [AuthController::class, 'showChooseAccount'])->name('login.choose');
Route::post('/login/choose/{user}', [AuthController::class, 'chooseAccount'])->name('login.choose.select');
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quick.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Admission Application Form (no login required)
Route::get('/apply', [AdmissionApplicationController::class, 'create'])->name('admission-applications.create');
Route::post('/apply', [AdmissionApplicationController::class, 'store'])->name('admission-applications.store');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change.show');
    Route::post('/change-password', [AuthController::class, 'updatePassword'])->name('password.change.update');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Academic Master & Management CRUD
    Route::get('/academics/departments', [AcademicController::class, 'departmentsIndex'])->name('academics.departments');
    Route::post('/academics/departments', [AcademicController::class, 'storeDepartment'])->name('academics.departments.store');
    Route::put('/academics/departments/{department}', [AcademicController::class, 'updateDepartment'])->name('academics.departments.update');
    Route::delete('/academics/departments/{department}', [AcademicController::class, 'destroyDepartment'])->name('academics.departments.destroy');
    Route::post('/academics/departments/bulk-destroy', [AcademicController::class, 'destroyDepartmentsBulk'])->name('academics.departments.bulk_destroy');

    Route::get('/academics/courses', [AcademicController::class, 'coursesIndex'])->name('academics.courses');
    Route::post('/academics/courses', [AcademicController::class, 'storeCourse'])->name('academics.courses.store');
    Route::put('/academics/courses/{course}', [AcademicController::class, 'updateCourse'])->name('academics.courses.update');
    Route::delete('/academics/courses/{course}', [AcademicController::class, 'destroyCourse'])->name('academics.courses.destroy');

    Route::get('/academics/subjects', [AcademicController::class, 'subjectsIndex'])->name('academics.subjects');
    Route::post('/academics/subjects', [AcademicController::class, 'storeSubject'])->name('academics.subjects.store');
    Route::put('/academics/subjects/{subject}', [AcademicController::class, 'updateSubject'])->name('academics.subjects.update');
    Route::delete('/academics/subjects/{subject}', [AcademicController::class, 'destroySubject'])->name('academics.subjects.destroy');

    // Semester-Wise Syllabus & Faculty Portal
    Route::get('/academics/my-syllabus', [AcademicController::class, 'mySyllabus'])->name('academics.my_syllabus');
    Route::get('/academics/syllabi', [AcademicController::class, 'syllabiIndex'])->name('academics.syllabi');
    Route::post('/academics/syllabi', [AcademicController::class, 'storeSyllabus'])->name('academics.syllabi.store');

    Route::get('/academics/staff', [StaffController::class, 'index'])->name('academics.staff');
    Route::post('/academics/staff', [StaffController::class, 'store'])->name('academics.staff.store');
    Route::put('/academics/staff/{staff}', [StaffController::class, 'update'])->name('academics.staff.update');
    Route::delete('/academics/staff/{staff}', [StaffController::class, 'destroy'])->name('academics.staff.destroy');

    // Online Admission Application Review (management only)
    Route::middleware('role:management')->group(function () {
        Route::get('/admission-applications', [AdmissionApplicationController::class, 'index'])->name('admission-applications.index');
        Route::post('/admission-applications/{application}/approve', [AdmissionApplicationController::class, 'approve'])->name('admission-applications.approve');
        Route::post('/admission-applications/{application}/reject', [AdmissionApplicationController::class, 'reject'])->name('admission-applications.reject');
    });

    // Marketing (WhatsApp / SMS outreach to students)
    Route::get('/marketing', [MarketingController::class, 'index'])->name('marketing.index');
    Route::post('/marketing/template', [MarketingController::class, 'storeTemplate'])->name('marketing.template');
    Route::post('/marketing/campaign', [MarketingController::class, 'launchCampaign'])->name('marketing.campaign');

    // Student Directory & Admissions (management & teachers)
    Route::middleware('role:management,staff')->group(function () {
        Route::get('/admissions/export', [StudentImportController::class, 'export'])->name('admissions.export');
        Route::get('/admissions/import', [StudentImportController::class, 'index'])->name('admissions.import');
        Route::get('/admissions/import/template', [StudentImportController::class, 'template'])->name('admissions.import.template');
        Route::post('/admissions/import/preview', [StudentImportController::class, 'preview'])->name('admissions.import.preview');
        Route::post('/admissions/import/run', [StudentImportController::class, 'import'])->name('admissions.import.run');
    });
    Route::middleware('role:management,staff')->group(function () {
        Route::get('/admissions', [AdmissionController::class, 'index'])->name('admissions.index');
        Route::get('/admissions/{student}', [AdmissionController::class, 'show'])->name('admissions.show');
    });
    Route::middleware('role:management')->group(function () {
        Route::post('/admissions', [AdmissionController::class, 'store'])->name('admissions.store');
        Route::post('/admissions/{student}/mark-left', [AdmissionController::class, 'markLeft'])->name('admissions.mark_left');
        Route::post('/admissions/promote-semester', [AdmissionController::class, 'promoteSemester'])->name('admissions.promote_semester');
    });

    // Stream Sections (Class A / B) & even-split assignment (management only)
    Route::middleware('role:management')->group(function () {
        Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
        Route::post('/sections', [SectionController::class, 'store'])->name('sections.store');
        Route::put('/sections/{section}', [SectionController::class, 'update'])->name('sections.update');
        Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');
        Route::post('/sections/divide-equally', [SectionController::class, 'divideEqually'])->name('sections.divide_equally');
    });

    // Hostel Facilities & Outpasses
    Route::get('/hostels', [HostelController::class, 'index'])->name('hostels.index');
    Route::post('/hostels/room', [HostelController::class, 'storeRoom'])->name('hostels.room.store');
    Route::post('/hostels/allot', [HostelController::class, 'allotRoom'])->name('hostels.allot');
    Route::post('/hostels/outpass', [HostelController::class, 'storeOutpass'])->name('hostels.outpass.store');

    // Complaints & Grievances
    Route::get('/complaints', [GrievanceController::class, 'index'])->name('complaints.index');
    Route::post('/complaints', [GrievanceController::class, 'store'])->name('complaints.store');
    Route::put('/complaints/{complaint}/status', [GrievanceController::class, 'updateStatus'])->name('complaints.status');

    // Leave Management (faculty applying to admin — not a student-facing feature)
    Route::middleware('role:management,staff')->group(function () {
        Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
        Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
        Route::put('/leaves/{leave}/status', [LeaveController::class, 'updateStatus'])->name('leaves.status');
    });

    // Printable Certificates & ID Cards
    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/id-card/{student}', [CertificateController::class, 'idCard'])->name('certificates.id_card');
    Route::get('/certificates/id-card/{student}/pdf', [CertificateController::class, 'idCardPdf'])->name('certificates.id_card.pdf');
    Route::get('/certificates/bonafide/{student}', [CertificateController::class, 'bonafide'])->name('certificates.bonafide');
    Route::get('/certificates/bonafide/{student}/pdf', [CertificateController::class, 'bonafidePdf'])->name('certificates.bonafide.pdf');

    // Attendance (management/staff only — students see their own attendance on their dashboard)
    Route::middleware('role:management,staff')->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/pdf', [AttendanceController::class, 'downloadPdf'])->name('attendance.pdf');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    });

    // Fees & Receipts (management only)
    Route::middleware('role:management')->group(function () {
        Route::get('/fees', [FeeController::class, 'index'])->name('fees.index');
        Route::post('/fees/structure', [FeeController::class, 'storeStructure'])->name('fees.structure');
        Route::post('/fees/pay', [FeeController::class, 'collectPayment'])->name('fees.pay');
        Route::get('/fees/receipt/{payment}', [FeeController::class, 'viewReceipt'])->name('fees.receipt');
        Route::get('/fees/receipt/{payment}/pdf', [FeeController::class, 'downloadReceiptPdf'])->name('fees.receipt.pdf');
    });

    // Exams & CGPA
    Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
    Route::post('/exams/store', [ExamController::class, 'storeMarks'])->name('exams.store');
    Route::get('/exams/marksheet/{student}', [ExamController::class, 'viewMarksheet'])->name('exams.marksheet');
    Route::get('/exams/marksheet/{student}/pdf', [ExamController::class, 'downloadMarksheetPdf'])->name('exams.marksheet.pdf');

    // Result Hard-Copy Collection Register
    Route::middleware('role:management,staff')->group(function () {
        Route::get('/exams/collection', [ResultCollectionController::class, 'index'])->name('exams.collection.index');
        Route::post('/exams/collection/mark', [ResultCollectionController::class, 'markBulk'])->name('exams.collection.mark');
        Route::post('/exams/collection/remind', [ResultCollectionController::class, 'remind'])->name('exams.collection.remind');
    });

    // Timetables (viewing is open to everyone; creating/generating slots is management only,
    // matching the buttons which were already hidden from other roles in the UI)
    Route::get('/timetables', [TimetableController::class, 'index'])->name('timetables.index');
    Route::middleware('role:management')->group(function () {
        Route::post('/timetables', [TimetableController::class, 'store'])->name('timetables.store');
        Route::post('/timetables/auto-generate', [TimetableController::class, 'autoGenerate'])->name('timetables.auto');
    });

    // Materials
    Route::get('/materials', [MaterialController::class, 'index'])->name('materials.index');
    Route::post('/materials', [MaterialController::class, 'store'])->name('materials.store');
    Route::post('/materials/{material}/submit', [MaterialController::class, 'submitAssignment'])->name('materials.submit');
    Route::post('/materials/submission/{submission}/grade', [MaterialController::class, 'gradeSubmission'])->name('materials.grade');

    // Notices
    Route::get('/notices', [NoticeController::class, 'index'])->name('notices.index');
    Route::post('/notices', [NoticeController::class, 'store'])->name('notices.store');

    // Queued alert notifications
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::post('/alerts/{alert}/read', [AlertController::class, 'markRead'])->name('alerts.read');
    Route::post('/alerts/read-all', [AlertController::class, 'markAllRead'])->name('alerts.read_all');

    // Real-Time Broadcast Messaging
    Route::get('/api/realtime-messages/check', [BroadcastController::class, 'checkRealtime'])->name('broadcasts.check');
    Route::post('/api/realtime-messages/{broadcast}/dismiss', [BroadcastController::class, 'dismiss'])->name('broadcasts.dismiss');

    Route::middleware('role:management')->group(function () {
        Route::get('/broadcasts', [BroadcastController::class, 'index'])->name('broadcasts.index');
        Route::post('/broadcasts/send', [BroadcastController::class, 'store'])->name('broadcasts.send');
        Route::post('/broadcasts/{broadcast}/toggle', [BroadcastController::class, 'toggle'])->name('broadcasts.toggle');
        Route::delete('/broadcasts/{broadcast}', [BroadcastController::class, 'destroy'])->name('broadcasts.destroy');
    });

    // Dedicated WhatsApp Hub & Messaging Center
    Route::middleware('role:management,staff')->group(function () {
        Route::get('/whatsapp', [WhatsAppController::class, 'index'])->name('whatsapp.index');
        Route::post('/whatsapp/send-direct', [WhatsAppController::class, 'sendDirect'])->name('whatsapp.send_direct');
        Route::post('/whatsapp/triggers', [WhatsAppController::class, 'updateTriggers'])->name('whatsapp.triggers');
        Route::get('/whatsapp/logs', [WhatsAppController::class, 'logs'])->name('whatsapp.logs');
        Route::get('/whatsapp/test-connection', [WhatsAppController::class, 'testConnection'])->name('whatsapp.test_connection');
    });

    // Website Settings (management only)
    Route::middleware('role:management')->group(function () {
        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('/settings/whatsapp/check', [SettingController::class, 'checkWhatsApp'])->name('settings.whatsapp.check');
    });
});
