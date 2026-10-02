<?php
/**
 * ISIMS — Integrated School Information Management System
 *
 * Every string on this page comes from the academic_pages_* tables and is
 * edited at admin/manage_departmental_resources.php?page=isims.
 * Seed content is installed by isims_page_content.sql.
 */

require_once 'includes/db_connect.php';

$page_key = 'isims';

$stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = ? AND is_active = 1");
$stmt->execute([$page_key]);
$hero = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

$stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = ? AND is_active = 1 ORDER BY display_order");
$stmt->execute([$page_key]);
$section_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Keyed by section_key so the markup below can pull a heading by name without
// caring what order the admin has put the sections in.
$sections = [];
foreach ($section_rows as $row) {
    $sections[$row['section_key']] = $row;
}

$stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = ? AND is_active = 1 ORDER BY section_key, display_order");
$stmt->execute([$page_key]);

$items = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $item) {
    $items[$item['section_key']][] = $item;
}

$stmt = $pdo->prepare("SELECT * FROM academic_pages_stats WHERE page_key = ? AND is_active = 1 ORDER BY display_order");
$stmt->execute([$page_key]);
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title  = $hero['meta_title'] ?? 'ISIMS Student Portal - Valley View University';
$active_page = "students";

$portal_url = 'https://isims.vvu.edu.gh';

/** Section heading helper — falls back to the supplied default when unset. */
function isims_section($sections, $key, $field, $default = '') {
    $value = $sections[$key][$field] ?? '';
    return $value !== '' ? $value : $default;
}

/**
 * Links are stored by an admin and may be an external URL, a site-relative
 * path, a mailto: or a tel:. Anything that is not already absolute is treated
 * as relative to the site root. javascript: and data: URLs are rejected so a
 * stored link can never become script injection.
 */
function isims_link($url) {
    $url = trim((string) $url);
    if ($url === '') {
        return '';
    }
    if (preg_match('#^(https?://|mailto:|tel:|/)#i', $url)) {
        return $url;
    }
    if (preg_match('#^[a-z][a-z0-9+.-]*:#i', $url)) {
        return '';   // unknown scheme (javascript:, data:, …)
    }
    return $url;
}

/** Tailwind colour token from the admin, e.g. "blue-600". */
function isims_color($item, $default = 'blue-600') {
    $color = preg_replace('/[^a-z0-9-]/', '', strtolower((string) ($item['item_color'] ?? '')));
    return $color !== '' ? $color : $default;
}

/** Shared centred heading used by every content section. */
function isims_heading($sections, $key, $default_title, $light = false) {
    $eyebrow = isims_section($sections, $key, 'section_subtitle');
    $title   = isims_section($sections, $key, 'section_title', $default_title);
    $desc    = isims_section($sections, $key, 'section_description');
    ?>
    <div class="max-w-5xl mx-auto text-center mb-16 md:mb-20">
        <?php if ($eyebrow !== ''): ?>
        <span class="inline-block px-6 py-2 mb-6 rounded-full text-lg font-black uppercase tracking-widest <?php echo $light ? 'bg-white/10 text-yellow-400 border border-white/20' : 'bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'; ?>">
            <?php echo strip_tags($eyebrow); ?>
        </span>
        <?php endif; ?>
        <h2 class="text-4xl sm:text-5xl md:text-6xl font-black mb-6 <?php echo $light ? 'text-white' : 'text-gray-900 dark:text-white'; ?>"><?php echo strip_tags($title); ?></h2>
        <div class="h-2 w-40 mx-auto rounded-full mb-8 <?php echo $light ? 'bg-yellow-400' : 'bg-blue-600'; ?>"></div>
        <?php if ($desc !== ''): ?>
        <p class="text-2xl sm:text-3xl font-bold leading-relaxed <?php echo $light ? 'text-blue-100' : 'text-gray-600 dark:text-gray-400'; ?>"><?php echo strip_tags($desc); ?></p>
        <?php endif; ?>
    </div>
    <?php
}

