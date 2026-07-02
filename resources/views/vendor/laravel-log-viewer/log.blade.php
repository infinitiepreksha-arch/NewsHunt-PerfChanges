<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex, nofollow">
    <title>Log Viewer | {{ $app_name ?? config('app.name') }}</title>
    <link rel="icon" href="{{ $favicon ?? asset('app/assets/images/logo/favicon.png') }}" type="image/x-icon" />
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/bootstrap.min.css') }}"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
          crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/log_viewer/logviewer.css') }}">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col sidebar mb-3">
            <h1><i class="fa fa-calendar" aria-hidden="true"></i> Log Viewer</h1>
            <div class="my-2">
                <a href="{{route('settings.index')}}" class="btn btn-primary">Back to Settings</a>
            </div>
            <div class="custom-control custom-switch">

                <input type="checkbox" class="custom-control-input" id="darkSwitch">
                <label class="custom-control-label mt-1" for="darkSwitch" >Dark Mode</label>
            </div>

            <div class="list-group div-scroll mt-3">
                @foreach($folders as $folder)
                    <div class="list-group-item">
                        <?php
                        \Rap2hpoutre\LaravelLogViewer\LaravelLogViewer::DirectoryTreeStructure($storage_path, $structure);
                        ?>

                    </div>
                @endforeach
                @foreach($files as $file)
                    <a href="?l={{ \Illuminate\Support\Facades\Crypt::encrypt($file) }}"
                       class="list-group-item @if ($current_file == $file) llv-active @endif">
                        {{$file}}
                    </a>
                @endforeach
            </div>
        </div>
        <div class="col-10 table-container">
            @if ($logs === null)
                <div>
                    Log file >50M, please download it.
                </div>
            @else
                <table id="table-log" class="table table-striped" data-ordering-index="{{ $standardFormat ? 2 : 0 }}">
                    <thead>
                    <tr>
                        @if ($standardFormat)
                            <th>Level</th>
                            <th>Context</th>
                            <th>Date</th>
                        @else
                            <th>Line number</th>
                        @endif
                        <th>Content</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($logs as $key => $log)
                        <tr data-display="stack{{{$key}}}">
                            @if ($standardFormat)
                                <td class="nowrap text-{{{$log['level_class']}}}">
                                    <span class="fa fa-{{{$log['level_img']}}}" aria-hidden="true"></span>&nbsp;&nbsp;{{$log['level']}}
                                </td>
                                <td class="text">{{$log['context']}}</td>
                            @endif
                            <td class="date">{{{$log['date']}}}</td>
                            <td class="text">
                                {{{$log['text']}}}
                                @if (isset($log['in_file']))
                                    <br/>{{{$log['in_file']}}}
                                @endif
                                @if ($log['stack'])
                                    <div class="mt-2 white-space" id="stack{{{$key}}}">{{{ trim($log['stack']) }}}</div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
            <div class="p-3">
                @if($current_file)
                    <a href="?dl={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}">
                        <span class="fa fa-download"></span> Download file
                    </a>
                    -
                    <a id="clean-log" href="?clean={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}">
                        <span class="fa fa-sync"></span> Clean file
                    </a>
                    -
                    <a id="delete-log" href="?del={{ \Illuminate\Support\Facades\Crypt::encrypt($current_file) }}{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}">
                        <span class="fa fa-trash"></span> Delete file
                    </a>
                    @if(count($files) > 1)
                        -
                        <a id="delete-all-log" href="?delall=true{{ ($current_folder) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($current_folder) : '' }}">
                            <span class="fa fa-trash-alt"></span> Delete all files
                        </a>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
<!-- jQuery for Bootstrap -->
<script src="{{ asset('assets/jquery/jquery-3.2.1.slim.min.js') }}"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script src="{{ asset('assets/bootstrap/bootstrap.min.js') }}"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
<!-- FontAwesome -->
<script defer src="{{asset('assets/js/custom/all.js')}}"></script>
<!-- Datatables -->
<script type="text/javascript" src="{{asset('assets/jquery/jquery.dataTables.min.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/jquery/dataTables.bootstrap4.min.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/js/custom/custom_ajex.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/js/log_theme/logtheme.js')}}"></script>
</body>
</html>
