<?php
require_once 'includes/db_connect.php';
$page_title = "FAQs - Valley View University";
$active_page = "about";

// Fetch FAQ data
$faq_hero = $pdo->query("SELECT * FROM faq_hero WHERE is_active = 1 LIMIT 1")->fetch();
$faq_trending = $pdo->query("SELECT * FROM faq_trending WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
$faq_categories = $pdo->query("SELECT * FROM faq_categories WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
$faqs_all = $pdo->query("SELECT * FROM faqs WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
$faq_docs = $pdo->query("SELECT * FROM faq_docs WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
$faq_support = $pdo->query("SELECT * FROM faq_support WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();

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
    .faq-card {
        transition: all 0.3s ease;
    }
    .faq-card:hover {
        transform: translateY(-5px);
    }
    details summary::-webkit-details-marker {
        display: none;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($faq_hero['hero_image_url'] ?? 'vvu_faq_hero_1766876441891.png'); ?>" 
                 alt="VVU Help Desk" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($faq_hero['badge_text'] ?? 'Support Center'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($faq_hero['title_black'] ?? 'Frequently Asked'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($faq_hero['title_gradient'] ?? 'Questions'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($faq_hero['description'] ?? '"Find answers to common inquiries about admissions, academics, and life at Valley View University."'); ?>
                </p>
            </div>
        </div>
    </section>



    <!-- Top Trending Questions -->
    <section class="py-12 bg-transparent">
        <div class="container px-4">
            <div class="max-w-6xl mx-auto">
                <div class="flex items-end justify-between mb-12">
                    <div>
                        <span class="text-blue-600 font-black tracking-widest uppercase text-xl mb-4 block">Quick Help</span>
                        <h2 class="text-5xl md:text-6xl font-black text-gray-900 dark:text-white">Trending Questions</h2>
                    </div>
                    <div class="hidden md:block">
                        <span class="material-symbols-outlined text-gray-300 text-8xl">trending_up</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($faq_trending as $trend): ?>
                    <div class="group bg-white dark:bg-gray-800 p-10 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-xl hover:shadow-2xl transition-all hover:-translate-y-2">
                        <div class="w-16 h-16 rounded-2xl <?php echo strip_tags($trend['bg_color_class']); ?> dark:bg-opacity-20 flex items-center justify-center <?php echo strip_tags($trend['icon_color_class']); ?> mb-8 <?php echo strip_tags($trend['hover_bg_class']); ?> group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-3xl font-bold"><?php echo strip_tags($trend['icon']); ?></span>
                        </div>
                        <h4 class="text-2xl font-black text-gray-900 dark:text-white mb-6 leading-tight"><?php echo strip_tags($trend['question']); ?></h4>
                        <p class="text-xl text-gray-500 dark:text-gray-400 mb-8 font-medium"><?php echo strip_tags($trend['answer']); ?></p>

                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Main FAQ Explorer -->
    <section class="py-32 relative overflow-hidden bg-white dark:bg-gray-950">
        <!-- Modern background accents -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-500/5 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-yellow-500/5 rounded-full blur-[120px]"></div>
        </div>

        <div class="container relative z-10 px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <span class="inline-block px-6 py-2 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold text-sm uppercase tracking-widest mb-6">Browse by Topic</span>
                    <h2 class="text-5xl md:text-7xl font-black text-gray-900 dark:text-white mb-4">Support Categories</h2>
                    <p class="text-2xl text-gray-500 dark:text-gray-400 font-medium">Select a category to find specialized answers</p>
                </div>

                <!-- Category Tabs (Desktop) -->
                <div class="flex flex-wrap justify-center gap-6 mb-20">
                    <div class="inline-flex p-3 bg-gray-100/50 dark:bg-gray-800/50 backdrop-blur-md rounded-[2.5rem] border border-gray-200 dark:border-gray-700/50 shadow-inner">
                        <?php if (!empty($faq_categories)): ?>
                            <?php foreach ($faq_categories as $index => $cat): ?>
                                <button onclick="showCategory('<?php echo strip_tags($cat['category_slug']); ?>')" 
                                        class="faq-tab <?php echo $index === 0 ? 'active' : ''; ?> px-10 py-5 rounded-[2rem] text-xl font-bold transition-all flex items-center gap-4 <?php echo $index === 0 ? 'bg-white dark:bg-gray-700 text-blue-600 shadow-xl' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white'; ?>">
                                    <span class="material-symbols-outlined text-2xl <?php echo $index === 0 ? 'text-blue-600' : 'text-gray-400'; ?>"><?php echo strip_tags($cat['icon']); ?></span>
                                    <?php echo strip_tags($cat['category_name']); ?>
                                </button>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- FAQ Lists Content -->
                <div id="faq-container" class="relative">
                    <?php foreach ($faq_categories as $index => $cat): ?>
                    <div class="faq-section <?php echo $index === 0 ? '' : 'hidden opacity-0 translate-y-8'; ?> transition-all duration-700" data-category="<?php echo strip_tags($cat['category_slug']); ?>">
                        <div class="grid grid-cols-1 gap-8">
                            <?php 
                            $cat_faqs = array_filter($faqs_all, function($f) use ($cat) {
                                return $f['category_id'] == $cat['id'];
                            });
                            ?>
                            <?php if (!empty($cat_faqs)): ?>
                                <?php foreach ($cat_faqs as $faq): ?>
                                <div class="faq-item group">
                                    <details class="glass rounded-[3rem] overflow-hidden border border-gray-100 dark:border-gray-800 shadow-xl hover:shadow-2xl hover:border-blue-200 dark:hover:border-blue-900 transition-all duration-500 bg-white/60 dark:bg-gray-800/40 backdrop-blur-xl">
                                        <summary class="flex items-center justify-between p-10 cursor-pointer list-none">
                                            <div class="flex items-center gap-8">
                                                <div class="w-16 h-16 rounded-[1.25rem] bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white shadow-lg shadow-blue-500/30 group-open:scale-110 transition-transform duration-500">
                                                    <span class="material-symbols-outlined text-3xl font-bold"><?php echo strip_tags($faq['icon']); ?></span>
                                                </div>
                                                <h3 class="text-3xl font-black text-gray-900 dark:text-white leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"><?php echo strip_tags($faq['question']); ?></h3>
                                            </div>
                                            <div class="w-12 h-12 rounded-full border-2 border-gray-100 dark:border-gray-700 flex items-center justify-center text-blue-600 group-hover:border-blue-600 transition-all duration-500 group-open:rotate-180 group-open:bg-blue-600 group-open:text-white">
                                                <span class="material-symbols-outlined text-3xl">expand_more</span>
                                            </div>
                                        </summary>
                                        <div class="px-12 pb-12 pt-4">
                                            <div class="w-full h-px bg-gradient-to-r from-transparent via-gray-200 dark:via-gray-700 to-transparent mb-10"></div>
                                            <div class="prose prose-2xl prose-blue dark:prose-invert max-w-none">
                                                <p class="text-2xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium">
                                                    <?php echo nl2br(strip_tags($faq['answer'])); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </details>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-20 px-10 rounded-[3rem] bg-gray-50 dark:bg-gray-900/50 border-2 border-dashed border-gray-200 dark:border-gray-800">
                                    <span class="material-symbols-outlined text-8xl text-gray-300 mb-6">psychology_alt</span>
                                    <h4 class="text-3xl font-bold text-gray-400">No questions found for this category.</h4>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Document Center Section (Suggested) -->
    <section class="py-24 bg-gray-50 dark:bg-gray-800/30">
        <div class="container px-4">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <span class="text-blue-600 font-black tracking-widest uppercase text-xl mb-4 block">Downloads</span>
                    <h2 class="text-5xl sm:text-6xl font-black text-gray-900 dark:text-white mb-6">Document Center</h2>
                    <p class="text-2xl text-gray-500 dark:text-gray-400 font-medium max-w-2xl mx-auto">Get easy access to the most requested forms and guides.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php foreach ($faq_docs as $doc): ?>
                    <a href="<?php echo strip_tags($doc['file_url']); ?>" class="group glass p-8 rounded-[2rem] border border-white/20 dark:border-gray-700 hover:bg-blue-600 transition-all shadow-xl hover:-translate-y-2">
                        <div class="w-16 h-16 rounded-2xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600 mb-6 group-hover:bg-white group-hover:text-blue-600">
                            <span class="material-symbols-outlined text-4xl"><?php echo strip_tags($doc['icon']); ?></span>
                        </div>
                        <h5 class="text-2xl font-black text-gray-900 dark:text-white mb-2 group-hover:text-white"><?php echo strip_tags($doc['title']); ?></h5>
                        <p class="text-lg text-gray-500 dark:text-gray-400 group-hover:text-blue-100"><?php echo strip_tags($doc['file_info']); ?></p>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Support Resources Grid (Enhanced Suggested Content) -->
    <section class="py-24 bg-blue-900 overflow-hidden relative">
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-yellow-400/10 rounded-full blur-[180px] -mr-96 -mt-96"></div>
        <div class="absolute bottom-0 left-0 w-[800px] h-[800px] bg-blue-400/10 rounded-full blur-[180px] -ml-96 -mb-96"></div>
        
        <div class="container relative z-10 px-4">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-5xl sm:text-7xl font-black text-white mb-6">Need Immediate Help?</h2>
                    <p class="text-2xl text-blue-100 font-medium max-w-3xl mx-auto">Our support networks are active 24/7 to ensure your university experience is smooth and rewarding.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <?php foreach ($faq_support as $sup): ?>
                    <div class="bg-white/10 backdrop-blur-xl p-12 rounded-[3.5rem] border border-white/10 hover:border-white/30 hover:bg-white/15 transition-all group">
                        <div class="w-20 h-20 rounded-3xl <?php echo strip_tags($sup['icon_bg_color']); ?> flex items-center justify-center text-blue-900 mb-10 group-hover:scale-110 group-hover:rotate-6 transition-all">
                            <span class="material-symbols-outlined text-4xl font-bold"><?php echo strip_tags($sup['icon']); ?></span>
                        </div>
                        <h4 class="text-4xl font-black text-white mb-6"><?php echo strip_tags($sup['title']); ?></h4>
                        <p class="text-xl text-blue-100/80 mb-10 leading-relaxed"><?php echo strip_tags($sup['description']); ?></p>
                        <a href="<?php echo strip_tags($sup['btn_link']); ?>" class="<?php echo strip_tags($sup['btn_color_class']); ?> font-black text-2xl flex items-center gap-3 hover:gap-6 transition-all">
                            <?php echo strip_tags($sup['btn_text']); ?> <span class="material-symbols-outlined">chevron_right</span>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Scripts for FAQ -->
    <script>
        function showCategory(category) {
            // Update tabs
            document.querySelectorAll('.faq-tab').forEach(tab => {
                tab.classList.remove('bg-white', 'dark:bg-gray-700', 'text-blue-600', 'shadow-xl', 'active');
                tab.classList.add('text-gray-500');
                
                // Icon update
                const icon = tab.querySelector('.material-symbols-outlined');
                if(icon) icon.classList.replace('text-blue-600', 'text-gray-400');
            });
            
            const activeTab = event.currentTarget;
            activeTab.classList.add('bg-white', 'dark:bg-gray-700', 'text-blue-600', 'shadow-xl', 'active');
            activeTab.classList.remove('text-gray-500');
            
            const activeIcon = activeTab.querySelector('.material-symbols-outlined');
            if(activeIcon) activeIcon.classList.replace('text-gray-400', 'text-blue-600');

            // Update sections with animation
            document.querySelectorAll('.faq-section').forEach(section => {
                section.classList.add('hidden', 'opacity-0', 'translate-y-8');
                if (section.getAttribute('data-category') === category) {
                    section.classList.remove('hidden');
                    // Small delay to trigger transition
                    setTimeout(() => {
                        section.classList.remove('opacity-0', 'translate-y-8');
                    }, 50);
                }
            });
        }


    </script>
    
    <style>
        .faq-tab.active {
            transform: translateY(-2px);
        }
        details > summary {
            list-style: none;
        }
        details > summary::-webkit-details-marker {
            display: none;
        }
        .prose-2xl p {
            line-height: 1.8;
        }
        .pl-22 { padding-left: 5.5rem; }
    </style>

    <!-- CTA Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl font-black text-gray-900 dark:text-white mb-8">Still have questions?</h2>
                <p class="text-2xl text-gray-600 dark:text-gray-400 mb-12 font-medium leading-relaxed">
                    If you couldn't find the answer you were looking for, please don't hesitate to reach out to us. Our team is always ready to help.
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="contact_us.php" class="px-12 py-6 bg-blue-600 hover:bg-blue-700 text-white text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl">mail</span>
                        Contact Us
                    </a>
                    <a href="tel:+233307011867" class="px-12 py-6 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-white text-2xl font-bold rounded-2xl transition-all border-2 border-gray-200 dark:border-gray-700 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl">call</span>
                        Call Admissions
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>