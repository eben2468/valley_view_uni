<?php
$page_title = "Valley View Radio - Voice of the Valley";
$active_page = "stories";
include 'includes/header.php';
require_once 'includes/db_connect.php';

// Fetch main content
$stmt = $pdo->query("SELECT * FROM radio_content WHERE id = 1");
$content = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch programs
$stmt = $pdo->query("SELECT * FROM radio_programs WHERE status = 'active' ORDER BY display_order ASC");
$programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch features
$stmt = $pdo->query("SELECT * FROM radio_features ORDER BY display_order ASC");
$features = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fallbacks if database is empty (shouldn't be after migration)
$content = $content ?: [
    'hero_title' => 'Valley View Radio',
    'hero_subtitle' => '"Voice of the Valley — Your #1 Campus Station for Music, News, and Spiritual Inspiration."',
    'hero_image' => 'images/vvu_radio_hero_bg.png',
    'live_on_air_text' => 'Live On Air',
    'now_playing_heading' => 'Now Playing',
    'current_show' => 'The Morning Rise',
    'current_host' => 'DJ Grace & Bro. Samuel',
    'current_show_image' => 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
    'next_show_time' => 'Campus Pulse @ 10:00 AM',
    'frequency' => '97.7 FM',
    'about_heading' => 'About Valley View Radio',
    'about_text' => 'Valley View Radio is the heartbeat of our campus, broadcasting 24/7 to provide a unique blend of educational content, spiritual nourishment, and the best in contemporary and traditional music.',
    'programs_heading' => 'Program Highlights',
    'programs_text' => 'Tune in to our most popular shows throughout the week.',
    'cta_heading' => 'Join the Conversation',
    'cta_text' => 'Want to request a song, share a shoutout, or join our team of presenters? We\'d love to hear from you!',
    'cta_phone' => '+233 307 011 832',
    'cta_email' => 'radio@vvu.edu.gh',
    'location_text' => 'Mile 19 Off the Adenta-Dodowa Road, Oyibi, Accra',
    'facebook_url' => '#',
    'twitter_url' => '#',
    'instagram_url' => '#',
    'hero_cta_1_text' => 'Listen Live',
    'hero_cta_1_link' => '#listen-live',
    'hero_cta_2_text' => 'Program Schedule',
    'hero_cta_2_link' => '#schedule',
    'about_image_1' => 'https://images.unsplash.com/photo-1478737270239-2f02b77fc618?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
    'about_image_2' => 'https://images.unsplash.com/photo-1520529011870-5c6b44c0bb8c?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
    'about_image_3' => 'https://images.unsplash.com/photo-1516280440614-37939bbacd81?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
    'about_image_4' => 'https://images.unsplash.com/photo-1493225255756-d9584f8606e9?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'
];
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
    @keyframes pulse-purple {
        0% { box-shadow: 0 0 0 0 rgba(139, 92, 246, 0.7); }
        70% { box-shadow: 0 0 0 20px rgba(139, 92, 246, 0); }
        100% { box-shadow: 0 0 0 0 rgba(139, 92, 246, 0); }
    }
    @keyframes sound-wave {
        0%, 100% { height: 10px; }
        50% { height: 40px; }
    }
    
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .animate-pulse-purple { animation: pulse-purple 2s infinite; }
    
    .radio-gradient {
        background: linear-gradient(135deg, #4c1d95 0%, #7c3aed 50%, #c026d3 100%);
    }
    .radio-text-gradient {
        background: linear-gradient(to right, #a78bfa, #f472b6, #fb923c);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .glass {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .program-card {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .program-card:hover {
        transform: translateY(-15px) scale(1.02);
    }
    .sound-bar {
        width: 4px;
        background: #a78bfa;
        border-radius: 2px;
        animation: sound-wave 1s ease-in-out infinite;
    }
    .sound-bar:nth-child(2) { animation-delay: 0.1s; }
    .sound-bar:nth-child(3) { animation-delay: 0.2s; }
    .sound-bar:nth-child(4) { animation-delay: 0.3s; }
    .sound-bar:nth-child(5) { animation-delay: 0.4s; }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[75vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($content['hero_image']); ?>" 
                 alt="VVU Radio Studio" class="w-full h-full object-cover animate-slow-zoom opacity-50">
            <div class="absolute inset-0 bg-gradient-to-b from-purple-900/80 via-indigo-900/60 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-6xl mx-auto text-center">
                <div class="inline-flex items-center gap-4 px-8 py-3 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-600"></span>
                    </span>
                    <span class="text-lg md:text-xl font-black tracking-widest uppercase text-white"><?php echo strip_tags($content['live_on_air_text']); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php 
                    $title_parts = explode('<br>', $content['hero_title']);
                    echo strip_tags($title_parts[0]);
                    if (isset($title_parts[1])):
                        echo "<br><span class='text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-pink-400 to-orange-400 block mt-4'>" . strip_tags($title_parts[1]) . "</span>";
                    endif;
                    ?>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic mb-12" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($content['hero_subtitle']); ?>
                </p>
                <div class="flex flex-wrap justify-center gap-6 animate-fadeInUp" style="animation-delay: 0.3s;">
                    <a href="<?php echo strip_tags($content['hero_cta_1_link']); ?>" class="px-10 py-5 radio-gradient text-white text-xl font-black rounded-2xl hover:scale-105 transition-all shadow-[0_0_30px_rgba(124,58,237,0.5)] flex items-center gap-4">
                        <span class="material-symbols-outlined text-3xl">play_circle</span>
                        <?php echo strip_tags($content['hero_cta_1_text']); ?>
                    </a>
                    <a href="<?php echo strip_tags($content['hero_cta_2_link']); ?>" class="px-10 py-5 bg-white/10 backdrop-blur-md border border-white/20 text-white text-xl font-black rounded-2xl hover:bg-white/20 transition-all flex items-center gap-4">
                        <span class="material-symbols-outlined text-3xl">schedule</span>
                        <?php echo strip_tags($content['hero_cta_2_text']); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Live Player Section -->
    <section id="listen-live" class="py-24 bg-white dark:bg-gray-900 relative overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="max-w-7xl mx-auto">
                <div class="radio-gradient rounded-[4rem] p-12 md:p-20 shadow-2xl relative overflow-hidden">
                    <!-- Decorative Elements -->
                    <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full -mr-48 -mt-48 blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-400/20 rounded-full -ml-48 -mb-48 blur-3xl"></div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center relative z-10">
                        <!-- Player Info -->
                        <div>
                            <div class="flex items-center gap-6 mb-10">
                                <div class="flex gap-1 h-10 items-end">
                                    <div class="sound-bar"></div>
                                    <div class="sound-bar"></div>
                                    <div class="sound-bar"></div>
                                    <div class="sound-bar"></div>
                                    <div class="sound-bar"></div>
                                </div>
                                <span class="text-2xl font-bold text-purple-200 tracking-widest uppercase"><?php echo strip_tags($content['now_playing_heading']); ?></span>
                            </div>
                            <h2 class="text-6xl md:text-7xl font-black text-white mb-6"><?php echo strip_tags($content['current_show']); ?></h2>
                            <p class="text-3xl text-purple-100 font-medium mb-12">Hosted by <?php echo strip_tags($content['current_host']); ?></p>
                            
                            <!-- Player Controls -->
                            <div class="flex items-center gap-10">
                                <button class="w-32 h-32 rounded-full bg-white text-purple-900 flex items-center justify-center shadow-2xl hover:scale-110 transition-transform animate-pulse-purple">
                                    <span class="material-symbols-outlined text-7xl">play_arrow</span>
                                </button>
                                <div class="flex-grow">
                                    <div class="flex justify-between text-white text-xl mb-4 font-bold">
                                        <span>Live Stream</span>
                                        <span><?php echo strip_tags($content['frequency']); ?></span>
                                    </div>
                                    <div class="h-4 bg-white/20 rounded-full overflow-hidden">
                                        <div class="h-full bg-white w-2/3 rounded-full relative">
                                            <div class="absolute right-0 top-1/2 -translate-y-1/2 w-8 h-8 bg-white rounded-full shadow-lg border-4 border-purple-600"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Visualizer/Image -->
                        <div class="relative">
                            <div class="aspect-square rounded-[3rem] overflow-hidden shadow-2xl border-8 border-white/10">
                                <img src="<?php echo strip_tags($content['current_show_image']); ?>" 
                                     alt="Radio Studio" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-purple-900/80 to-transparent"></div>
                                <div class="absolute bottom-10 left-10 right-10">
                                    <div class="flex items-center justify-between text-white">
                                        <div>
                                            <p class="text-xl font-bold opacity-80 uppercase tracking-tighter">Up Next</p>
                                            <p class="text-3xl font-black"><?php echo strip_tags($content['next_show_time']); ?></p>
                                        </div>
                                        <span class="material-symbols-outlined text-5xl">skip_next</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-800/30">
        <div class="container mx-auto px-4">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                    <div>
                        <h2 class="text-5xl md:text-7xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($content['about_heading']); ?></h2>
                        <div class="h-2 w-40 bg-purple-600 rounded-full mb-10"></div>
                        <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed mb-10">
                            <?php echo strip_tags($content['about_text']); ?>
                        </p>
                        <ul class="space-y-6">
                            <?php foreach ($features as $feat): ?>
                            <li class="flex items-center gap-6">
                                <div class="w-16 h-16 rounded-2xl bg-<?php echo $feat['color_class']; ?>-100 dark:bg-<?php echo $feat['color_class']; ?>-900/30 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-3xl text-<?php echo $feat['color_class']; ?>-600"><?php echo strip_tags($feat['icon']); ?></span>
                                </div>
                                <span class="text-2xl font-bold text-gray-800 dark:text-gray-200"><?php echo strip_tags($feat['title']); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="grid grid-cols-2 gap-8">
                        <div class="space-y-8 mt-12">
                            <div class="rounded-[2.5rem] overflow-hidden shadow-xl transform hover:scale-105 transition-all">
                                <img src="<?php echo strip_tags($content['about_image_1']); ?>" alt="Mic" class="w-full h-64 object-cover">
                            </div>
                            <div class="rounded-[2.5rem] overflow-hidden shadow-xl transform hover:scale-105 transition-all">
                                <img src="<?php echo strip_tags($content['about_image_2']); ?>" alt="Studio" class="w-full h-80 object-cover">
                            </div>
                        </div>
                        <div class="space-y-8">
                            <div class="rounded-[2.5rem] overflow-hidden shadow-xl transform hover:scale-105 transition-all">
                                <img src="<?php echo strip_tags($content['about_image_3']); ?>" alt="Headphones" class="w-full h-80 object-cover">
                            </div>
                            <div class="rounded-[2.5rem] overflow-hidden shadow-xl transform hover:scale-105 transition-all">
                                <img src="<?php echo strip_tags($content['about_image_4']); ?>" alt="Music" class="w-full h-64 object-cover">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Program Schedule Section -->
    <section id="schedule" class="py-24 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-5xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($content['programs_heading']); ?></h2>
                <div class="h-2 w-40 radio-gradient mx-auto rounded-full mb-8"></div>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags($content['programs_text']); ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($programs as $prog): ?>
                <div class="program-card glass p-10 rounded-[3rem] shadow-xl border-t-8 border-<?php echo strip_tags($prog['border_color']); ?>">
                    <div class="w-20 h-20 rounded-2xl bg-<?php echo strip_tags($prog['icon_bg_color']); ?> flex items-center justify-center text-white mb-8 shadow-lg">
                        <span class="material-symbols-outlined text-4xl text-white"><?php echo strip_tags($prog['icon']); ?></span>
                    </div>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($prog['title']); ?></h3>
                    <p class="text-xl text-purple-600 dark:text-purple-400 font-bold mb-6"><?php echo strip_tags($prog['schedule']); ?></p>
                    <p class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed"><?php echo strip_tags($prog['description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="py-24 relative overflow-hidden">
        <div class="absolute inset-0 radio-gradient opacity-90"></div>
        <div class="container relative z-10 mx-auto px-4 text-center">
            <h2 class="text-6xl md:text-8xl font-black text-white mb-10"><?php echo strip_tags($content['cta_heading']); ?></h2>
            <p class="text-3xl text-purple-100 max-w-4xl mx-auto mb-16 font-medium">
                <?php echo strip_tags($content['cta_text']); ?>
            </p>
            <div class="flex flex-wrap justify-center gap-8">
                <a href="tel:<?php echo str_replace(' ', '', $content['cta_phone']); ?>" class="px-12 py-6 bg-white text-purple-900 text-2xl font-black rounded-2xl hover:scale-105 transition-all shadow-2xl flex items-center gap-4">
                    <span class="material-symbols-outlined text-4xl">call</span>
                    Call the Studio
                </a>
                <a href="mailto:<?php echo strip_tags($content['cta_email']); ?>" class="px-12 py-6 bg-purple-900/50 backdrop-blur-md border border-white/30 text-white text-2xl font-black rounded-2xl hover:bg-purple-900/70 transition-all flex items-center gap-4">
                    <span class="material-symbols-outlined text-4xl">mail</span>
                    Send a Message
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Info Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-800/50">
        <div class="container mx-auto px-4">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div class="glass p-12 rounded-[3rem] text-center">
                        <div class="w-20 h-20 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mx-auto mb-8">
                            <span class="material-symbols-outlined text-4xl text-purple-600">location_on</span>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-4">Location</h3>
                        <p class="text-xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($content['location_text']); ?></p>
                    </div>
                    <div class="glass p-12 rounded-[3rem] text-center">
                        <div class="w-20 h-20 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center mx-auto mb-8">
                            <span class="material-symbols-outlined text-4xl text-pink-600">phone_in_talk</span>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-4">Contact</h3>
                        <p class="text-xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($content['cta_phone']); ?><br><?php echo strip_tags($content['cta_email']); ?></p>
                    </div>
                    <div class="glass p-12 rounded-[3rem] text-center">
                        <div class="w-20 h-20 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center mx-auto mb-8">
                            <span class="material-symbols-outlined text-4xl text-orange-600">share</span>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-4">Follow Us</h3>
                        <div class="flex justify-center gap-6 mt-4">
                            <a href="<?php echo strip_tags($content['facebook_url']); ?>" class="text-gray-600 dark:text-gray-400 hover:text-purple-600 transition-colors">
                                <i class="fab fa-facebook text-3xl"></i>
                            </a>
                            <a href="<?php echo strip_tags($content['twitter_url']); ?>" class="text-gray-600 dark:text-gray-400 hover:text-purple-600 transition-colors">
                                <i class="fab fa-twitter text-3xl"></i>
                            </a>
                            <a href="<?php echo strip_tags($content['instagram_url']); ?>" class="text-gray-600 dark:text-gray-400 hover:text-purple-600 transition-colors">
                                <i class="fab fa-instagram text-3xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>