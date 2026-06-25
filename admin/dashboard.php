<?php
require_once '../config.php';

if (!is_admin_logged_in()) {
    redirect('../login.php?role=admin');
}

// Fetch statistics using MySQL
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Total students
$result = $conn->query("SELECT COUNT(*) AS count FROM students");
$total_students = $result ? $result->fetch_assoc()['count'] : 0;

// Total courses
$result = $conn->query("SELECT COUNT(*) AS count FROM courses");
$total_courses = $result ? $result->fetch_assoc()['count'] : 0;

// Active enrollments
$result = $conn->query("SELECT COUNT(*) AS count FROM enrollments WHERE status='active'");
$total_enrollments = $result ? $result->fetch_assoc()['count'] : 0;

// Recent students
$recent_students = [];
$result = $conn->query("SELECT id, name, email, phone, created_at FROM students ORDER BY created_at DESC LIMIT 5");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recent_students[] = $row;
    }
}


// Recent enrollments
$recent_enrollments = [];
$sql = "SELECT e.id, e.enrollment_date, s.name AS student_name, c.course_name, c.course_code
        FROM enrollments e
        JOIN students s ON e.student_id = s.id
        JOIN courses c ON e.course_id = c.id
        ORDER BY e.enrollment_date DESC
        LIMIT 5";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recent_enrollments[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
        <link rel="stylesheet" href="../assets/css/bodhivaas.css">
</head>

<body>
        <div class="container-fluid app-shell">
            <div class="app-topbar">
                <div class="brand"><div class="logo">B</div><div class="d-none d-md-block">Bodhivaas Admin</div></div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-secondary" data-toggle-sidebar><i class="bi bi-list"></i></button>
                    <button class="btn btn-sm btn-outline-secondary" data-toggle-theme><i class="bi bi-moon-fill"></i></button>
                    <div class="dropdown">
                        <a class="text-muted text-decoration-none dropdown-toggle" href="#" data-bs-toggle="dropdown"><?php echo $_SESSION['admin_username']; ?></a>
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
                        <a class="nav-link" href="manage_students.php"><i class="bi bi-people"></i> <span class="nav-label">Students</span></a>
                        <a class="nav-link" href="manage_courses.php"><i class="bi bi-book"></i> <span class="nav-label">Courses</span></a>
                        <a class="nav-link" href="enrollments.php"><i class="bi bi-clipboard-check"></i> <span class="nav-label">Enrollments</span></a>
                        <a class="nav-link" href="notifications.php"><i class="bi bi-bell"></i> <span class="nav-label">Notifications</span></a>
                    </nav>
                </aside>

                <main class="app-main">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h4 class="mb-0">Dashboard</h4>
                            <small class="muted">Overview of campus activities</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-download"></i> Export</button>
                        </div>
                    </div>

                    <div class="dash-cards mb-3">
                        <div class="dash-card">
                            <div>
                                <div class="meta">Total Students</div>
                                <div class="value"><?php echo $total_students; ?></div>
                            </div>
                            <div class="iconbox"><i class="bi bi-people-fill"></i></div>
                        </div>
                        <div class="dash-card">
                            <div>
                                <div class="meta">Total Courses</div>
                                <div class="value"><?php echo $total_courses; ?></div>
                            </div>
                            <div class="iconbox"><i class="bi bi-book-fill"></i></div>
                        </div>
                        <div class="dash-card">
                            <div>
                                <div class="meta">Active Enrollments</div>
                                <div class="value"><?php echo $total_enrollments; ?></div>
                            </div>
                            <div class="iconbox"><i class="bi bi-clipboard-check-fill"></i></div>
                        </div>
                        <div class="dash-card">
                            <div>
                                <div class="meta">New (30d)</div>
                                <div class="value"><?php $result = $conn->query("SELECT COUNT(*) AS count FROM students WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"); echo $result ? $result->fetch_assoc()['count'] : 0; ?></div>
                            </div>
                            <div class="iconbox"><i class="bi bi-calendar2-week-fill"></i></div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-lg-7">
                            <div class="card glass-card">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">Attendance Trend</h6>
                                    <small class="muted">Last 30 days</small>
                                </div>
                                <canvas id="attendanceChart" height="160"></canvas>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header d-flex justify-content-between">
                                    <h6 class="mb-0">Recent Students</h6>
                                    <a href="manage_students.php" class="btn btn-sm btn-primary">View All</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-modern mb-0">
                                        <thead>
                                            <tr><th>Name</th><th>Email</th><th>Joined</th></tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($recent_students)): foreach ($recent_students as $student): ?>
                                                <tr><td><?php echo htmlspecialchars($student['name']); ?></td><td><?php echo htmlspecialchars($student['email']); ?></td><td><?php echo date('d M Y', strtotime($student['created_at'])); ?></td></tr>
                                            <?php endforeach; else: ?>
                                                <tr><td colspan="3" class="text-center muted py-3">No students yet</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="card glass-card mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">Enrollment Summary</h6>
                                    <small class="muted">By course</small>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-modern mb-0">
                                        <thead><tr><th>Course</th><th>Enrolled</th></tr></thead>
                                        <tbody>
                                            <?php foreach ($recent_enrollments as $en): ?>
                                                <tr><td><?php echo htmlspecialchars($en['course_name']); ?></td><td><?php echo htmlspecialchars($en['student_name']); ?></td></tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <h6 class="mb-2">Quick Actions</h6>
                                    <div class="d-grid gap-2">
                                        <a href="manage_students.php" class="btn btn-outline-secondary">Manage Students</a>
                                        <a href="manage_courses.php" class="btn btn-outline-secondary">Manage Courses</a>
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
        <script>
            // sample chart data - replace with real analytics later
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            const labels = Array.from({length:14}, (_, i) => { const d = new globalThis.Date(); d.setDate(d.getDate() - (13 - i)); return d.toLocaleDateString(); });
            const data = Array.from({length:14}, () => Math.floor(Math.random() * 30) + 70);
            createLineChart(ctx, labels, data, {label:'Attendance %', options:{}});
        </script>
</body>
</html>