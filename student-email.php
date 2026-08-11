<?php
/**
 * Student Email
 *
 * Every string on this page comes from the academic_pages_* tables and is
 * edited at admin/manage_departmental_resources.php?page=student_email.
 * Seed content is installed by dev-tools/migrate_student_email_ischool.php.
 */

require_once 'includes/db_connect.php';

$page_key = 'student_email';

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

$page_title  = $hero['meta_title'] ?? 'Student Email - Valley View University';
$active_page = "students";

/** Section heading helper — falls back to the supplied default when unset. */
function se_section($sections, $key, $field, $default = '') {
    $value = $sections[$key][$field] ?? '';
    return $value !== '' ? $value : $default;
}

/**
 * Links are stored by an admin and may be an external URL, a site-relative
 * path, a mailto: or a tel:. Anything that is not already absolute is treated
 * as relative to the site root. javascript: and data: URLs are rejected so a
 * stored link can never become script injection.
 */
function se_link($url) {
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
    .mail-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .mail-card:hover {
        transform: translateY(-15px);
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
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[70vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image'] ?? 'https://images.unsplash.com/photo-1596526131083-e8c633c948d2?auto=format&fit=crop&q=80&w=1920'); ?>"
                 alt="Student Email" class="w-full h-full object-cover animate-slow-zoom opacity-50">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>

        <div class="container relative z-10 py-24">
            <div class="max-w-6xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['hero_badge'] ?? 'Student Communication'); ?></span>
                </div>

                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['hero_title'] ?? 'Student'); ?> <br>
                    <span class="text-3xl sm:text-4xl md:text-5xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($hero['hero_subtitle'] ?? 'Email Account'); ?></span>
                </h1>

                <p class="text-xl sm:text-2xl md:text-3xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($hero['hero_description'] ?? ''); ?>
                </p>

                <?php if (!empty($hero['cta_button_link'])): ?>
                <!-- Primary Gmail sign-in call to action -->
                <div class="mt-12 animate-fadeInUp" style="animation-delay: 0.3s;">
                    <a href="<?php echo htmlspecialchars(se_link($hero['cta_button_link'])); ?>" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-4 px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-2xl font-black rounded-2xl transition-all transform hover:scale-105 shadow-2xl">
                        <span class="material-symbols-outlined text-3xl">mail</span>
                        <?php echo strip_tags($hero['cta_button_text'] ?? 'Open Student Email'); ?>
                        <span class="material-symbols-outlined text-3xl">open_in_new</span>
                    </a>
                </div>
                <?php endif; ?>

                <?php if ($stats): ?>
                <!-- Quick Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-20 max-w-5xl mx-auto">
                    <?php foreach ($stats as $stat): ?>
                    <div class="px-8 py-10 bg-white/5 backdrop-blur-md rounded-[2.5rem] border border-white/10 shadow-xl group hover:bg-white/10 transition-all">
                        <span class="material-symbols-outlined text-yellow-400 text-4xl mb-3 group-hover:scale-110 transition-transform"><?php echo strip_tags($stat['stat_icon'] ?? 'star'); ?></span>
                        <p class="text-3xl font-black text-white mb-1 break-words"><?php echo strip_tags($stat['stat_value']); ?></p>
                        <p class="text-lg text-blue-200 font-bold uppercase tracking-widest"><?php echo strip_tags($stat['stat_label']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Access Your Mailbox -->
    <?php if (!empty($items['access'])): ?>
    <section class="py-24 bg-white dark:bg-gray-900 relative z-20 -mt-20 mx-auto max-w-[95%] rounded-[3rem] shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-800">
        <div class="w-full px-8 md:px-16">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(se_section($sections, 'access', 'section_title', 'Access Your Mailbox')); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-bold leading-relaxed"><?php echo strip_tags(se_section($sections, 'access', 'section_description')); ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <?php foreach ($items['access'] as $item): ?>
                <?php $link = se_link($item['item_link']); ?>
                <div class="mail-card group p-10 bg-gray-50 dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 flex flex-col items-center text-center">
                    <div class="icon-container bg-<?php echo strip_tags($item['item_color'] ?: 'blue-600'); ?>">
                        <span class="material-symbols-outlined"><?php echo strip_tags($item['item_icon'] ?: 'mail'); ?></span>
                    </div>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($item['item_title']); ?></h3>
                    <p class="text-2xl text-gray-600 dark:text-gray-400 mb-10 flex-grow leading-relaxed font-medium">
                        <?php echo strip_tags($item['item_description']); ?>
                    </p>
                    <?php if ($link !== ''): ?>
                    <a href="<?php echo htmlspecialchars($link); ?>" target="_blank" rel="noopener"
                       class="w-full py-6 bg-<?php echo strip_tags($item['item_color'] ?: 'blue-600'); ?> text-white text-2xl font-bold rounded-2xl hover:opacity-90 transition-all shadow-lg flex items-center justify-center gap-3">
                        <?php echo strip_tags($item['item_stat_value'] ?: 'Open'); ?>
                        <span class="material-symbols-outlined text-3xl">open_in_new</span>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Activation Steps -->
    <?php if (!empty($items['activation'])): ?>
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="w-full max-w-[95%] mx-auto px-8 md:px-16">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(se_section($sections, 'activation', 'section_title', 'Activating Your Account')); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-bold leading-relaxed"><?php echo strip_tags(se_section($sections, 'activation', 'section_description')); ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-6xl mx-auto">
                <?php foreach ($items['activation'] as $i => $item): ?>
                <div class="relative text-center group">
                    <div class="w-28 h-28 rounded-3xl bg-<?php echo strip_tags($item['item_color'] ?: 'blue-600'); ?> flex items-center justify-center text-white text-5xl font-black mx-auto mb-8 group-hover:scale-110 transition-transform shadow-xl">
                        <?php echo strip_tags($item['item_stat_value'] !== '' ? $item['item_stat_value'] : ($i + 1)); ?>
                    </div>
                    <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($item['item_title']); ?></h4>
                    <p class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium"><?php echo strip_tags($item['item_description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Setup Guides -->
    <?php if (!empty($items['guides'])): ?>
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="w-full max-w-[95%] mx-auto px-8 md:px-16">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(se_section($sections, 'guides', 'section_title', 'Setup Guides')); ?></h2>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-bold leading-relaxed"><?php echo strip_tags(se_section($sections, 'guides', 'section_description')); ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <?php foreach ($items['guides'] as $item): ?>
                <?php $link = se_link($item['item_link']); ?>
                <div class="flex flex-col gap-6 p-10 bg-gray-50 dark:bg-gray-800 rounded-[2.5rem] shadow-lg border border-gray-100 dark:border-gray-700">
                    <div class="w-24 h-24 shrink-0 rounded-3xl bg-<?php echo strip_tags($item['item_color'] ?: 'blue-600'); ?> flex items-center justify-center text-white shadow-lg">
                        <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($item['item_icon'] ?: 'description'); ?></span>
                    </div>
                    <div class="flex-grow">
                        <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($item['item_title']); ?></h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium"><?php echo strip_tags($item['item_description']); ?></p>
                    </div>
                    <?php if ($link !== ''): ?>
                    <a href="<?php echo htmlspecialchars($link); ?>" target="_blank" rel="noopener"
                       class="inline-flex items-center justify-center gap-3 px-8 py-5 bg-<?php echo strip_tags($item['item_color'] ?: 'blue-600'); ?> text-white text-xl font-bold rounded-2xl hover:opacity-90 transition-all shadow-lg">
                        <?php echo strip_tags($item['item_stat_value'] ?: 'Download'); ?>
                        <span class="material-symbols-outlined text-2xl">download</span>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- What Your Email Is For -->
    <?php if (!empty($items['usage'])): ?>
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="w-full max-w-[95%] mx-auto px-8 md:px-16">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(se_section($sections, 'usage', 'section_title', 'What Your Email Is For')); ?></h2>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-bold leading-relaxed"><?php echo strip_tags(se_section($sections, 'usage', 'section_description')); ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <?php foreach ($items['usage'] as $item): ?>
                <?php $link = se_link($item['item_link']); ?>
                <div class="flex gap-8 p-10 bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-lg border border-gray-100 dark:border-gray-800">
                    <div class="w-24 h-24 shrink-0 rounded-3xl bg-<?php echo strip_tags($item['item_color'] ?: 'blue-600'); ?> flex items-center justify-center text-white shadow-lg">
                        <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($item['item_icon'] ?: 'check_circle'); ?></span>
                    </div>
                    <div>
                        <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($item['item_title']); ?></h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium"><?php echo strip_tags($item['item_description']); ?></p>
                        <?php if ($link !== ''): ?>
                        <a href="<?php echo htmlspecialchars($link); ?>" class="inline-flex items-center gap-2 mt-4 text-xl font-bold text-blue-600 dark:text-blue-400 hover:underline">
                            Visit
                            <span class="material-symbols-outlined text-2xl">arrow_forward</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Sign-in / Support CTA -->
    <section class="py-24 bg-blue-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>

        <div class="relative z-10 w-full max-w-[95%] mx-auto px-8 md:px-16">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-white mb-8"><?php echo strip_tags($hero['cta_title'] ?? 'Ready to Check Your Mail?'); ?></h2>
                <p class="text-2xl sm:text-3xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($hero['cta_subtitle'] ?? ''); ?>
                </p>

                <div class="flex flex-col sm:flex-row gap-6 justify-center mb-16">
                    <?php if (!empty($hero['cta_button_link'])): ?>
                    <a href="<?php echo htmlspecialchars(se_link($hero['cta_button_link'])); ?>" target="_blank" rel="noopener"
                       class="inline-flex items-center justify-center gap-4 px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-2xl font-black rounded-2xl transition-all transform hover:scale-105 shadow-xl">
                        <span class="material-symbols-outlined text-3xl">mail</span>
                        <?php echo strip_tags($hero['cta_button_text'] ?? 'Open Student Email'); ?>
                    </a>
                    <?php endif; ?>

                    <?php if (!empty($hero['cta_button_link_2'])): ?>
                    <a href="<?php echo htmlspecialchars(se_link($hero['cta_button_link_2'])); ?>" target="_blank" rel="noopener"
                       class="inline-flex items-center justify-center gap-4 px-12 py-6 bg-white/10 backdrop-blur-md border border-white/25 hover:bg-white/20 text-white text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl">
                        <span class="material-symbols-outlined text-3xl">menu_book</span>
                        <?php echo strip_tags($hero['cta_button_text_2'] ?? 'Activation Guide'); ?>
                    </a>
                    <?php endif; ?>
                </div>

                <?php if (!empty($hero['help_title'])): ?>
                <div class="max-w-3xl mx-auto p-12 bg-white/10 backdrop-blur-md rounded-[2.5rem] border border-white/20">
                    <h4 class="text-4xl font-black text-yellow-400 mb-6"><?php echo strip_tags($hero['help_title']); ?></h4>
                    <p class="text-2xl text-blue-100 leading-relaxed font-medium mb-6"><?php echo strip_tags($hero['help_description'] ?? ''); ?></p>
                    <?php if (!empty($hero['help_phone'])): ?>
                    <p class="text-3xl text-white font-bold"><?php echo strip_tags($hero['help_phone']); ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
