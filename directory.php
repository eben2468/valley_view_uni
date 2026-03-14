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
    .category-section { opacity: 0; transform: translateY(30px); transition: all 0.6s ease-out; }
    .category-section.visible { opacity: 1; transform: translateY(0); }
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

    <!-- Search Bar -->
    <section class="relative z-20 -mt-10">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-3 flex items-center gap-4 border border-gray-100 dark:border-gray-700">
                    <div class="w-14 h-14 rounded-2xl vvu-gradient flex items-center justify-center text-white shadow-lg shrink-0">
                        <span class="material-symbols-outlined text-3xl text-white">search</span>
                    </div>
                    <input type="text" id="dirSearch" placeholder="Search by name, title, or department..." class="w-full text-xl font-medium text-gray-700 dark:text-gray-200 bg-transparent border-none outline-none search-glow rounded-xl px-4 py-4" style="box-shadow: none !important; border: none !important;">
                    <div id="searchCount" class="hidden shrink-0 px-5 py-3 bg-blue-50 dark:bg-blue-900/30 rounded-2xl text-blue-700 dark:text-blue-300 font-bold text-lg whitespace-nowrap"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Stats -->
    <section class="py-20 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-6xl mx-auto">
                <div class="text-center">
                    <div class="text-5xl md:text-6xl font-black text-blue-900 dark:text-white mb-2"><?php echo count($directory_data); ?></div>
                    <div class="text-lg md:text-xl font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Leaders</div>
                </div>
                <div class="text-center">
                    <div class="text-5xl md:text-6xl font-black text-blue-900 dark:text-white mb-2"><?php echo count($grouped_directory); ?></div>
                    <div class="text-lg md:text-xl font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Categories</div>
                </div>
                <div class="text-center">
                    <div class="text-5xl md:text-6xl font-black text-blue-900 dark:text-white mb-2">3</div>
                    <div class="text-lg md:text-xl font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Campuses</div>
                </div>
                <div class="text-center">
                    <div class="text-5xl md:text-6xl font-black text-blue-900 dark:text-white mb-2">30+</div>
                    <div class="text-lg md:text-xl font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Departments</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Directory Groups -->
    <?php $groupIndex = 0; foreach ($grouped_directory as $category => $members): 
        $icon = $category_icons[$category] ?? 'badge';
        $gradient = $category_colors[$category] ?? 'from-blue-600 to-blue-800';
        $bgClass = $groupIndex % 2 === 0 ? 'bg-gray-50 dark:bg-gray-800/50' : 'bg-white dark:bg-gray-900';
    ?>
    <section class="py-24 <?php echo $bgClass; ?> category-section" data-category="<?php echo strip_tags($category); ?>">
        <div class="container mx-auto px-4">
            <!-- Section Header -->
            <div class="max-w-5xl mx-auto text-center mb-20">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-gradient-to-br <?php echo $gradient; ?> text-white shadow-xl mb-8">
                    <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($icon); ?></span>
                </div>
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($category); ?></h2>
                <div class="h-2 w-40 bg-gradient-to-r <?php echo $gradient; ?> mx-auto rounded-full mb-8"></div>
                <p class="text-2xl md:text-[1.75rem] text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                    <?php echo count($members); ?> member<?php echo count($members) > 1 ? 's' : ''; ?> serving the university community
                </p>
            </div>

            <!-- Members Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10 max-w-[90rem] mx-auto">
                <?php foreach ($members as $member): ?>
                <div class="member-card-hover directory-item" data-search="<?php echo strtolower($member['name'] . ' ' . $member['title'] . ' ' . $member['category']); ?>">
                    <div class="relative h-full glass p-8 md:p-10 rounded-3xl shadow-xl border-t-4 border-transparent hover:border-t-4 hover:border-blue-900 group">
                        <!-- Initials Avatar -->
                        <div class="flex items-start gap-6 mb-6">
                            <div class="w-16 h-16 shrink-0 rounded-2xl bg-gradient-to-br <?php echo $gradient; ?> flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                                <span class="text-2xl font-black" style="color: #fff !important;">
                                    <?php 
                                    $parts = explode(' ', trim($member['name']));
                                    $initials = '';
                                    // Get last meaningful word as initial
                                    foreach ($parts as $p) {
                                        $p = trim($p, '.,');
                                        if (strlen($p) > 2 && !in_array(strtolower($p), ['pr.', 'dr.', 'prof.', 'mrs.', 'mr.', 'esq.'])) {
                                            $initials .= strtoupper($p[0]);
                                        }
                                    }
                                    echo substr($initials, 0, 2);
                                    ?>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-2xl md:text-[1.65rem] font-black text-gray-900 dark:text-white leading-tight mb-2"><?php echo strip_tags($member['name']); ?></h3>
                                <p class="text-lg md:text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r <?php echo $gradient; ?> leading-snug"><?php echo strip_tags($member['title']); ?></p>
                            </div>
                        </div>
                        
                        <?php if (!empty($member['email'])): ?>
                        <div class="flex items-center gap-3 mt-auto pt-6 border-t border-gray-200 dark:border-gray-700">
                            <span class="material-symbols-outlined text-2xl text-gray-400">mail</span>
                            <a href="mailto:<?php echo strip_tags($member['email']); ?>" class="text-lg text-gray-500 dark:text-gray-400 hover:text-blue-700 transition-colors font-medium truncate"><?php echo strip_tags($member['email']); ?></a>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($member['phone'])): ?>
                        <div class="flex items-center gap-3 mt-3">
                            <span class="material-symbols-outlined text-2xl text-gray-400">call</span>
                            <a href="tel:<?php echo strip_tags($member['phone']); ?>" class="text-lg text-gray-500 dark:text-gray-400 hover:text-blue-700 transition-colors font-medium"><?php echo strip_tags($member['phone']); ?></a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php $groupIndex++; endforeach; ?>

    <!-- No Results -->
    <div id="noResults" class="hidden py-24 text-center">
        <div class="container mx-auto px-4">
            <div class="w-24 h-24 mx-auto rounded-3xl bg-gray-200 dark:bg-gray-700 flex items-center justify-center mb-8">
                <span class="material-symbols-outlined text-5xl text-gray-400 dark:text-gray-500">person_search</span>
            </div>
            <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-4">No Results Found</h3>
            <p class="text-2xl text-gray-500 dark:text-gray-400">Try adjusting your search terms.</p>
        </div>
    </div>

    <!-- CTA -->
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 vvu-gradient"></div>
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-72 h-72 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-amber-400 rounded-full blur-3xl"></div>
        </div>
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white mb-8 leading-tight tracking-tight">
                    <?php echo strip_tags($hero['cta_heading']); ?> <br>
                    <span class="text-5xl sm:text-6xl md:text-7xl lg:text-6xl text-amber-300 block mt-2"><?php echo strip_tags($hero['cta_subtitle']); ?></span>
                </h2>
                <p class="text-2xl sm:text-3xl md:text-4xl text-white/90 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($hero['cta_text']); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags($hero['cta_btn1_url']); ?>" class="px-10 py-5 bg-white hover:bg-gray-100 text-blue-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">school</span> <?php echo strip_tags($hero['cta_btn1_text']); ?>
                    </a>
                    <a href="<?php echo strip_tags($hero['cta_btn2_url']); ?>" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">mail</span> <?php echo strip_tags($hero['cta_btn2_text']); ?>
                    </a>
                </div>
                <div class="mt-20 grid grid-cols-1 sm:grid-cols-3 gap-12 border-t border-white/10 pt-16">
                    <div>
                        <div class="text-6xl font-black text-white mb-2"><?php echo strip_tags($hero['stat1_value']); ?></div>
                        <div class="text-amber-300 uppercase tracking-widest text-2xl font-black"><?php echo strip_tags($hero['stat1_label']); ?></div>
                    </div>
                    <div>
                        <div class="text-6xl font-black text-white mb-2"><?php echo strip_tags($hero['stat2_value']); ?></div>
                        <div class="text-amber-300 uppercase tracking-widest text-2xl font-black"><?php echo strip_tags($hero['stat2_label']); ?></div>
                    </div>
                    <div>
                        <div class="text-6xl font-black text-white mb-2"><?php echo strip_tags($hero['stat3_value']); ?></div>
                        <div class="text-amber-300 uppercase tracking-widest text-2xl font-black"><?php echo strip_tags($hero['stat3_label']); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('dirSearch');
    const items = document.querySelectorAll('.directory-item');
    const sections = document.querySelectorAll('.category-section');
    const noResults = document.getElementById('noResults');
    const searchCount = document.getElementById('searchCount');

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        let totalVisible = 0;

        sections.forEach(section => {
            let sectionVisible = false;
            const sectionItems = section.querySelectorAll('.directory-item');
            
            sectionItems.forEach(item => {
                const searchText = item.getAttribute('data-search');
                if (!query || searchText.includes(query)) {
                    item.style.display = '';
                    sectionVisible = true;
                    totalVisible++;
                } else {
                    item.style.display = 'none';
                }
            });

            section.style.display = sectionVisible ? '' : 'none';
        });

        if (query) {
            searchCount.classList.remove('hidden');
            searchCount.textContent = totalVisible + ' found';
        } else {
            searchCount.classList.add('hidden');
        }

        noResults.style.display = totalVisible === 0 && query ? '' : 'none';
    });

    // Scroll reveal for sections
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });

    sections.forEach(section => observer.observe(section));
});
</script>

<?php include 'includes/footer.php'; ?>
