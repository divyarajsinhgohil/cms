# CMS — Features & Improvements Audit

_Read-only audit of the Laravel application at `C:\xampp\htdocs\cms`. Generated 2026-09-01._

## 1. Overview

Despite the `cms` folder name, this is **not a content-management system** — it is a **College / Campus Management System** for an institution branded as *Harivandana College* (`resources/views/layouts/app.blade.php`). It is a role-based academic portal covering the full student lifecycle: admissions, academics, attendance, fees, exams, timetables, hostel, grievances, and outbound WhatsApp marketing.

- **Backend:** Laravel 12, PHP 8.2+ (`composer.json`), MySQL (`cms_database.sql`).
- **Frontend:** Server-rendered Blade + Bootstrap 5.3.2, FontAwesome, Choices.js, Plus Jakarta Sans — all loaded from CDN; light/dark theme via `data-bs-theme` + `localStorage`. Vite is configured (`vite.config.js`) but the UI relies on CDN assets, not the bundle.
- **APIs / integrations:** Laravel Sanctum token API (`routes/api.php`) mirroring the web app for a future mobile/SPA client; `barryvdh/laravel-dompdf` for PDFs; Meta WhatsApp Business Cloud API (`app/Services/WhatsAppService.php`).
- **Access model:** three roles — `management`, `staff`, `student` — enforced by `EnsureUserHasRole` middleware (aliased `role` in `bootstrap/app.php`), with `management` implicitly granted access everywhere.

## 2. What Exists Today

~20 functional modules are implemented (web + parallel Sanctum API):

- **Authentication** — login by email, phone, **or full name**; multi-match account chooser (`auth/choose-account`); forced first-login password change (`must_change_password`); "Left" students blocked from re-login (`AuthController.php`).
- **Role-Based Access Control** — `management` / `staff` / `student` via `EnsureUserHasRole` middleware.
- **Role-specific dashboards** — management KPIs (students, staff, pending applications, fees collected/dues), staff dashboard, student dashboard with 14-day attendance trend, CGPA, and fee status (`DashboardController.php`, `resources/views/dashboards/*`).
- **Academic master data (CRUD)** — Departments, Courses, Subjects (`AcademicController`).
- **Staff management** — `StaffProfile` (designation, qualification, salary, joining date) + stream/course scoping via `staff_courses` pivot.
- **Student directory & admissions** — enroll, mark-left (lifecycle status), promote-semester (`AdmissionController`).
- **Public online admission applications** — no-login `/apply` form + management approve/reject (`AdmissionApplicationController`).
- **Sections** — Class A/B sections per stream, even-split assignment, class-teacher field (`SectionController`).
- **Attendance** — per-subject daily marking, low-attendance report, student self-view (`AttendanceController`).
- **Fees** — fee structures, payment collection, receipts + PDF, student self-view (`FeeController`).
- **Exams & results** — marks entry, grade + CGPA/grade-point, marksheet + PDF, student self-view (`ExamController`).
- **Timetable** — manual slots + `auto-generate`, per course/semester, date-specific overrides (`specific_date`) (`TimetableController`).
- **Study materials & assignments** — upload (mime/size-validated, 10 MB), student submission, staff grading (`MaterialController`).
- **Notice board** — targeted by audience / course / semester, priority levels (`NoticeController`).
- **Hostel** — rooms, allotments, outpass/gate-pass requests (`HostelController`).
- **Complaints & grievances** — categorized, status workflow (`GrievanceController`).
- **Leave management** — staff/students apply, management approve/reject (`LeaveController`).
- **Certificates** — ID card + Bonafide certificate, HTML preview + PDF (`CertificateController`).
- **WhatsApp marketing** — templates, campaigns, delivery logs via Meta Cloud API (`MarketingController`, `WhatsAppService`).
- **Site settings** — branding (name, logo, affiliation), contact info, academic year, WhatsApp API credentials + live connection check (`SettingController`, `SiteSetting`).
- **PDF generation** — dompdf across receipts, marksheets, ID cards, bonafide.
- **REST API** — Sanctum-token endpoints mirroring the above (`routes/api.php`).

## 3. Improvements to Existing Features

### Security
- **No login rate limiting.** `POST /login` (`routes/web.php:30`) has no `throttle` middleware — brute-force is unthrottled. Add Laravel's `throttle` / a `RateLimiter`.
- **Login-by-name weakens auth.** `AuthController::login` matches on `LOWER(name)` and `phone` (`AuthController.php:37-41`), enabling account enumeration by guessing names; combined with weak defaults this is exploitable. Consider restricting to email/enrollment-no + password.
- **Weak/known default passwords.** Every seeded account uses `Hash::make('password')` (`DatabaseSeeder.php`); password policy is only `min:6` with no complexity (`AuthController::updatePassword`). Raise to `min:8` + complexity and force rotation of seeded accounts.
- **Dead/insecure route.** `GET /quick-login/{role}` (`routes/web.php:33`) points at `AuthController::quickLogin`, which **does not exist** — it will 500 if hit, and as a "log in by role" endpoint it is a passwordless-login hazard. Remove it.
- **Secrets stored in plaintext.** `whatsapp_access_token` is a plain `fillable` column on `SiteSetting` — add an `encrypted` cast so the Meta token isn't readable in the DB dump (`cms_database.sql`).
- **Stray DB export script in project root.** `export_mysql.php` (raw mysqldump-style script) sits at the app root; ensure it is never web-reachable and remove it from the repo.

