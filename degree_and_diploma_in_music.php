<?php
$page_title = "Degree & Diploma in Music - Valley View University";
$active_page = "admissions";
include 'includes/header.php';
require_once 'includes/db_connect.php';

// Fetch page content
$stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = 'music_programs'");
$stmt->execute();
$page_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch sections
$stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = 'music_programs' AND is_active = 1 ORDER BY display_order");
$stmt->execute();
$sections_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sections = [];
foreach ($sections_raw as $s) {
    $sections[$s['section_key']] = $s;
}

// Fetch items
$stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = 'music_programs' AND is_active = 1 ORDER BY display_order");
$stmt->execute();
$items_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
$items = [];
foreach ($items_raw as $i) {
    $items[$i['section_key']][] = $i;
}
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
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(251, 191, 36, 0.3); }
        50% { box-shadow: 0 0 40px rgba(251, 191, 36, 0.5); }
    }
    @keyframes note-float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-15px) rotate(5deg); }
    }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
    .animate-float { animation: float 4s ease-in-out infinite; }
    .animate-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
    .animate-note-float { animation: note-float 3s ease-in-out infinite; }
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
        background: linear-gradient(to right, #7c3aed, #fbbf24);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .program-card {
        transition: all 0.3s ease;
    }
    .program-card:hover {
        transform: translateY(-10px);
    }
    .benefit-card {
        transition: all 0.3s ease;
    }
    .benefit-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_data['hero_image'] ?? 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); ?>" 
                 alt="Music Education" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-purple-900/80 via-purple-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <?php if ($page_data['hero_badge']): ?>
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-lg md:text-xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($page_data['hero_badge']); ?></span>
                </div>
                <?php endif; ?>
                
                <h1 class="max-w-7xl mx-auto text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo $page_data['hero_title']; ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo $page_data['hero_subtitle']; ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($page_data['hero_description']); ?>"
                </p>

                <div class="mt-12 flex flex-col sm:flex-row gap-6 justify-center animate-fadeInUp" style="animation-delay: 0.3s;">
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?? 'apply.php'); ?>" class="px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-purple-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-4 animate-pulse-glow">
                        <span class="material-symbols-outlined text-3xl">edit_square</span>
                        <?php echo strip_tags($page_data['cta_button_text'] ?? 'Apply Now'); ?>
                    </a>
                    <a href="#programs" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl">music_note</span>
                        View Programs
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <?php if (isset($sections['about'])): ?>
    <section class="py-32 bg-white dark:bg-gray-800 relative z-20 -mt-10 mx-auto rounded-[3rem] shadow-2xl overflow-hidden">
        <div class="container">
            <div class="max-w-[140rem] mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-stretch">
                    <!-- Left Column - Image -->
                    <div class="relative rounded-[3rem] overflow-hidden shadow-2xl min-h-[500px]">
                        <img src="<?php echo strip_tags($sections['about']['section_image'] ?? 'uploads/music.jpg'); ?>" alt="Music Education at VVU" class="w-full h-full object-cover absolute inset-0">
                        <div class="absolute inset-0 bg-gradient-to-t from-purple-900/70 via-purple-900/20 to-transparent"></div>
                        <div class="absolute bottom-10 left-10 right-10">
                            <div class="flex items-center gap-6">
                                <div class="w-24 h-24 rounded-3xl bg-yellow-400 flex items-center justify-center animate-note-float shadow-xl">
                                    <span class="material-symbols-outlined text-5xl text-purple-900">music_note</span>
                                </div>
                                <div>
                                    <p class="text-4xl font-black text-white">Music Department</p>
                                    <p class="text-2xl text-white/90">Valley View University</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - About Content -->
                    <div class="p-14 bg-gradient-to-br from-purple-50 to-white dark:from-gray-900 dark:to-gray-800 rounded-[3rem] flex flex-col justify-center">
                        <div class="flex items-center gap-8 mb-12">
                            <div class="w-28 h-28 rounded-3xl bg-purple-600 flex items-center justify-center shadow-lg">
                                <span class="material-symbols-outlined text-6xl text-white">piano</span>
                            </div>
                            <div>
                                <h2 class="text-5xl sm:text-6xl font-black text-gray-900 dark:text-white"><?php echo strip_tags($sections['about']['section_title']); ?></h2>
                            </div>
                        </div>
                        <div class="text-2xl sm:text-3xl text-gray-600 dark:text-gray-400 mb-8 leading-relaxed">
                            <?php echo $sections['about']['section_description']; ?>
                        </div>
                        <div class="flex flex-wrap gap-6">
                            <div class="px-10 py-5 bg-purple-100 dark:bg-purple-900/30 rounded-2xl">
                                <span class="text-2xl font-bold text-purple-600 dark:text-purple-400">4-Year B.Ed</span>
                            </div>
                            <div class="px-10 py-5 bg-yellow-100 dark:bg-yellow-900/30 rounded-2xl">
                                <span class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">2-Year Diploma</span>
                            </div>
                            <div class="px-10 py-5 bg-green-100 dark:bg-green-900/30 rounded-2xl">
                                <span class="text-2xl font-bold text-green-600 dark:text-green-400">Accredited</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Benefits Section -->
    <?php if (isset($sections['benefits'])): ?>
    <section class="py-28 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-[100rem] mx-auto px-4">
                <div class="text-center mb-24">
                    <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($sections['benefits']['section_title']); ?></h2>
                    <div class="h-2 w-48 bg-purple-600 mx-auto rounded-full mb-10"></div>
                    <p class="text-3xl sm:text-4xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed max-w-5xl mx-auto"><?php echo strip_tags($sections['benefits']['section_subtitle']); ?></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                    <?php if (isset($items['benefits'])): ?>
                    <?php foreach ($items['benefits'] as $item): ?>
                    <div class="benefit-card bg-white dark:bg-gray-900 rounded-[3rem] p-14 shadow-xl border border-gray-100 dark:border-gray-800">
                        <div class="w-24 h-24 rounded-3xl bg-<?php echo !empty($item['item_color']) ? $item['item_color'] : 'blue-600'; ?> flex items-center justify-center mb-10 shadow-lg">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($item['item_icon']); ?></span>
                        </div>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($item['item_title']); ?></h3>
                        <p class="text-2xl sm:text-3xl text-gray-600 dark:text-gray-400 leading-relaxed"><?php echo strip_tags($item['item_description']); ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Programs Offered Section -->
    <?php if (isset($sections['programs'])): ?>
    <section id="programs" class="py-28 bg-purple-900 overflow-hidden relative">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-purple-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-[100rem] mx-auto px-4">
                <div class="text-center mb-24">
                    <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-white mb-8"><?php echo strip_tags($sections['programs']['section_title']); ?></h2>
                    <p class="text-3xl text-purple-100 font-medium leading-relaxed max-w-5xl mx-auto"><?php echo strip_tags($sections['programs']['section_subtitle']); ?></p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                    <?php if (isset($items['programs'])): ?>
                    <?php foreach ($items['programs'] as $item): 
                        $extra = json_decode($item['extra_data'], true);
                    ?>
                    <div class="program-card bg-white/10 backdrop-blur-md rounded-[3rem] p-14 border border-white/10">
                        <div class="flex items-center gap-6 mb-12">
                            <div class="w-28 h-28 rounded-3xl bg-<?php echo $item['item_color'] == 'yellow-400' ? 'yellow-400' : 'green-500'; ?> flex items-center justify-center">
                                <span class="material-symbols-outlined text-6xl <?php echo $item['item_color'] == 'yellow-400' ? 'text-purple-900' : 'text-white'; ?>"><?php echo strip_tags($item['item_icon']); ?></span>
                            </div>
                            <div>
                                <h3 class="text-4xl sm:text-5xl font-black text-white"><?php echo strip_tags($item['item_title']); ?></h3>
                                <p class="text-2xl text-purple-200 mt-2"><?php echo strip_tags($item['item_subtitle']); ?></p>
                            </div>
                        </div>
                        
                        <div class="space-y-8 mb-12">
                            <h4 class="text-3xl font-bold <?php echo $item['item_color'] == 'yellow-400' ? 'text-yellow-400' : 'text-green-400'; ?>">Admission Requirements:</h4>
                            
                            <?php if (isset($extra['direct_candidates'])): ?>
                            <div class="bg-white/5 rounded-2xl p-8">
                                <h5 class="text-2xl font-bold text-white mb-4">Direct Candidates:</h5>
                                <p class="text-2xl text-purple-100 leading-relaxed"><?php echo strip_tags($extra['direct_candidates']); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (isset($extra['mature_candidates'])): ?>
                            <div class="bg-white/5 rounded-2xl p-8">
                                <h5 class="text-2xl font-bold <?php echo $item['item_color'] == 'yellow-400' ? 'text-yellow-400' : 'text-green-400'; ?> mb-4">Mature Candidates:</h5>
                                <ul class="space-y-4 text-2xl">
                                    <?php foreach ($extra['mature_candidates'] as $req): ?>
                                    <li class="flex items-start gap-4">
                                        <span class="material-symbols-outlined <?php echo $item['item_color'] == 'yellow-400' ? 'text-yellow-400' : 'text-green-400'; ?> text-3xl mt-1">check_circle</span>
                                        <span class="<?php echo $item['item_color'] == 'yellow-400' ? 'text-yellow-200' : 'text-green-200'; ?>"><?php echo strip_tags($req); ?></span>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>

                        <a href="<?php echo strip_tags($item['item_link'] ?: 'apply.php'); ?>" class="inline-flex items-center gap-4 px-12 py-6 bg-<?php echo $item['item_color'] == 'yellow-400' ? 'yellow-400' : 'green-500'; ?> <?php echo $item['item_color'] == 'yellow-400' ? 'text-purple-900' : 'text-white'; ?> text-2xl font-bold rounded-2xl hover:opacity-90 transition-all shadow-lg">
                            <span class="material-symbols-outlined text-3xl">arrow_forward</span>
                            Apply for <?php echo explode(' ', $item['item_title'])[mt_rand(0,1)] == 'Diploma' ? 'Diploma' : 'B.Ed'; ?>
                        </a>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Career Opportunities Section -->
    <?php if (isset($sections['careers'])): ?>
    <section class="py-28 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-[100rem] mx-auto px-4">
                <div class="text-center mb-24">
                    <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($sections['careers']['section_title']); ?></h2>
                    <div class="h-2 w-48 bg-purple-600 mx-auto rounded-full mb-10"></div>
                    <p class="text-3xl sm:text-4xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed max-w-5xl mx-auto"><?php echo strip_tags($sections['careers']['section_subtitle']); ?></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                    <?php if (isset($items['careers'])): ?>
                    <?php foreach ($items['careers'] as $item): ?>
                    <div class="p-12 bg-gradient-to-br from-<?php echo str_replace('-600', '', $item['item_color']); ?>-50 to-<?php echo str_replace('-600', '', $item['item_color']); ?>-100 dark:from-<?php echo str_replace('-600', '', $item['item_color']); ?>-900/20 dark:to-<?php echo str_replace('-600', '', $item['item_color']); ?>-800/20 rounded-[3rem] text-center hover:shadow-xl transition-all group">
                        <div class="w-24 h-24 rounded-3xl bg-<?php echo !empty($item['item_color']) ? $item['item_color'] : 'blue-600'; ?> flex items-center justify-center mx-auto mb-10 group-hover:scale-110 transition-transform shadow-lg">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($item['item_icon']); ?></span>
                        </div>
                        <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($item['item_title']); ?></h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($item['item_description']); ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Why Choose VVU Section -->
    <?php if (isset($sections['vvu_features'])): ?>
    <section class="py-28 bg-gradient-to-br from-gray-50 to-purple-50 dark:from-gray-950 dark:to-purple-950/20">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-24">
                    <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($sections['vvu_features']['section_title']); ?></h2>
                    <div class="h-2 w-48 bg-purple-600 mx-auto rounded-full mb-10"></div>
                    <p class="text-3xl sm:text-4xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed max-w-5xl mx-auto"><?php echo strip_tags($sections['vvu_features']['section_description']); ?></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <?php if (isset($items['vvu_features'])): ?>
                    <?php foreach ($items['vvu_features'] as $item): ?>
                    <div class="flex items-start gap-8 p-10 bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-lg hover:shadow-xl transition-all">
                        <div class="w-20 h-20 rounded-2xl bg-<?php echo !empty($item['item_color']) ? $item['item_color'] : 'blue-600'; ?> flex items-center justify-center flex-shrink-0 shadow-lg">
                            <span class="material-symbols-outlined text-4xl text-white"><?php echo strip_tags($item['item_icon']); ?></span>
                        </div>
                        <div>
                            <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-3"><?php echo strip_tags($item['item_title']); ?></h4>
                            <p class="text-2xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($item['item_description']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <?php if ($page_data['cta_title']): ?>
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 bg-purple-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-purple-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black text-white mb-8 leading-tight tracking-tight">
                    <?php echo $page_data['cta_title']; ?> <br><span class="text-lg sm:text-xl md:text-2xl lg:text-4xl text-yellow-400 block mt-2 font-medium"><?php echo $page_data['cta_subtitle']; ?></span>
                </h2>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?? 'apply.php'); ?>" class="px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-purple-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl">edit_square</span>
                        <?php echo strip_tags($page_data['cta_button_text'] ?? 'Apply Now'); ?>
                    </a>
                    <a href="contact_us.php" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl">contact_support</span>
                        Contact Us
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php
include 'includes/footer.php';
?>
