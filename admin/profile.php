<?php
require_once '../config.php';

if (!is_admin_logged_in()) {
    redirect('../login.php?role=admin');
}

$success = '';
$error = '';
$admin_id = $_SESSION['admin_id'];

// Fetch admin data from MySQL
$stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize_input($_POST['email']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            $error = 'Password must be 6+ characters';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Passwords do not match';
        } elseif (!password_verify($current_password, $admin['password_hash'])) {
            $error = 'Current password is incorrect';
        } else {
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE admins SET email = ?, password_hash = ? WHERE id = ?");
            $stmt->bind_param("ssi", $email, $password_hash, $admin_id);
            if ($stmt->execute()) {
                $success = 'Password updated';
                // Refresh admin data
                $admin['email'] = $email;
                $admin['password_hash'] = $password_hash;
            } else {
                $error = 'Failed to update password';
            }
            $stmt->close();
        }
    } else {
        $stmt = $conn->prepare("UPDATE admins SET email = ? WHERE id = ?");
        $stmt->bind_param("si", $email, $admin_id);
        if ($stmt->execute()) {
            $success = 'Profile updated';
            $admin['email'] = $email;
        } else {
            $error = 'Failed to update profile';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: calc(100vh - 56px); background-color: #fff; box-shadow: 2px 0 4px rgba(0,0,0,.08); }
        .sidebar .nav-link { color: #333; padding: 12px 20px; border-left: 3px solid transparent; }
        .main-content { padding: 25px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <i class="bi bi-mortarboard-fill me-2"></i>SMS Admin
        </a>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item active" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 sidebar">
            <ul class="nav flex-column pt-3">
                <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_students.php"><i class="bi bi-people"></i> Students</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_courses.php"><i class="bi bi-book"></i> Courses</a></li>
                <li class="nav-item"><a class="nav-link" href="enrollments.php"><i class="bi bi-clipboard-check"></i> Enrollments</a></li>
                <li class="nav-item"><a class="nav-link" href="notifications.php"><i class="bi bi-bell"></i> Notifications</a></li>
            </ul>
        </div>

        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            <h2 class="mb-4"><i class="bi bi-person-circle me-2"></i>My Profile</h2>

            <?php if ($success): show_alert($success, 'success'); endif; ?>
            <?php if ($error): show_alert($error, 'danger'); endif; ?>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header"><h5 class="mb-0">Profile Information</h5></div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($admin['username']); ?>" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header"><h5 class="mb-0">Change Password</h5></div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" class="form-control" name="current_password">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-control" name="new_password" minlength="6">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" name="confirm_password" minlength="6">
                                </div>
                                <button type="submit" class="btn btn-warning">Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