### Performance
- **Synchronous WhatsApp campaigns.** `MarketingController` loops recipients and calls `sendTemplate()` inline in the request (`MarketingController.php:77-85`) — large lists will time out. Move to a queued job (queue infra already exists: `jobs` table, `queue:listen` in `composer.json`).
- **Dashboard aggregation queries.** Management dashboard runs a join+`sum` over `students × fee_structures` on every load (`DashboardController.php:35-38`); cache these KPIs or precompute.
- **CDN-only front-end.** All CSS/JS come from external CDNs rather than the Vite bundle — adds render-blocking third-party requests and a hard dependency on CDN uptime. Bundle via the existing Vite pipeline (or at minimum add `preconnect` + SRI).
- **Add indexes** on hot filter columns (e.g. `attendances.date`, `notices.target_audience`) to keep reports fast as data grows.

### Code Quality
- **Stale seeder.** `DatabaseSeeder.php` imports and uses `App\Models\Enquiry` and seeds the `enquiries` table, but there is **no `Enquiry` model** and migration `..._000008_drop_enquiries_table` drops that table — `db:seed` will fatal. Remove the enquiry seed block.
- **Two role middlewares, one used.** Both `EnsureUserHasRole` (registered) and `RoleMiddleware` (unused) exist — delete the dead one to avoid confusion.
- **Fat controllers.** Business logic (CGPA, attendance %, fee dues, timetable auto-generation) lives in controllers/models; extract to dedicated Service/Action classes for testability.
- **Near-zero test coverage.** Only the default Laravel test scaffold is present despite PHPUnit being configured — add feature tests for auth, role gates, and fee/exam calculations.

### UX / UI
- **No "forgot password" self-service.** Password reset depends on an admin; users who forget are stuck (only forced-change + manual change exist).
- **"SMS" channel is a stub.** Marketing offers an SMS template type but there is no SMS gateway — sends are simulated (`MarketingController.php:102`). Either wire a gateway or hide the option.
- **Flash-message consistency.** Some flows redirect with `success`/`info`/`error` keys inconsistently; standardize a single toast partial.

### SEO / Public-facing
- The only public pages are `/login` and `/apply`; both lack meta description, Open Graph tags, and a canonical URL. If the public admission funnel matters, add basic meta tags and a sitemap for the `/apply` landing page.

## 4. New Feature Ideas (Prioritized)

| Feature | What it does | Value | Effort | Priority |
|---|---|---|---|---|
| Self-service password reset | Email/OTP "forgot password" flow so students/staff recover accounts without admin | Removes #1 support burden; closes auth gap | S | **High** |
| Online fee payment gateway | Razorpay/Stripe/UPI so students pay online and receipts auto-generate (today `FeeController` only records manual payments) | Direct revenue enablement; fewer counter visits | M | **High** |
| Automated notifications | Reuse `WhatsAppService` (+ email) for low-attendance alerts, fee-due reminders, result-published, notice pushes | High engagement; infra already exists | M | **High** |
| Parent / guardian portal | Guardian login (guardian name/phone already stored) to view child's attendance, fees, results | Strong differentiator for schools/colleges | M | **High** |
| Consolidated transcript | Cross-semester marksheet/transcript PDF + result-publish workflow (dompdf already in use) | Registrar/graduation need | M | Med |
| Bulk student import (CSV/Excel) | Onboard a whole batch at once instead of one online form/manual entry | Big time saver at admission season | S | Med |
| Library management module | Books catalog, issue/return, fines — the `library_fee` column exists but no module does | Rounds out campus operations | M | Med |
| Timetable clash detection + calendar view | Validate room/staff conflicts on save and render a weekly calendar; faculty substitution | Prevents scheduling errors | M | Med |
| Audit log / activity trail | Record management actions (fee edits, mark changes, approvals) with actor + timestamp | Accountability & security | S | Med |
| Two-factor auth for management | TOTP 2FA on high-privilege accounts (Sanctum + Fortify-style) | Protects sensitive financial/academic data | M | Med |
| Reports & export center | Attendance/fee/result reports exportable to Excel/PDF with filters | Admin/NAAC reporting | M | Med |
| Analytics dashboard | Trends for admissions funnel, fee collection, pass %, attendance | Management decision support | M | Med |
| QR / self check-in attendance | Students scan a per-class QR to mark presence, staff confirm | Faster, less manual marking | M | Low |
| Academic calendar & events | Institution-wide events, holidays, exam windows shown on dashboards | Nice-to-have organization | S | Low |
| Hostel mess & transport add-ons | Mess menu/attendance and bus-route management alongside existing hostel module | Extends facilities coverage | L | Low |

## 5. Quick Wins (do these first)

1. **Add login throttling** — attach `throttle:5,1` (or a named RateLimiter) to `POST /login` in `routes/web.php`. One line, closes an open brute-force hole.
2. **Remove the dead `/quick-login/{role}` route** — it references a nonexistent method (500 risk) and reads as a passwordless-login backdoor. Delete line 33 of `routes/web.php`.
3. **Encrypt the WhatsApp token** — add `'whatsapp_access_token' => 'encrypted'` to a `casts()` method on `App\Models\SiteSetting`. Protects a live Meta credential in DB dumps.
4. **Fix the stale seeder + remove `export_mysql.php`** — drop the `Enquiry` seed block (currently makes `db:seed` fatal) and remove the loose root-level DB export script.
5. **Strengthen the password policy** — bump `updatePassword` validation from `min:6` to `min:8` with complexity, and rotate the seeded `password` defaults. Trivial change, meaningful hardening.

---
_All findings are grounded in the code as read; file paths cite where each item lives. No application code, migrations, or data were modified during this audit._
