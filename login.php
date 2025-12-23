<?php
require_once 'config.php';

$role = isset($_GET['role']) ? sanitize_input($_GET['role']) : 'student';
if (!in_array($role, ['admin', 'student'])) {
    $role = 'student';
}

if (is_admin_logged_in()) {
    redirect('admin/dashboard.php');
} elseif (is_student_logged_in()) {
    redirect('student/dashboard.php');
}

// Error message from redirect (if any)
// $error = isset($_GET['error']) ? sanitize_input($_GET['error']) : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($role); ?> Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, <?php echo $role == 'admin' ? '#667eea 0%, #764ba2' : '#56ab2f 0%, #a8e063'; ?> 100%);
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
        }

        .login-card {
            max-width: 340px;
            width: 100%;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(31, 38, 135, 0.12);
            background: #fff;
            margin: 24px auto;
            transition: box-shadow 0.2s;
        }

        .login-card:hover {
            box-shadow: 0 8px 28px rgba(31, 38, 135, 0.18);
        }

        .login-card .card-body {
            padding: 1.2rem 1rem 1rem 1rem;
        }

        .login-card .form-label {
            font-weight: 500;
            font-size: 0.98rem;
        }

        .login-card .display-6 {
            font-size: 1.15rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
        }

        .login-card .bi {
            font-size: 1.3rem;
        }

        .btn {
            font-size: 0.98rem;
            padding: 0.5rem 0;
            border-radius: 7px;
        }

        .alert-info {
            font-size: 0.92rem;
            border-radius: 7px;
            margin-top: 1rem;
        }

        .text-muted {
            font-size: 0.95rem;
        }

        @media (max-width: 575px) {
            .login-card {
                max-width: 98vw;
                margin: 8px auto;
            }

            .login-card .card-body {
                padding: 0.7rem 0.4rem 0.5rem 0.4rem;
            }

            .login-card .display-6 {
                font-size: 1rem;
            }

            .login-card .bi {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid h-100">
        <div class="row justify-content-center align-items-center h-100">
            <div class="col-12 col-sm-10 col-md-8 col-lg-7 col-xl-6">
                <div class="card login-card border-0 shadow-lg">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="bi <?php echo $role == 'admin' ? 'bi-shield-lock-fill text-primary' : 'bi-person-circle text-success'; ?>"></i>
                            <h2 class="display-6 mt-2 mb-1"><?php echo ucfirst($role); ?> Login</h2>
                            <p class="text-muted mb-0">Enter your credentials</p>
                        </div>
                        <?php if (isset($_SESSION['error'])): show_alert($_SESSION['error'], 'danger');
                            unset($_SESSION['error']);
                        endif; ?>
                        <form method="POST" action="php/code.php">
                            <input type="hidden" name="role" value="<?php echo $role; ?>">
                            <div class="mb-3">
                                <label for="email" class="form-label"><?php echo $role == 'admin' ? 'Username' : 'Email'; ?></label>
                                <input type="text" class="form-control" id="email" name="email" placeholder="Enter <?php echo $role == 'admin' ? 'username' : 'email'; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                            </div>
                            <button type="submit" class="btn <?php echo $role == 'admin' ? 'btn-primary' : 'btn-success'; ?> w-100 mb-2">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </button>
                            <div class="text-center">
                                <?php if ($role == 'student'): ?>
                                    <p class="mb-2">Don't have an account? <a href="register.php">Register here</a></p>
                                <?php endif; ?>
                                <a href="index.php" class="text-muted text-decoration-none">
                                    <i class="bi bi-arrow-left me-1"></i>Back to Home
                                </a>
                            </div>
                        </form>
                        <div class="alert alert-info mt-3 mb-0">
                            <small><strong>Demo:</strong><br>
                                <?php if ($role == 'admin'): ?>
                                    Username: admin | Password: admin123
                                <?php else: ?>
                                    Email: rahul.sharma@student.com | Password: student123
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>