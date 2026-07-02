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
                <a href="{{ url('admin/dashboard') }}">{{ __('page.HOME') }}/</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title">
                @yield('title')
            </h2>
        </div>
        @if ($news_language_status === 'active')
            @can('create-newslanguage')
                <div class="col-auto ms-auto d-print-none gap-1">
                    <a class="btn btn-primary" href="#" data-bs-toggle="modal"
                        data-bs-target="#addNewsLanguageModal">{{ __('page.CREATE_NEWSLANGUAGE') }}</a>
                </div>
            @endcan
        @endif
    </div>
@endsection

@if ($news_language_status === 'inactive')
    @section('content')
        @csrf
        <section class="section">
            <div class="col-12 mt-0">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="text-danger fs-20">
                            {{ __('page.NEWS_LANGUAGE_ACCESS_BLOCKED') }}
                        </p>
                        <a href="{{ route('settings.newslanguage_section') }}"
                            class="btn btn-primary m-5 mt-0 mb-0 p-8 pt-1 pb-1">
                            <i class="bi bi-gear m-1"></i> {{ __('page.NEWS_LANGUAGE_SETTIGNG') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endsection
@elseif ($news_language_status === 'active')
    @section('content')
        <section class="section">
            @can('list-newslanguage')
                <div class="col-12 mt-0">
                    <div class="alert alert-primary alert-dismissible" role="alert">
                        {{-- Add this to tags --}}
                        {{ __('page.DRAG_DROP_INSTRUCTION') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">

                        @if ($news_languages->isEmpty())
                            <p class="text-center">{{ __('page.NO_NEWS_LANGUAGES_FOUND') }}</p>
                        @else
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table id="sortable" class="table table-bordered text-nowrap border-bottom sortable">
                                            <thead class="text-center">
                                                <tr>
                                                    <th>{{ __('global.ID') }}</th>
                                                    <th>{{ __('global.IMAGE') }}</th>
                                                    <th>{{ __('global.NAME') }}</th>
                                                    <th>{{ __('global.CODE') }}</th>
                                                    <th>{{ __('global.DEFAULT_LANGUAGE') }}</th>
                                                    <th>{{ __('global.STATUS') }}</th>
                                                    <th>{{ __('global.ACTION') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-center">
                                                @foreach ($news_languages as $news_language)
                                                    <tr data-id="{{ $news_language->id }}">
                                                        <td>{{ $news_language->id }}</td>
                                                        <td>
                                                            <img src="{{ $news_language->image ? asset('storage/' . $news_language->image) : asset('assets/images/no_image_available.png') }}"
                                                                alt="Language Image" class="news-language-index-image-css">
                                                        </td>
                                                        <td>{{ $news_language->name }}</td>
                                                        <td>{{ $news_language->code }}</td>
                                                        <td class="default-lang-cell"
                                                            data-can-reorder="{{ auth()->user()->can('reorder-newslanguage') ? 1 : 0 }}"
                                                            data-id="{{ $news_language->id }}">
                                                            @if ($news_language->is_active == 1)
                                                                <span
                                                                    class="badge bg-success text-white">{{ __('global.YES') }}</span>
                                                            @else
                                                                <span
                                                                    class="badge bg-danger text-white">{{ __('global.NO') }}</span>
                                                            @endif
                                                        </td>

                                                        <td>
                                                            @if ($news_language->is_active != 1)
                                                                @can('status-newslanguage')
                                                                    <div class="d-flex align-items-center justify-content-center">
                                                                        <div class="form-check form-switch me-2">
                                                                            <input
                                                                                class="form-check-input news-language-status-toggle"
                                                                                type="checkbox"
                                                                                id="status-toggle-{{ $news_language->id }}"
                                                                                data-id="{{ $news_language->id }}"
                                                                                {{ $news_language->status == 'active' ? 'checked' : '' }}>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <span class='badge bg-primary text-white m-1'>No permission for
                                                                        Change Status.</span>
                                                                @endcan
                                                            @else
                                                                <div class="text-center text-muted"></div>
                                                            @endif
                                                        </td>

                                                        <td>
                                                            @if ($news_language->is_active != 1)
                                                                @can('update-newslanguage')
                                                                    <a href="#" class="btn text-primary btn-sm edit_btn"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#editNewsLanguageModal"
                                                                        data-id="{{ $news_language->id }}"
                                                                        data-name="{{ $news_language->name }}"
                                                                        data-code="{{ $news_language->code }}"
                                                                        data-status="{{ $news_language->status }}"
                                                                        data-update-url="{{ route('news_languages.update', '') }}">
                                                                        <i class='fa fa-pen'></i>
                                                                    </a>
                                                                @else
                                                                    <span class='badge bg-primary text-white m-1'>No permission for
                                                                        Edit.</span>
                                                                @endcan

                                                                @can('delete-newslanguage')
                                                                    <form id="news-language-delete-form-{{ $news_language->id }}"
                                                                        action="{{ route('admin.news-languages.destroy', $news_language->id) }}"
                                                                        method="POST" class="news-language-delete-css">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="btn text-danger btn-sm news-language-delete-form"
                                                                            data-id="{{ $news_language->id }}">
                                                                            <i class='fa fa-trash'></i>
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <span class='badge bg-danger text-white m-1'>No permission for
                                                                        Delete</span>
                                                                @endcan
                                                            @else
                                                                <div class="text-center text-muted"></div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="col-12 text-center py-5">
                    <h1 class="display-1 fw-bold text-danger">403</h1>
                    <h1 class="fw-bold mb-0 text-danger">Access Denied</h1>
                    <div class="d-flex justify-content-center mb-0">
                        <div class="col-6 col-md-8 col-lg-4">
                            <img src="{{ asset('assets/images/access_Denied/no permission.png') }}" alt="Access Denied">
                        </div>
                    </div>

                    <div class="d-inline-block">
                        <h3 class="text-danger mb-0">You do not have permission to view the list of News Languages .
                        </h3>
                    </div>
                </div>
            @endcan
        </section>

        @include('admin.models.news-language-model')
    @endsection
@endif
