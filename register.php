<?php
require_once 'config.php';

if (is_student_logged_in()) {
    redirect('student/dashboard.php');
}

$error = isset($_GET['error']) ? sanitize_input($_GET['error']) : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, rgb(121, 211, 121) 0%, #f0f9ff 100%);
            min-height: 100vh;
        }

        .register-card {
            border-radius: 1rem;
            background: #fff;
            padding: 2.5rem 2rem;
            box-shadow: 0 8px 32px rgba(60, 180, 75, 0.08);
        }

        .card-body {
            border-radius: 1rem;
        }

        .bi-person-plus-fill {
            font-size: 3rem;
        }

        .form-label {
            font-weight: 500;
        }

        .form-control:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.15);
        }

        .btn-success {
            font-weight: 600;
            font-size: 1.1rem;
            padding: 0.75rem 0;
            border-radius: 0.5rem;
            transition: background 0.2s;
        }

        .btn-success:hover {
            background: #157347;
        }

        .text-muted a {
            color: #198754 !important;
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .register-card {
                padding: 1.2rem 0.5rem;
            }

            .card-body {
                padding: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid py-5">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="card register-card shadow-lg border-0">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="bi bi-person-plus-fill text-success"></i>
                            <h2 class="mt-3 mb-1">Student Registration</h2>
                            <p class="text-muted">Create your account to get started</p>
                        </div>
                        <?php if ($error): show_alert($error, 'danger');
                        endif; ?>
                        <form method="POST" action="php/code.php">
                            <input type="hidden" name="action" value="register">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter phone number">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter your address"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" minlength="6" required>
                                <small class="form-text text-muted">At least 6 characters</small>
                            </div>
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm password" minlength="6" required>
                            </div>
                            <button name="register" type="submit" class="btn btn-success w-100 mb-3">
                                <i class="bi bi-person-plus me-2"></i>Register
                            </button>
                            <div class="text-center">
                                <p class="mb-2">Already have an account? <a href="login.php?role=student">Login here</a></p>
                                <a href="index.php" class="text-muted text-decoration-none">
                                    <i class="bi bi-arrow-left me-1"></i>Back to Home
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>