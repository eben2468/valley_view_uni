<?php
require_once 'includes/db_connect.php';
require_once 'includes/administration_content_helper.php';

// Initialize content helper
$content = new AdministrationContent($pdo);
$page = $content->getPageBySlug('office_of_the_vice_chancellor');

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

// Set page title from database or use default
$page_title = $page ? $page['page_title'] . " - Valley View University" : "Office of the Vice Chancellor - Valley View University";
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
            <div class="max-w-8xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags(getContent($pageContent, 'hero_section', 'badge_text', 'University Leadership')); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags(getContent($pageContent, 'hero_section', 'title_main', 'Office of the')); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags(getContent($pageContent, 'hero_section', 'title_highlight', 'Vice Chancellor')); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags(getContent($pageContent, 'hero_section', 'subtitle', 'Leading Valley View University towards a future of academic excellence, spiritual growth, and societal impact through dedicated service and visionary leadership.')); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- VC Profile Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-8xl mx-auto">
                <div class="flex flex-col lg:flex-row gap-16 items-center lg:items-start">
                    <!-- Profile Image -->
                    <div class="w-full lg:w-1/3 animate-fadeInUp">
                        <div class="relative group">
                            <div class="absolute -inset-4 bg-gradient-to-r from-blue-600 to-yellow-400 rounded-3xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                            <div class="relative aspect-[3/4] rounded-2xl overflow-hidden shadow-2xl border-4 border-white dark:border-gray-800">
                                <img src="<?php echo strip_tags(getContent($pageContent, 'vc_profile', 'profile_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCLxwRQaStcMjctmdRSUlFrbTzHrHZ4QmQ7_w-SjNu0YbuDefwcI5HThsfCdLLv2t2buwPecrNFBE0YG9eouPiuXF_v0W_iZyuf-LqyyZDM_LGTDg50yAveRJO1xUoJWTArmE9HlG_NBbpBogj3YzigfkiFnlpHCvldhseWxVTj3HaJpdFaTDwR34NqL0UJmX8pZa6aANMldz55PZSL0ZrzavkAeMjQv_pYGZbL4ObyJK-1ZZU9mW2rBo-Z6I1hzq_bCvv7QBKpvQy0')); ?>" 
                                     alt="<?php echo strip_tags(getContent($pageContent, 'vc_profile', 'name', 'William Kofi Koomson, PhD')); ?>" class="w-full h-full object-cover">
                            </div>
                            <div class="mt-8 text-center lg:text-left">
                                <h2 class="text-5xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags(getContent($pageContent, 'vc_profile', 'name', 'William Kofi Koomson, PhD')); ?></h2>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider"><?php echo strip_tags(getContent($pageContent, 'vc_profile', 'title', 'Vice Chancellor')); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Content -->
                    <div class="w-full lg:w-2/3 space-y-10 animate-fadeInUp" style="animation-delay: 0.2s;">
                        <div>
                            <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(getContent($pageContent, 'vc_profile', 'section_title', 'Profile & Biography')); ?></h3>
                            <div class="h-2 w-24 bg-yellow-400 rounded-full mb-8"></div>
                            <div class="space-y-6 text-4xl sm:text-5xl font-bold text-gray-700 dark:text-gray-300 leading-relaxed">
                                <p>
                                    <?php echo strip_tags(getContent($pageContent, 'vc_profile', 'bio_paragraph_1', 'William Kofi Koomson, a Ghanaian by birth, lived and worked in the Americas, including Jamaica, Trinidad and Tobago, Canada, and the United States of America for the past 30 years, after acquiring his initial secondary education in Ghana. He is married with four adult children.')); ?>
                                </p>
                                <p>
                                    <?php echo strip_tags(getContent($pageContent, 'vc_profile', 'bio_paragraph_2', 'He has worked for the Seventh-day Adventist Church for the past 35 years as an Administrator and Departmental Director at the local Conference, Union and General Conference (Review and Herald Publishing Association) levels, Vice Principal for the Literature Ministry Seminary, University Professor, College Principal/Rector and Pastor.')); ?>
                                </p>
                                <p>
                                    <?php echo strip_tags(getContent($pageContent, 'vc_profile', 'bio_paragraph_3', 'As part of his evangelistic outreach, he spearheaded a Community Sharing Ministry program in the USA, Canada and Europe to distribute within seven years over 250,000 copies of the book, Steps to Christ. He led in establishing two Churches in North America and through his evangelistic efforts, more than 1510 souls have been baptized into the Seventh-day Adventist Church.')); ?>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="p-8 bg-blue-50 dark:bg-blue-900/20 rounded-3xl border-l-8 border-blue-600">
                                <span class="material-symbols-outlined text-6xl text-blue-600 mb-4">history_edu</span>
                                <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'vc_profile', 'experience_title', 'Experience')); ?></h4>
                                <p class="text-3xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags(getContent($pageContent, 'vc_profile', 'experience_text', '35+ Years of Service in the Seventh-day Adventist Church.')); ?></p>
                            </div>
                            <div class="p-8 bg-yellow-50 dark:bg-yellow-900/20 rounded-3xl border-l-8 border-yellow-500">
                                <span class="material-symbols-outlined text-6xl text-yellow-500 mb-4">public</span>
                                <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'vc_profile', 'impact_title', 'Global Impact')); ?></h4>
                                <p class="text-3xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags(getContent($pageContent, 'vc_profile', 'impact_text', 'Extensive international experience across the Americas and Europe.')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Strategic Vision Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-8xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(getContent($pageContent, 'strategic_vision', 'section_title', 'Strategic Vision')); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-4xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                    <?php echo strip_tags(getContent($pageContent, 'strategic_vision', 'section_description', 'Under the leadership of Dr. William Kofi Koomson, the Office of the Vice Chancellor is focused on four key pillars of transformation.')); ?>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Pillar 1 -->
                <div class="group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800 hover:-translate-y-2">
                    <div class="w-24 h-24 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">school</span>
                    </div>
                    <h4 class="text-5xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'strategic_vision', 'pillar_1_title', 'Academic Excellence')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'strategic_vision', 'pillar_1_description', 'Enhancing the quality of teaching and learning through innovative curricula and world-class faculty development.')); ?>
                    </p>
                </div>

                <!-- Pillar 2 -->
                <div class="group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800 hover:-translate-y-2">
                    <div class="w-24 h-24 rounded-2xl bg-yellow-500 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">biotech</span>
                    </div>
                    <h4 class="text-5xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'strategic_vision', 'pillar_2_title', 'Research & Innovation')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'strategic_vision', 'pillar_2_description', 'Fostering a culture of research that addresses local and global challenges through interdisciplinary collaboration.')); ?>
                    </p>
                </div>

                <!-- Pillar 3 -->
                <div class="group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800 hover:-translate-y-2">
                    <div class="w-24 h-24 rounded-2xl bg-green-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">groups</span>
                    </div>
                    <h4 class="text-5xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'strategic_vision', 'pillar_3_title', 'Community Engagement')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'strategic_vision', 'pillar_3_description', 'Strengthening our impact on society through meaningful service, outreach, and strategic partnerships.')); ?>
                    </p>
                </div>

                <!-- Pillar 4 -->
                <div class="group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800 hover:-translate-y-2">
                    <div class="w-24 h-24 rounded-2xl bg-purple-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">auto_awesome</span>
                    </div>
                    <h4 class="text-5xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'strategic_vision', 'pillar_4_title', 'Spiritual Growth')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'strategic_vision', 'pillar_4_description', 'Nurturing the spiritual well-being of our community, grounded in the values of the Seventh-day Adventist Church.')); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact & Appointment Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="glass p-12 rounded-[3rem] shadow-2xl border border-gray-100 dark:border-gray-800">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                        <div>
                            <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'section_title', 'Contact the Office')); ?></h3>
                            <p class="text-4xl text-gray-600 dark:text-gray-400 mb-10 font-medium">
                                <?php echo strip_tags(getContent($pageContent, 'contact_section', 'section_description', 'For official inquiries, scheduling, or administrative matters, please reach out to our dedicated team.')); ?>
                            </p>
                            <div class="space-y-8">
                                <div class="flex items-center gap-6">
                                    <div class="w-16 h-16 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600">
                                        <span class="material-symbols-outlined text-4xl">mail</span>
                                    </div>
                                    <div>
                                        <p class="text-lg font-black text-gray-400 uppercase tracking-widest">Email Address</p>
                                        <a href="mailto:<?php echo strip_tags(getContent($pageContent, 'contact_section', 'email', 'vc@vvu.edu.gh')); ?>" class="text-3xl font-bold text-gray-900 dark:text-white hover:text-blue-600 transition-colors"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'email', 'vc@vvu.edu.gh')); ?></a>
                                    </div>
                                </div>
                                <div class="flex items-center gap-6">
                                    <div class="w-16 h-16 rounded-2xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center text-yellow-600">
                                        <span class="material-symbols-outlined text-4xl">call</span>
                                    </div>
                                    <div>
                                        <p class="text-lg font-black text-gray-400 uppercase tracking-widest">Phone Number</p>
                                        <a href="tel:<?php echo strip_tags(getContent($pageContent, 'contact_section', 'phone', '+233302501101')); ?>" class="text-3xl font-bold text-gray-900 dark:text-white hover:text-yellow-600 transition-colors"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'phone', '+233 (0) 302 501 101')); ?></a>
                                    </div>
                                </div>
                                <div class="flex items-center gap-6">
                                    <div class="w-16 h-16 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600">
                                        <span class="material-symbols-outlined text-4xl">location_on</span>
                                    </div>
                                    <div>
                                        <p class="text-lg font-black text-gray-400 uppercase tracking-widest">Office Location</p>
                                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'office_location', 'Administration Block, Oyibi Campus')); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-10 rounded-[2rem] border border-gray-100 dark:border-gray-700">
                            <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'form_title', 'Request an Appointment')); ?></h4>
                            <p class="text-xl text-gray-600 dark:text-gray-400 mb-8 font-medium">
                                <?php echo strip_tags(getContent($pageContent, 'contact_section', 'form_description', 'To schedule a meeting with the Vice Chancellor, please provide your details and the purpose of your visit.')); ?>
                            </p>
                            <form class="space-y-6">
                                <div>
                                    <input type="text" placeholder="Your Full Name" class="w-full px-6 py-4 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-600 outline-none text-xl">
                                </div>
                                <div>
                                    <input type="email" placeholder="Email Address" class="w-full px-6 py-4 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-600 outline-none text-xl">
                                </div>
                                <div>
                                    <textarea rows="4" placeholder="Purpose of Appointment" class="w-full px-6 py-4 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-600 outline-none text-xl"></textarea>
                                </div>
                                <button type="submit" class="w-full py-5 bg-blue-600 hover:bg-blue-700 text-white text-2xl font-black rounded-2xl transition-all shadow-lg hover:shadow-blue-500/25">
                                    <?php echo strip_tags(getContent($pageContent, 'contact_section', 'form_btn_text', 'Submit Request')); ?>
                                </button>
                            </form>
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
        
        <div class="container relative z-10">
            <div class="max-w-7xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white mb-8 leading-tight tracking-tight">
                    <?php echo strip_tags(getContent($pageContent, 'cta_section', 'cta_title', 'Building a Legacy of')); ?> <br><span class="text-yellow-400 text-6xl sm:text-7xl md:text-8xl lg:text-6xl block mt-2"><?php echo strip_tags(getContent($pageContent, 'cta_section', 'cta_highlight', 'Excellence Together')); ?></span>
                </h2>
                <p class="text-2xl sm:text-3xl md:text-4xl text-blue-100 mb-12 max-w-6xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags(getContent($pageContent, 'cta_section', 'cta_description', 'Join us in our mission to transform lives through quality Christian education and dedicated service to humanity.')); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_1_url', 'about_us.php')); ?>" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">info</span>
                        <?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_1_text', 'About the University')); ?>
                    </a>
                    <a href="<?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_2_url', 'contact_us.php')); ?>" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">mail</span>
                        <?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_2_text', 'Get in Touch')); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>