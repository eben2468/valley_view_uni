<?php
$page_title = "Gallery - Valley View University";
$active_page = "student-life";
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
    .gallery-card {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .gallery-card:hover {
        transform: translateY(-10px);
    }
    .gallery-card img {
        transition: transform 0.5s ease;
    }
    .gallery-card:hover img {
        transform: scale(1.1);
    }
    .gallery-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.4) 50%, transparent 100%);
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
            <img src="https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&q=80&w=1920" 
                 alt="VVU Gallery" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400">Photo Gallery</span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    VVU Gallery <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4">Life In Pictures</span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "Capturing the vibrant moments and memorable experiences that define Valley View University"
                </p>
            </div>
        </div>
    </section>

    <!-- Introduction Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-6xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6">Experience VVU Through Our Lens</h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed max-w-4xl mx-auto">
                    Have a look at all the good things happening in Valley View University with our comprehensive photo report. From graduation ceremonies to campus events, witness the vibrant life at VVU.
                </p>
            </div>
        </div>
    </section>

    <!-- Gallery Categories -->
    <section class="py-16 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="flex flex-wrap justify-center gap-4 mb-16">
                <button class="px-8 py-4 bg-blue-600 text-white text-xl font-bold rounded-full hover:bg-blue-700 transition-all shadow-lg">
                    <span class="material-symbols-outlined text-white align-middle mr-2">grid_view</span>
                    All Events
                </button>
                <button class="px-8 py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-xl font-bold rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-all shadow-lg border-2 border-gray-200 dark:border-gray-700">
                    <span class="material-symbols-outlined text-white align-middle mr-2">school</span>
                    Graduation
                </button>
                <button class="px-8 py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-xl font-bold rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-all shadow-lg border-2 border-gray-200 dark:border-gray-700">
                    <span class="material-symbols-outlined text-white align-middle mr-2">sports</span>
                    Sports
                </button>
                <button class="px-8 py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-xl font-bold rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-all shadow-lg border-2 border-gray-200 dark:border-gray-700">
                    <span class="material-symbols-outlined text-white align-middle mr-2">celebration</span>
                    Ceremonies
                </button>
                <button class="px-8 py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-xl font-bold rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-all shadow-lg border-2 border-gray-200 dark:border-gray-700">
                    <span class="material-symbols-outlined text-white align-middle mr-2">diversity_3</span>
                    Campus Life
                </button>
            </div>
        </div>
    </section>

    <!-- Main Gallery Grid -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Gallery Item 1 -->
                <div class="gallery-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&q=80&w=800" 
                             alt="Graduation Ceremony" class="w-full h-full object-cover">
                        <div class="gallery-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">December 17, 2023</span>
                            </div>
                            <h3 class="text-3xl font-black text-white mb-2">32nd Graduation Ceremony</h3>
                            <p class="text-lg text-gray-200">Celebrating academic excellence and achievement</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 2 -->
                <div class="gallery-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1571260899304-425eee4c7efc?auto=format&fit=crop&q=80&w=800" 
                             alt="Sports Day" class="w-full h-full object-cover">
                        <div class="gallery-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">December 01, 2023</span>
                            </div>
                            <h3 class="text-3xl font-black text-white mb-2">VVU Welfare Sports Day</h3>
                            <p class="text-lg text-gray-200">Staff wellness and team building activities</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 3 -->
                <div class="gallery-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&q=80&w=800" 
                             alt="Nursing Pinning" class="w-full h-full object-cover">
                        <div class="gallery-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">March 25, 2023</span>
                            </div>
                            <h3 class="text-3xl font-black text-white mb-2">Nursing Pinning Ceremony</h3>
                            <p class="text-lg text-gray-200">Honoring our future healthcare professionals</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 4 -->
                <div class="gallery-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&q=80&w=800" 
                             alt="Alumni Homecoming" class="w-full h-full object-cover">
                        <div class="gallery-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">June 26, 2023</span>
                            </div>
                            <h3 class="text-3xl font-black text-white mb-2">Alumni Homecoming</h3>
                            <p class="text-lg text-gray-200">Reconnecting with our VVU family</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 5 -->
                <div class="gallery-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&q=80&w=800" 
                             alt="Induction Ceremony" class="w-full h-full object-cover">
                        <div class="gallery-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">March 26, 2023</span>
                            </div>
                            <h3 class="text-3xl font-black text-white mb-2">Induction Ceremony</h3>
                            <p class="text-lg text-gray-200">Welcoming new professionals into their fields</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 6 -->
                <div class="gallery-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800" 
                             alt="Campus Event" class="w-full h-full object-cover">
                        <div class="gallery-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">April 07, 2023</span>
                            </div>
                            <h3 class="text-3xl font-black text-white mb-2">Wednesday Chapel Service</h3>
                            <p class="text-lg text-gray-200">Spiritual enrichment and community worship</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 7 -->
                <div class="gallery-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&q=80&w=800" 
                             alt="Award Ceremony" class="w-full h-full object-cover">
                        <div class="gallery-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">April 21, 2023</span>
                            </div>
                            <h3 class="text-3xl font-black text-white mb-2">VVU Alumnus Recognition</h3>
                            <p class="text-lg text-gray-200">Celebrating outstanding alumni achievements</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 8 -->
                <div class="gallery-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&q=80&w=800" 
                             alt="Career Summit" class="w-full h-full object-cover">
                        <div class="gallery-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">April 28, 2023</span>
                            </div>
                            <h3 class="text-3xl font-black text-white mb-2">Career Development Summit</h3>
                            <p class="text-lg text-gray-200">Empowering students for future success</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 9 -->
                <div class="gallery-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1543269865-cbf427effbad?auto=format&fit=crop&q=80&w=800" 
                             alt="Campus Tour" class="w-full h-full object-cover">
                        <div class="gallery-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">May 17, 2023</span>
                            </div>
                            <h3 class="text-3xl font-black text-white mb-2">MOU Signing Ceremony</h3>
                            <p class="text-lg text-gray-200">Strategic partnerships for innovation</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 10 -->
                <div class="gallery-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=800" 
                             alt="Orientation" class="w-full h-full object-cover">
                        <div class="gallery-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">May 05, 2023</span>
                            </div>
                            <h3 class="text-3xl font-black text-white mb-2">Staff Orientation Program</h3>
                            <p class="text-lg text-gray-200">Building a stronger university community</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 11 -->
                <div class="gallery-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?auto=format&fit=crop&q=80&w=800" 
                             alt="Garden Harvest" class="w-full h-full object-cover">
                        <div class="gallery-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">May 26, 2023</span>
                            </div>
                            <h3 class="text-3xl font-black text-white mb-2">Garden of Renown Harvest</h3>
                            <p class="text-lg text-gray-200">Sustainable agriculture initiatives at VVU</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 12 -->
                <div class="gallery-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&q=80&w=800" 
                             alt="Strategic Planning" class="w-full h-full object-cover">
                        <div class="gallery-overlay">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-yellow-400 text-2xl">calendar_month</span>
                                <span class="text-yellow-400 font-bold text-lg">February 04, 2023</span>
                            </div>
                            <h3 class="text-3xl font-black text-white mb-2">Strategic Planning Retreat</h3>
                            <p class="text-lg text-gray-200">Shaping the future of VVU together</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Load More Button -->
            <div class="text-center mt-16">
                <button class="px-12 py-6 bg-blue-600 hover:bg-blue-700 text-white text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl inline-flex items-center gap-4">
                    <span class="material-symbols-outlined text-3xl text-white">download</span>
                    Load More Photos
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
                <h2 class="text-5xl sm:text-6xl font-black text-white mb-6">Gallery Highlights</h2>
                <p class="text-2xl text-blue-100 font-medium leading-relaxed">Documenting the journey of excellence at Valley View University.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <div class="text-center">
                    <div class="w-24 h-24 rounded-3xl bg-yellow-400 flex items-center justify-center mx-auto mb-6 shadow-2xl">
                        <span class="material-symbols-outlined text-5xl text-blue-900">photo_library</span>
                    </div>
                    <div class="text-6xl font-black text-yellow-400 mb-2">1000+</div>
                    <div class="text-blue-100 uppercase tracking-widest text-xl font-black">Photos</div>
                </div>

                <div class="text-center">
                    <div class="w-24 h-24 rounded-3xl bg-yellow-400 flex items-center justify-center mx-auto mb-6 shadow-2xl">
                        <span class="material-symbols-outlined text-5xl text-blue-900">celebration</span>
                    </div>
                    <div class="text-6xl font-black text-yellow-400 mb-2">100+</div>
                    <div class="text-blue-100 uppercase tracking-widest text-xl font-black">Events</div>
                </div>

                <div class="text-center">
                    <div class="w-24 h-24 rounded-3xl bg-yellow-400 flex items-center justify-center mx-auto mb-6 shadow-2xl">
                        <span class="material-symbols-outlined text-5xl text-blue-900">groups</span>
                    </div>
                    <div class="text-6xl font-black text-yellow-400 mb-2">50K+</div>
                    <div class="text-blue-100 uppercase tracking-widest text-xl font-black">Students Featured</div>
                </div>

                <div class="text-center">
                    <div class="w-24 h-24 rounded-3xl bg-yellow-400 flex items-center justify-center mx-auto mb-6 shadow-2xl">
                        <span class="material-symbols-outlined text-5xl text-blue-900">calendar_today</span>
                    </div>
                    <div class="text-6xl font-black text-yellow-400 mb-2">10+</div>
                    <div class="text-blue-100 uppercase tracking-widest text-xl font-black">Years Documented</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-24 overflow-hidden bg-white dark:bg-gray-900">
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-gray-900 dark:text-white mb-8 leading-tight tracking-tight">
                    Want to See More?
                </h2>
                <p class="text-2xl sm:text-3xl md:text-4xl text-gray-600 dark:text-gray-400 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    Follow us on social media for daily updates and behind-the-scenes moments from campus life at VVU.
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="#" class="px-10 py-5 bg-blue-600 hover:bg-blue-700 text-white text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl text-white">photo_camera</span>
                        View Full Gallery
                    </a>
                    <a href="news_&_events.php" class="px-10 py-5 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-white text-xl font-bold rounded-2xl transition-all border-2 border-gray-200 dark:border-gray-700 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl text-white">event</span>
                        Upcoming Events
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
