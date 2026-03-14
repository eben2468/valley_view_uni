<?php
$page_title = "Water Factory - Valley View University";
$active_page = "ventures";
include 'includes/header.php';
require_once 'includes/db_connect.php';

$stmt = $pdo->prepare("SELECT * FROM ventures_pages_content WHERE page_key = 'water_factory'");
$stmt->execute();
$content = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM ventures_pages_sections WHERE page_key = 'water_factory' ORDER BY display_order");
$stmt->execute();
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sections_map = [];
foreach ($sections as $s) $sections_map[$s['section_key']] = $s;

$stmt = $pdo->prepare("SELECT * FROM ventures_pages_items WHERE page_key = 'water_factory' AND is_active = 1 ORDER BY section_key, display_order");
$stmt->execute();
$all_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
$items = [];
foreach ($all_items as $item) $items[$item['section_key']][] = $item;

$stmt = $pdo->prepare("SELECT * FROM ventures_pages_stats WHERE page_key = 'water_factory' ORDER BY display_order");
$stmt->execute();
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$content) {
    $content = ['hero_badge'=>'VVU Ventures','hero_title'=>'Water Factory','hero_subtitle'=>'Pure, Safe & Refreshing','hero_description'=>'','hero_image'=>'','about_heading'=>'','about_text'=>'','about_image'=>'','banner_image'=>'','cta_heading'=>'','cta_subtitle'=>'','cta_text'=>'','contact_phone'=>'','contact_location'=>'','contact_address'=>''];
}
?>

