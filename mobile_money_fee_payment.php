<?php
$page_title = "Mobile Money Fee Payment - Valley View University";
$active_page = "students";
require_once 'includes/db_connect.php';

// Fetch data from database
$page_key = 'mobile_money_payment';
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
    $grouped_items[$item['section_key']][] = $item;
}

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
    
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
    }
    .dark .glass-card {
        background: rgba(17, 24, 39, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .step-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
        border-radius: 1rem;
        font-weight: 800;
        font-size: 1.5rem;
        margin-right: 1.25rem;
        flex-shrink: 0;
    }
    
    .ussd-badge {
        font-family: 'JetBrains Mono', monospace;
        letter-spacing: -0.05em;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900 px-4 md:px-8 lg:px-16">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image'] ?? 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&q=80&w=1600'); ?>" 
                 alt="Financial Services" class="w-full h-full object-cover animate-slow-zoom opacity-50">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="w-full relative z-10 py-24 text-center">
            <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['hero_badge'] ?? 'Student Resources'); ?></span>
            </div>
            
            <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                <?php echo strip_tags($hero['hero_title'] ?? 'Mobile Money'); ?> <br>
                <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($hero['hero_subtitle'] ?? 'Fee Payment'); ?></span>
            </h1>
            
            <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-6xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                <?php echo strip_tags($hero['hero_description'] ?? '"Convenient, secure, and instant. Pay your university fees from the comfort of your home using our approved USSD short codes."'); ?>
            </p>
        </div>
    </section>

    <!-- Main Content Area -->
    <section class="py-24 bg-white dark:bg-gray-800 relative z-20 -mt-20 mx-4 md:mx-8 lg:mx-16 rounded-[3rem] shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700 px-4 md:px-8 lg:px-16">
        
        <!-- Header Section -->
        <div class="max-w-5xl mb-20">
            <h2 class="text-7xl font-black text-gray-900 dark:text-white mb-8 tracking-tight">How to Pay School Fees</h2>
            <div class="h-3 w-48 bg-blue-600 rounded-full mb-10"></div>
            <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                Students can pay their school fees conveniently using any of the approved USSD short codes. Follow the steps carefully to complete your payment successfully.
            </p>
        </div>

        <!-- Supported Networks -->
        <div class="mb-20">
            <div class="flex flex-wrap items-center justify-center gap-12 p-12 bg-gray-50 dark:bg-gray-900/50 rounded-[3rem] border border-gray-100 dark:border-gray-800 shadow-inner">
                <?php foreach ($grouped_items['networks'] ?? [] as $network): ?>
                <div class="flex flex-col items-center gap-4 group">
                    <div class="w-32 h-32 flex items-center justify-center bg-white dark:bg-gray-800 rounded-3xl shadow-lg group-hover:scale-110 transition-transform duration-300 p-4">
                        <img src="<?php echo strip_tags($network['item_image']); ?>" alt="<?php echo strip_tags($network['item_title']); ?>" class="max-w-full max-h-full object-contain">
                    </div>
                    <span class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-widest"><?php echo strip_tags($network['item_title']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>


        <!-- USSD Options Grid -->
        <?php 
        $ussd_section = array_values(array_filter($sections, fn($s) => $s['section_key'] === 'ussd_options'))[0] ?? null;
        if ($ussd_section): 
        ?>
        <div class="mb-24">
            <div class="flex items-center justify-between mb-16 px-4">
                <h3 class="text-6xl font-black text-gray-900 dark:text-white tracking-tight"><?php echo strip_tags($ussd_section['section_title']); ?></h3>
                <span class="px-8 py-3 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-full text-xl font-black uppercase tracking-widest"><?php echo strip_tags($ussd_section['section_subtitle']); ?></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-10">
                <?php foreach ($grouped_items['ussd_options'] ?? [] as $item): ?>
                <div class="glass-card p-12 rounded-[3.5rem] border-t-8 border-<?php echo strip_tags($item['item_color']); ?> group hover:shadow-2xl transition-all duration-500 flex flex-col">
                    <div class="flex items-center justify-between mb-12">
                        <div class="w-24 h-24 rounded-[2rem] bg-<?php echo strip_tags($item['item_color']); ?> flex items-center justify-center text-white shadow-xl group-hover:rotate-12 transition-transform">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($item['item_icon']); ?></span>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest text-base mb-2"><?php echo strip_tags($item['item_subtitle']); ?></p>
                            <span class="text-5xl font-black text-<?php echo str_replace('blue-600', 'blue-600', str_replace('yellow-400', 'yellow-600', $item['item_color'])); ?> ussd-badge"><?php echo strip_tags($item['item_stat_value']); ?></span>
                        </div>
                    </div>
                    
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-10"><?php echo strip_tags($item['item_title']); ?></h4>
                    
                    <div class="space-y-8 flex-grow">
                        <?php 
                        $steps = explode("\n", $item['item_description']);
                        foreach($steps as $index => $step): 
                            if(trim($step) === '') continue;
                            preg_match('/^\d+\.\s*(.*)/', $step, $matches);
                            $step_content = $matches[1] ?? $step;
                            // Highlight USSD if present
                            $step_content = preg_replace('/(\*[\d\*#]+#)/', '<span class="bg-gray-100 dark:bg-gray-800 px-4 py-2 rounded-xl font-mono text-blue-600">$1</span>', $step_content);
                        ?>
                        <div class="flex items-start">
                            <span class="step-number bg-<?php echo str_replace('600', '100', str_replace('400', '100', $item['item_color'])); ?> text-<?php echo str_replace('600', '700', str_replace('400', '700', $item['item_color'])); ?> dark:bg-<?php echo str_replace('600', '900/40', str_replace('400', '900/40', $item['item_color'])); ?> dark:text-<?php echo str_replace('600', '400', str_replace('400', '400', $item['item_color'])); ?>"><?php echo ($index + 1); ?></span>
                            <p class="text-2xl text-gray-700 dark:text-gray-300 font-bold"><?php echo $step_content; ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Important Notes Section -->
        <?php 
        $notes_section = array_values(array_filter($sections, fn($s) => $s['section_key'] === 'important_notes'))[0] ?? null;
        if ($notes_section): 
        ?>
        <div class="p-16 bg-blue-50/50 dark:bg-blue-900/10 rounded-[4rem] border border-blue-100 dark:border-blue-800/30">
            <div class="flex items-center gap-8 mb-16">
                <div class="w-24 h-24 rounded-[2rem] bg-blue-600 flex items-center justify-center text-white shadow-xl animate-float">
                    <span class="material-symbols-outlined text-5xl text-white">info</span>
                </div>
                <div>
                    <h3 class="text-5xl font-black text-gray-900 dark:text-white"><?php echo strip_tags($notes_section['section_title']); ?></h3>
                    <p class="text-2xl text-blue-600 dark:text-blue-400 font-bold"><?php echo strip_tags($notes_section['section_subtitle']); ?></p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-12">
                <?php foreach ($grouped_items['important_notes'] ?? [] as $note): ?>
                <div class="flex items-start gap-6 p-10 bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-800">
                    <span class="material-symbols-outlined text-blue-600 text-5xl mt-1"><?php echo strip_tags($note['item_icon'] ?? 'check_circle'); ?></span>
                    <p class="text-2xl text-gray-700 dark:text-gray-300 font-medium leading-relaxed"><?php echo strip_tags($note['item_description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="flex items-start gap-8 p-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-[3rem] border-2 border-yellow-200 dark:border-yellow-800/50">
                <span class="material-symbols-outlined text-yellow-600 text-6xl mt-1">warning</span>
                <div>
                    <p class="text-4xl text-gray-900 dark:text-white font-black mb-4">CRITICAL REMINDER</p>
                    <p class="text-3xl text-gray-700 dark:text-gray-300 font-bold leading-relaxed">Always remember to use your student account code (e.g. S206ES11001212).</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </section>

    <!-- Why Mobile Money Section -->
    <?php 
    $why_section = array_values(array_filter($sections, fn($s) => $s['section_key'] === 'why_used'))[0] ?? null;
    if ($why_section): 
    ?>
    <section class="py-24 bg-gray-50 dark:bg-gray-950 px-4 md:px-8 lg:px-16">
        <div class="text-center mb-20">
            <h2 class="text-6xl font-black text-gray-900 dark:text-white mb-6 tracking-tight"><?php echo strip_tags($why_section['section_title']); ?></h2>
            <div class="h-2 w-40 bg-blue-600 rounded-full mb-8"></div>
            <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($why_section['section_subtitle']); ?></p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <?php foreach ($grouped_items['why_used'] ?? [] as $reason): ?>
            <div class="p-12 bg-white dark:bg-gray-900 rounded-[3rem] shadow-xl border border-gray-100 dark:border-gray-800 text-center group hover:-translate-y-4 transition-all duration-500">
                <div class="w-24 h-24 rounded-[2rem] bg-<?php echo strip_tags($reason['item_color'] ?? 'blue-600'); ?> flex items-center justify-center text-white mx-auto mb-10 shadow-2xl group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($reason['item_icon'] ?? 'bolt'); ?></span>
                </div>
                <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($reason['item_title']); ?></h4>
                <p class="text-2xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium"><?php echo strip_tags($reason['item_description']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Support Section -->
    <section class="py-24 bg-blue-900 relative overflow-hidden px-4 md:px-8 lg:px-16">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="relative z-10 text-center">
            <h2 class="text-6xl font-black text-white mb-8 tracking-tight"><?php echo strip_tags($hero['cta_title'] ?? 'Need Assistance?'); ?></h2>
            <p class="text-3xl text-blue-100 mb-16 max-w-5xl mx-auto leading-relaxed font-medium">
                <?php echo strip_tags($hero['cta_subtitle'] ?? ''); ?>
            </p>
            <div class="flex flex-col lg:flex-row gap-10 justify-center items-stretch">
                <div class="flex items-center gap-8 bg-white/10 backdrop-blur-xl p-10 rounded-[2.5rem] border border-white/20 hover:bg-white/20 transition-all flex-1 max-w-2xl">
                    <div class="w-20 h-20 rounded-3xl bg-yellow-400 flex items-center justify-center text-blue-900 shadow-2xl">
                        <span class="material-symbols-outlined text-4xl">call</span>
                    </div>
                    <div class="text-left">
                        <p class="text-blue-200 text-xl font-bold uppercase tracking-widest mb-2">Call Support</p>
                        <p class="text-white text-4xl font-black tracking-tight">+233 30 701 1832</p>
                    </div>
                </div>
                <div class="flex items-center gap-8 bg-white/10 backdrop-blur-xl p-10 rounded-[2.5rem] border border-white/20 hover:bg-white/20 transition-all flex-1 max-w-2xl">
                    <div class="w-20 h-20 rounded-3xl bg-blue-500 flex items-center justify-center text-white shadow-xl">
                        <span class="material-symbols-outlined text-4xl text-white">mail</span>
                    </div>
                    <div class="text-left">
                        <p class="text-blue-200 text-xl font-bold uppercase tracking-widest mb-2">Email Us</p>
                        <p class="text-white text-4xl font-black tracking-tight"><?php echo strip_tags($hero['cta_button_text'] ?? 'finance@vvu.edu.gh'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>