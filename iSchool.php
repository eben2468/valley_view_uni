<?php
/**
 * iSchool portal landing page.
 *
 * Every string on this page used to be hard-coded in the markup below. It now
 * comes from the academic_pages_* tables and is edited at
 * admin/manage_departmental_resources.php?page=ischool.
 * Seed content is installed by dev-tools/migrate_student_email_ischool.php.
 */

require_once 'includes/db_connect.php';

$page_key = 'ischool';

$stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = ? AND is_active = 1");
$stmt->execute([$page_key]);
$hero = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

$stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = ? AND is_active = 1 ORDER BY display_order");
$stmt->execute([$page_key]);

// Keyed by section_key so a heading can be pulled by name regardless of the
// order an admin has arranged the sections in.
$sections = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $sections[$row['section_key']] = $row;
}

$stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = ? AND is_active = 1 ORDER BY section_key, display_order");
$stmt->execute([$page_key]);

$items = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $item) {
    $items[$item['section_key']][] = $item;
}

$page_title  = $hero['meta_title'] ?? 'iSchool - Valley View University';
$active_page = "resources";

if (!function_exists('ischool_section')) {
    /** Section heading helper — falls back to the supplied default when unset. */
    function ischool_section($sections, $key, $field, $default = '') {
        $value = $sections[$key][$field] ?? '';
        return $value !== '' ? $value : $default;
    }

    /**
     * Links are admin-supplied and may be an external URL, a site-relative
     * path, a mailto: or a tel:. Unknown schemes (javascript:, data:) are
     * dropped so a stored link can never become script injection.
     */
    function ischool_link($url) {
        $url = trim((string) $url);
        if ($url === '') {
            return '';
        }
        if (preg_match('#^(https?://|mailto:|tel:|/)#i', $url)) {
            return $url;
        }
        if (preg_match('#^[a-z][a-z0-9+.-]*:#i', $url)) {
            return '';
        }
        return $url;
    }
}

