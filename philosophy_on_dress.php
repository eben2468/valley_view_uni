<?php
$page_title = "Philosophy on Dress - Valley View University";
$active_page = "life_at_vvu";
include 'includes/header.php';
require_once 'includes/campus_life_content_helper.php';

// Fetch content from database
$content = getPhilosophyOnDressContent($pdo);

// Use default values if no content found
if (!$content) {
    $content = [
        'hero_title' => 'Philosophy On Dress',
        'hero_subtitle' => 'Modesty, Chastity, Simplicity, and Comeliness – The Biblical Standards We Embrace',
        'hero_image' => 'Education-Website-and-AdminPanel/images/pro-bg.jpg',
        'intro_heading' => 'Our Dress Philosophy',
        'intro_text' => 'Valley View University\'s philosophy of dress is firmly established on biblical ideals and professional standards expected of a Christian institution.',
        'intro_image' => 'uploads/Philosophy_on_dress.jpeg',
        'philosophy_statement' => 'Valley View University dress ideal seeks for appropriate covering of the body parts and avoids contemporary styles that are revealing or suggestive.',
        'encouraged_items' => "Neat, clean, and well-pressed clothing\nAppropriate covering of shoulders and knees\nProfessional attire for academic settings",
        'discouraged_items' => "Revealing or suggestive clothing\nExtremely tight or body-hugging outfits\nClothing with inappropriate graphics or text",
        'cta_heading' => 'Questions About Our Dress Code?',
        'cta_text' => 'Our Student Affairs Office is ready to help you understand our dress philosophy and guidelines.'
    ];
}

