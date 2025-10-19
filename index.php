
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Greenwood Academy - Excellence in Education</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --color-deep-blue: #002B5C;
            --color-gold: #FFB81C;
            --color-white: #ffffff;
            --color-light-gray: #f5f5f5;
            --color-dark-gray: #333333;
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            padding-top: 76px;
            background-color: #f9f9f9;
        }
        
        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 1rem;
            transition: var(--transition);
        }
        
        .navbar.scrolled {
            padding: 0.3rem 1rem;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--color-deep-blue) !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .navbar-brand img {
            transition: var(--transition);
            height: 40px;
            width: auto;
        }
        
        .navbar-brand:hover img {
            transform: rotate(10deg);
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--color-dark-gray) !important;
            position: relative;
            margin: 0 0.5rem;
            transition: var(--transition);
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background: var(--color-gold);
            transition: var(--transition);
            border-radius: 2px;
        }
        
        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }
        
        .nav-link:hover {
            color: var(--color-deep-blue) !important;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0, 43, 92, 0.8), rgba(0, 43, 92, 0.9)), url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2000&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 6rem 0;
            text-align: center;
        }
        
        /* Section Styling */
        .section-title {
            position: relative;
            margin-bottom: 2.5rem;
            font-weight: 700;
            color: var(--color-deep-blue);
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--color-gold);
            border-radius: 2px;
        }
        
        .section-title.text-center::after {
            left: 50%;
            transform: translateX(-50%);
        }
        
        /* Features Section */
        .feature-card {
            text-align: center;
            padding: 2rem;
            border-radius: 10px;
            transition: var(--transition);
            height: 100%;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background: white;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            font-size: 3rem;
            color: var(--color-deep-blue);
            margin-bottom: 1.5rem;
        }
        
        /* News Section */
        .news-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            transition: var(--transition);
            height: 100%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .news-date {
            color: var(--color-gold);
            font-weight: 600;
        }
        
        /* Testimonials */
        .testimonial-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 2rem;
            backdrop-filter: blur(5px);
            height: 100%;
        }
        
        .testimonial-text {
            font-style: italic;
            margin-bottom: 1.5rem;
        }
        
        .testimonial-author {
            font-weight: 600;
            color: var(--color-gold);
        }
        
        /* Programs */
        .program-card {
            border-radius: 10px;
            overflow: hidden;
            transition: var(--transition);
            height: 100%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            background: white;
        }
        
        .program-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        /* Form Styling */
        .form-control {
            border: 2px solid #e1e5eb;
            border-radius: 8px;
            padding: 0.8rem 1rem;
            transition: var(--transition);
        }
        
        .form-control:focus {
            border-color: var(--color-deep-blue);
            box-shadow: 0 0 0 0.25rem rgba(0, 43, 92, 0.25);
        }
        
        .btn-primary {
            background: var(--color-deep-blue);
            border: none;
            padding: 0.8rem 2rem;
            font-weight: 600;
            border-radius: 50px;
            transition: var(--transition);
        }
        
        .btn-primary:hover {
            background: var(--color-gold);
            color: var(--color-dark-gray);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        /* Footer */
        footer {
            background: var(--color-dark-gray);
            color: var(--color-white);
            padding: 3rem 0 1rem;
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 0.5rem;
        }
        
        .footer-links a {
            color: var(--color-light-gray);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .footer-links a:hover {
            color: var(--color-gold);
            padding-left: 5px;
        }
        
        .social-icons a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            color: var(--color-white);
            margin-right: 10px;
            transition: var(--transition);
        }
        
        .social-icons a:hover {
            background: var(--color-gold);
            color: var(--color-dark-gray);
            transform: translateY(-5px);
        }
        
        /* Page Content */
        .page-content {
            display: none;
            padding: 3rem 0;
        }
        
        .page-content.active {
            display: block;
        }
        
        /* Logo Styling */
        .logo {
            width: 40px;
            height: 40px;
        }
        
        .logo-large {
            width: 120px;
            height: 120px;
            margin-bottom: 2rem;
            border-radius: 50%;
            padding: 5px;
            background: var(--color-white);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: var(--transition);
            animation: pulse 2s infinite;
        }
        
        .logo-large:hover {
            transform: scale(1.05);
        }
        
        /* Image Styling */
        .img-container {
            overflow: hidden;
            border-radius: 10px;
        }
        
        .img-fluid {
            transition: transform 0.5s ease;
        }
        
        .img-fluid:hover {
            transform: scale(1.05);
        }
        
        /* Animations */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .animate__animated {
            animation-duration: 1s;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar-nav {
                text-align: center;
                padding: 1rem 0;
            }
            
            .nav-link {
                margin: 0.3rem 0;
            }
            
            .hero-section {
                padding: 4rem 0;
            }
        }
        
        /* Scroll to top button */
        .scroll-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--color-deep-blue);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            opacity: 0;
            visibility: hidden;
            z-index: 1000;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }
        
        .scroll-to-top.show {
            opacity: 1;
            visibility: visible;
        }
        
        .scroll-to-top:hover {
            background: var(--color-gold);
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="#" data-page="home">
                <!-- Embedded SVG Logo -->
                <svg class="logo" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="50" r="45" fill="#002B5C" />
                    <path d="M30,30 L70,30 L70,70 L30,70 Z" fill="#FFB81C" />
                    <text x="50" y="55" text-anchor="middle" fill="#002B5C" font-weight="bold" font-size="16">GA</text>
                </svg>
                Greenwood Academy
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" data-page="home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-page="about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-page="academics">Academics</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-page="admissions">Admissions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-page="campus">Campus Life</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-page="news">News & Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-page="contact">Contact</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="btn btn-outline-primary btn-sm" href="login.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Home Page -->
    <div id="home" class="page-content active">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <!-- Embedded SVG Logo -->
                        <svg class="logo-large" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="50" cy="50" r="45" fill="#002B5C" />
                            <path d="M30,30 L70,30 L70,70 L30,70 Z" fill="#FFB81C" />
                            <text x="50" y="55" text-anchor="middle" fill="#002B5C" font-weight="bold" font-size="16">GA</text>
                        </svg>
                        <h1 class="display-4 fw-bold mb-4">Welcome to Greenwood Academy</h1>
                        <p class="lead mb-5">Excellence in education since 1985. Empowering students to achieve their fullest potential in a nurturing and challenging environment.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="#" class="btn btn-light btn-lg" data-page="admissions">Apply Now</a>
                            <a href="#" class="btn btn-outline-light btn-lg" data-page="about">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-5">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="section-title text-center">Why Choose Greenwood Academy?</h2>
                    <p class="lead">Excellence in education since 1985</p>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <h4>Academic Excellence</h4>
                            <p>Our curriculum is designed to challenge students and foster critical thinking skills.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h4>Dedicated Faculty</h4>
                            <p>Our teachers are passionate educators with years of experience in their fields.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-microscope"></i>
                            </div>
                            <h4>Modern Facilities</h4>
                            <p>State-of-the-art laboratories, libraries, and sports facilities enhance the learning experience.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- News Section -->
        <section class="py-5 bg-light">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="section-title text-center">Latest News & Events</h2>
                    <p class="lead">Stay updated with what's happening in our school community</p>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card news-card">
                            <div class="img-container">
                                <img src="https://images.unsplash.com/photo-1584697964358-3e14ca57658b?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" class="card-img-top img-fluid" alt="Science Fair">
                            </div>
                            <div class="card-body">
                                <span class="news-date"><i class="far fa-calendar me-1"></i>June 15, 2023</span>
                                <h5 class="card-title mt-2">Annual Science Fair Winners</h5>
                                <p class="card-text">Congratulations to all participants and winners of our annual science fair.</p>
                                <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card news-card">
                            <div class="img-container">
                                <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" class="card-img-top img-fluid" alt="Sports Complex">
                            </div>
                            <div class="card-body">
                                <span class="news-date"><i class="far fa-calendar me-1"></i>June 10, 2023</span>
                                <h5 class="card-title mt-2">New Sports Complex Opening</h5>
                                <p class="card-text">Our new state-of-the-art sports complex will open next month.</p>
                                <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card news-card">
                            <div class="img-container">
                                <img src="https://images.unsplash.com/photo-1535982337059-51a5b2d3c079?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" class="card-img-top img-fluid" alt="Summer Program">
                            </div>
                            <div class="card-body">
                                <span class="news-date"><i class="far fa-calendar me-1"></i>June 5, 2023</span>
                                <h5 class="card-title mt-2">Summer Program Registration</h5>
                                <p class="card-text">Registration for our summer enrichment programs is now open.</p>
                                <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="#" class="btn btn-primary" data-page="news">View All News</a>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="py-5 bg-dark text-white">
            <div class="container">
                <div class="text-center mb-5">
                    <h2>What Parents & Students Say</h2>
                    <p class="lead">Hear from our school community</p>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="testimonial-card">
                            <div class="testimonial-text">
                                "This school has provided an exceptional learning environment for my child. The teachers are dedicated and caring."
                            </div>
                            <div class="testimonial-author">- Sarah Johnson, Parent</div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="testimonial-card">
                            <div class="testimonial-text">
                                "The extracurricular activities and academic programs have helped me discover my passions and strengths."
                            </div>
                            <div class="testimonial-author">- Michael Chen, Student</div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="testimonial-card">
                            <div class="testimonial-text">
                                "As an educator, I appreciate the school's commitment to innovation and student-centered learning approaches."
                            </div>
                            <div class="testimonial-author">- Dr. Emily Rodriguez, Teacher</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="py-5" style="background: var(--color-deep-blue); color: var(--color-white);">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3>Ready to Join Our Community?</h3>
                        <p>Schedule a visit or apply for admission today</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="#" class="btn btn-light btn-lg me-2" data-page="contact"><i class="fas fa-calendar me-1"></i>Schedule Visit</a>
                        <a href="#" class="btn btn-outline-light btn-lg" data-page="admissions">Apply Now</a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- About Page -->
    <div id="about" class="page-content">
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h2 class="section-title">About Greenwood Academy</h2>
                    <p class="lead">Established in 1985, Greenwood Academy has been a pillar of educational excellence in our community for nearly four decades.</p>
                    
                    <div class="my-5 img-container">
                        <img src="https://images.unsplash.com/photo-1562774053-701939374585?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="img-fluid rounded" alt="School Campus">
                    </div>
                    
                    <h3>Our History</h3>
                    <p>Founded by a group of visionary educators, Greenwood Academy began with a simple mission: to provide a challenging yet supportive learning environment where students could thrive academically, socially, and emotionally.</p>
                    
                    <p>Over the years, we've grown from a small community school to a respected educational institution, but we've never lost sight of our founding principles. We believe in educating the whole child, fostering not just academic excellence but also character development, leadership skills, and a sense of social responsibility.</p>
                    
                    <h3>Our Mission</h3>
                    <p>Our mission is to empower students to achieve their fullest potential in a nurturing and challenging environment that encourages intellectual curiosity, critical thinking, and a lifelong love of learning.</p>
                    
                    <h3>Our Values</h3>
                    <ul>
                        <li><strong>Excellence:</strong> We strive for the highest standards in all we do.</li>
                        <li><strong>Integrity:</strong> We act with honesty, respect, and ethical behavior.</li>
                        <li><strong>Innovation:</strong> We embrace new ideas and approaches to education.</li>
                        <li><strong>Community:</strong> We foster a sense of belonging and shared purpose.</li>
                        <li><strong>Diversity:</strong> We celebrate and learn from our differences.</li>
                    </ul>
                    
                    <h3>Our Achievements</h3>
                    <p>Over the years, our students and faculty have earned numerous awards and recognitions, including:</p>
                    <ul>
                        <li>National Blue Ribbon School Award (2019, 2022)</li>
                        <li>State Science Fair Champions (2018-2023)</li>
                        <li>Regional Athletics Championships (15 titles in the last 10 years)</li>
                        <li>100% college acceptance rate for the past 12 years</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Academics Page -->
    <div id="academics" class="page-content">
        <div class="container py-5">
            <h2 class="section-title text-center mb-5">Academic Programs</h2>
            
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto">
                    <p class="lead text-center">At Greenwood Academy, we offer a comprehensive curriculum designed to challenge students and prepare them for success in college and beyond.</p>
                </div>
            </div>
            
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="program-card card">
                        <div class="card-body">
                            <h3 class="card-title">Elementary School</h3>
                            <p class="card-text">Our elementary program focuses on building strong foundations in literacy, numeracy, and social skills through engaging, hands-on learning experiences.</p>
                            <ul>
                                <li>Integrated curriculum focusing on core subjects</li>
                                <li>Specialist teachers for art, music, and physical education</li>
                                <li>Emphasis on social-emotional learning</li>
                                <li>Technology integration starting from Grade 1</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="program-card card">
                        <div class="card-body">
                            <h3 class="card-title">Middle School</h3>
                            <p class="card-text">Our middle school program is designed to help students navigate the transition from childhood to adolescence while challenging them academically.</p>
                            <ul>
                                <li>Departmentalized instruction in core subjects</li>
                                <li>Exploratory wheel for elective courses</li>
                                <li>Advisory program for social-emotional support</li>
                                <li>Leadership and service opportunities</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="program-card card">
                        <div class="card-body">
                            <h3 class="card-title">High School</h3>
                            <p class="card-text">Our college-preparatory high school program offers rigorous academics, extensive elective choices, and numerous extracurricular opportunities.</p>
                            <ul>
                                <li>Advanced Placement and Honors courses</li>
                                <li>STEM-focused pathways</li>
                                <li>Arts and humanities programs</li>
                                <li>College counseling and career guidance</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="program-card card">
                        <div class="card-body">
                            <h3 class="card-title">Special Programs</h3>
                            <p class="card-text">We offer a variety of specialized programs to meet the diverse needs and interests of our student community.</p>
                            <ul>
                                <li>Gifted and Talented Education (GATE)</li>
                                <li>Learning Support Services</li>
                                <li>English Language Learner program</li>
                                <li>After-school enrichment classes</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-body text-center">
                            <h3 class="card-title">Academic Calendar</h3>
                            <p class="card-text">Download our academic calendar for the current school year to stay informed about important dates, holidays, and events.</p>
                            <a href="#" class="btn btn-primary"><i class="fas fa-download me-2"></i>Download Calendar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admissions Page -->
    <div id="admissions" class="page-content">
        <div class="container py-5">
            <h2 class="section-title text-center mb-5">Admissions Process</h2>
            
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto">
                    <p class="lead text-center">Thank you for your interest in Greenwood Academy. We're exciting to guide you through our admissions process.</p>
                </div>
            </div>
            
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h4>1. Inquiry</h4>
                        <p>Submit an online inquiry form to receive more information about our school and the admissions process.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <h4>2. Tour & Interview</h4>
                        <p>Schedule a campus tour and interview to experience our community firsthand.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h4>3. Application</h4>
                        <p>Complete our online application and submit all required documents by the deadline.</p>
                    </div>
                </div>
            </div>
            
            <div class="row mb-5">
                <div class="col-lg-10 mx-auto">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Application Deadlines</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Grade Level</th>
                                            <th>Application Deadline</th>
                                            <th>Decision Notification</th>
                                            <th>Enrollment Deadline</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Elementary (K-5)</td>
                                            <td>January 15, 2024</td>
                                            <td>March 1, 2024</td>
                                            <td>March 15, 2024</td>
                                        </tr>
                                        <tr>
                                            <td>Middle School (6-8)</td>
                                            <td>January 31, 2024</td>
                                            <td>March 15, 2024</td>
                                            <td>March 31, 2024</td>
                                        </tr>
                                        <tr>
                                            <td>High School (9-12)</td>
                                            <td>February 15, 2024</td>
                                            <td>April 1, 2024</td>
                                            <td>April 15, 2024</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Tuition & Financial Aid</h3>
                            <p class="card-text">We believe that financial circumstances should not be a barrier to accessing quality education. Greenwood Academy offers need-based financial aid to qualified families.</p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="#" class="btn btn-primary">View Tuition Details</a>
                                <a href="#" class="btn btn-outline-primary">Financial Aid Information</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Campus Life Page -->
    <div id="campus" class="page-content">
        <div class="container py-5">
            <h2 class="section-title text-center mb-5">Campus Life</h2>
            
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto">
                    <p class="lead text-center">At Greenwood Academy, education extends beyond the classroom. Our vibrant campus life offers numerous opportunities for growth, friendship, and fun.</p>
                </div>
            </div>
            
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="card program-card">
                        <div class="img-container">
                            <img src="https://images.unsplash.com/photo-1549060279-7e168fce7090?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" class="card-img-top img-fluid" alt="Athletics">
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">Athletics</h3>
                            <p class="card-text">Our athletic programs promote teamwork, discipline, and healthy competition. We offer a wide range of sports for all skill levels.</p>
                            <ul>
                                <li>Fall: Soccer, Cross Country, Volleyball</li>
                                <li>Winter: Basketball, Swimming, Wrestling</li>
                                <li>Spring: Track & Field, Baseball, Tennis</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card program-card">
                        <div class="img-container">
                            <img src="https://images.unsplash.com/photo-1514525253161-7a46d19cd819?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" class="card-img-top img-fluid" alt="Arts">
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">Arts & Culture</h3>
                            <p class="card-text">Our arts programs nurture creativity and self-expression through various mediums and performances.</p>
                            <ul>
                                <li>Visual Arts: Painting, Sculpture, Digital Media</li>
                                <li>Performing Arts: Theater, Dance, Music Ensembles</li>
                                <li>Cultural Clubs: Debate, Model UN, Language Clubs</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card program-card">
                        <div class="img-container">
                            <img src="https://images.unsplash.com/photo-1582582494705-f8ce0b0c24f0?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" class="card-img-top img-fluid" alt="Clubs">
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">Clubs & Organizations</h3>
                            <p class="card-text">Students can explore their interests and develop leadership skills through our diverse club offerings.</p>
                            <ul>
                                <li>STEM Club, Robotics Team, Math Olympiad</li>
                                <li>Environmental Club, Student Government</li>
                                <li>Literary Magazine, Yearbook Committee</li>
                                <li>Community Service Club, Peer Tutoring</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card program-card">
                        <div class="img-container">
                            <img src="https://images.unsplash.com/photo-1591123120675-6f7f1aae0e5b?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" class="card-img-top img-fluid" alt="Facilities">
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">Campus Facilities</h3>
                            <p class="card-text">Our 50-acre campus features state-of-the-art facilities designed to support learning and community engagement.</p>
                            <ul>
                                <li>Modern classrooms with technology integration</li>
                                <li>Science and computer laboratories</li>
                                <li>Library and media center</li>
                                <li>Athletic fields and gymnasium</li>
                                <li>Performing arts center and gallery</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="card">
                        <div class="card-body text-center">
                            <h3 class="card-title">Experience Campus Life</h3>
                            <p class="card-text">The best way to understand the Greenwood Academy experience is to see it for yourself. Schedule a campus tour today.</p>
                            <a href="#" class="btn btn-primary" data-page="contact"><i class="fas fa-calendar me-2"></i>Schedule a Tour</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- News & Events Page -->
    <div id="news" class="page-content">
        <div class="container py-5">
            <h2 class="section-title text-center mb-5">News & Events</h2>
            
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto">
                    <p class="lead text-center">Stay up to date with the latest happenings at Greenwood Academy.</p>
                </div>
            </div>
            
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card news-card">
                        <div class="img-container">
                            <img src="https://images.unsplash.com/photo-1584697964358-3e14ca57658b?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" class="card-img-top img-fluid" alt="Science Fair">
                        </div>
                        <div class="card-body">
                            <span class="news-date"><i class="far fa-calendar me-1"></i>June 15, 2023</span>
                            <h5 class="card-title mt-2">Annual Science Fair Winners</h5>
                            <p class="card-text">Congratulations to all participants and winners of our annual science fair.</p>
                            <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card news-card">
                        <div class="img-container">
                            <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" class="card-img-top img-fluid" alt="Sports Complex">
                        </div>
                        <div class="card-body">
                            <span class="news-date"><i class="far fa-calendar me-1"></i>June 10, 2023</span>
                            <h5 class="card-title mt-2">New Sports Complex Opening</h5>
                            <p class="card-text">Our new state-of-the-art sports complex will open next month.</p>
                            <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card news-card">
                        <div class="img-container">
                            <img src="https://images.unsplash.com/photo-1535982337059-51a5b2d3c079?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" class="card-img-top img-fluid" alt="Summer Program">
                        </div>
                        <div class="card-body">
                            <span class="news-date"><i class="far fa-calendar me-1"></i>June 5, 2023</span>
                            <h5 class="card-title mt-2">Summer Program Registration</h5>
                            <p class="card-text">Registration for our summer enrichment programs is now open.</p>
                            <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card news-card">
                        <div class="img-container">
                            <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" class="card-img-top img-fluid" alt="Alumni Reunion">
                        </div>
                        <div class="card-body">
                            <span class="news-date"><i class="far fa-calendar me-1"></i>May 28, 2023</span>
                            <h5 class="card-title mt-2">Alumni Reunion Weekend</h5>
                            <p class="card-text">Join us for a weekend of reconnecting with old friends and teachers.</p>
                            <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card news-card">
                        <div class="img-container">
                            <img src="https://images.unsplash.com/photo-1505373877841-8d25f7d46678?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" class="card-img-top img-fluid" alt="Spring Concert">
                        </div>
                        <div class="card-body">
                            <span class="news-date"><i class="far fa-calendar me-1"></i>May 20, 2023</span>
                            <h5 class="card-title mt-2">Spring Concert Series</h5>
                            <p class="card-text">Our music department presents its annual spring concert series.</p>
                            <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card news-card">
                        <div class="img-container">
                            <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" class="card-img-top img-fluid" alt="College Acceptance">
                        </div>
                        <div class="card-body">
                            <span class="news-date"><i class="far fa-calendar me-1"></i>May 15, 2023</span>
                            <h5 class="card-title mt-2">College Acceptance News</h5>
                            <p class="card-text">Congratulations to our seniors on their college acceptances!</p>
                            <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Upcoming Events</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Event</th>
                                            <th>Time</th>
                                            <th>Location</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>June 20, 2023</td>
                                            <td>Graduation Ceremony</td>
                                            <td>2:00 PM</td>
                                            <td>Main Auditorium</td>
                                        </tr>
                                        <tr>
                                            <td>June 22, 2023</td>
                                            <td>Summer Programs Begin</td>
                                            <td>9:00 AM</td>
                                            <td>Various Classrooms</td>
                                        </tr>
                                        <tr>
                                            <td>July 4, 2023</td>
                                            <td>Independence Day - School Closed</td>
                                            <td>All Day</td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td>July 15, 2023</td>
                                            <td>Summer Music Festival</td>
                                            <td>4:00 PM</td>
                                            <td>School Grounds</td>
                                        </tr>
                                        <tr>
                                            <td>August 1, 2023</td>
                                            <td>Fall Sports Tryouts</td>
                                            <td>3:30 PM</td>
                                            <td>Athletic Fields</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Page -->
    <div id="contact" class="page-content">
        <div class="container py-5">
            <h2 class="section-title text-center mb-5">Contact Us</h2>
            
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto">
                    <p class="lead text-center">We'd love to hear from you. Reach out to us with any questions or to schedule a visit.</p>
                </div>
            </div>
            
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h4>Address</h4>
                        <p>123 Education Street<br>Learning City, LC 12345</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h4>Phone</h4>
                        <p>(555) 123-4567<br>Admissions: ext. 123</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h4>Email</h4>
                        <p>info@greenwoodacademy.edu<br>admissions@greenwoodacademy.edu</p>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title text-center mb-4">Send Us a Message</h3>
                            <form>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="subject" class="form-label">Subject</label>
                                        <select class="form-select" id="subject">
                                            <option selected>Select a subject</option>
                                            <option>Admissions Inquiry</option>
                                            <option>General Information</option>
                                            <option>School Tour</option>
                                            <option>Other</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="message" class="form-label">Message</label>
                                        <textarea class="form-control" id="message" rows="5" required></textarea>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-primary btn-lg">Send Message</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title text-center mb-4">Campus Location</h3>
                            <div class="ratio ratio-16x9">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.9663095343008!2d-74.0042587242699!3d40.75473347138897!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c259af7ad71b83%3A0xca0d58a8ce5ac53!2s123%20Education%20St%2C%20New%20York%20NY%2010001!5e0!3m2!1sen!2sus!4v1686754962354!5m2!1sen!2sus" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>Greenwood Academy</h5>
                    <p>Excellence in education since 1985. Empowering students to achieve their fullest potential.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="#" data-page="home">Home</a></li>
                        <li><a href="#" data-page="about">About Us</a></li>
                        <li><a href="#" data-page="academics">Academics</a></li>
                        <li><a href="#" data-page="admissions">Admissions</a></li>
                        <li><a href="#" data-page="contact">Contact Us</a></li>
                    </ul>
                </div>
                
                <div class="col-md-4 mb-4">
                    <h5>Contact Information</h5>
                    <p><i class="fas fa-map-marker-alt me-2"></i> 123 Education Street, Learning City, LC 12345</p>
                    <p><i class="fas fa-phone me-2"></i> (555) 123-4567</p>
                    <p><i class="fas fa-envelope me-2"></i> info@greenwoodacademy.edu</p>
                </div>
            </div>
            
            <hr style="border-color: rgba(255,255,255,0.1);">
            
            <div class="text-center py-3">
                Copyright &copy; 2023 Greenwood Academy. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Scroll to top button -->
    <div class="scroll-to-top">
        <i class="fas fa-arrow-up"></i>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Page navigation
        document.querySelectorAll('.nav-link, [data-page]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetPage = this.getAttribute('data-page');
                
                // Update active nav link
                document.querySelectorAll('.nav-link').forEach(navLink => {
                    navLink.classList.remove('active');
                });
                this.classList.add('active');
                
                // Show target page
                document.querySelectorAll('.page-content').forEach(page => {
                    page.classList.remove('active');
                });
                document.getElementById(targetPage).classList.add('active');
                
                // Scroll to top
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
        
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNav');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Scroll to top functionality
        const scrollButton = document.querySelector('.scroll-to-top');
        
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollButton.classList.add('show');
            } else {
                scrollButton.classList.remove('show');
            }
        });
        
        scrollButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
</body>
</html>