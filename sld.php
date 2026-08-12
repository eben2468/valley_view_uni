<?php
$page_title = "Spiritual Life and Development - Valley View University";
$active_page = "student-life";
include 'includes/header.php';
require_once 'includes/campus_life_content_helper.php';

// Fetch content from database
$content = getSLDContent($pdo);

// Use default values if no content found
if (!$content) {
    $content = [
        'hero_title' => 'Spiritual Life & Development',
        'hero_subtitle' => 'Nurturing faith, character, and purpose in every student\'s journey',
        'hero_image' => 'https://images.unsplash.com/photo-1438232992991-995b7058bbb3?auto=format&fit=crop&q=80&w=1920',
        'welcome_heading' => 'Welcome to SLD Office',
        'welcome_text' => 'The Spiritual Life and Development office is committed to fostering holistic growth through spiritual guidance, counseling, and ministry programs.',
        'welcome_image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=1200',
        'mission_statement' => 'To nurture spiritual growth, provide pastoral care, and empower students to live purpose-driven lives rooted in Christian values.',
        'dean_name' => 'Emmanuel H. Takyi, Ph.D, DMin',
        'dean_title' => 'Dean of Spiritual Life And Development Office',
        'dean_description' => 'Leading our vision for holistic spiritual development across the university community.',
        'cta_heading' => 'Need Spiritual Support?',
        'cta_text' => 'Our doors are always open. Whether you need counseling, prayer, or simply someone to talk to, we\'re here for you.'
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
    /* ---------------- Service cards ----------------
       Each card carries its own accent through the --c / --c2 custom
       properties set inline, so the colour drives the icon gradient, the
       top rule, the hover glow and the shadow from one place. Doing it with
       variables rather than `bg-<colour>-600` class names also means the
       shadow and tint can use real alpha values, which Tailwind's palette
       utilities cannot express here. */
    .sld-card {
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
        padding: 2.25rem 2rem 2.5rem;
        background: #fff;
        border: 1px solid rgb(226 232 240);
        border-radius: 1.5rem;
        overflow: hidden;
        isolation: isolate;
        transition: transform .4s cubic-bezier(.2,.8,.2,1), box-shadow .4s ease, border-color .4s ease;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04), 0 8px 24px -16px rgba(15, 23, 42, .25);
    }
    .dark .sld-card {
        background: rgb(17 24 39);
        border-color: rgb(55 65 81);
    }

    /* Accent rule across the top — grows in from the left on hover. */
    .sld-card::before {
        content: "";
        position: absolute;
        inset: 0 0 auto 0;
        height: 4px;
        background: linear-gradient(90deg, var(--c), var(--c2));
        transform: scaleX(.28);
        transform-origin: left;
        transition: transform .45s cubic-bezier(.2,.8,.2,1);
    }
    .sld-card:hover::before,
    .sld-card:focus-within::before { transform: scaleX(1); }

    /* Soft wash of the accent colour, top-right, revealed on hover. */
    .sld-card::after {
        content: "";
        position: absolute;
        top: -70px; right: -70px;
        width: 200px; height: 200px;
        border-radius: 999px;
        background: radial-gradient(circle, var(--c) 0%, transparent 70%);
        opacity: 0;
        transition: opacity .45s ease;
        z-index: -1;
    }
    .sld-card:hover::after,
    .sld-card:focus-within::after { opacity: .13; }

    .sld-card:hover,
    .sld-card:focus-within {
        transform: translateY(-8px);
        border-color: color-mix(in srgb, var(--c) 35%, transparent);
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04),
                    0 26px 44px -22px color-mix(in srgb, var(--c) 55%, transparent);
    }

    .sld-card__icon {
        width: 4rem; height: 4rem;
        display: flex; align-items: center; justify-content: center;
        border-radius: 1.15rem;
        margin-bottom: 1.5rem;
        color: #fff;
        background: linear-gradient(140deg, var(--c), var(--c2));
        box-shadow: 0 10px 20px -10px color-mix(in srgb, var(--c) 75%, transparent);
        transition: transform .4s cubic-bezier(.2,.8,.2,1);
    }
    .sld-card__icon .material-symbols-outlined { font-size: 2rem; color: #fff; }
    .sld-card:hover .sld-card__icon,
    .sld-card:focus-within .sld-card__icon { transform: translateY(-2px) scale(1.06) rotate(-3deg); }

    .sld-card__title {
        font-size: 1.5rem;
        line-height: 1.25;
        font-weight: 700;
        letter-spacing: .01em;
        color: rgb(15 23 42);
        margin: 0 0 .75rem;
    }
    .dark .sld-card__title { color: #fff; }

    /* A short accent underline keeps the serif heading anchored to the card's
       colour without another heavy border. */
    .sld-card__title::after {
        content: "";
        display: block;
        width: 2.25rem; height: 3px;
        margin-top: .85rem;
        border-radius: 999px;
        background: linear-gradient(90deg, var(--c), var(--c2));
        opacity: .85;
        transition: width .45s cubic-bezier(.2,.8,.2,1);
    }
    .sld-card:hover .sld-card__title::after,
    .sld-card:focus-within .sld-card__title::after { width: 3.75rem; }

    .sld-card__text {
        font-size: 1.0625rem;
        line-height: 1.7;
        color: rgb(71 85 105);
        margin: 0;
    }
    .dark .sld-card__text { color: rgb(203 213 225); }

    /* color-mix is recent; these fallbacks keep older browsers sane. */
    @supports not (color: color-mix(in srgb, red 50%, transparent)) {
        .sld-card:hover, .sld-card:focus-within {
            border-color: rgb(203 213 225);
            box-shadow: 0 26px 44px -22px rgba(15, 23, 42, .35);
        }
        .sld-card__icon { box-shadow: 0 10px 20px -10px rgba(15, 23, 42, .45); }
    }

    @media (prefers-reduced-motion: reduce) {
        .sld-card, .sld-card::before, .sld-card::after,
        .sld-card__icon, .sld-card__title::after { transition: none !important; }
        .sld-card:hover, .sld-card:focus-within { transform: none; }
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($content['hero_image']); ?>" 
                 alt="Spiritual Life at VVU" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
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

    <!-- Welcome Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-20">
                    <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6">Welcome to SLD Office</h2>
                    <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed max-w-4xl mx-auto">
                        The Spiritual Life and Development office is committed to fostering holistic growth through spiritual guidance, counseling, and ministry programs that strengthen faith and character.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-stretch">
                    <div class="relative flex">
                        <div class="absolute -top-6 -left-6 w-72 h-72 bg-purple-200 dark:bg-purple-900/30 rounded-full blur-3xl opacity-40"></div>
                        <div class="relative rounded-3xl overflow-hidden shadow-2xl flex-1">
                            <img src="<?php echo strip_tags($content['welcome_image']); ?>" 
                                 alt="Spiritual Life at VVU" class="w-full h-full object-cover">
                        </div>
                    </div>

                    <div class="space-y-8 flex flex-col justify-center">
                        <div class="prose prose-lg dark:prose-invert max-w-none">
                            <p class="text-3xl text-gray-700 dark:text-gray-300 leading-relaxed font-medium mb-8">
                                At Valley View University, we believe that true education encompasses the development of the whole person—<strong class="text-blue-600 dark:text-blue-400">mind, body, and spirit</strong>. Our Spiritual Life and Development office provides comprehensive support through chaplaincy services, counseling, and student ministries.
                            </p>
                            
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 p-10 rounded-3xl border-l-8 border-blue-600 shadow-lg">
                                <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-6 flex items-center gap-4">
                                    <span class="material-symbols-outlined text-5xl text-blue-600">church</span>
                                    Our Mission
                                </h3>
                                <p class="text-3xl text-gray-800 dark:text-gray-200 font-bold leading-relaxed">
                                    To nurture spiritual growth, provide pastoral care, and empower students to live purpose-driven lives rooted in Christian values and service to humanity.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Services Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6">Our Services</h2>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">Comprehensive spiritual care and development programs designed to support your journey.</p>
            </div>

            <?php
            // The colour stored per service is a Tailwind palette name. Mapping it
            // to real hex here lets the card CSS build gradients, tinted shadows and
            // hover glows from it — none of which a `bg-<name>-600` class can do.
            // Unknown names fall back to the house blue rather than rendering
            // colourless.
            $sld_palette = [
                'blue'   => ['#2563eb', '#1d4ed8'],
                'green'  => ['#16a34a', '#15803d'],
                'purple' => ['#9333ea', '#7e22ce'],
                'yellow' => ['#d97706', '#b45309'],
                'red'    => ['#dc2626', '#b91c1c'],
                'indigo' => ['#4f46e5', '#4338ca'],
                'teal'   => ['#0d9488', '#0f766e'],
                'pink'   => ['#db2777', '#be185d'],
                'orange' => ['#ea580c', '#c2410c'],
            ];
            ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <?php
                $services = getSLDServices($pdo);
                foreach ($services as $service):
                    $key = strtolower(trim((string) ($service['color'] ?? '')));
                    [$c1, $c2] = $sld_palette[$key] ?? $sld_palette['blue'];
                ?>
                <!-- Service -->
                <article class="sld-card" style="--c: <?php echo $c1; ?>; --c2: <?php echo $c2; ?>;">
                    <div class="sld-card__icon">
                        <span class="material-symbols-outlined" aria-hidden="true"><?php echo strip_tags($service['icon']); ?></span>
                    </div>
                    <h3 class="sld-card__title"><?php echo strip_tags($service['title']); ?></h3>
                    <p class="sld-card__text">
                        <?php echo strip_tags($service['description']); ?>
                    </p>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Staff Team Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6">Meet Our Team</h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">Dedicated professionals committed to your spiritual growth and well-being.</p>
            </div>

            <div class="max-w-6xl mx-auto space-y-6">
                <!-- Dean -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-3xl p-10 shadow-lg border-l-8 border-blue-600">
                    <div class="flex items-start gap-6">
                        <div class="w-20 h-20 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg flex-shrink-0">
                            <span class="material-symbols-outlined text-4xl text-white">workspace_premium</span>
                        </div>
                        <div>
                            <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags($content['dean_name']); ?></h3>
                            <p class="text-3xl text-blue-700 dark:text-blue-400 font-bold mb-4"><?php echo strip_tags($content['dean_title']); ?></p>
                            <p class="text-2xl text-gray-700 dark:text-gray-300 leading-relaxed"><?php echo strip_tags($content['dean_description']); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Staff Members Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php 
                    $staff_members = getSLDStaff($pdo);
                    foreach ($staff_members as $index => $staff): 
                    ?>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-100 dark:border-gray-700 <?php echo $index === count($staff_members) - 1 && count($staff_members) % 2 !== 0 ? 'md:col-span-2' : ''; ?>">
                        <div class="flex items-center gap-4 mb-4">
                            <?php // The column is icon_color; reading $staff['color'] always
                                  // yielded null, so every icon rendered blue regardless of
                                  // what was set in the admin panel. ?>
                            <span class="material-symbols-outlined text-4xl text-<?php echo strip_tags($staff['icon_color'] ?: 'blue'); ?>-600">person</span>
                            <div>
                                <h4 class="text-3xl font-black text-gray-900 dark:text-white"><?php echo strip_tags($staff['name']); ?></h4>
                                <p class="text-xl text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags($staff['position']); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Campus Locations Section -->
    <section class="py-24 bg-blue-900 overflow-hidden relative">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-4xl mx-auto text-center mb-16">
                <h2 class="text-5xl sm:text-6xl font-black text-white mb-6">We're Here For You</h2>
                <p class="text-2xl text-blue-100 font-medium leading-relaxed">Our team serves students across all Valley View University campuses.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <?php 
                $locations = getSLDLocations($pdo);
                foreach ($locations as $loc): 
                ?>
                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-10 border border-white/20 text-center">
                    <div class="w-20 h-20 rounded-2xl bg-yellow-400 flex items-center justify-center mx-auto mb-8">
                        <span class="material-symbols-outlined text-4xl text-blue-900"><?php echo strip_tags($loc['icon']); ?></span>
                    </div>
                    <h3 class="text-4xl font-black text-white mb-4"><?php echo strip_tags($loc['title']); ?></h3>
                    <p class="text-xl text-blue-100 leading-relaxed"><?php echo strip_tags($loc['description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-24 overflow-hidden bg-gray-50 dark:bg-gray-950">
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl lg:text-6xl font-black text-gray-900 dark:text-white mb-8 leading-tight tracking-tight">
                    Need Spiritual Support?
                </h2>
                <p class="text-2xl sm:text-3xl md:text-3xl text-gray-600 dark:text-gray-400 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    Our doors are always open. Whether you need counseling, prayer, or simply someone to talk to, we're here for you.
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="contact_us.php" class="px-10 py-5 bg-blue-600 hover:bg-blue-700 text-white text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl text-white">connect_without_contact</span>
                        Connect With Us
                    </a>
                    <a href="student_life.php" class="px-10 py-5 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-white text-xl font-bold rounded-2xl transition-all border-2 border-gray-200 dark:border-gray-700 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">explore</span>
                        Explore Student Life
                    </a>
                </div>
                
                <div class="mt-20 grid grid-cols-1 sm:grid-cols-3 gap-12 border-t border-gray-200 dark:border-gray-800 pt-16">
                    <div>
                        <div class="text-6xl font-black text-blue-600 mb-2"><?php echo strip_tags($content['stats_staff']); ?></div>
                        <div class="text-gray-600 dark:text-gray-400 uppercase tracking-widest text-2xl font-black">Staff Members</div>
                    </div>
                    <div>
                        <div class="text-6xl font-black text-blue-600 mb-2"><?php echo strip_tags($content['stats_locations']); ?></div>
                        <div class="text-gray-600 dark:text-gray-400 uppercase tracking-widest text-2xl font-black">Campus Locations</div>
                    </div>
                    <div>
                        <div class="text-6xl font-black text-blue-600 mb-2">24/7</div>
                        <div class="text-gray-600 dark:text-gray-400 uppercase tracking-widest text-2xl font-black">Support Available</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
