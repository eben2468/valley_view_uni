<?php
$page_title = "Faculty Encyclopedia - Valley View University";
$active_page = "academics";
include 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'includes/directory_helper.php';

// ---------------------------------------------------------------------------
// Page copy (hero + CTA) — managed from admin/manage_encyclopedia_content.php
// ---------------------------------------------------------------------------
$content_stmt = $pdo->prepare("SELECT * FROM encyclopedia_content WHERE page_key = 'faculty'");
$content_stmt->execute();
$page_content = $content_stmt->fetch(PDO::FETCH_ASSOC) ?: [];
$page_content += [
    'hero_title'    => 'Faculty Encyclopedia',
    'hero_subtitle' => 'Discover our distinguished team of academic professionals shaping the future.',
    'hero_image'    => '',
    'cta_title'     => 'Join Our Academic Community',
    'cta_subtitle'  => 'Are you passionate about education and research? Explore careers at Valley View University.',
];

// ---------------------------------------------------------------------------
// Filters
// ---------------------------------------------------------------------------
$search        = trim((string) ($_GET['search'] ?? ''));
$dept          = trim((string) ($_GET['department'] ?? ''));
$faculty_group = trim((string) ($_GET['faculty_group'] ?? ''));
$rank          = trim((string) ($_GET['rank'] ?? ''));

$query  = "SELECT * FROM directory WHERE type = 'faculty' AND is_active = 1";
$params = [];

if ($search !== '') {
    $query .= " AND (name LIKE ? OR job_title LIKE ? OR department LIKE ? OR faculty_group LIKE ?)";
    array_push($params, "%$search%", "%$search%", "%$search%", "%$search%");
}
if ($dept !== '') {
    $query .= " AND department = ?";
    $params[] = $dept;
}
if ($faculty_group !== '') {
    $query .= " AND faculty_group = ?";
    $params[] = $faculty_group;
}
if ($rank !== '') {
    $query .= " AND job_title LIKE ?";
    $params[] = "%$rank%";
}

// sort_order carries the official running order of the ITS roll, so faculties
// and departments come out in the sequence the university publishes them in.
$query .= " ORDER BY sort_order ASC, name ASC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$faculties = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filter option lists — always the full set, never just what's on screen.
$depts  = $pdo->query("SELECT DISTINCT department FROM directory WHERE type = 'faculty' AND is_active = 1 AND department <> '' ORDER BY department")->fetchAll(PDO::FETCH_COLUMN);
$groups = $pdo->query("SELECT faculty_group, COUNT(*) AS n FROM directory WHERE type = 'faculty' AND is_active = 1 AND faculty_group <> '' GROUP BY faculty_group ORDER BY MIN(sort_order) ASC, faculty_group ASC")->fetchAll(PDO::FETCH_ASSOC);

$total_faculty = (int) $pdo->query("SELECT COUNT(*) FROM directory WHERE type = 'faculty' AND is_active = 1")->fetchColumn();
$total_depts   = count($depts);
$total_profs   = (int) $pdo->query("SELECT COUNT(*) FROM directory WHERE type = 'faculty' AND is_active = 1 AND (job_title LIKE '%Professor%')")->fetchColumn();

// Group the result set for rendering: faculty group -> department -> people
$grouped = [];
foreach ($faculties as $row) {
    $g = $row['faculty_group'] !== '' ? $row['faculty_group'] : 'Other Academic Staff';
    $d = $row['department'] !== '' ? $row['department'] : 'General';
    $grouped[$g][$d][] = $row;
}

$has_filters = ($search !== '' || $dept !== '' || $faculty_group !== '' || $rank !== '');

$ranks = ['Professor', 'Senior Lecturer', 'Lecturer', 'Assistant Lecturer'];
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
<?php vvu_dir_styles('#800000', '#5c0000'); ?>

