<?php
require_once '../config.php';

if (!is_admin_logged_in()) {
    redirect('../login.php?role=admin');
}





// ---- FETCH ENROLLMENTS ----
$enrollments_query = "SELECT * FROM enrollments ORDER BY enrollment_date DESC";
$enrollments_result = mysqli_query($conn, $enrollments_query);

$enrollments = [];
while ($row = mysqli_fetch_assoc($enrollments_result)) {
    $enrollments[] = $row;
}


// ---- FETCH STUDENTS ----
$students_query = "SELECT id, name, email FROM students";
$students_result = mysqli_query($conn, $students_query);

$students = [];
while ($row = mysqli_fetch_assoc($students_result)) {
    $students[] = $row;
}


// ---- FETCH COURSES ----
$courses_query = "SELECT id, course_code, course_name FROM courses";
$courses_result = mysqli_query($conn, $courses_query);

$courses = [];
while ($row = mysqli_fetch_assoc($courses_result)) {
    $courses[] = $row;
}

// 
$recent_enrollments = [];
$sql = "SELECT e.id, e.enrollment_date, s.name AS student_name, c.course_name, c.course_code, e.status
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
    <title>Enrollments</title>
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
                    <li class="nav-item"><a class="nav-link active" href="enrollments.php"><i class="bi bi-clipboard-check"></i> Enrollments</a></li>
                    <li class="nav-item"><a class="nav-link" href="notifications.php"><i class="bi bi-bell"></i> Notifications</a></li>
                </ul>
            </div>

            <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-clipboard-check me-2"></i>Enrollments</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-plus-circle me-1"></i>Add Enrollment
                    </button>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <?php show_alert($_SESSION['success'], 'success'); ?>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Enrollments</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recent_enrollments)): ?>
                                        <?php foreach ($recent_enrollments as $e): ?>
                                            <tr>
                                                <td> <?php echo substr($e['student_name'], 0,); ?></td>
                                                <td> <?php echo substr($e['course_name'], 0,); ?></td>
                                                <td><span class="badge bg-success"><?php echo ucfirst($e['status']); ?></span></td>
                                                <td><?php echo format_date($e['enrollment_date']); ?></td>
                                                <td>
                                                    <form action="../php/code.php" method="POST" class="d-inline" onsubmit="return confirm('Remove?')">
                                                        <input type="hidden" name="delete_enrollment" value="<?= htmlspecialchars($e['id']) ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No enrollments</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Enrollment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../php/code.php" method="POST">
                    <input type="hidden" name="add_enrollment" value="1">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Student</label>
                            <select class="form-select" name="student_id" required>
                                <option>Choose student...</option>
                                <?php if (!empty($students)): ?>
                                    <?php foreach ($students as $s): ?>
                                        <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Course</label>
                            <select class="form-select" name="course_id" required>
                                <option>Choose course...</option>
                                <?php if (!empty($courses)): ?>
                                    <?php foreach ($courses as $c): ?>
                                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['course_code'] . ' - ' . $c['course_name']); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Enroll</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>