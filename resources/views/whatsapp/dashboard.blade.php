@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Banner -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3 bg-white p-4 rounded-4 shadow-sm border border-light">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 56px; height: 56px; font-size: 1.8rem;">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1 text-dark">WhatsApp Communications Hub</h3>
                <p class="text-secondary mb-0 small">Send instant direct messages, configure automatic event triggers, and monitor gateway status.</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('marketing.index') }}" class="btn btn-outline-success rounded-pill px-3 fw-semibold fs-7">
                <i class="fa-solid fa-bullhorn me-1"></i> Bulk Campaigns
            </a>
            <a href="{{ route('whatsapp.logs') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold fs-7">
                <i class="fa-solid fa-list-check me-1"></i> Dispatch Logs
            </a>
        </div>
    </div>

    <!-- Status & Stat Metrics Grid -->
    <div class="row g-3 mb-4">
        <!-- Meta Connection Health Status -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold fs-7 text-uppercase text-secondary">Gateway Status</span>
                    @if($connectionStatus['state'] === 'ok')
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 fs-8">
                            <i class="fa-solid fa-circle-check me-1"></i> Connected
                        </span>
                    @else
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1 fs-8">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ strtoupper($connectionStatus['state']) }}
                        </span>
                    @endif
                </div>
                <div class="fw-bold text-dark fs-5 mb-1">
                    {{ $connectionStatus['name'] ?? ($settings->whatsapp_phone_number_id ? 'Phone ID Set' : 'Credentials Unset') }}
                </div>
                <div class="text-secondary fs-7 text-truncate">
                    {{ $connectionStatus['number'] ?? ($connectionStatus['message'] ?? 'No credentials saved') }}
                </div>
                <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center">
                    <a href="{{ route('settings.edit') }}" class="fs-7 text-primary text-decoration-none fw-semibold">
                        <i class="fa-solid fa-gear me-1"></i> Configure Settings
                    </a>
                    <button type="button" class="btn btn-sm btn-light fs-8 py-0 px-2 rounded-pill" id="btnCheckStatus" onclick="cmsTestWhatsAppStatus()">
                        <i class="fa-solid fa-arrows-rotate"></i> Ping
                    </button>
                </div>
            </div>
        </div>

        <!-- Today Dispatches -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold fs-7 text-uppercase text-secondary">Today's Messages</span>
                    <span class="badge bg-primary-subtle text-primary rounded-circle p-2"><i class="fa-solid fa-paper-plane"></i></span>
                </div>
                <div class="fs-2 fw-bold text-dark mb-0">{{ number_format($todayCount) }}</div>
                <div class="text-muted fs-7">Dispatched past 24 hours</div>
            </div>
        </div>

        <!-- Total Delivered -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold fs-7 text-uppercase text-secondary">Total Delivered</span>
                    <span class="badge bg-success-subtle text-success rounded-circle p-2"><i class="fa-solid fa-circle-check"></i></span>
                </div>
                <div class="fs-2 fw-bold text-success mb-0">{{ number_format($deliveredCount) }}</div>
                <div class="text-muted fs-7">Successful WhatsApp transmissions</div>
            </div>
        </div>

        <!-- Total Failed -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold fs-7 text-uppercase text-secondary">Failed / Errors</span>
                    <span class="badge bg-danger-subtle text-danger rounded-circle p-2"><i class="fa-solid fa-circle-xmark"></i></span>
                </div>
                <div class="fs-2 fw-bold text-danger mb-0">{{ number_format($failedCount) }}</div>
                <div class="text-muted fs-7">Check logs for details</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Quick Direct Sender & Automated Triggers -->
        <div class="col-lg-7">
            <!-- Quick Direct WhatsApp Message Panel -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fa-solid fa-comment-sms text-success"></i> Direct Instant WhatsApp Message
                    </h5>
                    <span class="badge bg-success text-white rounded-pill px-3 py-1 fs-7">Live Gateway</span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('whatsapp.send_direct') }}" method="POST">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Quick Select Recipient (Optional)</label>
                                <select class="form-select cms-searchable-select" id="quickStudentSelect" onchange="cmsFillRecipient(this)">
                                    <option value="">-- Choose Student / Staff --</option>
                                    <optgroup label="Students">
                                        @foreach($students as $st)
                                            @if(optional($st->user)->phone)
                                                <option value="{{ $st->user->phone }}" data-name="{{ $st->user->name }}">
                                                    {{ $st->user->name }} ({{ $st->enrollment_no }}) - {{ $st->user->phone }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Faculty & Management">
                                        @foreach($staffUsers as $usr)
                                            <option value="{{ $usr->phone }}" data-name="{{ $usr->name }}">
                                                {{ $usr->name }} ({{ ucfirst($usr->role) }}) - {{ $usr->phone }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Recipient Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="recipientPhone" class="form-control rounded-3" placeholder="e.g. +91 9876543210" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Recipient Name</label>
                            <input type="text" name="recipient_name" id="recipientName" class="form-control rounded-3" placeholder="e.g., Rahul Sharma">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Message Text <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control rounded-3" rows="4" placeholder="Type your WhatsApp message..." required></textarea>
                            <div class="form-text fs-7">Messages will be sent directly to the recipient's WhatsApp number.</div>
                        </div>

                        <button type="submit" class="btn btn-success fw-bold px-4 rounded-pill shadow-sm">
                            <i class="fa-brands fa-whatsapp me-1"></i> Send WhatsApp Message Now
                        </button>
                    </form>
                </div>
            </div>

            <!-- Automated System WhatsApp Notification Triggers -->
            @if(Auth::user()->isManagement())
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fa-solid fa-robot text-primary"></i> Automated WhatsApp Triggers
                    </h5>
                    <div class="text-secondary small mt-1">Automatically send instant WhatsApp alerts when actions occur across the CMS.</div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('whatsapp.triggers') }}" method="POST">
                        @csrf
                        <div class="list-group list-group-flush rounded-3 border">
                            <!-- Fee Receipts -->
                            <div class="list-group-item p-3 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-success-subtle text-success p-2 fs-5">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">Automated Fee Payment Receipts</div>
                                        <div class="text-muted fs-7">Send WhatsApp payment confirmation message & receipt details when fee payments are collected.</div>
                                    </div>
                                </div>
                                <div class="form-check form-switch fs-4">
                                    <input class="form-check-input" type="checkbox" name="whatsapp_auto_fee_receipt" value="1" {{ $settings->whatsapp_auto_fee_receipt ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- Attendance Low / Absence Alerts -->
                            <div class="list-group-item p-3 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-warning-subtle text-warning p-2 fs-5">
                                        <i class="fa-solid fa-clipboard-user"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">Student Absence & Attendance Alerts</div>
                                        <div class="text-muted fs-7">Send instant WhatsApp notification when a student is marked Absent during daily attendance.</div>
                                    </div>
                                </div>
                                <div class="form-check form-switch fs-4">
                                    <input class="form-check-input" type="checkbox" name="whatsapp_auto_attendance_alert" value="1" {{ $settings->whatsapp_auto_attendance_alert ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- Exam Result Alerts -->
                            <div class="list-group-item p-3 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary-subtle text-primary p-2 fs-5">
                                        <i class="fa-solid fa-award"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">Exam Result & Marksheet Updates</div>
                                        <div class="text-muted fs-7">Send WhatsApp notification with subject score & grade when faculty publishes exam marks.</div>
                                    </div>
                                </div>
                                <div class="form-check form-switch fs-4">
                                    <input class="form-check-input" type="checkbox" name="whatsapp_auto_exam_result" value="1" {{ $settings->whatsapp_auto_exam_result ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- Admission Application Approval -->
                            <div class="list-group-item p-3 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-info-subtle text-info p-2 fs-5">
                                        <i class="fa-solid fa-user-check"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">Admission Application Approvals</div>
                                        <div class="text-muted fs-7">Send WhatsApp welcome message & Enrollment No when an online application is approved.</div>
                                    </div>
                                </div>
                                <div class="form-check form-switch fs-4">
                                    <input class="form-check-input" type="checkbox" name="whatsapp_auto_admission_approval" value="1" {{ $settings->whatsapp_auto_admission_approval ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save Trigger Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Recent WhatsApp Logs -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-secondary"></i> Recent Activity Log
                    </h5>
                    <a href="{{ route('whatsapp.logs') }}" class="fs-7 text-primary text-decoration-none fw-bold">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($logs as $log)
                        <div class="list-group-item p-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div class="fw-bold text-dark fs-7">
                                    <i class="fa-brands fa-whatsapp text-success me-1"></i>
                                    {{ $log->recipient_name ?? 'Recipient' }}
                                    <span class="text-muted fw-normal">({{ $log->recipient_phone }})</span>
                                </div>
                                @if($log->status === 'Delivered')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-8">Delivered</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill fs-8">Failed</span>
                                @endif
                            </div>
                            <div class="text-secondary small text-truncate mb-1" style="max-width: 340px;">
                                {{ $log->message }}
                            </div>
                            <div class="d-flex align-items-center justify-content-between text-muted fs-8">
                                <span class="badge bg-light text-secondary border text-capitalize">{{ str_replace('_', ' ', $log->type) }}</span>
                                <span>{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5 text-muted p-4">
                            <i class="fa-brands fa-whatsapp fs-1 text-black-50 mb-2"></i>
                            <p class="mb-0">No WhatsApp messages dispatched yet.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function cmsFillRecipient(selectEl) {
        const option = selectEl.options[selectEl.selectedIndex];
        if (option && option.value) {
            document.getElementById('recipientPhone').value = option.value;
            document.getElementById('recipientName').value = option.getAttribute('data-name') || '';
        }
    }

    function cmsTestWhatsAppStatus() {
        const btn = document.getElementById('btnCheckStatus');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking...';

        fetch("{{ route('whatsapp.test_connection') }}")
            .then(res => res.json())
            .then(data => {
                alert(`WhatsApp Gateway Status: ${data.state.toUpperCase()}\n\n${data.message}`);
                window.location.reload();
            })
            .catch(err => {
                alert('Could not test connection: ' + err);
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-arrows-rotate"></i> Ping';
            });
    }
</script>
@endsection
