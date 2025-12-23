<?php
require_once '../config.php';

if (!is_admin_logged_in()) {
    redirect('../login.php?role=admin');
}



$notifications = [];
$result = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #fff;
            box-shadow: 2px 0 4px rgba(0, 0, 0, .08);
        }

        .sidebar .nav-link {
            color: #333;
            padding: 12px 20px;
            border-left: 3px solid transparent;
        }

        .sidebar .nav-link.active {
            background-color: #f8f9fa;
            border-left-color: #0d6efd;
            color: #0d6efd;
        }

        .main-content {
            padding: 25px;
        }
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
                        <i class="bi bi-person-circle me-1"></i><?php echo $_SESSION['admin_username']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
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
                    <li class="nav-item"><a class="nav-link active" href="notifications.php"><i class="bi bi-bell"></i> Notifications</a></li>
                </ul>
            </div>

            <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-bell me-2"></i>Notifications</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-plus-circle me-1"></i>Post Notification
                    </button>
                </div>

                <?php if (isset($_SESSION['success'])): show_alert($_SESSION['success'], 'success');
                    unset($_SESSION['success']);
                endif; ?>
                <?php if (isset($_SESSION['error'])): show_alert($_SESSION['error'], 'danger');
                    unset($_SESSION['error']);
                endif; ?>

                <div class="row">
                    <?php if (count($notifications) > 0): ?>
                        <?php foreach ($notifications as $notif): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between">
                                        <h5 class="mb-0"><?php echo htmlspecialchars($notif['title']); ?></h5>
                                        <form action="../php/code.php" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                                            <input type="hidden" name="delete_notification" value="<?= htmlspecialchars($notif['id']) ?>">
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                    <div class="card-body">
                                        <p><?php echo htmlspecialchars(substr($notif['message'], 0, 100)); ?>...</p>
                                    </div>
                                    <div class="card-footer text-muted">
                                        <small><?php echo date('d M Y, h:i A', strtotime($notif['created_at'])); ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="card text-center py-5">
                                <p class="text-muted">No notifications posted yet</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Post Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../php/code.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">

                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" name="message" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="post_notification" class="btn btn-primary">Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>