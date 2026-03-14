<?php
$page_title = "Work Study Program - Valley View University";
$active_page = "student-life";
include 'includes/header.php';
require_once 'includes/campus_life_content_helper.php';

// Fetch content from database
$content = getWorkStudyContent($pdo);

// Use default values if no content found
if (!$content) {
    $content = [
        'hero_title' => 'Work Study Program',
        'hero_subtitle' => 'Learn, Work, and Grow - Experience holistic development through meaningful campus employment opportunities',
        'hero_image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=1920',
        'overview_heading' => 'Campus Employment Philosophy',
        'overview_text' => 'In keeping with the Seventh-day Adventist philosophy of education, Valley View University provides varied opportunities for students to work in campus-related industries.',
        'overview_image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=1200',
        'minimum_hours' => 12,
        'spouse_policy_text' => 'If a student\'s spouse is employed in the Work Study Programme, the spouse\'s employment may be terminated once the student has graduated.',
        'application_process' => 'Visit the Student Employment Office to apply.',
        'cta_heading' => 'Ready to Work & Learn?',
        'cta_text' => 'Join our work study program and experience the value of combining academic excellence with practical work experience.'
    ];
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
    .work-card {
        transition: all 0.3s ease;
    }
    .work-card:hover {
        transform: translateY(-10px);
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($content['hero_image']); ?>" 
                 alt="VVU Campus Work Study" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400">Campus Employment</span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($content['hero_title']); ?>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($content['hero_subtitle']); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Program Overview Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-20">
                    <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6">Campus Employment Philosophy</h2>
                    <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-stretch">
                    <div class="relative flex">
                        <div class="absolute -top-6 -left-6 w-72 h-72 bg-blue-200 dark:bg-blue-900/30 rounded-full blur-3xl opacity-40"></div>
                        <div class="relative rounded-3xl overflow-hidden shadow-2xl flex-1">
                            <img src="<?php echo strip_tags($content['overview_image']); ?>" 
                                 alt="Students Working" class="w-full h-full object-cover">
                        </div>
                    </div>

                    <div class="space-y-8 flex flex-col justify-center">
                        <div class="prose prose-lg dark:prose-invert max-w-none">
                            <p class="text-3xl text-gray-700 dark:text-gray-300 leading-relaxed font-medium mb-8">
                                In keeping with the <strong class="text-blue-600 dark:text-blue-400">Seventh-day Adventist philosophy of education</strong>, which emphasizes the development of the <em>physical nature of humanity</em>, Valley View University provides varied opportunities for students to work in campus-related industries.
                            </p>
                            
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 p-10 rounded-3xl border-l-8 border-blue-600 shadow-lg">
                                <p class="text-3xl text-gray-800 dark:text-gray-200 font-bold leading-relaxed">
                                    Students are expected to work <span class="text-blue-600 dark:text-blue-400 text-4xl">at least 12 hours per week</span>, encouraging them to appreciate the value and dignity of labour while becoming self-reliant in financial matters.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Benefits Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6">Program Benefits</h2>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">Experience comprehensive personal and professional development through our work study program.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <?php 
                $benefits = getWorkStudyBenefits($pdo);
                foreach ($benefits as $benefit): 
                ?>
                <!-- Benefit -->
                <div class="work-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-xl border-t-8 border-<?php echo $benefit['color'] ?? 'blue'; ?>-600 hover:shadow-2xl">
                    <div class="w-24 h-24 rounded-3xl bg-<?php echo $benefit['color'] ?? 'blue'; ?>-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($benefit['icon']); ?></span>
                    </div>
                    <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($benefit['title']); ?></h3>
                    <p class="text-3xl text-gray-700 dark:text-gray-300 leading-relaxed font-medium">
                        <?php echo strip_tags($benefit['description']); ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Work Opportunities Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6">Work Opportunities</h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">Explore diverse employment opportunities across campus departments and industries.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 max-w-6xl mx-auto">
                <!-- Campus Industries -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-3xl p-12 shadow-xl">
                    <div class="flex items-center gap-6 mb-8">
                        <div class="w-20 h-20 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg">
                            <span class="material-symbols-outlined text-4xl text-white">factory</span>
                        </div>
                        <h3 class="text-5xl font-black text-gray-900 dark:text-white">Campus Industries</h3>
                    </div>
                    <ul class="space-y-6">
                        <?php 
                        $industries = getWorkStudyOpportunities($pdo, 'Campus Industries');
                        foreach ($industries as $opp): 
                        ?>
                        <li class="flex items-start gap-4">
                            <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-4xl mt-1">check_circle</span>
                            <span class="text-3xl text-gray-800 dark:text-gray-200 font-medium"><?php echo strip_tags($opp['opportunity_name']); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Academic & Administrative -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-3xl p-12 shadow-xl">
                    <div class="flex items-center gap-6 mb-8">
                        <div class="w-20 h-20 rounded-2xl bg-green-600 flex items-center justify-center text-white shadow-lg">
                            <span class="material-symbols-outlined text-4xl text-white">apartment</span>
                        </div>
                        <h3 class="text-5xl font-black text-gray-900 dark:text-white">Academic & Admin</h3>
                    </div>
                    <ul class="space-y-6">
                        <?php 
                        $academic = getWorkStudyOpportunities($pdo, 'Academic & Admin');
                        foreach ($academic as $opp): 
                        ?>
                        <li class="flex items-start gap-4">
                            <span class="material-symbols-outlined text-green-600 dark:text-green-400 text-4xl mt-1">check_circle</span>
                            <span class="text-3xl text-gray-800 dark:text-gray-200 font-medium"><?php echo strip_tags($opp['opportunity_name']); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Important Information Section -->
    <section class="py-24 bg-blue-900 overflow-hidden relative">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-5xl sm:text-6xl font-black text-white mb-6"><?php echo strip_tags($content['info_heading']); ?></h2>
                    <p class="text-2xl text-blue-100 font-medium leading-relaxed"><?php echo strip_tags($content['info_subtitle']); ?></p>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-12 border border-white/20">
                    <div class="flex items-start gap-8">
                        <div class="w-24 h-24 rounded-2xl bg-yellow-400 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-5xl text-blue-900">info</span>
                        </div>
                        <div>
                            <h3 class="text-4xl font-black text-white mb-6">Spouse Employment Policy</h3>
                            <p class="text-2xl text-blue-100 leading-relaxed font-medium mb-6">
                                <?php echo nl2br(strip_tags($content['spouse_policy_text'])); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How to Apply Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($content['steps_heading']); ?></h2>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($content['steps_subtitle']); ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 max-w-6xl mx-auto">
                <?php 
                $steps = getWorkStudySteps($pdo);
                foreach ($steps as $step): 
                ?>
                <div class="relative text-center group">
                    <div class="w-28 h-28 rounded-3xl bg-<?php echo $step['color'] ?? 'blue'; ?>-600 flex items-center justify-center text-white text-5xl font-black mx-auto mb-8 group-hover:scale-110 transition-transform shadow-xl">
                        <?php echo $step['step_number']; ?>
                    </div>
                    <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($step['title']); ?></h4>
                    <p class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium"><?php echo strip_tags($step['description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-24 overflow-hidden bg-white dark:bg-gray-900">
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl lg:text-6xl font-black text-gray-900 dark:text-white mb-8 leading-tight tracking-tight">
                    Ready to Work & Learn?
                </h2>
                <p class="text-2xl sm:text-3xl md:text-3xl text-gray-600 dark:text-gray-400 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    Join our work study program and experience the value of combining academic excellence with practical work experience.
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="contact_us.php" class="px-10 py-5 bg-blue-600 hover:bg-blue-700 text-white text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">contact_support</span>
                        Contact Student Affairs
                    </a>
                    <a href="student_life.php" class="px-10 py-5 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-white text-xl font-bold rounded-2xl transition-all border-2 border-gray-200 dark:border-gray-700 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">explore</span>
                        Explore Student Life
                    </a>
                </div>
                
                <div class="mt-20 grid grid-cols-1 sm:grid-cols-3 gap-12 border-t border-gray-200 dark:border-gray-800 pt-16">
                    <div>
                        <div class="text-6xl font-black text-blue-600 mb-2"><?php echo strip_tags($content['minimum_hours']); ?>hrs</div>
                        <div class="text-gray-600 dark:text-gray-400 uppercase tracking-widest text-2xl font-black">Minimum Per Week</div>
                    </div>
                    <div>
                        <div class="text-6xl font-black text-blue-600 mb-2"><?php echo strip_tags($content['stats_opportunities']); ?></div>
                        <div class="text-gray-600 dark:text-gray-400 uppercase tracking-widest text-2xl font-black">Job Opportunities</div>
                    </div>
                    <div>
                        <div class="text-6xl font-black text-blue-600 mb-2">∞</div>
                        <div class="text-gray-600 dark:text-gray-400 uppercase tracking-widest text-2xl font-black">Skills Developed</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
