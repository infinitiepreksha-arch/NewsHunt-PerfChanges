@extends('admin.layouts.main')

@section('title')
    {{ $title }}
@endsection

@section('pre-title')
    {{ $title }}
@endsection

@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">Home/</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title">
                @yield('title')
            </h2>
        </div>
        <div class="col-auto ms-auto d-print-none me-3">
            <a href="{{ route('topics.index') }}" class="btn btn-outline-primary">
                <i class="fa fa-list me-1"></i> All Topics
            </a>
        </div>
    </div>
@endsection


@section('content')
    <div class="container mt-4">

        {{-- Language Dropdown --}}
        <div class="card mb-3">
            <div class="card-body">
                <label class="form-label" for="languageFilter">Select News Language</label>
                <select id="languageFilter" class="form-select w-auto">
                    @foreach ($news_languages as $lang)
                        <option value="{{ $lang->id }}" {{ $lang->id == $default_lang_id ? 'selected' : '' }}>
                            {{ $lang->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Topics List --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Drag topics to reorder</h3>
            </div>
            <div class="card-body">
                <ul id="sortableTopics" class="list-group">
                    {{-- Populated by AJAX --}}
                </ul>
                {{-- <div class="mt-4">
                    <button id="saveOrder" class="btn btn-primary">Save Order
                    </button>
                </div> --}}

                <div class="modal-footer gap-2 mt-4">
                    <a href="{{ url('admin/posts') }}" id="back_button" class="btn btn-secondary">{{ __('page.BACK') }}
                    </a>
                    <button type="submit" id="saveOrder" class="btn btn-primary">{{ __('page.SAVE') }}
                    </button>
                </div>
            </div>
        </div>

    </div>
@endsection
