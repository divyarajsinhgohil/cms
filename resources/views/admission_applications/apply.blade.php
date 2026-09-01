<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Application - {{ $siteSettings->site_name ?? 'College Management System' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bs-primary: #6d3fa0;
            --bs-primary-rgb: 109, 63, 160;
        }
        .btn-primary {
            --bs-btn-bg: #6d3fa0;
            --bs-btn-border-color: #6d3fa0;
            --bs-btn-hover-bg: #5c3488;
            --bs-btn-hover-border-color: #562f7e;
            --bs-btn-active-bg: #562f7e;
            --bs-btn-active-border-color: #502c75;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
        }
        .brand-strip {
            height: 5px;
            background: linear-gradient(90deg, #f5841f 0%, #e6007e 25%, #6d3fa0 50%, #2e5fa3 75%, #2ba84a 100%);
        }
        .apply-header {
            background: linear-gradient(135deg, #4a1f6e 0%, #2e5fa3 100%);
            color: #fff;
            padding: 2.5rem 1rem;
            text-align: center;
        }
        .form-wrap { max-width: 900px; margin: -3rem auto 3rem; padding: 0 1rem; }
        .section-card { background: #fff; border-radius: 1rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08); padding: 1.75rem; margin-bottom: 1.5rem; }
        .section-title { font-weight: 700; color: #4a1f6e; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; }
        .photo-preview { width: 100px; height: 100px; object-fit: cover; border-radius: 0.75rem; border: 2px dashed #cbd5e1; }
    </style>
</head>
<body>
    <div class="brand-strip"></div>

    <div class="apply-header">
        @if(!empty($siteSettings?->logo_url))
            <img src="{{ $siteSettings->logo_url }}" alt="Logo" height="56" class="rounded mb-2">
        @else
            <i class="fa-solid fa-graduation-cap text-warning display-4 mb-2"></i>
        @endif
        <h2 class="fw-bold mb-1">{{ $siteSettings->site_name ?? 'College Management System' }}</h2>
        <p class="mb-0 opacity-75">{{ $siteSettings->affiliation ?? 'Online Admission Application Form' }}</p>
    </div>

    <div class="form-wrap">
        @if(session('success'))
            <div class="alert alert-success shadow-sm border-0 rounded-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger shadow-sm border-0 rounded-4" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> Please fix the errors below and resubmit.
                <ul class="mb-0 mt-2 fs-7">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="section-card text-center">
            <h4 class="fw-bold mb-1">Online Admission Application</h4>
            <p class="text-muted mb-0">Fill in your details carefully. Your application will be reviewed by the admissions office, and you will be enrolled once approved.</p>
        </div>

        <form action="{{ route('admission-applications.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Personal Details -->
            <div class="section-card">
                <div class="section-title"><i class="fa-solid fa-user"></i> Personal Details</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Surname</label>
                        <input type="text" name="surname" class="form-control text-uppercase" oninput="this.value = this.value.toUpperCase()" value="{{ old('surname') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">First Name</label>
                        <input type="text" name="first_name" class="form-control text-uppercase" oninput="this.value = this.value.toUpperCase()" value="{{ old('first_name') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Last Name</label>
                        <input type="text" name="last_name" class="form-control text-uppercase" oninput="this.value = this.value.toUpperCase()" value="{{ old('last_name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Father's Name</label>
                        <input type="text" name="father_name" class="form-control text-uppercase" oninput="this.value = this.value.toUpperCase()" value="{{ old('father_name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Mother's Name</label>
                        <input type="text" name="mother_name" class="form-control text-uppercase" oninput="this.value = this.value.toUpperCase()" value="{{ old('mother_name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Gender</label>
                        <select name="gender" class="form-select" required>
                            <option value="" disabled selected>Select Gender</option>
                            <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Aadhar Card Number</label>
                        <input type="text" name="aadhar_number" class="form-control" maxlength="12" pattern="\d{12}" placeholder="12-digit Aadhar number" value="{{ old('aadhar_number') }}" required>
                    </div>
                </div>
            </div>

            <!-- Photo & Documents -->
            <div class="section-card">
                <div class="section-title"><i class="fa-solid fa-id-badge"></i> Photo & Documents</div>
                <div class="row g-3 align-items-start">
                    <div class="col-md-6 d-flex gap-3 align-items-center">
                        <img id="photoPreview" src="https://placehold.co/100x100?text=Photo" class="photo-preview">
                        <div class="flex-grow-1">
                            <label class="form-label fw-bold">Passport Size Photo</label>
                            <input type="file" name="photo" accept="image/*" class="form-control" onchange="cmsPreviewPhoto(this, 'photoPreview')" required>
                            <div class="form-text fs-7">JPG or PNG, max 10MB.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Aadhar Card Photo / Scan</label>
                        <input type="file" name="aadhar_photo" accept=".jpg,.jpeg,.png,.pdf" class="form-control" required>
                        <div class="form-text fs-7">JPG, PNG, or PDF, max 10MB.</div>
                    </div>
                </div>
            </div>

            <!-- Category -->
            <div class="section-card">
                <div class="section-title"><i class="fa-solid fa-list-check"></i> Category</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Category</label>
                        <select name="category" id="categorySelect" class="form-select" onchange="document.getElementById('casteCertWrap').classList.toggle('d-none', this.value === 'General')" required>
                            <option value="General" {{ old('category', 'General') === 'General' ? 'selected' : '' }}>General</option>
                            <option value="OBC" {{ old('category') === 'OBC' ? 'selected' : '' }}>OBC</option>
                            <option value="SC" {{ old('category') === 'SC' ? 'selected' : '' }}>SC</option>
                            <option value="ST" {{ old('category') === 'ST' ? 'selected' : '' }}>ST</option>
                        </select>
                    </div>
                    <div class="col-md-6 {{ in_array(old('category'), ['SC','ST','OBC']) ? '' : 'd-none' }}" id="casteCertWrap">
                        <label class="form-label fw-bold">Caste Certificate</label>
                        <input type="file" name="caste_certificate" accept=".jpg,.jpeg,.png,.pdf" class="form-control">
                        <div class="form-text fs-7">Required for OBC / SC / ST category. Max 10MB.</div>
                    </div>
                </div>
            </div>

            <!-- Contact -->
            <div class="section-card">
                <div class="section-title"><i class="fa-solid fa-phone"></i> Contact Details</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Your Mobile Number</label>
                        <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Father's Mobile</label>
                        <input type="text" name="father_mobile" class="form-control" value="{{ old('father_mobile') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Mother's Mobile</label>
                        <input type="text" name="mother_mobile" class="form-control" value="{{ old('mother_mobile') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Email Address</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="section-card">
                <div class="section-title"><i class="fa-solid fa-location-dot"></i> Address</div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">Home Address</label>
                        <textarea name="home_address" id="homeAddress" class="form-control" rows="2" required oninput="syncPermanentAddress()">{{ old('home_address') }}</textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="sameAsHome" onchange="syncPermanentAddress()">
                            <label class="form-check-label fs-7" for="sameAsHome">Permanent address is the same as home address</label>
                        </div>
                        <label class="form-label fw-bold">Permanent Address</label>
                        <textarea name="permanent_address" id="permanentAddress" class="form-control" rows="2" required>{{ old('permanent_address') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Hostel -->
            <div class="section-card">
                <div class="section-title"><i class="fa-solid fa-hotel"></i> Hostel Accommodation</div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="wants_hostel" value="1" id="wantsHostel" {{ old('wants_hostel') ? 'checked' : '' }} onchange="document.getElementById('hostelPrefWrap').classList.toggle('d-none', !this.checked)">
                    <label class="form-check-label" for="wantsHostel">I require hostel accommodation</label>
                </div>
                <div id="hostelPrefWrap" class="{{ old('wants_hostel') ? '' : 'd-none' }}">
                    <label class="form-label fw-bold">Preferred Hostel / Block</label>
                    <input type="text" name="hostel_preference" class="form-control" placeholder="e.g. Boys Hostel Block A" value="{{ old('hostel_preference') }}">
                </div>
            </div>

            <!-- Admission -->
            <div class="section-card">
                <div class="section-title"><i class="fa-solid fa-graduation-cap"></i> Stream / Degree Program</div>
                <select name="course_id" class="form-select" required>
                    <option value="" disabled selected>Select the stream you are applying for</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ (string) old('course_id') === (string) $course->id ? 'selected' : '' }}>
                            {{ $course->name }} ({{ $course->code }}) &mdash; {{ optional($course->department)->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="section-card text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold">
                    <i class="fa-solid fa-paper-plane me-2"></i> Submit Application
                </button>
                <div class="mt-3">
                    <a href="{{ route('login') }}" class="text-muted fs-7 text-decoration-none"><i class="fa-solid fa-arrow-left me-1"></i> Back to login</a>
                </div>
            </div>
        </form>
    </div>

    <script>
        function cmsPreviewPhoto(input, previewId) {
            if (!input.files || !input.files[0]) return;
            const reader = new FileReader();
            reader.onload = (e) => document.getElementById(previewId).src = e.target.result;
            reader.readAsDataURL(input.files[0]);
        }

        function syncPermanentAddress() {
            const same = document.getElementById('sameAsHome').checked;
            const permanent = document.getElementById('permanentAddress');
            if (same) {
                permanent.value = document.getElementById('homeAddress').value;
                permanent.setAttribute('readonly', 'readonly');
            } else {
                permanent.removeAttribute('readonly');
            }
        }
    </script>
</body>
</html>
