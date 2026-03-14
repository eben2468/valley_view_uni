<?php
require_once 'includes/db_connect.php';
require_once 'includes/administration_content_helper.php';

// Initialize content helper
$content = new AdministrationContent($pdo);
$page = $content->getPageBySlug('recorders');

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

$page_title = $page ? $page['page_title'] . " - Valley View University" : "University Recorders - Valley View University";
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
    .document-card {
        transition: all 0.3s ease;
    }
    .document-card:hover {
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
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags(getContent($pageContent, 'hero_section', 'badge_text', 'Official Records')); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags(getContent($pageContent, 'hero_section', 'title_main', 'University')); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags(getContent($pageContent, 'hero_section', 'title_highlight', 'Recorders')); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags(getContent($pageContent, 'hero_section', 'subtitle', 'Official documentation of university decisions, policies, and administrative actions. Your gateway to institutional transparency and governance records.')); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Introduction Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-16">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(getContent($pageContent, 'introduction', 'section_title', 'What Are University Recorders?')); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-4xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                    <?php echo strip_tags(getContent($pageContent, 'introduction', 'section_description', 'University Recorders are official documents that record important decisions, policies, appointments, and administrative actions taken by Valley View University. These documents serve as the institutional memory and provide transparency in university governance.')); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- What's Included Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(getContent($pageContent, 'whats_included', 'section_title', 'What\'s Included')); ?></h2>
                <p class="text-4xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                    <?php echo strip_tags(getContent($pageContent, 'whats_included', 'section_subtitle', 'University Recorders document the following types of institutional actions and decisions:')); ?>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="document-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">person_add</span>
                    </div>
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'whats_included', 'item_1_title', 'Appointments & Promotions')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'whats_included', 'item_1_description', 'Faculty and staff appointments, promotions, and position changes within the university.')); ?>
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="document-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 rounded-2xl bg-yellow-500 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">gavel</span>
                    </div>
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'whats_included', 'item_2_title', 'Policy Decisions')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'whats_included', 'item_2_description', 'University policies, regulations, and disciplinary actions taken by the administration.')); ?>
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="document-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 rounded-2xl bg-green-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">groups</span>
                    </div>
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'whats_included', 'item_3_title', 'Committee Reports')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'whats_included', 'item_3_description', 'Reports from university committees on scholarships, business ventures, and strategic initiatives.')); ?>
                    </p>
                </div>

                <!-- Card 4 -->
                <div class="document-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 rounded-2xl bg-purple-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">school</span>
                    </div>
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'whats_included', 'item_4_title', 'Academic Events')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'whats_included', 'item_4_description', 'Records of matriculation ceremonies, graduation events, and other academic milestones.')); ?>
                    </p>
                </div>

                <!-- Card 5 -->
                <div class="document-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 rounded-2xl bg-red-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">account_balance</span>
                    </div>
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'whats_included', 'item_5_title', 'Administrative Actions')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'whats_included', 'item_5_description', 'Registrar appointments, rector selections, and other key administrative decisions.')); ?>
                    </p>
                </div>

                <!-- Card 6 -->
                <div class="document-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">payments</span>
                    </div>
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'whats_included', 'item_6_title', 'Allowances & Benefits')); ?></h4>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'whats_included', 'item_6_description', 'Adjustments to allowances, teaching compensation, and employee benefit structures.')); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Access Documents Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(getContent($pageContent, 'access_documents', 'section_title', 'Access Recorder Documents')); ?></h2>
                    <div class="h-2 w-40 bg-green-600 mx-auto rounded-full mb-8"></div>
                    <p class="text-4xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'access_documents', 'section_description', 'All official university recorders are available for review. Access requires authentication to ensure document integrity and proper access control.')); ?>
                    </p>
                </div>

                <div class="glass p-12 rounded-[3rem] shadow-2xl border border-gray-100 dark:border-gray-800">
                    <div class="text-center space-y-8">
                        <div class="w-24 h-24 rounded-full bg-blue-600 flex items-center justify-center text-white shadow-lg mx-auto">
                            <span class="material-symbols-outlined text-6xl text-white">lock</span>
                        </div>
                        <h3 class="text-5xl font-black text-gray-900 dark:text-white"><?php echo strip_tags(getContent($pageContent, 'access_documents', 'login_title', 'Login Required')); ?></h3>
                        <p class="text-3xl text-gray-700 dark:text-gray-300 font-medium leading-relaxed max-w-2xl mx-auto">
                            <?php echo strip_tags(getContent($pageContent, 'access_documents', 'login_description', 'University Recorders contain official institutional records. To access these documents, please log in with your university credentials through the iSchool portal.')); ?>
                        </p>
                        <div class="flex flex-col sm:flex-row gap-6 justify-center pt-6">
                            <a href="<?php echo strip_tags(getContent($pageContent, 'access_documents', 'login_url', 'https://ischool.vvu.edu.gh')); ?>" target="_blank" class="px-10 py-5 bg-blue-600 hover:bg-blue-700 text-white text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                                <span class="material-symbols-outlined text-3xl">login</span>
                                <?php echo strip_tags(getContent($pageContent, 'access_documents', 'login_btn_text', 'Login to iSchool')); ?>
                            </a>
                            <a href="<?php echo strip_tags(getContent($pageContent, 'access_documents', 'help_url', 'contact_us.php')); ?>" class="px-10 py-5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                                <span class="material-symbols-outlined text-3xl">help</span>
                                <?php echo strip_tags(getContent($pageContent, 'access_documents', 'help_btn_text', 'Need Help?')); ?>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Document List Preview -->
                <div class="mt-16">
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-8 text-center"><?php echo strip_tags(getContent($pageContent, 'access_documents', 'list_title', 'Available Recorders')); ?></h3>
                    <div class="space-y-4">
                        <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <span class="material-symbols-outlined text-4xl text-blue-600">description</span>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo strip_tags(getContent($pageContent, 'access_documents', 'doc_1_title', 'Recorder 4-24')); ?></p>
                                        <p class="text-xl text-gray-600 dark:text-gray-400"><?php echo strip_tags(getContent($pageContent, 'access_documents', 'doc_1_desc', 'Staff appointments, promotions, and policy updates (2011-2014)')); ?></p>
                                    </div>
                                </div>
                                <span class="material-symbols-outlined text-3xl text-gray-400">lock</span>
                            </div>
                        </div>
                        <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <span class="material-symbols-outlined text-4xl text-blue-600">description</span>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo strip_tags(getContent($pageContent, 'access_documents', 'doc_2_title', 'Recorder 36')); ?></p>
                                        <p class="text-xl text-gray-600 dark:text-gray-400"><?php echo strip_tags(getContent($pageContent, 'access_documents', 'doc_2_desc', 'VVU Staff Changes (February 2018)')); ?></p>
                                    </div>
                                </div>
                                <span class="material-symbols-outlined text-3xl text-gray-400">lock</span>
                            </div>
                        </div>
                        <div class="text-center mt-8">
                            <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags(getContent($pageContent, 'access_documents', 'list_footer', '...and more documents available after login')); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Transparency Matters Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-full mx-auto px-4 md:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(getContent($pageContent, 'transparency', 'section_title', 'Why Transparency Matters')); ?></h2>
                    <p class="text-4xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags(getContent($pageContent, 'transparency', 'section_description', 'Our commitment to transparency strengthens trust and accountability in university governance.')); ?>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg border border-gray-100 dark:border-gray-800">
                        <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg mb-6">
                            <span class="material-symbols-outlined text-4xl text-white">visibility</span>
                        </div>
                        <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'transparency', 'item_1_title', 'Institutional Transparency')); ?></h4>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                            <?php echo strip_tags(getContent($pageContent, 'transparency', 'item_1_description', 'Open access to official decisions helps maintain trust and allows stakeholders to understand university operations.')); ?>
                        </p>
                    </div>

                    <div class="p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg border border-gray-100 dark:border-gray-800">
                        <div class="w-16 h-16 rounded-2xl bg-green-600 flex items-center justify-center text-white shadow-lg mb-6">
                            <span class="material-symbols-outlined text-4xl text-white">verified</span>
                        </div>
                        <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'transparency', 'item_2_title', 'Historical Record')); ?></h4>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                            <?php echo strip_tags(getContent($pageContent, 'transparency', 'item_2_description', 'Recorders provide a permanent, official history of institutional development and decision-making over time.')); ?>
                        </p>
                    </div>

                    <div class="p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg border border-gray-100 dark:border-gray-800">
                        <div class="w-16 h-16 rounded-2xl bg-yellow-500 flex items-center justify-center text-white shadow-lg mb-6">
                            <span class="material-symbols-outlined text-4xl text-white">balance</span>
                        </div>
                        <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'transparency', 'item_3_title', 'Accountability')); ?></h4>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                            <?php echo strip_tags(getContent($pageContent, 'transparency', 'item_3_description', 'Documented decisions ensure accountability and provide a reference for policy implementation and review.')); ?>
                        </p>
                    </div>

                    <div class="p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg border border-gray-100 dark:border-gray-800">
                        <div class="w-16 h-16 rounded-2xl bg-purple-600 flex items-center justify-center text-white shadow-lg mb-6">
                            <span class="material-symbols-outlined text-4xl text-white">fact_check</span>
                        </div>
                        <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags(getContent($pageContent, 'transparency', 'item_4_title', 'Legal Compliance')); ?></h4>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                            <?php echo strip_tags(getContent($pageContent, 'transparency', 'item_4_description', 'Official records ensure compliance with regulatory requirements and provide documentation for audits.')); ?>
                        </p>
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
                    <?php echo strip_tags(getContent($pageContent, 'cta_section', 'cta_title', 'Questions About')); ?> <br><span class="text-yellow-400 text-6xl sm:text-7xl md:text-8xl lg:text-6xl block mt-2"><?php echo strip_tags(getContent($pageContent, 'cta_section', 'cta_highlight', 'University Records?')); ?></span>
                </h2>
                <p class="text-2xl sm:text-3xl md:text-4xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags(getContent($pageContent, 'cta_section', 'cta_description', 'Our administrative team is here to assist you with questions about university recorders, official documents, and institutional policies.')); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_1_url', 'office_of_the_registrar.php')); ?>" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">admin_panel_settings</span>
                        <?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_1_text', 'Office of the Registrar')); ?>
                    </a>
                    <a href="<?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_2_url', 'contact_us.php')); ?>" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">mail</span>
                        <?php echo strip_tags(getContent($pageContent, 'cta_section', 'button_2_text', 'Contact Us')); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>