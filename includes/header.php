<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?php echo isset($page_title) ? $page_title : "Valley View University"; ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;700;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#0A2A4E",
                        "secondary": "#FFD700",
                        "accent": "#40E0D0",
                        "background-light": "#FFFFFF",
                        "background-dark": "#0F172A",
                        "text-light": "#1E293B",
                        "text-dark": "#F1F5F9",
                        "gray-light": "#94A3B8",
                        "gray-dark": "#64748B"
                    },
                    fontFamily: {
                        "display": ["Lexend", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.5rem",
                        "lg": "0.75rem",
                        "xl": "1rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings:
            'FILL' 0,
            'wght' 400,
            'GRAD' 0,
            'opsz' 24
        }
        /* Dropdown menu styles */
        .dropdown:hover .dropdown-menu {
            display: block;
        }
        /* Improved dropdown behavior with transition delays */
        .dropdown-menu {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            transform: translateY(-10px);
        }
        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        /* Submenu behavior - only show on direct hover */
        .dropdown-submenu .dropdown-menu {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            transform: translateX(-10px);
        }
        .dropdown-submenu:hover > .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }
        /* Ensure sub-submenus are hidden by default */
        .dropdown-submenu .dropdown-submenu .dropdown-menu {
            opacity: 0;
            visibility: hidden;
        }
        .dropdown-submenu .dropdown-submenu:hover > .dropdown-menu {
            opacity: 1;
            visibility: visible;
        }
        /* Enhanced header styling */
        .header-logo {
            transition: transform 0.3s ease;
        }
        .header-logo:hover {
            transform: scale(1.05);
        }
        /* Enhanced navigation links */
        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #FFD700;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }
        .nav-link.active::after {
            width: 100%;
        }
        /* Enhanced button styling */
        .header-button {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-text-light dark:text-text-dark">
<div class="relative flex min-h-screen w-full flex-col">
    <header class="sticky top-0 z-50 w-full bg-white dark:bg-slate-900 backdrop-blur-lg border-b border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="container mx-auto px-2 sm:px-4">
            <div class="flex h-20 items-center justify-between flex-nowrap">
                <div class="flex items-center gap-4 md:gap-6">
                    <a class="flex items-center gap-2 md:gap-3 text-text-light dark:text-text-dark flex-shrink-0" href="homepage.php">
                        <img src="vvu_logo.jpg" alt="Valley View University Logo" class="header-logo h-10 w-auto object-contain">
                        <h2 class="text-lg md:text-xl font-bold tracking-tight text-primary dark:text-white whitespace-nowrap">Valley View University</h2>
                    </a>
                    <nav class="hidden lg:flex items-center gap-4 md:gap-6 flex-nowrap">
                        <!-- Home -->
                        <a class="nav-link text-xs md:text-sm font-semibold hover:text-primary dark:hover:text-secondary transition-colors whitespace-nowrap <?php echo (isset($active_page) && $active_page == 'home') ? 'text-primary dark:text-secondary active' : 'text-text-light dark:text-text-dark'; ?>" href="homepage.php">Home</a>
                        
                        <!-- About Us with Dropdown -->
                        <div class="relative dropdown group flex-shrink-0">
                            <button class="nav-link flex items-center gap-1 text-xs md:text-sm font-semibold hover:text-primary dark:hover:text-secondary transition-colors whitespace-nowrap <?php echo (isset($active_page) && $active_page == 'about') ? 'text-primary dark:text-secondary active' : 'text-text-light dark:text-text-dark'; ?>">
                                About Us
                                <span class="material-symbols-outlined text-base md:text-lg">arrow_drop_down</span>
                            </button>
                            <div class="dropdown-menu absolute left-0 mt-2 w-48 md:w-56 lg:w-64 rounded-lg shadow-lg bg-background-light dark:bg-background-dark border border-gray-light/20 dark:border-gray-dark/20 py-2 hidden group-hover:block">
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="mission_and_vision.php">Mission and Vision</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="core_values.php">Core Values</a>
                                
                                <!-- Offices Submenu -->
                                <div class="relative dropdown-submenu group/sub">
                                    <button class="flex justify-between items-center w-full px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary">
                                        Offices
                                        <span class="material-symbols-outlined text-lg">arrow_right</span>
                                    </button>
                                    <div class="dropdown-menu absolute left-full top-0 mt-0 w-48 md:w-56 lg:w-64 rounded-lg shadow-lg bg-background-light dark:bg-background-dark border border-gray-light/20 dark:border-gray-dark/20 py-2 hidden">
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="office_of_the_vice_chancellor.php">Office of the Vice Chancellor</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="office_of_the_pro-vice_chancellor.php">Office of the Pro-Vice Chancellor</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="office_of_the_registrar.php">Office of the Registrar</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="rectors.php">Rectors</a>
                                    </div>
                                </div>
                                
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="accreditation_and_charter.php">Accreditation and Charter</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="strategic_plan.php">Strategic Plan</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="policies.php">Policies</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="recorders.php">Recorders</a>
                                
                                <!-- The VVU Submenu -->
                                <div class="relative dropdown-submenu group/sub">
                                    <button class="flex justify-between items-center w-full px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary">
                                        The VVU
                                        <span class="material-symbols-outlined text-lg">arrow_right</span>
                                    </button>
                                    <div class="dropdown-menu absolute left-full top-0 mt-0 w-48 md:w-56 lg:w-64 rounded-lg shadow-lg bg-background-light dark:bg-background-dark border border-gray-light/20 dark:border-gray-dark/20 py-2 hidden">
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="vvu_anthem.php">VVU Anthem</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="the_campus.php">The Campus</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Our History</a>
                                    </div>
                                </div>
                                
                                <!-- Faculty and Staff Directory Submenu -->
                                <div class="relative dropdown-submenu group/sub">
                                    <button class="flex justify-between items-center w-full px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary">
                                        Faculty and Staff Directory
                                        <span class="material-symbols-outlined text-lg">arrow_right</span>
                                    </button>
                                    <div class="dropdown-menu absolute left-full top-0 mt-0 w-48 md:w-56 lg:w-64 rounded-lg shadow-lg bg-background-light dark:bg-background-dark border border-gray-light/20 dark:border-gray-dark/20 py-2 hidden">
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="faculty_encyclopedia.php">Faculty Encyclopedia</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="staff_encyclopedia.php">Staff Encyclopedia</a>
                                    </div>
                                </div>
                                
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="faqs_about_vvu.php">FAQs about VVU</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="ecology.php">Ecology</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="contact_us.php">Contact</a>
                            </div>
                        </div>
                        
                        <!-- Academics with Dropdown -->
                        <div class="relative dropdown group flex-shrink-0">
                            <button class="nav-link flex items-center gap-1 text-xs md:text-sm font-semibold hover:text-primary dark:hover:text-secondary transition-colors whitespace-nowrap <?php echo (isset($active_page) && $active_page == 'academics') ? 'text-primary dark:text-secondary active' : 'text-text-light dark:text-text-dark'; ?>">
                                Academics
                                <span class="material-symbols-outlined text-base md:text-lg">arrow_drop_down</span>
                            </button>
                            <div class="dropdown-menu absolute left-0 mt-2 w-48 md:w-56 lg:w-64 rounded-lg shadow-lg bg-background-light dark:bg-background-dark border border-gray-light/20 dark:border-gray-dark/20 py-2 hidden group-hover:block">
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="academics.php">Get Started</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="academic_programs_overview.php">Academic Programs</a>
                                
                                <!-- Schools/Faculties Submenu -->
                                <div class="relative dropdown-submenu group/sub">
                                    <button class="flex justify-between items-center w-full px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary">
                                        Schools/Faculties
                                        <span class="material-symbols-outlined text-lg">arrow_right</span>
                                    </button>
                                    <div class="dropdown-menu absolute left-full top-0 mt-0 w-48 md:w-56 lg:w-64 rounded-lg shadow-lg bg-background-light dark:bg-background-dark border border-gray-light/20 dark:border-gray-dark/20 py-2 hidden">
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="faculty_of_science.php">Faculty of Science</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">School of Business</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Faculty of Arts & Social Science</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">School of Nursing and Midwifery</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">School of Education</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">School of Graduate Studies</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">School of Theology & Missions</a>
                                    </div>
                                </div>
                                
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Centres and Campuses</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="library_resources.php">VVU Library</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Journals</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="student_handbook.php">Student Handbook</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="academic_bulletin.php">Academic Bulletin</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="academic_calendar.php">Academic Calendar</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="sandwich_calendar.php">Sandwich Calendar</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="learning_outcomes.php">Learning Outcomes</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="featured_faculty.php">Featured Faculty</a>
                            </div>
                        </div>
                        
                        <!-- Admissions with Dropdown -->
                        <div class="relative dropdown group flex-shrink-0">
                            <button class="nav-link flex items-center gap-1 text-xs md:text-sm font-semibold hover:text-primary dark:hover:text-secondary transition-colors whitespace-nowrap <?php echo (isset($active_page) && $active_page == 'admissions') ? 'text-primary dark:text-secondary active' : 'text-text-light dark:text-text-dark'; ?>">
                                Admissions
                                <span class="material-symbols-outlined text-base md:text-lg">arrow_drop_down</span>
                            </button>
                            <div class="dropdown-menu absolute left-0 mt-2 w-48 md:w-56 lg:w-64 rounded-lg shadow-lg bg-background-light dark:bg-background-dark border border-gray-light/20 dark:border-gray-dark/20 py-2 hidden group-hover:block">
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Executive Sports Courses</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Provisional Admissions List</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Entry Requirements</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Caution to Applicants</a>
                                
                                <!-- Scholarships Submenu -->
                                <div class="relative dropdown-submenu group/sub">
                                    <button class="flex justify-between items-center w-full px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary">
                                        Scholarships
                                        <span class="material-symbols-outlined text-lg">arrow_right</span>
                                    </button>
                                    <div class="dropdown-menu absolute left-full top-0 mt-0 w-48 md:w-56 lg:w-64 rounded-lg shadow-lg bg-background-light dark:bg-background-dark border border-gray-light/20 dark:border-gray-dark/20 py-2 hidden">
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Scholarship Forms</a>
                                    </div>
                                </div>
                                
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Fee Structure</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="why_choose_vvu.php">Why Choose VVU</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="apply.php">Apply Online</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Downloads Forms</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Mature Entrance</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Degree and Diploma in Music</a>
                            </div>
                        </div>
                        
                        <!-- Life @ VVU with Dropdown -->
                        <div class="relative dropdown group flex-shrink-0">
                            <button class="nav-link flex items-center gap-1 text-xs md:text-sm font-semibold hover:text-primary dark:hover:text-secondary transition-colors whitespace-nowrap <?php echo (isset($active_page) && $active_page == 'student_life') ? 'text-primary dark:text-secondary active' : 'text-text-light dark:text-text-dark'; ?>">
                                Life @ VVU
                                <span class="material-symbols-outlined text-base md:text-lg">arrow_drop_down</span>
                            </button>
                            <div class="dropdown-menu absolute left-0 mt-2 w-48 md:w-56 lg:w-64 rounded-lg shadow-lg bg-background-light dark:bg-background-dark border border-gray-light/20 dark:border-gray-dark/20 py-2 hidden group-hover:block">
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Philosophy on Dress</a>
                                
                                <!-- Activities and Clubs Submenu -->
                                <div class="relative dropdown-submenu group/sub">
                                    <button class="flex justify-between items-center w-full px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary">
                                        Activities and Clubs
                                        <span class="material-symbols-outlined text-lg">arrow_right</span>
                                    </button>
                                    <div class="dropdown-menu absolute left-full top-0 mt-0 w-48 md:w-56 lg:w-64 rounded-lg shadow-lg bg-background-light dark:bg-background-dark border border-gray-light/20 dark:border-gray-dark/20 py-2 hidden">
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">COSSA</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">THEMSA</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">NURSA</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">SOBSA</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">EDSA</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">BMEDSA</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">ISA</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">DESSA</a>
                                    </div>
                                </div>
                                
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">SRC</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="accommodation.php">Accommodation</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="grocery.php">Food Services</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Work Study</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Spiritual Life and Development</a>
                                
                                <!-- Gallery Submenu -->
                                <div class="relative dropdown-submenu group/sub">
                                    <button class="flex justify-between items-center w-full px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary">
                                        Gallery
                                        <span class="material-symbols-outlined text-lg">arrow_right</span>
                                    </button>
                                    <div class="dropdown-menu absolute left-full top-0 mt-0 w-48 md:w-56 lg:w-64 rounded-lg shadow-lg bg-background-light dark:bg-background-dark border border-gray-light/20 dark:border-gray-dark/20 py-2 hidden">
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">News Gallery</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Strategic Planning - 2023</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">SRC Gallery</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Stories with Dropdown -->
                        <div class="relative dropdown group flex-shrink-0">
                            <button class="nav-link flex items-center gap-1 text-xs md:text-sm font-semibold hover:text-primary dark:hover:text-secondary transition-colors whitespace-nowrap">
                                Stories
                                <span class="material-symbols-outlined text-base md:text-lg">arrow_drop_down</span>
                            </button>
                            <div class="dropdown-menu absolute left-0 mt-2 w-48 md:w-56 lg:w-64 rounded-lg shadow-lg bg-background-light dark:bg-background-dark border border-gray-light/20 dark:border-gray-dark/20 py-2 hidden group-hover:block">
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="events.php">Events</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Notices</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="news_&_events.php">News</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="vvu_radio.php">Valley View Radio</a>
                            </div>
                        </div>
                        
                        <!-- Resources with Dropdown -->
                        <div class="relative dropdown group flex-shrink-0">
                            <button class="nav-link flex items-center gap-1 text-xs md:text-sm font-semibold hover:text-primary dark:hover:text-secondary transition-colors whitespace-nowrap">
                                Resources
                                <span class="material-symbols-outlined text-base md:text-lg">arrow_drop_down</span>
                            </button>
                            <div class="dropdown-menu absolute left-0 mt-2 w-48 md:w-56 lg:w-64 rounded-lg shadow-lg bg-background-light dark:bg-background-dark border border-gray-light/20 dark:border-gray-dark/20 py-2 hidden group-hover:block">
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="freshmen_info.php">Freshmen Info</a>
                                
                                <!-- Parents Submenu -->
                                <div class="relative dropdown-submenu group/sub">
                                    <button class="flex justify-between items-center w-full px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary">
                                        Parents
                                        <span class="material-symbols-outlined text-lg">arrow_right</span>
                                    </button>
                                    <div class="dropdown-menu absolute left-full top-0 mt-0 w-48 md:w-56 lg:w-64 rounded-lg shadow-lg bg-background-light dark:bg-background-dark border border-gray-light/20 dark:border-gray-dark/20 py-2 hidden">
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">New to VVU</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Take a Tour</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Download our Forms</a>
                                    </div>
                                </div>
                                
                                <!-- Current Students Submenu -->
                                <div class="relative dropdown-submenu group/sub">
                                    <button class="flex justify-between items-center w-full px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary">
                                        Current Students
                                        <span class="material-symbols-outlined text-lg">arrow_right</span>
                                    </button>
                                    <div class="dropdown-menu absolute left-full top-0 mt-0 w-48 md:w-56 lg:w-64 rounded-lg shadow-lg bg-background-light dark:bg-background-dark border border-gray-light/20 dark:border-gray-dark/20 py-2 hidden">
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="mobile_money_fee_payment.php">Mobile Money Fee Payment</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Student Email</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">iSchool</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">E-learning</a>
                                    </div>
                                </div>
                                
                                <!-- Faculty and Staff Submenu -->
                                <div class="relative dropdown-submenu group/sub">
                                    <button class="flex justify-between items-center w-full px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary">
                                        Faculty and Staff
                                        <span class="material-symbols-outlined text-lg">arrow_right</span>
                                    </button>
                                    <div class="dropdown-menu absolute left-full top-0 mt-0 w-48 md:w-56 lg:w-64 rounded-lg shadow-lg bg-background-light dark:bg-background-dark border border-gray-light/20 dark:border-gray-dark/20 py-2 hidden">
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">University Policies</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Faculty & Staff Email</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">iSchool</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">E-Learning</a>
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Faculty and Staff Forms</a>
                                    </div>
                                </div>
                                
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">Downloads</a>
                                
                                <!-- Employment Opportunity Submenu -->
                                <div class="relative dropdown-submenu group/sub">
                                    <button class="flex justify-between items-center w-full px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary">
                                        Employment Opportunity
                                        <span class="material-symbols-outlined text-lg">arrow_right</span>
                                    </button>
                                    <div class="dropdown-menu absolute left-full top-0 mt-0 w-48 md:w-56 lg:w-64 rounded-lg shadow-lg bg-background-light dark:bg-background-dark border border-gray-light/20 dark:border-gray-dark/20 py-2 hidden">
                                        <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="employment_opportunity.php">Employment Application Form</a>
                                    </div>
                                </div>
                                
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">IBC Abstracts</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="#">E-learning Materials</a>
                            </div>
                        </div>
                        
                        <!-- Ventures with Dropdown -->
                        <div class="relative dropdown group flex-shrink-0">
                            <button class="nav-link flex items-center gap-1 text-xs md:text-sm font-semibold hover:text-primary dark:hover:text-secondary transition-colors whitespace-nowrap">
                                Ventures
                                <span class="material-symbols-outlined text-base md:text-lg">arrow_drop_down</span>
                            </button>
                            <div class="dropdown-menu absolute left-0 mt-2 w-48 md:w-56 lg:w-64 rounded-lg shadow-lg bg-background-light dark:bg-background-dark border border-gray-light/20 dark:border-gray-dark/20 py-2 hidden group-hover:block">
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="bakery_factory_page.php">Bakery Factory</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="water_factory.html">Water Factory</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="grocery.html">Grocery</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="cement_block_factory.php">Cement Block Factory</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="basic_schools_page.php">Basic Schools</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="guest_house.php">Guest House</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="hospital.php">Hospital</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="vvu_radio.html">Radio Station</a>
                                <a class="block px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-secondary/10 hover:text-primary dark:hover:text-secondary" href="vvu_eye_clinic.php">VVU Eye Clinic</a>
                            </div>
                        </div>
                    </nav>
                </div>
                <div class="hidden lg:flex items-center justify-end gap-2 md:gap-3 flex-shrink-0">
                    <a class="header-button flex h-8 md:h-10 min-w-[60px] md:min-w-[84px] items-center justify-center overflow-hidden rounded-lg bg-primary/10 px-2 md:px-4 text-primary dark:bg-slate-700 dark:text-white text-xs md:text-sm font-bold tracking-[0.015em] hover:bg-primary/20 dark:hover:bg-slate-600 transition-colors" href="#">
                        <span class="truncate">Visit</span>
                    </a>
                    <a class="header-button flex h-8 md:h-10 min-w-[60px] md:min-w-[84px] items-center justify-center overflow-hidden rounded-lg bg-primary/10 px-2 md:px-4 text-primary dark:bg-slate-700 dark:text-white text-xs md:text-sm font-bold tracking-[0.015em] hover:bg-primary/20 dark:hover:bg-slate-600 transition-colors" href="donate.php">
                        <span class="truncate">Give</span>
                    </a>
                    <a class="header-button flex h-8 md:h-10 min-w-[60px] md:min-w-[84px] items-center justify-center overflow-hidden rounded-lg bg-secondary px-2 md:px-4 text-primary text-xs md:text-sm font-bold tracking-[0.015em] hover:bg-yellow-500 transition-colors" href="apply.php">
                        <span class="truncate">Apply</span>
                    </a>
                    <button class="header-button flex h-8 md:h-10 w-8 md:w-10 cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-gray-light/10 dark:bg-slate-700 text-text-light dark:text-text-dark hover:bg-gray-light/20 dark:hover:bg-slate-600 transition-colors">
                        <span class="material-symbols-outlined text-base md:text-xl">search</span>
                    </button>
                </div>
                <button class="lg:hidden flex h-10 w-10 cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-gray-light/10 dark:bg-slate-700 text-text-light dark:text-text-dark hover:bg-gray-light/20 dark:hover:bg-slate-600 transition-colors">
                    <span class="material-symbols-outlined text-2xl">menu</span>
                </button>
            </div>
        </div>
    </header>
    <main class="flex-grow">