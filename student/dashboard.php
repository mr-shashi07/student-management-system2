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
            border-left-color: #28a745;
            color: #28a745;
        }

        .main-content {
            padding: 25px;
        }

        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-mortarboard-fill me-2"></i>SMS Student
            </a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($_SESSION['student_name']); ?>
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
                        <a class="nav-link" href="my_courses.php">
                            <i class="bi bi-book"></i> My Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="enroll_course.php">
                            <i class="bi bi-plus-circle"></i> Enroll Course
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <i class="bi bi-person"></i> Profile
                        </a>
                    </li>
                </ul>
            </div>

            <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
                <div class="mb-4">
                    <h2><i class="bi bi-speedometer2 me-2"></i>Welcome, <?php echo htmlspecialchars($_SESSION['student_name']); ?></h2>
                    <p class="text-muted">Here's your academic overview</p>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="bi bi-book-fill text-success" style="font-size: 2.5rem;"></i>
                                <h6 class="text-muted mt-2">Active Courses</h6>
                                <h2 class="mb-0"><?php echo count($enrollments); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="bi bi-bell-fill text-info" style="font-size: 2.5rem;"></i>
                                <h6 class="text-muted mt-2">Announcements</h6>
                                <h2 class="mb-0"><?php echo count($notifications); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="bi bi-person-fill text-warning" style="font-size: 2.5rem;"></i>
                                <h6 class="text-muted mt-2">Member Since</h6>
                                <h5 class="mb-0"><?php echo $student ? format_date($student['created_at']) : '-'; ?></h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h5 class="mb-0"><i class="bi bi-book me-2"></i>Your Courses</h5>
                                <a href="enroll_course.php" class="btn btn-sm btn-success">Enroll More</a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Course Code</th>
                                                <th>Course Name</th>
                                                <th>Status</th>
                                                <th>Enrolled Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($enrollments) > 0): ?>
                                                <?php foreach ($enrollments as $e): ?>
                                                    <tr>
                                                        <td>
                                                            <span class="badge bg-primary">
                                                                <?php echo 'COURSE ' . substr($e['course_id'], 0, 6); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo isset($course_names[$e['course_id']]) ? htmlspecialchars($course_names[$e['course_id']]) : 'Course Name'; ?></td>
                                                        <td><span class="badge bg-success">Active</span></td>
                                                        <td><?php echo format_date($e['enrollment_date']); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-4">No enrolled courses</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-bell me-2"></i>Announcements</h5>
                            </div>
                            <div class="card-body p-0">
                                <?php if (count($notifications) > 0): ?>
                                    <?php foreach ($notifications as $notif): ?>
                                        <div class="p-3 border-bottom">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($notif['title']); ?></h6>
                                            <p class="text-muted small mb-0"><?php echo htmlspecialchars(substr($notif['message'], 0, 60)); ?>...</p>
                                            <small class="text-muted"><?php echo format_date($notif['created_at']); ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="p-3 text-center text-muted">
                                        <p class="mb-0">No announcements</p>
                                    </div>
                                <?php endif; ?>
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