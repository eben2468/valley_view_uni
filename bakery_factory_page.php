<?php
$page_title = "Bakery Factory - Valley View University";
$active_page = "ventures";
include 'includes/header.php';
require_once 'includes/db_connect.php';

// Fetch page content
$stmt = $pdo->prepare("SELECT * FROM ventures_pages_content WHERE page_key = 'bakery_factory'");
$stmt->execute();
$content = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch sections
$stmt = $pdo->prepare("SELECT * FROM ventures_pages_sections WHERE page_key = 'bakery_factory' ORDER BY display_order");
$stmt->execute();
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sections_map = [];
foreach ($sections as $s) $sections_map[$s['section_key']] = $s;

// Fetch items by section
$stmt = $pdo->prepare("SELECT * FROM ventures_pages_items WHERE page_key = 'bakery_factory' AND is_active = 1 ORDER BY section_key, display_order");
$stmt->execute();
$all_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
$items = [];
foreach ($all_items as $item) $items[$item['section_key']][] = $item;

// Fetch stats
$stmt = $pdo->prepare("SELECT * FROM ventures_pages_stats WHERE page_key = 'bakery_factory' ORDER BY display_order");
$stmt->execute();
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fallbacks
if (!$content) {
    $content = ['hero_badge'=>'VVU Ventures','hero_title'=>'Bakery Factory','hero_subtitle'=>'Freshly Baked, Naturally Healthy','hero_description'=>'','hero_image'=>'','about_heading'=>'','about_text'=>'','about_image'=>'','cta_heading'=>'','cta_subtitle'=>'','contact_phone'=>'','contact_whatsapp'=>'','contact_location'=>'','contact_address'=>''];
}
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
    .product-card {
        transition: all 0.3s ease;
    }
    .product-card:hover {
        transform: translateY(-10px);
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[70vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($content['hero_image']); ?>" 
                 alt="VVU Bakery Factory" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-amber-900/80 via-amber-900/40 to-gray-900"></div>
        </div>
        <div class="container relative z-10 py-24 mx-auto px-4">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($content['hero_badge']); ?></span>
                </div>
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($content['hero_title']); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($content['hero_subtitle']); ?></span>
                </h1>
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($content['hero_description']); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Specialty Breads Section -->
    <?php $sec = $sections_map['specialty_breads'] ?? null; if ($sec && !empty($items['specialty_breads'])): ?>
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($sec['section_title']); ?></h2>
                <div class="h-2 w-40 bg-amber-600 mx-auto rounded-full mb-8"></div>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($sec['section_subtitle'] ?? ''); ?></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-12">
                <?php foreach ($items['specialty_breads'] as $bread): ?>
                <div class="product-card relative group">
                    <div class="relative h-full glass p-10 rounded-3xl shadow-xl border-t-8 border-<?php echo strip_tags($bread['item_color']); ?> flex flex-col">
                        <div class="w-24 h-24 rounded-3xl bg-<?php echo strip_tags($bread['item_color']); ?> flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($bread['item_icon']); ?></span>
                        </div>
                        <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($bread['item_title']); ?></h3>
                        <p class="text-2xl text-gray-600 dark:text-gray-400 leading-relaxed mb-8 flex-grow"><?php echo strip_tags($bread['item_description']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Quality & Operations Section -->
    <?php $sec = $sections_map['quality_features'] ?? null; if ($sec && !empty($items['quality_features'])): ?>
    <section class="py-24 bg-gray-50 dark:bg-gray-800/50">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
                <div class="lg:col-span-5 relative">
                    <div class="absolute -top-10 -left-10 w-40 h-40 bg-amber-600/10 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-yellow-400/10 rounded-full blur-3xl"></div>
                    <div class="max-w-md mx-auto lg:max-w-full">
                        <img src="<?php echo strip_tags($content['about_image']); ?>" 
                             alt="Bakery Logo" class="relative z-10 rounded-[3rem] shadow-2xl w-full object-cover aspect-square">
                    </div>
                </div>
                <div class="lg:col-span-7 flex flex-col gap-12">
                    <div>
                        <h2 class="text-6xl sm:text-7xl font-black text-gray-900 dark:text-white mb-8 leading-tight"><?php echo strip_tags($content['about_heading']); ?></h2>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium"><?php echo strip_tags($content['about_text']); ?></p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-10">
                        <?php foreach ($items['quality_features'] as $feat): ?>
                        <div class="flex gap-8 items-start">
                            <div class="w-20 h-20 shrink-0 rounded-2xl bg-<?php echo strip_tags($feat['item_color']); ?> flex items-center justify-center text-white shadow-lg">
                                <span class="material-symbols-outlined text-4xl text-white"><?php echo strip_tags($feat['item_icon']); ?></span>
                            </div>
                            <div>
                                <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-3"><?php echo strip_tags($feat['item_title']); ?></h4>
                                <p class="text-2xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($feat['item_description']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Distribution Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="max-w-[90rem] mx-auto glass p-16 md:p-24 rounded-[5rem] shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-96 h-96 bg-amber-600/5 rounded-full -mr-48 -mt-48 blur-3xl"></div>
                <div class="relative z-10 flex flex-col lg:flex-row gap-20 items-center">
                    <div class="lg:w-1/2">
                        <h2 class="text-6xl sm:text-7xl font-black text-gray-900 dark:text-white mb-10 leading-tight"><?php echo strip_tags($content['cta_heading']); ?></h2>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 leading-relaxed mb-12 font-medium"><?php echo strip_tags($content['cta_subtitle']); ?></p>
                        <div class="flex flex-wrap gap-8">
                            <div class="flex items-center gap-4 px-8 py-4 bg-amber-600/10 rounded-full border border-amber-600/20">
                                <span class="material-symbols-outlined text-amber-600 text-3xl">local_shipping</span>
                                <span class="text-2xl font-bold text-amber-700 dark:text-amber-400">Delivery Fleet</span>
                            </div>
                            <div class="flex items-center gap-4 px-8 py-4 bg-amber-600/10 rounded-full border border-amber-600/20">
                                <span class="material-symbols-outlined text-amber-600 text-3xl">location_on</span>
                                <span class="text-2xl font-bold text-amber-700 dark:text-amber-400">Ashaiman Depot</span>
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-1/2 grid grid-cols-2 gap-8">
                        <?php if (!empty($items['distribution'])): foreach ($items['distribution'] as $di): ?>
                        <div class="relative group overflow-hidden rounded-[2.5rem] shadow-lg aspect-square">
                            <img src="<?php echo strip_tags($di['item_image']); ?>" alt="<?php echo strip_tags($di['item_title']); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        </div>
                        <?php endforeach; endif; ?>
                        <?php foreach ($stats as $stat): ?>
                        <div class="p-10 bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-lg text-center flex flex-col justify-center">
                            <div class="text-6xl font-black text-amber-600 mb-3"><?php echo strip_tags($stat['stat_value']); ?></div>
                            <div class="text-2xl font-bold text-gray-600 dark:text-gray-400 uppercase tracking-widest"><?php echo strip_tags($stat['stat_label']); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <?php $sec = $sections_map['gallery'] ?? null; if ($sec && !empty($items['gallery'])): ?>
    <section class="py-24 bg-gray-50 dark:bg-gray-800/50">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($sec['section_title']); ?></h2>
                <div class="h-2 w-40 bg-amber-600 mx-auto rounded-full mb-8"></div>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($sec['section_subtitle'] ?? ''); ?></p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($items['gallery'] as $gi): ?>
                <div class="relative group overflow-hidden rounded-3xl shadow-xl aspect-[3/4]">
                    <img src="<?php echo strip_tags($gi['item_image']); ?>" alt="<?php echo strip_tags($gi['item_title']); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-8">
                        <h4 class="text-white text-3xl font-bold"><?php echo strip_tags($gi['item_title']); ?></h4>
                        <p class="text-white/80 text-xl"><?php echo strip_tags($gi['item_subtitle']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Testimonials -->
    <?php $sec = $sections_map['testimonials'] ?? null; if ($sec && !empty($items['testimonials'])): ?>
    <?php $test = $items['testimonials'][0]; ?>
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($sec['section_title']); ?></h2>
                <div class="h-2 w-40 bg-amber-600 mx-auto rounded-full mb-8"></div>
            </div>
            <div class="max-w-5xl mx-auto">
                <div class="glass p-12 md:p-16 rounded-[3rem] shadow-2xl relative">
                    <span class="material-symbols-outlined text-8xl text-amber-600/20 absolute top-10 left-10">format_quote</span>
                    <div class="relative z-10">
                        <p class="text-3xl md:text-4xl text-gray-700 dark:text-gray-300 italic leading-relaxed mb-10"><?php echo strip_tags($test['item_description']); ?></p>
                        <div class="flex items-center gap-6">
                            <div class="w-20 h-20 rounded-full bg-amber-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg"><?php echo strip_tags($test['item_stat_value']); ?></div>
                            <div>
                                <h4 class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo strip_tags($test['item_title']); ?></h4>
                                <p class="text-xl text-amber-600 font-medium"><?php echo strip_tags($test['item_subtitle']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Contact Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-800/50">
        <div class="container mx-auto px-4">
            <div class="max-w-7xl mx-auto text-center">
                <h2 class="text-6xl font-black text-gray-900 dark:text-white mb-16">Visit or Contact Us</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div class="p-12 glass rounded-[3rem] shadow-xl flex flex-col items-center">
                        <div class="w-24 h-24 bg-amber-600 rounded-3xl flex items-center justify-center text-white mb-8 shadow-lg">
                            <span class="material-symbols-outlined text-5xl text-white">location_on</span>
                        </div>
                        <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Location</h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400 leading-relaxed"><?php echo strip_tags($content['contact_location']); ?></p>
                    </div>
                    <div class="p-12 glass rounded-[3rem] shadow-xl flex flex-col items-center">
                        <div class="w-24 h-24 bg-amber-600 rounded-3xl flex items-center justify-center text-white mb-8 shadow-lg">
                            <span class="material-symbols-outlined text-5xl text-white">call</span>
                        </div>
                        <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Phone & WhatsApp</h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($content['contact_phone']); ?></p>
                        <?php if (!empty($content['contact_whatsapp'])): ?>
                        <p class="text-3xl text-amber-600 font-black mt-4">WhatsApp: <?php echo strip_tags($content['contact_whatsapp']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="p-12 glass rounded-[3rem] shadow-xl flex flex-col items-center">
                        <div class="w-24 h-24 bg-amber-600 rounded-3xl flex items-center justify-center text-white mb-8 shadow-lg">
                            <span class="material-symbols-outlined text-5xl text-white">mail</span>
                        </div>
                        <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Mailing Address</h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($content['contact_address']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>