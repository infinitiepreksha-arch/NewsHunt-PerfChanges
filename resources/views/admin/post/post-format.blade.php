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
    </div>
@endsection
@section('content')
    <div class="container-xl">
        <div class="row row-cards">
            <!-- Image Post Card -->
            @can('create-post')
                <div class="col-md-6 col-lg-3">
                    <a href="{{ route('posts.create') }}" class="text-decoration-none">
                        <div class="card h-100 d-flex flex-column">
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-blue-lt"
                                style="height: 200px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                                    stroke-linejoin="round" class="text-blue">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M15 8h.01" />
                                    <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" />
                                    <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" />
                                    <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" />
                                </svg>
                            </div>
                            <div class="card-body d-flex flex-column flex-fill">
                                <div class="mb-3">
                                    <span class="badge bg-blue-lt">Image Post</span>
                                </div>
                                <h3 class="card-title" >Beautiful Landscape Photography</h3>
                                <p class="text-secondary flex-grow-1" >
                                    Capture stunning moments with high-resolution images. Perfect for showcasing photography,
                                    artwork, and visual content.
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            @endcan

            <!-- Video Post Card -->
            <div class="col-md-6 col-lg-3">
                <a href="{{ route('videos.create.custom') }}" class="text-decoration-none">
                    <div class="card h-100 d-flex flex-column">
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-purple-lt"
                            style="height: 200px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                                stroke-linejoin="round" class="text-purple">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M15 10l4.553 -2.276a1 1 0 0 1 1.447 .894v6.764a1 1 0 0 1 -1.447 .894l-4.553 -2.276v-4z" />
                                <path d="M3 6m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" />
                            </svg>
                        </div>
                        <div class="card-body d-flex flex-column flex-fill">
                            <div class="mb-3">
                                <span class="badge bg-purple-lt">Video Post</span>
                            </div>
                            <h3 class="card-title" >Upload Video Content</h3>
                            <p class="text-secondary flex-grow-1" >
                                Share engaging video content directly. Support for MP4, WebM, and other popular video
                                formats
                                with thumbnail previews.
                            </p>

                        </div>
                    </div>
                </a>
            </div>

            <!-- YouTube Post Card -->
            <div class="col-md-6 col-lg-3">
                <a href="{{ route('videos.create.youtube') }}" class="text-decoration-none">
                    <div class="card h-100 d-flex flex-column">
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-red-lt"
                            style="height: 200px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                                stroke-linejoin="round" class="text-red">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M2 8a4 4 0 0 1 4 -4h12a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-12a4 4 0 0 1 -4 -4v-8z" />
                                <path d="M10 9l5 3l-5 3z" />
                            </svg>
                        </div>
                        <div class="card-body d-flex flex-column flex-fill">
                            <div class="mb-3">
                                <span class="badge bg-red-lt">YouTube Post</span>
                            </div>
                            <h3 class="card-title" >Embed YouTube Videos</h3>
                            <p class="text-secondary flex-grow-1" >
                                Easily embed YouTube videos by simply pasting the URL. Automatic thumbnail and metadata
                                extraction included.
                            </p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Audio Post Card -->
            <div class="col-md-6 col-lg-3">
                <a href="{{ route('audios.create') }}" class="text-decoration-none">
                    <div class="card h-100 d-flex flex-column">
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-green-lt"
                            style="height: 200px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round"
                                stroke-linejoin="round" class="text-green">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                <path d="M13 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                <path d="M9 17v-13h10v13" />
                                <path d="M9 8h10" />
                            </svg>
                        </div>
                        <div class="card-body d-flex flex-column flex-fill">
                            <div class="mb-3">
                                <span class="badge bg-green-lt">Audio Post</span>
                            </div>
                            <h3 class="card-title" >Share Audio Files</h3>
                            <p class="text-secondary flex-grow-1">
                                Upload podcasts, music, or audio recordings. Supports MP3, WAV, and other audio formats with
                                custom player controls.
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
