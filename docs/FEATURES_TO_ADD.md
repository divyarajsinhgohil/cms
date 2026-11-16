# CMS — Features To Add (Implementation-Ready Backlog)

_Buildable backlog for the **Harivandana College** Campus Management System at `C:\xampp\htdocs\cms` (Laravel 12 / PHP 8.2 / MySQL, Blade + Bootstrap 5, Sanctum API, dompdf, Meta WhatsApp Cloud API). Turns the feature ideas in `FEATURES_AND_IMPROVEMENTS.md` into specs grounded in the real code. Generated 2026-09-01._

> Scope note: this file is **new features only**. The security/perf/code-quality fixes (login throttling, remove dead `/quick-login`, encrypt WhatsApp token, seeder fix, password policy) live in `FEATURES_AND_IMPROVEMENTS.md` §3 and should ship alongside Phase 1 but are not repeated here.

---

## Phase 1 — Quick Wins (ship first)

Small effort, high leverage, no external vendor or budget needed:

1. **F1 Self-service password reset** — closes the top support burden and the auth gap.
2. **F5 Bulk student import (CSV/Excel)** — kills manual one-by-one entry at admission season.
3. **F7 Audit log / activity trail** — accountability on fee/mark/approval edits; ~1 table + 1 observer.
4. **F13 Academic calendar & events** — 1 table + 1 controller + dashboard widget.

---

## Summary Table (ordered by priority)

| # | Feature | Value | Effort | Priority | Depends on |
|---|---|---|---|---|---|
| F1 | Self-service password reset | Removes #1 support load; closes auth gap | S | **High** | Mail config (SMTP) |
| F2 | Automated event notifications | Attendance/fee/result/notice pushes; infra exists | M | **High** | Queue worker; F3 for fee events |
| F3 | Online fee payment gateway (Razorpay/UPI) | Direct revenue enablement | M | **High** | **Vendor + credentials (human)** |
| F4 | Parent / Guardian portal | Strong school/college differentiator | M | **High** | Guardian data (exists) |
| F5 | Bulk student import (CSV/Excel) | Big time-saver at admission | S | **High** | — |
| F6 | Two-factor auth (TOTP) for management | Protects financial/academic data | M | Med | **Package choice (human)** |
| F7 | Audit log / activity trail | Accountability & security | S | Med | — |
| F8 | Library management module | `library_fee` exists, no module | M | Med | — |
| F9 | Consolidated transcript & result-publish | Registrar/graduation need | M | Med | — |
| F10 | Timetable clash detection + calendar view | Prevents scheduling errors | M | Med | — |
| F11 | Reports & export center (Excel/PDF) | Admin/NAAC reporting | M | Med | — |
| F12 | Analytics dashboard | Management decision support | M | Med | — |
| F13 | Academic calendar & events | Institution-wide organization | S | Med | — |
| F14 | QR / self check-in attendance | Faster marking | M | Low | — |
| F15 | Transport management module | Extends facilities coverage | M | Low | — |
| F16 | Hostel mess management | Extends hostel module | L | Low | — |

---

## Feature Details

