@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Degree Course Programs Master</h3>
        <p class="text-muted mb-0">Manage degree programs (MCA, BCA, B.Com, MBA, B.Tech CSE) with Add, Edit, Delete, and Filters.</p>
    </div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addCourseModal">
        <i class="fa-solid fa-graduation-cap me-1"></i> Add Course Program
    </button>
</div>

<!-- Summary Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Degree Programs</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['totalCourses'] }}</div>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Departments Covered</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['totalDepartments'] }}</div>
                </div>
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Subject Modules</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['totalSubjects'] }}</div>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fa-solid fa-book-open"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Enrolled Students</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['totalStudents'] }}</div>
                </div>
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <form action="{{ route('academics.courses') }}" method="GET" class="row g-3">
            <div class="col-md-5">
                <select name="department_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Academic Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }} ({{ $dept->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search course title or code..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-magnifying-glass me-1"></i> Filter</button>
                @if(request()->hasAny(['department_id', 'search']))
                    <a href="{{ route('academics.courses') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Courses Master Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Course Code</th>
                        <th>Degree Course Program</th>
                        <th>Associated Department</th>
                        <th>Program Duration</th>
                        <th>Subject Modules</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                    <tr>
                        <td><span class="badge bg-primary-subtle text-primary font-monospace border border-primary-subtle fs-7">{{ $course->code }}</span></td>
                        <td class="fw-bold text-dark">{{ $course->name }}</td>
                        <td><span class="badge bg-light text-dark border">{{ optional($course->department)->name }}</span></td>
                        <td><span class="badge bg-info-subtle text-info">{{ $course->duration_years }} {{ Str::plural('Year', $course->duration_years) }}</span></td>
                        <td>
                            <a href="{{ route('academics.subjects', ['course_id' => $course->id]) }}" class="badge bg-success-subtle text-success border border-success-subtle text-decoration-none">
                                {{ $course->subjects->count() }} Subjects
                            </a>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCourseModal{{ $course->id }}">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                </button>
                                <form action="{{ route('academics.courses.destroy', $course->id) }}" method="POST" data-confirm="Delete course program {{ $course->name }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal: Edit Course -->
                    <div class="modal fade" id="editCourseModal{{ $course->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('academics.courses.update', $course->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Course: {{ $course->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Department</label>
                                            <select name="department_id" class="form-select" required>
                                                @foreach($departments as $dept)
                                                    <option value="{{ $dept->id }}" {{ $course->department_id == $dept->id ? 'selected' : '' }}>
                                                        {{ $dept->name }} ({{ $dept->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Course Program Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ $course->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Course Code</label>
                                            <input type="text" name="code" class="form-control font-monospace text-uppercase" value="{{ $course->code }}" oninput="this.value = this.value.toUpperCase()" maxlength="20" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Duration (Years)</label>
                                            <select name="duration_years" class="form-select" required>
                                                <option value="1" {{ $course->duration_years == 1 ? 'selected' : '' }}>1 Year (Diploma / Certificate)</option>
                                                <option value="2" {{ $course->duration_years == 2 ? 'selected' : '' }}>2 Years (Postgraduate)</option>
                                                <option value="3" {{ $course->duration_years == 3 ? 'selected' : '' }}>3 Years (Undergraduate)</option>
                                                <option value="4" {{ $course->duration_years == 4 ? 'selected' : '' }}>4 Years (Professional B.Tech)</option>
                                                <option value="5" {{ $course->duration_years == 5 ? 'selected' : '' }}>5 Years (Integrated Program)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary fw-bold">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fa-solid fa-graduation-cap display-5 mb-2 text-secondary"></i>
                            <p class="mb-0 fw-semibold">No course programs found matching your filter.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add Course -->
<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('academics.courses.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-graduation-cap text-primary me-2"></i> Add Degree Course Program</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Department</label>
                        <select name="department_id" class="form-select" required>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Course Program Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Bachelor of Commerce (B.Com Hons)" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Course Code</label>
                        <input type="text" name="code" class="form-control font-monospace text-uppercase" placeholder="e.g. BCOM, MCA, BCA, MBA" oninput="this.value = this.value.toUpperCase()" maxlength="20" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Duration (Years)</label>
                        <select name="duration_years" class="form-select" required>
                            <option value="1">1 Year (Diploma / Certificate)</option>
                            <option value="2">2 Years (Postgraduate: MCA, MBA, M.Com)</option>
                            <option value="3" selected>3 Years (Undergraduate: BCA, B.Com, B.Sc)</option>
                            <option value="4">4 Years (Professional: B.Tech CSE, BE)</option>
                            <option value="5">5 Years (Integrated Program)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Create Course Program</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
