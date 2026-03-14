<?php
$page_title = "School of Graduate Studies - Valley View University";
$active_page = "academics";
include 'includes/header.php';
require_once 'includes/db_connect.php';

$content = $pdo->query("SELECT * FROM graduate_page_content WHERE id=1")->fetch(PDO::FETCH_ASSOC) ?: [];
$sections = $pdo->query("SELECT * FROM graduate_page_sections WHERE is_active=1 ORDER BY display_order")->fetchAll(PDO::FETCH_ASSOC);
$sec_map = []; foreach ($sections as $s) $sec_map[$s['section_key']] = $s;
$all_items = $pdo->query("SELECT * FROM graduate_page_items WHERE is_active=1 ORDER BY section_key, display_order")->fetchAll(PDO::FETCH_ASSOC);
$items = []; foreach ($all_items as $i) $items[$i['section_key']][] = $i;
$stats = $pdo->query("SELECT * FROM graduate_page_stats ORDER BY display_order")->fetchAll(PDO::FETCH_ASSOC);
// Fetch graduate programs from academic_programs
$programs = $pdo->query("SELECT ap.* FROM academic_programs ap JOIN program_categories pc ON ap.category_id = pc.id WHERE pc.name = 'School of Graduate Studies' AND ap.is_active = 1 ORDER BY ap.title")->fetchAll(PDO::FETCH_ASSOC);
$colors = ['from-blue-600 to-blue-800','from-purple-600 to-purple-800','from-emerald-600 to-emerald-800','from-amber-600 to-amber-800','from-rose-600 to-rose-800','from-cyan-600 to-cyan-800','from-indigo-600 to-indigo-800','from-teal-600 to-teal-800','from-fuchsia-600 to-fuchsia-800','from-orange-600 to-orange-800','from-sky-600 to-sky-800','from-violet-600 to-violet-800'];
?>

