<?php
$page_title = "Staff Encyclopedia - Valley View University";
$active_page = "about";
include 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'includes/directory_helper.php';

// ---------------------------------------------------------------------------
// Page copy (hero + CTA) — managed from admin/manage_encyclopedia_content.php
// ---------------------------------------------------------------------------
$content_stmt = $pdo->prepare("SELECT * FROM encyclopedia_content WHERE page_key = 'staff'");
$content_stmt->execute();
$page_content = $content_stmt->fetch(PDO::FETCH_ASSOC) ?: [];
$page_content += [
    'hero_title'    => 'Staff Encyclopedia',
    'hero_subtitle' => 'Meet the dedicated administrative professionals who keep our university running smoothly.',
    'hero_image'    => '',
    'cta_title'     => 'Join Our Administrative Team',
    'cta_subtitle'  => 'Help us build a better university environment for our students and staff.',
];

// ---------------------------------------------------------------------------
// Staff tiers. `staff_category` is set from the admin panel; anything left
// blank still shows up, under "Other Staff", so nobody silently disappears.
// ---------------------------------------------------------------------------
$CATEGORIES = [
    'senior_member' => [
        'label' => 'Non-Teaching Senior Members',
        'blurb' => 'Principal officers, directors and senior members leading the university’s administrative directorates.',
        'icon'  => 'workspace_premium',
    ],
    'senior_staff' => [
        'label' => 'Senior Staff',
        'blurb' => 'Chief, principal and senior assistants running the day-to-day work of every office, hall and laboratory.',
        'icon'  => 'badge',
    ],
    'junior_staff' => [
        'label' => 'Junior Staff',
        'blurb' => 'The security, catering, transport, works and grounds teams who keep the campus safe, fed and running.',
        'icon'  => 'engineering',
    ],
    'other' => [
        'label' => 'Other Staff',
        'blurb' => 'Members of staff not yet assigned to a category.',
        'icon'  => 'group',
    ],
];

// ---------------------------------------------------------------------------
// Filters
// ---------------------------------------------------------------------------
$search   = trim((string) ($_GET['search'] ?? ''));
$unit     = trim((string) ($_GET['unit'] ?? ''));
$category = trim((string) ($_GET['category'] ?? ''));
if ($category !== '' && !isset($CATEGORIES[$category])) {
    $category = '';
}

$query  = "SELECT * FROM directory WHERE type = 'staff' AND is_active = 1";
$params = [];

if ($search !== '') {
    $query .= " AND (name LIKE ? OR job_title LIKE ? OR department LIKE ?)";
    array_push($params, "%$search%", "%$search%", "%$search%");
}
if ($unit !== '') {
    $query .= " AND department = ?";
    $params[] = $unit;
}
if ($category !== '') {
    $query .= $category === 'other'
        ? " AND (staff_category IS NULL OR staff_category = '' OR staff_category NOT IN ('senior_member','senior_staff','junior_staff'))"
        : " AND staff_category = ?";
    if ($category !== 'other') {
        $params[] = $category;
    }
}

// sort_order carries the official running order of the ITS roll.
$query .= " ORDER BY sort_order ASC, name ASC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filter option lists — always the full set, never just what's on screen.
$units = $pdo->query("SELECT DISTINCT department FROM directory WHERE type = 'staff' AND is_active = 1 AND department <> '' ORDER BY department")->fetchAll(PDO::FETCH_COLUMN);

