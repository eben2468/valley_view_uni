<?php
$page_title = "Alumni Network - Valley View University";
$active_page = "about";
include 'includes/header.php';
require_once 'includes/db_connect.php';

// Fetch content
$content = $pdo->query("SELECT * FROM alumni_page_content WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$slides = $pdo->query("SELECT * FROM alumni_page_slides WHERE is_active=1 ORDER BY display_order")->fetchAll(PDO::FETCH_ASSOC);
$sections = $pdo->query("SELECT * FROM alumni_page_sections WHERE is_active=1 ORDER BY display_order")->fetchAll(PDO::FETCH_ASSOC);
$sections_map = []; foreach ($sections as $s) $sections_map[$s['section_key']] = $s;
$all_items = $pdo->query("SELECT * FROM alumni_page_items WHERE is_active=1 ORDER BY section_key, display_order")->fetchAll(PDO::FETCH_ASSOC);
$items = []; foreach ($all_items as $item) $items[$item['section_key']][] = $item;
$stats = $pdo->query("SELECT * FROM alumni_page_stats ORDER BY display_order")->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slowZoom { 0% { transform: scale(1); } 100% { transform: scale(1.1); } }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
    .glass { background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); }
    .dark .glass { background: rgba(31,41,55,0.7); border: 1px solid rgba(255,255,255,0.1); }
    .alumni-gradient { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #60a5fa 100%); }
    .alumni-gradient-gold { background: linear-gradient(135deg, #b45309 0%, #f59e0b 50%, #fbbf24 100%); }
    .benefit-card { transition: all 0.3s ease; }
    .benefit-card:hover { transform: translateY(-10px); }
    .hero-slider { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
    .hero-slide { position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: none; background: #111; }
    .hero-slide.active { display: block; }
    .hero-slide img { width: 100%; height: 100%; object-fit: cover; }
    .slider-dots { position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); display: flex; gap: 12px; z-index: 20; }
    .slider-dot { width: 14px; height: 14px; border-radius: 50%; background: rgba(255,255,255,0.5); cursor: pointer; transition: all 0.3s ease; border: 2px solid transparent; }
    .slider-dot.active { background: #fbbf24; border-color: white; transform: scale(1.2); }
    .slider-dot:hover { background: rgba(255,255,255,0.8); }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section with Image Slider -->
    <section class="relative min-h-[70vh] flex items-center overflow-hidden">
        <div class="absolute inset-0 z-0 hero-slider" id="heroSlider">
            <?php foreach ($slides as $i => $slide): ?>
            <div class="hero-slide <?php echo $i===0?'active':'';?>">
                <img src="<?php echo strip_tags($slide['image_url']); ?>" alt="<?php echo strip_tags($slide['alt_text']); ?>" class="w-full h-full object-cover">
            </div>
            <?php endforeach; ?>
        </div>
        <div class="slider-dots">
            <?php foreach ($slides as $i => $slide): ?>
            <div class="slider-dot <?php echo $i===0?'active':'';?>" data-slide="<?php echo $i;?>"></div>
            <?php endforeach; ?>
        </div>
        <div class="container relative z-10 py-24"></div>
    </section>

    <!-- Our Mission Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center max-w-[90rem] mx-auto">
                <div class="lg:col-span-5 relative">
                    <div class="absolute -top-10 -left-10 w-40 h-40 bg-blue-600/10 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-yellow-400/10 rounded-full blur-3xl"></div>
                    <div class="max-w-md mx-auto lg:max-w-full">
                        <img src="<?php echo strip_tags($content['mission_image']??''); ?>" alt="VVU Graduate" class="relative z-10 rounded-[3rem] shadow-2xl w-full object-cover">
                    </div>
                </div>
                <div class="lg:col-span-7 flex flex-col gap-8">
                    <div>
                        <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-8 leading-tight"><?php echo strip_tags($content['mission_heading']??'Our Mission'); ?></h2>
                        <div class="h-2 w-40 bg-blue-600 rounded-full mb-8"></div>
                        <p class="text-2xl md:text-[1.75rem] lg:text-3xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium mb-6"><?php echo strip_tags($content['mission_text']??''); ?></p>
                        <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400 leading-relaxed"><?php echo strip_tags($content['mission_text_2']??''); ?></p>
                    </div>
                    <?php if (!empty($stats)): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-4">
                        <?php foreach ($stats as $stat): ?>
                        <div class="text-center p-6 bg-<?php echo strip_tags($stat['stat_bg']??'blue-50');?> dark:bg-<?php echo strip_tags($stat['stat_color']??'blue');?>-900/20 rounded-2xl">
                            <div class="text-5xl font-black text-<?php echo strip_tags($stat['stat_color']??'blue');?>-600 mb-2"><?php echo strip_tags($stat['stat_value']); ?></div>
                            <div class="text-lg font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider"><?php echo strip_tags($stat['stat_label']); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <?php $sec = $sections_map['benefits'] ?? null; if ($sec && !empty($items['benefits'])): ?>
    <section class="py-24 bg-gray-50 dark:bg-gray-800/50">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($sec['section_title']); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-2xl md:text-[1.75rem] lg:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($sec['section_subtitle']??''); ?></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 max-w-[90rem] mx-auto">
                <?php foreach ($items['benefits'] as $benefit): ?>
                <div class="benefit-card group p-10 bg-white dark:bg-gray-800 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700">
                    <div class="w-20 h-20 rounded-2xl <?php echo strip_tags($benefit['item_color']); ?> flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-4xl text-white"><?php echo strip_tags($benefit['item_icon']); ?></span>
                    </div>
                    <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-4"><?php echo strip_tags($benefit['item_title']); ?></h4>
                    <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($benefit['item_description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Get Involved Section -->
    <?php $sec = $sections_map['get_involved'] ?? null; if ($sec && !empty($items['get_involved'])): ?>
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($sec['section_title']); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-2xl md:text-[1.75rem] lg:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($sec['section_subtitle']??''); ?></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-6xl mx-auto">
                <?php foreach ($items['get_involved'] as $gi): ?>
                <div class="relative group overflow-hidden rounded-3xl shadow-xl">
                    <div class="absolute inset-0 <?php echo strip_tags($gi['item_bg_class']); ?> opacity-90"></div>
                    <div class="relative z-10 p-12 md:p-16 text-white">
                        <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center shadow-lg mb-8">
                            <span class="material-symbols-outlined text-4xl text-white"><?php echo strip_tags($gi['item_icon']); ?></span>
                        </div>
                        <h3 class="text-4xl font-black mb-4"><?php echo strip_tags($gi['item_title']); ?></h3>
                        <p class="text-xl md:text-2xl text-white/90 mb-8"><?php echo strip_tags($gi['item_description']); ?></p>
                        <?php if (!empty($gi['item_link'])): ?>
                        <a href="<?php echo strip_tags($gi['item_link']); ?>" class="inline-flex items-center gap-3 px-8 py-4 bg-white <?php echo strip_tags($gi['item_link_color']); ?> font-bold rounded-xl hover:bg-opacity-90 transition-all">
                            <span><?php echo strip_tags($gi['item_link_text']); ?></span>
                            <span class="material-symbols-outlined">arrow_forward</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Alumni Coordinator Section -->
    <section class="py-28 bg-gray-50 dark:bg-gray-800/50">
        <div class="container mx-auto px-4">
            <div class="max-w-[90rem] mx-auto">
                <div class="glass rounded-[3rem] p-16 md:p-28 shadow-2xl overflow-hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-20 items-center">
                        <div class="lg:col-span-6">
                            <div class="relative group">
                                <div class="absolute -top-10 -left-10 w-64 h-64 bg-blue-600/20 rounded-full blur-3xl group-hover:bg-blue-600/30 transition-all duration-700"></div>
                                <div class="absolute -bottom-10 -right-10 w-56 h-56 bg-yellow-400/20 rounded-full blur-3xl group-hover:bg-yellow-400/30 transition-all duration-700"></div>
                                <div class="relative z-10 rounded-[2.5rem] overflow-hidden shadow-2xl transform group-hover:scale-[1.02] transition-all duration-700">
                                    <img src="<?php echo strip_tags($content['coordinator_image']??''); ?>" alt="<?php echo strip_tags($content['coordinator_name']??''); ?>" class="w-full h-full object-cover aspect-[3/4]">
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-6">
                            <div class="inline-flex items-center gap-3 px-8 py-4 mb-8 rounded-full bg-blue-100 dark:bg-blue-900/30">
                                <span class="material-symbols-outlined text-3xl text-blue-600">badge</span>
                                <span class="text-xl font-bold text-blue-700 dark:text-blue-300 uppercase tracking-wider"><?php echo strip_tags($content['coordinator_title']??''); ?></span>
                            </div>
                            <h3 class="text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($content['coordinator_name']??''); ?></h3>
                            <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 leading-relaxed mb-10"><?php echo strip_tags($content['coordinator_description']??''); ?></p>
                            <div class="flex flex-wrap gap-6">
                                <?php if (!empty($content['coordinator_email'])): ?>
                                <a href="mailto:<?php echo strip_tags($content['coordinator_email']); ?>" class="inline-flex items-center gap-4 px-10 py-5 alumni-gradient text-white text-xl font-bold rounded-2xl hover:opacity-90 transition-all shadow-lg">
                                    <span class="material-symbols-outlined text-2xl text-white">mail</span>
                                    <span>Contact Me</span>
                                </a>
                                <?php endif; ?>
                                <?php if (!empty($content['coordinator_phone'])): ?>
                                <a href="tel:<?php echo str_replace(' ', '', $content['coordinator_phone']); ?>" class="inline-flex items-center gap-4 px-10 py-5 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white text-xl font-bold rounded-2xl hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                                    <span class="material-symbols-outlined text-2xl">call</span>
                                    <span><?php echo strip_tags($content['coordinator_phone']); ?></span>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Legacy Fund CTA -->
    <section id="legacy-fund" class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 alumni-gradient"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-white/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white mb-8 leading-tight tracking-tight">
                    <?php echo strip_tags($content['cta_heading']??''); ?> <br><span class="text-yellow-400 text-5xl sm:text-6xl md:text-7xl lg:text-6xl block mt-2"><?php echo strip_tags($content['cta_subtitle']??''); ?></span>
                </h2>
                <p class="text-2xl sm:text-3xl md:text-4xl text-white/90 mb-12 max-w-4xl mx-auto leading-relaxed font-medium"><?php echo strip_tags($content['cta_description']??''); ?></p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <?php if (!empty($content['cta_button_link'])): ?>
                    <a href="<?php echo strip_tags($content['cta_button_link']); ?>" target="_blank" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">volunteer_activism</span>
                        <?php echo strip_tags($content['cta_button_text']??'Donate Now'); ?>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($content['cta_button2_link'])): ?>
                    <a href="<?php echo strip_tags($content['cta_button2_link']); ?>" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">info</span>
                        <?php echo strip_tags($content['cta_button2_text']??'Learn More'); ?>
                    </a>
                    <?php endif; ?>
                </div>
                <!-- Social Links -->
                <div class="mt-16 flex justify-center gap-6">
                    <?php if (!empty($content['social_facebook'])): ?>
                    <a href="<?php echo strip_tags($content['social_facebook']); ?>" target="_blank" class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition-all">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($content['social_twitter'])): ?>
                    <a href="<?php echo strip_tags($content['social_twitter']); ?>" target="_blank" class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition-all">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($content['social_linkedin'])): ?>
                    <a href="<?php echo strip_tags($content['social_linkedin']); ?>" target="_blank" class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition-all">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($content['social_threads'])): ?>
                    <a href="<?php echo strip_tags($content['social_threads']); ?>" target="_blank" class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition-all">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12.186 24h-.007c-3.581-.024-6.334-1.205-8.184-3.509C2.35 18.44 1.5 15.586 1.472 12.01v-.017c.03-3.579.879-6.43 2.525-8.482C5.845 1.205 8.6.024 12.18 0h.014c2.746.02 5.043.725 6.826 2.098 1.677 1.29 2.858 3.13 3.509 5.467l-2.04.569c-1.104-3.96-3.898-5.984-8.304-6.015-2.91.022-5.11.936-6.54 2.717C4.307 6.504 3.616 8.914 3.589 12c.027 3.086.718 5.496 2.057 7.164 1.43 1.783 3.631 2.698 6.54 2.717 2.623-.02 4.358-.631 5.8-2.045 1.647-1.613 1.618-3.593 1.09-4.798-.31-.71-.873-1.3-1.634-1.75-.192 1.352-.622 2.446-1.284 3.272-.886 1.102-2.14 1.704-3.73 1.79-1.202.065-2.361-.218-3.259-.801-1.063-.689-1.685-1.74-1.752-2.96-.065-1.17.408-2.243 1.33-3.023.88-.744 2.121-1.158 3.476-1.155.88 0 1.686.122 2.397.37l.087-.86C13.69 8.634 12.96 8.5 12.12 8.5c-1.776-.008-3.36.5-4.472 1.439-1.157.976-1.774 2.335-1.738 3.828.036 1.56.771 2.9 2.07 3.769 1.135.759 2.584 1.114 4.1 1.014 1.235-.078 2.17-.513 2.86-1.333.548-.653.93-1.506 1.098-2.496-.15-.07-.3-.142-.45-.217-1.197-.598-2.476-.858-3.496-.858-.876 0-1.628.17-2.173.496-.592.352-.912.838-.901 1.369.011.5.321.934.875 1.222.579.302 1.335.444 2.132.401.876-.048 1.56-.322 2.032-.816.363-.38.61-.887.743-1.517l.018-.09 2.05.406c-.203 1.052-.622 1.95-1.245 2.673-.95 1.102-2.318 1.768-4.07 1.979-.17.02-.34.03-.51.03z"/></svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-800/50">
        <div class="container mx-auto px-4">
            <div class="max-w-7xl mx-auto text-center">
                <h2 class="text-6xl font-black text-gray-900 dark:text-white mb-16"><?php echo strip_tags($content['contact_heading']??'Contact Alumni Relations'); ?></h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div class="p-12 glass rounded-[3rem] shadow-xl flex flex-col items-center">
                        <div class="w-24 h-24 alumni-gradient rounded-3xl flex items-center justify-center text-white mb-8 shadow-lg">
                            <span class="material-symbols-outlined text-5xl text-white">location_on</span>
                        </div>
                        <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Location</h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400 leading-relaxed"><?php echo nl2br(strip_tags($content['contact_location']??'')); ?></p>
                    </div>
                    <div class="p-12 glass rounded-[3rem] shadow-xl flex flex-col items-center">
                        <div class="w-24 h-24 alumni-gradient rounded-3xl flex items-center justify-center text-white mb-8 shadow-lg">
                            <span class="material-symbols-outlined text-5xl text-white">call</span>
                        </div>
                        <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Phone</h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($content['contact_phone']??''); ?></p>
                        <?php if (!empty($content['contact_phone_note'])): ?>
                        <p class="text-xl text-blue-600 font-bold mt-4"><?php echo strip_tags($content['contact_phone_note']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="p-12 glass rounded-[3rem] shadow-xl flex flex-col items-center">
                        <div class="w-24 h-24 alumni-gradient rounded-3xl flex items-center justify-center text-white mb-8 shadow-lg">
                            <span class="material-symbols-outlined text-5xl text-white">mail</span>
                        </div>
                        <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Address</h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400"><?php echo nl2br(strip_tags($content['contact_address']??'')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.slider-dot');
    if (slides.length === 0) return;
    let currentSlide = 0;
    const totalSlides = slides.length;
    function showSlide(index) {
        slides.forEach((slide, i) => { slide.classList.remove('active'); if(dots[i]) dots[i].classList.remove('active'); });
        slides[index].classList.add('active');
        if(dots[index]) dots[index].classList.add('active');
    }
    function nextSlide() { currentSlide = (currentSlide + 1) % totalSlides; showSlide(currentSlide); }
    setInterval(nextSlide, 5000);
    dots.forEach((dot, index) => { dot.addEventListener('click', () => { currentSlide = index; showSlide(currentSlide); }); });
});
</script>

<?php include 'includes/footer.php'; ?>
