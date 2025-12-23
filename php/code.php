<?php
require_once '../config.php';

// UUID function
if (!function_exists('generate_uuid_v4')) {
    function generate_uuid_v4()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}

// --- REGISTRATION CODE FIRST ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    // $error = '';

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        $_SESSION['error'] = 'Database connection failed: ' . $conn->connect_error;
    } elseif (empty($name) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'Please fill all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email format';
    } elseif (strlen($password) < 6) {
        $_SESSION['error'] = 'Password must be at least 6 characters';
    } elseif ($password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM students WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $_SESSION['error'] = 'Email already exists.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $uuid = generate_uuid_v4();

            $stmt = $conn->prepare("INSERT INTO students (id, name, email, phone, address, password_hash) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $uuid, $name, $email, $phone, $address, $hashed_password);
            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header('Location: ../login.php?role=student&registered=1');
                exit;
            } else {
                $_SESSION['error'] = 'Registration failed. Please try again.';
            }
        }
        $stmt->close();
    }
    $conn->close();

    // Redirect back to register page with error
    if (isset($_SESSION['error'])) {
        header('Location: ../register.php?error=' . urlencode($_SESSION['error']));
        exit;
    }
}

// --- LOGIN CODE SECOND ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['role'])) {
    $role = isset($_POST['role']) ? sanitize_input($_POST['role']) : 'student';
    if (!in_array($role, ['admin', 'student'])) {
        $role = 'student';
    }

    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    // $error = '';

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Please enter both email and password';
    } else {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($role == 'admin') {
            $query = "SELECT id, username, email, password_hash FROM admins WHERE username = ? LIMIT 1";
        } else {
            $query = "SELECT id, name, email, password_hash FROM students WHERE email = ? LIMIT 1";
        }
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password_hash'])) {
                if ($role == 'admin') {
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    header("Location: ../admin/dashboard.php");
                    exit();
                } else {
                    $_SESSION['student_id'] = $user['id'];
                    $_SESSION['student_name'] = $user['name'];
                    $_SESSION['student_email'] = $user['email'];
                    header("Location: ../student/dashboard.php");
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Invalid password';
            }
        } else {
            $_SESSION['error'] = 'User not found';
        }
        $stmt->close();
        $conn->close();
    }
    // Redirect back to login with error
    header("Location: ../login.php?role=$role&error=" . urlencode($_SESSION['error']));
    exit();
}





// Student profile update
if (isset($_POST['update']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start();
    require_once '../config.php';
    $student_id = $_SESSION['student_id'];

    $name = sanitize_input($_POST['name']);
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $stmt = $conn->prepare("UPDATE students SET name=?, phone=?, address=? WHERE id=?");
    $stmt->bind_param("ssss", $name, $phone, $address, $student_id);
    if ($stmt->execute()) {
        $_SESSION['student_name'] = $name;
        $_SESSION['success'] = 'Profile updated';
    } else {
        $_SESSION['error'] = 'Error updating profile';
    }
    $stmt->close();
    $conn->close();
    header("Location: ../student/profile.php");
    exit();
}

// Student password change
if (isset($_POST['change_pass']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start();
    require_once '../config.php';
    $student_id = $_SESSION['student_id'];

    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    // Fetch current password hash
    $stmt = $conn->prepare("SELECT password_hash FROM students WHERE id=?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();

    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            $_SESSION['error'] = 'Password must be 6+ characters';
        } elseif ($new_password !== $confirm_password) {
            $_SESSION['error'] = 'Passwords do not match';
        } elseif (!password_verify($current_password, $student['password_hash'])) {
            $_SESSION['error'] = 'Current password incorrect';
        } else {
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE students SET password_hash=? WHERE id=?");
            $stmt->bind_param("ss", $password_hash, $student_id);
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Password updated';
            } else {
                $_SESSION['error'] = 'Error updating password';
            }
            $stmt->close();
        }
    }
    $conn->close();
    header("Location: ../student/profile.php");
    exit();
}





// enroll course
if (isset($_POST['enroll']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../config.php';
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $course_id = $_POST['course_id'] ?? '';
    $student_id = $_POST['student_id'] ?? '';

    if (empty($course_id) || empty($student_id)) {
        $_SESSION['error'] = "Invalid request.";
        header('Location: ../student/enroll_course.php');
        exit;
    }

    // Check if already enrolled
    $check_sql = "SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ss", $student_id, $course_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Already enrolled in this course.";
    } else {
        $id = generate_uuid_v4();
        $enroll_sql = "INSERT INTO enrollments (id, student_id, course_id, status) VALUES (?, ?, ?, 'active')";
        $enroll_stmt = $conn->prepare($enroll_sql);
        $enroll_stmt->bind_param("sss", $id, $student_id, $course_id);
        if ($enroll_stmt->execute()) {
            $_SESSION['success'] = "Course enrolled successfully!";
        } else {
            $_SESSION['error'] = "Failed to enroll: " . $conn->error;
        }
        $enroll_stmt->close();
    }
    $stmt->close();
    $conn->close();
    header('Location: ../student/enroll_course.php');
    exit;
}






// ADmin ADmin
// manage student