$counts_raw = $pdo->query("SELECT IFNULL(NULLIF(staff_category,''),'other') AS cat, COUNT(*) AS n
                             FROM directory WHERE type = 'staff' AND is_active = 1 GROUP BY cat")->fetchAll(PDO::FETCH_KEY_PAIR);
$counts = [];
foreach ($CATEGORIES as $key => $_) {
    $counts[$key] = (int) ($counts_raw[$key] ?? 0);
}
$total_staff = array_sum($counts);

// Group the result set: category -> unit -> people
$grouped = [];
foreach ($staff as $row) {
    $cat = (string) ($row['staff_category'] ?? '');
    if (!isset($CATEGORIES[$cat]) || $cat === 'other') {
        $cat = 'other';
    }
    $u = $row['department'] !== '' ? $row['department'] : 'General Administration';
    $grouped[$cat][$u][] = $row;
}
// Keep the tiers in rank order regardless of what the query returned. Senior
// members keep the directorate order of the official roll (Vice Chancellery
// first); the other tiers read better with their units in A–Z order.
$ordered = [];
foreach (array_keys($CATEGORIES) as $key) {
    if (empty($grouped[$key])) {
        continue;
    }
    if ($key !== 'senior_member') {
        ksort($grouped[$key]);
    }
    $ordered[$key] = $grouped[$key];
}

$has_filters = ($search !== '' || $unit !== '' || $category !== '');
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
</style>
<?php vvu_dir_styles('#002147', '#00366f'); ?>

<main class="flex-grow bg-gray-50 dark:bg-gray-900 pb-20 dir-scope">
    <!-- Hero Section (Directly from faqs_about_vvu.php design) -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_content['hero_image'] ?: 'images/home-2.jpg'); ?>"
                 alt="Staff Hero" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>

        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-6 py-2 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xs md:text-sm font-black tracking-widest uppercase text-yellow-400">Administrative Directory</span>
                </div>

                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($page_content['hero_title']); ?> <br>
                    <span class="text-3xl sm:text-4xl md:text-5xl lg:text-5xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-2">Staff Profiles</span>
                </h1>

                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-3xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($page_content['hero_subtitle']); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Search / Filter -->
    <section class="relative z-20 -mt-20 md:-mt-24">
        <div class="container px-4">
            <div class="dir-wrap">
                <form action="staff_encyclopedia.php" method="GET" class="dir-panel m-0">
                    <div class="dir-panel__grid">
                        <label class="dir-field">
                            <span class="material-symbols-outlined">person_search</span>
                            <input type="text" name="search" data-dir-search
                                   class="dir-field__control"
                                   placeholder="Search by name, job title or unit…"
                                   value="<?php echo vvu_dir_e($search); ?>"
                                   autocomplete="off">
                        </label>

                        <label class="dir-field">
                            <span class="material-symbols-outlined">badge</span>
                            <select name="category" class="dir-field__control browser-default">
                                <option value="">All Categories</option>
                                <?php foreach ($CATEGORIES as $key => $meta): ?>
                                    <?php if ($counts[$key] === 0 && $category !== $key) continue; ?>
                                    <option value="<?php echo vvu_dir_e($key); ?>" <?php echo $category === $key ? 'selected' : ''; ?>><?php echo vvu_dir_e($meta['label']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <label class="dir-field">
                            <span class="material-symbols-outlined">apartment</span>
                            <select name="unit" class="dir-field__control browser-default">
                                <option value="">All Departments</option>
                                <?php foreach ($units as $u): ?>
                                    <option value="<?php echo vvu_dir_e($u); ?>" <?php echo $unit === $u ? 'selected' : ''; ?>><?php echo vvu_dir_e($u); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <button type="submit" class="dir-btn">
                            <span class="material-symbols-outlined">search</span>
                            Search
                        </button>
                    </div>

                    <div class="dir-panel__foot">
                        <p class="m-0 font-semibold" style="font-size:12px;" style="color:var(--muted)">
                            Browse the full university staff roll by category, department or name.
                        </p>
                        <div class="flex items-center gap-3">
                            <span class="dir-count" data-dir-count><b><?php echo count($staff); ?></b> of <?php echo $total_staff; ?> listed</span>
                            <?php if ($has_filters): ?>
                                <a href="staff_encyclopedia.php" class="dir-btn dir-btn--ghost" style="height:42px;font-size:12px;padding:0 18px;">
                                    <span class="material-symbols-outlined" style="font-size:17px;">restart_alt</span>
                                    Reset
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="dir-block">
        <div class="container px-4">
            <div class="dir-wrap dir-stats">
                <div class="dir-stat">
                    <span class="dir-stat__icon"><span class="material-symbols-outlined">diversity_3</span></span>
                    <div>
                        <div class="dir-stat__num"><?php echo $total_staff; ?></div>
                        <div class="dir-stat__label">Total Staff</div>
                    </div>
                </div>
                <?php foreach (['senior_member', 'senior_staff', 'junior_staff'] as $key): ?>
                    <div class="dir-stat">
                        <span class="dir-stat__icon"><span class="material-symbols-outlined"><?php echo $CATEGORIES[$key]['icon']; ?></span></span>
                        <div>
                            <div class="dir-stat__num"><?php echo $counts[$key]; ?></div>
                            <div class="dir-stat__label"><?php echo vvu_dir_e($CATEGORIES[$key]['label']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Category tabs -->
    <section class="dir-block--tight">
        <div class="container px-4">
            <div class="dir-wrap dir-tabs">
                <a href="staff_encyclopedia.php" class="dir-tab <?php echo $category === '' ? 'is-active' : ''; ?>">
                    <span class="material-symbols-outlined" style="font-size:17px;">apps</span>
                    All Staff
                    <span class="dir-tab__n"><?php echo $total_staff; ?></span>
                </a>
                <?php foreach ($CATEGORIES as $key => $meta): ?>
                    <?php if ($counts[$key] === 0) continue; ?>
                    <a href="staff_encyclopedia.php?category=<?php echo urlencode($key); ?>"
                       class="dir-tab <?php echo $category === $key ? 'is-active' : ''; ?>">
                        <span class="material-symbols-outlined" style="font-size:17px;"><?php echo $meta['icon']; ?></span>
                        <?php echo vvu_dir_e($meta['label']); ?>
                        <span class="dir-tab__n"><?php echo $counts[$key]; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Staff grid, split by category -->
    <section class="dir-block--cards">
        <div class="container px-4">
            <div class="dir-wrap">
                <?php if (empty($staff)): ?>
                    <div class="dir-empty">
                        <div class="dir-empty__icon"><span class="material-symbols-outlined">search_off</span></div>
                        <h3>No staff members found</h3>
                        <p>Try a different name, category or department.</p>
                        <a href="staff_encyclopedia.php" class="dir-btn">
                            <span class="material-symbols-outlined">restart_alt</span> Reset All Filters
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($ordered as $catKey => $unitsInCat): ?>
                        <?php
                        $meta = $CATEGORIES[$catKey];
                        $catCount = array_sum(array_map('count', $unitsInCat));
                        ?>
                        <section class="dir-section" id="<?php echo vvu_dir_e($catKey); ?>" data-dir-section>
                            <div class="dir-section__head">
                                <span class="dir-section__bar"></span>
                                <div>
                                    <h2 class="dir-section__title">
                                        <span class="material-symbols-outlined align-middle" style="font-size:1em;color:var(--accent);vertical-align:-3px;"><?php echo $meta['icon']; ?></span>
                                        <?php echo vvu_dir_e($meta['label']); ?>
                                    </h2>
                                    <p class="dir-section__meta">
                                        <?php echo $catCount; ?> member<?php echo $catCount === 1 ? '' : 's'; ?>
                                        &middot; <?php echo count($unitsInCat); ?> unit<?php echo count($unitsInCat) === 1 ? '' : 's'; ?>
                                        &nbsp;—&nbsp; <?php echo vvu_dir_e($meta['blurb']); ?>
                                    </p>
                                </div>
                                <span class="dir-section__rule"></span>
                            </div>

                            <?php foreach ($unitsInCat as $unitName => $people): ?>
                                <div data-dir-group>
                                    <h3 class="dir-subhead">
                                        <?php echo vvu_dir_e($unitName); ?>
                                        <span class="dir-subhead__n"><?php echo count($people); ?></span>
                                    </h3>
                                    <div class="dir-grid">
                                        <?php foreach ($people as $person) { vvu_dir_card($person, 'staff'); } ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </section>
                    <?php endforeach; ?>

                    <div class="dir-empty" data-dir-empty hidden>
                        <div class="dir-empty__icon"><span class="material-symbols-outlined">search_off</span></div>
                        <h3>Nothing matches that search</h3>
                        <p>Clear the search box to see every staff member again.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section (Directly from faqs_about_vvu.php design) -->
    <section class="py-24 bg-white dark:bg-gray-900 mt-20">
        <div class="container">
            <div class="max-w-5xl mx-auto text-center glass p-20 rounded-[4rem] shadow-2xl">
                <h2 class="text-5xl sm:text-6xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($page_content['cta_title']); ?></h2>
                <p class="text-2xl text-gray-600 dark:text-gray-400 mb-12 font-medium leading-relaxed">
                    <?php echo strip_tags($page_content['cta_subtitle']); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="contact_us.php" class="px-12 py-6 bg-blue-600 hover:bg-blue-700 text-white text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl">mail</span>
                        Contact Administration
                    </a>
                </div>
            </div>
        </div>
    </section>

    <button type="button" class="dir-backtotop" data-dir-top aria-label="Back to top">
        <span class="material-symbols-outlined">arrow_upward</span>
    </button>
</main>

<?php vvu_dir_script(); ?>
<?php include 'includes/footer.php'; ?>
