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
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #f8f9fa;
            border-left-color: #0d6efd;
            color: #0d6efd;
        }

        .main-content {
            padding: 25px;
        }

        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            margin-bottom: 20px;
        }

        .stats-card {
            border-left: 4px solid #0d6efd;
        }

        .table {
            margin-bottom: 0;
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
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i><?php echo $_SESSION['admin_username']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
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
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_students.php">
                            <i class="bi bi-people"></i> Manage Students
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_courses.php">
                            <i class="bi bi-book"></i> Manage Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="enrollments.php">
                            <i class="bi bi-clipboard-check"></i> Enrollments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="notifications.php">
                            <i class="bi bi-bell"></i> Notifications
                        </a>
                    </li>
                </ul>
            </div>

            <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
                <h2 class="mb-4"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h2>

                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card">
                            <div class="card-body">
                                <h6 class="text-muted">Total Students</h6>
                                <h2><?php echo $total_students; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card" style="border-left-color: #28a745;">
                            <div class="card-body">
                                <h6 class="text-muted">Total Courses</h6>
                                <h2><?php echo $total_courses; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card" style="border-left-color: #ffc107;">
                            <div class="card-body">
                                <h6 class="text-muted">Active Enrollments</h6>
                                <h2><?php echo $total_enrollments; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card" style="border-left-color: #dc3545;">
                            <div class="card-body">
                                <h6 class="text-muted">New (30d)</h6>
                                <h2> <?php
                                        $result = $conn->query("SELECT COUNT(*) AS count FROM students WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
                                        echo $result ? $result->fetch_assoc()['count'] : 0;
                                        ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h5 class="mb-0"><i class="bi bi-people me-2"></i>Recent Students</h5>
                                <a href="manage_students.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Joined</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($recent_students)): ?>
                                                <?php foreach ($recent_students as $student): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                                                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                                                        <td><?php echo date('d M Y', strtotime($student['created_at'])); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-3">No students yet</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Recent Enrollments</h5>
                                <a href="enrollments.php" class="btn btn-sm btn-success">View All</a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Course</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($recent_enrollments)): ?>

                                                <?php foreach ($recent_enrollments as $enrollment): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($enrollment['student_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($enrollment['course_name']); ?></td>
                                                        <td><?php echo date('d M Y', strtotime($enrollment['enrollment_date'])); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-3">No enrollments yet</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
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