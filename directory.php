<?php
$page_title = "University Directory - Valley View University";
$active_page = "about";
include 'includes/header.php';
require_once 'includes/db_connect.php';

// Fetch directory hero/page content
$hero = $pdo->query("SELECT * FROM university_directory_page WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

// Defaults if table doesn't exist yet
if (!$hero) {
    $hero = [
        'hero_badge' => 'Governance & Leadership',
        'hero_title' => 'University',
        'hero_subtitle' => 'Directory',
        'hero_description' => 'Meet the dedicated leaders and administrators driving excellence at Valley View University.',
        'hero_image' => 'uploads/directory_hero.jpg',
        'cta_heading' => 'Join Our Community',
        'cta_subtitle' => 'Of Excellence & Service',
        'cta_text' => 'Valley View University continues to shape future leaders through faith-based, quality education. Be part of the legacy.',
        'cta_btn1_text' => 'Apply Now',
        'cta_btn1_url' => 'admissions.php',
        'cta_btn2_text' => 'Contact Us',
        'cta_btn2_url' => 'contact_us.php',
        'stat1_value' => '70+',
        'stat1_label' => 'Leaders & Staff',
        'stat2_value' => '3',
        'stat2_label' => 'Campuses',
        'stat3_value' => '30+',
        'stat3_label' => 'Departments',
    ];
}

// Fetch directory entries grouped by category
$stmt = $pdo->query("SELECT * FROM university_directory WHERE is_active = 1 ORDER BY display_order ASC");
$directory_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$grouped_directory = [];
foreach ($directory_data as $item) {
    $grouped_directory[$item['category']][] = $item;
}

// Category icons mapping
$category_icons = [
    'Principal Officers' => 'verified',
    'Campus Administration' => 'apartment',
    'Academic Deans & Research' => 'school',
    'Departmental & Unit Heads' => 'domain',
    'University Directors' => 'shield_person',
    'Associate Officers & Section Heads' => 'groups',
    'Financial Officers' => 'account_balance',
    'Operations & Services Support' => 'engineering',
];

$category_colors = [
    'Principal Officers' => 'from-amber-600 to-amber-800',
    'Campus Administration' => 'from-emerald-600 to-emerald-800',
    'Academic Deans & Research' => 'from-blue-600 to-blue-800',
    'Departmental & Unit Heads' => 'from-violet-600 to-violet-800',
    'University Directors' => 'from-rose-600 to-rose-800',
    'Associate Officers & Section Heads' => 'from-cyan-600 to-cyan-800',
    'Financial Officers' => 'from-orange-600 to-orange-800',
    'Operations & Services Support' => 'from-teal-600 to-teal-800',
];
?>

<link rel="stylesheet" href="css/vvu-modern.css?v=1.0">
<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slowZoom { 0% { transform: scale(1); } 100% { transform: scale(1.1); } }
    @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
    .animate-float { animation: float 3s ease-in-out infinite; }
    .glass { background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); }
    .dark .glass { background: rgba(31,41,55,0.7); border: 1px solid rgba(255,255,255,0.1); }
    .vvu-gradient { background: linear-gradient(135deg, #002147 0%, #003580 50%, #004AAD 100%); }
    .member-card-hover { transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1); }
    .member-card-hover:hover { transform: translateY(-8px); }
    .search-glow:focus { box-shadow: 0 0 0 4px rgba(0, 33, 71, 0.15); }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <div class="w-full h-full animate-slow-zoom opacity-60" style="background: url('<?php echo strip_tags($hero['hero_image']); ?>') center/cover no-repeat;"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-blue-950/80 via-blue-900/40 to-gray-900"></div>
        </div>
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-amber-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-amber-400"><?php echo strip_tags($hero['hero_badge']); ?></span>
                </div>
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['hero_title']); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-amber-400 via-amber-200 to-amber-500 block mt-4"><?php echo strip_tags($hero['hero_subtitle']); ?></span>
                </h1>
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($hero['hero_description']); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Search (overlaps the hero) -->
    <div class="container" style="position: relative; z-index: 20; margin-top: -34px;">
        <div class="vm-search" role="search">
            <span class="material-symbols-outlined" aria-hidden="true">search</span>
            <label for="dirSearch" class="sr-only">Search the directory</label>
            <input type="search" id="dirSearch" autocomplete="off" placeholder="Search by name, title or department…">
            <span id="searchCount" class="vm-search-count hidden"></span>
        </div>
    </div>

    <!-- Quick figures -->
    <section class="vm-section vm-section--white" style="padding: 56px 0 24px;">
        <div class="container">
            <div class="vm-figures vm-wrap" style="justify-content: center; margin-top: 0; gap: 18px 56px;">
                <div class="vm-figure" style="text-align: center;">
                    <span class="vm-figure-value"><?php echo count($directory_data); ?></span>
                    <span class="vm-figure-label">Total Leaders</span>
                </div>
                <div class="vm-figure" style="text-align: center;">
                    <span class="vm-figure-value"><?php echo count($grouped_directory); ?></span>
                    <span class="vm-figure-label">Categories</span>
                </div>
                <div class="vm-figure" style="text-align: center;">
                    <span class="vm-figure-value">3</span>
                    <span class="vm-figure-label">Campuses</span>
                </div>
                <div class="vm-figure" style="text-align: center;">
                    <span class="vm-figure-value">30+</span>
                    <span class="vm-figure-label">Departments</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Directory groups -->
    <section class="vm-section vm-section--white" style="padding-top: 40px;">
        <div class="container">
            <div class="vm-wrap" style="max-width: 1280px;">
                <?php foreach ($grouped_directory as $category => $members):
                    $icon = $category_icons[$category] ?? 'badge';
                ?>
                <div class="vm-group category-section" data-category="<?php echo htmlspecialchars(strip_tags($category)); ?>">
                    <div class="vm-group-head">
                        <span class="vm-icon"><span class="material-symbols-outlined"><?php echo strip_tags($icon); ?></span></span>
                        <div>
                            <div class="vm-group-title" role="heading" aria-level="2"><?php echo htmlspecialchars(strip_tags($category)); ?></div>
                            <span class="vm-group-meta"><?php echo count($members); ?> member<?php echo count($members) === 1 ? '' : 's'; ?></span>
                        </div>
                        <span class="vm-group-rule"></span>
                    </div>

                    <div class="vm-grid">
                        <?php foreach ($members as $member):
                            $parts = explode(' ', trim($member['name']));
                            $initials = '';
                            foreach ($parts as $p) {
                                $p = trim($p, '.,');
                                if (strlen($p) > 2 && !in_array(strtolower($p), ['pr.', 'dr.', 'prof.', 'mrs.', 'mr.', 'esq.'])) {
                                    $initials .= strtoupper($p[0]);
                                }
                            }
                        ?>
                        <div class="vm-card vm-person directory-item" data-search="<?php echo htmlspecialchars(strtolower($member['name'] . ' ' . $member['title'] . ' ' . $member['category'])); ?>">
                            <div class="vm-person-head">
                                <span class="vm-avatar"><?php echo htmlspecialchars(substr($initials, 0, 2)); ?></span>
                                <div>
                                    <div class="vm-person-name" role="heading" aria-level="3"><?php echo htmlspecialchars(strip_tags($member['name'])); ?></div>
                                    <span class="vm-person-role"><?php echo htmlspecialchars(strip_tags($member['title'])); ?></span>
                                </div>
                            </div>
                            <?php if (!empty($member['email']) || !empty($member['phone'])): ?>
                            <div class="vm-person-links">
                                <?php if (!empty($member['email'])): ?>
                                <a href="mailto:<?php echo htmlspecialchars(strip_tags($member['email'])); ?>"><span class="material-symbols-outlined">mail</span><?php echo htmlspecialchars(strip_tags($member['email'])); ?></a>
                                <?php endif; ?>
                                <?php if (!empty($member['phone'])): ?>
                                <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^\d+]/', '', strip_tags($member['phone']))); ?>"><span class="material-symbols-outlined">call</span><?php echo htmlspecialchars(strip_tags($member['phone'])); ?></a>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- No Results -->
                <div id="noResults" class="vm-empty hidden">
                    <span class="material-symbols-outlined">person_search</span>
                    <div class="vm-empty-title">No results found</div>
                    <p class="vm-empty-text">Try a different name, title or department.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to action -->
    <section class="vm-cta">
        <div class="container">
            <div class="vm-cta-head">
                <div class="vm-cta-heading" role="heading" aria-level="2"><?php echo htmlspecialchars(trim(strip_tags($hero['cta_heading']) . ' ' . strip_tags($hero['cta_subtitle']))); ?></div>
                <p class="vm-cta-lead"><?php echo htmlspecialchars(strip_tags($hero['cta_text'])); ?></p>
                <div class="vm-actions">
                    <a href="<?php echo strip_tags($hero['cta_btn1_url']); ?>" class="vm-btn vm-btn--gold"><span class="material-symbols-outlined">school</span><?php echo htmlspecialchars(strip_tags($hero['cta_btn1_text'])); ?></a>
                    <a href="<?php echo strip_tags($hero['cta_btn2_url']); ?>" class="vm-btn vm-btn--ghost"><span class="material-symbols-outlined">mail</span><?php echo htmlspecialchars(strip_tags($hero['cta_btn2_text'])); ?></a>
                </div>
            </div>
            <div class="vm-stats">
                <?php foreach ([1, 2, 3] as $n): ?>
                <div class="vm-stat">
                    <span class="vm-stat-value"><?php echo htmlspecialchars(strip_tags($hero["stat{$n}_value"])); ?></span>
                    <span class="vm-stat-label"><?php echo htmlspecialchars(strip_tags($hero["stat{$n}_label"])); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<script>
// Live directory search: hides non-matching people, then empty groups
document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('dirSearch');
    var groups = document.querySelectorAll('.category-section');
    var noResults = document.getElementById('noResults');
    var count = document.getElementById('searchCount');
    if (!input) return;

    input.addEventListener('input', function () {
        var query = this.value.toLowerCase().trim();
        var total = 0;

        Array.prototype.forEach.call(groups, function (group) {
            var shown = 0;
            Array.prototype.forEach.call(group.querySelectorAll('.directory-item'), function (item) {
                var hit = !query || (item.getAttribute('data-search') || '').indexOf(query) !== -1;
                item.classList.toggle('hidden', !hit);
                if (hit) shown++;
            });
            group.classList.toggle('hidden', shown === 0);
            total += shown;
        });

        count.textContent = total + ' found';
        count.classList.toggle('hidden', !query);
        noResults.classList.toggle('hidden', !(query && total === 0));
    });
});
</script>

<?php include 'includes/footer.php'; ?>
