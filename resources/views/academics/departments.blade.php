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

<!-- Bulk Delete Bar (shown once at least one department is selected) -->
<div id="deptBulkBar" class="alert alert-danger d-none d-flex justify-content-between align-items-center rounded-4 shadow-sm">
    <span class="fw-bold"><span id="deptBulkCount">0</span> department(s) selected</span>
    <button type="button" class="btn btn-danger btn-sm fw-bold" onclick="cmsOpenDeptBulkDeleteModal()">
        <i class="fa-solid fa-trash me-1"></i> Delete Selected
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
                        <th style="width: 40px;"><input type="checkbox" class="form-check-input" id="deptSelectAll" onchange="cmsToggleAllDepts(this)"></th>
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
                        <td>
                            <input type="checkbox" class="form-check-input dept-checkbox" value="{{ $dept->id }}"
                                data-name="{{ $dept->name }}" data-courses="{{ $dept->courses_count }}"
                                data-staff="{{ $dept->staff_count }}" data-students="{{ $dept->students_count }}"
                                onchange="cmsUpdateDeptBulkBar()">
                        </td>
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
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteDeptModal{{ $dept->id }}">
                                    <i class="fa-solid fa-trash me-1"></i> Delete
                                </button>
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

                    <!-- Modal: Delete Department (type-to-confirm) -->
                    <div class="modal fade" id="deleteDeptModal{{ $dept->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('academics.departments.destroy', $dept->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-header bg-danger-subtle">
                                        <h5 class="modal-title fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i> Permanently Delete Department</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-danger fs-7">
                                            This will permanently delete <strong>{{ $dept->name }}</strong> along with its
                                            <strong>{{ $dept->courses_count }}</strong> course(s),
                                            <strong>{{ $dept->staff_count }}</strong> faculty member(s), and
                                            <strong>{{ $dept->students_count }}</strong> student(s) enrolled under it &mdash;
                                            including their attendance, fee, and exam records. This cannot be undone.
                                        </div>
                                        <label class="form-label fw-bold">Type <span class="font-monospace">{{ $dept->name }}</span> to confirm:</label>
                                        <input type="text" name="confirm_name" class="form-control" autocomplete="off" oninput="this.closest('form').querySelector('.dept-confirm-submit').disabled = (this.value !== @js($dept->name))">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger fw-bold dept-confirm-submit" disabled>
                                            <i class="fa-solid fa-trash me-1"></i> Delete Permanently
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
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

<!-- Modal: Bulk Delete Departments (type-to-confirm) -->
<div class="modal fade" id="deptBulkDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deptBulkDeleteForm" action="{{ route('academics.departments.bulk_destroy') }}" method="POST">
                @csrf
                <div id="deptBulkHiddenInputs"></div>
                <div class="modal-header bg-danger-subtle">
                    <h5 class="modal-title fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i> Permanently Delete Selected Departments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger fs-7">
                        This will permanently delete <strong id="deptBulkNames"></strong> along with all their courses,
                        faculty, and students &mdash; including those students' attendance, fee, and exam records. This cannot be undone.
                    </div>
                    <label class="form-label fw-bold">Type <span class="font-monospace">DELETE</span> to confirm:</label>
                    <input type="text" id="deptBulkConfirmInput" name="confirm_word" class="form-control" autocomplete="off" oninput="document.getElementById('deptBulkSubmitBtn').disabled = (this.value.toUpperCase() !== 'DELETE')">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="deptBulkSubmitBtn" class="btn btn-danger fw-bold" disabled>
                        <i class="fa-solid fa-trash me-1"></i> Delete Selected Permanently
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function cmsToggleAllDepts(source) {
        document.querySelectorAll('.dept-checkbox').forEach(cb => cb.checked = source.checked);
        cmsUpdateDeptBulkBar();
    }

    function cmsUpdateDeptBulkBar() {
        const checked = document.querySelectorAll('.dept-checkbox:checked');
        document.getElementById('deptBulkBar').classList.toggle('d-none', checked.length === 0);
        document.getElementById('deptBulkCount').textContent = checked.length;

        const all = document.querySelectorAll('.dept-checkbox');
        document.getElementById('deptSelectAll').checked = all.length > 0 && checked.length === all.length;
    }

    function cmsOpenDeptBulkDeleteModal() {
        const checked = Array.from(document.querySelectorAll('.dept-checkbox:checked'));
        if (checked.length === 0) return;

        const hiddenContainer = document.getElementById('deptBulkHiddenInputs');
        hiddenContainer.innerHTML = '';
        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'department_ids[]';
            input.value = cb.value;
            hiddenContainer.appendChild(input);
        });

        document.getElementById('deptBulkNames').textContent = checked.map(cb => cb.dataset.name).join(', ');

        const confirmInput = document.getElementById('deptBulkConfirmInput');
        confirmInput.value = '';
        document.getElementById('deptBulkSubmitBtn').disabled = true;

        new bootstrap.Modal(document.getElementById('deptBulkDeleteModal')).show();
    }
</script>
@endsection
