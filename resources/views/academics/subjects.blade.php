@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Subjects & Faculty Mapping Master</h3>
        <p class="text-muted mb-0">Manage course subjects across semesters and assign faculty teachers to each subject.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('academics.syllabi') }}" class="btn btn-outline-danger shadow-sm fw-semibold">
            <i class="fa-solid fa-file-pdf me-1"></i> Upload Semester Syllabi
        </a>
        <button class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
            <i class="fa-solid fa-book-open me-1"></i> Add New Subject
        </button>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <form action="{{ route('academics.subjects') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="course_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Degree Courses / Streams</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->name }} ({{ $course->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="semester" class="form-select" onchange="this.form.submit()">
                    <option value="">All Semesters</option>
                    @for($s = 1; $s <= 8; $s++)
                        <option value="{{ $s }}" {{ request('semester') == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search subject title or code..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-magnifying-glass me-1"></i> Filter</button>
                @if(request()->hasAny(['course_id', 'semester', 'search']))
                    <a href="{{ route('academics.subjects') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Subjects Master Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Subject Code</th>
                        <th>Subject Title</th>
                        <th>Associated Course Program</th>
                        <th>Semester</th>
                        <th>Assigned Faculty Teacher</th>
                        <th>Credits</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $sub)
                    <tr>
                        <td class="ps-4"><span class="badge bg-primary font-monospace fs-7">{{ $sub->code }}</span></td>
                        <td class="fw-bold text-dark">{{ $sub->name }}</td>
                        <td>
                            <span class="badge bg-light text-dark border me-1">{{ optional($sub->course)->code }}</span>
                            <span class="fs-7 text-muted">{{ optional($sub->course)->name }}</span>
                        </td>
                        <td><span class="badge bg-warning-subtle text-dark border border-warning-subtle">Semester {{ $sub->semester }}</span></td>
                        <td>
                            @if($sub->teacher)
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $sub->teacher->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($sub->teacher->name) }}" class="rounded-circle border" width="28" height="28">
                                    <div>
                                        <div class="fw-semibold text-dark fs-7">{{ $sub->teacher->name }}</div>
                                        <div class="text-muted fs-8">{{ optional($sub->teacher->staffProfile)->designation ?? 'Faculty' }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Unassigned</span>
                            @endif
                        </td>
                        <td><span class="badge bg-info-subtle text-info font-monospace">{{ $sub->credits }} Credits</span></td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-1 justify-content-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editSubjectModal{{ $sub->id }}">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                </button>
                                <form action="{{ route('academics.subjects.destroy', $sub->id) }}" method="POST" data-confirm="Delete subject {{ $sub->name }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal: Edit Subject -->
                    <div class="modal fade" id="editSubjectModal{{ $sub->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('academics.subjects.update', $sub->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Subject & Assign Faculty</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Course Program / Stream</label>
                                            <select name="course_id" class="form-select" required>
                                                @foreach($courses as $c)
                                                    <option value="{{ $c->id }}" {{ $sub->course_id == $c->id ? 'selected' : '' }}>
                                                        {{ $c->name }} ({{ $c->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Semester</label>
                                            <select name="semester" class="form-select" required>
                                                @for($i=1; $i<=8; $i++)
                                                    <option value="{{ $i }}" {{ $sub->semester == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Subject Title</label>
                                            <input type="text" name="name" class="form-control" value="{{ $sub->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Subject Code</label>
                                            <input type="text" name="code" class="form-control font-monospace" value="{{ $sub->code }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Assigned Faculty Teacher</label>
                                            <select name="teacher_id" class="form-select">
                                                <option value="">-- Select Teaching Faculty Member --</option>
                                                @foreach($facultyUsers as $fac)
                                                    <option value="{{ $fac->id }}" {{ $sub->teacher_id == $fac->id ? 'selected' : '' }}>
                                                        {{ $fac->name }} ({{ optional($fac->staffProfile)->designation ?? ucfirst($fac->role) }}) - {{ $fac->email }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Credits</label>
                                            <input type="number" name="credits" class="form-control" value="{{ $sub->credits }}" required>
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
                        <td colspan="7" class="text-center text-muted py-5">No subjects found matching filter.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add Subject -->
<div class="modal fade" id="addSubjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('academics.subjects.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-book-open text-primary me-2"></i> Add Subject & Map Faculty</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Course Program / Stream</label>
                        <select name="course_id" class="form-select" required>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Semester</label>
                        <select name="semester" class="form-select" required>
                            @for($i=1; $i<=8; $i++)
                                <option value="{{ $i }}">Semester {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Subject Title</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Data Structures & Algorithms" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Subject Code</label>
                        <input type="text" name="code" class="form-control font-monospace" placeholder="e.g. BCA301, MCA102, CSE401" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Assign Faculty Teacher</label>
                        <select name="teacher_id" class="form-select">
                            <option value="">-- Select Teaching Faculty Member --</option>
                            @foreach($facultyUsers as $fac)
                                <option value="{{ $fac->id }}">
                                    {{ $fac->name }} ({{ optional($fac->staffProfile)->designation ?? ucfirst($fac->role) }}) - {{ $fac->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Academic Credits</label>
                        <input type="number" name="credits" class="form-control" value="4" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Create Subject & Assign Faculty</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
