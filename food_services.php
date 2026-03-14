<?php
$page_title = "Food Services - Valley View University";
$active_page = "life_vvu";
include 'includes/header.php';
require_once 'includes/campus_life_content_helper.php';

// Fetch content from database
$content = getFoodServicesContent($pdo);

// Use default values if no content found
if (!$content) {
    $content = [
        'hero_title' => 'Nourishing Body & Mind',
        'hero_subtitle' => 'Experience wholesome, vegetarian cuisine prepared with care for our university community',
        'hero_image' => 'images/cafeteria_interior.png',
        'philosophy_heading' => 'A Healthy Mind Starts with a Healthy Plate',
        'philosophy_text' => 'At Valley View University, we believe that physical well-being is the foundation of academic and spiritual growth.',
        'philosophy_image' => 'images/vegetarian_meal.png',
        'breakfast_time' => '6:30 - 8:30',
        'lunch_time' => '10:00 - 2:00',
        'dinner_time' => '4:00 - 6:00',
        'meal_plans_description' => 'Flexible meal plans available for all students.',
        'feedback_heading' => 'How are we doing?',
        'feedback_text' => 'Your feedback helps us improve.'
    ];
}
?>

<style>
    :root {
        --vvu-blue: #003366;
        --vvu-gold: #C5A059;
        --vvu-green: #2D5A27;
        --vvu-light-green: #E8F5E9;
    }

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

    .hero-text {
        text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
    }

    .dark .glass-card {
        background: rgba(17, 24, 39, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .section-badge {
        display: inline-block;
        padding: 0.5rem 1.5rem;
        background: var(--vvu-light-green);
        color: var(--vvu-green);
        border-radius: 9999px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
    }

    .dark .section-badge {
        background: rgba(45, 90, 39, 0.2);
        color: #4ade80;
    }

    .image-reveal {
        position: relative;
        overflow: hidden;
    }

    .image-reveal::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: var(--vvu-blue);
        transform: translateX(-101%);
        transition: transform 0.6s cubic-bezier(0.77, 0, 0.175, 1);
    }

    .image-reveal.active::after {
        transform: translateX(101%);
    }

    .schedule-card {
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .schedule-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .meal-plan-card {
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .meal-plan-card:hover {
        border-color: var(--vvu-gold);
        background: white;
    }

    .dark .meal-plan-card:hover {
        background: #1f2937;
    }
</style>

<main class="bg-white dark:bg-gray-950 overflow-hidden">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($content['hero_image']); ?>" 
                 alt="VVU Cafeteria" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400">Campus Life</span>
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

    <!-- Philosophy Section -->
    <section id="philosophy" class="py-32 relative">
        <div class="container px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-24 items-center">
                <div class="relative">
                    <div class="rounded-[3rem] overflow-hidden shadow-2xl">
                        <img src="<?php echo strip_tags($content['philosophy_image']); ?>" alt="Healthy Vegetarian Meal" class="w-full h-[380px] object-cover transform hover:scale-105 transition-transform duration-1000">
                    </div>
                    <div class="absolute -bottom-12 -right-12 w-64 h-64 bg-green-600 rounded-[2rem] p-10 flex flex-col justify-center text-white shadow-2xl hidden md:flex">
                        <span class="text-5xl font-black mb-2">100%</span>
                        <span class="text-xl font-bold uppercase tracking-wider">Vegetarian Entrees</span>
                    </div>
                </div>
                
                <div>
                    <span class="section-badge">Our Philosophy</span>
                    <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-10 leading-tight">
                        <?php echo strip_tags($content['philosophy_heading']); ?>
                    </h2>
                    <p class="text-2xl text-gray-600 dark:text-gray-400 leading-relaxed mb-12">
                        At Valley View University, we believe that physical well-being is the foundation of academic and spiritual growth. Our cafeteria is dedicated to providing balanced, nutritious, and delicious vegetarian meals that fuel your journey.
                    </p>
                    
                    <div class="space-y-8">
                        <div class="flex gap-6">
                            <div class="w-16 h-16 shrink-0 rounded-2xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                <span class="material-symbols-outlined text-3xl">eco</span>
                            </div>
                            <div>
                                <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Wholesome Nutrition</h4>
                                <p class="text-2xl text-gray-500 dark:text-gray-400">Carefully curated menus designed by nutritionists to ensure a balanced diet.</p>
                            </div>
                        </div>
                        <div class="flex gap-6">
                            <div class="w-16 h-16 shrink-0 rounded-2xl bg-green-50 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                                <span class="material-symbols-outlined text-3xl">restaurant</span>
                            </div>
                            <div>
                                <h4 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Vegetarian Excellence</h4>
                                <p class="text-2xl text-gray-500 dark:text-gray-400">All entrees served are strictly vegetarian, promoting a lifestyle of health and compassion.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Schedule Section -->
    <section id="schedule" class="py-32 bg-gray-50 dark:bg-gray-900/50">
        <div class="container px-6">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <span class="section-badge">Dining Hours</span>
                <h2 class="text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-6">When to Dine</h2>
                <p class="text-2xl text-gray-600 dark:text-gray-400">Join us for freshly prepared meals throughout the day at our main cafeteria.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Breakfast -->
                <div class="schedule-card glass-card p-12 rounded-[2.5rem] text-center">
                    <div class="w-24 h-24 mx-auto rounded-3xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 mb-8">
                        <span class="material-symbols-outlined text-5xl">light_mode</span>
                    </div>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-4">Breakfast</h3>
                    <p class="text-4xl font-black text-blue-900 dark:text-blue-400 mb-6"><?php echo strip_tags($content['breakfast_time']); ?></p>
                    <p class="text-xl text-gray-500 dark:text-gray-400"><?php echo strip_tags($content['breakfast_desc']); ?></p>
                </div>

                <!-- Lunch -->
                <div class="schedule-card glass-card p-12 rounded-[2.5rem] text-center border-2 border-blue-600/20">
                    <div class="w-24 h-24 mx-auto rounded-3xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 mb-8">
                        <span class="material-symbols-outlined text-5xl">wb_sunny</span>
                    </div>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-4">Lunch</h3>
                    <p class="text-4xl font-black text-blue-900 dark:text-blue-400 mb-6"><?php echo strip_tags($content['lunch_time']); ?></p>
                    <p class="text-xl text-gray-500 dark:text-gray-400"><?php echo strip_tags($content['lunch_desc']); ?></p>
                </div>

                <!-- Dinner -->
                <div class="schedule-card glass-card p-12 rounded-[2.5rem] text-center">
                    <div class="w-24 h-24 mx-auto rounded-3xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 mb-8">
                        <span class="material-symbols-outlined text-5xl">dark_mode</span>
                    </div>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-4">Dinner</h3>
                    <p class="text-4xl font-black text-blue-900 dark:text-blue-400 mb-6"><?php echo strip_tags($content['dinner_time']); ?></p>
                    <p class="text-xl text-gray-500 dark:text-gray-400"><?php echo strip_tags($content['dinner_desc']); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Meal Plans Section -->
    <section class="py-32">
        <div class="container px-6">
            <div class="bg-blue-900 rounded-[4rem] overflow-hidden relative">
                <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 p-16 md:p-24 items-center relative z-10">
                    <div>
                        <h2 class="text-5xl md:text-7xl font-black text-white mb-8"><?php echo strip_tags($content['meal_plans_heading']); ?></h2>
                        <p class="text-2xl text-blue-100 leading-relaxed mb-12">
                            <?php echo strip_tags($content['meal_plans_text']); ?>
                        </p>
                        <div class="flex flex-wrap gap-6">
                            <div class="px-8 py-4 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl">
                                <span class="block text-3xl font-black text-white">Full Board</span>
                                <span class="text-blue-200">3 Meals Daily</span>
                            </div>
                            <div class="px-8 py-4 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl">
                                <span class="block text-3xl font-black text-white">Partial</span>
                                <span class="text-blue-200">Lunch Only</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 p-12 rounded-[3rem] shadow-2xl">
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Ready to register?</h3>
                        <p class="text-xl text-gray-500 dark:text-gray-400 mb-10"><?php echo strip_tags($content['meal_plans_reg_info']); ?></p>
                        <a href="<?php echo strip_tags($content['meal_plans_btn_url']); ?>" class="block w-full py-6 bg-[#C5A059] hover:bg-[#b08d4a] text-white text-center text-2xl font-black rounded-2xl transition-all shadow-xl">
                            <?php echo strip_tags($content['meal_plans_btn_text']); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section class="py-32">
        <div class="container px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php 
                $features = getFoodServicesFeatures($pdo);
                foreach ($features as $feature): 
                ?>
                <div class="p-10 rounded-[2.5rem] bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 hover:border-green-500/30 transition-all group">
                    <div class="w-20 h-20 rounded-2xl bg-white dark:bg-gray-800 shadow-lg flex items-center justify-center text-green-500 mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-4xl"><?php echo strip_tags($feature['icon']); ?></span>
                    </div>
                    <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-4"><?php echo strip_tags($feature['title']); ?></h4>
                    <p class="text-xl md:text-2xl text-gray-500 dark:text-gray-400"><?php echo strip_tags($feature['description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Feedback Section -->
    <section class="py-32 bg-gray-950 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-blue-900/20 blur-[120px] rounded-full"></div>
        <div class="container px-6 relative z-10 text-center">
            <h2 class="text-5xl md:text-8xl font-black mb-10">How are we doing?</h2>
            <p class="text-2xl md:text-3xl text-gray-400 max-w-3xl mx-auto mb-16">
                Your feedback helps us improve. Tell us about your dining experience or suggest new menu items.
            </p>
            <a href="contact_us.php" class="inline-flex items-center gap-4 px-12 py-6 bg-white text-gray-950 text-2xl font-black rounded-2xl hover:bg-gold-500 hover:text-white transition-all group">
                Send Feedback
                <span class="material-symbols-outlined group-hover:translate-x-2 transition-transform">arrow_forward</span>
            </a>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
