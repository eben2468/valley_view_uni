<?php
require_once 'includes/db_connect.php';
require_once 'includes/administration_content_helper.php';

// Initialize content helper
$content = new AdministrationContent($pdo);
$page = $content->getPageBySlug('office_of_the_pro-vice_chancellor');

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

$page_title = $page ? $page['page_title'] . " - Valley View University" : "Office of the Pro-Vice Chancellor - Valley View University";
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
    .text-gradient {
        background: linear-gradient(to right, #2563eb, #fbbf24);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .profile-card {
        transition: all 0.3s ease;
    }
    .profile-card:hover {
        transform: translateY(-5px);
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags(getContent($pageContent, 'hero_section', 'background_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE')); ?>" 
                 alt="VVU Campus" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags(getContent($pageContent, 'hero_section', 'badge_text', 'Academic Leadership')); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags(getContent($pageContent, 'hero_section', 'title_main', 'Office of the')); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags(getContent($pageContent, 'hero_section', 'title_highlight', 'Pro-Vice Chancellor')); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags(getContent($pageContent, 'hero_section', 'subtitle', 'Empowering academic excellence, fostering innovation, and driving digital transformation to shape the future of Valley View University.')); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Pro-VC Profile Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
                <div class="flex flex-col lg:flex-row gap-16 items-center lg:items-start">
                    <!-- Profile Image -->
                    <div class="w-full lg:w-1/3 animate-fadeInUp">
                        <div class="relative group">
                            <div class="absolute -inset-4 bg-gradient-to-r from-blue-600 to-yellow-400 rounded-3xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                            <div class="relative aspect-[3/4] rounded-2xl overflow-hidden shadow-2xl border-4 border-white dark:border-gray-800">
                                <img src="<?php echo strip_tags(getContent($pageContent, 'provc_profile', 'profile_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuA3DYOu722UbeqMnt5A6z1RSC3ZJL7ObJOF_ymftJttcbb5hu5KPxUDwEWQ1YnJlEH67SXOPJLcgHfK6yLx9gBSAua8CWI_F6jNO2wY-e7O34KnmgWRDReSfhRVWn52zOTyEdtoE2cGzFfFu9sNA1Dh-aJLxeJGilTtSnsSi8a9Y43daV1pkjPRFDI5UuJzqGSbsFQFsvwFGALUyQptWXtxWsDY-4eLAiFyVJgje0T_UrdsWG0iKcP-FCYMHijjKe-1x5gwT5xhNjWk')); ?>" 
                                     alt="<?php echo strip_tags(getContent($pageContent, 'provc_profile', 'name', 'Prof. Winfred Ofoe Larkotey')); ?>" class="w-full h-full object-cover">
                            </div>
                            <div class="mt-8 text-center lg:text-left">
                                <h2 class="text-4xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags(getContent($pageContent, 'provc_profile', 'name', 'Prof. Winfred Ofoe Larkotey')); ?></h2>
                                <p class="text-xl font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider"><?php echo strip_tags(getContent($pageContent, 'provc_profile', 'title', 'Pro-Vice Chancellor')); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Content -->
                    <div class="w-full lg:w-2/3 space-y-10 animate-fadeInUp" style="animation-delay: 0.2s;">
                        <div>
                            <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(getContent($pageContent, 'provc_profile', 'section_title', 'Profile & Vision')); ?></h3>
                            <div class="h-2 w-24 bg-yellow-400 rounded-full mb-8"></div>
                            <div class="space-y-6 text-5xl sm:text-6xl font-bold text-gray-700 dark:text-gray-300 leading-relaxed">
                                <p>
                                    <?php echo strip_tags(getContent($pageContent, 'provc_profile', 'bio_paragraph_1', 'Prof. Winfred Ofoe Larkotey, an accomplished academic and visionary leader, embodies a rare blend of academic excellence, innovative thinking, and administrative prowess. With a robust background in Information Systems coupled with extensive experience in higher education administration, Prof. Larkotey has made significant contributions to academia, research, and institutional development.')); ?>
                                </p>
                                <p>
                                    <?php echo strip_tags(getContent($pageContent, 'provc_profile', 'bio_paragraph_2', 'His exemplary journey epitomizes academic excellence, leadership, and a steadfast commitment to driving positive change. As he continues to inspire and empower the next generation of scholars and leaders, his impact resonates far beyond the confines of academia, shaping the future of technology and education.')); ?>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="p-8 bg-blue-50 dark:bg-blue-900/20 rounded-3xl border-l-8 border-blue-600">
                                <span class="material-symbols-outlined text-5xl text-blue-600 mb-4">school</span>
                                <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'provc_profile', 'academic_journey_title', 'Academic Journey')); ?></h4>
                                <p class="text-3xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags(getContent($pageContent, 'provc_profile', 'academic_journey_text', 'PhD in Information Systems from the University of Ghana, Legon.')); ?></p>
                            </div>
                            <div class="p-8 bg-yellow-50 dark:bg-yellow-900/20 rounded-3xl border-l-8 border-yellow-500">
                                <span class="material-symbols-outlined text-5xl text-yellow-500 mb-4">terminal</span>
                                <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'provc_profile', 'tech_expertise_title', 'Tech Expertise')); ?></h4>
                                <p class="text-3xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags(getContent($pageContent, 'provc_profile', 'tech_expertise_text', 'Specialist in Software Development, Fintech, and Digital Transformation.')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </section>

    <!-- Career & Leadership Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
                <div class="max-w-4xl mx-auto text-center mb-20">
                    <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(getContent($pageContent, 'career_leadership', 'section_title', 'Career & Leadership')); ?></h2>
                    <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                    <p class="text-4xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'career_leadership', 'section_subtitle', 'A journey of excellence from software engineering to university leadership.')); ?>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <!-- Professional Experience -->
                    <div class="glass p-10 rounded-3xl shadow-xl">
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-8 flex items-center gap-4">
                            <span class="material-symbols-outlined text-blue-600 text-5xl">work</span>
                            <?php echo strip_tags(getContent($pageContent, 'career_leadership', 'experience_title', 'Professional Experience')); ?>
                        </h3>
                        <ul class="space-y-8">
                            <li class="flex gap-6">
                                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                                    <span class="w-4 h-4 rounded-full bg-blue-600"></span>
                                </div>
                                <div>
                                    <h4 class="text-2xl font-black text-gray-900 dark:text-white"><?php echo strip_tags(getContent($pageContent, 'career_leadership', 'exp_1_title', 'SG Bank - Ghana')); ?></h4>
                                    <p class="text-2xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags(getContent($pageContent, 'career_leadership', 'exp_1_text', 'Software and Systems Developer for the AKOBEN Project.')); ?></p>
                                </div>
                            </li>
                            <li class="flex gap-6">
                                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                                    <span class="w-4 h-4 rounded-full bg-blue-600"></span>
                                </div>
                                <div>
                                    <h4 class="text-2xl font-black text-gray-900 dark:text-white"><?php echo strip_tags(getContent($pageContent, 'career_leadership', 'exp_2_title', 'Ministry of Finance')); ?></h4>
                                    <p class="text-2xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags(getContent($pageContent, 'career_leadership', 'exp_2_text', 'Budget and Public Expenditure Management Systems (BPEMS).')); ?></p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Leadership Roles -->
                    <div class="glass p-10 rounded-3xl shadow-xl">
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-8 flex items-center gap-4">
                            <span class="material-symbols-outlined text-yellow-500 text-5xl">leaderboard</span>
                            <?php echo strip_tags(getContent($pageContent, 'career_leadership', 'leadership_title', 'Leadership Roles')); ?>
                        </h3>
                        <ul class="space-y-8">
                            <li class="flex gap-6">
                                <div class="w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center shrink-0">
                                    <span class="w-4 h-4 rounded-full bg-yellow-500"></span>
                                </div>
                                <div>
                                    <h4 class="text-2xl font-black text-gray-900 dark:text-white"><?php echo strip_tags(getContent($pageContent, 'career_leadership', 'role_1_title', 'Rector, Kumasi Campus')); ?></h4>
                                    <p class="text-2xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags(getContent($pageContent, 'career_leadership', 'role_1_text', 'Steered the academic division with strategic vision (2021-2023).')); ?></p>
                                </div>
                            </li>
                            <li class="flex gap-6">
                                <div class="w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center shrink-0">
                                    <span class="w-4 h-4 rounded-full bg-yellow-500"></span>
                                </div>
                                <div>
                                    <h4 class="text-2xl font-black text-gray-900 dark:text-white"><?php echo strip_tags(getContent($pageContent, 'career_leadership', 'role_2_title', 'Director, IT Services')); ?></h4>
                                    <p class="text-2xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags(getContent($pageContent, 'career_leadership', 'role_2_text', 'Driving digital transformation across the university landscape.')); ?></p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
        </div>
    </section>

    <!-- Research & Contributions Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
                <div class="flex flex-col lg:flex-row gap-16 items-center">
                    <div class="w-full lg:w-1/2 space-y-8">
                        <h2 class="text-5xl font-black text-gray-900 dark:text-white"><?php echo strip_tags(getContent($pageContent, 'research_contributions', 'section_title', 'Research & Academic Contributions')); ?></h2>
                        <div class="h-2 w-24 bg-green-600 rounded-full"></div>
                        <p class="text-4xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                            <?php echo strip_tags(getContent($pageContent, 'research_contributions', 'section_description', 'A prolific researcher with interests spanning digitalization, human-computer interaction, and fintech.')); ?>
                        </p>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4 p-6 bg-green-50 dark:bg-green-900/20 rounded-2xl border-l-4 border-green-600">
                                <span class="material-symbols-outlined text-green-600 text-4xl">verified</span>
                                <span class="text-3xl font-bold text-gray-800 dark:text-gray-200"><?php echo strip_tags(getContent($pageContent, 'research_contributions', 'membership_1', 'Member, Association of Information Systems')); ?></span>
                            </div>
                            <div class="flex items-center gap-4 p-6 bg-green-50 dark:bg-green-900/20 rounded-2xl border-l-4 border-green-600">
                                <span class="material-symbols-outlined text-green-600 text-4xl">verified</span>
                                <span class="text-3xl font-bold text-gray-800 dark:text-gray-200"><?php echo strip_tags(getContent($pageContent, 'research_contributions', 'membership_2', 'Member, UK Association of Information Systems')); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="w-full lg:w-1/2 grid grid-cols-2 gap-6">
                        <div class="p-8 bg-gray-50 dark:bg-gray-800 rounded-3xl text-center profile-card shadow-lg">
                            <span class="material-symbols-outlined text-6xl text-blue-600 mb-4">payments</span>
                            <h4 class="text-2xl font-black text-gray-900 dark:text-white"><?php echo strip_tags(getContent($pageContent, 'research_contributions', 'interest_1', 'Fintech')); ?></h4>
                        </div>
                        <div class="p-8 bg-gray-50 dark:bg-gray-800 rounded-3xl text-center profile-card shadow-lg">
                            <span class="material-symbols-outlined text-6xl text-yellow-500 mb-4">devices</span>
                            <h4 class="text-2xl font-black text-gray-900 dark:text-white"><?php echo strip_tags(getContent($pageContent, 'research_contributions', 'interest_2', 'HCI')); ?></h4>
                        </div>
                        <div class="p-8 bg-gray-50 dark:bg-gray-800 rounded-3xl text-center profile-card shadow-lg">
                            <span class="material-symbols-outlined text-6xl text-green-600 mb-4">smartphone</span>
                            <h4 class="text-2xl font-black text-gray-900 dark:text-white"><?php echo strip_tags(getContent($pageContent, 'research_contributions', 'interest_3', 'Mobile Apps')); ?></h4>
                        </div>
                        <div class="p-8 bg-gray-50 dark:bg-gray-800 rounded-3xl text-center profile-card shadow-lg">
                            <span class="material-symbols-outlined text-6xl text-purple-600 mb-4">cloud</span>
                            <h4 class="text-2xl font-black text-gray-900 dark:text-white"><?php echo strip_tags(getContent($pageContent, 'research_contributions', 'interest_4', 'Digitalization')); ?></h4>
                        </div>
                    </div>
                </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
                <div class="glass p-12 rounded-[3rem] shadow-2xl border border-gray-100 dark:border-gray-800">
                    <div class="text-center mb-12">
                        <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'section_title', 'Get in Touch')); ?></h3>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'section_description', 'For academic inquiries and administrative matters.')); ?></p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="text-center p-8 rounded-3xl bg-white dark:bg-gray-900 shadow-lg border border-gray-100 dark:border-gray-800">
                            <div class="w-16 h-16 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 mx-auto mb-6">
                                <span class="material-symbols-outlined text-4xl">mail</span>
                            </div>
                            <p class="text-lg font-black text-gray-400 uppercase tracking-widest mb-2">Email</p>
                            <a href="mailto:<?php echo strip_tags(getContent($pageContent, 'contact_section', 'email', 'provc@vvu.edu.gh')); ?>" class="text-3xl font-bold text-gray-900 dark:text-white hover:text-blue-600 transition-colors"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'email', 'provc@vvu.edu.gh')); ?></a>
                        </div>
                        <div class="text-center p-8 rounded-3xl bg-white dark:bg-gray-900 shadow-lg border border-gray-100 dark:border-gray-800">
                            <div class="w-16 h-16 rounded-2xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center text-yellow-600 mx-auto mb-6">
                                <span class="material-symbols-outlined text-4xl">call</span>
                            </div>
                            <p class="text-lg font-black text-gray-400 uppercase tracking-widest mb-2">Phone</p>
                            <a href="tel:<?php echo strip_tags(getContent($pageContent, 'contact_section', 'phone', '+233302501101')); ?>" class="text-3xl font-bold text-gray-900 dark:text-white hover:text-yellow-600 transition-colors"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'phone', '+233 (0) 302 501 101')); ?></a>
                        </div>
                        <div class="text-center p-8 rounded-3xl bg-white dark:bg-gray-900 shadow-lg border border-gray-100 dark:border-gray-800">
                            <div class="w-16 h-16 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 mx-auto mb-6">
                                <span class="material-symbols-outlined text-4xl">location_on</span>
                            </div>
                            <p class="text-lg font-black text-gray-400 uppercase tracking-widest mb-2">Location</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'office_location', 'Admin Block, Oyibi Campus')); ?></p>
                        </div>
                    </div>
                </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-white mb-8 leading-tight tracking-tight">
                    <?php echo strip_tags(getContent($pageContent, 'cta_section', 'cta_title', 'Join Our Academic')); ?> <br><span class="text-yellow-400"><?php echo strip_tags(getContent($pageContent, 'cta_section', 'cta_highlight', 'Community Today')); ?></span>
                </h2>
                <p class="text-4xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags(getContent($pageContent, 'cta_section', 'cta_description', 'Experience a transformative education grounded in Christian values and academic excellence.')); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_1_url', 'https://admissions.vvu.edu.gh')); ?>" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">edit_note</span>
                        <?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_1_text', 'Apply Now')); ?>
                    </a>
                    <a href="<?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_2_url', 'contact_us.php')); ?>" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">info</span>
                        <?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_2_text', 'Request Info')); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>