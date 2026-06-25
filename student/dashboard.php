<?php
require_once '../config.php';

// session_start();

if (!isset($_SESSION['student_id'])) {
    header('Location: ../login.php?role=student');
    exit;
}

$student_id = $_SESSION['student_id'];

// Fetch student data
$student = null;
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
}
$stmt->close();

// Fetch enrollments
$enrollments = [];
$stmt = $conn->prepare("SELECT * FROM enrollments WHERE student_id = ? AND status = 'active'");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $enrollments[] = $row;
}
$stmt->close();

// Fetch notifications
$notifications = [];
$sql = "SELECT * FROM notifications ORDER BY created_at DESC LIMIT 3";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

// Helper function for date formatting
// function format_date($date)
// {
//     return date('d M Y', strtotime($date));
// }

// Fetch course names for enrollments
$course_names = [];
if (count($enrollments) > 0) {
    $course_ids = array_map(function ($e) {
        return $e['course_id'];
    }, $enrollments);
    $in = implode(',', array_fill(0, count($course_ids), '?'));
    $types = str_repeat('i', count($course_ids));
    $stmt = $conn->prepare("SELECT id, course_name FROM courses WHERE id IN ($in)");
    $stmt->bind_param($types, ...$course_ids);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $course_names[$row['id']] = $row['course_name'];
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Student Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
        <link rel="stylesheet" href="../assets/css/bodhivaas.css">
</head>

<body>
        <div class="container-fluid app-shell">
            <div class="app-topbar">
                <div class="brand"><div class="logo">B</div><div class="d-none d-md-block">Bodhivaas</div></div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-secondary" data-toggle-sidebar><i class="bi bi-list"></i></button>
                    <button class="btn btn-sm btn-outline-secondary" data-toggle-theme><i class="bi bi-moon-fill"></i></button>
                    <div class="dropdown">
                        <a class="text-muted text-decoration-none dropdown-toggle" href="#" data-bs-toggle="dropdown"><?php echo htmlspecialchars($_SESSION['student_name']); ?></a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="app-layout">
                <aside class="app-sidebar">
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="dashboard.php"><i class="bi bi-speedometer2"></i> <span class="nav-label">Dashboard</span></a>
                        <a class="nav-link" href="my_courses.php"><i class="bi bi-book"></i> <span class="nav-label">My Courses</span></a>
                        <a class="nav-link" href="enroll_course.php"><i class="bi bi-plus-circle"></i> <span class="nav-label">Enroll</span></a>
                        <a class="nav-link" href="profile.php"><i class="bi bi-person"></i> <span class="nav-label">Profile</span></a>
                    </nav>
                </aside>

                <main class="app-main">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h4 class="mb-0">Welcome, <?php echo htmlspecialchars($_SESSION['student_name']); ?></h4>
                            <small class="muted">Here's your academic overview</small>
                        </div>
                        <div>
                            <a href="enroll_course.php" class="btn btn-sm btn-primary">Enroll Course</a>
                        </div>
                    </div>

                    <div class="dash-cards mb-3">
                        <div class="dash-card">
                            <div>
                                <div class="meta">Active Courses</div>
                                <div class="value"><?php echo count($enrollments); ?></div>
                            </div>
                            <div class="iconbox"><i class="bi bi-journal-bookmark-fill"></i></div>
                        </div>
                        <div class="dash-card">
                            <div>
                                <div class="meta">Announcements</div>
                                <div class="value"><?php echo count($notifications); ?></div>
                            </div>
                            <div class="iconbox"><i class="bi bi-bell-fill"></i></div>
                        </div>
                        <div class="dash-card">
                            <div>
                                <div class="meta">Member Since</div>
                                <div class="value"><?php echo $student ? format_date($student['created_at']) : '-'; ?></div>
                            </div>
                            <div class="iconbox"><i class="bi bi-person-fill"></i></div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <h6 class="mb-0">Your Courses</h6>
                                    <a href="enroll_course.php" class="btn btn-sm btn-primary">Enroll More</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-modern mb-0">
                                        <thead><tr><th>Course Code</th><th>Course Name</th><th>Status</th><th>Enrolled Date</th></tr></thead>
                                        <tbody>
                                            <?php if (count($enrollments) > 0): foreach ($enrollments as $e): ?>
                                                <tr>
                                                    <td><span class="badge bg-primary"><?php echo 'COURSE ' . substr($e['course_id'], 0, 6); ?></span></td>
                                                    <td><?php echo isset($course_names[$e['course_id']]) ? htmlspecialchars($course_names[$e['course_id']]) : 'Course Name'; ?></td>
                                                    <td><span class="badge bg-success">Active</span></td>
                                                    <td><?php echo format_date($e['enrollment_date']); ?></td>
                                                </tr>
                                            <?php endforeach; else: ?>
                                                <tr><td colspan="4" class="text-center muted py-4">No enrolled courses</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card glass-card mb-3">
                                <div class="card-header"><h6 class="mb-0">Announcements</h6></div>
                                <div class="card-body p-0">
                                    <?php if (count($notifications) > 0): foreach ($notifications as $notif): ?>
                                        <div class="p-3 border-bottom">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($notif['title']); ?></h6>
                                            <p class="muted small mb-0"><?php echo htmlspecialchars(substr($notif['message'], 0, 60)); ?>...</p>
                                            <small class="muted"><?php echo format_date($notif['created_at']); ?></small>
                                        </div>
                                    <?php endforeach; else: ?>
                                        <div class="p-3 text-center muted">No announcements</div>
                                    <?php endif; ?></div>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <h6 class="mb-2">Profile</h6>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="profile-avatar"><?php echo strtoupper(substr($_SESSION['student_name'],0,1)); ?></div>
                                        <div>
                                            <div class="fw-bold"><?php echo htmlspecialchars($_SESSION['student_name']); ?></div>
                                            <div class="muted small"><?php echo htmlspecialchars($_SESSION['student_email']); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="../assets/js/bodhivaas.js"></script>
</body>

</html>