<?php
session_start();

// Redirect logged‑in users to dashboard
if (isset($_SESSION['id']) && isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: admin/dashboard.php");
            break;
        case 'teacher':
            header("Location: teachers/dashboard.php");
            break;
        case 'student':
            header("Location: student/dashboard.php");
            break;
        default:
            session_unset();
            session_destroy();
            header("Location: authentication/login.php");
    }
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Everest College | Home</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
/* RESET */
* { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
body { overflow-x: hidden; }

/* TOP BAR */
.top-bar {
    background:#0d1b40;
    color:#fff;
    padding:8px 20px;
    display:flex;
    justify-content:flex-end;
    align-items:center;
    gap:20px;
    font-size:14px;
}
.top-bar a {
    text-decoration: none;
    font-weight: bold;
}
.top-bar a.apply-btn {
    background:#ff4f3c;
    color:#fff;
    padding:5px 12px;
    border-radius:5px;
}

/* MAIN NAV */
nav {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:15px 20px;
    background:#fff;
    box-shadow:0 2px 5px rgba(0,0,0,0.1);
}
nav img.logo {
    height:90px; /* bigger logo */
}
nav .menu a {
    color:#0d1b40;
    margin-left:20px;
    font-weight:bold;
    text-decoration:none;
}

/* HERO SECTION */
.hero-container {
    position: relative;
    text-align: center;
}
.hero-text {
    position: absolute;
    top:20%;
    width: 100%;
    color:blue;
}
.hero-text h1 {
    font-size:50px;
    text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
    
}
.hero-text p {
    font-size:22px;
    margin-top:10px;
    text-shadow: 1px 1px 5px rgba(0,0,0,0.5);
}
.hero img.banner {
    width:100%;
    height:450px;
    object-fit:cover;
    
}

/* APPLY NOW BUTTON */
.hero .btn {
    background:#ff4f3c;
    padding:12px 28px;
    color:#fff;
    font-size:18px;
    border-radius:5px;
    text-decoration:none;
    margin-top:15px;
}

/* SECTIONS */
section { padding:60px 20px; text-align:center; }
h2 { color:#0d1b40; margin-bottom:15px; font-size:28px; }

/* COURSES */
.course-list {
    display:flex;
    flex-wrap:wrap;
    gap:20px;
    justify-content:center;
}
.course-card {
    border:1px solid #ddd;
    width:240px;
    border-radius:8px;
    overflow:hidden;
    padding:15px;
    transition:0.3s ease;
}
.course-card:hover { transform:translateY(-5px); box-shadow:0 8px 20px rgba(0,0,0,0.15); }
.course-card img { width:100%; }
.course-card h3 { margin:12px 0; color:#0d1b40; }

/* ABOUT */
.about p { font-size:18px; line-height:1.6; color:#333; }

/* NEWS / NOTICES */
.news-list {
    display:grid;
    gap:15px;
    max-width:900px;
    margin:auto;
}
.notice-card {
    border-radius:6px;
    background:#ffe0b2; /* light orange background */
    padding:15px;
}
.notice-card h4 { font-size:20px; color:#d84315; margin-bottom:6px; }
.notice-card p { font-size:16px; color:#333; }

/* CONTACT */
.contact header{
    background: lightgrey; /* light blue background */
    padding:20px;
    border-radius:8px;
    margin-bottom:15px;
}

.contact-info {
    background: lightgrey; /* light blue background */
    padding:20px;
    border-radius:8px;
    margin-bottom:15px;
}
.contact-info p { font-size:18px; margin:8px 0; color:#0d47a1; }
.map-container {
    border-radius:8px;
    overflow:hidden;
    margin-top:15px;
}

/* MAP */
.map-container iframe { width:100%; height:300px; border:none; }

/* FOOTER */
footer { background:#0d1b40; color:#fff; padding:16px; font-size:15px; }

/* LINKS HOVER */
a:hover { opacity:0.85; transition:0.3s; }
</style>

</head>
<body>

<!-- TOP BAR -->
<div class="top-bar">
    <span>Email: info@yourcollege.edu.np</span>
    <span>Phone: +977 01-1234567</span>
    <a href="authentication/register.php" class="apply-btn">Apply Now</a>
    <a href="authentication/login.php">Login</a>
</div>

<!-- MAIN NAV -->
<nav>
    <img src="assets/images/everest logo.png" alt="Everest College Logo" class="logo">
    <div class="menu">
        <a href="#home">Home</a>
        <a href="#courses">Programs</a>
        <a href="#about">About</a>
        <a href="#notices">Notices</a>
        <a href="#contact">Contact</a>
    </div>
</nav>

<!-- HERO SECTION -->
<div class="hero-container" id="home">
    <div class="hero-text">
        <h1>Welcome to Everest College</h1>
        <p>Providing quality education and shaping futures.</p>
        <a href="authentication/register.php" class="btn">Apply Now</a>
    </div>
    <img src="assets/images/banner.png" alt="Banner" class="banner">
</div>

<!-- COURSES -->
<section id="courses">
    <h2>Our Programs</h2>
    <div class="course-list">
        <div class="course-card">
            <img src="assets/images/bba.jpg" alt="BBA Program">
            <h3>BBA</h3>
            <p>Bachelor of Business Administration</p>
        </div>
        <div class="course-card">
            <img src="assets/images/bca.jpg" alt="BCA Program">
            <h3>BCA</h3>
            <p>Bachelor of Computer Applications</p>
        </div>
        <div class="course-card">
            <img src="assets/images/mbs.jpg" alt="MBS Program">
            <h3>MBS</h3>
            <p>Master of Business Studies</p>
        </div>
        <div class="course-card">
            <img src="assets/images/bbs.jpg" alt="BBS Program">
            <h3>BBS</h3>
            <p>Bachelor of Business Studies</p>
        </div>
    </div>
</section>

<!-- ABOUT -->
<section id="about">
    <h2>About Us</h2>

    <img src="assets/images/aboutus.jpeg" alt="About Us" 
         style="width:300px; margin-bottom:20px;">

    <p>
        We look forward to welcoming you to <strong>Everest College</strong>. 
        Our faculty and administrative staff are here to teach, support, and 
        challenge you to reach your full potential. We aim to provide an 
        environment where academic excellence meets personal growth.
    </p>

    <p>
        At Everest, learning is both intense and rewarding. With a strong 
        industry presence and career network, you won’t just study what you love—you’ll do what you love. 
        Hands-on learning is integrated into the curriculum, helping you build 
        real-world experience and professional connections before you graduate.
    </p>

    <p>
        We believe in balancing professional success with personal well-being. 
        Our faculty and researchers are encouraged to excel in their fields 
        while maintaining a healthy work-life balance. At Everest, people are 
        our most valuable resource.
    </p>

    <p>
        Whether you're seeking practical experience, a career change, or career 
        advancement, Everest College offers programs designed to meet modern 
        industry demands. Students benefit from expert faculty, internships, 
        and financial opportunities that turn academic goals into reality.
    </p>

    <p>
        <strong>Everest success starts here.</strong>
    </p>
</section>

<!-- NOTICES -->
<section id="notices">
    <h2>Notices & News</h2>
    <div class="news-list">
        <div class="notice-card">
            <h4>Admissions Open for 2026</h4>
            <p>Apply now for Bachelor and Master programs.</p>
        </div>
        <div class="notice-card">
            <h4>Exam Schedule Released</h4>
            <p>Check your exam timetables online.</p>
        </div>
        
    </div>
</section>

<!-- CONTACT -->
<section id="contact">
    <h2>Contact Us</h2>
    <div class="contact-info">
        <p><strong>Address:</strong> Boudha, Tushal, Kathmandu, Nepal</p>
        <p><strong>Phone:</strong> 014570224</p>
        <p><strong>Email:</strong> info@yourcollege.edu.np</p>
    </div>
    <div class="map-container">
        <iframe src="https://maps.google.com/maps?q=Boudha,%20Kathmandu&t=&z=15&ie=UTF8&iwloc=&output=embed"></iframe>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <p>&copy; 2026 Everest College. All rights reserved.</p>
</footer>

</body>
</html>