<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slowZoom { 0% { transform: scale(1); } 100% { transform: scale(1.1); } }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .glass { background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); }
    .dark .glass { background: rgba(31,41,55,0.7); border: 1px solid rgba(255,255,255,0.1); }
    .grad-gradient { background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 50%, #3b82f6 100%); }
    .grad-gold { background: linear-gradient(135deg, #92400e 0%, #d97706 50%, #fbbf24 100%); }
    .program-card-hover { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    .program-card-hover:hover { transform: translateY(-12px); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
    .benefit-card { transition: all 0.3s ease; }
    .benefit-card:hover { transform: translateY(-8px); }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[75vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($content['hero_image']??'images/home-2.jpg'); ?>" alt="Graduate Studies" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-br from-gray-900/95 via-blue-900/80 to-gray-900/90"></div>
        </div>
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-10 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/3 right-20 w-48 h-48 bg-yellow-500/10 rounded-full blur-3xl"></div>
        </div>
        <div class="container relative z-10 py-24 mx-auto px-4">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-lg md:text-xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($content['hero_badge']??''); ?></span>
                </div>
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 drop-shadow-2xl"><?php echo strip_tags($content['hero_title']??''); ?></h1>
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto font-medium"><?php echo strip_tags($content['hero_subtitle']??''); ?></p>
                <div class="flex flex-col sm:flex-row gap-5 justify-center mt-12">
                    <a href="<?php echo strip_tags($content['cta_button_link']??''); ?>" target="_blank" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-2xl">edit_note</span>
                        <?php echo strip_tags($content['cta_button_text']??'Apply Now'); ?>
                    </a>
                    <a href="#programs" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-2xl">school</span>
                        View Programs
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Bar -->
    <?php if (!empty($stats)): ?>
    <section class="py-8 grad-gradient">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-6xl mx-auto">
                <?php foreach ($stats as $stat): ?>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-black text-white mb-2"><?php echo strip_tags($stat['stat_value']); ?></div>
                    <div class="text-blue-200 font-semibold text-lg"><?php echo strip_tags($stat['stat_label']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- About Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center max-w-[90rem] mx-auto">
                <div class="lg:col-span-5 relative">
                    <div class="absolute -top-10 -left-10 w-40 h-40 bg-blue-600/10 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-yellow-400/10 rounded-full blur-3xl"></div>
                    <div class="max-w-md mx-auto lg:max-w-full">
                        <img src="<?php echo strip_tags($content['about_image']??''); ?>" alt="Graduate Studies" class="relative z-10 rounded-[3rem] shadow-2xl w-full object-cover">
                    </div>
                </div>
                <div class="lg:col-span-7 flex flex-col gap-8">
                    <div>
                        <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-8 leading-tight"><?php echo strip_tags($content['about_heading']??''); ?></h2>
                        <div class="h-2 w-40 bg-blue-600 rounded-full mb-8"></div>
                        <p class="text-2xl md:text-[1.75rem] lg:text-3xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium mb-6"><?php echo strip_tags($content['about_text']??''); ?></p>
                        <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400 leading-relaxed"><?php echo strip_tags($content['about_text_2']??''); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Section -->
    <?php $sec = $sec_map['why_choose'] ?? null; if ($sec && !empty($items['why_choose'])): ?>
    <section class="py-24 bg-gray-50 dark:bg-gray-800/50">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($sec['section_title']); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-2xl md:text-[1.75rem] lg:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($sec['section_subtitle']??''); ?></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 max-w-[90rem] mx-auto">
                <?php foreach ($items['why_choose'] as $item): ?>
                <div class="benefit-card group p-10 bg-white dark:bg-gray-800 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700">
                    <div class="w-20 h-20 rounded-2xl bg-<?php echo strip_tags($item['item_color']); ?>-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-4xl text-white"><?php echo strip_tags($item['item_icon']); ?></span>
                    </div>
                    <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-4"><?php echo strip_tags($item['item_title']); ?></h4>
                    <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($item['item_description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Programs Section -->
    <section id="programs" class="py-24 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($content['programs_heading']??'Our Graduate Programs'); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-2xl md:text-[1.75rem] lg:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($content['programs_subtitle']??''); ?></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 max-w-[90rem] mx-auto">
                <?php foreach ($programs as $idx => $prog): $grad = $colors[$idx % count($colors)]; ?>
                <div class="program-card-hover rounded-3xl overflow-hidden shadow-lg border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 flex flex-col">
                    <div class="h-3 bg-gradient-to-r <?php echo $grad; ?>"></div>
                    <div class="p-8 flex-grow flex flex-col">
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r <?php echo $grad; ?> text-white text-sm font-bold mb-6 self-start">
                            <span class="material-symbols-outlined" style="font-size:1rem;">school</span>
                            <?php echo strip_tags($prog['level']??'Postgraduate'); ?>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-4 leading-tight"><?php echo strip_tags($prog['title']); ?></h3>
                        <p class="text-lg text-gray-600 dark:text-gray-400 mb-6 flex-grow line-clamp-3"><?php echo strip_tags($prog['description']??''); ?></p>
                        <div class="flex flex-wrap gap-3 mb-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-2 text-sm font-bold text-gray-500 dark:text-gray-400">
                                <span class="material-symbols-outlined text-lg text-blue-600">schedule</span>
                                <?php echo strip_tags($prog['duration']??''); ?>
                            </div>
                        </div>
                        <a href="course_details.php?id=<?php echo $prog['id']; ?>" class="w-full py-4 text-center font-bold text-lg rounded-2xl bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white hover:bg-gradient-to-r hover:<?php echo $grad; ?> hover:text-white transition-all flex items-center justify-center gap-2">
                            Explore Program <span class="material-symbols-outlined">arrow_forward</span>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Admission Requirements -->
    <?php $sec = $sec_map['admission'] ?? null; if ($sec && !empty($items['admission'])): ?>
    <section class="py-24 bg-gray-50 dark:bg-gray-800/50">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($sec['section_title']); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-2xl md:text-[1.75rem] lg:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($sec['section_subtitle']??''); ?></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 max-w-6xl mx-auto">
                <?php $admit_colors = ['blue','purple','emerald','amber']; foreach ($items['admission'] as $idx => $item): $c = $admit_colors[$idx % 4]; ?>
                <div class="group relative overflow-hidden rounded-3xl shadow-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-10 benefit-card">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-<?php echo $c; ?>-500/5 rounded-full -mr-10 -mt-10"></div>
                    <div class="w-20 h-20 rounded-2xl bg-<?php echo $c; ?>-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-4xl text-white"><?php echo strip_tags($item['item_icon']); ?></span>
                    </div>
                    <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-4"><?php echo strip_tags($item['item_title']); ?></h4>
                    <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400 leading-relaxed"><?php echo strip_tags($item['item_description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Research Section -->
    <?php $sec = $sec_map['research'] ?? null; if ($sec && !empty($items['research'])): ?>
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($sec['section_title']); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-2xl md:text-[1.75rem] lg:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($sec['section_subtitle']??''); ?></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 max-w-7xl mx-auto">
                <?php foreach ($items['research'] as $item): ?>
                <div class="benefit-card group p-10 bg-white dark:bg-gray-800 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700">
                    <div class="w-20 h-20 rounded-2xl bg-<?php echo strip_tags($item['item_color']); ?>-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-4xl text-white"><?php echo strip_tags($item['item_icon']); ?></span>
                    </div>
                    <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-4"><?php echo strip_tags($item['item_title']); ?></h4>
                    <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($item['item_description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Dean's Message -->
    <?php if (!empty($content['dean_message'])): ?>
    <section class="py-28 bg-gray-50 dark:bg-gray-800/50">
        <div class="container mx-auto px-4">
            <div class="max-w-[90rem] mx-auto">
                <div class="glass rounded-[3rem] p-16 md:p-28 shadow-2xl overflow-hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-20 items-center">
                        <?php if (!empty($content['dean_image'])): ?>
                        <div class="lg:col-span-5">
                            <div class="relative group">
                                <div class="absolute -top-10 -left-10 w-64 h-64 bg-blue-600/20 rounded-full blur-3xl"></div>
                                <div class="relative z-10 rounded-[2.5rem] overflow-hidden shadow-2xl">
                                    <img src="<?php echo strip_tags($content['dean_image']); ?>" alt="<?php echo strip_tags($content['dean_name']??''); ?>" class="w-full h-full object-cover aspect-[3/4]">
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-7">
                        <?php else: ?>
                        <div class="lg:col-span-12">
                        <?php endif; ?>
                            <div class="inline-flex items-center gap-3 px-8 py-4 mb-8 rounded-full bg-blue-100 dark:bg-blue-900/30">
                                <span class="material-symbols-outlined text-3xl text-blue-600">badge</span>
                                <span class="text-xl font-bold text-blue-700 dark:text-blue-300 uppercase tracking-wider"><?php echo strip_tags($content['dean_title']??''); ?></span>
                            </div>
                            <?php if (!empty($content['dean_name'])): ?>
                            <h3 class="text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($content['dean_name']); ?></h3>
                            <?php endif; ?>
                            <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 leading-relaxed mb-10"><?php echo strip_tags($content['dean_message']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 grad-gradient"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-white/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="container relative z-10 mx-auto px-4">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white mb-8 leading-tight tracking-tight"><?php echo strip_tags($content['cta_heading']??''); ?></h2>
                <p class="text-2xl sm:text-3xl text-white/90 mb-12 max-w-4xl mx-auto leading-relaxed font-medium"><?php echo strip_tags($content['cta_subtitle']??''); ?></p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags($content['cta_button_link']??''); ?>" target="_blank" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">volunteer_activism</span>
                        <?php echo strip_tags($content['cta_button_text']??'Apply Now'); ?>
                    </a>
                    <a href="<?php echo strip_tags($content['cta_button2_link']??''); ?>" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">mail</span>
                        <?php echo strip_tags($content['cta_button2_text']??'Contact Us'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-800/50">
        <div class="container mx-auto px-4">
            <div class="max-w-7xl mx-auto text-center">
                <h2 class="text-6xl font-black text-gray-900 dark:text-white mb-16"><?php echo strip_tags($content['contact_heading']??'Graduate School Office'); ?></h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                    <div class="p-10 glass rounded-[3rem] shadow-xl flex flex-col items-center">
                        <div class="w-20 h-20 grad-gradient rounded-3xl flex items-center justify-center text-white mb-6 shadow-lg">
                            <span class="material-symbols-outlined text-4xl text-white">location_on</span>
                        </div>
                        <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Location</h4>
                        <p class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed"><?php echo nl2br(strip_tags($content['contact_location']??'')); ?></p>
                    </div>
                    <div class="p-10 glass rounded-[3rem] shadow-xl flex flex-col items-center">
                        <div class="w-20 h-20 grad-gradient rounded-3xl flex items-center justify-center text-white mb-6 shadow-lg">
                            <span class="material-symbols-outlined text-4xl text-white">call</span>
                        </div>
                        <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Phone</h4>
                        <p class="text-xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($content['contact_phone']??''); ?></p>
                    </div>
                    <div class="p-10 glass rounded-[3rem] shadow-xl flex flex-col items-center">
                        <div class="w-20 h-20 grad-gradient rounded-3xl flex items-center justify-center text-white mb-6 shadow-lg">
                            <span class="material-symbols-outlined text-4xl text-white">mail</span>
                        </div>
                        <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Email</h4>
                        <p class="text-xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($content['contact_email']??''); ?></p>
                    </div>
                    <div class="p-10 glass rounded-[3rem] shadow-xl flex flex-col items-center">
                        <div class="w-20 h-20 grad-gradient rounded-3xl flex items-center justify-center text-white mb-6 shadow-lg">
                            <span class="material-symbols-outlined text-4xl text-white">schedule</span>
                        </div>
                        <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Office Hours</h4>
                        <p class="text-xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($content['contact_hours']??''); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
