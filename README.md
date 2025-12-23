# Student Management System

A fully functional Student Management System built with PHP and Supabase. Features complete admin and student panels with authentication, course management, and enrollment tracking.

## Features

### Admin Panel
- Admin login/logout with secure authentication
- Dashboard with statistics (total students, courses, active enrollments)
- Student Management:
  - Add, edit, delete students
  - Search students by name or email
  - View student details
- Course Management:
  - Create, edit, delete courses
  - Track course enrollments
- Enrollment Management:
  - Assign students to courses
  - Update enrollment status (active, completed, dropped)
  - View all enrollments with pagination
- Notification System:
  - Post announcements for students
  - Delete notifications
- Admin Profile:
  - View profile information
  - Change password

### Student Panel
- Student registration and login
- Personalized dashboard with course overview
- Course Management:
  - View enrolled courses
  - Browse all available courses
  - Enroll in new courses
  - View course details
- Profile Management:
  - Update profile information
  - Change password
- View Announcements:
  - See latest admin notifications on dashboard

### Security Features
- Password hashing with bcrypt
- Session-based authentication
- Input sanitization to prevent XSS attacks
- Prepared statements for database queries
- Row-level security on Supabase

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: Supabase (PostgreSQL)
- **Frontend**: Bootstrap 5, HTML5, JavaScript
- **Icons**: Bootstrap Icons
- **Authentication**: PHP Sessions

## Installation & Setup

### Prerequisites
- PHP 7.4 or higher with curl extension
- Supabase account (free at supabase.com)
- Web server (Apache, Nginx, or PHP built-in server)

### Step 1: Clone or Download
```bash
git clone <repository-url>
cd student-management-system
```

### Step 2: Configure Supabase Credentials

1. Go to [Supabase Dashboard](https://app.supabase.com)
2. Create a new project
3. Copy your `Project URL` and `Anon Key`
4. Update `.env` file:
```
VITE_SUPABASE_URL=your_project_url_here
VITE_SUPABASE_ANON_KEY=your_anon_key_here
```

### Step 3: Initialize Database

The database tables are automatically created. If needed, manually run the SQL migrations from Supabase dashboard:

1. Go to SQL Editor in Supabase
2. Copy and paste the SQL from migration files
3. Execute the queries

### Step 4: Start the Application

**Using PHP built-in server:**
```bash
php -S localhost:8000
```

Then visit: `http://localhost:8000`

**Using Apache/Nginx:**
- Configure your web server to point to the project directory
- Visit: `http://localhost/student-management-system`

## Default Credentials

### Admin Account
- **Username**: admin
- **Password**: admin123

### Sample Student Account
- **Email**: rahul.sharma@student.com
- **Password**: student123

(Additional sample students available in database with same password)

## Project Structure

```
student-management-system/
├── index.php                 # Home page
├── login.php                 # Login page
├── register.php              # Student registration
├── logout.php                # Logout handler
├── config.php                # Configuration and helpers
├── .env                      # Environment variables
├── admin/
│   ├── dashboard.php         # Admin dashboard
│   ├── manage_students.php   # Student CRUD
│   ├── manage_courses.php    # Course CRUD
│   ├── enrollments.php       # Enrollment management
│   ├── notifications.php     # Announcements
│   └── profile.php           # Admin profile
├── student/
│   ├── dashboard.php         # Student dashboard
│   ├── my_courses.php        # View enrolled courses
│   ├── enroll_course.php     # Browse and enroll
│   └── profile.php           # Student profile
└── database.sql              # Database schema (reference)
```

## Database Schema

### Tables

**admins**
- id (UUID, Primary Key)
- username (VARCHAR 100, UNIQUE)
- email (VARCHAR 150)
- password_hash (VARCHAR 255)
- created_at (TIMESTAMP)

**students**
- id (UUID, Primary Key)
- name (VARCHAR 150)
- email (VARCHAR 150, UNIQUE)
- phone (VARCHAR 20)
- address (TEXT)
- password_hash (VARCHAR 255)
- created_at (TIMESTAMP)

**courses**
- id (UUID, Primary Key)
- course_name (VARCHAR 150)
- course_code (VARCHAR 50, UNIQUE)
- description (TEXT)
- duration (VARCHAR 50)
- created_at (TIMESTAMP)

**enrollments**
- id (UUID, Primary Key)
- student_id (UUID, Foreign Key)
- course_id (UUID, Foreign Key)
- status (VARCHAR 20: active, completed, dropped)
- enrollment_date (TIMESTAMP)
- UNIQUE(student_id, course_id)

**notifications**
- id (UUID, Primary Key)
- title (VARCHAR 200)
- message (TEXT)
- created_by (UUID, Foreign Key to admins)
- created_at (TIMESTAMP)

## Usage

### For Admin

1. **Login**: Visit `/login.php?role=admin` and enter admin credentials
2. **Dashboard**: View statistics and quick overview
3. **Manage Students**: Add/edit/delete students, search functionality
4. **Manage Courses**: Create and manage available courses
5. **Enrollments**: Assign students to courses, update status
6. **Announcements**: Post notifications visible to all students
7. **Profile**: Update email and change password

### For Students

1. **Register**: Visit `/register.php` and create an account
2. **Login**: Enter your email and password
3. **Dashboard**: View overview of enrolled courses and announcements
4. **Browse Courses**: Go to "Enroll Course" to view all available courses
5. **Enroll**: Click "Enroll Now" to enroll in a course
6. **My Courses**: View all your active enrollments
7. **Profile**: Update personal information and change password

## Key Features Explained

### Authentication
- Passwords are securely hashed using PHP's `password_hash()` function
- Session-based authentication keeps users logged in
- Automatic redirection for unauthorized access

### Search & Filter
- Students can be searched by name, email, or phone
- Results filtered with pagination (10 records per page)

### Enrollment Status
- **Active**: Student is currently enrolled
- **Completed**: Course is finished
- **Dropped**: Student withdrew from course

### Notifications
- Admins can post announcements
- All students see notifications on their dashboard
- Latest 3 notifications displayed

## Troubleshooting

### Connection Issues
- Verify Supabase URL and API key in `.env`
- Check internet connection
- Ensure curl is enabled in PHP

### Login Issues
- Clear browser cookies/cache
- Verify credentials match database
- Check if user exists in database

### Database Errors
- Ensure all tables are created
- Run migrations if needed
- Check Supabase project status

## Security Considerations

- Never commit `.env` file with real credentials
- Use strong passwords for admin account
- Regularly update password policy
- Monitor admin activities
- Keep PHP and dependencies updated

## License

This project is open source and available for educational purposes.

## Support

For issues or questions, please check the documentation or contact support.

---

**Happy Learning!** 🎓
