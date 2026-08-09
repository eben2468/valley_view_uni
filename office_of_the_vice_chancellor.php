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

// Escaped output shortcut
if (!function_exists('vcOut')) {
    function vcOut($sections, $section_key, $field_key, $default = '') {
        return htmlspecialchars(getContent($sections, $section_key, $field_key, $default), ENT_QUOTES, 'UTF-8');
    }
}

// Material symbol name with a safe fallback
if (!function_exists('vcIcon')) {
    function vcIcon($sections, $section_key, $field_key, $default = 'star') {
        $icon = preg_replace('/[^a-z0-9_]/', '', strtolower(getContent($sections, $section_key, $field_key, $default)));
        return $icon !== '' ? $icon : $default;
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

    /* ---- Refreshed content styling ---- */

    /* Wide content rail - fills the empty gutters on large screens.
       .container is already 96% wide site-wide, so this only caps the
       very widest displays. */
    .vc-wrap {
        max-width: 1720px;
        margin-left: auto;
        margin-right: auto;
    }

    .vc-card { transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease; }
    .vc-card:hover { transform: translateY(-6px); box-shadow: 0 26px 50px -22px rgba(15, 23, 42, .40); }

    /* Decorative opening quote for the VC message */
    .vc-quote-mark {
        font-family: Georgia, 'Times New Roman', serif;
        line-height: .8;
        user-select: none;
    }

    /* Sticky portrait rail on large screens only */
    @media (min-width: 1024px) {
        .vc-sticky { position: sticky; top: 110px; }
    }

    /* Prose flows as one continuous column at every width. */
    .vc-message p + p,
    .vc-bio p + p { margin-top: 1.5rem; }
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

    <!-- ============================ VC PROFILE ============================ -->
    <section class="py-24 sm:py-32 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="vc-wrap">
                <div class="flex flex-col lg:flex-row gap-12 lg:gap-16 items-center lg:items-start">
                    <!-- Profile Image -->
                    <div class="w-full max-w-sm lg:max-w-none lg:w-1/4 animate-fadeInUp">
                        <div class="relative group">
                            <div class="absolute -inset-3 bg-gradient-to-r from-blue-600 to-yellow-400 rounded-3xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                            <div class="relative aspect-[3/4] rounded-2xl overflow-hidden shadow-2xl border-4 border-white dark:border-gray-800">
                                <img src="<?php echo strip_tags(getContent($pageContent, 'vc_profile', 'profile_image', 'images/leadership/prof-daniel-ganu.jpg')); ?>"
                                     alt="<?php echo vcOut($pageContent, 'vc_profile', 'name', 'Vice Chancellor'); ?>"
                                     class="w-full h-full object-cover object-top">
                            </div>
                            <div class="mt-6 text-center lg:text-left">
                                <h2 class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white mb-1 tracking-tight"><?php echo vcOut($pageContent, 'vc_profile', 'name', 'Vice Chancellor'); ?></h2>
                                <p class="text-sm font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest"><?php echo vcOut($pageContent, 'vc_profile', 'title', 'Vice Chancellor'); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Content -->
                    <div class="w-full lg:w-3/4 space-y-10 animate-fadeInUp" style="animation-delay: 0.2s;">
                        <div>
                            <h3 class="text-4xl sm:text-5xl font-black text-gray-900 dark:text-white mb-5 tracking-tight"><?php echo vcOut($pageContent, 'vc_profile', 'section_title', 'Profile & Biography'); ?></h3>
                            <div class="h-2 w-32 bg-yellow-400 rounded-full mb-8"></div>
                            <div class="vc-bio text-lg sm:text-xl text-gray-600 dark:text-gray-400 leading-relaxed">
                                <?php for ($i = 1; $i <= 3; $i++):
                                    $bio = getContent($pageContent, 'vc_profile', "bio_paragraph_{$i}");
                                    if ($bio === '') continue; ?>
                                <p><?php echo htmlspecialchars($bio, ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="p-9 bg-blue-50 dark:bg-blue-900/20 rounded-3xl border-l-8 border-blue-600">
                                <span class="material-symbols-outlined text-5xl text-blue-600 mb-3">history_edu</span>
                                <h4 class="text-2xl font-black text-gray-900 dark:text-white mb-2"><?php echo vcOut($pageContent, 'vc_profile', 'experience_title', 'Experience'); ?></h4>
                                <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed"><?php echo vcOut($pageContent, 'vc_profile', 'experience_text', ''); ?></p>
                            </div>
                            <div class="p-9 bg-yellow-50 dark:bg-yellow-900/20 rounded-3xl border-l-8 border-yellow-500">
                                <span class="material-symbols-outlined text-5xl text-yellow-500 mb-3">public</span>
                                <h4 class="text-2xl font-black text-gray-900 dark:text-white mb-2"><?php echo vcOut($pageContent, 'vc_profile', 'impact_title', 'Global Impact'); ?></h4>
                                <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed"><?php echo vcOut($pageContent, 'vc_profile', 'impact_text', ''); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================ VICE CHANCELLOR'S MESSAGE ============================ -->
    <section class="relative py-24 sm:py-32 bg-gray-50 dark:bg-gray-950 overflow-hidden">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-500/5 rounded-full blur-[130px] -mr-64 -mt-64"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-yellow-500/5 rounded-full blur-[130px] -ml-64 -mb-64"></div>

        <div class="container relative z-10">
            <div class="vc-wrap">
                <div class="text-center mb-14">
                    <span class="inline-block px-5 py-2 mb-6 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-base font-black uppercase tracking-widest">
                        <?php echo vcOut($pageContent, 'vc_message', 'section_label', 'From the Vice Chancellor'); ?>
                    </span>
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black text-gray-900 dark:text-white mb-5 tracking-tight">
                        <?php echo vcOut($pageContent, 'vc_message', 'section_title', "The Vice Chancellor's Message"); ?>
                    </h2>
                    <div class="h-2 w-36 bg-yellow-400 mx-auto rounded-full"></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">

                    <!-- Portrait + signature rail -->
                    <div class="lg:col-span-3">
                        <div class="vc-sticky">
                            <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
                                <div class="relative aspect-[4/5] bg-gray-100 dark:bg-gray-800 overflow-hidden">
                                    <img src="<?php echo strip_tags(getContent($pageContent, 'vc_message', 'signature_image', 'images/leadership/prof-daniel-ganu.jpg')); ?>"
                                         alt="<?php echo vcOut($pageContent, 'vc_message', 'signature_name', 'Vice Chancellor'); ?>"
                                         loading="lazy"
                                         class="w-full h-full object-cover object-top">
                                    <div class="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-black/70 to-transparent"></div>
                                    <div class="absolute bottom-4 left-5 right-5">
                                        <p class="text-white text-xl font-black leading-tight drop-shadow"><?php echo vcOut($pageContent, 'vc_message', 'signature_name', ''); ?></p>
                                        <p class="text-yellow-300 text-xs font-bold uppercase tracking-widest"><?php echo vcOut($pageContent, 'vc_message', 'signature_title', ''); ?></p>
                                    </div>
                                </div>

                                <?php $pull = getContent($pageContent, 'vc_message', 'pull_quote'); ?>
                                <?php if ($pull !== ''): ?>
                                <div class="p-9 border-t-4 border-yellow-400">
                                    <span class="material-symbols-outlined text-4xl text-yellow-500 mb-2">format_quote</span>
                                    <p class="text-lg sm:text-xl font-bold italic text-gray-700 dark:text-gray-300 leading-relaxed">
                                        <?php echo htmlspecialchars($pull, ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- The message -->
                    <div class="lg:col-span-9">
                        <div class="relative bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 p-10 sm:p-16">
                            <span class="vc-quote-mark absolute -top-2 left-8 text-[10rem] sm:text-[13rem] text-blue-600/10 dark:text-blue-400/10 pointer-events-none">&ldquo;</span>

                            <div class="relative">
                                <?php $greeting = getContent($pageContent, 'vc_message', 'greeting'); ?>
                                <?php if ($greeting !== ''): ?>
                                <h3 class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white mb-6 tracking-tight">
                                    <?php echo htmlspecialchars($greeting, ENT_QUOTES, 'UTF-8'); ?>
                                </h3>
                                <?php endif; ?>

                                <div class="vc-message text-lg sm:text-xl text-gray-600 dark:text-gray-400 leading-relaxed">
                                    <?php for ($i = 1; $i <= 5; $i++):
                                        $para = getContent($pageContent, 'vc_message', "paragraph_{$i}");
                                        if ($para === '') continue;
                                        $lead = ($i === 1); ?>
                                    <p class="<?php echo $lead ? 'text-xl sm:text-2xl text-gray-800 dark:text-gray-200 font-medium' : ''; ?>">
                                        <?php echo htmlspecialchars($para, ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                    <?php endfor; ?>
                                </div>

                                <!-- Signature -->
                                <div class="mt-10 pt-8 border-t border-gray-100 dark:border-gray-800 flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center text-white shrink-0 shadow-lg">
                                        <span class="material-symbols-outlined text-4xl">draw</span>
                                    </div>
                                    <div>
                                        <p class="text-lg sm:text-2xl font-black text-gray-900 dark:text-white leading-tight"><?php echo vcOut($pageContent, 'vc_message', 'signature_name', ''); ?></p>
                                        <p class="text-sm font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest"><?php echo vcOut($pageContent, 'vc_message', 'signature_title', ''); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================ STRATEGIC PRIORITIES ============================ -->
    <section class="py-24 sm:py-32 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-5xl mx-auto text-center mb-16">
                <span class="inline-block px-5 py-2 mb-6 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 text-base font-black uppercase tracking-widest">
                    <?php echo vcOut($pageContent, 'vision_pillars', 'section_label', 'Looking Ahead'); ?>
                </span>
                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black text-gray-900 dark:text-white mb-5 tracking-tight">
                    <?php echo vcOut($pageContent, 'vision_pillars', 'section_title', 'Strategic Priorities'); ?>
                </h2>
                <div class="h-2 w-36 bg-blue-600 mx-auto rounded-full mb-7"></div>
                <p class="text-lg sm:text-xl text-gray-600 dark:text-gray-400 leading-relaxed">
                    <?php echo vcOut($pageContent, 'vision_pillars', 'section_description', ''); ?>
                </p>
            </div>

            <?php
            $pillar_colors = [
                1 => ['bg' => 'bg-blue-600',   'soft' => 'group-hover:border-blue-500'],
                2 => ['bg' => 'bg-yellow-500', 'soft' => 'group-hover:border-yellow-500'],
                3 => ['bg' => 'bg-green-600',  'soft' => 'group-hover:border-green-500'],
                4 => ['bg' => 'bg-purple-600', 'soft' => 'group-hover:border-purple-500'],
            ];
            ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-10 vc-wrap">
                <?php foreach ($pillar_colors as $n => $c):
                    $ptitle = getContent($pageContent, 'vision_pillars', "pillar_{$n}_title");
                    if ($ptitle === '') continue; ?>
                <div class="vc-card group p-10 sm:p-12 bg-gray-50 dark:bg-gray-800/50 rounded-3xl shadow-lg border-2 border-transparent <?php echo $c['soft']; ?>">
                    <div class="flex items-start gap-5">
                        <div class="w-20 h-20 shrink-0 rounded-2xl <?php echo $c['bg']; ?> flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-5xl"><?php echo vcIcon($pageContent, 'vision_pillars', "pillar_{$n}_icon"); ?></span>
                        </div>
                        <div>
                            <div class="text-sm font-black uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1">Priority <?php echo $n; ?></div>
                            <h4 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mb-3 leading-tight"><?php echo htmlspecialchars($ptitle, ENT_QUOTES, 'UTF-8'); ?></h4>
                            <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed">
                                <?php echo vcOut($pageContent, 'vision_pillars', "pillar_{$n}_description", ''); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ============================ MANDATE OF THE OFFICE ============================ -->
    <section class="py-24 sm:py-32 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-5xl mx-auto text-center mb-16">
                <span class="inline-block px-5 py-2 mb-6 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-base font-black uppercase tracking-widest">
                    <?php echo vcOut($pageContent, 'office_mandate', 'section_label', 'The Office'); ?>
                </span>
                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black text-gray-900 dark:text-white mb-5 tracking-tight">
                    <?php echo vcOut($pageContent, 'office_mandate', 'section_title', 'Mandate of the Office'); ?>
                </h2>
                <div class="h-2 w-36 bg-yellow-400 mx-auto rounded-full mb-7"></div>
                <p class="text-lg sm:text-xl text-gray-600 dark:text-gray-400 leading-relaxed">
                    <?php echo vcOut($pageContent, 'office_mandate', 'section_description', ''); ?>
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 vc-wrap">
                <?php for ($n = 1; $n <= 6; $n++):
                    $ititle = getContent($pageContent, 'office_mandate', "item_{$n}_title");
                    if ($ititle === '') continue; ?>
                <div class="vc-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-lg border border-gray-100 dark:border-gray-800">
                    <div class="w-16 h-16 rounded-2xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-5 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-4xl"><?php echo vcIcon($pageContent, 'office_mandate', "item_{$n}_icon"); ?></span>
                    </div>
                    <h4 class="text-lg sm:text-2xl font-black text-gray-900 dark:text-white mb-3 leading-tight"><?php echo htmlspecialchars($ititle, ENT_QUOTES, 'UTF-8'); ?></h4>
                    <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed">
                        <?php echo vcOut($pageContent, 'office_mandate', "item_{$n}_text", ''); ?>
                    </p>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <!-- ============================ RELATED OFFICES ============================ -->
    <section class="py-24 sm:py-32 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-5xl mx-auto text-center mb-16">
                <span class="inline-block px-5 py-2 mb-6 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-base font-black uppercase tracking-widest">
                    <?php echo vcOut($pageContent, 'related_offices', 'section_label', 'Explore Further'); ?>
                </span>
                <h2 class="text-4xl sm:text-5xl font-black text-gray-900 dark:text-white mb-5 tracking-tight">
                    <?php echo vcOut($pageContent, 'related_offices', 'section_title', 'Related Offices & Resources'); ?>
                </h2>
                <div class="h-2 w-32 bg-blue-600 mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 vc-wrap">
                <?php for ($n = 1; $n <= 4; $n++):
                    $ltitle = getContent($pageContent, 'related_offices', "link_{$n}_title");
                    if ($ltitle === '') continue; ?>
                <a href="<?php echo vcOut($pageContent, 'related_offices', "link_{$n}_url", '#'); ?>"
                   class="vc-card group block p-9 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border border-gray-100 dark:border-gray-800 hover:border-blue-500 dark:hover:border-blue-500 no-underline">
                    <div class="w-14 h-14 rounded-xl bg-white dark:bg-gray-900 text-blue-600 dark:text-blue-400 flex items-center justify-center shadow mb-5 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-3xl"><?php echo vcIcon($pageContent, 'related_offices', "link_{$n}_icon", 'link'); ?></span>
                    </div>
                    <h4 class="text-xl font-black text-gray-900 dark:text-white mb-2 leading-snug"><?php echo htmlspecialchars($ltitle, ENT_QUOTES, 'UTF-8'); ?></h4>
                    <p class="text-base text-gray-600 dark:text-gray-400 leading-relaxed mb-4"><?php echo vcOut($pageContent, 'related_offices', "link_{$n}_text", ''); ?></p>
                    <span class="inline-flex items-center gap-1 text-sm font-black text-blue-600 dark:text-blue-400">
                        Visit <span class="material-symbols-outlined text-lg transition-transform group-hover:translate-x-1">arrow_forward</span>
                    </span>
                </a>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <!-- ============================ CONTACT & APPOINTMENT ============================ -->
    <section class="py-24 sm:py-32 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="vc-wrap">
                <div class="bg-white dark:bg-gray-900 p-10 sm:p-16 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-gray-800">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">

                        <!-- Contact details -->
                        <div>
                            <span class="inline-block px-4 py-1.5 mb-5 rounded-full bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-sm font-black uppercase tracking-widest">
                                <?php echo vcOut($pageContent, 'office_contact', 'section_label', 'Get in Touch'); ?>
                            </span>
                            <h3 class="text-4xl sm:text-5xl font-black text-gray-900 dark:text-white mb-4 tracking-tight"><?php echo vcOut($pageContent, 'office_contact', 'section_title', 'Contact the Office'); ?></h3>
                            <p class="text-lg sm:text-xl text-gray-600 dark:text-gray-400 mb-9 leading-relaxed">
                                <?php echo vcOut($pageContent, 'office_contact', 'section_description', ''); ?>
                            </p>

                            <?php
                            $email    = getContent($pageContent, 'office_contact', 'email');
                            $phone    = getContent($pageContent, 'office_contact', 'phone');
                            $location = getContent($pageContent, 'office_contact', 'office_location');
                            $postal   = getContent($pageContent, 'office_contact', 'postal_address');
                            $hours    = getContent($pageContent, 'office_contact', 'office_hours');
                            $map      = getContent($pageContent, 'office_contact', 'map_url');
                            ?>
                            <div class="space-y-5">
                                <?php if ($email !== ''): ?>
                                <div class="flex items-start gap-4">
                                    <div class="w-14 h-14 shrink-0 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                        <span class="material-symbols-outlined text-3xl">mail</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-black text-gray-400 uppercase tracking-widest mb-0.5">Email Address</p>
                                        <a href="mailto:<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" class="text-xl font-bold text-gray-900 dark:text-white hover:text-blue-600 transition-colors break-all"><?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?></a>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if ($phone !== ''): ?>
                                <div class="flex items-start gap-4">
                                    <div class="w-14 h-14 shrink-0 rounded-2xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center text-yellow-600 dark:text-yellow-400">
                                        <span class="material-symbols-outlined text-3xl">call</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-black text-gray-400 uppercase tracking-widest mb-0.5">Phone Number</p>
                                        <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9+]/', '', $phone), ENT_QUOTES, 'UTF-8'); ?>" class="text-xl font-bold text-gray-900 dark:text-white hover:text-yellow-600 transition-colors"><?php echo htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'); ?></a>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if ($location !== ''): ?>
                                <div class="flex items-start gap-4">
                                    <div class="w-14 h-14 shrink-0 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                                        <span class="material-symbols-outlined text-3xl">location_on</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-black text-gray-400 uppercase tracking-widest mb-0.5">Office Location</p>
                                        <p class="text-xl font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($location, ENT_QUOTES, 'UTF-8'); ?></p>
                                        <?php if ($postal !== ''): ?>
                                        <p class="text-base text-gray-500 dark:text-gray-400 mt-0.5"><?php echo htmlspecialchars($postal, ENT_QUOTES, 'UTF-8'); ?></p>
                                        <?php endif; ?>
                                        <?php if ($map !== ''): ?>
                                        <a href="<?php echo htmlspecialchars($map, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-1 mt-2 text-sm font-black text-green-600 dark:text-green-400 hover:underline">
                                            View on map <span class="material-symbols-outlined text-lg">open_in_new</span>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if ($hours !== ''): ?>
                                <div class="flex items-start gap-4">
                                    <div class="w-14 h-14 shrink-0 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                        <span class="material-symbols-outlined text-3xl">schedule</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-black text-gray-400 uppercase tracking-widest mb-0.5">Office Hours</p>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white leading-relaxed"><?php echo htmlspecialchars($hours, ENT_QUOTES, 'UTF-8'); ?></p>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Appointment request -->
                        <div class="bg-gray-50 dark:bg-gray-800/50 p-10 sm:p-12 rounded-[2rem] border border-gray-100 dark:border-gray-700">
                            <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-3 tracking-tight"><?php echo vcOut($pageContent, 'office_contact', 'form_title', 'Request an Appointment'); ?></h4>
                            <p class="text-lg text-gray-600 dark:text-gray-400 mb-7 leading-relaxed">
                                <?php echo vcOut($pageContent, 'office_contact', 'form_description', ''); ?>
                            </p>
                            <form action="contact_process.php" method="post" class="space-y-4">
                                <input type="hidden" name="inquiry-type" value="Appointment Request - Office of the Vice Chancellor">
                                <div>
                                    <label for="vc_name" class="sr-only">Full name</label>
                                    <input id="vc_name" name="name" type="text" required placeholder="Your Full Name"
                                           class="w-full px-6 py-5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none text-lg transition">
                                </div>
                                <div>
                                    <label for="vc_email" class="sr-only">Email address</label>
                                    <input id="vc_email" name="email" type="email" required placeholder="Email Address"
                                           class="w-full px-6 py-5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none text-lg transition">
                                </div>
                                <div>
                                    <label for="vc_msg" class="sr-only">Purpose of appointment</label>
                                    <textarea id="vc_msg" name="message" rows="4" required placeholder="Purpose of Appointment"
                                              class="w-full px-6 py-5 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none text-lg transition resize-y"></textarea>
                                </div>
                                <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white text-xl font-black rounded-2xl transition-all shadow-lg hover:shadow-blue-500/25 flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined">send</span>
                                    <?php echo vcOut($pageContent, 'office_contact', 'form_btn_text', 'Submit Request'); ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================ CTA ============================ -->
    <section class="relative py-24 sm:py-32 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>

        <div class="container relative z-10">
            <div class="vc-wrap text-center">
                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white mb-6 leading-tight tracking-tight">
                    <?php echo vcOut($pageContent, 'cta_section', 'cta_title', 'Building a Legacy of'); ?>
                    <span class="text-yellow-400 block mt-2"><?php echo vcOut($pageContent, 'cta_section', 'cta_highlight', 'Excellence Together'); ?></span>
                </h2>
                <p class="text-lg sm:text-xl lg:text-2xl text-blue-100 mb-10 max-w-4xl mx-auto leading-relaxed">
                    <?php echo vcOut($pageContent, 'cta_section', 'cta_description', ''); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo vcOut($pageContent, 'cta_section', 'button_1_url', 'history.php'); ?>" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-lg font-black rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined">info</span>
                        <?php echo vcOut($pageContent, 'cta_section', 'button_1_text', 'About the University'); ?>
                    </a>
                    <a href="<?php echo vcOut($pageContent, 'cta_section', 'button_2_url', 'contact_us.php'); ?>" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-lg font-black rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined">mail</span>
                        <?php echo vcOut($pageContent, 'cta_section', 'button_2_text', 'Get in Touch'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
