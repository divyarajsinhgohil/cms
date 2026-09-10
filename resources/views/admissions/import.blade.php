@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Bulk Student Import</h1>
        <p class="text-muted mb-0">Upload a CSV file for validation, dry-run preview, and error reporting before import.</p>
    </div>
    <a href="{{ route('admissions.import.template') }}" class="btn btn-outline-primary">
        <i class="fa-solid fa-download me-1" aria-hidden="true"></i> Download Template
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3">
                <h2 class="h6 fw-bold mb-0">Upload CSV</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('admissions.import.preview') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="import_file" class="form-label fw-bold">CSV File</label>
                        <input type="file" name="import_file" id="import_file" class="form-control" accept=".csv,.txt,.xlsx,.xls" required
                               aria-describedby="importHelp">
                        <div id="importHelp" class="form-text">Supports CSV & Excel CSV exports. Required columns: Name, Email, Phone, Department Code, Course/Stream Code, Batch Year, Semester, Gender. UTF-8 BOM & header aliases auto-handled.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa-solid fa-magnifying-glass me-1" aria-hidden="true"></i> Validate &amp; Dry Run
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(!empty($preview))
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h2 class="h6 fw-bold mb-0">Dry Run Summary</h2>
                <span class="badge bg-success">{{ $summary['valid'] ?? 0 }} valid</span>
                <span class="badge bg-danger">{{ $summary['invalid'] ?? 0 }} errors</span>
            </div>
            <div class="card-body">
                @if(!empty($invalidRows))
                    <h3 class="h6 fw-bold text-danger">Error Report</h3>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm table-bordered" role="table">
                            <thead><tr><th scope="col">Line</th><th scope="col">Errors</th></tr></thead>
                            <tbody>
                                @foreach($invalidRows as $line => $errors)
                                    <tr>
                                        <td>{{ $line }}</td>
                                        <td><ul class="mb-0 ps-3">@foreach($errors as $e)<li>{{ $e }}</li>@endforeach</ul></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if(!empty($validRows) && ($summary['invalid'] ?? 0) === 0)
                    <form action="{{ route('admissions.import.run') }}" method="POST">
                        @csrf
                        <input type="hidden" name="stored_path" value="{{ $storedPath }}">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="confirm" value="1" id="confirmImport" required>
                            <label class="form-check-label" for="confirmImport">
                                I confirm importing {{ count($validRows) }} student record(s). Default password will be <code>password</code>.
                            </label>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-file-import me-1" aria-hidden="true"></i> Import {{ count($validRows) }} Students
                        </button>
                    </form>
                @elseif(!empty($validRows))
                    <p class="text-warning mb-0"><i class="fa-solid fa-triangle-exclamation me-1"></i> Fix all errors before importing. {{ count($validRows) }} row(s) are valid.</p>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

@if(session('import_failed'))
<div class="alert alert-warning mt-3" role="alert">
    <strong>Partial import failures:</strong>
    <ul class="mb-0 mt-2">@foreach(session('import_failed') as $line => $msg)<li>Line {{ $line }}: {{ $msg }}</li>@endforeach</ul>
</div>
@endif
@endsection
