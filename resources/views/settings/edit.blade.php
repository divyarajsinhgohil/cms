@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Website Settings</h3>
        <p class="text-muted mb-0">Manage the college's branding and contact details shown across the portal.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-gear text-primary me-2"></i> General Settings</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">College / Institute Name</label>
                            <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $settings->site_name) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Current Academic Year</label>
                            <input type="text" name="academic_year" class="form-control" value="{{ old('academic_year', $settings->academic_year) }}" placeholder="2026-2027" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Affiliation / Tagline</label>
                            <input type="text" name="affiliation" class="form-control" value="{{ old('affiliation', $settings->affiliation) }}" placeholder="e.g. Affiliated to Saurashtra University">
                            <div class="form-text fs-7">Shown under the college name on the login page.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Logo Image URL</label>
                            <input type="text" name="logo_url" class="form-control" value="{{ old('logo_url', $settings->logo_url) }}" placeholder="https://example.com/logo.png">
                            <div class="form-text fs-7">Shown in the top navigation bar and login page instead of the default icon. Leave empty to keep the icon.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Contact Email</label>
                            <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $settings->contact_email) }}" placeholder="accounts@cms.edu">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Contact Phone</label>
                            <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $settings->contact_phone) }}" placeholder="+91 98765 43210">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Campus Address</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Campus Road, Institutional Area...">{{ old('address', $settings->address) }}</textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary fw-bold px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-circle-info text-info me-2"></i> Where This Appears</h5>
            </div>
            <div class="card-body fs-7 text-muted">
                <p>The College Name, Affiliation, and Logo apply to:</p>
                <ul>
                    <li>Top navigation bar brand</li>
                    <li>Login page header</li>
                    <li>ID Cards & Bonafide Certificates</li>
                    <li>Marksheets & Fee Receipts</li>
                </ul>
                <p class="mb-0">Update them here and they'll reflect everywhere the next time a page or PDF is generated.</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fa-brands fa-whatsapp text-success me-2"></i> WhatsApp Business API</h5>
                <button type="button" id="waCheckBtn" class="btn btn-sm btn-outline-secondary" onclick="cmsCheckWhatsApp()">
                    <i class="fa-solid fa-satellite-dish me-1"></i> Test Connection
                </button>
            </div>
            <div class="card-body">
                <div id="waCheckResult" class="d-none alert py-2 fs-7 mb-3"></div>

                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="site_name" value="{{ $settings->site_name }}">
                    <input type="hidden" name="academic_year" value="{{ $settings->academic_year }}">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Permanent Access Token</label>
                            <input type="password" name="whatsapp_access_token" class="form-control font-monospace" value="" autocomplete="off" placeholder="{{ filled($settings->whatsapp_access_token) ? 'Saved (hidden) - leave blank to keep it' : 'EAAG...' }}">
                            <div class="form-text fs-7">From Meta Business Settings → System Users → generate a permanent token with whatsapp_business_messaging permission.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phone Number ID</label>
                            <input type="text" name="whatsapp_phone_number_id" class="form-control font-monospace" value="{{ old('whatsapp_phone_number_id', $settings->whatsapp_phone_number_id) }}" placeholder="1029384756...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">WhatsApp Business Account ID</label>
                            <input type="text" name="whatsapp_business_account_id" class="form-control font-monospace" value="{{ old('whatsapp_business_account_id', $settings->whatsapp_business_account_id) }}" placeholder="Optional">
                        </div>
                    </div>
                    <div class="alert alert-warning fs-7 mt-3 mb-0">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        Meta only lets you message someone <strong>first</strong> using an approved message <strong>template</strong> (created and approved in Meta Business Manager), never free text. When creating a Marketing Template for WhatsApp, set its "Meta Template Name" to match an approved template exactly, or the send will fail.
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-success fw-bold px-4"><i class="fa-solid fa-floppy-disk me-1"></i> Save WhatsApp Credentials</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-comment-dollar text-warning me-2"></i> SMS Sending</h5>
            </div>
            <div class="card-body fs-7 text-muted">
                <p>SMS is not wired to a gateway yet — it still runs in simulation mode on the Marketing page.</p>
                <p class="mb-0">Typical Indian SMS gateways (MSG91, Fast2SMS, Twilio) charge roughly ₹0.10–₹0.25 per SMS domestically, usually sold as prepaid credit bundles. Pricing varies by provider, route (promotional vs. transactional/DLT-registered), and volume — check the provider's current pricing page before committing.</p>
            </div>
        </div>
    </div>
</div>

<script>
    function cmsCheckWhatsApp() {
        const btn = document.getElementById('waCheckBtn');
        const box = document.getElementById('waCheckResult');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Checking...';

        fetch('{{ route('settings.whatsapp.check') }}', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                box.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-secondary');
                if (data.state === 'ok') {
                    box.classList.add('alert-success');
                    box.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> Connected — ' + (data.name || '') + ' (' + (data.number || '') + ')';
                } else if (data.state === 'unset') {
                    box.classList.add('alert-secondary');
                    box.innerHTML = '<i class="fa-solid fa-circle-info me-1"></i> ' + data.message;
                } else {
                    box.classList.add('alert-danger');
                    box.innerHTML = '<i class="fa-solid fa-circle-exclamation me-1"></i> ' + data.message + (data.hint ? '<br><span class="text-muted">' + data.hint + '</span>' : '');
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-satellite-dish me-1"></i> Test Connection';
            });
    }
</script>
@endsection
