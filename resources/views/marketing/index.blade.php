@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">WhatsApp & SMS Student Outreach</h3>
        <p class="text-muted mb-0">Create approved message templates, launch outreach campaigns, and monitor gateway delivery logs.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#newTemplateModal">
            <i class="fa-solid fa-file-code me-1"></i> New Template
        </button>
        <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#newCampaignModal">
            <i class="fa-brands fa-whatsapp me-1"></i> Launch Campaign
        </button>
    </div>
</div>

@if($whatsappConfigured)
<div class="alert alert-success border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 mb-4">
    <i class="fa-brands fa-whatsapp fs-3"></i>
    <div>
        <strong>WhatsApp Business API &mdash; Live.</strong>
        A campaign launched on WhatsApp using a template with a Meta Template Name will send for real. SMS still runs in <strong>simulation mode</strong> until a gateway is configured.
    </div>
</div>
@else
<div class="alert alert-warning border-0 shadow-sm rounded-4 d-flex align-items-center gap-3 mb-4">
    <i class="fa-solid fa-clock-rotate-left fs-3"></i>
    <div>
        <strong>WhatsApp &amp; SMS Gateway &mdash; Not Configured Yet.</strong>
        Campaigns below run in <strong>simulation mode</strong> (messages are logged, not actually delivered). Add your WhatsApp Business API credentials in <a href="{{ route('settings.edit') }}">Website Settings</a> to send for real.
    </div>
</div>
@endif

<div class="row g-4 mb-4">
    <!-- Active Message Templates -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-layer-group text-primary me-2"></i> Message Templates</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3">
                    @forelse($templates as $tmpl)
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark">{{ $tmpl->title }}</span>
                            <span class="badge {{ $tmpl->type === 'whatsapp' ? 'bg-success' : 'bg-info' }} text-uppercase fs-7">
                                {{ $tmpl->type }}
                            </span>
                        </div>
                        <p class="font-monospace small text-secondary bg-white p-2 rounded border mb-0">{{ $tmpl->content }}</p>
                        @if($tmpl->type === 'whatsapp')
                            @if($tmpl->meta_template_name)
                                <div class="fs-7 mt-2"><i class="fa-solid fa-circle-check text-success me-1"></i> Real send &mdash; Meta template: <code>{{ $tmpl->meta_template_name }}</code> ({{ $tmpl->meta_language }})</div>
                            @else
                                <div class="fs-7 mt-2 text-muted"><i class="fa-solid fa-flask me-1"></i> Simulated &mdash; no Meta Template Name set</div>
                            @endif
                        @endif
                    </div>
                    @empty
                    <p class="text-muted text-center py-4">No templates created yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Outreach Campaigns -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-paper-plane text-success me-2"></i> Campaign History</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Campaign Name</th>
                                <th>Template</th>
                                <th>Recipients</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($campaigns as $camp)
                            <tr>
                                <td class="fw-bold">{{ $camp->name }}</td>
                                <td>{{ optional($camp->template)->title }}</td>
                                <td><span class="badge bg-primary rounded-pill">{{ $camp->recipients_count }} Students</span></td>
                                <td><span class="badge bg-success">Dispatched</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No campaigns launched yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Live Gateway Stream Log -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-list-check text-dark me-2"></i> Simulated Gateway Delivery Logs</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Time</th>
                        <th>Channel</th>
                        <th>Mobile Number</th>
                        <th>Dispatched Message Content</th>
                        <th>Gateway Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="text-muted fs-7">{{ $log->created_at->format('M d, H:i:s') }}</td>
                        <td>
                            <span class="badge {{ $log->channel === 'WhatsApp' ? 'bg-success' : 'bg-info' }}">
                                {{ $log->channel }}
                            </span>
                        </td>
                        <td class="font-monospace fw-bold">{{ $log->recipient_phone }}</td>
                        <td class="text-secondary small">{{ Str::limit($log->message, 80) }}</td>
                        <td>
                            @if($log->status === 'Delivered')
                                <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fa-solid fa-check-double me-1"></i> Delivered</span>
                            @elseif(str_starts_with($log->status, 'Failed'))
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle" title="{{ $log->status }}"><i class="fa-solid fa-circle-xmark me-1"></i> Failed</span>
                            @else
                                <span class="badge bg-info-subtle text-info border border-info-subtle"><i class="fa-solid fa-paper-plane me-1"></i> {{ $log->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No gateway logs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: New Template -->
<div class="modal fade" id="newTemplateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('marketing.template') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Create Message Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Template Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Admission Reminder 2026" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Channel Type</label>
                        <select name="type" id="tmplType" class="form-select" onchange="document.getElementById('metaTemplateWrap').classList.toggle('d-none', this.value !== 'whatsapp')">
                            <option value="whatsapp">WhatsApp Business API</option>
                            <option value="sms">SMS Gateway</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Message Content</label>
                        <textarea name="content" class="form-control" rows="4" placeholder="Use placeholders: {name}, {course}..." required></textarea>
                        <div class="form-text fs-7">Available placeholders: <code>{name}</code>, <code>{course}</code>. This is shown in the app and used for SMS/simulated sends.</div>
                    </div>
                    <div id="metaTemplateWrap" class="row g-2 mb-1">
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-muted">Meta Template Name (for real WhatsApp sends)</label>
                            <input type="text" name="meta_template_name" class="form-control font-monospace" placeholder="e.g. admission_open">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Language</label>
                            <input type="text" name="meta_language" class="form-control" value="en_US">
                        </div>
                        <div class="form-text fs-7">Must exactly match a template already <strong>approved</strong> in Meta Business Manager → WhatsApp Manager → Message Templates. Leave empty to keep this template simulated only.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Template</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Launch Campaign -->
<div class="modal fade" id="newCampaignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('marketing.campaign') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-brands fa-whatsapp text-success me-2"></i> Launch Outreach Campaign</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Campaign Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Early Bird Admissions Blast" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Approved Template</label>
                        <select name="template_id" class="form-select" required>
                            @foreach($templates as $tmpl)
                                <option value="{{ $tmpl->id }}">{{ $tmpl->title }} ({{ strtoupper($tmpl->type) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Delivery Channel</label>
                        <select name="channel" class="form-select">
                            <option value="WhatsApp">WhatsApp Gateway</option>
                            <option value="SMS">SMS Gateway</option>
                        </select>
                    </div>
                    <div class="alert alert-info py-2 fs-7 mb-0">
                        <i class="fa-solid fa-circle-info me-1"></i> Target Audience: <strong>{{ $recipients->count() }} active students</strong> with a mobile number will receive this message.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fa-solid fa-paper-plane me-1"></i> Dispatch Campaign</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
