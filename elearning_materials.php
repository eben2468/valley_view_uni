<?php
$page_title = "E-Learning Materials - Valley View University";
$active_page = "students";
require_once 'includes/db_connect.php';

// Fetch data from database
$page_key = 'elearning_materials';
$stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = ? AND is_active = 1");
$stmt->execute([$page_key]);
$hero = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = ? ORDER BY display_order");
$stmt->execute([$page_key]);
$sections = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = ? AND is_active = 1 ORDER BY section_key, display_order");
$stmt->execute([$page_key]);
$all_items = $stmt->fetchAll();

$grouped_items = [];
foreach ($all_items as $item) {
    if ($item['extra_data']) {
        $item['meta'] = json_decode($item['extra_data'], true) ?: [];
    }
    $grouped_items[$item['section_key']][] = $item;
}

$stmt = $pdo->prepare("SELECT * FROM academic_pages_stats WHERE page_key = ? ORDER BY display_order");
$stmt->execute([$page_key]);
$stats = $stmt->fetchAll();

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
    .hover-lift {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .hover-lift:hover {
        transform: translateY(-10px);
        box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.15);
    }
    .video-card-thumb {
        position: relative;
        overflow: hidden;
        border-radius: 2rem;
        aspect-ratio: 16/9;
    }
    .video-card-thumb::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .hover-lift:hover .video-card-thumb::after {
        opacity: 1;
    }
    .play-btn {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.8);
        width: 80px;
        height: 80px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        transition: all 0.3s ease;
        opacity: 0;
    }
    .hover-lift:hover .play-btn {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900 overflow-hidden">
    <!-- Hero Section -->
    <section class="relative min-h-[70vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image'] ?? 'https://images.unsplash.com/photo-1510074377623-8cf13fb86c08?auto=format&fit=crop&q=80&w=1920'); ?>" 
                 alt="Digital Learning" class="w-full h-full object-cover animate-slow-zoom opacity-50">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-6xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['hero_badge'] ?? 'E-Learning Support Hub'); ?></span>
                </div>
                
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['hero_title'] ?? 'Digital'); ?> <br>
                    <span class="text-3xl sm:text-4xl md:text-5xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($hero['hero_subtitle'] ?? 'Resource Center'); ?></span>
                </h1>
                
                <p class="text-xl sm:text-2xl md:text-3xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($hero['hero_description'] ?? '"Empowering your academic journey with instant access to tutorials, manuals, and technical support documentation."'); ?>
                </p>
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-20 max-w-5xl mx-auto">
                    <?php foreach ($stats as $stat): ?>
                    <div class="px-8 py-10 bg-white/5 backdrop-blur-md rounded-[2.5rem] border border-white/10 shadow-xl group hover:bg-white/10 transition-all">
                        <span class="material-symbols-outlined text-yellow-400 text-4xl mb-3 group-hover:scale-110 transition-transform"><?php echo strip_tags($stat['stat_icon'] ?? 'star'); ?></span>
                        <p class="text-4xl font-black text-white mb-1"><?php echo strip_tags($stat['stat_value']); ?></p>
                        <p class="text-lg text-blue-200 font-bold uppercase tracking-widest"><?php echo strip_tags($stat['stat_label']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Area -->
    <div class="w-full max-w-[95%] mx-auto px-8 md:px-16 relative z-20 -mt-20">
        <!-- Search Bar -->
        <section class="bg-white dark:bg-gray-800 p-6 rounded-[3rem] shadow-2xl mb-24 border border-gray-100 dark:border-gray-700">
            <div class="relative group">
                <span class="material-symbols-outlined absolute left-8 top-1/2 -translate-y-1/2 text-gray-400 text-5xl">search</span>
                <input type="text" id="resourceSearch" placeholder="Search for manuals, tutorials, or activation guides..." 
                       class="w-full pl-20 pr-10 py-8 bg-gray-50 dark:bg-gray-900 rounded-[2rem] border-none focus:ring-4 focus:ring-blue-500/20 text-xl md:text-2xl font-bold placeholder-gray-400 shadow-inner">
                <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-4">
                    <div class="hidden md:flex items-center gap-2 text-xl font-bold text-gray-500">
                        <span>Popular:</span>
                        <button onclick="document.getElementById('resourceSearch').value = 'V-Class'; document.getElementById('resourceSearch').dispatchEvent(new Event('input'));" class="hover:text-blue-600 transition-colors">V-Class</button>,
                        <button onclick="document.getElementById('resourceSearch').value = 'Email'; document.getElementById('resourceSearch').dispatchEvent(new Event('input'));" class="hover:text-blue-600 transition-colors">Email</button>
                    </div>
                    <button id="searchBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-5 rounded-2xl text-xl font-black transition-all transform hover:scale-105 shadow-lg">
                        Search Resources
                    </button>
                </div>
            </div>
        </section>

        <!-- Document Resource Grid -->
        <?php 
        $docs_section = array_values(array_filter($sections, fn($s) => $s['section_key'] === 'platform_guides'))[0] ?? null;
        if ($docs_section): 
        ?>
        <section class="mb-32 resource-section">
            <div class="flex flex-col md:flex-row items-end justify-between gap-8 mb-20 px-4">
                <div class="max-w-4xl">
                    <h2 class="text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($docs_section['section_title']); ?></h2>
                    <div class="h-2 w-32 bg-blue-600 rounded-full mb-8"></div>
                    <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-bold leading-relaxed"><?php echo strip_tags($docs_section['section_subtitle']); ?></p>
                </div>
                <div class="flex gap-4">
                    <button class="p-6 bg-white dark:bg-gray-800 rounded-3xl shadow-lg border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all leading-none">
                        <span class="material-symbols-outlined text-4xl text-gray-600 dark:text-gray-400">grid_view</span>
                    </button>
                    <button class="p-6 bg-blue-600 rounded-3xl shadow-lg hover:bg-blue-700 transition-all leading-none">
                        <span class="material-symbols-outlined text-4xl text-white">list</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                <?php foreach ($grouped_items['platform_guides'] ?? [] as $doc): ?>
                <div class="resource-card hover-lift p-10 bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-700 flex flex-col group">
                    <div class="w-24 h-24 rounded-3xl bg-<?php echo strip_tags($doc['item_color'] ?? 'blue-600'); ?> flex items-center justify-center text-white shadow-xl mb-10 group-hover:rotate-6 transition-transform">
                        <span class="material-symbols-outlined text-6xl text-white"><?php echo strip_tags($doc['item_icon'] ?? 'picture_as_pdf'); ?></span>
                    </div>
                    <h3 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-6 leading-tight"><?php echo strip_tags($doc['item_title']); ?></h3>
                    <p class="text-lg md:text-xl text-gray-600 dark:text-gray-400 mb-10 flex-grow leading-relaxed font-medium">
                        <?php echo strip_tags($doc['item_description']); ?>
                    </p>
                    <div class="flex items-center justify-between pt-10 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex flex-col">
                            <span class="text-lg font-black text-gray-400 uppercase tracking-widest">Modified</span>
                            <span class="text-xl font-bold text-gray-700 dark:text-gray-300">Jan 2024</span>
                        </div>
                        <a href="<?php echo strip_tags($doc['item_link']); ?>" target="_blank" class="px-10 py-5 bg-gray-900 dark:bg-blue-600 text-white text-xl font-bold rounded-2xl hover:brightness-110 shadow-lg flex items-center gap-3">
                            Read Now
                            <span class="material-symbols-outlined text-white">arrow_forward</span>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Email Activation Guides -->
        <?php 
        $email_section = array_values(array_filter($sections, fn($s) => $s['section_key'] === 'email_activation'))[0] ?? null;
        if ($email_section): 
        ?>
        <section class="mb-32 resource-section">
            <div class="max-w-4xl mb-20 px-4">
                <h2 class="text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($email_section['section_title']); ?></h2>
                <div class="h-2 w-32 bg-orange-500 rounded-full mb-8"></div>
                <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-bold leading-relaxed"><?php echo strip_tags($email_section['section_subtitle']); ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <?php foreach ($grouped_items['email_activation'] ?? [] as $doc): ?>
                <div class="resource-card hover-lift p-10 bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-700 flex gap-8 items-center group">
                    <div class="w-24 h-24 shrink-0 rounded-3xl bg-<?php echo strip_tags($doc['item_color'] ?? 'orange-600'); ?> flex items-center justify-center text-white shadow-xl group-hover:rotate-6 transition-transform">
                        <span class="material-symbols-outlined text-6xl text-white"><?php echo strip_tags($doc['item_icon'] ?? 'mail'); ?></span>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($doc['item_title']); ?></h3>
                        <p class="text-lg md:text-xl text-gray-600 dark:text-gray-400 mb-6 font-medium"><?php echo strip_tags($doc['item_description']); ?></p>
                        <a href="<?php echo strip_tags($doc['item_link']); ?>" target="_blank" class="inline-flex items-center gap-3 px-8 py-4 bg-orange-500 text-white text-xl font-bold rounded-2xl hover:bg-orange-600 transition-all shadow-lg">
                            Download Guide
                            <span class="material-symbols-outlined text-white">download</span>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Video Tutorials Section -->
        <?php 
        $video_section = array_values(array_filter($sections, fn($s) => $s['section_key'] === 'video_tutorials'))[0] ?? null;
        if ($video_section): 
        ?>
        <section class="py-32 px-10 md:px-20 bg-gray-900 rounded-[4rem] relative overflow-hidden mb-32 resource-section">
            <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
            
            <div class="relative z-10">
                <div class="text-center mb-24">
                    <h2 class="text-6xl font-black text-white mb-6"><?php echo strip_tags($video_section['section_title']); ?></h2>
                    <p class="text-2xl text-blue-200/80 font-bold max-w-3xl mx-auto leading-relaxed"><?php echo strip_tags($video_section['section_subtitle']); ?></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                    <?php foreach ($grouped_items['video_tutorials'] ?? [] as $video): ?>
                    <div class="resource-card hover-lift flex flex-col group p-8 bg-white/5 backdrop-blur-sm rounded-[3rem] border border-white/10 h-full">
                        <div class="video-card-thumb mb-8 shadow-2xl">
                             <img src="<?php echo strip_tags($video['item_image'] ?: 'https://img.youtube.com/vi/'.(explode('embed/', $video['item_link'])[1] ?? 'dQw4w9WgXcQ').'/maxresdefault.jpg'); ?>" 
                                 alt="Video Thumbnail" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                             <a href="<?php echo strip_tags($video['item_link']); ?>" target="_blank" class="play-btn shadow-2xl">
                                 <span class="material-symbols-outlined text-blue-600 text-5xl">play_arrow</span>
                             </a>
                             <span class="absolute bottom-6 right-6 px-4 py-2 bg-black/60 backdrop-blur-md text-white text-lg font-bold rounded-xl z-20">10:45</span>
                        </div>
                        <h3 class="text-2xl font-black text-white mb-4 group-hover:text-yellow-400 transition-colors"><?php echo strip_tags($video['item_title']); ?></h3>
                        <p class="text-lg text-blue-100/70 font-medium mb-8 line-clamp-3 flex-grow"><?php echo strip_tags($video['item_description']); ?></p>
                        <div class="flex items-center gap-4 pt-6 border-t border-white/10">
                            <div class="w-10 h-10 rounded-full overflow-hidden bg-blue-600 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-white text-xl">person</span>
                            </div>
                            <span class="text-lg font-bold text-gray-400">VVU IT Support Team</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="text-center mt-24">
                    <a href="#" class="inline-flex items-center px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-2xl font-black rounded-3xl transition-all backdrop-blur-xl border border-white/20 shadow-2xl">
                        Browse All Video Lessons
                        <span class="material-symbols-outlined ml-4 text-3xl">video_library</span>
                    </a>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Support Section -->
        <section class="py-32 text-center">
            <div class="max-w-6xl mx-auto p-16 md:p-24 bg-gradient-to-br from-blue-600 to-blue-800 rounded-[4rem] shadow-2xl relative overflow-hidden">
                <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                <div class="relative z-10">
                    <h2 class="text-5xl md:text-6xl font-black text-white mb-8"><?php echo strip_tags($hero['cta_title'] ?? 'Still Lost? We\'re Online.'); ?></h2>
                    <p class="text-2xl md:text-3xl text-blue-100 mb-16 leading-relaxed font-bold">
                        <?php echo strip_tags($hero['cta_subtitle'] ?? 'Our IT support desk is available to help you navigate our digital learning platforms 24/7.'); ?>
                    </p>
                    <div class="flex flex-col lg:flex-row gap-10 justify-center items-center">
                        <a href="mailto:it.support@vvu.edu.gh" class="w-full lg:w-auto px-16 py-8 bg-yellow-400 text-blue-900 text-3xl font-black rounded-[2rem] hover:bg-yellow-300 transition-all shadow-xl flex items-center justify-center gap-6">
                            <span class="material-symbols-outlined text-5xl">mail</span>
                            <?php echo strip_tags($hero['cta_button_text'] ?? 'it.support@vvu.edu.gh'); ?>
                        </a>
                        <a href="#" class="w-full lg:w-auto px-16 py-8 bg-white/10 text-white text-3xl font-black rounded-[2rem] border-2 border-white/30 backdrop-blur-xl hover:bg-white/20 transition-all flex items-center justify-center gap-6">
                            <span class="material-symbols-outlined text-5xl">headset_mic</span>
                            Live Chat Help
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div id="noResults" class="hidden text-center py-20 mb-32">
        <span class="material-symbols-outlined text-gray-300 text-9xl mb-8">search_off</span>
        <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-4">No matching resources found</h3>
        <p class="text-2xl text-gray-500 font-bold">Try searching for different keywords or browse the sections below.</p>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('resourceSearch');
    const searchBtn = document.getElementById('searchBtn');
    const sections = document.querySelectorAll('.resource-section');
    const cards = document.querySelectorAll('.resource-card');
    const noResults = document.getElementById('noResults');

    const performSearch = () => {
        const query = searchInput.value.toLowerCase().trim();
        let anyVisible = false;

        sections.forEach(section => {
            let sectionHasMatches = false;
            const contentCards = section.querySelectorAll('.resource-card');
            
            contentCards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const description = card.querySelector('p').textContent.toLowerCase();
                
                if (title.includes(query) || description.includes(query)) {
                    card.style.display = 'flex';
                    sectionHasMatches = true;
                    anyVisible = true;
                } else {
                    card.style.display = 'none';
                }
            });

            // Toggle section visibility
            section.style.display = sectionHasMatches ? 'block' : 'none';
        });

        // Show/Hide "No Results" message
        noResults.classList.toggle('hidden', anyVisible);
    };

    searchInput.addEventListener('input', performSearch);
    searchBtn.addEventListener('click', performSearch);
    
    // Support enter key in search input
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') performSearch();
    });
});
</script>

<?php include 'includes/footer.php'; ?>
