@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Class Sections</h3>
        <p class="text-muted mb-0">Create divisions (A, B, C...) per stream & semester with a seat capacity, then auto-divide students equally.</p>
    </div>
    @if($courseId && $semester)
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#newSectionModal">
        <i class="fa-solid fa-plus me-1"></i> New Section
    </button>
    @endif
</div>

<!-- Stream & Semester Picker -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <form action="{{ route('sections.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold small text-muted">Stream / Degree Program</label>
                <select name="course_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Choose a stream...</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ (string) $courseId === (string) $course->id ? 'selected' : '' }}>
                            {{ $course->name }} ({{ $course->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted">Semester</label>
                <select name="semester" class="form-select" onchange="this.form.submit()">
                    <option value="">Choose a semester...</option>
                    @for($s = 1; $s <= 8; $s++)
                        <option value="{{ $s }}" {{ (string) $semester === (string) $s ? 'selected' : '' }}>Semester {{ $s }}</option>
                    @endfor
                </select>
            </div>
        </form>
    </div>
</div>

@if($courseId && $semester)
    @php $selectedCourse = $courses->firstWhere('id', (int) $courseId); @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="text-muted small fw-bold text-uppercase">Active Students</div>
                <div class="fs-2 fw-bold text-dark mt-1">{{ $totalActiveCount }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="text-muted small fw-bold text-uppercase">Unassigned</div>
                <div class="fs-2 fw-bold {{ $unassignedCount > 0 ? 'text-danger' : 'text-success' }} mt-1">{{ $unassignedCount }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card d-flex flex-column justify-content-center">
                <form action="{{ route('sections.divide_equally') }}" method="POST" data-confirm="Divide all {{ $totalActiveCount }} active students alphabetically across the sections below, up to each section's capacity?">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $courseId }}">
                    <input type="hidden" name="semester" value="{{ $semester }}">
                    <button type="submit" class="btn btn-success w-100 fw-bold" {{ $sections->isEmpty() ? 'disabled' : '' }}>
                        <i class="fa-solid fa-scale-balanced me-1"></i> Divide Equally
                    </button>
                </form>
                @if($sections->isEmpty())
                    <div class="form-text fs-7 text-center mt-1">Create a section first</div>
                @endif
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="fw-bold mb-0"><i class="fa-solid fa-chalkboard text-primary me-2"></i> Sections for {{ optional($selectedCourse)->name }} &mdash; Semester {{ $semester }}</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th></th>
                            <th>Section</th>
                            <th>Class Teacher</th>
                            <th>Capacity</th>
                            <th>Students Assigned</th>
                            <th>Occupancy</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sections as $section)
                        <tr class="cms-section-toggle" data-bs-toggle="collapse" data-bs-target="#sectionRoster{{ $section->id }}" style="cursor: pointer;" role="button">
                            <td class="text-muted"><i class="fa-solid fa-chevron-down"></i></td>
                            <td><span class="badge bg-primary fs-6 px-3 py-2">{{ $section->name }}</span></td>
                            <td>
                                @if($section->classTeacher)
                                    <span class="fw-semibold"><i class="fa-solid fa-chalkboard-user text-primary me-1"></i> {{ optional($section->classTeacher->user)->name }}</span>
                                @else
                                    <span class="text-muted fs-7">Not assigned</span>
                                @endif
                            </td>
                            <td>{{ $section->capacity }}</td>
                            <td>{{ $section->students_count }}</td>
                            <td style="min-width: 160px;">
                                @php $pct = $section->capacity > 0 ? min(100, round(($section->students_count / $section->capacity) * 100)) : 0; @endphp
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar {{ $section->students_count > $section->capacity ? 'bg-danger' : 'bg-success' }}" style="width: {{ $pct }}%"></div>
                                </div>
                            </td>
                            <td onclick="event.stopPropagation()">
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editSectionModal{{ $section->id }}" title="Edit capacity / class teacher">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <form action="{{ route('sections.destroy', $section->id) }}" method="POST" data-confirm="Delete section {{ $section->name }}? Its students will become unassigned.">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr class="collapse" id="sectionRoster{{ $section->id }}">
                            <td colspan="7" class="p-0 border-0">
                                <div class="bg-light p-3">
                                    @forelse($section->students as $st)
                                        <a href="{{ route('admissions.show', $st->id) }}" class="d-inline-flex align-items-center gap-2 bg-white border rounded-pill px-3 py-1 me-2 mb-2 text-decoration-none">
                                            <span class="badge bg-primary-subtle text-primary rounded-circle">{{ $st->roll_no ?? '—' }}</span>
                                            <span class="fw-semibold text-dark">{{ optional($st->user)->name }}</span>
                                        </a>
                                    @empty
                                        <span class="text-muted fs-7">No students assigned to this section yet.</span>
                                    @endforelse
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fa-solid fa-chalkboard display-5 mb-2 text-secondary"></i>
                                <p class="mb-0 fw-semibold">No sections created yet for this stream & semester.</p>
                            </td>
                        </tr>
                        @endforelse
                        @if($unassignedStudents->isNotEmpty())
                        <tr class="cms-section-toggle" data-bs-toggle="collapse" data-bs-target="#sectionRosterUnassigned" style="cursor: pointer;" role="button">
                            <td class="text-muted"><i class="fa-solid fa-chevron-down"></i></td>
                            <td><span class="badge bg-secondary fs-6 px-3 py-2">Unassigned</span></td>
                            <td></td>
                            <td>—</td>
                            <td>{{ $unassignedStudents->count() }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr class="collapse" id="sectionRosterUnassigned">
                            <td colspan="7" class="p-0 border-0">
                                <div class="bg-light p-3">
                                    @foreach($unassignedStudents as $st)
                                        <a href="{{ route('admissions.show', $st->id) }}" class="d-inline-flex align-items-center gap-2 bg-white border rounded-pill px-3 py-1 me-2 mb-2 text-decoration-none">
                                            <span class="badge bg-primary-subtle text-primary rounded-circle">{{ $st->roll_no ?? '—' }}</span>
                                            <span class="fw-semibold text-dark">{{ optional($st->user)->name }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modals: Edit Section (one per section) -->
    @foreach($sections as $section)
    <div class="modal fade" id="editSectionModal{{ $section->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('sections.update', $section->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Section {{ $section->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Seat Capacity</label>
                            <input type="number" name="capacity" class="form-control" value="{{ $section->capacity }}" min="1" required>
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-bold">Class Teacher</label>
                            <select name="class_teacher_id" class="form-select">
                                <option value="">Not assigned</option>
                                @foreach($eligibleStaff as $staff)
                                    <option value="{{ $staff->id }}" {{ $section->class_teacher_id == $staff->id ? 'selected' : '' }}>
                                        {{ optional($staff->user)->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Modal: New Section -->
    <div class="modal fade" id="newSectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('sections.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $courseId }}">
                    <input type="hidden" name="semester" value="{{ $semester }}">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">New Class Section</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Section Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. A" maxlength="5" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Seat Capacity</label>
                            <input type="number" name="capacity" class="form-control" placeholder="e.g. 60" min="1" required>
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-bold">Class Teacher <span class="text-muted fw-normal">(optional)</span></label>
                            <select name="class_teacher_id" class="form-select">
                                <option value="">Not assigned</option>
                                @foreach($eligibleStaff as $staff)
                                    <option value="{{ $staff->id }}">{{ optional($staff->user)->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Section</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info border-0 shadow-sm rounded-4">
        <i class="fa-solid fa-circle-info me-1"></i> Choose a stream and semester above to view or create its class sections.
    </div>
@endif
@endsection