// The portal grid shows the first three cards; any additional card (the
// graduation status checker) is rendered as the wide banner underneath.
$portals = $items['portals'] ?? [];
$portal_cards  = array_slice($portals, 0, 3);
$portal_banner = $portals[3] ?? null;

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
    .ischool-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .ischool-card:hover {
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
        color: white !important;
        font-size: 40px;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image'] ?? 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=1920'); ?>"
                 alt="Digital Campus" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>

        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['hero_badge'] ?? 'Student Online Services'); ?></span>
                </div>

                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['hero_title'] ?? 'iSchool'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($hero['hero_subtitle'] ?? 'Digital Portal'); ?></span>
                </h1>

                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($hero['hero_description'] ?? ''); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Main Portals Section -->
    <?php if ($portal_cards): ?>
    <section class="py-24 bg-white dark:bg-gray-900 relative z-20 -mt-20 mx-auto max-w-[95%] rounded-[3rem] shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-800">
        <div class="w-full px-8 md:px-16">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-6xl sm:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(ischool_section($sections, 'portals', 'section_title', 'Access Your Portal')); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-4xl text-gray-600 dark:text-gray-400 font-bold leading-relaxed"><?php echo strip_tags(ischool_section($sections, 'portals', 'section_description')); ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <?php foreach ($portal_cards as $item): ?>
                <?php
                    $link  = ischool_link($item['item_link']);
                    $color = strip_tags($item['item_color'] ?: 'blue-600');
                ?>
                <div class="ischool-card group p-10 bg-gray-50 dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 flex flex-col items-center text-center">
                    <div class="icon-container bg-<?php echo $color; ?>">
                        <span class="material-symbols-outlined"><?php echo strip_tags($item['item_icon'] ?: 'school'); ?></span>
                    </div>
                    <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($item['item_title']); ?></h3>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 mb-10 flex-grow leading-relaxed font-medium">
                        <?php echo strip_tags($item['item_description']); ?>
                    </p>
                    <?php if ($link !== ''): ?>
                    <a href="<?php echo htmlspecialchars($link); ?>" target="_blank" rel="noopener"
                       class="w-full py-6 bg-<?php echo $color; ?> text-white text-2xl font-bold rounded-2xl hover:opacity-90 transition-all shadow-lg flex items-center justify-center gap-3">
                        <?php echo strip_tags($item['item_stat_value'] ?: 'Launch Portal'); ?>
                        <span class="material-symbols-outlined text-3xl">open_in_new</span>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($portal_banner): ?>
            <!-- Graduation Status Checker (any 4th portal entry renders here) -->
            <?php $banner_link = ischool_link($portal_banner['item_link']); ?>
            <div class="mt-16 max-w-6xl mx-auto">
                <a href="<?php echo htmlspecialchars($banner_link); ?>" target="_blank" rel="noopener"
                   class="group flex flex-col md:flex-row items-center gap-10 p-10 md:p-14 rounded-[2.5rem] bg-gradient-to-br from-emerald-600 to-emerald-800 shadow-2xl transition-all hover:shadow-emerald-900/40 hover:-translate-y-1">
                    <div class="flex-shrink-0 w-28 h-28 rounded-[2rem] bg-white/15 backdrop-blur-md border border-white/25 flex items-center justify-center">
                        <span class="material-symbols-outlined text-white" style="font-size: 4rem;"><?php echo strip_tags($portal_banner['item_icon'] ?: 'verified'); ?></span>
                    </div>
                    <div class="flex-grow text-center md:text-left">
                        <?php if (!empty($portal_banner['item_subtitle'])): ?>
                        <span class="inline-block px-5 py-2 mb-4 rounded-full bg-white/20 text-white text-lg font-black uppercase tracking-widest"><?php echo strip_tags($portal_banner['item_subtitle']); ?></span>
                        <?php endif; ?>
                        <h3 class="text-4xl md:text-5xl font-black text-white mb-4"><?php echo strip_tags($portal_banner['item_title']); ?></h3>
                        <p class="text-2xl md:text-3xl text-emerald-50 font-medium leading-relaxed">
                            <?php echo strip_tags($portal_banner['item_description']); ?>
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center gap-3 px-10 py-6 bg-white text-emerald-800 text-2xl font-black rounded-2xl shadow-lg transition-transform group-hover:scale-105">
                            <?php echo strip_tags($portal_banner['item_stat_value'] ?: 'Check Status'); ?>
                            <span class="material-symbols-outlined text-3xl">open_in_new</span>
                        </span>
                    </div>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Key Features Section -->
    <?php if (!empty($items['features'])): ?>
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="w-full max-w-[95%] mx-auto px-8 md:px-16">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-6xl sm:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags(ischool_section($sections, 'features', 'section_title', 'What You Can Do')); ?></h2>
                <p class="text-4xl text-gray-600 dark:text-gray-400 font-bold leading-relaxed"><?php echo strip_tags(ischool_section($sections, 'features', 'section_description')); ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <?php foreach ($items['features'] as $item): ?>
                <div class="flex gap-8 p-10 bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-lg border border-gray-100 dark:border-gray-800">
                    <div class="w-24 h-24 shrink-0 rounded-3xl bg-<?php echo strip_tags($item['item_color'] ?: 'blue-600'); ?> flex items-center justify-center text-white shadow-lg">
                        <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($item['item_icon'] ?: 'check_circle'); ?></span>
                    </div>
                    <div>
                        <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($item['item_title']); ?></h4>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium"><?php echo strip_tags($item['item_description']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Support Section -->
    <section class="py-24 bg-blue-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>

        <div class="relative z-10 w-full max-w-[95%] mx-auto px-8 md:px-16">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-white mb-8"><?php echo strip_tags($hero['cta_title'] ?? 'Having Trouble?'); ?></h2>
                <p class="text-2xl sm:text-3xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($hero['cta_subtitle'] ?? ''); ?>
                </p>

                <?php if (!empty($items['support'])): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-12 max-w-6xl mx-auto">
                    <?php foreach ($items['support'] as $item): ?>
                    <?php $link = ischool_link($item['item_link']); ?>
                    <div class="p-12 bg-white/10 backdrop-blur-md rounded-[2.5rem] border border-white/20 text-left">
                        <h4 class="text-4xl font-black text-yellow-400 mb-6"><?php echo strip_tags($item['item_title']); ?></h4>
                        <?php if ($link !== ''): ?>
                            <a href="<?php echo htmlspecialchars($link); ?>" class="text-3xl text-white font-bold hover:text-yellow-300 transition-colors break-words"><?php echo strip_tags($item['item_description']); ?></a>
                        <?php else: ?>
                            <p class="text-3xl text-white font-bold break-words"><?php echo strip_tags($item['item_description']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($item['item_subtitle'])): ?>
                        <p class="text-2xl text-blue-200 mt-4 font-medium"><?php echo strip_tags($item['item_subtitle']); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($hero['cta_button_link'])): ?>
                <div class="mt-16">
                    <a href="<?php echo htmlspecialchars(ischool_link($hero['cta_button_link'])); ?>" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-4 px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl">
                        <?php echo strip_tags($hero['cta_button_text'] ?? 'Visit Help Desk'); ?>
                        <span class="material-symbols-outlined text-3xl">arrow_forward</span>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
