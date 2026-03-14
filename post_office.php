<?php
$page_title = "University Post Office - Valley View University";
$active_page = "ventures";
include 'includes/header.php';
require_once 'includes/db_connect.php';

$stmt = $pdo->prepare("SELECT * FROM ventures_pages_content WHERE page_key = 'post_office'");
$stmt->execute();
$content = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM ventures_pages_sections WHERE page_key = 'post_office' ORDER BY display_order");
$stmt->execute();
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sections_map = [];
foreach ($sections as $s) $sections_map[$s['section_key']] = $s;

$stmt = $pdo->prepare("SELECT * FROM ventures_pages_items WHERE page_key = 'post_office' AND is_active = 1 ORDER BY section_key, display_order");
$stmt->execute();
$all_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
$items = [];
foreach ($all_items as $item) $items[$item['section_key']][] = $item;

$stmt = $pdo->prepare("SELECT * FROM ventures_pages_stats WHERE page_key = 'post_office' ORDER BY display_order");
$stmt->execute();
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$content) $content = ['hero_badge'=>'Campus Essential Services','hero_title'=>'University Post Office','hero_subtitle'=>'','hero_description'=>'','hero_image'=>'','about_heading'=>'','about_text'=>'','cta_heading'=>'','cta_text'=>'','contact_phone'=>'','contact_email'=>'','contact_location'=>'','contact_hours'=>'','extra_field_1'=>''];
?>

