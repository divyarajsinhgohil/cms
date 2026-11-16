# Harivandana College Management System

A web-based **College Management System** for Harivandana College (affiliated to Saurashtra University), built as an MCA Semester 1 project.

It runs the day-to-day work of a college in one place: admissions, students, staff, attendance, fees, exams and results, timetables, study material, notices, hostel, leave, complaints, certificates, and WhatsApp messaging.

---

## Screenshots

> Add screenshots here (`docs/screenshots/`): login, management dashboard, student dashboard, fee receipt, marksheet.

---

## Tech stack

| Part | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.2+ |
| Database | MySQL (SQLite works for local testing) |
| Frontend | Blade templates, Bootstrap 5, Font Awesome |
| PDFs | barryvdh/laravel-dompdf (receipts, marksheets, ID cards, bonafide) |
| API | Laravel Sanctum token API (`routes/api.php`) for a future mobile app |
| Messaging | Meta WhatsApp Business Cloud API |
| Languages | English, Gujarati, Hindi (public pages) |

---

## Roles

There are three roles. Every action is checked on the **server** (route middleware `role:`), not only hidden in the menu.

| Feature | Management | Staff | Student |
|---|:-:|:-:|:-:|
| Departments, courses, subjects, staff records | ✅ | ❌ | ❌ |
| Fees, receipts | ✅ | ❌ | own (dashboard) |
| Admissions: approve / reject applications | ✅ | ❌ | ❌ |
| Student directory | ✅ | ✅ | ❌ |
| Attendance marking | ✅ | ✅ | own (dashboard) |
| Enter exam marks | ✅ | ✅ | ❌ |
| Marksheet | ✅ | ✅ | own only |
| Upload study material, grade assignments | ✅ | ✅ | ❌ |
| Submit assignments | ❌ | ❌ | ✅ |
| Post notices | ✅ | ✅ | ❌ |
| Apply for leave | ✅ | ✅ | ❌ |
| Approve leave, update complaint status | ✅ | ❌ | ❌ |
| Hostel rooms and allotment | ✅ | ❌ | ❌ |
| WhatsApp campaigns, broadcasts, site settings | ✅ | ❌ | ❌ |

---

## Modules

- **Login**: by email or mobile number. Shared phone numbers (e.g. siblings) get an account chooser. A forced password change applies on first login. Students marked "Left" cannot log in.
- **Dashboards**: separate dashboards for management (students, staff, fee collection, pending applications), staff, and students (attendance trend, CGPA, fee status).
- **Online admission**: public `/apply` form; management approves or rejects, and approval creates the student account.
- **Bulk student import/export**: CSV.
- **Sections**: split a class into A/B sections evenly and assign a class teacher.
- **Attendance**: per subject, per day; low-attendance report; PDF export.
- **Fees**: fee structure per course and semester, payment collection, receipt with PDF.
- **Exams**: internal (40) + external (60) marks, grade and grade point on a 10-point scale, credit-weighted CGPA, marksheet PDF, result hard-copy collection register.
- **Timetable**: manual slots, auto-generate, clash detection, date-specific changes.
- **Study material and assignments**: upload (max 10 MB, file types checked), student submission, staff grading.
- **Notices**: targeted by audience, course and semester, with priority.
- **Hostel**: rooms, allotment, outpass.
- **Leave and complaints**: apply, then management approves or resolves.
- **Certificates**: ID card and bonafide certificate (HTML + PDF).
- **WhatsApp**: templates, campaigns (sent in the background by a queue worker), automatic alerts, delivery logs.
- **Settings**: college name, logo, contact details, academic year, WhatsApp credentials.

---

## Grading scale

Total marks are out of 100 (internal 40 + external 60). Defined in `app/Models/ExamMark.php`.

| Total | Grade | Grade point |
|---|---|---|
| 90 - 100 | O | 10 |
| 80 - 89 | A+ | 9 |
| 70 - 79 | A | 8 |
| 60 - 69 | B+ | 7 |
| 50 - 59 | B | 6 |
| 40 - 49 | C | 5 |
| below 40 | F | 0 |

**CGPA** = sum of (grade point x subject credits) / sum of subject credits.

---

## Setup (local)

Requirements: PHP 8.2+, Composer, MySQL (or SQLite), Node.js (optional).

```bash
git clone https://github.com/divyarajsinhgohil/cms.git
```

```bash
cd cms
```

```bash
composer install
```

```bash
cp .env.example .env
```

```bash
php artisan key:generate
```

Edit `.env` and set your database (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`). Then create the tables and demo data:

```bash
php artisan migrate --seed
```

```bash
php artisan storage:link
```

```bash
php artisan serve
```

Open http://127.0.0.1:8000.

WhatsApp campaigns and alerts are sent by a background worker. Run this in a second terminal:

```bash
php artisan queue:work
```

### Demo accounts

All demo accounts use the password `password`. The quick-fill demo buttons on the login page only appear when `APP_ENV=local`.

| Role | Email |
|---|---|
| Management | admin@cms.edu |
| Staff | faculty@cms.edu |
| Student | student@cms.edu |

**Change these passwords before putting the app on a real server.** New passwords must be at least 8 characters and contain letters and numbers.

---

## Tests

```bash
php artisan test
```

The feature tests (`tests/Feature`) run against a fresh in-memory SQLite database seeded with the demo data. They cover:

- login by email and phone, rejection of login by name, rate limiting, blocking students who left, forced password change, password strength
- role access: students cannot enter marks, grade assignments, post notices, edit departments or launch campaigns; staff cannot approve leave or open management pages
- students can only see their own marksheet
- WhatsApp token stored encrypted and never shown on the settings page
- campaigns are queued, not sent inside the web request
- every page loads for each role (smoke test)
- 10-point grade bands and credit-weighted CGPA
- the API: token login, role checks, rate limiting

---

## Security notes

- Login is rate limited (5 tries per minute per account + IP, 20 per minute per IP).
- Every write route has a server-side role check.
- The WhatsApp access token is encrypted in the database with `APP_KEY`.
- Uploaded files are checked for type and size.

---

## Project structure

```
app/Http/Controllers      Web controllers (one per module)
app/Http/Controllers/Api  Sanctum API controllers
app/Http/Middleware       EnsureUserHasRole (the `role:` middleware)
app/Jobs                  Queue jobs (alerts, WhatsApp campaigns)
app/Services              WhatsApp, timetable clash check, student import, alerts
database/migrations       Database schema
database/seeders          Demo data
resources/views           Blade templates
routes/web.php            Web routes and role rules
routes/api.php            API routes
tests/Feature             Feature tests
docs/                     Feature audit, backlog, theme guide
```
