@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Faculty & Teacher Master Directory</h3>
        <p class="text-muted mb-0">Manage teaching staff, professor profiles, stream/work fields, qualifications, and pay scales with full Add, Edit, Delete controls.</p>
    </div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addStaffModal">
        <i class="fa-solid fa-user-plus me-1"></i> Add New Faculty / Teacher
    </button>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <form action="{{ route('academics.staff') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="department_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Academic Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }} ({{ $dept->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="work_field" class="form-select" onchange="this.form.submit()">
                    <option value="">All Work Fields / Streams</option>
                    <option value="Commerce" {{ request('work_field') === 'Commerce' ? 'selected' : '' }}>Commerce & Finance</option>
                    <option value="Computer Science" {{ request('work_field') === 'Computer Science' ? 'selected' : '' }}>Computer Science / IT</option>
                    <option value="Management" {{ request('work_field') === 'Management' ? 'selected' : '' }}>Management Studies</option>
                    <option value="Engineering" {{ request('work_field') === 'Engineering' ? 'selected' : '' }}>Engineering & Tech</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search teacher name, email, employee ID..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-magnifying-glass me-1"></i> Filter</button>
                @if(request()->hasAny(['department_id', 'work_field', 'search']))
                    <a href="{{ route('academics.staff') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Faculty Directory Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Employee ID</th>
                        <th>Faculty / Teacher</th>
                        <th>Department</th>
                        <th>Work Field / Stream</th>
                        <th>Assigned Streams</th>
                        <th>Designation</th>
                        <th>Qualification & Address</th>
                        <th>Pay Scale</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffMembers as $staff)
                    <tr>
                        <td>
                            <span class="badge bg-primary-subtle text-primary font-monospace border border-primary-subtle px-2 py-1 fs-7">
                                {{ $staff->employee_id }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ optional($staff->user)->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(optional($staff->user)->name) }}" class="rounded-circle border" width="42" height="42">
                                <div>
                                    <div class="fw-bold text-dark">{{ optional($staff->user)->name }}</div>
                                    <div class="text-muted fs-7">{{ optional($staff->user)->email }} | {{ optional($staff->user)->phone }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ optional($staff->department)->name }}</span></td>
                        <td>
                            <span class="badge bg-info-subtle text-info">
                                <i class="fa-solid fa-briefcase me-1"></i> {{ $staff->work_field ?? 'General Academic' }}
                            </span>
                        </td>
                        <td>
                            @forelse($staff->courses as $c)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-1">{{ $c->code }}</span>
                            @empty
                                <span class="text-muted fs-7">All Streams (unrestricted)</span>
                            @endforelse
                        </td>
                        <td class="fw-semibold text-primary">{{ $staff->designation }}</td>
                        <td>
                            <div class="fw-bold fs-7">{{ $staff->qualification ?? 'Degree Not Specified' }}</div>
                            <div class="text-muted fs-7"><i class="fa-solid fa-location-dot me-1"></i> {{ Str::limit($staff->address ?? 'Address N/A', 25) }}</div>
                        </td>
                        <td>
                            @if($staff->salary)
                                <span class="fw-bold text-success font-monospace fs-7">₹{{ number_format($staff->salary, 2) }}</span>
                            @else
                                <span class="text-muted fs-7">Not set</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editStaffModal{{ $staff->id }}">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                </button>
                                <form action="{{ route('academics.staff.destroy', $staff->id) }}" method="POST" data-confirm="Delete teacher profile {{ optional($staff->user)->name }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal: Edit Staff -->
                    <div class="modal fade" id="editStaffModal{{ $staff->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form action="{{ route('academics.staff.update', $staff->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Faculty Member: {{ optional($staff->user)->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Photo Upload Section -->
                                        <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3 border">
                                            <img id="staffPhotoPreview{{ $staff->id }}" src="{{ optional($staff->user)->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(optional($staff->user)->name) }}" class="rounded-circle border" width="72" height="72" style="object-fit: cover;">
                                            <div class="flex-grow-1">
                                                <label class="form-label fw-bold mb-1">Faculty Photo</label>
                                                <input type="file" name="photo" accept="image/*" class="form-control" onchange="cmsPreviewPhoto(this, 'staffPhotoPreview{{ $staff->id }}')">
                                                <div class="form-text fs-7">JPG or PNG, max 2MB. Leave empty to keep the current photo.</div>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Full Teacher Name</label>
                                                <input type="text" name="name" class="form-control" value="{{ optional($staff->user)->name }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Mobile Phone</label>
                                                <input type="text" name="phone" class="form-control" value="{{ optional($staff->user)->phone }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Department</label>
                                                <select name="department_id" class="form-select" required>
                                                    @foreach($departments as $dept)
                                                        <option value="{{ $dept->id }}" {{ $staff->department_id == $dept->id ? 'selected' : '' }}>
                                                            {{ $dept->name }} ({{ $dept->code }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Work Field / Stream</label>
                                                <select name="work_field" class="form-select">
                                                    <option value="Commerce" {{ $staff->work_field === 'Commerce' ? 'selected' : '' }}>Commerce & Finance</option>
                                                    <option value="Computer Science" {{ $staff->work_field === 'Computer Science' ? 'selected' : '' }}>Computer Science / IT</option>
                                                    <option value="Management" {{ $staff->work_field === 'Management' ? 'selected' : '' }}>Management Studies</option>
                                                    <option value="Engineering" {{ $staff->work_field === 'Engineering' ? 'selected' : '' }}>Engineering & Tech</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Designation</label>
                                                <select name="designation" class="form-select" required>
                                                    <option value="Professor & HOD" {{ $staff->designation === 'Professor & HOD' ? 'selected' : '' }}>Professor & HOD</option>
                                                    <option value="Associate Professor" {{ $staff->designation === 'Associate Professor' ? 'selected' : '' }}>Associate Professor</option>
                                                    <option value="Assistant Professor" {{ $staff->designation === 'Assistant Professor' ? 'selected' : '' }}>Assistant Professor</option>
                                                    <option value="Guest Faculty" {{ $staff->designation === 'Guest Faculty' ? 'selected' : '' }}>Guest Faculty</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Academic Qualification</label>
                                                <input type="text" name="qualification" class="form-control" value="{{ $staff->qualification }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Salary / Monthly Pay Scale (₹)</label>
                                                <input type="number" name="salary" class="form-control" value="{{ $staff->salary }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Joining Date</label>
                                                <input type="date" name="joining_date" class="form-control" value="{{ $staff->joining_date }}">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-bold">Residential Address</label>
                                                <textarea name="address" class="form-control" rows="2">{{ $staff->address }}</textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-bold">Assigned Streams (Student Directory / Attendance / Exams will be scoped to these)</label>
                                                @php $assignedIds = $staff->courses->pluck('id')->all(); @endphp
                                                <select name="course_ids[]" multiple class="form-select cms-searchable-select">
                                                    @foreach($courses as $course)
                                                        <option value="{{ $course->id }}" {{ in_array($course->id, $assignedIds) ? 'selected' : '' }}>{{ $course->name }} ({{ $course->code }})</option>
                                                    @endforeach
                                                </select>
                                                <div class="form-text fs-7">Search and select one or more streams. Leave empty to keep this faculty member unrestricted (sees all streams).</div>
                                            </div>
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
                        <td colspan="9" class="text-center text-muted py-5">No faculty members found matching filter.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($staffMembers->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted fs-7">Showing {{ $staffMembers->firstItem() }}-{{ $staffMembers->lastItem() }} of {{ $staffMembers->total() }} faculty members</span>
            {{ $staffMembers->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal: Add Staff -->
<div class="modal fade" id="addStaffModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('academics.staff.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-tie text-primary me-2"></i> Add Faculty / Teacher Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2 fs-7 mb-3">
                        <i class="fa-solid fa-key me-1"></i> <strong>First-Time Password:</strong> defaults to the teacher's full name, lowercase with no spaces (e.g. "Prof. Warren Buffett" &rarr; <code>prof.warrenbuffett</code>). They'll be asked to set their own password the first time they log in.
                    </div>

                    <!-- Photo Upload Section -->
                    <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3 border">
                        <img id="newStaffPhotoPreview" src="https://ui-avatars.com/api/?name=Teacher&background=e2e8f0&color=64748b" class="rounded-circle border" width="72" height="72" style="object-fit: cover;">
                        <div class="flex-grow-1">
                            <label class="form-label fw-bold mb-1">Faculty Photo</label>
                            <input type="file" name="photo" accept="image/*" class="form-control" onchange="cmsPreviewPhoto(this, 'newStaffPhotoPreview')">
                            <div class="form-text fs-7">JPG or PNG, max 2MB. Leave empty to use an auto-generated avatar.</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Full Teacher Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Prof. Warren Buffett" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Official Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="teacher@cms.edu" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Mobile Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="+91 98765 43211" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Department</label>
                            <select name="department_id" class="form-select" required>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Work Field / Stream</label>
                            <select name="work_field" class="form-select">
                                <option value="Commerce">Commerce & Finance</option>
                                <option value="Computer Science" selected>Computer Science / IT</option>
                                <option value="Management">Management Studies</option>
                                <option value="Engineering">Engineering & Tech</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Designation</label>
                            <select name="designation" class="form-select" required>
                                <option value="Professor & HOD">Professor & HOD</option>
                                <option value="Associate Professor">Associate Professor</option>
                                <option value="Assistant Professor" selected>Assistant Professor</option>
                                <option value="Guest Faculty">Guest Faculty</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Academic Qualification</label>
                            <input type="text" name="qualification" class="form-control" placeholder="e.g. Ph.D. in Commerce, M.Com, MCA, M.Tech">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Salary / Monthly Pay Scale (₹)</label>
                            <input type="number" name="salary" class="form-control" placeholder="75000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Joining Date</label>
                            <input type="date" name="joining_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Residential Address</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Full permanent address..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Assigned Streams (Student Directory / Attendance / Exams will be scoped to these)</label>
                            <select name="course_ids[]" multiple class="form-select cms-searchable-select">
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }} ({{ $course->code }})</option>
                                @endforeach
                            </select>
                            <div class="form-text fs-7">Search and select one or more streams. Leave empty to keep this faculty member unrestricted (sees all streams).</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Create Teacher Account & Employee ID</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
