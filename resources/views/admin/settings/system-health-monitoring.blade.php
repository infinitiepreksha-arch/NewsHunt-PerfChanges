@extends('admin.layouts.main')

@section('title')
    {{ __('page.SYSTEM_HEALTH') }}
@endsection

@section('pre-title')
    {{ __('page.SYSTEM_HEALTH') }}
@endsection

@section('page-title')
    <div class="page-pretitle">
        {{ __('page.HOME') }}/ {{ __('page.SETTINGS') }}/ {{ __('page.SYSTEM_HEALTH') }}
    </div>
    <h2 class="page-title">System Health</h2>
    <div class="text-muted">Monitor and manage your system requirements and configurations</div>
@endsection

@section('content')
    {{-- PHP Version Cards --}}
    <div class="row row-cards mb-3">
        <div class="col-12 col-sm-6 col-lg-4 mb-3 mb-lg-0">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center flex-wrap">
                        <div class="me-3 mb-2 mb-sm-0">
                            @if ($data['php']['status'])
                                <span class="badge text-white bg-success badge-pill">✓</span>
                            @else
                                <span class="badge text-white bg-danger badge-pill">✗</span>
                            @endif
                        </div>
                        <div class="flex-fill">
                            <h3 class="m-0">Current PHP Version</h3>
                            <div class="text-muted">
                                <strong class="text-primary">{{ $data['php']['version'] }}</strong>
                            </div>
                            <small class="text-muted">Currently running version</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4 mb-3 mb-lg-0">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="m-0">Minimum Required</h3>
                    <div class="text-muted">
                        <strong>{{ $data['php']['min_required'] }}</strong>
                    </div>
                    <small class="text-muted">Minimum supported version</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="m-0">Maximum Required</h3>
                    <div class="text-muted">
                        <strong>{{ $data['php']['max_required'] }}</strong>
                    </div>
                    <small class="text-muted">Maximum supported version</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Database Status --}}
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">
                <span class="me-2">🗄️</span> Database Status
            </h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>Status:</strong>
                @if ($data['database']['status'])
                    <span class="badge text-white bg-success ms-2 d-inline-block mt-1">✓ Database connection is
                        working</span>
                @else
                    <span class="badge text-white bg-danger ms-2 d-inline-block mt-1">✗ Database connection failed</span>
                @endif
            </div>

            @if ($data['database']['status'])
                <div class="mb-2">
                    <strong>Details:</strong>
                </div>
                <ul class="mb-0 ps-3">
                    <li class="mb-1"><strong>Database Name:</strong> {{ $data['database']['name'] }}</li>
                    <li><strong>Driver:</strong> {{ $data['database']['driver'] }}</li>
                </ul>
            @else
                <div class="alert alert-danger mb-0">
                    <strong>Error:</strong> <span class="d-block d-sm-inline mt-1">{{ $data['database']['error'] }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Directory Permissions --}}
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">
                <span class="me-2">📁</span> Directory Permissions
            </h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>Status:</strong>
                @if ($data['permissions']['status'])
                    <span class="badge text-white bg-success ms-2 d-inline-block mt-1">✓ All directories are writable</span>
                @else
                    <span class="badge text-white bg-danger ms-2 d-inline-block mt-1">✗ Some directories need
                        attention</span>
                @endif
            </div>

            @if (!$data['permissions']['status'] && count($data['permissions']['unwritable_directories']) > 0)
                <div class="alert alert-warning">
                    <strong>Unwritable Directories:</strong>
                    <ul class="mb-0 mt-2 ps-3">
                        @foreach ($data['permissions']['unwritable_directories'] as $dir)
                            <li class="mb-2">
                                <strong>{{ $dir['name'] }}:</strong>
                                <span class="d-block d-sm-inline text-break">{{ $dir['path'] }}</span>
                                @if (!$dir['exists'])
                                    <span class="badge text-white bg-danger ms-sm-2 d-inline-block mt-1">Not Exists</span>
                                @elseif(!$dir['writable'])
                                    <span class="badge text-white bg-warning ms-sm-2 d-inline-block mt-1">Not
                                        Writable</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-3">
                <strong>All Directories:</strong>
                <div class="table-responsive mt-2">
                    <table class="table table-bordered table-sm table-vcenter">
                        <thead>
                            <tr>
                                <th class="w-auto">Directory</th>
                                <th class="d-none d-md-table-cell">Path</th>
                                <th class="text-center">Exists</th>
                                <th class="text-center">Writable</th>
                                <th class="text-center d-none d-lg-table-cell">Readable</th>
                                <th class="d-none d-xl-table-cell">Permissions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['permissions']['paths'] as $name => $permission)
                                <tr>
                                    <td>
                                        <strong>{{ $name }}</strong>
                                        <small
                                            class="d-block d-md-none text-muted text-break">{{ $permission['path'] }}</small>
                                    </td>
                                    <td class="d-none d-md-table-cell"><small
                                            class="text-break">{{ $permission['path'] }}</small></td>
                                    <td class="text-center">
                                        @if ($permission['exists'])
                                            <span class="badge text-white bg-success">✓</span>
                                        @else
                                            <span class="badge text-white bg-danger">✗</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($permission['writable'])
                                            <span class="badge text-white bg-success">✓</span>
                                        @else
                                            <span class="badge text-white bg-danger">✗</span>
                                        @endif
                                    </td>
                                    <td class="text-center d-none d-lg-table-cell">
                                        @if ($permission['readable'])
                                            <span class="badge text-white bg-success">✓</span>
                                        @else
                                            <span class="badge text-white bg-danger">✗</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-xl-table-cell">{{ $permission['permissions'] ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- System Requirements & Extensions --}}
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">
                <span class="me-2">⚙️</span> System Requirements & Extensions
            </h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>Status:</strong>
                @if ($data['extensions']['healthy'])
                    <span class="badge text-white bg-success ms-2 d-inline-block mt-1">✓ All required extensions are
                        installed</span>
                @else
                    <span class="badge text-white bg-danger ms-2 d-inline-block mt-1">✗ Missing
                        {{ $data['extensions']['total_missing'] }} extension(s)</span>
                @endif
            </div>

            @if (!$data['extensions']['healthy'])
                <div class="alert alert-danger">
                    <strong>Missing Extensions:</strong>
                    <ul class="mb-0 mt-2 ps-3">
                        @foreach ($data['extensions']['missing'] as $ext)
                            <li><code>{{ $ext }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $extensionsInfo = [
                    'pdo_mysql' => [
                        'name' => 'PDO MySQL Extension',
                        'icon' => '🗄️',
                        'desc' => 'Required for database operations using PDO.',
                    ],
                    'mbstring' => [
                        'name' => 'Mbstring Extension',
                        'icon' => '🔤',
                        'desc' => 'Required for string handling and encoding.',
                    ],
                    'fileinfo' => [
                        'name' => 'Fileinfo Extension',
                        'icon' => '📁',
                        'desc' => 'Required for identifying file types (uploads).',
                    ],
                    'openssl' => [
                        'name' => 'OpenSSL Extension',
                        'icon' => '🔒',
                        'desc' => 'Required for secure HTTPS communication.',
                    ],
                    'tokenizer' => [
                        'name' => 'Tokenizer Extension',
                        'icon' => '✂️',
                        'desc' => 'Required for Laravel and PHP code parsing.',
                    ],
                    'json' => [
                        'name' => 'JSON Extension',
                        'icon' => '📘',
                        'desc' => 'Required for JSON encoding/decoding.',
                    ],
                    'curl' => [
                        'name' => 'cURL Extension',
                        'icon' => '🌐',
                        'desc' => 'Required for API calls and external requests.',
                    ],
                    'zip' => [
                        'name' => 'Zip Extension',
                        'icon' => '📦',
                        'desc' => 'Used for system updates and Zip-based processes.',
                    ],
                    'shell_exec' => [
                        'name' => 'Shell Exec',
                        'icon' => '🖥️',
                        'desc' => 'Required for executing shell commands on server.',
                    ],
                ];
                $index = 1;
            @endphp

            <div class="table-responsive mt-3">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th class="d-none d-sm-table-cell">#</th>
                            <th>EXTENSION / SERVICE</th>
                            <th class="d-none d-lg-table-cell">DESCRIPTION</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($extensionsInfo as $key => $ext)
                            @php
                                if ($key === 'shell_exec') {
                                    $isInstalled = $data['extensions']['shell_exec'];
                                } else {
                                    $isInstalled = in_array($key, $data['extensions']['installed']);
                                }
                            @endphp
                            <tr>
                                <td class="d-none d-sm-table-cell">{{ $index++ }}</td>
                                <td>
                                    <div class="d-flex align-items-start flex-column flex-sm-row">
                                        <span class="me-2">{{ $ext['icon'] }}</span>
                                        <div>
                                            <strong>{{ $ext['name'] }}</strong>
                                            <small class="d-block d-lg-none text-muted mt-1">{{ $ext['desc'] }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    {{ $ext['desc'] }}
                                </td>
                                <td>
                                    @if ($isInstalled)
                                        <span class="badge text-white bg-success text-nowrap">✓ Installed</span>
                                    @else
                                        <span class="badge text-white bg-danger text-nowrap">✗ Missing</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Notification Settings --}}
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24"
                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                    <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                </svg>
                Notification Settings
            </h3>
        </div>

        <div class="card-body">
            <p class="mb-2 text-info">
                <strong>Reference:</strong> Visit console.firebase.google.com to create and manage your Firebase
                project, generate API keys, and download the service account file required for push notifications.
            </p>

            <div class="mb-3">
                <a href="https://console.firebase.google.com/" target="_blank" class="btn btn-sm btn-info">
                    Visit Firebase Console
                </a>
            </div>

            <div class="alert alert-info mb-0">
                <h4 class="alert-title">Firebase Push Notifications Setup</h4>
                <div class="text-muted">
                    To enable Application Push Notifications, please complete these steps.<br>
                    <strong class="d-block mt-2">Configure Firebase Settings here:</strong>
                    <a href="{{ url('admin/settings/firebase') }}" class="fw-bold text-break d-inline-block mt-1">
                        {{ url('admin/settings/firebase') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Email Settings --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24"
                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <rect x="3" y="5" width="18" height="14" rx="2" />
                    <polyline points="3 7 12 13 21 7" />
                </svg>
                Email Settings
            </h3>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <div class="d-flex">
                    <div>
                        <h4 class="alert-title">SMTP Configuration Required</h4>
                        <div class="text-muted">You need to set SMTP Email Settings for Email Notification.</div>
                        <strong>Configure SMTP Settings here:</strong>
                        <a href="{{ url('/admin/settings/smtp_mail_configuration') }}" class="fw-bold">
                            {{ url('/admin/settings/smtp_mail_configuration') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Gateway Settings --}}
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24"
                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <rect x="3" y="5" width="18" height="14" rx="3" />
                    <line x1="3" y1="10" x2="21" y2="10" />
                    <line x1="7" y1="15" x2="7.01" y2="15" />
                    <line x1="11" y1="15" x2="13" y2="15" />
                </svg>
                Payment Gateway Settings
            </h3>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Razorpay --}}
                <div class="col-12 col-md-6">
                    <div class="card card-link card-link-pop h-100">
                        <div class="card-body">
                            <a href="https://razorpay.com/" class="text-decoration-none">
                                <div class="d-flex align-items-center">
                                    <span class="avatar rounded me-3 flex-shrink-0" style="background-color: #0c83f5;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="white">
                                            <rect x="3" y="5" width="18" height="14" rx="2" />
                                        </svg>
                                    </span>
                                    <div class="flex-fill">
                                        <div class="fw-bold">Razorpay Payments</div>
                                        <div class="text-muted small">Create Razorpay business account</div>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted flex-shrink-0 ms-2"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <polyline points="9 6 15 12 9 18" />
                                    </svg>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Stripe --}}
                <div class="col-12 col-md-6">
                    <div class="card card-link card-link-pop h-100">
                        <div class="card-body">
                            <a href="https://stripe.com/in" class="text-decoration-none">
                                <div class="d-flex align-items-center">
                                    <span class="avatar rounded me-3 bg-dark flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="white">
                                            <rect x="3" y="5" width="18" height="14" rx="2" />
                                        </svg>
                                    </span>
                                    <div class="flex-fill">
                                        <div class="fw-bold">Stripe Payments</div>
                                        <div class="text-muted small">Create Stripe business account</div>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted flex-shrink-0 ms-2"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <polyline points="9 6 15 12 9 18" />
                                    </svg>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Stripe --}}
                <div class="col-12 col-md-6">
                    <div class="card card-link card-link-pop h-100">
                        <div class="card-body">
                            <a href="https://developer.apple.com/in-app-purchase/" class="text-decoration-none">
                                <div class="d-flex align-items-center">
                                    <span class="avatar rounded me-3 bg-dark flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="white">
                                            <rect x="3" y="5" width="18" height="14" rx="2" />
                                        </svg>
                                    </span>
                                    <div class="flex-fill">
                                        <div class="fw-bold">In App Purchase Payments</div>
                                        <div class="text-muted small">Create In App Purchase account</div>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted flex-shrink-0 ms-2"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <polyline points="9 6 15 12 9 18" />
                                    </svg>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Authentication Settings --}}
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">
                <span class="me-2">🔐</span> Authentication Settings
            </h3>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-3">
                <h4 class="alert-title">Firebase Configuration</h4>
                <p class="mb-0">Setup Firebase authentication</p>
            </div>
            <div class="alert alert-warning mb-0">
                <h4 class="alert-title">Firebase Configuration</h4>
                <p class="mb-0">
                    Configure Firebase Settings here:
                    <a href="{{ url('admin/settings/firebase') }}" class="text-break d-block d-sm-inline mt-1">
                        {{ url('admin/settings/firebase') }}
                    </a>
                </p>
            </div>
        </div>
    </div>

    {{-- Upload Limits --}}
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">
                <span class="me-2">📤</span> Upload Limits
            </h3>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-3">
                Ensure these values are larger than the files you upload.
            </div>
            <ul class="mb-0 ps-3">
                <li class="mb-1"><strong>postMaxSize:</strong> {{ $data['upload_limits']['post_max_size'] }}</li>
                <li><strong>uploadMaxFilesize:</strong> {{ $data['upload_limits']['upload_max_filesize'] }}</li>
            </ul>
        </div>
    </div>

    {{-- System Update --}}
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">
                <span class="me-2">🔄</span> System Update
            </h3>
        </div>
        <div class="card-body">

            <div class="alert alert-warning mb-0">
                <p>Please make sure that the version file you upload is included in the proper sequence. The version file must be uploaded in the correct order.</p>
                <p class="mb-1">
                ⚠️ Make sure that <strong>post_max_size</strong> and <strong>upload_max_filesize</strong> settings on your
                server are always greater than the size of files you want to upload.
                If they are smaller, file uploads may fail.
            </p>

            <p class="mb-0">
                For complete instructions, please follow our -->
                <a href="https://newshunt.infinitietech.com/public/documentation/web/index.html" target="_blank"
                    class="text-primary text-decoration-underline">
                   <strong> documentation </strong>
                </a> .
            </p>
                <a href="{{ url('admin/settings/system-update/index') }}" class="text-break d-block d-sm-inline mt-1">
                    {{ url('admin/settings/system-update/index') }}
                </a>
            </div>
        </div>
    </div>
@endsection
