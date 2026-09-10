<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Department;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Student;
use App\Models\StaffProfile;
use App\Models\AdmissionApplication;
use App\Models\MarketingTemplate;
use App\Models\MarketingCampaign;
use App\Models\MarketingLog;
use App\Models\Attendance;
use App\Models\FeeStructure;
use App\Models\FeePayment;
use App\Models\ExamMark;
use App\Models\Timetable;
use App\Models\StudyMaterial;
use App\Models\AssignmentSubmission;
use App\Models\Notice;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Core Users
        $adminUser = User::create([
            'name' => 'Dr. Robert Vance (Director)',
            'email' => 'admin@cms.edu',
            'password' => Hash::make('password'),
            'role' => 'management',
            'phone' => '+91 98765 43210',
            'avatar' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=150',
        ]);

        $staffUser = User::create([
            'name' => 'Prof. Sarah Jenkins',
            'email' => 'faculty@cms.edu',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'phone' => '+91 98765 43211',
            'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150',
        ]);

        $studentUser = User::create([
            'name' => 'Alex Morgan',
            'email' => 'student@cms.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
            'phone' => '+91 98765 43212',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150',
        ]);

        // Additional Students
        $studentUser2 = User::create([
            'name' => 'Rahul Sharma',
            'email' => 'rahul@cms.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
            'phone' => '+91 98765 43213',
        ]);

        // 2. Departments
        $mcaDept = Department::create([
            'name' => 'Master of Computer Applications',
            'code' => 'MCA',
            'head_of_department' => 'Dr. Alan Turing',
        ]);

        $cseDept = Department::create([
            'name' => 'Computer Science & Engineering',
            'code' => 'CSE',
            'head_of_department' => 'Dr. Grace Hopper',
        ]);

        $comDept = Department::create([
            'name' => 'Department of Commerce & Finance',
            'code' => 'DOC',
            'head_of_department' => 'Dr. Warren Buffett',
        ]);

        $mbaDept = Department::create([
            'name' => 'Master of Business Administration',
            'code' => 'MBA',
            'head_of_department' => 'Dr. Peter Drucker',
        ]);

        // 3. Courses
        $mcaCourse = Course::create([
            'department_id' => $mcaDept->id,
            'name' => 'Master of Computer Applications (MCA)',
            'code' => 'MCA',
            'duration_years' => 2,
        ]);

        $bcaCourse = Course::create([
            'department_id' => $mcaDept->id,
            'name' => 'Bachelor of Computer Applications (BCA)',
            'code' => 'BCA',
            'duration_years' => 3,
        ]);

        $bcomCourse = Course::create([
            'department_id' => $comDept->id,
            'name' => 'Bachelor of Commerce (B.Com Hons)',
            'code' => 'BCOM',
            'duration_years' => 3,
        ]);

        $mbaCourse = Course::create([
            'department_id' => $mbaDept->id,
            'name' => 'Master of Business Administration (MBA)',
            'code' => 'MBA',
            'duration_years' => 2,
        ]);

        $cseCourse = Course::create([
            'department_id' => $cseDept->id,
            'name' => 'B.Tech Computer Science & Engineering',
            'code' => 'BTECH-CSE',
            'duration_years' => 4,
        ]);

        // 4. Staff Profile
        StaffProfile::create([
            'user_id' => $staffUser->id,
            'department_id' => $mcaDept->id,
            'employee_id' => 'EMP-MCA-001',
            'designation' => 'Associate Professor',
            'qualification' => 'Ph.D. in Computer Science',
            'joining_date' => '2020-08-15',
        ]);

        // 5. Students Profiles
        $student1 = Student::create([
            'user_id' => $studentUser->id,
            'enrollment_no' => 'CMS-2026-MCA-00101',
            'department_id' => $mcaDept->id,
            'course_id' => $mcaCourse->id,
            'batch_year' => 2026,
            'current_semester' => 3,
            'gender' => 'Female',
            'dob' => '2001-05-14',
            'address' => '42 College Road, Tech City',
            'guardian_name' => 'Michael Morgan',
            'guardian_phone' => '+91 98765 00001',
        ]);

        $student2 = Student::create([
            'user_id' => $studentUser2->id,
            'enrollment_no' => 'CMS-2026-MCA-00102',
            'department_id' => $mcaDept->id,
            'course_id' => $mcaCourse->id,
            'batch_year' => 2026,
            'current_semester' => 3,
            'gender' => 'Male',
            'dob' => '2000-11-20',
            'address' => '88 Innovation Boulevard',
            'guardian_name' => 'Sunil Sharma',
            'guardian_phone' => '+91 98765 00002',
        ]);

        // Student 3 (BCA Stream)
        $bcaUser = User::create([
            'name' => 'Rohan Mehta',
            'email' => 'rohan@cms.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
            'phone' => '+91 98765 43214',
        ]);

        Student::create([
            'user_id' => $bcaUser->id,
            'enrollment_no' => 'CMS-2026-MCA-00103',
            'department_id' => $mcaDept->id,
            'course_id' => $bcaCourse->id,
            'batch_year' => 2026,
            'current_semester' => 1,
            'gender' => 'Male',
            'dob' => '2003-02-10',
            'address' => '12 Park Street',
            'guardian_name' => 'Rajesh Mehta',
            'guardian_phone' => '+91 98765 00003',
        ]);

        // Student 5 (First-Time Phone Login Test Student)
        $newStudentUser = User::create([
            'name' => 'Vikram Roy',
            'email' => 'vikram@cms.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
            'phone' => '+91 98765 43299',
            'must_change_password' => true,
        ]);

        Student::create([
            'user_id' => $newStudentUser->id,
            'enrollment_no' => 'CMS-2026-MCA-00104',
            'department_id' => $mcaDept->id,
            'course_id' => $mcaCourse->id,
            'batch_year' => 2026,
            'current_semester' => 1,
            'gender' => 'Male',
            'dob' => '2002-05-12',
            'address' => '99 Innovation Hub',
            'guardian_name' => 'Dipak Roy',
            'guardian_phone' => '+91 98765 00005',
        ]);

        // Student 4 (B.Com Stream)
        $bcomUser = User::create([
            'name' => 'Ananya Sen',
            'email' => 'ananya@cms.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
            'phone' => '+91 98765 43215',
        ]);

        Student::create([
            'user_id' => $bcomUser->id,
            'enrollment_no' => 'CMS-2026-DOC-00101',
            'department_id' => $comDept->id,
            'course_id' => $bcomCourse->id,
            'batch_year' => 2026,
            'current_semester' => 2,
            'gender' => 'Female',
            'dob' => '2002-08-25',
            'address' => '55 Finance Avenue',
            'guardian_name' => 'Subhash Sen',
            'guardian_phone' => '+91 98765 00004',
        ]);

        // 6. Subjects
        $sub1 = Subject::create([
            'course_id' => $mcaCourse->id,
            'semester' => 3,
            'name' => 'Web Application Development with Laravel',
            'code' => 'MCA301',
            'credits' => 4,
        ]);

        $sub2 = Subject::create([
            'course_id' => $mcaCourse->id,
            'semester' => 3,
            'name' => 'Advanced Database Management Systems',
            'code' => 'MCA302',
            'credits' => 4,
        ]);

        $sub3 = Subject::create([
            'course_id' => $mcaCourse->id,
            'semester' => 3,
            'name' => 'Cloud Computing & DevOps',
            'code' => 'MCA303',
            'credits' => 3,
        ]);

        // 7. Admission Applications
        AdmissionApplication::create([
            'first_name' => 'David',
            'last_name' => 'Miller',
            'surname' => 'Miller',
            'email' => 'david@gmail.com',
            'mobile' => '+91 98111 22233',
            'course_id' => $mcaCourse->id,
            'gender' => 'Male',
            'status' => 'Pending',
        ]);

        AdmissionApplication::create([
            'first_name' => 'Priya',
            'last_name' => 'Nair',
            'surname' => 'Nair',
            'email' => 'priya@nair.com',
            'mobile' => '+91 98222 33344',
            'course_id' => $cseCourse->id,
            'gender' => 'Female',
            'status' => 'Pending',
        ]);

        AdmissionApplication::create([
            'first_name' => 'Kevin',
            'last_name' => 'Peterson',
            'surname' => 'Peterson',
            'email' => 'kevin@yahoo.com',
            'mobile' => '+91 98333 44455',
            'course_id' => $mcaCourse->id,
            'gender' => 'Male',
            'status' => 'Approved',
        ]);

        // 8. Marketing Templates & Campaigns
        $tmpl1 = MarketingTemplate::create([
            'title' => 'Admission Open 2026 - MCA & CSE',
            'type' => 'whatsapp',
            'content' => "Hello {name}, Admissions for Session 2026 are now OPEN at CMS College! Apply now for {course} and get early-bird scholarships. Contact us at 9876543210 or reply YES.",
        ]);

        $tmpl2 = MarketingTemplate::create([
            'title' => 'Campus Visit Invitation SMS',
            'type' => 'sms',
            'content' => "Dear {name}, join our Grand Open Campus Tour on Saturday at CMS College. Experience state-of-the-art labs & meet faculty. Call 9876543210 to book spot.",
        ]);

        $campaign = MarketingCampaign::create([
            'name' => 'August MCA Outreach Blitz',
            'template_id' => $tmpl1->id,
            'recipients_count' => 125,
            'status' => 'Completed',
            'sent_at' => now()->subHours(4),
        ]);

        MarketingLog::create([
            'campaign_id' => $campaign->id,
            'recipient_phone' => '+91 98111 22233',
            'channel' => 'WhatsApp',
            'message' => str_replace(['{name}', '{course}'], ['David Miller', 'MCA'], $tmpl1->content),
            'status' => 'Delivered',
        ]);

        MarketingLog::create([
            'campaign_id' => $campaign->id,
            'recipient_phone' => '+91 98222 33344',
            'channel' => 'WhatsApp',
            'message' => str_replace(['{name}', '{course}'], ['Priya Nair', 'B.Tech CSE'], $tmpl1->content),
            'status' => 'Read',
        ]);

        // 9. Attendance
        $dates = [now()->subDays(4)->format('Y-m-d'), now()->subDays(3)->format('Y-m-d'), now()->subDays(2)->format('Y-m-d'), now()->subDays(1)->format('Y-m-d')];
        foreach ($dates as $d) {
            Attendance::create([
                'student_id' => $student1->id,
                'subject_id' => $sub1->id,
                'date' => $d,
                'status' => 'present',
                'marked_by' => $staffUser->id,
            ]);
            Attendance::create([
                'student_id' => $student2->id,
                'subject_id' => $sub1->id,
                'date' => $d,
                'status' => $d == end($dates) ? 'absent' : 'present',
                'marked_by' => $staffUser->id,
            ]);
        }

        // 10. Fee Structure & Payment
        $feeStruct = FeeStructure::create([
            'course_id' => $mcaCourse->id,
            'academic_year' => '2026-2027',
            'tuition_fee' => 60000.00,
            'exam_fee' => 5000.00,
            'library_fee' => 3000.00,
            'other_fee' => 2000.00,
            'total_amount' => 70000.00,
        ]);

        FeePayment::create([
            'student_id' => $student1->id,
            'receipt_no' => 'REC-2026-00101',
            'amount_paid' => 45000.00,
            'payment_mode' => 'Online UPI',
            'payment_date' => now()->subDays(10)->format('Y-m-d'),
            'remarks' => 'First installment semester 3 fee received.',
        ]);

        FeePayment::create([
            'student_id' => $student2->id,
            'receipt_no' => 'REC-2026-00102',
            'amount_paid' => 70000.00,
            'payment_mode' => 'Bank Transfer',
            'payment_date' => now()->subDays(15)->format('Y-m-d'),
            'remarks' => 'Full annual fee clear.',
        ]);

        // 11. Exam Marks
        ExamMark::create([
            'student_id' => $student1->id,
            'subject_id' => $sub1->id,
            'semester' => 3,
            'internal_marks' => 28,
            'external_marks' => 65,
            'total_marks' => 93,
            'grade' => 'A+',
            'grade_point' => 4.00,
        ]);

        ExamMark::create([
            'student_id' => $student1->id,
            'subject_id' => $sub2->id,
            'semester' => 3,
            'internal_marks' => 25,
            'external_marks' => 60,
            'total_marks' => 85,
            'grade' => 'A',
            'grade_point' => 3.75,
        ]);

        // 12. Timetables
        Timetable::create([
            'course_id' => $mcaCourse->id,
            'semester' => 3,
            'subject_id' => $sub1->id,
            'staff_id' => $staffUser->id,
            'classroom' => 'Lab 102',
            'day_of_week' => 'Monday',
            'start_time' => '09:30:00',
            'end_time' => '10:30:00',
        ]);

        Timetable::create([
            'course_id' => $mcaCourse->id,
            'semester' => 3,
            'subject_id' => $sub2->id,
            'staff_id' => $staffUser->id,
            'classroom' => 'Room 204',
            'day_of_week' => 'Tuesday',
            'start_time' => '10:30:00',
            'end_time' => '11:30:00',
        ]);

        // 13. Study Material
        $sm1 = StudyMaterial::create([
            'subject_id' => $sub1->id,
            'uploaded_by' => $staffUser->id,
            'title' => 'Laravel MVC Architecture & Eloquent ORM Master Class',
            'description' => 'Complete slide deck and code snippets covering Blade templates, Eloquent relations, and middleware security.',
            'type' => 'notes',
            'due_date' => null,
        ]);

        $sm2 = StudyMaterial::create([
            'subject_id' => $sub1->id,
            'uploaded_by' => $staffUser->id,
            'title' => 'Assignment 1: Build a College Management Subsystem',
            'description' => 'Implement role-based middleware and CRUD controllers for student admission.',
            'type' => 'assignment',
            'due_date' => now()->addDays(5)->format('Y-m-d'),
        ]);

        AssignmentSubmission::create([
            'study_material_id' => $sm2->id,
            'student_id' => $student1->id,
            'comments' => 'Submitted assignment zip with test cases.',
            'status' => 'graded',
            'marks_obtained' => 95.0,
        ]);

        // 14. Notices
        Notice::create([
            'title' => 'Mid-Semester Examination Schedule Announced',
            'content' => 'The Mid-Semester examinations for MCA and B.Tech CSE will commence from 15th September. Detailed timetable is available on the portal.',
            'target_audience' => 'all',
            'priority' => 'urgent',
            'posted_by' => $adminUser->id,
        ]);

        Notice::create([
            'title' => 'Faculty Meeting: NAAC Accreditation Review',
            'content' => 'All Department Heads and Assistant Professors are requested to attend the NAAC self-study report briefing in Conference Room A.',
            'target_audience' => 'staff',
            'priority' => 'normal',
            'posted_by' => $adminUser->id,
        ]);
    }
}
