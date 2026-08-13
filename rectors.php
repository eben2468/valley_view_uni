<?php
require_once 'includes/db_connect.php';
require_once 'includes/administration_content_helper.php';

// Initialize content helper
$content = new AdministrationContent($pdo);
$page = $content->getPageBySlug('rectors');

// Get all content sections
$pageContent = [];
if ($page) {
    $pageContent = $content->getPageContent($page['id']);
}

// Helper function to get field value
if (!function_exists('getContent')) {
    function getContent($sections, $section_key, $field_key, $default = '') {
        return isset($sections[$section_key]['fields'][$field_key]) ? $sections[$section_key]['fields'][$field_key] : $default;
    }
}

$page_title = $page ? $page['page_title'] . " - Valley View University" : "Campus Rectors - Valley View University";
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
    .rector-card {
        transition: all 0.3s ease;
    }
    .rector-card:hover {
        transform: translateY(-10px);
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
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags(getContent($pageContent, 'hero_section', 'badge_text', 'Campus Leadership')); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags(getContent($pageContent, 'hero_section', 'title_main', 'Campus')); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags(getContent($pageContent, 'hero_section', 'title_highlight', 'Leadership')); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags(getContent($pageContent, 'hero_section', 'subtitle', 'Leading with vision, integrity, and commitment to academic excellence across our three campuses. Meet the distinguished leaders shaping the future of Valley View University.')); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Introduction Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-16">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(getContent($pageContent, 'introduction', 'section_title', 'Our Campus Leadership Structure')); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-4xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                    <?php echo strip_tags(getContent($pageContent, 'introduction', 'section_description', 'Valley View University operates across three campuses. The main campus is led by the Vice-Chancellor and Pro Vice-Chancellor, while the Kumasi and Techiman campuses each have a Rector who serves as the chief academic and administrative officer, ensuring excellence in teaching, research, and community engagement.')); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Main Campus Leadership Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="text-center mb-16">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(getContent($pageContent, 'main_campus', 'section_title', 'Main Campus - Oyibi')); ?></h2>
                <div class="h-2 w-40 bg-purple-600 mx-auto rounded-full mb-8"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Vice-Chancellor Card -->
                <div class="rector-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border-2 border-purple-200 dark:border-purple-800">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-24 h-24 rounded-full bg-purple-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-6xl text-white">account_balance</span>
                        </div>
                        <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'main_campus', 'vc_title', 'Vice-Chancellor')); ?></h3>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-bold mb-6"><?php echo strip_tags(getContent($pageContent, 'main_campus', 'vc_subtitle', 'Chief Executive Officer of the University')); ?></p>
                        <div class="space-y-4 text-left w-full">
                            <div class="p-6 bg-purple-50 dark:bg-purple-900/20 rounded-2xl">
                                <p class="text-3xl text-gray-700 dark:text-gray-300 font-medium leading-relaxed">
                                    <?php echo strip_tags(getContent($pageContent, 'main_campus', 'vc_description', 'The Vice-Chancellor provides overall leadership and strategic direction for the entire university system.')); ?>
                                </p>
                            </div>
                            <a href="<?php echo strip_tags(getContent($pageContent, 'main_campus', 'vc_url', 'office_of_the_vice_chancellor.php')); ?>" class="flex items-center justify-center gap-3 px-8 py-4 bg-purple-600 hover:bg-purple-700 text-white text-2xl font-bold rounded-2xl transition-all transform hover:scale-105">
                                <span class="material-symbols-outlined text-3xl">info</span>
                                <?php echo strip_tags(getContent($pageContent, 'main_campus', 'vc_btn_text', 'Learn More')); ?>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Pro Vice-Chancellor Card -->
                <div class="rector-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border-2 border-indigo-200 dark:border-indigo-800">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-24 h-24 rounded-full bg-indigo-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-6xl text-white">co_present</span>
                        </div>
                        <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'main_campus', 'provc_title', 'Pro Vice-Chancellor')); ?></h3>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-bold mb-6"><?php echo strip_tags(getContent($pageContent, 'main_campus', 'provc_subtitle', 'Academic Leadership & Innovation')); ?></p>
                        <div class="space-y-4 text-left w-full">
                            <div class="p-6 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl">
                                <p class="text-3xl text-gray-700 dark:text-gray-300 font-medium leading-relaxed">
                                    <?php echo strip_tags(getContent($pageContent, 'main_campus', 'provc_description', 'The Pro Vice-Chancellor oversees academic excellence, digital transformation, and strategic development.')); ?>
                                </p>
                            </div>
                            <a href="<?php echo strip_tags(getContent($pageContent, 'main_campus', 'provc_url', 'office_of_the_pro-vice_chancellor.php')); ?>" class="flex items-center justify-center gap-3 px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white text-2xl font-bold rounded-2xl transition-all transform hover:scale-105">
                                <span class="material-symbols-outlined text-3xl">info</span>
                                <?php echo strip_tags(getContent($pageContent, 'main_campus', 'provc_btn_text', 'Learn More')); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Kumasi Campus Rector Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="flex flex-col lg:flex-row gap-16 items-center lg:items-start">
                <!-- Profile Image -->
                <div class="w-full lg:w-1/3 animate-fadeInUp">
                    <div class="relative group">
                        <div class="absolute -inset-4 bg-gradient-to-r from-blue-600 to-yellow-400 rounded-3xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                        <div class="relative aspect-[3/4] rounded-2xl overflow-hidden shadow-2xl border-4 border-white dark:border-gray-800">
                            <img src="<?php echo strip_tags(getContent($pageContent, 'kumasi_rector', 'profile_image', 'https://vvu.edu.gh/images/2021/04/21/dr-larkotey.jpg')); ?>" 
                                 alt="<?php echo strip_tags(getContent($pageContent, 'kumasi_rector', 'name', 'Winfred Ofoe Larkotey, PhD')); ?>" class="w-full h-full object-cover">
                        </div>
                        <div class="mt-8 text-center lg:text-left">
                            <h2 class="text-4xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags(getContent($pageContent, 'kumasi_rector', 'name', 'Winfred Ofoe Larkotey, PhD')); ?></h2>
                            <p class="text-xl font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider"><?php echo strip_tags(getContent($pageContent, 'kumasi_rector', 'title', 'Rector, Kumasi Campus')); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Profile Content -->
                <div class="w-full lg:w-2/3 space-y-10 animate-fadeInUp" style="animation-delay: 0.2s;">
                    <div>
                        <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(getContent($pageContent, 'kumasi_rector', 'section_title', 'Profile & Vision')); ?></h3>
                        <div class="h-2 w-24 bg-yellow-400 rounded-full mb-8"></div>
                        <div class="space-y-6 text-5xl sm:text-6xl font-bold text-gray-700 dark:text-gray-300 leading-relaxed">
                            <p>
                                <?php echo strip_tags(getContent($pageContent, 'kumasi_rector', 'bio_paragraph_1', 'Winfred Ofoe Larkotey, PhD, is an enthusiastic information systems specialist and a Senior Lecturer with nine years of experience in consulting and training young minds on the development and use of technology. He has been a faculty member with Valley View University since January 2012.')); ?>
                            </p>
                            <p>
                                <?php echo strip_tags(getContent($pageContent, 'kumasi_rector', 'bio_paragraph_2', 'Currently, Dr. Larkotey serves as the Rector of the Valley View University, Kumasi Campus, an appointment that started in February 2021. Previously, he served as Vice Rector for the Kumasi campus and Director of Information Technology Services.')); ?>
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="p-8 bg-blue-50 dark:bg-blue-900/20 rounded-3xl border-l-8 border-blue-600">
                            <span class="material-symbols-outlined text-5xl text-blue-600 mb-4">school</span>
                            <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'kumasi_rector', 'credentials_title', 'Academic Credentials')); ?></h4>
                            <p class="text-3xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags(getContent($pageContent, 'kumasi_rector', 'credentials_text', 'PhD in Information Systems, University of Ghana. BSc Computer Science from VVU')); ?></p>
                        </div>
                        <div class="p-8 bg-yellow-50 dark:bg-yellow-900/20 rounded-3xl border-l-8 border-yellow-500">
                            <span class="material-symbols-outlined text-5xl text-yellow-500 mb-4">interests</span>
                            <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'kumasi_rector', 'interests_title', 'Research Interests')); ?></h4>
                            <p class="text-3xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags(getContent($pageContent, 'kumasi_rector', 'interests_text', 'Digital Government, Mobile Platforms, Human-Computer Interaction')); ?></p>
                        </div>
                    </div>

                    <div class="p-8 bg-green-50 dark:bg-green-900/20 rounded-3xl border-l-8 border-green-600">
                        <div class="flex items-start gap-6">
                            <span class="material-symbols-outlined text-5xl text-green-600 mt-1">workspace_premium</span>
                            <div>
                                <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'kumasi_rector', 'membership_title', 'Professional Memberships')); ?></h4>
                                <ul class="space-y-3 text-3xl text-gray-700 dark:text-gray-300 font-bold">
                                    <li class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-green-600 text-3xl">check_circle</span>
                                        <?php echo strip_tags(getContent($pageContent, 'kumasi_rector', 'membership_1', 'Association of Information Systems')); ?>
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-green-600 text-3xl">check_circle</span>
                                        <?php echo strip_tags(getContent($pageContent, 'kumasi_rector', 'membership_2', 'United Kingdom Association of Information Systems')); ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Techiman Campus Rector Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="flex flex-col lg:flex-row-reverse gap-16 items-center lg:items-start">
                <!-- Profile Image -->
                <div class="w-full lg:w-1/3 animate-fadeInUp">
                    <div class="relative group">
                        <div class="absolute -inset-4 bg-gradient-to-r from-green-600 to-blue-400 rounded-3xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                        <div class="relative aspect-[3/4] rounded-2xl overflow-hidden shadow-2xl border-4 border-white dark:border-gray-800">
                            <img src="<?php echo strip_tags(getContent($pageContent, 'techiman_rector', 'profile_image', 'https://vvu.edu.gh/images/principal-officers/dr-emmanuel-bismarck-amponsah.jpg')); ?>" 
                                 alt="<?php echo strip_tags(getContent($pageContent, 'techiman_rector', 'name', 'Emmanuel B. Amponsah, PhD')); ?>" class="w-full h-full object-cover">
                        </div>
                        <div class="mt-8 text-center lg:text-right">
                            <h2 class="text-4xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags(getContent($pageContent, 'techiman_rector', 'name', 'Emmanuel B. Amponsah, PhD')); ?></h2>
                            <p class="text-xl font-bold text-green-600 dark:text-green-400 uppercase tracking-wider"><?php echo strip_tags(getContent($pageContent, 'techiman_rector', 'title', 'Rector, Techiman Campus')); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Profile Content -->
                <div class="w-full lg:w-2/3 space-y-10 animate-fadeInUp" style="animation-delay: 0.2s;">
                    <div>
                        <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(getContent($pageContent, 'techiman_rector', 'section_title', 'Profile & Achievements')); ?></h3>
                        <div class="h-2 w-24 bg-green-400 rounded-full mb-8"></div>
                        <div class="space-y-6 text-5xl sm:text-6xl font-bold text-gray-700 dark:text-gray-300 leading-relaxed">
                            <p>
                                <?php echo strip_tags(getContent($pageContent, 'techiman_rector', 'bio_paragraph_1', 'Emmanuel B. Amponsah (affectionately called EB) is an Associate Professor of Accounting who started teaching with the Ghana Education Service in 1986 and joined the Valley View University faculty in 2006. He has 9 enviable academic awards, bagging 5 of them on a single graduation day.')); ?>
                            </p>
                            <p>
                                <?php echo strip_tags(getContent($pageContent, 'techiman_rector', 'bio_paragraph_2', 'Prof. EB joined the University Administration on February 1, 2016, as the Acting Rector of the Kumasi Campus where he is now the Rector of Techiman Campus. He is a gifted resource person, meticulous moderator, and successful fundraiser who has netted hundreds of thousands of cedis in assets for the University.')); ?>
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="p-8 bg-green-50 dark:bg-green-900/20 rounded-3xl border-l-8 border-green-600">
                            <span class="material-symbols-outlined text-5xl text-green-600 mb-4">school</span>
                            <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'techiman_rector', 'credentials_title', 'Academic Credentials')); ?></h4>
                            <p class="text-3xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags(getContent($pageContent, 'techiman_rector', 'credentials_text', 'PhD in Business Administration, MPhil Accounting, BA Religion/Business Administration')); ?></p>
                        </div>
                        <div class="p-8 bg-purple-50 dark:bg-purple-900/20 rounded-3xl border-l-8 border-purple-600">
                            <span class="material-symbols-outlined text-5xl text-purple-600 mb-4">verified</span>
                            <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'techiman_rector', 'status_title', 'Professional Status')); ?></h4>
                            <p class="text-3xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags(getContent($pageContent, 'techiman_rector', 'status_text', 'Member, Chartered Institute of Management Accountants (UK & Ghana) since 2002')); ?></p>
                        </div>
                    </div>

                    <div class="p-8 bg-blue-50 dark:bg-blue-900/20 rounded-3xl border-l-8 border-blue-600">
                        <div class="flex items-start gap-6">
                            <span class="material-symbols-outlined text-5xl text-blue-600 mt-1">menu_book</span>
                            <div>
                                <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'techiman_rector', 'research_title', 'Research & Publications')); ?></h4>
                                <p class="text-3xl text-gray-700 dark:text-gray-300 font-bold mb-4"><?php echo strip_tags(getContent($pageContent, 'techiman_rector', 'research_stats', '26 Articles, 2 Books')); ?></p>
                                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags(getContent($pageContent, 'techiman_rector', 'research_specialization', 'Specializations: Accounting Ethics, Management Accounting, Financial Management, Strategic Management, Personal Finance, Higher Education')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Leadership Impact Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-16">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(getContent($pageContent, 'leadership_impact', 'section_title', 'Leadership Impact')); ?></h2>
                <p class="text-4xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags(getContent($pageContent, 'leadership_impact', 'section_subtitle', 'Our rectors drive excellence across multiple dimensions of university life.')); ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Impact Card 1 -->
                <div class="rector-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">school</span>
                    </div>
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'leadership_impact', 'impact_1_title', 'Academic Excellence')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'leadership_impact', 'impact_1_description', 'Maintaining high academic standards and fostering a culture of continuous improvement across all programs.')); ?>
                    </p>
                </div>

                <!-- Impact Card 2 -->
                <div class="rector-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 rounded-2xl bg-green-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">groups</span>
                    </div>
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'leadership_impact', 'impact_2_title', 'Community Building')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'leadership_impact', 'impact_2_description', 'Creating vibrant learning communities that support student success and faculty development.')); ?>
                    </p>
                </div>

                <!-- Impact Card 3 -->
                <div class="rector-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 rounded-2xl bg-yellow-500 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">lightbulb</span>
                    </div>
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'leadership_impact', 'impact_3_title', 'Innovation & Research')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'leadership_impact', 'impact_3_description', 'Promoting cutting-edge research and innovative teaching methodologies that prepare students for the future.')); ?>
                    </p>
                </div>

                <!-- Impact Card 4 -->
                <div class="rector-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 rounded-2xl bg-purple-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">handshake</span>
                    </div>
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'leadership_impact', 'impact_4_title', 'Strategic Partnerships')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'leadership_impact', 'impact_4_description', 'Building collaborations with industry, government, and international institutions to enhance opportunities.')); ?>
                    </p>
                </div>

                <!-- Impact Card 5 -->
                <div class="rector-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 rounded-2xl bg-red-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">trending_up</span>
                    </div>
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'leadership_impact', 'impact_5_title', 'Resource Development')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'leadership_impact', 'impact_5_description', 'Securing resources and infrastructure to support the university\'s mission and student success.')); ?>
                    </p>
                </div>

                <!-- Impact Card 6 -->
                <div class="rector-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">volunteer_activism</span>
                    </div>
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'leadership_impact', 'impact_6_title', 'Service Leadership')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'leadership_impact', 'impact_6_description', 'Exemplifying servant leadership and commitment to the university\'s Christian values and mission.')); ?>
                    </p>
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
                    <?php echo strip_tags(getContent($pageContent, 'cta_section', 'cta_title', 'Join Our Academic')); ?> <br><span class="text-yellow-400 text-6xl sm:text-7xl md:text-8xl lg:text-6xl block mt-2"><?php echo strip_tags(getContent($pageContent, 'cta_section', 'cta_highlight', 'Community Today')); ?></span>
                </h2>
                <p class="text-2xl sm:text-3xl md:text-4xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags(getContent($pageContent, 'cta_section', 'cta_description', 'Experience transformative education under visionary leadership at Valley View University\'s three campuses: Main Campus (Oyibi), Kumasi, and Techiman.')); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_1_url', 'https://admissions.vvu.edu.gh')); ?>" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">edit_note</span>
                        <?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_1_text', 'Apply Now')); ?>
                    </a>
                    <a href="<?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_2_url', 'contact_us.php')); ?>" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">info</span>
                        <?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_2_text', 'Learn More')); ?>
                    </a>
                </div>

                <div class="mt-20 grid grid-cols-1 sm:grid-cols-3 gap-12 border-t border-white/10 pt-16">
                    <div>
                        <div class="text-6xl font-black text-yellow-400 mb-2"><?php echo strip_tags(getContent($pageContent, 'cta_section', 'stat_1_val', '3')); ?></div>
                        <div class="text-blue-200 uppercase tracking-widest text-2xl font-black"><?php echo strip_tags(getContent($pageContent, 'cta_section', 'stat_1_label', 'Campuses')); ?></div>
                    </div>
                    <div>
                        <div class="text-6xl font-black text-yellow-400 mb-2"><?php echo strip_tags(getContent($pageContent, 'cta_section', 'stat_2_val', 'Excellence')); ?></div>
                        <div class="text-blue-200 uppercase tracking-widest text-2xl font-black"><?php echo strip_tags(getContent($pageContent, 'cta_section', 'stat_2_label', 'Driven Leadership')); ?></div>
                    </div>
                    <div>
                        <div class="text-6xl font-black text-yellow-400 mb-2"><?php echo strip_tags(getContent($pageContent, 'cta_section', 'stat_3_val', 'Vision')); ?></div>
                        <div class="text-blue-200 uppercase tracking-widest text-2xl font-black"><?php echo strip_tags(getContent($pageContent, 'cta_section', 'stat_3_label', 'For Tomorrow')); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>