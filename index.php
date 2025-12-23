<?php
require_once 'config.php';

if (is_admin_logged_in()) {
    redirect('admin/dashboard.php');
} elseif (is_student_logged_in()) {
    redirect('student/dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            /* Prevent scroll */
        }

        body {
            min-height: 100vh;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .main-card {
            max-width: 1000px;
            width: 100%;
            margin: auto;
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
            background: #fff;
        }

        .main-card .card-body {
            padding: 2.2rem 1.5rem;
        }

        .main-card .display-4 {
            font-size: 2rem;
        }

        .main-card .lead {
            font-size: 1rem;
        }

        .main-card .bi-mortarboard-fill {
            font-size: 3rem;
        }

        @media (max-width: 991px) {
            .main-card {
                max-width: 98vw;
            }
        }

        @media (max-width: 767px) {
            .main-card .card-body {
                padding: 1.2rem 0.5rem;
            }

            .main-card .display-4 {
                font-size: 1.3rem;
            }

            .main-card .bi-mortarboard-fill {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid h-100">
        <div class="row justify-content-center align-items-center h-100">
            <div class="col-12 col-md-10 col-lg-9">
                <div class="card main-card border-0 shadow-lg">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-mortarboard-fill text-primary"></i>
                        </div>
                        <h1 class="display-4 fw-bold mb-2">Student Management System</h1>
                        <p class="lead text-muted mb-4">A comprehensive platform for managing students, courses, and enrollments</p>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100 border-primary">
                                    <div class="card-body p-3">
                                        <div class="mb-2">
                                            <i class="bi bi-shield-lock-fill text-primary" style="font-size: 2rem;"></i>
                                        </div>
                                        <h3 class="h5 mb-2">Admin Panel</h3>
                                        <p class="text-muted mb-3">Manage students, courses, and enrollments</p>
                                        <a href="login.php?role=admin" class="btn btn-primary btn-lg w-100">
                                            <i class="bi bi-box-arrow-in-right me-2"></i>Admin Login
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-success">
                                    <div class="card-body p-3">
                                        <div class="mb-2">
                                            <i class="bi bi-person-fill text-success" style="font-size: 2rem;"></i>
                                        </div>
                                        <h3 class="h5 mb-2">Student Portal</h3>
                                        <p class="text-muted mb-3">Access your profile and enroll in courses</p>
                                        <a href="login.php?role=student" class="btn btn-success btn-lg w-100 mb-2">
                                            <i class="bi bi-box-arrow-in-right me-2"></i>Student Login
                                        </a>
                                        <a href="register.php" class="btn btn-outline-success w-100">
                                            <i class="bi bi-person-plus me-2"></i>Register Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>