### F1 — Self-service password reset · S · High
**Story:** As a student/staff who forgot my password, I reset it via an email link (or OTP) without waiting on an admin.
**Touchpoints:**
- Routes: add to `routes/web.php` — `GET/POST /forgot-password`, `GET/POST /reset-password/{token}` (mirror Laravel's built-in `PasswordBroker`).
- Controller: new `App\Http\Controllers\Auth\PasswordResetController` (or use Laravel's `Password::sendResetLink`).
- Tables: `password_reset_tokens` already exists (default users migration `0001_01_01_000000`); no schema change.
- Models: `User` already `Notifiable` (`app/Models/User.php:7,12`) — add `sendPasswordResetNotification()` or use default.
- Views: new `resources/views/auth/forgot-password.blade.php`, `reset-password.blade.php` (match existing `auth/` styling).
- Config: needs working SMTP in `.env` (`MAIL_*`). Optional WhatsApp-OTP fallback via `WhatsAppService::sendTemplate` if email is unavailable.
**Acceptance:**
- "Forgot password?" link on `/login` opens the request form.
- Submitting a known email/phone sends a time-limited reset link (or OTP); unknown identifiers return the same neutral message (no enumeration).
- Following the link lets the user set a new password meeting the policy (align with hardened `min:8`).
- Token is single-use and expires (default 60 min); `must_change_password` is cleared after reset.
**Effort:** S — mostly wiring Laravel's built-in flow + 2 Blade views.

---

### F2 — Automated event notifications · M · High
**Story:** As a student/guardian I automatically get a WhatsApp/email alert when my attendance drops, a fee is due, results publish, or a targeted notice is posted.
**Touchpoints:**
- Service: reuse `app/Services/WhatsAppService.php` (`sendTemplate($to,$templateName,$language,$bodyParams)`); add a `NotificationService` that picks channel (WhatsApp/email) and logs to `marketing_logs`.
- Events/observers: hook `ExamMark` (result published), `FeePayment`/`FeeStructure` (due reminders), `Notice::created` (`app/Models/Notice.php`), and a scheduled low-attendance scan using `Student::calculateAttendancePercentage()` (`app/Models/Student.php:78`).
- Queue: dispatch as jobs — `jobs` table + `queue:listen` already configured (per audit §3); **do not** send inline (fixes the synchronous-campaign issue too).
- Scheduler: register recurring scans in `routes/console.php` (fee-due + low-attendance daily).
- Tables: add `notification_preferences` (user_id, channel, event_type, enabled) OR a simple `notifications` log; reuse `marketing_logs` for delivery status.
- Recipients: student phone via `users.phone`; guardian via `students.guardian_phone`.
**Acceptance:**
- Publishing exam marks queues a "result available" message to each affected student (and guardian if enabled).
- A daily job flags students under a configurable attendance threshold and notifies them once (no daily spam — dedupe).
- Fee-due reminders fire N days before/after due date; stop once paid.
- All sends are queued (request returns immediately) and recorded with delivered/failed status.
- Meta template names/languages are configurable in Settings (templates must be pre-approved — see WhatsAppService docblock).
**Effort:** M. **Human decision:** Meta template approval + per-message WhatsApp cost.

---

### F3 — Online fee payment gateway (Razorpay/UPI) · M · High
**Story:** As a student I pay fees online via UPI/card and get an auto-generated receipt, instead of visiting the counter.
**Touchpoints:**
- Today `FeeController::collectPayment` (`routes/web.php:135`) only records manual payments (management-only). Add a **student-facing** pay flow.
- Routes: `GET /fees/pay-online`, `POST /fees/pay-online/order` (create order), `POST /fees/pay-online/callback` + webhook `POST /fees/webhook/razorpay` (CSRF-exempt).
- Controller: new `App\Http\Controllers\FeePaymentGatewayController`; service `App\Services\RazorpayService` (SDK `razorpay/razorpay`).
- Tables: extend `fee_payments` (add `gateway`, `gateway_order_id`, `gateway_payment_id`, `gateway_signature`, `status` pending/success/failed). `payment_mode` enum already includes `UPI`/`Online` (migration `..._000001:151`).
- Models: `FeePayment` — add gateway columns to `$fillable`; receipt_no still `unique`.
- Config: `.env` keys `RAZORPAY_KEY`, `RAZORPAY_SECRET`, webhook secret; expose in `SiteSetting` (encrypted cast) if per-tenant.
- Views: new `resources/views/fees/pay-online.blade.php` (Razorpay checkout JS); reuse existing receipt/PDF (`FeeController::downloadReceiptPdf`).
**Acceptance:**
- Student sees outstanding dues (`Student::calculatePendingDues()`, `Student.php:91`) and a "Pay now" button.
- Successful payment verifies signature server-side, writes a `fee_payments` row with `status=success`, and issues the same PDF receipt as counter payments.
- Failed/abandoned payments do not create a paid record; webhook reconciles late confirmations.
- Amount is server-computed (never trusted from client).
- Management fee list shows online vs. counter payments.
**Effort:** M. **Human decision:** vendor choice (Razorpay/Stripe/PayU), merchant account + live credentials, settlement bank account.

---

### F4 — Parent / Guardian portal · M · High
**Story:** As a guardian I log in to see my child's attendance, fees, and results.
**Touchpoints:**
- Data already present: `students.guardian_name`, `students.guardian_phone` (migration `..._000001:90-91`).
- Auth model: add `guardian` to the role set in `EnsureUserHasRole` (aliased `role` in `bootstrap/app.php`) OR add a `guardians` table (guardian_id, student_id) for multi-child support. Recommend a `guardians` table + `guardian_student` pivot for parents with >1 child.
- Routes: guardian-scoped `GET /guardian/dashboard`, `/guardian/attendance`, `/guardian/fees`, `/guardian/results` (middleware `role:guardian`).
- Controller: new `GuardianController` reusing `Student::calculateAttendancePercentage/calculatePendingDues/calculateCGPA`.
- Views: new `resources/views/dashboards/guardian.blade.php` (read-only mirror of student dashboard).
- Onboarding: create guardian login when a student is enrolled (`AdmissionController::store`) using guardian phone; forced first-login password change (`must_change_password`).
- API: mirror in `routes/api.php` for a future parent app.
**Acceptance:**
- Guardian logs in and sees only their linked child/children (no cross-student data leak — enforce ownership on every query).
- Read-only: attendance %, 14-day trend, fee dues + receipts, CGPA + marksheet PDF.
- A guardian linked to multiple students can switch child.
- Guardian cannot access management/staff routes.
**Effort:** M.

---

### F5 — Bulk student import (CSV/Excel) · S · High
**Story:** As management I upload a CSV/Excel to onboard a whole batch at once instead of the one-at-a-time form.
**Touchpoints:**
- Routes: `GET /admissions/import` (form + template download), `POST /admissions/import` (upload) under `role:management` (near `routes/web.php:85-89`).
- Controller: `AdmissionController::showImport/import`; use `maatwebsite/excel` (add to composer) or native `fgetcsv` for a dependency-free path.
- Models/tables: creates `User` (role=student) + `Student` per row; reuse enrollment-no generation (`app/Support/UniqueCode.php`). Validate `department_id`/`course_id`/`section_id` foreign keys.
- Views: new `resources/views/admissions/import.blade.php` with downloadable column template + dry-run preview.
- Transaction: wrap in DB transaction; collect per-row errors.
**Acceptance:**
- Downloadable template lists required columns (name, email, phone, course code, batch year, guardian…).
- Upload shows a preview with per-row validation errors before commit.
- Valid rows create user + student, assign enrollment no, set `must_change_password=true`.
- Duplicate email/phone/enrollment rows are rejected with a clear reason; no partial commit on fatal error.
- Post-import summary: N created, M skipped, with a downloadable error report.
**Effort:** S–M.

---

### F6 — Two-factor auth (TOTP) for management · M · Med
**Story:** As a management user I enable TOTP 2FA so a stolen password alone can't access financial/academic data.
**Touchpoints:**
- Package: `pragmarx/google2fa-laravel` (or Fortify's 2FA). Add `two_factor_secret`, `two_factor_confirmed_at`, `two_factor_recovery_codes` (encrypted) to `users`.
- Routes: `GET /settings/2fa`, `POST /settings/2fa/enable|confirm|disable`; a `2fa-challenge` step after login in `AuthController::login`.
- Controller: `AuthController` + new `TwoFactorController`.
- Views: `resources/views/auth/two-factor-challenge.blade.php`, settings panel with QR + recovery codes.
- Gate: enforce for `role:management` (optionally staff).
**Acceptance:**
- Management user can enable 2FA (scan QR, confirm a code) and receive one-time recovery codes.
- After enabling, login requires the 6-digit TOTP; recovery code works if device is lost.
- 2FA state and recovery codes stored encrypted.
- Admin can require 2FA for all management accounts via a Settings toggle.
**Effort:** M. **Human decision:** package selection (Fortify vs. google2fa).

---

### F7 — Audit log / activity trail · S · Med
**Story:** As management I see who changed a fee, edited a mark, or approved an application, and when.
**Touchpoints:**
- Table: new `activity_logs` (id, user_id, action, subject_type, subject_id, before/after JSON, ip, user_agent, created_at).
- Implementation: Eloquent observers on high-value models — `FeePayment`, `ExamMark`, `AdmissionApplication`, `Student` (mark-left/promote), `SiteSetting`. Or drop in `spatie/laravel-activitylog`.
- Routes: `GET /audit` (management only), filter by actor/module/date.
- Controller: new `AuditLogController`; views `resources/views/audit/index.blade.php`.
- Indexes: add index on `(subject_type, subject_id)` and `created_at`.
**Acceptance:**
- Create/update/delete on the watched models writes an immutable log row with actor + timestamp + changed fields.
- Management can filter/search the trail; students/staff cannot access it.
- Logs are append-only (no edit/delete from UI).
- Approvals (`AdmissionApplicationController::approve/reject`) and fee edits appear with before/after values.
**Effort:** S.

---

### F8 — Library management module · M · Med
**Story:** As a librarian I catalog books, issue/return copies, and auto-charge fines — the `library_fee` column exists but no module uses it.
**Touchpoints:**
- Tables: new `books` (title, author, isbn, category, total_copies, available_copies), `book_issues` (book_id, student_id, issued_at, due_at, returned_at, fine_amount, status).
- Models: `Book`, `BookIssue`; relate `BookIssue` → `Student`.
- Routes: `role:management,staff` — `/library` (catalog CRUD), `/library/issue`, `/library/return/{issue}`, `/library/fines`.
- Controller: new `LibraryController`.
- Views: new `resources/views/library/*` (matches `academics/` CRUD pattern).
- Fines: overdue calc job (reuse scheduler from F2); optionally push a due reminder via F2.
- API: mirror in `routes/api.php`.
**Acceptance:**
- Management/staff can add/edit/remove books; available-copies decrements on issue, increments on return.
- Issuing sets a due date; a book with 0 available copies can't be issued.
- Returning after due date computes a fine at a configurable per-day rate.
- Student dashboard shows their currently issued books + due dates.
- Library fines optionally roll into the student's dues view.
**Effort:** M.

---

### F9 — Consolidated transcript & result-publish workflow · M · Med
**Story:** As the registrar I publish semester results in a controlled step and generate a cross-semester consolidated transcript PDF.
**Touchpoints:**
- Today `ExamController::storeMarks` writes marks immediately and students see them (`routes/web.php:141-144`). Add a `published` gate.
- Tables: add `published_at`/`is_published` to `exam_marks` (or a `result_publications` table per course+semester).
- Controller: `ExamController` — add `publishResults(course, semester)`; `viewTranscript(student)`, `downloadTranscriptPdf(student)`.
- Views: new `resources/views/exams/transcript.blade.php` (all semesters, SGPA per sem + cumulative CGPA); reuse dompdf like existing marksheet.
- Models: `ExamMark` (`app/Models/ExamMark.php`), `Student::calculateCGPA` (`Student.php:97`); add per-semester SGPA helper.
**Acceptance:**
- Staff enter marks in draft; students see results only after management publishes that course+semester.
- Publishing optionally triggers F2 "result available" notification.
- Consolidated transcript PDF lists every completed semester with SGPA and final CGPA, on college letterhead.
- Unpublished semesters are excluded from the transcript.
**Effort:** M.

---

### F10 — Timetable clash detection + weekly calendar view · M · Med
**Story:** As management I'm blocked from saving a slot that double-books a room or a teacher, and I view the week as a calendar.
**Touchpoints:**
- Controller: `TimetableController::store` / `autoGenerate` (`routes/web.php:150-151`) — add validation against existing `timetables` rows on overlapping `day_of_week` + `start_time`/`end_time` for the same `classroom` or `staff_id`.
- Table: `timetables` (staff_id, classroom, day_of_week, start_time, end_time — migration `..._000001:172`); add composite indexes for the conflict query; supports `specific_date` overrides (per `..._000006_redesign_timetables_table`).
- Views: enhance `resources/views/timetables/` with a weekly grid (day × time) render; show conflicts inline.
- Optional: faculty substitution — mark a slot's staff as substituted for a date.
**Acceptance:**
- Saving a slot that overlaps an existing one for the same room or teacher is rejected with a specific message naming the clash.
- Auto-generate never emits a room/teacher conflict.
- Weekly calendar view renders per course/semester and per staff.
- Date-specific overrides display correctly against the recurring grid.
**Effort:** M.

---

### F11 — Reports & export center · M · Med
**Story:** As admin/NAAC coordinator I export filtered attendance/fee/result reports to Excel and PDF.
**Touchpoints:**
- Routes: new `/reports` hub (management/staff) — attendance, fee-collection, results, admissions funnel; each with `?format=xlsx|pdf`.
- Controller: new `ReportController`; `maatwebsite/excel` for xlsx, existing dompdf for PDF.
- Data: reuse existing aggregations (attendance %, fee sums via `Student` helpers; `FeePayment`, `ExamMark`, `AdmissionApplication`).
- Views: `resources/views/reports/*` with date/course/semester filters.
- Perf: add indexes on hot filter columns (`attendances.date`, `fee_payments.payment_date`) per audit §3.
**Acceptance:**
- Each report supports course/semester/date-range filters.
- On-screen table + one-click Excel and PDF export of the same filtered set.
- Fee-collection report totals reconcile with the dashboard KPI.
- Large exports run without timing out (chunked query / queued export for big ranges).
**Effort:** M.

---

### F12 — Analytics dashboard · M · Med
**Story:** As management I see trends — admissions funnel, monthly fee collection, pass %, attendance — not just current totals.
**Touchpoints:**
- Controller: extend `DashboardController::index` (`routes/web.php:45`) or new `AnalyticsController` for `role:management`.
- Data: time-series from `admission_applications` (funnel: applied→approved), `fee_payments.payment_date` (monthly collection), `exam_marks` (pass/fail %), `attendances.date` (trend).
- Views: `resources/views/dashboards/analytics.blade.php`; charts via a CDN chart lib (Chart.js) consistent with current CDN approach, or bundle via existing Vite.
- Perf: cache KPI/aggregate queries (audit flags the dashboard join+sum runs every load, `DashboardController.php:35-38`).
**Acceptance:**
- Admissions funnel, monthly fee collection, course-wise pass %, and attendance trend render as charts.
- Filters by academic year / department.
- Expensive aggregates are cached (e.g. 15 min) and refresh on relevant writes.
- Numbers reconcile with the existing management KPI cards.
**Effort:** M.

---

### F13 — Academic calendar & events · S · Med
**Story:** As any user I see institution-wide events, holidays, and exam windows on my dashboard.
**Touchpoints:**
- Table: new `calendar_events` (title, description, type [holiday/exam/event], start_date, end_date, audience, created_by).
- Model: `CalendarEvent`.
- Routes: `/calendar` (view all) + management-only CRUD.
- Controller: new `CalendarController`.
- Views: `resources/views/calendar/index.blade.php` (month grid) + a "Upcoming" widget on each dashboard (`resources/views/dashboards/*`).
**Acceptance:**
- Management creates/edits/deletes events with type + audience targeting (reuse notice audience pattern).
- All users see a month calendar and an "upcoming (next 7 days)" widget on their dashboard.
- Holidays/exam windows are visually distinct.
- Optional: pushes a reminder via F2.
**Effort:** S.

---

### F14 — QR / self check-in attendance · M · Low
**Story:** As a student I scan a per-class QR to mark presence; staff confirm the session.
**Touchpoints:**
- Tables: `attendance_sessions` (subject_id, staff_id, date, token, expires_at, is_open); attendance rows still land in `attendances` (`..._000001:122`).
- Controller: extend `AttendanceController` — `openSession`, `qr`, student `checkIn(token)`.
- Views: staff QR display; student scan/confirm page.
- Security: rotating short-lived token per session (prevents remote/shared-QR abuse); one check-in per student per session.
**Acceptance:**
- Staff opens a session and displays a QR that expires quickly.
- Student on the same session scans and is marked `present` in `attendances`; late scans marked `late`.
- Duplicate/expired/foreign-course check-ins are rejected.
- Staff sees live check-in count and can close the session.
**Effort:** M.

---

### F15 — Transport management module · M · Low
**Story:** As management I manage bus routes and student transport assignments.
**Touchpoints:**
- Tables: `transport_routes` (name, driver, vehicle_no, stops JSON, annual_fee), `transport_allotments` (student_id, route_id, stop, allotted_at).
- Models: `TransportRoute`, `TransportAllotment`.
- Routes: `role:management` `/transport` CRUD + assign; student sees their route on dashboard.
- Controller: new `TransportController` (mirror `HostelController` pattern).
- Views: `resources/views/facilities/transport/*` (a `facilities` views dir already exists).
- Fees: optional roll-up into student dues.
**Acceptance:**
- Management can CRUD routes with stops and assign students.
- A route's capacity isn't exceeded.
- Student dashboard shows assigned route + stop + fee.
- Transport fee optionally appears in dues.
**Effort:** M.

---

### F16 — Hostel mess management · L · Low
**Story:** As a hostel warden I publish the weekly mess menu and track mess attendance/charges alongside the existing hostel module.
**Touchpoints:**
- Tables: `mess_menus` (day, meal, items), `mess_attendance` (student_id, date, meal, present), optional `mess_charges`.
- Models: `MessMenu`, `MessAttendance`.
- Routes: extend hostel area (`routes/web.php:101-104`) — `/hostels/mess/*`.
- Controller: extend `HostelController` or new `MessController`.
- Views: `resources/views/facilities/*` menu + attendance.
- Integrates with existing `hostel_rooms`/`room_allotments`.
**Acceptance:**
- Warden publishes a weekly menu visible to hostel students.
- Daily mess attendance can be recorded per meal.
- Monthly mess charge computed from attendance (configurable rate).
- Only hostel-allotted students appear in mess lists.
**Effort:** L.

---

_All specs are grounded in the code as read; file paths and table/column names cite where each touchpoint lives. No application code, migrations, or data were modified. Cross-cutting infra (queue worker for F2/F11, encrypted-cast credentials for F3/F6, mail/SMTP for F1/F2) should be provisioned once and shared._
