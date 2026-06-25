<?php
require_once 'config.php';

if (is_admin_logged_in()) {
    redirect('admin/dashboard.php');
} elseif (is_student_logged_in()) {
    redirect('student/dashboard.php');
}
// Gather lightweight stats for the landing page (non-destructive reads)
$total_students = 0; $total_courses = 0; $total_enrollments = 0; $success_rate = 0;
// Use existing connection if available
if (isset($conn) && $conn instanceof mysqli) {
    $r = $conn->query("SELECT COUNT(*) AS c FROM students");
    $total_students = $r ? (int)$r->fetch_assoc()['c'] : 0;
    $r = $conn->query("SELECT COUNT(*) AS c FROM courses");
    $total_courses = $r ? (int)$r->fetch_assoc()['c'] : 0;
    $r = $conn->query("SELECT COUNT(*) AS c FROM enrollments WHERE status='active'");
    $total_enrollments = $r ? (int)$r->fetch_assoc()['c'] : 0;
    // success rate = % of students who have at least one enrollment
    $r = $conn->query("SELECT COUNT(DISTINCT student_id) AS c FROM enrollments");
    $students_with_enroll = $r ? (int)$r->fetch_assoc()['c'] : 0;
    $success_rate = $total_students > 0 ? round(($students_with_enroll / $total_students) * 100, 1) : 0;
    // course highlights
    $course_highlights = [];
    $r = $conn->query("SELECT id, course_name, course_code, description FROM courses ORDER BY created_at DESC LIMIT 6");
    if ($r) {
        while ($row = $r->fetch_assoc()) $course_highlights[] = $row;
    }
    // testimonials from students table (sample)
    $testimonials = [];
    $r = $conn->query("SELECT id, name, email FROM students ORDER BY created_at DESC LIMIT 4");
    if ($r) {
        while ($row = $r->fetch_assoc()) $testimonials[] = $row;
    }
} else {
    // fallback values if no connection
    $total_students = 0; $total_courses = 0; $total_enrollments = 0; $success_rate = 0;
    $course_highlights = [];
    $testimonials = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <!-- Core CSS & fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bodhivaas.css">
    <style>
        /* Landing-specific overrides */
        .hero {padding:80px 0;background:linear-gradient(180deg, rgba(108,92,231,0.06), rgba(142,123,255,0.02));}
        .hero .eyebrow{font-weight:600;color:var(--brand);}
        .stat {text-align:center;padding:18px;background:var(--card);border-radius:12px;box-shadow:var(--shadow)}
        .feature-card{transition:transform .22s, box-shadow .22s}
        .feature-card:hover{transform:translateY(-6px);box-shadow:0 12px 30px rgba(16,24,40,0.08)}
        .course-card{transition:transform .18s}
        .course-card:hover{transform:translateY(-6px)}
        .back-to-top{position:fixed;right:18px;bottom:20px;display:none;z-index:999}
        .loader{position:fixed;inset:0;background:linear-gradient(180deg,rgba(255,255,255,0.6),#fff);display:flex;align-items:center;justify-content:center;z-index:2000}
    </style>
</head>

<body>
        <div class="loader" id="pageLoader"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>

        <!-- NAVBAR -->
        <header class="py-3 bg-transparent sticky-top" style="backdrop-filter: blur(6px);">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <a href="index.php" class="brand text-decoration-none"><div class="logo">B</div><div class="d-none d-md-block ms-1">Bodhivaas</div></a>
                    </div>
                    <nav class="d-none d-md-flex align-items-center gap-3">
                        <a href="#features" class="text-muted small">Features</a>
                        <a href="#courses" class="text-muted small">Courses</a>
                        <a href="#pricing" class="text-muted small">Why choose us</a>
                        <a href="login.php?role=student" class="btn btn-sm btn-outline-primary">Student Login</a>
                        <a href="login.php?role=admin" class="btn btn-sm btn-primary">Admin</a>
                    </nav>
                    <button class="btn btn-sm d-md-none" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu"><i class="fa fa-bars"></i></button>
                </div>
            </div>
        </header>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                <a class="d-block mb-2" href="#features">Features</a>
                <a class="d-block mb-2" href="#courses">Courses</a>
                <a class="d-block mb-2" href="#pricing">Why choose us</a>
                <hr>
                <a class="btn btn-primary w-100 mb-2" href="login.php?role=student">Student Login</a>
                <a class="btn btn-outline-primary w-100" href="login.php?role=admin">Admin</a>
            </div>
        </div>

        <!-- HERO -->
        <section class="hero">
            <div class="container">
                <div class="row align-items-center gy-4">
                    <div class="col-lg-6" data-aos="fade-up">
                        <div class="eyebrow">Smart Education ERP</div>
                        <h1 class="mt-3">Bodhivaas — Smarter student management for modern institutions</h1>
                        <p class="muted mb-4">Manage admissions, enrollments, results, and student life — all from a single, elegant dashboard.</p>
                        <div class="d-flex gap-2">
                            <a href="login.php?role=student" class="btn btn-primary btn-lg">Get Started</a>
                            <a href="#features" class="btn btn-outline-secondary btn-lg">Explore Features</a>
                        </div>
                        <div class="mt-4 d-flex gap-3">
                            <div class="stat" style="min-width:120px">
                                <div class="muted small">Students</div>
                                <div class="h4 fw-bold" data-counter><?php echo $total_students; ?></div>
                            </div>
                            <div class="stat" style="min-width:120px">
                                <div class="muted small">Courses</div>
                                <div class="h4 fw-bold" data-counter><?php echo $total_courses; ?></div>
                            </div>
                            <div class="stat" style="min-width:120px">
                                <div class="muted small">Enrollments</div>
                                <div class="h4 fw-bold" data-counter><?php echo $total_enrollments; ?></div>
                            </div>
                            <div class="stat" style="min-width:120px">
                                <div class="muted small">Success Rate</div>
                                <div class="h4 fw-bold" data-counter><?php echo $success_rate; ?>%</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" data-aos="zoom-in">
                        <div class="card border-0 shadow-lg">
                            <div class="card-body">
                                <img src="https://images.unsplash.com/photo-1531746790731-6c087fecd65a?q=80&w=1200&auto=format&fit=crop&crop=top" alt="education" class="img-fluid rounded">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FEATURES -->
        <section id="features" class="py-5">
            <div class="container">
                <div class="text-center mb-4" data-aos="fade-up">
                    <h3>Powerful features built for administrators and students</h3>
                    <p class="muted">Everything you need to run a modern educational institution.</p>
                </div>
                <div class="row g-3">
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="50">
                        <div class="p-4 feature-card glass-card">
                            <div class="mb-2"><i class="fa fa-users fa-2x text-primary"></i></div>
                            <h5>Student Management</h5>
                            <p class="muted small">Centralized student profiles, secure authentication, and role based access.</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="p-4 feature-card glass-card">
                            <div class="mb-2"><i class="fa fa-book fa-2x text-success"></i></div>
                            <h5>Course & Enrollment</h5>
                            <p class="muted small">Create and manage courses, enroll students, and track progress.</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="150">
                        <div class="p-4 feature-card glass-card">
                            <div class="mb-2"><i class="fa fa-chart-line fa-2x text-warning"></i></div>
                            <h5>Analytics</h5>
                            <p class="muted small">Interactive dashboards and analytics to understand student performance.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ABOUT / TIMELINE -->
        <section class="py-5 bg-light">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6" data-aos="fade-right">
                        <h4>About Bodhivaas</h4>
                        <p class="muted">Bodhivaas is built to simplify academic workflows, improve communication, and provide insights for administrations and learners.</p>
                        <ul class="muted">
                            <li>Secure authentication & roles</li>
                            <li>Streamlined course management</li>
                            <li>Reports & analytics</li>
                        </ul>
                    </div>
                    <div class="col-md-6" data-aos="fade-left">
                        <div class="card glass-card border-0">
                            <div class="card-body">
                                <h6>Milestones</h6>
                                <div class="mt-3">
                                    <div class="d-flex align-items-start mb-2"><div class="me-3"><span class="badge bg-primary">2024</span></div><div><strong>Initial Launch</strong><div class="muted small">Core student and course management.</div></div></div>
                                    <div class="d-flex align-items-start mb-2"><div class="me-3"><span class="badge bg-success">2025</span></div><div><strong>Analytics</strong><div class="muted small">Introduced dashboards and reports for admins.</div></div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- WHY CHOOSE US -->
        <section id="pricing" class="py-5">
            <div class="container">
                <div class="text-center mb-4"><h4>Why choose Bodhivaas</h4><p class="muted">Designed for clarity, security, and scale.</p></div>
                <div class="row g-3">
                    <div class="col-md-4" data-aos="zoom-in"><div class="p-4 feature-card"><h5>Reliable</h5><p class="muted small">Built with production-ready patterns and security.</p></div></div>
                    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="50"><div class="p-4 feature-card"><h5>Fast</h5><p class="muted small">Optimized for fast load times and smooth workflows.</p></div></div>
                    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100"><div class="p-4 feature-card"><h5>Secure</h5><p class="muted small">Session management and hashed passwords by default.</p></div></div>
                </div>
            </div>
        </section>

        <!-- COURSES -->
        <section id="courses" class="py-5 bg-light">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Course Highlights</h4><div class="small muted">Explore top courses</div></div>
                <div class="row g-3">
                    <?php if (!empty($course_highlights)): foreach ($course_highlights as $c): ?>
                        <div class="col-md-4" data-aos="fade-up">
                            <div class="card course-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($c['course_name']); ?></h6>
                                            <div class="muted small"><?php echo htmlspecialchars($c['course_code']); ?></div>
                                        </div>
                                        <div><span class="badge bg-primary">New</span></div>
                                    </div>
                                    <p class="muted small mb-3"><?php echo htmlspecialchars(substr($c['description'],0,100)); ?></p>
                                    <div class="d-flex gap-2">
                                        <a href="login.php?role=student" class="btn btn-sm btn-outline-primary">View Course</a>
                                        <a href="enroll_course.php" class="btn btn-sm btn-primary">Enroll</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                        <div class="col-12"><div class="muted">No courses available</div></div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- TESTIMONIALS -->
        <section class="py-5">
            <div class="container">
                <div class="text-center mb-4"><h4>What students say</h4></div>
                <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php if (!empty($testimonials)): $i=0; foreach ($testimonials as $t): ?>
                            <div class="carousel-item <?php echo $i==0? 'active':''; ?>">
                                <div class="card p-4">
                                    <div class="d-flex gap-3 align-items-center mb-2">
                                        <div class="profile-avatar"><?php echo strtoupper(substr($t['name'],0,1)); ?></div>
                                        <div>
                                            <strong><?php echo htmlspecialchars($t['name']); ?></strong>
                                            <div class="muted small"><?php echo htmlspecialchars($t['email']); ?></div>
                                        </div>
                                    </div>
                                    <p class="muted">"Bodhivaas made managing my courses simple and intuitive. The dashboard is clean and fast."</p>
                                </div>
                            </div>
                        <?php $i++; endforeach; else: ?>
                            <div class="carousel-item active"><div class="card p-4"><p class="muted">No testimonials yet.</p></div></div>
                        <?php endif; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                    <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-5" style="background:linear-gradient(90deg,var(--brand),var(--brand-2));color:white;">
            <div class="container text-center">
                <h3>Ready to transform your institution?</h3>
                <p class="muted mb-3">Sign up and get started with Bodhivaas today.</p>
                <a href="register.php" class="btn btn-light btn-lg">Get Started</a>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="py-5 bg-white">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <h5>Bodhivaas</h5>
                        <p class="muted small">Premium Student Management ERP.</p>
                    </div>
                    <div class="col-md-2 mb-3"><h6>Product</h6><ul class="list-unstyled muted small"><li><a href="#features">Features</a></li><li><a href="#courses">Courses</a></li></ul></div>
                    <div class="col-md-3 mb-3"><h6>Company</h6><ul class="list-unstyled muted small"><li><a href="#pricing">Why choose us</a></li></ul></div>
                    <div class="col-md-3 mb-3"><h6>Contact</h6><div class="muted small">hello@bodhivaas.example<br>+91 99999 99999</div><div class="mt-2"><a href="#" class="me-2"><i class="fab fa-twitter"></i></a><a href="#" class="me-2"><i class="fab fa-facebook"></i></a><a href="#"><i class="fab fa-linkedin"></i></a></div></div>
                </div>
                <div class="text-center muted small mt-4">&copy; <?php echo date('Y'); ?> Bodhivaas. All rights reserved.</div>
            </div>
        </footer>

        <button class="btn btn-primary back-to-top" id="backToTop"><i class="fa fa-chevron-up"></i></button>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
        <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
        <script src="assets/js/bodhivaas.js"></script>
        <script>
            AOS.init({duration:700, once:true});
            // page loader
            window.addEventListener('load', ()=>{ document.getElementById('pageLoader').style.display='none'; });
            // back to top
            const btt = document.getElementById('backToTop');
            window.addEventListener('scroll', ()=>{ if(window.scrollY>300) btt.style.display='block'; else btt.style.display='none'; });
            btt.addEventListener('click', ()=>{ window.scrollTo({top:0,behavior:'smooth'}); });

            // animated counters
            document.querySelectorAll('[data-counter]').forEach(el=>{
                const val = parseFloat(el.textContent)||0; el.textContent='0';
                let start=0; const dur=1000; const step = Math.ceil(val/ (dur/16));
                const iv = setInterval(()=>{ start+=step; if(start>=val){ el.textContent = val; clearInterval(iv);} else el.textContent = start; },16);
            });

            // enable smooth scrolling for internal links
            document.querySelectorAll('a[href^="#"]').forEach(a=>{ a.addEventListener('click', (e)=>{ e.preventDefault(); const t=document.querySelector(a.getAttribute('href')); if(t) t.scrollIntoView({behavior:'smooth', block:'start'}); }); });
        </script>
</body>

</html>