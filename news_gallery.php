<?php
$page_title = "News Gallery - Valley View University";
$active_page = "stories";
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
    .news-card {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .news-card:hover {
        transform: translateY(-10px);
    }
    .news-card img {
        transition: transform 0.5s ease;
    }
    .news-card:hover img {
        transform: scale(1.1);
    }
    .news-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.6) 50%, transparent 100%);
        padding: 2rem;
        transform: translateY(0);
        transition: all 0.3s ease;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&q=80&w=1920" 
                 alt="VVU News Gallery" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400">News & Updates</span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    News Gallery <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4">Stories That Matter</span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "Capturing the moments that shape our university's journey and celebrate our achievements"
                </p>
            </div>
        </div>
    </section>

    <!-- Introduction Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-6xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6">Latest News In Pictures</h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed max-w-4xl mx-auto">
                    Stay informed with visual stories of achievements, partnerships, and milestones at Valley View University. From academic excellence to community impact, witness our journey through compelling images.
                </p>
            </div>
        </div>
    </section>

    <!-- News Categories -->
    <section class="py-16 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="flex flex-wrap justify-center gap-4 mb-16">
                <button class="px-8 py-4 bg-blue-600 text-white text-xl font-bold rounded-full hover:bg-blue-700 transition-all shadow-lg">
                    <span class="material-symbols-outlined text-white align-middle mr-2">grid_view</span>
                    All News
                </button>
                <button class="px-8 py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-xl font-bold rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-all shadow-lg border-2 border-gray-200 dark:border-gray-700">
                    <span class="material-symbols-outlined text-white align-middle mr-2">emoji_events</span>
                    Achievements
                </button>
                <button class="px-8 py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-xl font-bold rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-all shadow-lg border-2 border-gray-200 dark:border-gray-700">
                    <span class="material-symbols-outlined text-white align-middle mr-2">handshake</span>
                    Partnerships
                </button>
                <button class="px-8 py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-xl font-bold rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-all shadow-lg border-2 border-gray-200 dark:border-gray-700">
                    <span class="material-symbols-outlined text-white align-middle mr-2">diversity_3</span>
                    Community
                </button>
                <button class="px-8 py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-xl font-bold rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-all shadow-lg border-2 border-gray-200 dark:border-gray-700">
                    <span class="material-symbols-outlined text-white align-middle mr-2">school</span>
                    Academic
                </button>
            </div>
        </div>
    </section>

    <!-- Main News Gallery Grid -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- News Item 1 -->
                <div class="news-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&q=80&w=800" 
                             alt="Partnership News" class="w-full h-full object-cover">
                        <div class="news-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl text-white">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">May 27, 2024</span>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-2">VVU Partners with Georgia State University</h3>
                            <p class="text-base text-gray-200">Strengthening nursing education through collaboration</p>
                        </div>
                    </div>
                </div>

                <!-- News Item 2 -->
                <div class="news-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&q=80&w=800" 
                             alt="IRB Inauguration" class="w-full h-full object-cover">
                        <div class="news-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl text-white">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">May 27, 2024</span>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-2">VVU Inaugurates Institutional Review Board</h3>
                            <p class="text-base text-gray-200">Enhancing research ethics and governance</p>
                        </div>
                    </div>
                </div>

                <!-- News Item 3 -->
                <div class="news-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&q=80&w=800" 
                             alt="Technology News" class="w-full h-full object-cover">
                        <div class="news-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl text-white">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">May 14, 2024</span>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-2">TME Education's Cutting-Edge Technology</h3>
                            <p class="text-base text-gray-200">Empowering VVU students with innovation</p>
                        </div>
                    </div>
                </div>

                <!-- News Item 4 -->
                <div class="news-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1571260899304-425eee4c7efc?auto=format&fit=crop&q=80&w=800" 
                             alt="Music Program" class="w-full h-full object-cover">
                        <div class="news-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl text-white">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">November 15, 2023</span>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-2">VVU Collaborates With Ghana Armed Forces</h3>
                            <p class="text-base text-gray-200">Six-month music training program launched</p>
                        </div>
                    </div>
                </div>

                <!-- News Item 5 -->
                <div class="news-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&q=80&w=800" 
                             alt="Presidential Honor" class="w-full h-full object-cover">
                        <div class="news-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl text-white">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">July 16, 2023</span>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-2">President Akufo-Addo Honored at VVU</h3>
                            <p class="text-base text-gray-200">Special congregation recognizes distinguished leaders</p>
                        </div>
                    </div>
                </div>

                <!-- News Item 6 -->
                <div class="news-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1560264280-88b68371db39?auto=format&fit=crop&q=80&w=800" 
                             alt="Book Visit" class="w-full h-full object-cover">
                        <div class="news-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl text-white">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">June 09, 2023</span>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-2">Dr. and Mrs. Adu-Febiri Book Visits VVU</h3>
                            <p class="text-base text-gray-200">Academic excellence and literary engagement</p>
                        </div>
                    </div>
                </div>

                <!-- News Item 7 -->
                <div class="news-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1593113646773-028c64a8f1b8?auto=format&fit=crop&q=80&w=800" 
                             alt="Aquaculture Partnership" class="w-full h-full object-cover">
                        <div class="news-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl text-white">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">June 07, 2023</span>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-2">VVU Promotes Aquaculture in Ghana</h3>
                            <p class="text-base text-gray-200">Partnership with Arkansas & Delaware universities</p>
                        </div>
                    </div>
                </div>

                <!-- News Item 8 -->
                <div class="news-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1491438590914-bc09fcaaf77a?auto=format&fit=crop&q=80&w=800" 
                             alt="Farewell" class="w-full h-full object-cover">
                        <div class="news-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl text-white">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">June 02, 2023</span>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-2">Elder Pablo Rivas Bids Farewell to VVU</h3>
                            <p class="text-base text-gray-200">Celebrating years of missionary service</p>
                        </div>
                    </div>
                </div>

                <!-- News Item 9 -->
                <div class="news-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=800" 
                             alt="Staff Orientation" class="w-full h-full object-cover">
                        <div class="news-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl text-white">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">May 26, 2023</span>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-2">2-Day Orientation for Staff</h3>
                            <p class="text-base text-gray-200">Professional development and team building</p>
                        </div>
                    </div>
                </div>

                <!-- News Item 10 -->
                <div class="news-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1530836369250-ef72a3f5cda8?auto=format&fit=crop&q=80&w=800" 
                             alt="Garden Harvest" class="w-full h-full object-cover">
                        <div class="news-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl text-white">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">May 26, 2023</span>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-2">Garden of Renown Makes First Harvest</h3>
                            <p class="text-base text-gray-200">Sustainable agriculture initiative bears fruit</p>
                        </div>
                    </div>
                </div>

                <!-- News Item 11 -->
                <div class="news-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1556761175-4b46a572b786?auto=format&fit=crop&q=80&w=800" 
                             alt="MOU Signing" class="w-full h-full object-cover">
                        <div class="news-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl text-white">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">May 17, 2023</span>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-2">VVU Signs MOU with CSIR</h3>
                            <p class="text-base text-gray-200">Training 1000 in composite flour technology</p>
                        </div>
                    </div>
                </div>

                <!-- News Item 12 -->
                <div class="news-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&q=80&w=800" 
                             alt="Staff Orientation" class="w-full h-full object-cover">
                        <div class="news-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl text-white">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">May 05, 2023</span>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-2">3-Day Staff Orientation Program</h3>
                            <p class="text-base text-gray-200">Empowering staff and reassigned officers</p>
                        </div>
                    </div>
                </div>

                <!-- News Item 13 -->
                <div class="news-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1515187029135-18ee286d815b?auto=format&fit=crop&q=80&w=800" 
                             alt="Faith Journey" class="w-full h-full object-cover">
                        <div class="news-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl text-white">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">May 04, 2023</span>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-2">University Moved-In By Faith</h3>
                            <p class="text-base text-gray-200">Celebrating spiritual foundations and growth</p>
                        </div>
                    </div>
                </div>

                <!-- News Item 14 -->
                <div class="news-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800" 
                             alt="Career Summit" class="w-full h-full object-cover">
                        <div class="news-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl text-white">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">April 28, 2023</span>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-2">Career & Capacity Development Summit</h3>
                            <p class="text-base text-gray-200">Preparing students for professional success</p>
                        </div>
                    </div>
                </div>

                <!-- News Item 15 -->
                <div class="news-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&q=80&w=800" 
                             alt="Jubilee House Visit" class="w-full h-full object-cover">
                        <div class="news-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl text-white">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">April 07, 2023</span>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-2">VVU Officers Visit Jubilee House</h3>
                            <p class="text-base text-gray-200">Courtesy call strengthens national ties</p>
                        </div>
                    </div>
                </div>

                <!-- News Item 16 -->
                <div class="news-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&q=80&w=800" 
                             alt="Quiz Competition" class="w-full h-full object-cover">
                        <div class="news-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl text-white">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">April 10, 2022</span>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-2">VVU Wins Africa Smart-B Quiz</h3>
                            <p class="text-base text-gray-200">Maiden victory in continental competition</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Load More Button -->
            <div class="text-center mt-16">
                <button class="px-12 py-6 bg-blue-600 hover:bg-blue-700 text-white text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl inline-flex items-center gap-4">
                    <span class="material-symbols-outlined text-3xl text-white">download</span>
                    Load More News
                </button>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-24 bg-blue-900 overflow-hidden relative">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-4xl mx-auto text-center mb-16">
                <h2 class="text-5xl sm:text-6xl font-black text-white mb-6">News Highlights</h2>
                <p class="text-2xl text-blue-100 font-medium leading-relaxed">Documenting milestones and achievements at Valley View University.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <div class="text-center">
                    <div class="w-24 h-24 rounded-3xl bg-yellow-400 flex items-center justify-center mx-auto mb-6 shadow-2xl">
                        <span class="material-symbols-outlined text-5xl text-blue-900">newspaper</span>
                    </div>
                    <div class="text-6xl font-black text-yellow-400 mb-2">500+</div>
                    <div class="text-blue-100 uppercase tracking-widest text-xl font-black">News Stories</div>
                </div>

                <div class="text-center">
                    <div class="w-24 h-24 rounded-3xl bg-yellow-400 flex items-center justify-center mx-auto mb-6 shadow-2xl">
                        <span class="material-symbols-outlined text-5xl text-blue-900">handshake</span>
                    </div>
                    <div class="text-6xl font-black text-yellow-400 mb-2">50+</div>
                    <div class="text-blue-100 uppercase tracking-widest text-xl font-black">Partnerships</div>
                </div>

                <div class="text-center">
                    <div class="w-24 h-24 rounded-3xl bg-yellow-400 flex items-center justify-center mx-auto mb-6 shadow-2xl">
                        <span class="material-symbols-outlined text-5xl text-blue-900">emoji_events</span>
                    </div>
                    <div class="text-6xl font-black text-yellow-400 mb-2">100+</div>
                    <div class="text-blue-100 uppercase tracking-widest text-xl font-black">Achievements</div>
                </div>

                <div class="text-center">
                    <div class="w-24 h-24 rounded-3xl bg-yellow-400 flex items-center justify-center mx-auto mb-6 shadow-2xl">
                        <span class="material-symbols-outlined text-5xl text-blue-900">groups</span>
                    </div>
                    <div class="text-6xl font-black text-yellow-400 mb-2">Global</div>
                    <div class="text-blue-100 uppercase tracking-widest text-xl font-black">Impact</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-24 overflow-hidden bg-white dark:bg-gray-900">
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-gray-900 dark:text-white mb-8 leading-tight tracking-tight">
                    Stay Updated With VVU News
                </h2>
                <p class="text-2xl sm:text-3xl md:text-4xl text-gray-600 dark:text-gray-400 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    Subscribe to our newsletter and never miss important updates, achievements, and stories from Valley View University.
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="news_&_events.php" class="px-10 py-5 bg-blue-600 hover:bg-blue-700 text-white text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl text-white">article</span>
                        Read Full Articles
                    </a>
                    <a href="gallery.php" class="px-10 py-5 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-white text-xl font-bold rounded-2xl transition-all border-2 border-gray-200 dark:border-gray-700 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl text-white">photo_library</span>
                        View All Galleries
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
