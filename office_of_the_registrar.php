<?php
require_once 'includes/db_connect.php';
require_once 'includes/administration_content_helper.php';

// Initialize content helper
$content = new AdministrationContent($pdo);
$page = $content->getPageBySlug('office_of_the_registrar');

// Get all content sections
$pageContent = [];
if ($page) {
    $pageContent = $content->getPageContent($page['id']);
}

// Helper function to get field value with HTML cleaning
if (!function_exists('getContent')) {
    function getContent($sections, $section_key, $field_key, $default = '') {
        $value = isset($sections[$section_key]['fields'][$field_key]) ? $sections[$section_key]['fields'][$field_key] : $default;
        // Clean HTML tags and entities from CKEditor content
        return AdministrationContent::cleanHtml($value);
    }
}

$page_title = $page ? $page['page_title'] . " - Valley View University" : "Office of the Registrar - Valley View University";
$active_page = "about";
include 'includes/header.php';
?>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
    @keyframes slowZoom {
        0% { transform: scale(1); }
        100% { transform: scale(1.1); }
    }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
    .animate-float { animation: float 4s ease-in-out infinite; }
    .glass {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .dark .glass {
        background: rgba(31, 41, 55, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .service-card {
        transition: all 0.3s ease;
    }
    .service-card:hover {
        transform: translateY(-10px);
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags(getContent($pageContent, 'hero_section', 'background_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuAdHExs_SfkRASYoES-KYWziZLFeXa6CwRE1tFfcoJoSatmp3K87chu9ZaDIp4kjBmAC4kTIatiMlZ3XOe354S5VOhhunVP4Wo9_FMc1LLmh72jKzKTTlzaL4qCmkTEo6z_WERGbhxGfFNtdyLOIJMxOTvuW1sK-AmKP0QVv4GCOd6a1lt3FrWoQ9IVoflIKJeoTiDMa44B7wkgq0Ykb3ud1rt5gDR_byRW18BjRjWDIiNKKd4-z8QKco_zxFkDaYymChai--z4X8Hv')); ?>" 
                 alt="VVU Administration Building" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags(getContent($pageContent, 'hero_section', 'badge_text', 'Administrative Excellence')); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags(getContent($pageContent, 'hero_section', 'title_main', 'Office of the')); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags(getContent($pageContent, 'hero_section', 'title_highlight', 'Registrar')); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags(getContent($pageContent, 'hero_section', 'subtitle', 'Your partner in academic success. We are committed to providing exceptional service to the Valley View University community from registration to graduation.')); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Registrar Profile Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="flex flex-col lg:flex-row gap-16 items-center lg:items-start">
                <!-- Profile Image -->
                <div class="w-full lg:w-1/3 animate-fadeInUp">
                    <div class="relative group">
                        <div class="absolute -inset-4 bg-gradient-to-r from-blue-600 to-yellow-400 rounded-3xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                        <div class="relative aspect-[3/4] rounded-2xl overflow-hidden shadow-2xl border-4 border-white dark:border-gray-800">
                            <img src="<?php echo strip_tags(getContent($pageContent, 'registrar_profile', 'profile_image', 'https://vvu.edu.gh/images/2021/04/20/mr-ibrah-web.jpg')); ?>" 
                                 alt="<?php echo strip_tags(getContent($pageContent, 'registrar_profile', 'name', 'Albert Kweku Imbrah')); ?>" class="w-full h-full object-cover">
                        </div>
                        <div class="mt-8 text-center lg:text-left">
                            <h2 class="text-4xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags(getContent($pageContent, 'registrar_profile', 'name', 'Albert Kweku Imbrah')); ?></h2>
                            <p class="text-xl font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider"><?php echo strip_tags(getContent($pageContent, 'registrar_profile', 'title', 'Registrar')); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Profile Content -->
                <div class="w-full lg:w-2/3 space-y-10 animate-fadeInUp" style="animation-delay: 0.2s;">
                    <div>
                        <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(getContent($pageContent, 'registrar_profile', 'section_title', 'Profile & Background')); ?></h3>
                        <div class="h-2 w-24 bg-yellow-400 rounded-full mb-8"></div>
                        <div class="space-y-6 text-5xl sm:text-6xl font-bold text-gray-700 dark:text-gray-300 leading-relaxed">
                            <p>
                                <?php echo strip_tags(getContent($pageContent, 'registrar_profile', 'bio_paragraph_1', 'Albert Kweku Imbrah joined Valley View University on March 1, 2006, having been appointed to set up and run the University\'s Human Resource Department. His experience at Valley View University spans three administrations with each serving for a quinquennium.')); ?>
                            </p>
                            <p>
                                <?php echo strip_tags(getContent($pageContent, 'registrar_profile', 'bio_paragraph_2', 'With extensive experience of the workings of the administrative machinery of the contemporary tertiary academic landscape coupled with his strong leadership capabilities, the Registrar\'s collaborative vision is to engender an administrative apparatus that ensures the University becomes a leading centre of excellence for value-based Christian Education.')); ?>
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="p-8 bg-blue-50 dark:bg-blue-900/20 rounded-3xl border-l-8 border-blue-600">
                            <span class="material-symbols-outlined text-5xl text-blue-600 mb-4">school</span>
                            <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'registrar_profile', 'credentials_title', 'Academic Credentials')); ?></h4>
                            <p class="text-3xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags(getContent($pageContent, 'registrar_profile', 'credentials_text', 'MA in Human Resource Management, BA in Social Science, LLB from KNUST')); ?></p>
                        </div>
                        <div class="p-8 bg-yellow-50 dark:bg-yellow-900/20 rounded-3xl border-l-8 border-yellow-500">
                            <span class="material-symbols-outlined text-5xl text-yellow-500 mb-4">workspace_premium</span>
                            <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'registrar_profile', 'membership_title', 'Professional Membership')); ?></h4>
                            <p class="text-3xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags(getContent($pageContent, 'registrar_profile', 'membership_text', 'Member, Institute of Human Resource Practitioners, Ghana')); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Services Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(getContent($pageContent, 'our_services', 'section_title', 'Our Services')); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-4xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                    <?php echo strip_tags(getContent($pageContent, 'our_services', 'section_subtitle', 'The Office of the Registrar provides comprehensive administrative support for students, faculty, and staff throughout your academic journey.')); ?>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Service Card 1 -->
                <div class="service-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">verified_user</span>
                    </div>
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'our_services', 'service_1_title', 'Enrollment Verification')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'our_services', 'service_1_description', 'Official verification of student enrollment status for scholarships, insurance, loans, and other requirements.')); ?>
                    </p>
                </div>

                <!-- Service Card 2 -->
                <div class="service-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 rounded-2xl bg-yellow-500 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">description</span>
                    </div>
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'our_services', 'service_2_title', 'Transcripts & Records')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'our_services', 'service_2_description', 'Processing official transcripts, maintaining accurate academic records, and ensuring data privacy.')); ?>
                    </p>
                </div>

                <!-- Service Card 3 -->
                <div class="service-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 rounded-2xl bg-green-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">app_registration</span>
                    </div>
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'our_services', 'service_3_title', 'Course Registration')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'our_services', 'service_3_description', 'Guidance and support for course registration, add/drop procedures, and schedule changes.')); ?>
                    </p>
                </div>

                <!-- Service Card 4 -->
                <div class="service-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 rounded-2xl bg-purple-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">school</span>
                    </div>
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'our_services', 'service_4_title', 'Graduation Services')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'our_services', 'service_4_description', 'Diploma applications, degree audits, commencement information, and graduation clearance.')); ?>
                    </p>
                </div>

                <!-- Service Card 5 -->
                <div class="service-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 rounded-2xl bg-red-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">event_available</span>
                    </div>
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'our_services', 'service_5_title', 'Academic Calendar')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'our_services', 'service_5_description', 'Managing the university\'s academic timetable, exam schedules, and important deadlines.')); ?>
                    </p>
                </div>

                <!-- Service Card 6 -->
                <div class="service-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">folder_open</span>
                    </div>
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'our_services', 'service_6_title', 'Forms & Documents')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'our_services', 'service_6_description', 'Access to academic forms, policy documents, and essential university paperwork.')); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Links Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-16">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(getContent($pageContent, 'quick_links', 'section_title', 'Quick Links')); ?></h2>
                <p class="text-4xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags(getContent($pageContent, 'quick_links', 'section_description', 'Frequently accessed services and resources for your convenience.')); ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="<?php echo strip_tags(getContent($pageContent, 'quick_links', 'link_1_url', '#')); ?>" class="group p-8 bg-gray-50 dark:bg-gray-800 rounded-2xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all hover:-translate-y-2 border-2 border-transparent hover:border-blue-600">
                    <div class="text-blue-600 mb-4">
                        <span class="material-symbols-outlined text-5xl">description</span>
                    </div>
                    <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags(getContent($pageContent, 'quick_links', 'link_1_text', 'Request Transcript')); ?></h4>
                    <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags(getContent($pageContent, 'quick_links', 'link_1_sub', 'Order official transcripts')); ?></p>
                </a>

                <a href="<?php echo strip_tags(getContent($pageContent, 'quick_links', 'link_2_url', 'academic_calendar.php')); ?>" class="group p-8 bg-gray-50 dark:bg-gray-800 rounded-2xl hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-all hover:-translate-y-2 border-2 border-transparent hover:border-yellow-500">
                    <div class="text-yellow-500 mb-4">
                        <span class="material-symbols-outlined text-5xl">calendar_month</span>
                    </div>
                    <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags(getContent($pageContent, 'quick_links', 'link_2_text', 'Academic Calendar')); ?></h4>
                    <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags(getContent($pageContent, 'quick_links', 'link_2_sub', 'View important dates')); ?></p>
                </a>

                <a href="<?php echo strip_tags(getContent($pageContent, 'quick_links', 'link_3_url', '#')); ?>" class="group p-8 bg-gray-50 dark:bg-gray-800 rounded-2xl hover:bg-green-50 dark:hover:bg-green-900/20 transition-all hover:-translate-y-2 border-2 border-transparent hover:border-green-600">
                    <div class="text-green-600 mb-4">
                        <span class="material-symbols-outlined text-5xl">folder_shared</span>
                    </div>
                    <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags(getContent($pageContent, 'quick_links', 'link_3_text', 'Download Forms')); ?></h4>
                    <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags(getContent($pageContent, 'quick_links', 'link_3_sub', 'Access academic forms')); ?></p>
                </a>

                <a href="<?php echo strip_tags(getContent($pageContent, 'quick_links', 'link_4_url', '#')); ?>" class="group p-8 bg-gray-50 dark:bg-gray-800 rounded-2xl hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all hover:-translate-y-2 border-2 border-transparent hover:border-purple-600">
                    <div class="text-purple-600 mb-4">
                        <span class="material-symbols-outlined text-5xl">gavel</span>
                    </div>
                    <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags(getContent($pageContent, 'quick_links', 'link_4_text', 'Academic Policies')); ?></h4>
                    <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags(getContent($pageContent, 'quick_links', 'link_4_sub', 'View university policies')); ?></p>
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="glass p-12 rounded-[3rem] shadow-2xl border border-gray-100 dark:border-gray-800">
                <div class="text-center mb-12">
                    <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'section_title', 'Get in Touch')); ?></h3>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'section_description', 'We\'re here to assist you with all your academic needs.')); ?></p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center p-8 rounded-3xl bg-white dark:bg-gray-900 shadow-lg border border-gray-100 dark:border-gray-800">
                        <div class="w-16 h-16 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 mx-auto mb-6">
                            <span class="material-symbols-outlined text-4xl">mail</span>
                        </div>
                        <p class="text-lg font-black text-gray-400 uppercase tracking-widest mb-2">Email</p>
                        <a href="mailto:<?php echo strip_tags(getContent($pageContent, 'contact_section', 'email', 'registrar@vvu.edu.gh')); ?>" class="text-3xl font-bold text-gray-900 dark:text-white hover:text-blue-600 transition-colors"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'email', 'registrar@vvu.edu.gh')); ?></a>
                    </div>
                    <div class="text-center p-8 rounded-3xl bg-white dark:bg-gray-900 shadow-lg border border-gray-100 dark:border-gray-800">
                        <div class="w-16 h-16 rounded-2xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center text-yellow-600 mx-auto mb-6">
                            <span class="material-symbols-outlined text-4xl">call</span>
                        </div>
                        <p class="text-lg font-black text-gray-400 uppercase tracking-widest mb-2">Phone</p>
                        <a href="tel:<?php echo strip_tags(getContent($pageContent, 'contact_section', 'phone', '+233307051149')); ?>" class="text-3xl font-bold text-gray-900 dark:text-white hover:text-yellow-600 transition-colors"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'phone', '+233 (0) 307 051 149')); ?></a>
                    </div>
                    <div class="text-center p-8 rounded-3xl bg-white dark:bg-gray-900 shadow-lg border border-gray-100 dark:border-gray-800">
                        <div class="w-16 h-16 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 mx-auto mb-6">
                            <span class="material-symbols-outlined text-4xl">location_on</span>
                        </div>
                        <p class="text-lg font-black text-gray-400 uppercase tracking-widest mb-2">Location</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'office_location', 'Admin Block, Oyibi Campus')); ?></p>
                    </div>
                </div>

                <div class="mt-12 p-8 bg-blue-50 dark:bg-blue-900/20 rounded-3xl border-l-8 border-blue-600">
                    <div class="flex items-start gap-6">
                        <span class="material-symbols-outlined text-5xl text-blue-600 mt-1">schedule</span>
                        <div>
                            <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'hours_title', 'Office Hours')); ?></h4>
                            <p class="text-3xl text-gray-700 dark:text-gray-300 font-bold"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'hours_text', 'Monday - Friday: 8:00 AM - 5:00 PM')); ?></p>
                            <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium mt-2"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'hours_sub', 'Closed on weekends and public holidays')); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white mb-8 leading-tight tracking-tight">
                    <?php echo strip_tags(getContent($pageContent, 'cta_section', 'cta_title', 'Need Assistance?')); ?> <br><span class="text-yellow-400 text-6xl sm:text-7xl md:text-8xl lg:text-6xl block mt-2"><?php echo strip_tags(getContent($pageContent, 'cta_section', 'cta_highlight', 'We\'re Here to Help')); ?></span>
                </h2>
                <p class="text-2xl sm:text-3xl md:text-4xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags(getContent($pageContent, 'cta_section', 'cta_description', 'Have questions about registration, transcripts, or academic records? Contact the Office of the Registrar today.')); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_1_url', 'contact_us.php')); ?>" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">mail</span>
                        <?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_1_text', 'Contact Us')); ?>
                    </a>
                    <a href="<?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_2_url', 'apply.php')); ?>" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">how_to_reg</span>
                        <?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_2_text', 'Apply Now')); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>