// Handle delete
if (isset($_POST['delete'])) {

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $student_id = $_POST['delete'];
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("s", $student_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Student deleted successfully';
    } else {
        $_SESSION['error'] = 'Failed to delete student';
    }
    $stmt->close();
    $conn->close();
    header('Location: ../admin/manage_students.php');
    exit;
}


// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_student_crud'])) {

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);

    if (empty($name) || empty($email)) {
        $_SESSION['error'] = 'Name and email required';
    } else {
        $student_id = isset($_POST['student_id']) ? $_POST['student_id'] : '';

        if ($student_id) {
            $stmt = $conn->prepare("UPDATE students SET name=?, email=?, phone=?, address=? WHERE id=?");
            $stmt->bind_param("sssss", $name, $email, $phone, $address, $student_id);
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Student updated';
            } else {
                $_SESSION['error'] = 'Failed to update student';
            }
            $stmt->close();
        } else {
            $id = generate_uuid_v4();
            $password_hash = password_hash('student123', PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO students (id, name, email, phone, address, password_hash, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssssss", $id, $name, $email, $phone, $address, $password_hash);
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Student added with password: student123';
            } else {
                $_SESSION['error'] = 'Failed to add student';
            }
            $stmt->close();
        }
    }
    $conn->close();
    header('Location: ../admin/manage_students.php');
    exit;
}





// manage course
// Delete course
if (isset($_POST['delete_course'])) {
    $course_id = $_POST['delete_course'];
    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param("s", $course_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Course deleted';
    } else {
        $_SESSION['error'] = 'Failed to delete';
    }
    $stmt->close();
    $conn->close();
    header('Location: ../admin/manage_courses.php');
    exit;
}

// Add or update course
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['course_crud'])) {
    $course_name = sanitize_input($_POST['course_name']);
    $course_code = sanitize_input($_POST['course_code']);
    $description = sanitize_input($_POST['description']);
    $duration = sanitize_input($_POST['duration']);
    $course_id = isset($_POST['course_id']) ? $_POST['course_id'] : '';

    if (!$course_name || !$course_code) {
        $_SESSION['error'] = 'Course name and code required';
    } else {
        if ($course_id) {
            $stmt = $conn->prepare("UPDATE courses SET course_name=?, course_code=?, description=?, duration=? WHERE id=?");
            $stmt->bind_param("sssss", $course_name, $course_code, $description, $duration, $course_id);
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Course updated';
            } else {
                $_SESSION['error'] = 'Failed to update course';
            }
            $stmt->close();
        } else {
            $course_id = generate_uuid_v4();
            $stmt = $conn->prepare("INSERT INTO courses (id, course_name, course_code, description, duration, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssss", $course_id, $course_name, $course_code, $description, $duration);
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Course added';
            } else {
                $_SESSION['error'] = 'Failed to add course';
            }
            $stmt->close();
        }
    }
    $conn->close();
    header('Location: ../admin/manage_courses.php');
    exit;
}




// manage enrollments 
// ---- DELETE ENROLLMENT ----
if (isset($_POST['delete_enrollment'])) {
    $enrollment_id = $_POST['delete_enrollment'];
    $stmt = $conn->prepare("DELETE FROM enrollments WHERE id = ?");
    $stmt->bind_param("s", $enrollment_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Enrollment deleted';
    } else {
        $_SESSION['error'] = 'Failed to delete enrollment';
    }
    $stmt->close();
    $conn->close();
    header('Location: ../admin/enrollments.php');
    exit;
}


// Add enrollment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_enrollment'])) {
    $student_id = $_POST['student_id'] ?? '';
    $course_id = $_POST['course_id'] ?? '';
    if (!$student_id || !$course_id) {
        $_SESSION['error'] = 'Student and course required';
    } else {
        // Check if already enrolled
        $stmt = $conn->prepare("SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?");
        $stmt->bind_param("ss", $student_id, $course_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $_SESSION['error'] = 'Already enrolled';
        } else {
            $id = generate_uuid_v4();
            $stmt = $conn->prepare("INSERT INTO enrollments (id, student_id, course_id, status, enrollment_date) VALUES (?, ?, ?, 'active', NOW())");
            $stmt->bind_param("sss", $id, $student_id, $course_id);
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Enrollment added';
            } else {
                $_SESSION['error'] = 'Failed to add enrollment';
            }
        }
        $stmt->close();
    }
    $conn->close();
    header('Location: ../admin/enrollments.php');
    exit;
}




// manage notification
if (isset($_POST['delete_notification'])) {
    $id = $_POST['delete_notification'];
    $stmt = $conn->prepare("DELETE FROM notifications WHERE id = ?");
    $stmt->bind_param("s", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Notification deleted';
    } else {
        $_SESSION['error'] = 'Failed to delete notification';
    }
    $stmt->close();
    $conn->close();
    header('Location: ../admin/notifications.php');
    exit;
}

// Post notification
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_notification'])) {
    $title = sanitize_input($_POST['title']);
    $message = sanitize_input($_POST['message']);
    $admin_id = $_SESSION['admin_id'];
    $id = generate_uuid_v4();

    $stmt = $conn->prepare("INSERT INTO notifications (id, title, message, admin_id, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssi", $id, $title, $message, $admin_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Notification posted';
    } else {
        $_SESSION['error'] = 'Failed to post notification';
    }
    $stmt->close();
    $conn->close();
    header('Location: ../admin/notifications.php');
    exit;
}
