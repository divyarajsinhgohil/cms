@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Study Materials & Assignment Upload Center</h3>
        <p class="text-muted mb-0">
            @if($ownOnly)
                Lecture notes, syllabus resources, and assignments for your stream.
            @else
                Share lecture notes, syllabus resources, assign homework, and grade student submissions.
                @if($streamScoped)
                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle fs-7"><i class="fa-solid fa-lock me-1"></i> Scoped to your assigned stream(s)</span>
                @endif
            @endif
        </p>
    </div>
    @if(Auth::user()->isStaff() || Auth::user()->isManagement())
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadMaterialModal">
        <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Study Material / Assignment
    </button>
    @endif
</div>

<div class="row g-4">
    @forelse($materials as $material)
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge {{ $material->type === 'notes' ? 'bg-primary' : ($material->type === 'assignment' ? 'bg-warning text-dark' : 'bg-info') }} text-uppercase fs-7">
                        {{ $material->type }}
                    </span>
                    <span class="text-muted fs-7">{{ $material->created_at->diffForHumans() }}</span>
                </div>
                <h5 class="fw-bold text-dark mb-1">{{ $material->title }}</h5>
                <div class="text-muted fs-7 mb-2"><i class="fa-solid fa-book me-1"></i> Subject: {{ optional($material->subject)->name }}</div>
                <p class="text-secondary small mb-3">{{ Str::limit($material->description, 100) }}</p>

                @if($material->due_date)
                    <div class="alert alert-warning py-1 px-2 fs-7 mb-3">
                        <i class="fa-regular fa-clock me-1"></i> Assignment Due: <strong>{{ $material->due_date }}</strong>
                    </div>
                @endif

                @if($material->file_path)
                    <a href="{{ asset('storage/' . $material->file_path) }}" download class="btn btn-sm btn-outline-success w-100 mb-2">
                        <i class="fa-solid fa-file-arrow-down me-1"></i> Download Attached File
                    </a>
                @endif

                @if(Auth::user()->isStudent())
                    @php $mySubmission = $material->submissions->firstWhere('student_id', optional(Auth::user()->studentProfile)->id); @endphp
                    @if($mySubmission)
                        <div class="p-2 bg-light rounded-3 border text-center">
                            @if($mySubmission->status === 'graded')
                                <span class="badge bg-success mb-1"><i class="fa-solid fa-circle-check me-1"></i> Approved & Graded</span>
                                <div class="fw-bold text-success">{{ $mySubmission->marks_obtained }} / 100</div>
                            @else
                                <span class="badge bg-warning text-dark mb-1"><i class="fa-regular fa-clock me-1"></i> Submitted &mdash; Pending Review</span>
                            @endif
                            <div class="fs-7 text-muted mt-1">Turned in {{ $mySubmission->created_at->diffForHumans() }}. Submissions can't be changed once turned in.</div>
                            @if($mySubmission->file_path)
                                <a href="{{ asset('storage/' . $mySubmission->file_path) }}" download class="btn btn-sm btn-outline-secondary w-100 mt-2">
                                    <i class="fa-solid fa-file-arrow-down me-1"></i> Download My Submission
                                </a>
                            @endif
                        </div>
                    @else
                    <button class="btn btn-sm btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#submitModal{{ $material->id }}">
                        <i class="fa-solid fa-upload me-1"></i> Submit Assignment
                    </button>

                    <!-- Student Submit Modal -->
                    <div class="modal fade" id="submitModal{{ $material->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('materials.submit', $material->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Submit Assignment: {{ $material->title }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-warning py-2 fs-7 mb-3">
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i> You can only submit this once. Double-check your file before turning it in.
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Comments / Notes</label>
                                            <textarea name="comments" class="form-control" rows="3" placeholder="Add submission comments..."></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Attach Solution File</label>
                                            <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.jpg,.jpeg,.png">
                                            <div class="form-text fs-7">PDF, Word, Excel, PowerPoint, ZIP, or image — max 10MB.</div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary fw-bold">Turn In Assignment</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                @else
                    <button class="btn btn-sm btn-light border w-100" data-bs-toggle="modal" data-bs-target="#viewSubmissionsModal{{ $material->id }}">
                        <i class="fa-solid fa-list-check me-1"></i> View Submissions ({{ $material->submissions->count() }})
                    </button>

                    <!-- View Submissions Modal -->
                    <div class="modal fade" id="viewSubmissionsModal{{ $material->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Student Submissions: {{ $material->title }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Student</th>
                                                    <th>Turned In</th>
                                                    <th>File</th>
                                                    <th>Status</th>
                                                    <th>Marks</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($material->submissions as $sub)
                                                <tr>
                                                    <td class="fw-bold">{{ optional($sub->student->user)->name }}</td>
                                                    <td>{{ $sub->created_at->format('M d, H:i') }}</td>
                                                    <td>
                                                        @if($sub->file_path)
                                                            <a href="{{ asset('storage/' . $sub->file_path) }}" download class="btn btn-sm btn-outline-success">
                                                                <i class="fa-solid fa-file-arrow-down me-1"></i> Download
                                                            </a>
                                                        @else
                                                            <span class="text-muted fs-7">No file attached</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($sub->status === 'graded')
                                                            <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> Approved</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark"><i class="fa-regular fa-clock me-1"></i> Pending Review</span>
                                                        @endif
                                                    </td>
                                                    <td class="fw-bold">{{ $sub->marks_obtained ?? 'Not Graded' }}</td>
                                                    <td>
                                                        <form action="{{ route('materials.grade', $sub->id) }}" method="POST" class="d-flex gap-1">
                                                            @csrf
                                                            <input type="number" name="marks_obtained" class="form-control form-control-sm" value="{{ $sub->marks_obtained }}" placeholder="95" min="0" max="100" style="width: 70px;" required>
                                                            <button type="submit" class="btn btn-sm btn-primary">{{ $sub->status === 'graded' ? 'Update' : 'Approve & Grade' }}</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-3">No student submissions received yet.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center text-muted py-5">
        <i class="fa-solid fa-folder-open display-4 mb-2"></i>
        <p class="mb-0 fs-5 fw-semibold">No study materials or assignments uploaded.</p>
    </div>
    @endforelse
</div>

<!-- Modal: Upload Material -->
<div class="modal fade" id="uploadMaterialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('materials.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-cloud-arrow-up text-primary me-2"></i> Publish Study Material</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Subject</label>
                        <select name="subject_id" class="form-select" required>
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }} ({{ $sub->code }}) &mdash; {{ optional($sub->course)->code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Resource Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Lecture Notes Chapter 3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Resource Category</label>
                        <select name="type" class="form-select">
                            <option value="notes">Lecture Notes / Slides</option>
                            <option value="assignment">Student Assignment Task</option>
                            <option value="syllabus">Syllabus & Course Outline</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Due Date (For Assignments)</label>
                        <input type="date" name="due_date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description / Prompt</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Provide instructions for students..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Attach File (optional)</label>
                        <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.jpg,.jpeg,.png">
                        <div class="form-text fs-7">PDF, Word, Excel, PowerPoint, ZIP, or image — max 10MB.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Publish to Course Hub</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
