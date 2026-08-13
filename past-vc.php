<?php
require_once 'includes/db_connect.php';
require_once 'includes/administration_content_helper.php';

// ---------------------------------------------------------------
// Load CMS content for this page (managed from
// /admin/manage_administration_pages.php -> "Past Vice-Chancellors")
// ---------------------------------------------------------------
$content = new AdministrationContent($pdo);
$page = $content->getPageBySlug('past-vc');

$pageContent = [];
if ($page) {
    $pageContent = $content->getPageContent($page['id']);
}

if (!function_exists('getContent')) {
    function getContent($sections, $section_key, $field_key, $default = '') {
        return isset($sections[$section_key]['fields'][$field_key]) ? $sections[$section_key]['fields'][$field_key] : $default;
    }
}

// Clean value coming out of the rich-text editor (strips <p> wrappers etc.)
if (!function_exists('pvcText')) {
    function pvcText($sections, $section_key, $field_key, $default = '') {
        $raw = getContent($sections, $section_key, $field_key, $default);
        return AdministrationContent::cleanHtml($raw);
    }
}

// Resolve an image value that may be a full URL or a project-relative path
if (!function_exists('pvcImage')) {
    function pvcImage($value, $fallback = 'images/past-vice-chancellors/dummy.jpg') {
        $value = trim(strip_tags($value));
        return $value !== '' ? $value : $fallback;
    }
}

// ---------------------------------------------------------------
// Collect the leader entries (sections keyed leader_1, leader_2, ...)
// A leader with an empty name is treated as hidden.
// ---------------------------------------------------------------
$leaders = [];
foreach ($pageContent as $key => $section) {
    if (strpos($key, 'leader_') !== 0) continue;
    $name = AdministrationContent::cleanHtml($section['fields']['name'] ?? '');
    if ($name === '') continue;

    $leaders[] = [
        'order'  => (int) $section['content_order'],
        'name'   => $name,
        'title'  => AdministrationContent::cleanHtml($section['fields']['title'] ?? ''),
        'tenure' => AdministrationContent::cleanHtml($section['fields']['tenure'] ?? ''),
        'note'   => AdministrationContent::cleanHtml($section['fields']['note'] ?? ''),
        'photo'  => pvcImage($section['fields']['photo'] ?? ''),
    ];
}
usort($leaders, function ($a, $b) { return $a['order'] <=> $b['order']; });

$page_title  = ($page ? $page['page_title'] : 'Past Vice-Chancellors') . ' - Valley View University';
$active_page = 'about';
include 'includes/header.php';
?>