<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slowZoom { 0% { transform: scale(1); } 100% { transform: scale(1.1); } }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
    .glass { background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); }
    .dark .glass { background: rgba(31,41,55,0.7); border: 1px solid rgba(255,255,255,0.1); }
    .product-card { transition: all 0.3s ease; }
    .product-card:hover { transform: translateY(-10px); }
    .water-gradient { background: linear-gradient(135deg, #0077b6 0%, #00b4d8 50%, #90e0ef 100%); }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($content['hero_image']); ?>" alt="VVU Water Factory" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-cyan-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-cyan-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-cyan-400"><?php echo strip_tags($content['hero_badge']); ?></span>
                </div>
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($content['hero_title']); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-cyan-200 to-cyan-500 block mt-4"><?php echo strip_tags($content['hero_subtitle']); ?></span>
                </h1>
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($content['hero_description']); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Banner Ad -->
    <?php if (!empty($content['banner_image'])): ?>
    <section class="w-full">
        <img src="<?php echo strip_tags($content['banner_image']); ?>" alt="VVU Water Factory Advertisement" class="w-full h-auto object-cover">
    </section>
    <?php endif; ?>

    <!-- About -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="max-w-7xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($content['about_heading']); ?></h2>
                <div class="h-2 w-40 bg-cyan-600 mx-auto rounded-full mb-12"></div>
                <p class="text-2xl md:text-[1.75rem] lg:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed max-w-6xl mx-auto"><?php echo strip_tags($content['about_text']); ?></p>
            </div>
        </div>
    </section>

    <!-- Products -->
    <?php $sec = $sections_map['products'] ?? null; if ($sec && !empty($items['products'])): ?>
    <section class="py-24 bg-gray-50 dark:bg-gray-800/50">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($sec['section_title']); ?></h2>
                <div class="h-2 w-40 bg-cyan-600 mx-auto rounded-full mb-8"></div>
                <p class="text-2xl md:text-[1.75rem] lg:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($sec['section_subtitle']??''); ?></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12 max-w-7xl mx-auto">
                <?php foreach ($items['products'] as $prod): ?>
                <div class="product-card relative group">
                    <div class="relative h-full glass p-10 rounded-3xl shadow-xl border-t-8 border-<?php echo strip_tags($prod['item_color']); ?> flex flex-col items-center text-center">
                        <div class="w-28 h-28 rounded-3xl water-gradient flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-6xl text-white"><?php echo strip_tags($prod['item_icon']); ?></span>
                        </div>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($prod['item_title']); ?></h3>
                        <p class="text-2xl text-gray-600 dark:text-gray-400 leading-relaxed mb-6"><?php echo strip_tags($prod['item_description']); ?></p>
                        <?php if (!empty($prod['item_stat_value'])): ?>
                        <div class="mt-auto px-6 py-3 bg-cyan-100 dark:bg-cyan-900/30 rounded-full">
                            <span class="text-xl font-bold text-cyan-700 dark:text-cyan-300"><?php echo strip_tags($prod['item_stat_value']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Process -->
    <?php $sec = $sections_map['process'] ?? null; if ($sec && !empty($items['process'])): ?>
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($sec['section_title']); ?></h2>
                <div class="h-2 w-40 bg-cyan-600 mx-auto rounded-full mb-8"></div>
                <p class="text-2xl md:text-[1.75rem] lg:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($sec['section_subtitle']??''); ?></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 max-w-[90rem] mx-auto">
                <?php foreach ($items['process'] as $step): ?>
                <div class="relative flex flex-col items-center text-center group">
                    <div class="w-24 h-24 rounded-3xl water-gradient flex items-center justify-center text-white shadow-xl mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($step['item_icon']); ?></span>
                    </div>
                    <span class="text-6xl font-black text-cyan-600 mb-4"><?php echo strip_tags($step['item_stat_value']); ?></span>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-4"><?php echo strip_tags($step['item_title']); ?></h3>
                    <p class="text-[1.35rem] text-gray-600 dark:text-gray-400 leading-relaxed"><?php echo strip_tags($step['item_description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Quality -->
    <?php $sec = $sections_map['quality'] ?? null; if ($sec && !empty($items['quality'])): ?>
    <section class="py-24 bg-gray-50 dark:bg-gray-800/50">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center max-w-[90rem] mx-auto">
                <div class="lg:col-span-5 relative">
                    <div class="max-w-md mx-auto lg:max-w-full">
                        <img src="<?php echo strip_tags($content['about_image']); ?>" alt="Quality Control" class="relative z-10 rounded-[3rem] shadow-2xl w-full object-cover aspect-square">
                    </div>
                </div>
                <div class="lg:col-span-7 flex flex-col gap-12">
                    <div>
                        <h2 class="text-6xl sm:text-7xl font-black text-gray-900 dark:text-white mb-8 leading-tight"><?php echo strip_tags($sec['section_title']); ?></h2>
                        <p class="text-2xl md:text-[1.75rem] lg:text-3xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium"><?php echo strip_tags($sec['section_subtitle']??''); ?></p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-10">
                        <?php foreach ($items['quality'] as $q): ?>
                        <div class="flex gap-8 items-start">
                            <div class="w-20 h-20 shrink-0 rounded-2xl water-gradient flex items-center justify-center text-white shadow-lg">
                                <span class="material-symbols-outlined text-4xl text-white"><?php echo strip_tags($q['item_icon']); ?></span>
                            </div>
                            <div>
                                <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-3"><?php echo strip_tags($q['item_title']); ?></h4>
                                <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($q['item_description']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Why Choose Us -->
    <?php $sec = $sections_map['why_choose'] ?? null; if ($sec && !empty($items['why_choose'])): ?>
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($sec['section_title']); ?></h2>
                <div class="h-2 w-40 bg-cyan-600 mx-auto rounded-full mb-8"></div>
                <p class="text-2xl md:text-[1.75rem] lg:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($sec['section_subtitle']??''); ?></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 max-w-[90rem] mx-auto">
                <?php foreach ($items['why_choose'] as $wc): ?>
                <div class="group p-10 bg-gray-50 dark:bg-gray-800 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-2">
                    <div class="w-20 h-20 rounded-2xl water-gradient flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-4xl text-white"><?php echo strip_tags($wc['item_icon']); ?></span>
                    </div>
                    <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-4"><?php echo strip_tags($wc['item_title']); ?></h4>
                    <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($wc['item_description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA -->
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 water-gradient"></div>
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white mb-8 leading-tight tracking-tight">
                    <?php echo strip_tags($content['cta_heading']); ?> <br><span class="text-cyan-200 text-5xl sm:text-6xl md:text-7xl lg:text-6xl block mt-2"><?php echo strip_tags($content['cta_subtitle']); ?></span>
                </h2>
                <p class="text-2xl sm:text-3xl md:text-4xl text-white/90 mb-12 max-w-4xl mx-auto leading-relaxed font-medium"><?php echo strip_tags($content['cta_text']); ?></p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="contact_us.php" class="px-10 py-5 bg-white hover:bg-gray-100 text-cyan-700 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">handshake</span> Distributor Inquiries
                    </a>
                    <a href="tel:<?php echo str_replace(' ', '', $content['contact_phone']); ?>" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">call</span> Call Us Now
                    </a>
                </div>
                <?php if (!empty($stats)): ?>
                <div class="mt-20 grid grid-cols-1 sm:grid-cols-3 gap-12 border-t border-white/10 pt-16">
                    <?php foreach ($stats as $stat): ?>
                    <div>
                        <div class="text-6xl font-black text-white mb-2"><?php echo strip_tags($stat['stat_value']); ?></div>
                        <div class="text-cyan-200 uppercase tracking-widest text-2xl font-black"><?php echo strip_tags($stat['stat_label']); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Contact -->
    <section class="py-24 bg-gray-50 dark:bg-gray-800/50">
        <div class="container mx-auto px-4">
            <div class="max-w-7xl mx-auto text-center">
                <h2 class="text-6xl font-black text-gray-900 dark:text-white mb-16">Get In Touch</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div class="p-12 glass rounded-[3rem] shadow-xl flex flex-col items-center">
                        <div class="w-24 h-24 water-gradient rounded-3xl flex items-center justify-center text-white mb-8 shadow-lg"><span class="material-symbols-outlined text-5xl text-white">location_on</span></div>
                        <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Location</h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400 leading-relaxed"><?php echo nl2br(strip_tags($content['contact_location'])); ?></p>
                    </div>
                    <div class="p-12 glass rounded-[3rem] shadow-xl flex flex-col items-center">
                        <div class="w-24 h-24 water-gradient rounded-3xl flex items-center justify-center text-white mb-8 shadow-lg"><span class="material-symbols-outlined text-5xl text-white">call</span></div>
                        <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Phone</h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($content['contact_phone']); ?></p>
                    </div>
                    <div class="p-12 glass rounded-[3rem] shadow-xl flex flex-col items-center">
                        <div class="w-24 h-24 water-gradient rounded-3xl flex items-center justify-center text-white mb-8 shadow-lg"><span class="material-symbols-outlined text-5xl text-white">mail</span></div>
                        <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Address</h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400"><?php echo nl2br(strip_tags($content['contact_address'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>