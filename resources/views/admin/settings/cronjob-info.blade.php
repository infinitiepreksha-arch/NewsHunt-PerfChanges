@extends('admin.layouts.main')
@section('title')
    {{ __('page.CRON_JOB_INFO') }}
@endsection
@section('pre-title')
    {{ __('page.CRON_JOB_INFO') }}
@endsection
@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <!-- Page pre-title -->
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('page.HOME') }}/</a>
                <a href="{{ url('admin/settings') }}">{{ __('page.SETTINGS') }}/</a>
                {{ __('page.CRON_JOB_INFO') }}
            </div>
        </div>
        <!-- Page title actions -->
        <div class="col-auto ms-auto d-print-none">
        </div>
    </div>
@endsection
@section('content')
    <section class="section mt-4">
        <div class="card admin_cards">
            <div class="card-header">
                <h2 class="card-title">{{ __('Cron job Info') }}</h2>
            </div>
            <div class="card-body">
                <div class="row mt-1">
                    <div class="card-body">
                        <div class="container mt-0">
                            <h1>How to Fetch RSS Feeds Using a Cron Job</h1>
                            <p>To fetch RSS feeds, you need to set up a <strong>cron job</strong>. Once configured, the cron
                                job will run automatically at specified intervals. Follow these steps to set up your cron
                                job in cPanel:</p>

                            <h2>Methods to Set Up Cron Jobs in cPanel</h2>
                            <p>There are <strong>two ways</strong> to configure cron jobs in cPanel:</p>
                            <ol>
                                <li><strong>Configuring cPanel Cron Jobs in WHM</strong></li>
                                <li><strong>Configuring Cron Jobs Directly in cPanel</strong></li>
                            </ol>

                            <h3>Step 1: Configuring cPanel Cron Jobs in WHM</h3>
                            <ol>
                                <li>Log in to your server.</li>
                                <li>In the sidebar menu, go to <strong>Server Configuration</strong> and select
                                    <strong>Configure cPanel Cron Jobs</strong>.
                                </li>
                                <li>
                                    You will see five fields to define the time intervals. Set these fields to determine how
                                    frequently the cron job will run.
                                    You can adjust these values to your preferred schedule.
                                </li>
                            </ol>

                            <h3>Step 2: Configuring Cron Jobs in cPanel</h3>
                            <ol>
                                <li>In cPanel, select <strong>Cron Jobs</strong> in the <strong>Advanced</strong> section of
                                    the main menu.</li>
                                <li>
                                    You will find a table for adding new scripts and setting their time intervals.
                                    If you don’t find a setting that fits your needs in the main drop-down menu, you can:
                                    <ul>
                                        <li>Enter a time interval manually in the fields on the left.</li>
                                        <li>Or use the dropdown menus on the right to select five minuets time entries for each period. but you can it as per your required time.</li>
                                    </ul>
                                </li>
                                <li>
                                    Enter the command or path to your script in the <strong>Command</strong> field and click
                                    <strong>Add New Cron Job</strong> to save.
                                    The cron job will now run your script automatically at the intervals you selected.
                                </li>
                            </ol>

                            <h3>Example Command</h3>
                            <p>Use the following type of URL in the cPanel command field:</p>

                            <pre>@if (env('DEMO_MODE'))YOUR/PATH/ABSOLUTE/PATH/TO/DOCUMENT/ROOT/artisan @else{{ $_SERVER['DOCUMENT_ROOT'] . '/artisan' }}@endif schedule:run >> /dev/null 2>&1</pre>

                            <p>If you have any confusion, refer to this documentation:</p>
                            <a href="https://cpanel.net/blog/tips-and-tricks/how-to-configure-a-cron-job/"
                                target="_blank"><b>Reference Link</b></a><br>
                            <div class="mt-3">
                                <b>Note:</b> if you do not want to setup in cpanel you can run it manually from the rss
                                feeds in sidebar click on it and you will see option to Sync feeds button..
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
