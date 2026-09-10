@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-file-pdf text-danger me-2"></i>Stream Semester Syllabi Management</h3>
            <p class="text-secondary mb-0">Upload and update official semester-wise syllabus PDFs for each stream/course and semester.</p>
        </div>
        <a href="{{ route('academics.my_syllabus') }}" class="btn btn-outline-primary rounded-pill px-4 fw-semibold">
            <i class="fa-solid fa-eye me-1"></i> Preview Student View
        </a>
    </div>

    <div class="row g-4">
        <!-- Left: Upload Form -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up text-primary"></i> Upload / Replace Syllabus PDF
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('academics.syllabi.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Stream / Course Program <span class="text-danger">*</span></label>
                            <select name="course_id" class="form-select rounded-3" required>
                                <option value="">-- Choose Course / Stream --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }} ({{ $course->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Semester Number <span class="text-danger">*</span></label>
                            <select name="semester" class="form-select rounded-3" required>
                                @for($s = 1; $s <= 8; $s++)
                                    <option value="{{ $s }}" {{ request('semester') == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Syllabus Title (Optional)</label>
                            <input type="text" name="title" class="form-control rounded-3" placeholder="e.g., BCA Semester 1 Official Curriculum">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Official Syllabus PDF File <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control rounded-3" accept="application/pdf" required>
                            <div class="form-text fs-7">Only PDF files up to 15MB are accepted.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Notes / Description</label>
                            <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Optional notes for students..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm w-100">
                            <i class="fa-solid fa-paper-plane me-1"></i> Upload & Broadcast Syllabus PDF
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right: Uploaded Syllabi List -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-list-check me-2 text-primary"></i>Uploaded Stream Syllabi</h5>
                    <span class="badge bg-primary rounded-pill px-3 py-2 fs-7">{{ $syllabi->count() }} Uploaded</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Stream / Course</th>
                                <th>Semester</th>
                                <th>Title</th>
                                <th>Uploader</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($syllabi as $syl)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark fs-6">{{ optional($syl->course)->code }}</div>
                                    <div class="text-muted fs-7">{{ optional($syl->course)->name }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1 fs-7">
                                        Sem {{ $syl->semester }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark fs-7">{{ $syl->title }}</div>
                                    <div class="text-muted fs-8">{{ $syl->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="fs-7 text-secondary">
                                    {{ optional($syl->uploader)->name ?? 'Management' }}
                                </td>
                                <td>
                                    <a href="{{ Storage::url($syl->syllabus_pdf_path) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="View PDF">
                                        <i class="fa-solid fa-file-pdf me-1"></i> PDF
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-file-pdf fs-1 text-black-50 mb-2"></i>
                                    <h5 class="fw-semibold">No Semester Syllabi Uploaded Yet</h5>
                                    <p class="small mb-0">Use the form on the left to upload your first semester syllabus PDF.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