<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slowZoom { 0% { transform: scale(1); } 100% { transform: scale(1.1); } }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp .7s ease-out both; }

    /* ---- Timeline ---- */
    .pvc-card { transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease; }
    .pvc-card:hover { transform: translateY(-6px); box-shadow: 0 26px 50px -22px rgba(15, 23, 42, .45); }
    .pvc-photo { transition: transform .5s ease; }
    .pvc-card:hover .pvc-photo { transform: scale(1.06); }

    /* Keep long tenure strings from wrapping awkwardly */
    .pvc-tenure { font-variant-numeric: tabular-nums; letter-spacing: .02em; }

    /* Roll of honour table -> stacked cards on small screens */
    @media (max-width: 640px) {
        .pvc-roll thead { display: none; }
        .pvc-roll tr { display: block; padding: 14px 4px; border-bottom: 1px solid rgba(148,163,184,.25); }
        .pvc-roll td { display: flex; justify-content: space-between; gap: 16px; padding: 4px 0; border: 0; }
        .pvc-roll td::before { content: attr(data-label); font-weight: 700; color: #64748b; flex: 0 0 auto; }
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">

    <!-- ============================= HERO ============================= -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo htmlspecialchars(pvcImage(getContent($pageContent, 'hero_section', 'background_image', ''), 'images/home-1.jpg')); ?>"
                 alt="Valley View University campus" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>

        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo htmlspecialchars(pvcText($pageContent, 'hero_section', 'badge_text', 'Our Heritage')); ?></span>
                </div>

                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay:.1s;">
                    <?php echo htmlspecialchars(pvcText($pageContent, 'hero_section', 'title_main', 'Past Vice')); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo htmlspecialchars(pvcText($pageContent, 'hero_section', 'title_highlight', 'Chancellors')); ?></span>
                </h1>

                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay:.2s;">
                    "<?php echo htmlspecialchars(pvcText($pageContent, 'hero_section', 'subtitle', 'From a modest seminary in Bekwai to Ghana\'s first chartered private university.')); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- ============================= MILESTONE STATS ============================= -->
    <section class="relative z-20 -mt-16 sm:-mt-20">
        <div class="container">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-6 sm:p-10 border border-gray-100 dark:border-gray-700">
                <?php for ($i = 1; $i <= 4; $i++):
                    $val = pvcText($pageContent, 'stats_section', "stat_{$i}_val");
                    $lbl = pvcText($pageContent, 'stats_section', "stat_{$i}_label");
                    if ($val === '' && $lbl === '') continue;
                ?>
                <div class="text-center px-2">
                    <div class="text-3xl sm:text-4xl lg:text-5xl font-black text-blue-700 dark:text-yellow-400 leading-none mb-2"><?php echo htmlspecialchars($val); ?></div>
                    <div class="text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 leading-snug"><?php echo htmlspecialchars($lbl); ?></div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <!-- ============================= INTRODUCTION ============================= -->
    <section class="py-20 sm:py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-12">
                <span class="inline-block px-5 py-2 mb-6 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-sm font-black uppercase tracking-widest">
                    <?php echo htmlspecialchars(pvcText($pageContent, 'introduction', 'section_label', 'The Story So Far')); ?>
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 dark:text-white mb-6 tracking-tight">
                    <?php echo htmlspecialchars(pvcText($pageContent, 'introduction', 'section_title', 'A Legacy Built by Many Hands')); ?>
                </h2>
                <div class="h-1.5 w-28 bg-yellow-400 mx-auto rounded-full"></div>
            </div>

            <div class="max-w-4xl mx-auto space-y-6">
                <?php for ($i = 1; $i <= 4; $i++):
                    $para = pvcText($pageContent, 'introduction', "paragraph_{$i}");
                    if ($para === '') continue;
                    $isLead = ($i === 1);
                ?>
                <p class="<?php echo $isLead ? 'text-lg sm:text-xl text-gray-800 dark:text-gray-200 font-medium border-l-4 border-blue-600 pl-6' : 'text-base sm:text-lg text-gray-600 dark:text-gray-400'; ?> leading-relaxed">
                    <?php echo htmlspecialchars($para); ?>
                </p>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <!-- ============================= LEADERSHIP TIMELINE ============================= -->
    <section class="py-20 sm:py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-3xl mx-auto text-center mb-16">
                <span class="inline-block px-5 py-2 mb-6 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 text-sm font-black uppercase tracking-widest">
                    <?php echo htmlspecialchars(pvcText($pageContent, 'timeline_section', 'section_label', 'Leadership Timeline')); ?>
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 dark:text-white mb-6 tracking-tight">
                    <?php echo htmlspecialchars(pvcText($pageContent, 'timeline_section', 'section_title', 'Those Who Led the Way')); ?>
                </h2>
                <p class="text-base sm:text-lg text-gray-600 dark:text-gray-400 leading-relaxed">
                    <?php echo htmlspecialchars(pvcText($pageContent, 'timeline_section', 'section_subtitle', '')); ?>
                </p>
            </div>

            <?php if (empty($leaders)): ?>
                <p class="text-center text-gray-500 dark:text-gray-400">Leadership records are being updated. Please check back soon.</p>
            <?php else: ?>
            <div class="relative max-w-6xl mx-auto">
                <!-- vertical rail -->
                <div class="absolute top-2 bottom-2 left-5 lg:left-1/2 w-1 -translate-x-1/2 rounded-full bg-gradient-to-b from-blue-600 via-blue-400 to-yellow-400 opacity-70"></div>

                <?php foreach ($leaders as $i => $l):
                    $left    = ($i % 2 === 0);
                    $current = (stripos($l['tenure'], 'present') !== false || stripos($l['tenure'], 'current') !== false);
                    $accent  = $current ? 'yellow' : 'blue';
                ?>
                <div class="relative pl-14 sm:pl-20 lg:pl-0 mb-10 lg:mb-14 lg:grid lg:grid-cols-2 lg:gap-16 lg:items-center">

                    <!-- node -->
                    <div class="absolute left-5 lg:left-1/2 -translate-x-1/2 top-8 lg:top-1/2 lg:-translate-y-1/2 z-10">
                        <span class="block w-5 h-5 rounded-full border-4 shadow-lg <?php echo $current ? 'bg-yellow-400 border-yellow-200 animate-pulse' : 'bg-white dark:bg-gray-900 border-blue-600'; ?>"></span>
                    </div>

                    <!-- card -->
                    <div class="<?php echo $left ? 'lg:col-start-1 lg:row-start-1' : 'lg:col-start-2 lg:row-start-1'; ?>">
                        <article class="pvc-card group bg-white dark:bg-gray-900 rounded-3xl shadow-lg border <?php echo $current ? 'border-yellow-300 dark:border-yellow-600/60 ring-2 ring-yellow-400/40' : 'border-gray-100 dark:border-gray-800'; ?> overflow-hidden">
                            <div class="flex flex-col sm:flex-row">
                                <!-- portrait -->
                                <div class="relative sm:w-44 lg:w-48 shrink-0 overflow-hidden bg-gray-100 dark:bg-gray-800">
                                    <img src="<?php echo htmlspecialchars($l['photo']); ?>"
                                         alt="<?php echo htmlspecialchars($l['name']); ?>"
                                         loading="lazy"
                                         onerror="this.onerror=null;this.src='images/past-vice-chancellors/dummy.jpg';"
                                         class="pvc-photo w-full h-56 sm:h-full object-cover object-top">
                                    <?php if ($current): ?>
                                    <span class="absolute top-3 left-3 px-3 py-1 rounded-full bg-yellow-400 text-blue-900 text-[11px] font-black uppercase tracking-wider shadow">In Office</span>
                                    <?php endif; ?>
                                </div>

                                <!-- details -->
                                <div class="p-6 sm:p-7 flex-1">
                                    <span class="pvc-tenure inline-flex items-center gap-2 px-3 py-1 mb-3 rounded-lg text-xs font-black uppercase tracking-wider <?php echo $current ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300' : 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'; ?>">
                                        <span class="material-symbols-outlined text-base leading-none">calendar_month</span>
                                        <?php echo htmlspecialchars($l['tenure']); ?>
                                    </span>

                                    <h3 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white leading-tight mb-1">
                                        <?php echo htmlspecialchars($l['name']); ?>
                                    </h3>
                                    <p class="text-sm font-bold uppercase tracking-wider <?php echo $current ? 'text-yellow-600 dark:text-yellow-400' : 'text-blue-600 dark:text-blue-400'; ?> mb-4">
                                        <?php echo htmlspecialchars($l['title']); ?>
                                    </p>

                                    <?php if ($l['note'] !== ''): ?>
                                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 leading-relaxed">
                                        <?php echo htmlspecialchars($l['note']); ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ============================= ROLL OF HONOUR ============================= -->
    <?php if (!empty($leaders)): ?>
    <section class="py-20 sm:py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-3xl mx-auto text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white mb-4 tracking-tight">Roll of Honour</h2>
                <div class="h-1.5 w-24 bg-blue-600 mx-auto rounded-full"></div>
            </div>

            <div class="max-w-4xl mx-auto overflow-x-auto rounded-3xl border border-gray-100 dark:border-gray-800 shadow-lg">
                <table class="pvc-roll w-full text-left border-collapse bg-white dark:bg-gray-900">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-gray-500 dark:text-gray-400">Name</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-gray-500 dark:text-gray-400">Designation</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-gray-500 dark:text-gray-400">Period</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leaders as $l): ?>
                        <tr class="border-t border-gray-100 dark:border-gray-800 hover:bg-blue-50/50 dark:hover:bg-gray-800/50 transition-colors">
                            <td data-label="Name" class="px-6 py-4 font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($l['name']); ?></td>
                            <td data-label="Designation" class="px-6 py-4 text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($l['title']); ?></td>
                            <td data-label="Period" class="pvc-tenure px-6 py-4 font-semibold text-blue-700 dark:text-blue-300 whitespace-nowrap"><?php echo htmlspecialchars($l['tenure']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ============================= CTA ============================= -->
    <section class="relative py-20 sm:py-24 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>

        <div class="container relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white mb-6 leading-tight tracking-tight">
                    <?php echo htmlspecialchars(pvcText($pageContent, 'cta_section', 'cta_title', 'Continuing a Legacy of')); ?>
                    <span class="text-yellow-400 block mt-2"><?php echo htmlspecialchars(pvcText($pageContent, 'cta_section', 'cta_highlight', 'Excellence and Service')); ?></span>
                </h2>
                <p class="text-base sm:text-lg lg:text-xl text-blue-100 mb-10 max-w-2xl mx-auto leading-relaxed">
                    <?php echo htmlspecialchars(pvcText($pageContent, 'cta_section', 'cta_description', '')); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo htmlspecialchars(pvcText($pageContent, 'cta_section', 'button_1_url', 'office_of_the_vice_chancellor.php')); ?>"
                       class="px-8 py-4 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-base font-black rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined">account_balance</span>
                        <?php echo htmlspecialchars(pvcText($pageContent, 'cta_section', 'button_1_text', 'Office of the Vice-Chancellor')); ?>
                    </a>
                    <a href="<?php echo htmlspecialchars(pvcText($pageContent, 'cta_section', 'button_2_url', 'history.php')); ?>"
                       class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white text-base font-black rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined">history_edu</span>
                        <?php echo htmlspecialchars(pvcText($pageContent, 'cta_section', 'button_2_text', 'Our History')); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
