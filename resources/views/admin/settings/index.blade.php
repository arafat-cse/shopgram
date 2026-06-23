@extends('layouts.admin')
@section('title', 'Settings')
@section('content')
<h4 class="fw-bold mb-4">Site Settings</h4>
<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">General</div>
                <div class="card-body">
                    <div class="mb-3"><label class="form-label">Site Name</label>
                        <input type="text" name="site_name" class="form-control" value="{{ $settings['site_name'] ?? '' }}"></div>
                    <div class="mb-3"><label class="form-label">Contact Email</label>
                        <input type="email" name="contact_email" class="form-control" value="{{ $settings['contact_email'] ?? '' }}"></div>
                    <div class="mb-3"><label class="form-label">Contact Phone</label>
                        <input type="text" name="contact_phone" class="form-control" value="{{ $settings['contact_phone'] ?? '' }}"></div>
                    <div class="mb-3"><label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" value="{{ $settings['address'] ?? '' }}"></div>
                    <div class="mb-3"><label class="form-label">Logo</label>
                        <input type="file" name="site_logo" class="form-control" accept="image/*"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Social Links</div>
                <div class="card-body">
                    <div class="mb-3"><label class="form-label">Facebook</label>
                        <input type="url" name="facebook" class="form-control" value="{{ $settings['facebook'] ?? '' }}"></div>
                    <div class="mb-3"><label class="form-label">YouTube</label>
                        <input type="url" name="youtube" class="form-control" value="{{ $settings['youtube'] ?? '' }}"></div>
                    <div class="mb-3"><label class="form-label">Instagram</label>
                        <input type="url" name="instagram" class="form-control" value="{{ $settings['instagram'] ?? '' }}"></div>
                    <div class="mb-3"><label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control" value="{{ $settings['whatsapp'] ?? '' }}"></div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Maintenance</div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="maintenance_mode" value="1" {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label">Enable Maintenance Mode</label>
                    </div>
                    <div class="mb-3"><label class="form-label">Maintenance Message</label>
                        <textarea name="maintenance_message" class="form-control" rows="2">{{ $settings['maintenance_message'] ?? '' }}</textarea></div>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary mt-4 px-4">Save Settings</button>
</form>
@endsection
