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
            <!-- Page pre-title -->
            <div class="page-pretitle">
                <a href="{{url('admin/dashboard')}}">{{__('HOME')}}/</a>
                <a href="{{url('admin/posts')}}">{{__('POSTS')}}/</a>
                {{__('COMMENTS')}}
            </div>
            <h2 class="page-title">
                {{$post_id->title}}
            </h2>
        </div>
        <!-- Page title actions -->
        <div class="col-auto ms-auto d-print-none">
        </div>
    </div>
@endsection

@section('content')
    <section class="gradient-custom">
        <div class="container my-5 py-5">
            <div class="row d-flex justify-content-center">
                <div class="col-md-12 col-lg-10 col-xl-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered text-nowrap border-bottom" id="comments-table" data-url="{{ route('user-comments.show', 1) }}">
                                        <thead>
                                            <tr>
                                                <th class="wd-15p border-bottom-0">{{ __('ID') }}</th>
                                                <th class="wd-15p border-bottom-0">{{ __('user name') }}</th>
                                                <th class="wd-20p border-bottom-0">{{ __('comment') }}</th>
                                                <th class="wd-15p border-bottom-0">{{ __('date') }}</th>
                                                <th class="wd-15p border-bottom-0">{{ __('ACTION') }}</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="post_id" value="{{$post_id->id}}">
        <input type="hidden" id="comment_url" value="{{route('user-comments.show',0)}}">
    </section>
@endsection
