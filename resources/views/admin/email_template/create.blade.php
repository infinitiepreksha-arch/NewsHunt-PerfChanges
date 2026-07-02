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
                <a href="{{ url('admin/email-template') }}">{{ __('page.EMAIL_TEMPLATE_DETAILS') }}/</a>
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
        <div class="card admin_cards">
            <form action="{{ route('email-template.store') }}" method="POST" id="email_template_form">
                @csrf
                <div class="card-body">
                    <div class="row">
                        {{-- Title --}}
                        <div class="col-md-6 mt-2">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                            <span class="parsley-required"><strong id="title-error"></strong></span>
                        </div>


                        {{-- Post Count --}}
                        <div class="col-md-6 mt-3">
                            <label for="post_count" class="form-label">Post Count <span class="text-danger">*</span></label>
                            <input type="number" name="post_count" class="form-control" value="{{ old('post_count', 5) }}"
                                min="1">
                            <span class="parsley-required"><strong id="post_count-error"></strong></span>
                        </div>

                        {{-- Layout Width --}}
                        <div class="col-md-6 mt-3">
                            <label for="layout_width" class="form-label">Layout Width (px) <span
                                    class="text-danger">*</span></label>
                            <input type="number" name="layout_width" class="form-control"
                                value="{{ old('layout_width', 600) }}" min="300">
                            <span class="parsley-required"><strong id="layout_width-error"></strong></span>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6 mt-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>

                        {{-- HTML Content (TinyMCE) --}}
                        <div class="col-md-12 mt-4">
                            <label for="html_content" class="form-label">Email HTML Content <span
                                    class="text-danger">*</span></label>
                            <textarea id="tinymce_editor" name="html_content" class="form-control" rows="10">{{ old('html_content') }}</textarea>
                            <span class="parsley-required"><strong id="html_content-error"></strong></span>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="col-12 mt-4 d-flex justify-content-end">
                        <button class="btn btn-primary me-1 mb-1" type="submit">{{ __('message.SAVE') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