// The in-page navigation lists only the sections that actually have content.
$quick_nav = [
    'services'     => ['Services', 'apps'],
    'login'        => ['First Login', 'login'],
    'registration' => ['Registration', 'how_to_reg'],
    'hostel'       => ['Hostel & Meals', 'apartment'],
    'troubleshoot' => ['Troubleshooting', 'build'],
    'guides'       => ['Guides', 'menu_book'],
    'support'      => ['Help', 'support_agent'],
];
$quick_nav = array_filter($quick_nav, function ($key) use ($items) {
    return $key === 'hostel' ? (!empty($items['hostel']) || !empty($items['cafeteria'])) : !empty($items[$key]);
}, ARRAY_FILTER_USE_KEY);

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
    html { scroll-behavior: smooth; }
    .isims-anchor { scroll-margin-top: 120px; }
    .isims-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .isims-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }
    .icon-container {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
    }
    .icon-container span {
        color: #fff !important;
        font-size: 40px;
    }
    @media (max-width: 639px) {
        .icon-container {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            margin-bottom: 1.25rem;
        }
        .icon-container span { font-size: 30px; }
    }
    .doc-cover {
        background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.18) 0, transparent 45%),
                          linear-gradient(135deg, rgba(255,255,255,0.08) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0.08) 75%, transparent 75%);
        background-size: auto, 28px 28px;
    }
    .preview-tab[aria-selected="true"] {
        background: #002147;
        color: #fff;
        border-color: #002147;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[70vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo htmlspecialchars(strip_tags($hero['hero_image'] ?? 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=1920')); ?>"
                 alt="Students using the ISIMS portal" class="w-full h-full object-cover animate-slow-zoom opacity-50">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>

        <div class="container relative z-10 py-24">
            <div class="max-w-6xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['hero_badge'] ?? 'New Student Portal'); ?></span>
                </div>

                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['hero_title'] ?? 'ISIMS'); ?> <br>
                    <span class="text-3xl sm:text-4xl md:text-5xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($hero['hero_subtitle'] ?? 'Integrated School Information Management System'); ?></span>
                </h1>

                <p class="text-xl sm:text-2xl md:text-3xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($hero['hero_description'] ?? ''); ?>
                </p>

                <!-- Primary portal call to action -->
                <div class="mt-12 flex flex-col sm:flex-row gap-4 sm:gap-6 justify-center animate-fadeInUp" style="animation-delay: 0.3s;">
                    <a href="<?php echo htmlspecialchars(isims_link($hero['cta_button_link'] ?? '') ?: $portal_url); ?>" target="_blank" rel="noopener"
                       class="inline-flex items-center justify-center gap-3 sm:gap-4 px-6 py-3.5 sm:px-12 sm:py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-lg sm:text-2xl font-black rounded-xl sm:rounded-2xl transition-all transform hover:scale-105 shadow-2xl">
                        <span class="material-symbols-outlined text-2xl sm:text-3xl">login</span>
                        <?php echo strip_tags($hero['cta_button_text'] ?? 'Log In to ISIMS'); ?>
                        <span class="material-symbols-outlined text-2xl sm:text-3xl">open_in_new</span>
                    </a>
                    <?php if (!empty($hero['cta_button_link_2'])): ?>
                    <a href="<?php echo htmlspecialchars(isims_link($hero['cta_button_link_2'])); ?>" target="_blank" rel="noopener"
                       class="inline-flex items-center justify-center gap-3 sm:gap-4 px-6 py-3.5 sm:px-12 sm:py-6 bg-white/10 backdrop-blur-md border border-white/25 hover:bg-white/20 text-white text-lg sm:text-2xl font-bold rounded-xl sm:rounded-2xl transition-all transform hover:scale-105 shadow-2xl">
                        <span class="material-symbols-outlined text-2xl sm:text-3xl">menu_book</span>
                        <?php echo strip_tags($hero['cta_button_text_2'] ?? 'Student User Guide'); ?>
                    </a>
                    <?php endif; ?>
                </div>

                <p class="mt-8 text-lg md:text-xl text-blue-100 font-bold tracking-wide animate-fadeInUp" style="animation-delay: 0.35s;">
                    <span class="material-symbols-outlined align-middle text-yellow-400">lock</span>
                    <?php echo htmlspecialchars(preg_replace('#^https?://#', '', $portal_url)); ?>
                </p>

                <?php if ($stats): ?>
                <!-- Quick Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-8 mt-10 sm:mt-20 max-w-5xl mx-auto">
                    <?php foreach ($stats as $stat): ?>
                    <div class="px-3 py-4 sm:px-8 sm:py-10 bg-white/5 backdrop-blur-md rounded-2xl sm:rounded-[2.5rem] border border-white/10 shadow-xl group hover:bg-white/10 transition-all">
                        <span class="material-symbols-outlined text-yellow-400 text-2xl sm:text-4xl mb-1 sm:mb-3 group-hover:scale-110 transition-transform"><?php echo strip_tags($stat['stat_icon'] ?? 'star'); ?></span>
                        <p class="text-xl sm:text-3xl font-black text-white mb-0.5 sm:mb-1 break-words"><?php echo strip_tags($stat['stat_value']); ?></p>
                        <p class="text-xs sm:text-lg text-blue-200 font-bold uppercase tracking-wider sm:tracking-widest"><?php echo strip_tags($stat['stat_label']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Services (overlapping card) -->
    <section id="services" class="isims-anchor py-20 md:py-24 bg-white dark:bg-gray-900 relative z-20 -mt-20 mx-auto max-w-[95%] rounded-[3rem] shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-800">
        <div class="w-full px-6 md:px-16">
            <?php if ($quick_nav): ?>
            <!-- On-page navigation -->
            <div role="navigation" aria-label="On this page" class="flex flex-wrap justify-center gap-3 mb-16 md:mb-20">
                <?php foreach ($quick_nav as $anchor => [$label, $icon]): ?>
                <a href="#<?php echo $anchor; ?>"
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-lg font-bold hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 transition-colors">
                    <span class="material-symbols-outlined text-xl"><?php echo $icon; ?></span>
                    <?php echo $label; ?>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($items['services'])): ?>
            <?php isims_heading($sections, 'services', 'Everything In One Portal'); ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-8 md:gap-10">
                <?php foreach ($items['services'] as $item): $color = isims_color($item); ?>
                <div class="isims-card group p-6 sm:p-10 bg-gray-50 dark:bg-gray-800 rounded-3xl sm:rounded-[2.5rem] border border-gray-100 dark:border-gray-700 flex flex-col items-center text-center">
                    <div class="icon-container bg-<?php echo $color; ?>">
                        <span class="material-symbols-outlined"><?php echo strip_tags($item['item_icon'] ?: 'apps'); ?></span>
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mb-3 sm:mb-5"><?php echo strip_tags($item['item_title']); ?></h3>
                    <p class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium"><?php echo strip_tags($item['item_description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- First Login -->
    <?php if (!empty($items['login'])): ?>
    <section id="login" class="isims-anchor py-24 bg-gray-50 dark:bg-gray-950">
        <div class="w-full max-w-[95%] mx-auto px-6 md:px-16">
            <?php isims_heading($sections, 'login', 'Logging In For The First Time'); ?>

            <div class="relative max-w-7xl mx-auto">
                <!-- Connector line behind the step badges on wide screens -->
                <div class="max-lg:hidden absolute top-14 left-[10%] right-[10%] h-1 bg-gradient-to-r from-blue-200 via-purple-200 to-yellow-200 dark:from-blue-900 dark:via-purple-900 dark:to-yellow-900 rounded-full"></div>

                <div class="relative grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-<?php echo min(5, count($items['login'])); ?> gap-10">
                    <?php foreach ($items['login'] as $i => $item): $color = isims_color($item); $link = isims_link($item['item_link']); ?>
                    <div class="text-center group">
                        <div class="relative w-28 h-28 rounded-3xl bg-<?php echo $color; ?> flex items-center justify-center mx-auto mb-8 group-hover:scale-110 transition-transform shadow-xl ring-8 ring-gray-50 dark:ring-gray-950">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($item['item_icon'] ?: 'check'); ?></span>
                            <span class="absolute -top-3 -right-3 w-10 h-10 rounded-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-lg font-black flex items-center justify-center shadow-lg">
                                <?php echo strip_tags($item['item_stat_value'] !== '' ? $item['item_stat_value'] : ($i + 1)); ?>
                            </span>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($item['item_title']); ?></h3>
                        <p class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium"><?php echo strip_tags($item['item_description']); ?></p>
                        <?php if ($link !== ''): ?>
                        <a href="<?php echo htmlspecialchars($link); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 mt-4 text-xl font-bold text-blue-600 dark:text-blue-400 hover:underline">
                            Open
                            <span class="material-symbols-outlined text-2xl">arrow_forward</span>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Course Registration + Blocked Courses -->
    <?php if (!empty($items['registration']) || !empty($items['blocked'])): ?>
    <section id="registration" class="isims-anchor py-24 bg-white dark:bg-gray-900">
        <div class="w-full max-w-[95%] mx-auto px-6 md:px-16">
            <?php isims_heading($sections, 'registration', 'Registering For Courses'); ?>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 max-w-7xl mx-auto">
                <?php if (!empty($items['registration'])): ?>
                <!-- Timeline -->
                <ol class="<?php echo !empty($items['blocked']) ? 'lg:col-span-7' : 'lg:col-span-12'; ?> relative">
                    <?php $last = count($items['registration']) - 1; ?>
                    <?php foreach ($items['registration'] as $i => $item): $color = isims_color($item); ?>
                    <li class="relative flex gap-6 md:gap-8 pb-12 last:pb-0">
                        <?php if ($i < $last): ?>
                        <span class="absolute left-10 top-20 bottom-0 w-1 -ml-0.5 bg-gray-200 dark:bg-gray-700 rounded-full" aria-hidden="true"></span>
                        <?php endif; ?>
                        <div class="relative z-10 shrink-0 w-20 h-20 rounded-2xl bg-<?php echo $color; ?> flex items-center justify-center text-white text-3xl font-black shadow-lg">
                            <?php echo strip_tags($item['item_stat_value'] !== '' ? $item['item_stat_value'] : ($i + 1)); ?>
                        </div>
                        <div class="flex-grow p-8 bg-gray-50 dark:bg-gray-800 rounded-[2rem] border border-gray-100 dark:border-gray-700">
                            <?php if (!empty($item['item_subtitle'])): ?>
                            <p class="text-base font-black uppercase tracking-widest text-<?php echo $color; ?> mb-2"><?php echo strip_tags($item['item_subtitle']); ?></p>
                            <?php endif; ?>
                            <h3 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-3 flex items-center gap-3">
                                <?php echo strip_tags($item['item_title']); ?>
                            </h3>
                            <p class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium"><?php echo strip_tags($item['item_description']); ?></p>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ol>
                <?php endif; ?>

                <?php if (!empty($items['blocked'])): ?>
                <!-- Blocked courses explainer -->
                <aside class="<?php echo !empty($items['registration']) ? 'lg:col-span-5' : 'lg:col-span-12'; ?>">
                    <div class="lg:sticky lg:top-32 p-8 md:p-10 bg-gradient-to-br from-blue-900 to-blue-700 rounded-[2.5rem] shadow-2xl text-white">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-16 h-16 shrink-0 rounded-2xl bg-white/15 flex items-center justify-center">
                                <span class="material-symbols-outlined text-4xl text-white">block</span>
                            </div>
                            <h3 class="text-3xl font-black"><?php echo strip_tags(isims_section($sections, 'blocked', 'section_title', 'Why Is A Course Blocked?')); ?></h3>
                        </div>
                        <p class="text-xl text-blue-100 font-medium leading-relaxed mb-8"><?php echo strip_tags(isims_section($sections, 'blocked', 'section_description')); ?></p>

                        <div class="space-y-5">
                            <?php foreach ($items['blocked'] as $item): $color = isims_color($item); ?>
                            <div class="flex gap-5 p-6 bg-white/10 backdrop-blur-md rounded-2xl border border-white/15">
                                <div class="w-14 h-14 shrink-0 rounded-xl bg-<?php echo $color; ?> flex items-center justify-center shadow-lg">
                                    <span class="material-symbols-outlined text-3xl text-white"><?php echo strip_tags($item['item_icon'] ?: 'info'); ?></span>
                                </div>
                                <div>
                                    <h4 class="text-2xl font-black text-white mb-1"><?php echo strip_tags($item['item_title']); ?></h4>
                                    <p class="text-lg text-blue-100 leading-relaxed"><?php echo strip_tags($item['item_description']); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </aside>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Hostel & Cafeteria -->
    <?php $campus_keys = array_values(array_filter(['hostel', 'cafeteria'], function ($k) use ($items) { return !empty($items[$k]); })); ?>
    <?php if ($campus_keys): ?>
    <section id="hostel" class="isims-anchor py-24 bg-gray-50 dark:bg-gray-950">
        <div class="w-full max-w-[95%] mx-auto px-6 md:px-16">
            <div class="max-w-5xl mx-auto text-center mb-16 md:mb-20">
                <span class="inline-block px-6 py-2 mb-6 rounded-full text-lg font-black uppercase tracking-widest bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">Campus Living</span>
                <h2 class="text-4xl sm:text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-6">
                    <?php echo strip_tags(implode(' & ', array_map(function ($k) use ($sections) {
                        return isims_section($sections, $k, 'section_title', ucfirst($k));
                    }, $campus_keys))); ?>
                </h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 <?php echo count($campus_keys) > 1 ? 'lg:grid-cols-2' : ''; ?> gap-10 max-w-7xl mx-auto">
                <?php foreach ($campus_keys as $key): ?>
                <?php
                    $first = $items[$key][0];
                    $color = isims_color($first, $key === 'hostel' ? 'purple-600' : 'orange-600');
                    $head_icon = $key === 'hostel' ? 'apartment' : 'restaurant';
                ?>
                <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden flex flex-col">
                    <div class="relative p-10 bg-<?php echo $color; ?> text-white overflow-hidden">
                        <span class="material-symbols-outlined absolute -right-6 -bottom-10 text-[12rem] text-white/10 select-none" aria-hidden="true"><?php echo $head_icon; ?></span>
                        <p class="relative text-lg font-black uppercase tracking-widest text-white/80 mb-2"><?php echo strip_tags(isims_section($sections, $key, 'section_subtitle')); ?></p>
                        <h3 class="relative text-4xl font-black mb-4"><?php echo strip_tags(isims_section($sections, $key, 'section_title', ucfirst($key))); ?></h3>
                        <p class="relative text-xl text-white/90 font-medium leading-relaxed"><?php echo strip_tags(isims_section($sections, $key, 'section_description')); ?></p>
                    </div>
                    <ol class="p-8 md:p-10 space-y-6 flex-grow">
                        <?php foreach ($items[$key] as $i => $item): $c = isims_color($item, $color); ?>
                        <li class="flex gap-5 items-start">
                            <div class="w-14 h-14 shrink-0 rounded-2xl bg-<?php echo $c; ?> flex items-center justify-center shadow-md">
                                <span class="material-symbols-outlined text-3xl text-white"><?php echo strip_tags($item['item_icon'] ?: 'check'); ?></span>
                            </div>
                            <div>
                                <h4 class="text-2xl font-black text-gray-900 dark:text-white mb-1">
                                    <span class="text-<?php echo $c; ?> mr-1"><?php echo strip_tags($item['item_stat_value'] !== '' ? $item['item_stat_value'] : ($i + 1)); ?>.</span>
                                    <?php echo strip_tags($item['item_title']); ?>
                                </h4>
                                <p class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium"><?php echo strip_tags($item['item_description']); ?></p>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- "User Not Found" Troubleshooting -->
    <?php if (!empty($items['troubleshoot'])): ?>
    <section id="troubleshoot" class="isims-anchor py-24 bg-gray-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-[150px] -ml-60 -mb-60"></div>

        <div class="relative z-10 w-full max-w-[95%] mx-auto px-6 md:px-16">
            <?php isims_heading($sections, 'troubleshoot', 'Seeing "User Not Found"?', true); ?>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
                <?php foreach ($items['troubleshoot'] as $i => $item): ?>
                <div class="relative p-8 md:p-10 bg-white/5 backdrop-blur-md rounded-[2rem] border border-white/10 hover:bg-white/10 transition-all overflow-hidden">
                    <span class="absolute right-6 top-2 text-8xl font-black text-white/5 select-none" aria-hidden="true">
                        <?php echo strip_tags($item['item_stat_value'] !== '' ? $item['item_stat_value'] : ($i + 1)); ?>
                    </span>
                    <div class="w-16 h-16 rounded-2xl bg-<?php echo isims_color($item); ?> flex items-center justify-center mb-6 shadow-lg">
                        <span class="material-symbols-outlined text-4xl text-white"><?php echo strip_tags($item['item_icon'] ?: 'check'); ?></span>
                    </div>
                    <p class="text-base font-black uppercase tracking-widest text-yellow-400 mb-2">Step <?php echo strip_tags($item['item_stat_value'] !== '' ? $item['item_stat_value'] : ($i + 1)); ?></p>
                    <h3 class="text-2xl md:text-3xl font-black text-white mb-3"><?php echo strip_tags($item['item_title']); ?></h3>
                    <p class="text-xl text-gray-300 leading-relaxed font-medium"><?php echo strip_tags($item['item_description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($items['support'])): ?>
            <p class="mt-14 text-center text-2xl text-blue-100 font-bold">
                Still having problems?
                <a href="#support" class="inline-flex items-center gap-1 text-yellow-400 hover:text-yellow-300 underline underline-offset-4">
                    Get help from the support team
                    <span class="material-symbols-outlined text-2xl">arrow_downward</span>
                </a>
            </p>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Official Guides (PDF downloads) -->
    <?php if (!empty($items['guides'])): ?>
    <section id="guides" class="isims-anchor py-24 bg-white dark:bg-gray-900">
        <div class="w-full max-w-[95%] mx-auto px-6 md:px-16">
            <?php isims_heading($sections, 'guides', 'Official ISIMS Guides'); ?>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 max-w-7xl mx-auto">
                <?php foreach ($items['guides'] as $i => $item): $color = isims_color($item); $link = isims_link($item['item_link']); ?>
                <article class="isims-card flex flex-col sm:flex-row bg-gray-50 dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-lg overflow-hidden">
                    <!-- Stylised document cover -->
                    <div class="doc-cover relative sm:w-56 shrink-0 bg-<?php echo $color; ?> flex flex-col items-center justify-center gap-4 p-10 text-white">
                        <span class="material-symbols-outlined text-7xl text-white"><?php echo strip_tags($item['item_icon'] ?: 'description'); ?></span>
                        <span class="px-4 py-1 rounded-full bg-white/20 text-sm font-black uppercase tracking-widest">PDF</span>
                    </div>
                    <div class="flex flex-col flex-grow p-8 md:p-10">
                        <?php if (!empty($item['item_subtitle'])): ?>
                        <p class="text-base font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-3"><?php echo strip_tags($item['item_subtitle']); ?></p>
                        <?php endif; ?>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($item['item_title']); ?></h3>
                        <p class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium mb-8 flex-grow"><?php echo strip_tags($item['item_description']); ?></p>
                        <?php if ($link !== ''): ?>
                        <div class="flex flex-wrap gap-4">
                            <a href="<?php echo htmlspecialchars($link); ?>" download
                               class="inline-flex items-center justify-center gap-3 px-7 py-4 bg-<?php echo $color; ?> text-white text-lg font-bold rounded-2xl hover:opacity-90 transition-all shadow-lg">
                                <span class="material-symbols-outlined text-2xl">download</span>
                                <?php echo strip_tags($item['item_stat_value'] ?: 'Download'); ?>
                            </a>
                            <a href="<?php echo htmlspecialchars($link); ?>" target="_blank" rel="noopener"
                               class="inline-flex items-center justify-center gap-3 px-7 py-4 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 text-lg font-bold rounded-2xl border-2 border-gray-200 dark:border-gray-700 hover:border-blue-600 transition-all">
                                <span class="material-symbols-outlined text-2xl">open_in_new</span>
                                Open
                            </a>
                            <button type="button" data-preview="<?php echo $i; ?>"
                                    class="preview-trigger inline-flex max-lg:hidden items-center justify-center gap-3 px-7 py-4 text-[#1e3a8a] dark:text-blue-300 text-lg font-bold rounded-2xl hover:bg-blue-50 dark:hover:bg-gray-700 transition-all">
                                <span class="material-symbols-outlined text-2xl">visibility</span>
                                Preview
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <?php
                // Only guides with a usable link can be previewed in the embedded viewer.
                $previewable = [];
                foreach ($items['guides'] as $i => $item) {
                    $link = isims_link($item['item_link']);
                    if ($link !== '') {
                        $previewable[$i] = ['title' => $item['item_title'], 'link' => $link];
                    }
                }
                $first_preview = $previewable ? reset($previewable) : null;
            ?>
            <?php if ($first_preview): ?>
            <!-- Embedded reader (desktop only — mobile browsers do not render PDFs inline) -->
            <div id="guide-preview" class="max-lg:hidden max-w-7xl mx-auto mt-16 isims-anchor">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white flex items-center gap-3">
                        <span class="material-symbols-outlined text-4xl text-blue-600">chrome_reader_mode</span>
                        Read Online
                    </h3>
                    <div class="flex flex-wrap gap-3" role="tablist" aria-label="Choose a guide to preview">
                        <?php $selected = true; foreach ($previewable as $i => $p): ?>
                        <button type="button" role="tab" data-preview="<?php echo $i; ?>" aria-selected="<?php echo $selected ? 'true' : 'false'; ?>"
                                data-src="<?php echo htmlspecialchars($p['link']); ?>"
                                class="preview-tab px-6 py-3 rounded-full border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 text-lg font-bold transition-all">
                            <?php echo strip_tags($p['title']); ?>
                        </button>
                        <?php $selected = false; endforeach; ?>
                    </div>
                </div>
                <div class="rounded-[2rem] overflow-hidden border border-gray-200 dark:border-gray-700 shadow-2xl bg-gray-100 dark:bg-gray-800">
                    <iframe id="guide-frame" title="<?php echo htmlspecialchars($first_preview['title']); ?>"
                            src="<?php echo htmlspecialchars($first_preview['link']); ?>#view=FitH"
                            class="w-full h-[80vh] max-h-[900px]" loading="lazy"></iframe>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Important Student Notes -->
    <?php if (!empty($items['notes'])): ?>
    <section id="notes" class="isims-anchor py-24 bg-gray-50 dark:bg-gray-950">
        <div class="w-full max-w-[95%] mx-auto px-6 md:px-16">
            <?php isims_heading($sections, 'notes', 'Before You Click Submit'); ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 max-w-7xl mx-auto">
                <?php foreach ($items['notes'] as $item): ?>
                <div class="flex gap-8 p-10 bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-lg border border-gray-100 dark:border-gray-800">
                    <div class="w-20 h-20 shrink-0 rounded-3xl bg-<?php echo isims_color($item); ?> flex items-center justify-center shadow-lg">
                        <span class="material-symbols-outlined text-4xl text-white"><?php echo strip_tags($item['item_icon'] ?: 'info'); ?></span>
                    </div>
                    <div>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-3"><?php echo strip_tags($item['item_title']); ?></h3>
                        <p class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium"><?php echo strip_tags($item['item_description']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Get Started / Support CTA -->
    <section id="support" class="isims-anchor py-24 bg-blue-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>

        <div class="relative z-10 w-full max-w-[95%] mx-auto px-6 md:px-16">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-white mb-8"><?php echo strip_tags($hero['cta_title'] ?? 'Ready to Get Started?'); ?></h2>
                <p class="text-2xl sm:text-3xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($hero['cta_subtitle'] ?? ''); ?>
                </p>

                <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 justify-center mb-14 sm:mb-20">
                    <a href="<?php echo htmlspecialchars(isims_link($hero['cta_button_link'] ?? '') ?: $portal_url); ?>" target="_blank" rel="noopener"
                       class="inline-flex items-center justify-center gap-3 sm:gap-4 px-6 py-3.5 sm:px-12 sm:py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-lg sm:text-2xl font-black rounded-xl sm:rounded-2xl transition-all transform hover:scale-105 shadow-xl">
                        <span class="material-symbols-outlined text-2xl sm:text-3xl">login</span>
                        <?php echo strip_tags($hero['cta_button_text'] ?? 'Log In to ISIMS'); ?>
                    </a>
                    <?php if (!empty($items['guides'])): ?>
                    <a href="#guides"
                       class="inline-flex items-center justify-center gap-3 sm:gap-4 px-6 py-3.5 sm:px-12 sm:py-6 bg-white/10 backdrop-blur-md border border-white/25 hover:bg-white/20 text-white text-lg sm:text-2xl font-bold rounded-xl sm:rounded-2xl transition-all transform hover:scale-105 shadow-xl">
                        <span class="material-symbols-outlined text-2xl sm:text-3xl">menu_book</span>
                        View the Guides
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($items['support'])): ?>
            <div class="max-w-6xl mx-auto">
                <h3 class="text-center text-3xl md:text-4xl font-black text-yellow-400 mb-4"><?php echo strip_tags(isims_section($sections, 'support', 'section_title', 'Getting Help')); ?></h3>
                <p class="text-center text-xl md:text-2xl text-blue-100 font-medium mb-12 max-w-4xl mx-auto"><?php echo strip_tags(isims_section($sections, 'support', 'section_description')); ?></p>

                <div class="grid grid-cols-1 md:grid-cols-<?php echo min(3, count($items['support'])); ?> gap-8">
                    <?php foreach ($items['support'] as $item): $link = isims_link($item['item_link']); $external = preg_match('#^https?://#i', $link); ?>
                    <div class="flex flex-col p-10 bg-white/10 backdrop-blur-md rounded-[2.5rem] border border-white/20">
                        <div class="flex items-center gap-5 mb-6">
                            <div class="w-16 h-16 shrink-0 rounded-2xl bg-<?php echo isims_color($item); ?> flex items-center justify-center shadow-lg">
                                <span class="material-symbols-outlined text-4xl text-white"><?php echo strip_tags($item['item_icon'] ?: 'help'); ?></span>
                            </div>
                            <div>
                                <?php if (!empty($item['item_subtitle'])): ?>
                                <p class="text-base font-black uppercase tracking-widest text-blue-200"><?php echo strip_tags($item['item_subtitle']); ?></p>
                                <?php endif; ?>
                                <h4 class="text-2xl md:text-3xl font-black text-white"><?php echo strip_tags($item['item_title']); ?></h4>
                            </div>
                        </div>
                        <p class="text-xl text-blue-100 leading-relaxed font-medium mb-8 flex-grow"><?php echo strip_tags($item['item_description']); ?></p>
                        <?php if ($link !== ''): ?>
                        <a href="<?php echo htmlspecialchars($link); ?>"<?php echo $external ? ' target="_blank" rel="noopener"' : ''; ?>
                           class="inline-flex items-center justify-center gap-3 px-8 py-5 bg-white text-blue-900 text-xl font-black rounded-2xl hover:bg-yellow-400 transition-all shadow-lg">
                            <?php echo strip_tags($item['item_stat_value'] ?: 'Open'); ?>
                            <span class="material-symbols-outlined text-2xl"><?php echo $external ? 'open_in_new' : 'arrow_forward'; ?></span>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($hero['help_title'])): ?>
            <div class="max-w-3xl mx-auto mt-12 p-10 bg-white/5 rounded-[2.5rem] border border-white/10 text-center">
                <h4 class="text-3xl font-black text-yellow-400 mb-4"><?php echo strip_tags($hero['help_title']); ?></h4>
                <p class="text-xl text-blue-100 leading-relaxed font-medium"><?php echo strip_tags($hero['help_description'] ?? ''); ?></p>
                <?php if (!empty($hero['help_phone'])): ?>
                <p class="mt-4 text-3xl text-white font-bold"><?php echo strip_tags($hero['help_phone']); ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<script>
    // Embedded guide reader: the tabs and each card's "Preview" button swap the PDF shown.
    (function () {
        var frame = document.getElementById('guide-frame');
        if (!frame) return;
        var tabs = document.querySelectorAll('.preview-tab');

        function show(index, scroll) {
            tabs.forEach(function (tab) {
                var active = tab.getAttribute('data-preview') === String(index);
                tab.setAttribute('aria-selected', active ? 'true' : 'false');
                if (active) {
                    frame.src = tab.getAttribute('data-src') + '#view=FitH';
                    frame.title = tab.textContent.trim();
                }
            });
            if (scroll) {
                document.getElementById('guide-preview').scrollIntoView({ behavior: 'smooth' });
            }
        }

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () { show(tab.getAttribute('data-preview'), false); });
        });
        document.querySelectorAll('.preview-trigger').forEach(function (btn) {
            btn.addEventListener('click', function () { show(btn.getAttribute('data-preview'), true); });
        });
    })();
</script>

<?php include 'includes/footer.php'; ?>