<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slowZoom { 0% { transform: scale(1); } 100% { transform: scale(1.1); } }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
    .glass { background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); }
    .dark .glass { background: rgba(31,41,55,0.7); border: 1px solid rgba(255,255,255,0.1); }
    .service-card { transition: all 0.3s ease; }
    .service-card:hover { transform: translateY(-10px); }
    .post-gradient { background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 50%, #60a5fa 100%); }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($content['hero_image']); ?>" alt="University Post Office" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-blue-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-blue-400"><?php echo strip_tags($content['hero_badge']); ?></span>
                </div>
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($content['hero_title']); ?>
                </h1>
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-5xl mx-auto animate-fadeInUp font-bold drop-shadow-lg" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($content['hero_subtitle']); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- About -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center max-w-[90rem] mx-auto">
                <div class="lg:col-span-5 relative">
                    <div class="max-w-md mx-auto lg:max-w-full">
                        <?php if (!empty($content['extra_field_1'])): ?>
                        <img src="<?php echo strip_tags($content['extra_field_1']); ?>" alt="Post Office" class="relative z-10 rounded-[3rem] shadow-2xl w-full object-cover aspect-square">
                        <?php endif; ?>
                        <?php if (!empty($stats)): ?>
                        <div class="flex gap-6 mt-8 justify-center">
                            <?php foreach ($stats as $stat): ?>
                            <div class="p-6 bg-blue-50 dark:bg-blue-900/30 rounded-2xl text-center flex-1">
                                <div class="text-4xl font-black text-blue-600"><?php echo strip_tags($stat['stat_value']); ?></div>
                                <div class="text-lg font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider"><?php echo strip_tags($stat['stat_label']); ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="lg:col-span-7">
                    <h2 class="text-6xl sm:text-7xl font-black text-gray-900 dark:text-white mb-8 leading-tight"><?php echo strip_tags($content['about_heading']); ?></h2>
                    <p class="text-2xl md:text-[1.75rem] lg:text-3xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium"><?php echo strip_tags($content['about_text']); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services -->
    <?php $sec = $sections_map['services'] ?? null; if ($sec && !empty($items['services'])): ?>
    <section class="py-24 bg-gray-50 dark:bg-gray-800/50">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($sec['section_title']); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-2xl md:text-[1.75rem] lg:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($sec['section_subtitle']??''); ?></p>
            </div>
            <?php 
            $service_colors = ['blue', 'yellow', 'purple', 'orange'];
            $i = 0;
            ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 max-w-7xl mx-auto">
                <?php foreach ($items['services'] as $service): 
                    $color = $service['item_color'] ?: ($service_colors[$i % count($service_colors)]);
                    $i++;
                ?>
                <div class="service-card group">
                    <div class="h-full glass p-10 rounded-3xl shadow-xl border-l-8 border-<?php echo $color; ?>-500 flex flex-col">
                        <div class="flex items-center gap-6 mb-6">
                            <div class="w-20 h-20 rounded-2xl post-gradient flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined text-4xl text-white"><?php echo strip_tags($service['item_icon']); ?></span>
                            </div>
                            <h3 class="text-3xl font-black text-gray-900 dark:text-white"><?php echo strip_tags($service['item_title']); ?></h3>
                        </div>
                        <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400 leading-relaxed flex-grow"><?php echo strip_tags($service['item_description']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Financial Services -->
    <?php $sec = $sections_map['financial'] ?? null; if ($sec && !empty($items['financial'])): ?>
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($sec['section_title']); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-2xl md:text-[1.75rem] lg:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($sec['section_subtitle']??''); ?></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 max-w-7xl mx-auto">
                <?php 
                $fin_icons = [
                    'Western Union' => ['bg' => '#FFCC00', 'text' => '#000000', 'letter' => 'WU', 'icon_bg' => 'rgba(0,0,0,0.08)'],
                    'MoneyGram' => ['bg' => '#E11B22', 'text' => '#ffffff', 'letter' => 'MG', 'icon_bg' => 'rgba(255,255,255,0.15)'],
                    'Cash Post' => ['bg' => '#2563eb', 'text' => '#ffffff', 'letter' => 'CP', 'icon_bg' => 'rgba(255,255,255,0.15)']
                ];
                foreach ($items['financial'] as $fin): 
                    $brand = $fin_icons[$fin['item_title']] ?? ['bg' => '#3b82f6', 'text' => '#fff', 'letter' => '?', 'icon_bg' => 'rgba(255,255,255,0.15)'];
                ?>
                <div class="relative group overflow-hidden rounded-3xl shadow-2xl transition-transform duration-300 hover:-translate-y-2 hover:shadow-3xl">
                    <div class="p-10 h-full flex flex-col items-center text-center" style="background-color: <?php echo $brand['bg']; ?>;">
                        <div class="w-24 h-24 rounded-full flex items-center justify-center mb-8 shadow-lg group-hover:scale-110 transition-transform" style="background: <?php echo $brand['icon_bg']; ?>;">
                            <span class="text-4xl font-black" style="color: <?php echo $brand['text']; ?>; opacity: 0.9;"><?php echo $brand['letter']; ?></span>
                        </div>
                        <h3 class="text-4xl font-black mb-4" style="color: <?php echo $brand['text']; ?>;"><?php echo strip_tags($fin['item_title']); ?></h3>
                        <p class="text-xl md:text-2xl leading-relaxed" style="color: <?php echo $brand['text']; ?>; opacity: 0.85;"><?php echo strip_tags($fin['item_description']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA -->
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 post-gradient"></div>
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white mb-8 leading-tight tracking-tight"><?php echo strip_tags($content['cta_heading']); ?></h2>
                <p class="text-2xl sm:text-3xl md:text-4xl text-white/90 mb-12 max-w-4xl mx-auto leading-relaxed font-medium"><?php echo strip_tags($content['cta_text']); ?></p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="contact_us.php" class="px-10 py-5 bg-white hover:bg-gray-100 text-blue-700 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">contact_support</span> Contact Us
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact -->
    <section class="py-24 bg-gray-50 dark:bg-gray-800/50">
        <div class="container mx-auto px-4">
            <div class="max-w-7xl mx-auto text-center">
                <h2 class="text-6xl font-black text-gray-900 dark:text-white mb-16">Contact & Hours</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="p-10 glass rounded-[3rem] shadow-xl flex flex-col items-center">
                        <div class="w-20 h-20 post-gradient rounded-3xl flex items-center justify-center text-white mb-6 shadow-lg"><span class="material-symbols-outlined text-5xl text-white">location_on</span></div>
                        <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Location</h4>
                        <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed"><?php echo nl2br(strip_tags($content['contact_location'])); ?></p>
                    </div>
                    <div class="p-10 glass rounded-[3rem] shadow-xl flex flex-col items-center">
                        <div class="w-20 h-20 post-gradient rounded-3xl flex items-center justify-center text-white mb-6 shadow-lg"><span class="material-symbols-outlined text-5xl text-white">call</span></div>
                        <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Phone</h4>
                        <p class="text-lg text-gray-600 dark:text-gray-400"><?php echo strip_tags($content['contact_phone']); ?></p>
                    </div>
                    <div class="p-10 glass rounded-[3rem] shadow-xl flex flex-col items-center">
                        <div class="w-20 h-20 post-gradient rounded-3xl flex items-center justify-center text-white mb-6 shadow-lg"><span class="material-symbols-outlined text-5xl text-white">mail</span></div>
                        <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Email</h4>
                        <p class="text-lg text-gray-600 dark:text-gray-400"><?php echo strip_tags($content['contact_email']); ?></p>
                    </div>
                    <div class="p-10 glass rounded-[3rem] shadow-xl flex flex-col items-center">
                        <div class="w-20 h-20 post-gradient rounded-3xl flex items-center justify-center text-white mb-6 shadow-lg"><span class="material-symbols-outlined text-5xl text-white">schedule</span></div>
                        <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Hours</h4>
                        <p class="text-lg text-gray-600 dark:text-gray-400"><?php echo nl2br(strip_tags(str_replace(', ', "\n", $content['contact_hours']))); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