<main class="flex-grow bg-gray-50 dark:bg-gray-900 pb-20 dir-scope">
    <!-- Hero Section (Directly from faqs_about_vvu.php design) -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_content['hero_image'] ?: 'vvu_faq_hero_1766876441891.png'); ?>"
                 alt="Faculty Hero" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>

        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-6 py-2 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xs md:text-sm font-black tracking-widest uppercase text-yellow-400">Academic Directory</span>
                </div>

                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($page_content['hero_title']); ?> <br>
                    <span class="text-3xl sm:text-4xl md:text-5xl lg:text-5xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-2">Faculty Profiles</span>
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
                <form action="faculty_encyclopedia.php" method="GET" class="dir-panel m-0">
                    <div class="dir-panel__grid">
                        <label class="dir-field">
                            <span class="material-symbols-outlined">person_search</span>
                            <input type="text" name="search" data-dir-search
                                   class="dir-field__control"
                                   placeholder="Search by name, rank or department…"
                                   value="<?php echo vvu_dir_e($search); ?>"
                                   autocomplete="off">
                        </label>

                        <label class="dir-field">
                            <span class="material-symbols-outlined">account_balance</span>
                            <select name="faculty_group" class="dir-field__control browser-default">
                                <option value="">All Faculties</option>
                                <?php foreach ($groups as $g): ?>
                                    <option value="<?php echo vvu_dir_e($g['faculty_group']); ?>" <?php echo $faculty_group === $g['faculty_group'] ? 'selected' : ''; ?>><?php echo vvu_dir_e($g['faculty_group']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <label class="dir-field">
                            <span class="material-symbols-outlined">school</span>
                            <select name="department" class="dir-field__control browser-default">
                                <option value="">All Departments</option>
                                <?php foreach ($depts as $d): ?>
                                    <option value="<?php echo vvu_dir_e($d); ?>" <?php echo $dept === $d ? 'selected' : ''; ?>><?php echo vvu_dir_e($d); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <button type="submit" class="dir-btn">
                            <span class="material-symbols-outlined">search</span>
                            Search
                        </button>
                    </div>

                    <div class="dir-panel__foot">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-black uppercase tracking-wider" style="font-size:11px;" style="color:var(--muted)">Rank:</span>
                            <?php
                            $rank_options = array_merge([''], $ranks);
                            foreach ($rank_options as $r):
                                $qs = http_build_query(array_filter([
                                    'search' => $search, 'department' => $dept,
                                    'faculty_group' => $faculty_group, 'rank' => $r,
                                ], 'strlen'));
                            ?>
                                <a href="faculty_encyclopedia.php<?php echo $qs ? '?' . $qs : ''; ?>"
                                   class="dir-tab <?php echo $rank === $r ? 'is-active' : ''; ?>"
                                   style="padding:7px 14px;font-size:12px;">
                                    <?php echo $r === '' ? 'All' : vvu_dir_e($r); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="dir-count" data-dir-count><b><?php echo count($faculties); ?></b> of <?php echo $total_faculty; ?> listed</span>
                            <?php if ($has_filters): ?>
                                <a href="faculty_encyclopedia.php" class="dir-btn dir-btn--ghost" style="height:42px;font-size:12px;padding:0 18px;">
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
                    <span class="dir-stat__icon"><span class="material-symbols-outlined">groups</span></span>
                    <div>
                        <div class="dir-stat__num"><?php echo $total_faculty; ?></div>
                        <div class="dir-stat__label">Faculty Members</div>
                    </div>
                </div>
                <div class="dir-stat">
                    <span class="dir-stat__icon"><span class="material-symbols-outlined">account_balance</span></span>
                    <div>
                        <div class="dir-stat__num"><?php echo count($groups); ?></div>
                        <div class="dir-stat__label">Faculties &amp; Schools</div>
                    </div>
                </div>
                <div class="dir-stat">
                    <span class="dir-stat__icon"><span class="material-symbols-outlined">school</span></span>
                    <div>
                        <div class="dir-stat__num"><?php echo $total_depts; ?></div>
                        <div class="dir-stat__label">Departments</div>
                    </div>
                </div>
                <div class="dir-stat">
                    <span class="dir-stat__icon"><span class="material-symbols-outlined">workspace_premium</span></span>
                    <div>
                        <div class="dir-stat__num"><?php echo $total_profs; ?></div>
                        <div class="dir-stat__label">Professorial Rank</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Faculty / School quick jump -->
    <?php if (!empty($groups)): ?>
    <section class="dir-block--tight">
        <div class="container px-4">
            <div class="dir-wrap dir-tabs">
                <a href="faculty_encyclopedia.php" class="dir-tab <?php echo $faculty_group === '' ? 'is-active' : ''; ?>">
                    <span class="material-symbols-outlined" style="font-size:17px;">apps</span>
                    All Faculties
                    <span class="dir-tab__n"><?php echo $total_faculty; ?></span>
                </a>
                <?php foreach ($groups as $g): ?>
                    <a href="faculty_encyclopedia.php?faculty_group=<?php echo urlencode($g['faculty_group']); ?>"
                       class="dir-tab <?php echo $faculty_group === $g['faculty_group'] ? 'is-active' : ''; ?>">
                        <?php echo vvu_dir_e($g['faculty_group']); ?>
                        <span class="dir-tab__n"><?php echo (int) $g['n']; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Faculty grid -->
    <section class="dir-block--cards">
        <div class="container px-4">
            <div class="dir-wrap">
                <?php if (empty($faculties)): ?>
                    <div class="dir-empty">
                        <div class="dir-empty__icon"><span class="material-symbols-outlined">search_off</span></div>
                        <h3>No faculty members found</h3>
                        <p>Try a different name, department or rank.</p>
                        <a href="faculty_encyclopedia.php" class="dir-btn">
                            <span class="material-symbols-outlined">restart_alt</span> Reset All Filters
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($grouped as $groupName => $departments): ?>
                        <?php $groupCount = array_sum(array_map('count', $departments)); ?>
                        <section class="dir-section" data-dir-section>
                            <div class="dir-section__head">
                                <span class="dir-section__bar"></span>
                                <div>
                                    <h2 class="dir-section__title"><?php echo vvu_dir_e($groupName); ?></h2>
                                    <p class="dir-section__meta">
                                        <?php echo $groupCount; ?> member<?php echo $groupCount === 1 ? '' : 's'; ?>
                                        &middot; <?php echo count($departments); ?> department<?php echo count($departments) === 1 ? '' : 's'; ?>
                                    </p>
                                </div>
                                <span class="dir-section__rule"></span>
                            </div>

                            <?php foreach ($departments as $deptName => $people): ?>
                                <div data-dir-group>
                                    <?php if (count($departments) > 1 || $deptName !== $groupName): ?>
                                        <h3 class="dir-subhead">
                                            <?php echo vvu_dir_e($deptName); ?>
                                            <span class="dir-subhead__n"><?php echo count($people); ?></span>
                                        </h3>
                                    <?php endif; ?>
                                    <div class="dir-grid">
                                        <?php foreach ($people as $fac) { vvu_dir_card($fac, 'faculty'); } ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </section>
                    <?php endforeach; ?>

                    <div class="dir-empty" data-dir-empty hidden>
                        <div class="dir-empty__icon"><span class="material-symbols-outlined">search_off</span></div>
                        <h3>Nothing matches that search</h3>
                        <p>Clear the search box to see every faculty member again.</p>
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
                        Contact Faculty Office
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
