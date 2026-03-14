<?php
require_once 'includes/db_connect.php';
require_once 'includes/administration_content_helper.php';

// Initialize content helper
$content = new AdministrationContent($pdo);
$page = $content->getPageBySlug('office_of_the_cfo');

// Get all content sections
$pageContent = [];
if ($page) {
    $pageContent = $content->getPageContent($page['id']);
}

// Helper function to get field value with HTML cleaning
if (!function_exists('getContent')) {
    function getContent($sections, $section_key, $field_key, $default = '') {
        $value = isset($sections[$section_key]['fields'][$field_key]) ? $sections[$section_key]['fields'][$field_key] : $default;
        return AdministrationContent::cleanHtml($value);
    }
}

$page_title = $page ? $page['page_title'] . " - Valley View University" : "Office of the Chief Finance Officer - Valley View University";
$active_page = "about";
include 'includes/header.php';
?>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slowZoom {
        0% { transform: scale(1); }
        100% { transform: scale(1.1); }
    }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
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
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags(getContent($pageContent, 'hero_section', 'background_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE')); ?>" 
                 alt="VVU Campus" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags(getContent($pageContent, 'hero_section', 'badge_text', 'Financial Leadership')); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags(getContent($pageContent, 'hero_section', 'title_main', 'Office of the')); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags(getContent($pageContent, 'hero_section', 'title_highlight', 'Chief Finance Officer')); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags(getContent($pageContent, 'hero_section', 'subtitle', 'Ensuring financial sustainability and strategic resource management to support the university\'s mission of academic excellence and institutional growth.')); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- CFO Profile Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-8xl mx-auto">
                <div class="flex flex-col lg:flex-row gap-16 items-center lg:items-start">
                    <!-- Profile Image -->
                    <div class="w-full lg:w-1/3 animate-fadeInUp">
                        <div class="relative group">
                            <div class="absolute -inset-4 bg-gradient-to-r from-blue-600 to-yellow-400 rounded-3xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                            <div class="relative aspect-[3/4] rounded-2xl overflow-hidden shadow-2xl border-4 border-white dark:border-gray-800">
                                <img src="<?php echo strip_tags(getContent($pageContent, 'cfo_profile', 'profile_image', 'https://via.placeholder.com/400x500/4680ff/ffffff?text=CFO')); ?>" 
                                     alt="<?php echo strip_tags(getContent($pageContent, 'cfo_profile', 'name', 'Chief Finance Officer')); ?>" class="w-full h-full object-cover">
                            </div>
                            <div class="mt-8 text-center lg:text-left">
                                <h2 class="text-5xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags(getContent($pageContent, 'cfo_profile', 'name', 'Chief Finance Officer')); ?></h2>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider"><?php echo strip_tags(getContent($pageContent, 'cfo_profile', 'title', 'Chief Finance Officer')); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Content -->
                    <div class="w-full lg:w-2/3 space-y-10 animate-fadeInUp" style="animation-delay: 0.2s;">
                        <div>
                            <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(getContent($pageContent, 'cfo_profile', 'section_title', 'Profile & Expertise')); ?></h3>
                            <div class="h-2 w-24 bg-yellow-400 rounded-full mb-8"></div>
                            <div class="space-y-6 text-4xl sm:text-5xl font-bold text-gray-700 dark:text-gray-300 leading-relaxed">
                                <p>
                                    <?php echo strip_tags(getContent($pageContent, 'cfo_profile', 'bio_paragraph_1', 'The Chief Finance Officer oversees all financial operations of Valley View University, ensuring fiscal responsibility, transparency, and strategic resource allocation to support the institution\'s academic and operational goals.')); ?>
                                </p>
                                <p>
                                    <?php echo strip_tags(getContent($pageContent, 'cfo_profile', 'bio_paragraph_2', 'With extensive experience in financial management and higher education administration, the CFO leads initiatives in budget planning, financial reporting, investment management, and compliance with regulatory requirements.')); ?>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="p-8 bg-blue-50 dark:bg-blue-900/20 rounded-3xl border-l-8 border-blue-600">
                                <span class="material-symbols-outlined text-6xl text-blue-600 mb-4">account_balance</span>
                                <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'cfo_profile', 'experience_title', 'Financial Expertise')); ?></h4>
                                <p class="text-3xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags(getContent($pageContent, 'cfo_profile', 'experience_text', 'Strategic financial planning and institutional resource management.')); ?></p>
                            </div>
                            <div class="p-8 bg-yellow-50 dark:bg-yellow-900/20 rounded-3xl border-l-8 border-yellow-500">
                                <span class="material-symbols-outlined text-6xl text-yellow-500 mb-4">verified</span>
                                <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'cfo_profile', 'impact_title', 'Fiscal Stewardship')); ?></h4>
                                <p class="text-3xl text-gray-600 dark:text-gray-400 font-bold"><?php echo strip_tags(getContent($pageContent, 'cfo_profile', 'impact_text', 'Ensuring financial sustainability and accountability.')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Financial Management Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-8xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(getContent($pageContent, 'financial_vision', 'section_title', 'Financial Management Pillars')); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-4xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                    <?php echo strip_tags(getContent($pageContent, 'financial_vision', 'section_description', 'Our financial strategy is built on transparency, sustainability, and strategic investment in the university\'s future.')); ?>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800 hover:-translate-y-2">
                    <div class="w-24 h-24 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">account_balance_wallet</span>
                    </div>
                    <h4 class="text-5xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'financial_vision', 'pillar_1_title', 'Budget Management')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'financial_vision', 'pillar_1_description', 'Strategic allocation of resources to support academic programs, infrastructure development, and operational excellence.')); ?>
                    </p>
                </div>

                <div class="group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800 hover:-translate-y-2">
                    <div class="w-24 h-24 rounded-2xl bg-yellow-500 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">description</span>
                    </div>
                    <h4 class="text-5xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'financial_vision', 'pillar_2_title', 'Financial Reporting')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'financial_vision', 'pillar_2_description', 'Maintaining transparent and accurate financial records that meet international accounting standards and regulatory requirements.')); ?>
                    </p>
                </div>

                <div class="group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800 hover:-translate-y-2">
                    <div class="w-24 h-24 rounded-2xl bg-green-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">trending_up</span>
                    </div>
                    <h4 class="text-5xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'financial_vision', 'pillar_3_title', 'Investment Strategy')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'financial_vision', 'pillar_3_description', 'Prudent investment of university funds to generate sustainable returns and support long-term institutional growth.')); ?>
                    </p>
                </div>

                <div class="group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800 hover:-translate-y-2">
                    <div class="w-24 h-24 rounded-2xl bg-purple-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">shield</span>
                    </div>
                    <h4 class="text-5xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'financial_vision', 'pillar_4_title', 'Risk Management')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'financial_vision', 'pillar_4_description', 'Implementing robust financial controls and risk mitigation strategies to protect university assets and ensure compliance.')); ?>
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
                                <?php echo strip_tags(getContent($pageContent, 'contact_section', 'section_description', 'For financial inquiries, budget matters, or administrative questions, please reach out to our finance team.')); ?>
                            </p>
                            <div class="space-y-8">
                                <div class="flex items-center gap-6">
                                    <div class="w-16 h-16 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600">
                                        <span class="material-symbols-outlined text-4xl">mail</span>
                                    </div>
                                    <div>
                                        <p class="text-lg font-black text-gray-400 uppercase tracking-widest">Email Address</p>
                                        <a href="mailto:<?php echo strip_tags(getContent($pageContent, 'contact_section', 'email', 'cfo@vvu.edu.gh')); ?>" class="text-3xl font-bold text-gray-900 dark:text-white hover:text-blue-600 transition-colors"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'email', 'cfo@vvu.edu.gh')); ?></a>
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
                                        <p class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'office_location', 'Finance Office, Administration Block')); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-10 rounded-[2rem] border border-gray-100 dark:border-gray-700">
                            <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(getContent($pageContent, 'contact_section', 'form_title', 'Financial Inquiry')); ?></h4>
                            <p class="text-xl text-gray-600 dark:text-gray-400 mb-8 font-medium">
                                <?php echo strip_tags(getContent($pageContent, 'contact_section', 'form_description', 'Submit your financial questions or requests for information.')); ?>
                            </p>
                            <form class="space-y-6">
                                <div>
                                    <input type="text" placeholder="Your Full Name" class="w-full px-6 py-4 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-600 outline-none text-xl">
                                </div>
                                <div>
                                    <input type="email" placeholder="Email Address" class="w-full px-6 py-4 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-600 outline-none text-xl">
                                </div>
                                <div>
                                    <textarea rows="4" placeholder="Your Inquiry" class="w-full px-6 py-4 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-600 outline-none text-xl"></textarea>
                                </div>
                                <button type="submit" class="w-full py-5 bg-blue-600 hover:bg-blue-700 text-white text-2xl font-black rounded-2xl transition-all shadow-lg hover:shadow-blue-500/25">
                                    <?php echo strip_tags(getContent($pageContent, 'contact_section', 'form_btn_text', 'Submit Inquiry')); ?>
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
                <h2 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black text-white mb-8 leading-tight tracking-tight">
                    <?php echo strip_tags(getContent($pageContent, 'cta_section', 'cta_title', 'Financial Transparency &')); ?> <br><span class="text-yellow-400 text-5xl sm:text-6xl md:text-7xl lg:text-5xl block mt-2"><?php echo strip_tags(getContent($pageContent, 'cta_section', 'cta_highlight', 'Accountability')); ?></span>
                </h2>
                <p class="text-lg sm:text-xl md:text-2xl text-blue-100 mb-12 max-w-6xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags(getContent($pageContent, 'cta_section', 'cta_description', 'Committed to responsible stewardship of university resources for the benefit of our students and community.')); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_1_url', '#')); ?>" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">description</span>
                        <?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_1_text', 'Financial Reports')); ?>
                    </a>
                    <a href="<?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_2_url', 'contact_us.php')); ?>" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">mail</span>
                        <?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_2_text', 'Contact Finance')); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
