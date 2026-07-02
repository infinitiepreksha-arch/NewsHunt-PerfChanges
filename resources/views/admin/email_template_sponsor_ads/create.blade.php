@extends('admin.layouts.main')

@section('title')
    {{ $title }}
@endsection

@section('pre-title')
    {{ $pre_title }}
@endsection

@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('page.HOME') }}/</a>
                <a href="{{ route('email-Sponsor-Ads.index') }}">{{ __('page.SPONSOR_EMAIL_TEMPLATES_DETAILS') }}/</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title">
                @yield('title')
            </h2>
        </div>
    </div>
@endsection

@section('content')
    <section class="section m-1">
        @can('create-SponsorEmailtemplate')
            <div class="row">
                <div class="col-lg-8" id="sponsorAddata">
                    <div class="card admin_cards">
                        <form action="{{ route('email-Sponsor-Ads.store') }}" method="POST" id="email_sponsor_template_form"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    {{-- Title --}}
                                    <div class="col-md-6 mt-3">
                                        <label for="title" class="form-label">Title <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="title" id="title"
                                            class="form-control @error('title') is-invalid @enderror"
                                            value="{{ old('title') }}" placeholder="e.g., Your Subscription Details">
                                        <span class="parsley-required">
                                            <strong id="title-error-message"></strong>
                                        </span>
                                    </div>
                                    {{-- Subject --}}
                                    <div class="col-md-6 mt-3">
                                        <label for="subject" class="form-label">Subject <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="subject" id="subject"
                                            class="form-control @error('subject') is-invalid @enderror"
                                            value="{{ old('subject') }}"
                                            placeholder="e.g., Update Regarding Your Sponsor Ad Request">
                                        <span class="parsley-required">
                                            <strong id="subject-error-message"></strong>
                                        </span>
                                    </div>

                                    {{-- Logo Upload with Cropper --}}
                                    <div class="col-md-6 mt-3">
                                        <label for="logo" class="form-label">Logo (Optional)</label>
                                        <input type="file" name="logo" id="logo"
                                            class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                                        <span class="parsley-required">
                                            <strong id="logo-error-message"></strong>
                                        </span>
                                        <div id="logo-cropper" class="mt-2" style="display: none;">
                                            <img id="logo-preview" src="#" alt="Logo Preview"
                                                style="max-width: 100%; max-height: 300px;">
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-success btn-sm" id="crop-logo">Crop
                                                    Logo</button>
                                                <button type="button" class="btn btn-secondary btn-sm"
                                                    id="cancel-logo">Cancel</button>
                                            </div>
                                        </div>
                                        <canvas id="logo-canvas" style="display: none;"></canvas>
                                    </div>

                                    {{-- Extra Image Upload with Cropper --}}
                                    <div class="col-md-6 mt-3">
                                        <label for="image" class="form-label">Extra Image (Optional)</label>
                                        <input type="file" name="image" id="image"
                                            class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                        <span class="parsley-required">
                                            <strong id="image-error-message"></strong>
                                        </span>
                                        <div id="image-cropper" class="mt-2" style="display: none;">
                                            <img id="image-preview" src="#" alt="Image Preview"
                                                style="max-width: 100%; max-height: 300px;">
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-success btn-sm" id="crop-image">Crop
                                                    Image</button>
                                                <button type="button" class="btn btn-secondary btn-sm"
                                                    id="cancel-image">Cancel</button>
                                            </div>
                                        </div>
                                        <canvas id="image-canvas" style="display: none;"></canvas>
                                    </div>

                                    {{-- Layout Width --}}
                                    <div class="col-md-6 mt-3">
                                        <label for="layout_width" class="form-label">Layout Width (px)</label>
                                        <input type="number" name="layout_width" id="layout_width"
                                            class="form-control @error('layout_width') is-invalid @enderror"
                                            value="{{ old('layout_width', 600) }}">
                                        <span class="parsley-required">
                                            <strong id="layout_width-error-message"></strong>
                                        </span>
                                    </div>

                                    {{-- Status --}}
                                    <div class="col-md-6 mt-3">
                                        <label for="status" class="form-label">Status <span
                                                class="parsley-required">*</span></label>
                                        <select name="status" id="status" class="form-select">
                                            <option value="">Select Status</option>
                                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>
                                        <span class="parsley-required">
                                            <strong id="status-error-message"></strong>
                                        </span>
                                    </div>


                                    {{-- HTML Content (TinyMCE) --}}
                                    <div class="col-md-12 mt-4">
                                        <label for="html_content" class="form-label">Email HTML Content <span
                                                class="text-danger">*</span></label>
                                        <textarea id="tinymce_editor" name="html_content" class="form-control @error('html_content') is-invalid @enderror"
                                            rows="10" placeholder="Write your email content here...">{{ old('html_content') }}</textarea>
                                        <span class="parsley-required">
                                            <strong id="html_content-error-message"></strong>
                                        </span>
                                    </div>

                                    {{-- Closing --}}
                                    <div class="col-md-6 mt-3">
                                        <label for="closing" class="form-label">Closing (Optional)</label>
                                        <input type="text" name="closing" id="closing"
                                            class="form-control @error('closing') is-invalid @enderror"
                                            value="{{ old('closing') }}" placeholder="e.g., Best Regards, Thank You">
                                    </div>

                                    {{-- Signature --}}
                                    <div class="col-md-6 mt-3">
                                        <label for="signature" class="form-label">Signature (Optional)</label>
                                        <textarea name="signature" id="signature" class="form-control @error('signature') is-invalid @enderror"
                                            rows="3" placeholder="Enter your name or company signature">{{ old('signature') }}</textarea>
                                    </div>

                                    {{-- Footer Text --}}
                                    <div class="col-md-12 mt-3">
                                        <label for="footer_text" class="form-label">Footer Text (Optional)</label>
                                        <textarea name="footer_text" id="footer_text" class="form-control @error('footer_text') is-invalid @enderror"
                                            rows="2" placeholder="Enter footer text like © 2025 Smart Ads. All rights reserved.">{{ old('footer_text') }}</textarea>
                                    </div>

                                </div>

                                {{-- Submit --}}
                                <div class="col-12 mt-4 d-flex justify-content-end">
                                    <button class="btn btn-primary me-1 mb-1"
                                        type="submit">{{ __('message.SAVE') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Email Preview Section --}}
                <div class="col-lg-4">
                    <div class="card admin_cards">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Email Preview</h3>
                            <div>
                                <button class="btn btn-sm btn-outline-secondary" id="toggle-preview-mode"
                                    data-mode="desktop">
                                    Switch to Mobile View
                                </button>
                                <button class="btn btn-sm btn-outline-primary" id="toggle-refresh-mode" data-mode="desktop">
                                    <small>Refresh Preview</small>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="email-preview" class="border p-3"
                                style="min-height: 400px; border-radius: 5px; font-family: Arial, sans-serif; line-height: 1.6;">
                                <div class="mb-2" style="font-size: 14px;">
                                    <strong>Title:</strong> <span id="preview-title">No title</span>
                                </div>
                                <div class="mb-2" style="font-size: 14px;">
                                    <strong>Type:</strong> <span id="preview-type">sponsor</span>
                                </div>
                                <div class="mb-2" style="font-size: 14px;">
                                    <strong>Status:</strong> <span id="preview-status">active</span>
                                </div>
                                <div class="text-center mb-3">
                                    <div id="preview-logo" style="display: none;">
                                        <img id="preview-logo-img" src="" alt="Logo"
                                            style="max-width: 150px; height: auto; border-radius: 5px;" width="150">
                                    </div>
                                </div>
                                <div class="mb-2" style="font-size: 14px;">
                                    <strong>Subject:</strong> <span id="preview-subject">No subject</span>
                                </div>
                                <div id="preview-content"
                                    style="border: 1px solid #dadce0; padding: 15px; min-height: 200px; border-radius: 5px; overflow-wrap: break-word; word-wrap: break-word;">
                                    <p class="text-muted">Content will appear here...</p>
                                    <div class="closing mt-3"></div>
                                    <div class="signature mt-2"></div>
                                </div>
                                <div id="preview-extra-image" class="mt-3 text-center" style="display: none;">
                                    <img id="preview-extra-img" src="" alt="Extra Image"
                                        style="max-width: 100%; height: auto; border-radius: 5px;" width="100%">
                                </div>
                                <div class="mt-3">
                                    <strong>Layout Width:</strong> <span id="preview-width">600px</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </section>
@endsection

@push('scripts')
    <!-- Include TinyMCE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.4/tinymce.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
@endpush
