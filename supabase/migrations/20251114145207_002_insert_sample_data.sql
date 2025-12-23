-- Insert admin account (password: admin123)
INSERT IGNORE INTO
    admins (
        id,
        username,
        email,
        password_hash
    )
VALUES (
        UUID(),
        'admin',
        'admin@sms.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
    );

-- Insert sample courses
INSERT IGNORE INTO
    courses (
        id,
        course_name,
        course_code,
        description,
        duration
    )
VALUES (
        UUID(),
        'Computer Science',
        'CS101',
        'Comprehensive computer science program covering programming, algorithms, and data structures',
        '4 Years'
    ),
    (
        UUID(),
        'Business Administration',
        'BA201',
        'Business management, marketing, finance, and entrepreneurship fundamentals',
        '3 Years'
    ),
    (
        UUID(),
        'Civil Engineering',
        'CE301',
        'Structural design, construction management, and infrastructure development',
        '4 Years'
    ),
    (
        UUID(),
        'Electronics Engineering',
        'EE401',
        'Circuit design, embedded systems, and telecommunications',
        '4 Years'
    ),
    (
        UUID(),
        'Mechanical Engineering',
        'ME501',
        'Thermodynamics, manufacturing processes, and machine design',
        '4 Years'
    ),
    (
        UUID(),
        'Information Technology',
        'IT601',
        'Software development, networking, and database management',
        '3 Years'
    ),
    (
        UUID(),
        'English Literature',
        'EL701',
        'Literary analysis, creative writing, and linguistic studies',
        '3 Years'
    ),
    (
        UUID(),
        'Mathematics',
        'MA801',
        'Pure and applied mathematics including calculus, algebra, and statistics',
        '3 Years'
    );

-- Insert sample students (password: student123)
INSERT IGNORE INTO
    students (
        id,
        name,
        email,
        phone,
        address,
        password_hash
    )
VALUES (
        UUID(),
        'Rahul Sharma',
        'rahul.sharma@student.com',
        '9876543210',
        '123 MG Road, Delhi',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
    ),
    (
        UUID(),
        'Priya Patel',
        'priya.patel@student.com',
        '9876543211',
        '456 SG Highway, Ahmedabad',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
    ),
    (
        UUID(),
        'Amit Kumar',
        'amit.kumar@student.com',
        '9876543212',
        '789 Park Street, Kolkata',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
    ),
    (
        UUID(),
        'Sneha Desai',
        'sneha.desai@student.com',
        '9876543213',
        '321 FC Road, Pune',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
    ),
    (
        UUID(),
        'Vikram Singh',
        'vikram.singh@student.com',
        '9876543214',
        '654 Brigade Road, Bangalore',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
    ),
    (
        UUID(),
        'Neha Gupta',
        'neha.gupta@student.com',
        '9876543215',
        '987 Anna Salai, Chennai',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
    ),
    (
        UUID(),
        'Arjun Reddy',
        'arjun.reddy@student.com',
        '9876543216',
        '147 Banjara Hills, Hyderabad',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
    ),
    (
        UUID(),
        'Divya Nair',
        'divya.nair@student.com',
        '9876543217',
        '258 MG Road, Kochi',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
    ),
    (
        UUID(),
        'Karan Malhotra',
        'karan.malhotra@student.com',
        '9876543218',
        '369 Civil Lines, Jaipur',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
    ),
    (
        UUID(),
        'Anjali Verma',
        'anjali.verma@student.com',
        '9876543219',
        '753 Hazratganj, Lucknow',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
    );

-- Insert sample notifications
-- Get admin id for notifications
SET
    @admin_id = (
        SELECT id
        FROM admins
        WHERE
            username = 'admin'
        LIMIT 1
    );

INSERT IGNORE INTO
    notifications (
        id,
        title,
        message,
        created_by
    )
VALUES (
        UUID(),
        'Welcome to New Academic Year',
        'We welcome all students to the new academic year 2024-25. Please check your course schedules and complete the enrollment process.',
        @admin_id
    ),
    (
        UUID(),
        'Library Timing Update',
        'The library will now remain open until 10 PM on weekdays. Students can utilize extended hours for their studies.',
        @admin_id
    ),
    (
        UUID(),
        'Examination Schedule Released',
        'The semester examination schedule has been released. Please check the notice board and prepare accordingly.',
        @admin_id
    );