@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Academic Departments Master</h3>
        <p class="text-muted mb-0">Manage college departments (Commerce, Computer Applications, Engineering, Management, etc.).</p>
    </div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addDeptModal">
        <i class="fa-solid fa-building-columns me-1"></i> Add New Department
    </button>
</div>

<!-- Summary Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Departments</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['totalDepartments'] }}</div>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Degree Courses</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['totalCourses'] }}</div>
                </div>
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Total Students</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['totalStudents'] }}</div>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Total Faculty</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['totalStaff'] }}</div>
                </div>
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <form action="{{ route('academics.departments') }}" method="GET" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Search department name, code, or HOD..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-magnifying-glass me-1"></i> Search</button>
                @if(request('search'))
                    <a href="{{ route('academics.departments') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Departments Master Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Dept Code</th>
                        <th>Department Name</th>
                        <th>Head of Department (HOD)</th>
                        <th>Associated Courses</th>
                        <th>Faculty Staff</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $dept)
                    <tr>
                        <td><span class="badge bg-dark font-monospace fs-7">{{ $dept->code }}</span></td>
                        <td class="fw-bold text-dark">{{ $dept->name }}</td>
                        <td>{{ $dept->head_of_department ?? 'Not Assigned' }}</td>
                        <td>
                            <a href="{{ route('academics.courses', ['department_id' => $dept->id]) }}" class="badge bg-primary-subtle text-primary border border-primary-subtle fs-7 text-decoration-none">
                                {{ $dept->courses_count }} Courses
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('academics.staff', ['department_id' => $dept->id]) }}" class="badge bg-success-subtle text-success border border-success-subtle fs-7 text-decoration-none">
                                {{ $dept->staff_count }} Faculty
                            </a>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editDeptModal{{ $dept->id }}">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                </button>
                                <form action="{{ route('academics.departments.destroy', $dept->id) }}" method="POST" data-confirm="Delete department {{ $dept->name }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal: Edit Department -->
                    <div class="modal fade" id="editDeptModal{{ $dept->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('academics.departments.update', $dept->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Department: {{ $dept->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Department Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ $dept->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Department Short Code</label>
                                            <input type="text" name="code" class="form-control font-monospace text-uppercase" value="{{ $dept->code }}" oninput="this.value = this.value.toUpperCase()" maxlength="20" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Head of Department (HOD)</label>
                                            <input type="text" name="head_of_department" class="form-control" value="{{ $dept->head_of_department }}">
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
                            <i class="fa-solid fa-building-circle-xmark display-5 mb-2 text-secondary"></i>
                            <p class="mb-0 fw-semibold">No departments found matching your search.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add Department -->
<div class="modal fade" id="addDeptModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('academics.departments.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-building-columns text-primary me-2"></i> Add New Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Department Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Department of Commerce & Finance" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Department Code</label>
                        <input type="text" name="code" class="form-control font-monospace text-uppercase" placeholder="e.g. DOC, MCA, CSE, MBA" oninput="this.value = this.value.toUpperCase()" maxlength="20" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Head of Department (HOD)</label>
                        <input type="text" name="head_of_department" class="form-control" placeholder="e.g. Dr. Warren Buffett">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Create Department</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
