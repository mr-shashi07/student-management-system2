<?php
/**
 * Configuration File
 * Contains Supabase connection settings and helper functions
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load environment variables
$env_file = __DIR__ . '/.env';
if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, 'VITE_') === 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = str_replace('VITE_', '', $key);
            $_ENV[$key] = $value;
        }
    }
}

// Supabase configuration
define('SUPABASE_URL', $_ENV['SUPABASE_URL'] ?? '');
define('SUPABASE_ANON_KEY', $_ENV['SUPABASE_ANON_KEY'] ?? '');

// Application constants
define('SITE_URL', 'http://localhost/student_management');
define('RECORDS_PER_PAGE', 10);

// Helper function to make HTTP requests to Supabase
function supabase_request($method, $endpoint, $data = null, $token = null) {
    $url = SUPABASE_URL . '/rest/v1/' . $endpoint;

    $headers = [
        'Content-Type: application/json',
        'apikey: ' . SUPABASE_ANON_KEY,
    ];

    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    if ($data && in_array($method, ['POST', 'PATCH', 'PUT'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'status' => $http_code,
        'data' => json_decode($response, true),
        'raw' => $response
    ];
}

/**
 * Sanitize user input to prevent XSS attacks
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Check if admin is logged in
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Check if student is logged in
 */
function is_student_logged_in() {
    return isset($_SESSION['student_id']) && !empty($_SESSION['student_id']);
}

/**
 * Redirect to specified page
 */
function redirect($page) {
    header("Location: " . $page);
    exit();
}

/**
 * Display alert message using Bootstrap classes
 */
function show_alert($message, $type = 'info') {
    echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">';
    echo $message;
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    echo '</div>';
}

/**
 * Format date for display
 */
function format_date($date) {
    return date('d M Y', strtotime($date));
}

/**
 * Format datetime for display
 */
function format_datetime($datetime) {
    return date('d M Y h:i A', strtotime($datetime));
}
?>