// Parse line-separated items
$encouraged = parseLineItems($content['encouraged_items'] ?? '');
$discouraged = parseLineItems($content['discouraged_items'] ?? '');
?>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slowZoom {
        0% { transform: scale(1); }
        100% { transform: scale(1.1); }
    }
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 30px rgba(37, 99, 235, 0.3); }
        50% { box-shadow: 0 0 60px rgba(37, 99, 235, 0.5); }
    }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.8s ease-out forwards; }
    .animate-float { animation: float 4s ease-in-out infinite; }
    .animate-pulse-glow { animation: pulse-glow 3s ease-in-out infinite; }
    
    .glass {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .dark .glass {
        background: rgba(31, 41, 55, 0.85);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .text-gradient {
        background: linear-gradient(to right, #2563eb, #fbbf24);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .principle-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .principle-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.2);
    }
    
    .image-frame {
        position: relative;
        border-radius: 2rem;
        overflow: hidden;
    }
    .image-frame::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 2rem;
        padding: 4px;
        background: linear-gradient(135deg, #2563eb, #fbbf24, #10b981);
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none;
    }

    .section-spacing {
        padding-top: 8rem;
        padding-bottom: 8rem;
    }

    /* Force white color for icons in gradient containers */
    [class*="bg-gradient"] .material-symbols-outlined,
    .bg-purple-600 .material-symbols-outlined,
    .bg-blue-600 .material-symbols-outlined,
    .bg-green-600 .material-symbols-outlined,
    .bg-green-500 .material-symbols-outlined,
    .bg-red-500 .material-symbols-outlined,
    .bg-yellow-400 .material-symbols-outlined {
        color: white !important;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($content['hero_image']); ?>" 
                 alt="VVU Philosophy on Dress" class="w-full h-full object-cover animate-slow-zoom opacity-50">
            <div class="absolute inset-0 bg-gradient-to-b from-purple-900/80 via-purple-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-4 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400">Life @ VVU</span>
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

    <!-- Introduction Section with Featured Image -->
    <section class="py-28 bg-white dark:bg-gray-800 relative z-20 -mt-20 mx-auto max-w-[1800px] rounded-[3rem] shadow-2xl overflow-hidden">
        <div class="container px-8 md:px-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <!-- Featured Image -->
                <div class="relative animate-fadeInUp">
                    <div class="absolute -inset-8 bg-gradient-to-r from-purple-600 to-blue-600 rounded-[3rem] blur-2xl opacity-20 animate-pulse-glow"></div>
                    <div class="image-frame relative">
                        <img src="<?php echo strip_tags($content['intro_image']); ?>" alt="VVU Students in Proper Dress" class="w-full h-[550px] rounded-[2rem] shadow-2xl object-cover">
                    </div>
                    <div class="absolute -bottom-12 -right-6 lg:-right-12 glass p-12 rounded-[2.5rem] shadow-2xl animate-float" style="min-width: 380px;">
                        <div class="flex items-center gap-8">
                            <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-purple-600 to-blue-600 flex items-center justify-center text-white shadow-lg">
                                <span class="material-symbols-outlined text-5xl">checkroom</span>
                            </div>
                            <div>
                                <p class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white">Value-Based</p>
                                <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-medium">Dress Standards</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Introduction Text -->
                <div class="animate-fadeInUp" style="animation-delay: 0.2s;">
                    <div class="inline-flex items-center gap-3 px-8 py-4 mb-10 rounded-full bg-purple-100 dark:bg-purple-900/30">
                        <span class="w-3 h-3 rounded-full bg-purple-600"></span>
                        <span class="text-xl md:text-2xl font-black text-purple-600 dark:text-purple-400 uppercase tracking-wider">Ghana's First Chartered Private University</span>
                    </div>
                    
                    <h2 class="text-5xl md:text-6xl lg:text-7xl font-black text-gray-900 dark:text-white mb-10 leading-tight">
                        <?php echo strip_tags($content['intro_heading']); ?>
                    </h2>
                    
                    <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed mb-10">
                        <?php echo nl2br(strip_tags($content['intro_text'])); ?>
                    </p>
                    
                    <?php if (!empty($content['philosophy_statement'])): ?>
                    <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed mb-12">
                        <?php echo nl2br(strip_tags($content['philosophy_statement'])); ?>
                    </p>
                    <?php endif; ?>

                    <div class="flex flex-wrap gap-6">
                        <a href="student_handbook.php" class="inline-flex items-center gap-4 px-10 py-6 bg-purple-600 hover:bg-purple-700 text-white text-2xl font-bold rounded-2xl transition-all shadow-lg hover:shadow-xl">
                            <span class="material-symbols-outlined text-3xl">menu_book</span>
                            Student Handbook
                        </a>
                        <a href="life_at_vvu.php" class="inline-flex items-center gap-4 px-10 py-6 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-2xl font-bold rounded-2xl hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                            <span class="material-symbols-outlined text-3xl">explore</span>
                            Life @ VVU
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Principles Section -->
    <section class="section-spacing bg-gradient-to-br from-purple-50 to-blue-50 dark:from-gray-900 dark:to-gray-800">
        <div class="container mx-auto max-w-7xl px-8">
            <div class="text-center mb-20 animate-fadeInUp">
                <span class="inline-block px-6 py-2 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-2xl font-bold rounded-full mb-6 uppercase tracking-wider">
                    Our Guiding Standards
                </span>
                <h2 class="text-5xl md:text-6xl lg:text-7xl font-black text-gray-900 dark:text-white mb-8">
                    <?php echo strip_tags($content['principles_heading']); ?>
                </h2>
                <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 max-w-4xl mx-auto font-medium leading-relaxed">
                    <?php echo strip_tags($content['principles_subtitle']); ?>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <?php 
                $principles = getPhilosophyPrinciples($pdo);
                foreach ($principles as $principle): 
                ?>
                <div class="principle-card glass p-10 rounded-[2.5rem] border-t-8 border-<?php echo $principle['border_color']; ?>">
                    <div class="w-24 h-24 rounded-3xl bg-gradient-to-br from-<?php echo str_replace('-600', '-500', $principle['border_color']); ?> to-<?php echo $principle['border_color']; ?> flex items-center justify-center text-white shadow-xl mb-8">
                        <span class="material-symbols-outlined text-5xl"><?php echo strip_tags($principle['icon']); ?></span>
                    </div>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($principle['title']); ?></h3>
                    <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags($principle['description']); ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Our Philosophy Statement -->
    <section class="section-spacing bg-white dark:bg-gray-900">
        <div class="container mx-auto max-w-7xl px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="animate-fadeInUp">
                    <div class="inline-flex items-center gap-3 px-6 py-3 mb-8 rounded-full bg-blue-100 dark:bg-blue-900/30">
                        <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">auto_stories</span>
                        <span class="text-xl font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Our Commitment</span>
                    </div>
                    
                    <h2 class="text-5xl md:text-5xl font-black text-gray-900 dark:text-white mb-10 leading-tight">
                        <?php echo strip_tags($content['holistic_heading']); ?>
                    </h2>

                    <div class="space-y-8">
                        <div class="flex items-start gap-6">
                            <div class="w-14 h-14 rounded-2xl bg-blue-600 flex items-center justify-center text-white shrink-0 shadow-lg">
                                <span class="material-symbols-outlined text-2xl">church</span>
                            </div>
                            <div>
                                <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-3"><?php echo strip_tags($content['adventist_values_title']); ?></h4>
                                <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                                    <?php echo strip_tags($content['adventist_values_text']); ?>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-6">
                            <div class="w-14 h-14 rounded-2xl bg-purple-600 flex items-center justify-center text-white shrink-0 shadow-lg">
                                <span class="material-symbols-outlined text-2xl">diversity_3</span>
                            </div>
                            <div>
                                <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-3"><?php echo strip_tags($content['universal_respect_title']); ?></h4>
                                <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                                    <?php echo strip_tags($content['universal_respect_text']); ?>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-6">
                            <div class="w-14 h-14 rounded-2xl bg-green-600 flex items-center justify-center text-white shrink-0 shadow-lg">
                                <span class="material-symbols-outlined text-2xl">psychology</span>
                            </div>
                            <div>
                                <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-3"><?php echo strip_tags($content['total_person_title']); ?></h4>
                                <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                                    <?php echo strip_tags($content['total_person_text']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quote Card -->
                <div class="relative animate-fadeInUp" style="animation-delay: 0.2s;">
                    <div class="absolute -inset-4 bg-gradient-to-r from-purple-600 to-blue-600 rounded-[3rem] blur-xl opacity-20"></div>
                    <div class="relative bg-gradient-to-br from-purple-600 to-blue-700 p-12 md:p-16 rounded-[3rem] text-white shadow-2xl">
                        <span class="material-symbols-outlined text-8xl text-white/30 mb-8 block">format_quote</span>
                        <blockquote class="text-3xl md:text-4xl font-bold leading-relaxed mb-10 italic">
                            "<?php echo strip_tags($content['quote_text']); ?>"
                        </blockquote>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                                <span class="material-symbols-outlined text-3xl">school</span>
                            </div>
                            <div>
                                <p class="text-4xl font-black" style="color: #ffd500ff;"><?php echo strip_tags($content['quote_author']); ?></p>
                                <p class="text-2xl text-white/80"><?php echo strip_tags($content['quote_author_title']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dress Code Guidelines -->
    <section class="section-spacing bg-gray-100 dark:bg-gray-950">
        <div class="container mx-auto max-w-7xl px-8">
            <div class="text-center mb-20 animate-fadeInUp">
                <span class="inline-block px-6 py-2 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-2xl font-bold rounded-full mb-6 uppercase tracking-wider">
                    Guidelines
                </span>
                <h2 class="text-5xl md:text-6xl lg:text-7xl font-black text-gray-900 dark:text-white mb-8">
                    What We Encourage
                </h2>
                <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 max-w-4xl mx-auto font-medium leading-relaxed">
                    Practical guidelines to help you dress appropriately on campus
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Encouraged -->
                <div class="bg-white dark:bg-gray-800 p-12 rounded-[2.5rem] shadow-xl border-l-8 border-green-500">
                    <div class="flex items-center gap-6 mb-10">
                        <div class="w-20 h-20 rounded-3xl bg-green-500 flex items-center justify-center text-white shadow-lg">
                            <span class="material-symbols-outlined text-5xl">thumb_up</span>
                        </div>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white">Encouraged</h3>
                    </div>
                    <ul class="space-y-6">
                        <?php 
                        $encouraged = parseLineItems($content['encouraged_items']);
                        foreach ($encouraged as $item): 
                        ?>
                        <li class="flex items-start gap-5">
                            <span class="material-symbols-outlined text-green-500 text-3xl shrink-0 mt-1">check_circle</span>
                            <span class="text-2xl text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags($item); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Discouraged -->
                <div class="bg-white dark:bg-gray-800 p-12 rounded-[2.5rem] shadow-xl border-l-8 border-red-500">
                    <div class="flex items-center gap-6 mb-10">
                        <div class="w-20 h-20 rounded-3xl bg-red-500 flex items-center justify-center text-white shadow-lg">
                            <span class="material-symbols-outlined text-5xl">block</span>
                        </div>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white">Discouraged</h3>
                    </div>
                    <ul class="space-y-6">
                        <?php 
                        $discouraged = parseLineItems($content['discouraged_items']);
                        foreach ($discouraged as $item): 
                        ?>
                        <li class="flex items-start gap-5">
                            <span class="material-symbols-outlined text-red-500 text-3xl shrink-0 mt-1">cancel</span>
                            <span class="text-2xl text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags($item); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Why It Matters -->
    <section class="section-spacing bg-white dark:bg-gray-900">
        <div class="container mx-auto max-w-7xl px-8">
            <div class="text-center mb-20 animate-fadeInUp">
                <span class="inline-block px-6 py-2 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 text-2xl font-bold rounded-full mb-6 uppercase tracking-wider">
                    Why It Matters
                </span>
                <h2 class="text-5xl md:text-6xl lg:text-7xl font-black text-gray-900 dark:text-white mb-8">
                    Benefits of Our Dress Philosophy
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php 
                $benefits = getPhilosophyBenefits($pdo);
                foreach ($benefits as $benefit): 
                ?>
                <div class="text-center p-10">
                    <div class="w-28 h-28 rounded-full bg-gradient-to-br from-<?php echo strip_tags($benefit['gradient_start']); ?> to-<?php echo strip_tags($benefit['gradient_end']); ?> flex items-center justify-center text-white mx-auto mb-8 shadow-2xl">
                        <span class="material-symbols-outlined text-6xl"><?php echo strip_tags($benefit['icon']); ?></span>
                    </div>
                    <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($benefit['title']); ?></h4>
                    <p class="text-xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        <?php echo strip_tags($benefit['description']); ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-32 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-900 to-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        
        <div class="container relative z-10 mx-auto max-w-7xl px-8">
            <div class="bg-white/10 backdrop-blur-xl rounded-[4rem] p-16 md:p-24 border border-white/20 text-center">
                <h2 class="text-5xl md:text-6xl lg:text-7xl font-black text-white mb-8"><?php echo strip_tags($content['cta_heading']); ?></h2>
                <p class="text-2xl md:text-3xl text-white/90 mb-16 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($content['cta_text']); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-8 justify-center">
                    <a href="contact_us.php" class="px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-purple-900 text-2xl font-black rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl">contact_support</span>
                        Contact Student Affairs
                    </a>
                    <a href="student_handbook.php" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-2xl font-black rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl">menu_book</span>
                        Student Handbook
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>