<?php
$page_title = "Our History - Valley View University";
$active_page = "about";
require_once 'includes/db_connect.php';

// Fetch data from database
$hero = $pdo->query("SELECT * FROM history_hero WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$overview = $pdo->query("SELECT * FROM history_overview WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$milestones = $pdo->query("SELECT * FROM history_milestones WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$community = $pdo->query("SELECT * FROM history_community WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$cta = $pdo->query("SELECT * FROM history_cta WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();

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
    .history-card {
        transition: all 0.3s ease;
    }
    .history-card:hover {
        transform: translateY(-10px);
    }
    .timeline-line {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        width: 4px;
        height: 100%;
        background: linear-gradient(to bottom, #2563eb, #fbbf24);
        border-radius: 2px;
    }
    @media (max-width: 768px) {
        .timeline-line {
            left: 20px;
        }
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image_url'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuDlpqAxUpsNTDcRAQIlxSNJQ8SojHcCq-EJUtGi1fL4Ks81Fov4uUGjJrsaziEer_Gb2EzOGjNFYzIvSXn8BgUcJTOJ60Ln7ogU_UGxoqMGsnyt1wEkW1636dKPzO17EdOyoT7GZLZ7-VADxDD39JsJ31e3yOzPXyo_69Va5FW22seP0WfrtmjXil3J2I1YDq8D9rg2aEcx572kdiJMjcAlfXPO3bQ46H2PtAA2WpbTZN8cvvoWSPdLKzgJaKL0f6lY99R4t-07NQsh'); ?>" 
                 alt="VVU History" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo $hero ? strip_tags($hero['page_subtitle']) : 'Our Legacy'; ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo $hero ? strip_tags($hero['hero_title']) : 'The Journey'; ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo $hero ? strip_tags($hero['hero_subtitle']) : 'Of Excellence'; ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo $hero ? strip_tags($hero['hero_description']) : '"From our humble beginnings in 1979 to becoming Ghana\'s first chartered private university, our history is a testament to faith, vision, and academic brilliance."'; ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Historical Overview -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="animate-fadeInUp">
                    <h2 class="text-5xl sm:text-6xl font-black text-gray-900 dark:text-white mb-8"><?php echo $overview ? strip_tags($overview['section_title']) : 'A Visionary <span class="text-blue-600">Beginning</span>'; ?></h2>
                    <p class="text-3xl text-gray-700 dark:text-gray-300 leading-relaxed mb-6 font-medium">
                        <?php echo $overview ? nl2br(strip_tags($overview['paragraph_1'])) : 'Valley View University was established in 1979 by the West African Union Mission of Seventh-day Adventists. What started as a focused mission to provide quality Christian education has grown into a beacon of higher learning in West Africa.'; ?>
                    </p>
                    <p class="text-3xl text-gray-700 dark:text-gray-300 leading-relaxed mb-8 font-medium">
                        <?php echo $overview ? nl2br(strip_tags($overview['paragraph_2'])) : 'In 1997, the institution was absorbed into the Adventist University system operated by the West Central African Division of Seventh-day Adventists, headquartered in Abidjan, Cote d\'Ivoire, further strengthening its global academic ties.'; ?>
                    </p>
                    <div class="flex gap-6">
                        <div class="flex flex-col">
                            <span class="text-6xl font-black text-blue-600"><?php echo $overview ? strip_tags($overview['founded_year']) : '1979'; ?></span>
                            <span class="text-2xl font-bold text-gray-500 uppercase tracking-widest">Founded</span>
                        </div>
                        <div class="w-px h-16 bg-gray-200 dark:bg-gray-700"></div>
                        <div class="flex flex-col">
                            <span class="text-6xl font-black text-yellow-500"><?php echo $overview ? strip_tags($overview['chartered_year']) : '2006'; ?></span>
                            <span class="text-2xl font-bold text-gray-500 uppercase tracking-widest">Chartered</span>
                        </div>
                    </div>
                </div>
                <div class="relative animate-fadeInUp" style="animation-delay: 0.2s;">
                    <div class="absolute -inset-4 bg-gradient-to-r from-blue-600 to-yellow-500 rounded-3xl blur-2xl opacity-20"></div>
                    <img src="<?php echo strip_tags($overview['overview_image_url'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuDlpqAxUpsNTDcRAQIlxSNJQ8SojHcCq-EJUtGi1fL4Ks81Fov4uUGjJrsaziEer_Gb2EzOGjNFYzIvSXn8BgUcJTOJ60Ln7ogU_UGxoqMGsnyt1wEkW1636dKPzO17EdOyoT7GZLZ7-VADxDD39JsJ31e3yOzPXyo_69Va5FW22seP0WfrtmjXil3J2I1YDq8D9rg2aEcx572kdiJMjcAlfXPO3bQ46H2PtAA2WpbTZN8cvvoWSPdLKzgJaKL0f6lY99R4t-07NQsh'); ?>" 
                         alt="Founding Era" class="relative rounded-3xl shadow-2xl object-cover w-full h-[500px]">
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950 relative overflow-hidden">
        <div class="container relative z-10">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6">Historical <span class="text-blue-600 text-6xl font-black">Milestones</span></h2>
                <div class="h-2 w-40 bg-yellow-500 mx-auto rounded-full mb-8"></div>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">Tracing our path from a mission-driven college to a premier chartered university.</p>
            </div>

            <div class="relative">
                <!-- Timeline Line -->
                <div class="timeline-line"></div>

                <div class="space-y-24">
                    <?php $index = 0; foreach ($milestones as $milestone): $index++; ?>
                    <div class="relative flex flex-col md:flex-<?php echo $index % 2 == 1 ? 'row' : 'row-reverse'; ?> items-center justify-between">
                        <div class="md:w-5/12 mb-8 md:mb-0">
                            <div class="glass p-8 rounded-3xl shadow-xl history-card border-<?php echo $index % 2 == 1 ? 'l' : 'r'; ?>-8 border-<?php echo strip_tags($milestone['border_color']); ?>">
                                <span class="text-5xl font-black text-<?php echo strip_tags($milestone['dot_color']); ?> mb-4 block"><?php echo strip_tags($milestone['year']); ?></span>
                                <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($milestone['milestone_title']); ?></h3>
                                <p class="text-2xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium">
                                    <?php echo strip_tags($milestone['milestone_description']); ?>
                                </p>
                            </div>
                        </div>
                        <div class="absolute left-1/2 transform -translate-x-1/2 w-8 h-8 bg-<?php echo strip_tags($milestone['dot_color']); ?> rounded-full border-4 border-white dark:border-gray-900 z-20 hidden md:block"></div>
                        <div class="md:w-5/12"></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Values & Inclusivity -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="glass p-12 md:p-20 rounded-[3rem] shadow-2xl border border-gray-100 dark:border-gray-800 text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/5 rounded-full -mr-32 -mt-32"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-yellow-500/5 rounded-full -ml-32 -mb-32"></div>
                
                <span class="material-symbols-outlined text-7xl text-blue-600 mb-8 animate-float">public</span>
                <h2 class="text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-8"><?php echo $community ? $community['section_title'] : 'A Global <span class="text-blue-600">Community</span>'; ?></h2>
                <p class="text-3xl md:text-4xl text-gray-700 dark:text-gray-300 leading-relaxed font-medium mb-12">
                    <?php echo $community ? strip_tags($community['section_description']) : 'Today, Valley View University serves undergraduate and graduate students from all over the world. We admit qualified students regardless of their religious background, provided they accept the Christian principles and lifestyle that form the basis of our operations.'; ?>
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="p-6 bg-blue-50 dark:bg-blue-900/20 rounded-2xl">
                        <h4 class="text-5xl font-black text-blue-600 mb-2"><?php echo $community ? strip_tags($community['feature_1_title']) : 'Global'; ?></h4>
                        <p class="text-2xl font-bold text-gray-500 uppercase"><?php echo $community ? strip_tags($community['feature_1_label']) : 'Reach'; ?></p>
                    </div>
                    <div class="p-6 bg-yellow-50 dark:bg-yellow-900/20 rounded-2xl">
                        <h4 class="text-5xl font-black text-yellow-500 mb-2"><?php echo $community ? strip_tags($community['feature_2_title']) : 'Inclusive'; ?></h4>
                        <p class="text-2xl font-bold text-gray-500 uppercase"><?php echo $community ? strip_tags($community['feature_2_label']) : 'Community'; ?></p>
                    </div>
                    <div class="p-6 bg-blue-50 dark:bg-blue-900/20 rounded-2xl">
                        <h4 class="text-5xl font-black text-blue-600 mb-2"><?php echo $community ? strip_tags($community['feature_3_title']) : 'Chartered'; ?></h4>
                        <p class="text-2xl font-bold text-gray-500 uppercase"><?php echo $community ? strip_tags($community['feature_3_label']) : 'Excellence'; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white mb-8 leading-tight tracking-tight">
                    <?php echo $cta ? strip_tags($cta['cta_title_1']) : 'Be Part of Our'; ?> <br><span class="text-yellow-400 text-6xl sm:text-7xl md:text-8xl lg:text-6xl block mt-2"><?php echo $cta ? strip_tags($cta['cta_title_2']) : 'Future History'; ?></span>
                </h2>
                <p class="text-2xl sm:text-3xl md:text-4xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo $cta ? strip_tags($cta['cta_description']) : 'Join a legacy of excellence and innovation. Your journey at Valley View University starts here.'; ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo $cta ? strip_tags($cta['button_1_url']) : 'admissions.php'; ?>" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">school</span>
                        <?php echo $cta ? strip_tags($cta['button_1_text']) : 'Apply Now'; ?>
                    </a>
                    <a href="<?php echo $cta ? strip_tags($cta['button_2_url']) : 'contact_us.php'; ?>" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">mail</span>
                        <?php echo $cta ? strip_tags($cta['button_2_text']) : 'Contact Us'; ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
