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
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
        <link rel="stylesheet" href="assets/css/bodhivaas.css">
</head>

<body>
        <div class="container-fluid app-shell">
            <div class="app-topbar">
                <div class="brand"><div class="logo">B</div><div class="d-none d-md-block">Bodhivaas</div></div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-secondary" data-toggle-theme><i class="bi bi-moon-fill"></i></button>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-center" style="min-height:calc(100vh - 64px);">
                <div style="max-width:420px;width:100%;padding:1rem;">
                    <div class="card glass-card border-0">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h4 class="mb-0"><?php echo ucfirst($role); ?> Login</h4>
                                <small class="muted">Secure access to your dashboard</small>
                            </div>
                            <div class="text-end">
                                <div class="text-muted small">Welcome</div>
                            </div>
                        </div>

                        <?php if (isset($_SESSION['error'])): show_alert($_SESSION['error'], 'danger');
                                unset($_SESSION['error']);
                        endif; ?>

                        <form method="POST" action="php/code.php" class="mb-3">
                            <input type="hidden" name="role" value="<?php echo $role; ?>">
                            <div class="mb-3">
                                <label for="email" class="form-label small muted"><?php echo $role == 'admin' ? 'Username' : 'Email'; ?></label>
                                <input type="text" class="form-control" id="email" name="email" placeholder="Enter <?php echo $role == 'admin' ? 'username' : 'email'; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label small muted">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                            </div>

                            <div class="d-grid mb-2">
                                <button type="submit" class="btn btn-primary">Sign in</button>
                            </div>

                            <div class="d-flex justify-content-between small muted">
                                <div><?php if ($role == 'student'): ?><a href="register.php">Create account</a><?php endif; ?></div>
                                <div><a href="index.php">Back to Home</a></div>
                            </div>
                        </form>

                        <div class="alert alert-info small mb-0">
                            <strong>Demo</strong>: <?php echo $role == 'admin' ? 'Username: admin | Password: admin123' : 'Email: rahul.sharma@student.com | Password: student123'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="assets/js/bodhivaas.js"></script>
</body>

</html>