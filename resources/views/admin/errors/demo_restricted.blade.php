<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Restriction - Access Denied</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light min-vh-100 d-flex align-items-center justify-content-center">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7 col-sm-9">
                <!-- Main Card -->
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">

                    <!-- Card Body -->
                    <div class="card-body text-center p-5">
                        <!-- Warning Icon -->
                        <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                            style="width: 90px; height: 90px;">
                            <i class="fas fa-exclamation-triangle display-5 text-danger"></i>
                        </div>

                        <!-- Error Badge -->
                        <div class="mb-4">
                            <span
                                class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill fs-6">
                                <i class="fas fa-code me-2"></i>Error Code: 112
                            </span>
                        </div>

                        <!-- Message -->
                        <div class="mb-4">

                            <h5 class="mb-2">Do Not Have Permission</h5>
                            <p class="text-danger fw-semibold mb-3">This action is not allowed in the Demo Version.</p>
                        </div>
                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-md-6 d-grid gap-3">
                                <button onclick="history.back()" class="btn btn-danger btn-lg rounded-3 shadow-sm">
                                   Go Back
                                </button>
                            </div>
                            <div class="col-md-6 d-grid gap-3">
                                <a href="/" class="btn bg-primary btn-lg rounded-3 text-white">
                                    <i class="fas fa-home me-2"></i>Return Home
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
