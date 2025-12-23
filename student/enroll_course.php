<?php
require_once '../config.php';

if (!is_student_logged_in()) {
    header('Location: ../login.php?role=student');
    exit;
}

$student_id = $_SESSION['student_id'];

// DB connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Fetch courses
$courses = [];
$courses_sql = "SELECT * FROM courses ORDER BY course_name";
$result = $conn->query($courses_sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

// Fetch enrolled courses
$enrolled_ids = [];
$my_courses_sql = "SELECT course_id FROM enrollments WHERE student_id = ?";
$stmt = $conn->prepare($my_courses_sql);
$stmt->bind_param("s", $student_id); // UUID is string
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $enrolled_ids[] = $row['course_id'];
}
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll in Courses</title>
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

        .course-card {
            transition: all 0.3s;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
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
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_courses.php">
                            <i class="bi bi-book"></i> My Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="enroll_course.php">
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
                <h2 class="mb-4"><i class="bi bi-plus-circle me-2"></i>Available Courses</h2>

                <?php if (isset($_SESSION['success'])): show_alert($_SESSION['success'], 'success');
                    unset($_SESSION['success']);
                endif; ?>
                <?php if (isset($_SESSION['error'])): show_alert($_SESSION['error'], 'danger');
                    unset($_SESSION['error']);
                endif; ?>

                <div class="row">
                    <?php if (count($courses) > 0): ?>
                        <?php foreach ($courses as $course): ?>
                            <?php $is_enrolled = in_array($course['id'], $enrolled_ids); ?>
                            <div class="col-md-6 mb-4">
                                <div class="card course-card h-100">
                                    <div class="card-body">
                                        <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($course['course_code']); ?></span>

                                        <h5 class="card-title"><?php echo htmlspecialchars($course['course_name']); ?></h5>
                                        <p class="card-text text-muted"><?php echo htmlspecialchars(substr($course['description'] ?? '', 0, 100)); ?>...</p>
                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <small class="text-muted">Duration:</small>
                                                <p class="mb-0"><strong><?php echo htmlspecialchars($course['duration'] ?? 'N/A'); ?></strong></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white">
                                        <?php if ($is_enrolled): ?>
                                            <span class="badge bg-success w-100 py-2">
                                                <i class="bi bi-check-circle me-1"></i>Already Enrolled
                                            </span>
                                        <?php else: ?>
                                            <form action="../php/code.php" method="POST" class="d-inline">
                                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                                                <button type="submit" name="enroll" class="btn btn-success w-100">
                                                    <i class="bi bi-plus-circle me-1"></i>Enroll Now
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="card text-center py-5">
                                <p class="text-muted">No courses available</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>