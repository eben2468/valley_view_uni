<?php
$page_title = "Strategic Planning 2023 Gallery - Valley View University";
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
    .strategic-card {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .strategic-card:hover {
        transform: translateY(-10px);
    }
    .strategic-card img {
        transition: transform 0.5s ease;
    }
    .strategic-card:hover img {
        transform: scale(1.1);
    }
    .strategic-overlay {
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
            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&q=80&w=1920" 
                 alt="Strategic Planning 2023" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400">Strategic Planning 2023</span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    Key Officers <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4">Retreat Gallery</span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "Shaping the Future: Strategic Planning for Excellence and Innovation at Valley View University"
                </p>
            </div>
        </div>
    </section>

    <!-- Introduction Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-6xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6">Strategic Planning Retreat 2023</h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed max-w-4xl mx-auto">
                    Valley View University held a comprehensive Key Officers' Retreat at the Techiman Campus, focusing on strategic planning for institutional development, innovation, and excellence across five key pillars.
                </p>
            </div>
        </div>
    </section>

    <!-- Key Focus Areas -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6">Strategic Focus Areas</h2>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">Five pillars guiding VVU's path to excellence and growth.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-8">
                <!-- Focus Area 1 -->
                <div class="strategic-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-xl border-t-8 border-blue-600 hover:shadow-2xl">
                    <div class="w-24 h-24 rounded-3xl bg-blue-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">church</span>
                    </div>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-6">Spiritual Life & Development</h3>
                    <p class="text-2xl text-gray-700 dark:text-gray-300 leading-relaxed font-medium">
                        Strengthening faith-based values and spiritual growth throughout the university community.
                    </p>
                </div>

                <!-- Focus Area 2 -->
                <div class="strategic-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-xl border-t-8 border-green-600 hover:shadow-2xl">
                    <div class="w-24 h-24 rounded-3xl bg-green-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">business_center</span>
                    </div>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-6">Operational & Administrative</h3>
                    <p class="text-2xl text-gray-700 dark:text-gray-300 leading-relaxed font-medium">
                        Enhancing efficiency through financial decentralization and innovative operational strategies.
                    </p>
                </div>

                <!-- Focus Area 3 -->
                <div class="strategic-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-xl border-t-8 border-purple-600 hover:shadow-2xl">
                    <div class="w-24 h-24 rounded-3xl bg-purple-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">school</span>
                    </div>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-6">Academic & Research</h3>
                    <p class="text-2xl text-gray-700 dark:text-gray-300 leading-relaxed font-medium">
                        Expanding academic programs and fostering research excellence across all faculties.
                    </p>
                </div>

                <!-- Focus Area 4 -->
                <div class="strategic-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-xl border-t-8 border-yellow-600 hover:shadow-2xl">
                    <div class="w-24 h-24 rounded-3xl bg-yellow-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">account_balance</span>
                    </div>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-6">Financial Sustainability</h3>
                    <p class="text-2xl text-gray-700 dark:text-gray-300 leading-relaxed font-medium">
                        Implementing revenue-sharing models and sustainable financial planning for growth.
                    </p>
                </div>

                <!-- Focus Area 5 -->
                <div class="strategic-card group p-10 bg-white dark:bg-gray-900 rounded-3xl shadow-xl border-t-8 border-red-600 hover:shadow-2xl">
                    <div class="w-24 h-24 rounded-3xl bg-red-600 flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">apartment</span>
                    </div>
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-6">Infrastructure Development</h3>
                    <p class="text-2xl text-gray-700 dark:text-gray-300 leading-relaxed font-medium">
                        Building world-class facilities to support academic excellence and student life.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Photo Gallery Grid -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6">Retreat Moments</h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">Capturing key moments from our strategic planning sessions.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Gallery Item 1 -->
                <div class="strategic-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&q=80&w=800" 
                             alt="Cross Section of Retreat Participants" class="w-full h-full object-cover">
                        <div class="strategic-overlay">
                            <h3 class="text-2xl font-black text-white mb-2">Cross Section of Participants</h3>
                            <p class="text-base text-gray-200">Key officers and leaders gathered for strategic planning</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 2 -->
                <div class="strategic-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&q=80&w=800" 
                             alt="Group Discussions" class="w-full h-full object-cover">
                        <div class="strategic-overlay">
                            <h3 class="text-2xl font-black text-white mb-2">Group Discussions</h3>
                            <p class="text-base text-gray-200">Collaborative brainstorming and planning sessions</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 3 -->
                <div class="strategic-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1560439514-4e9645039924?auto=format&fit=crop&q=80&w=800" 
                             alt="Presentations" class="w-full h-full object-cover">
                        <div class="strategic-overlay">
                            <h3 class="text-2xl font-black text-white mb-2">Presentations</h3>
                            <p class="text-base text-gray-200">Key strategic initiatives and implementation plans</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 4 -->
                <div class="strategic-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1515187029135-18ee286d815b?auto=format&fit=crop&q=80&w=800" 
                             alt="Facilitators" class="w-full h-full object-cover">
                        <div class="strategic-overlay">
                            <h3 class="text-2xl font-black text-white mb-2">Facilitators</h3>
                            <p class="text-base text-gray-200">Expert guidance and leadership development</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 5 -->
                <div class="strategic-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&q=80&w=800" 
                             alt="Exercise Time" class="w-full h-full object-cover">
                        <div class="strategic-overlay">
                            <h3 class="text-2xl font-black text-white mb-2">Exercise Time</h3>
                            <p class="text-base text-gray-200">Wellness and team building activities</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 6 -->
                <div class="strategic-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&q=80&w=800" 
                             alt="Inspection at Chapel" class="w-full h-full object-cover">
                        <div class="strategic-overlay">
                            <h3 class="text-2xl font-black text-white mb-2">Chapel Building Inspection</h3>
                            <p class="text-base text-gray-200">Reviewing infrastructure development projects</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 7 -->
                <div class="strategic-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&q=80&w=800" 
                             alt="Strategic Sessions" class="w-full h-full object-cover">
                        <div class="strategic-overlay">
                            <h3 class="text-2xl font-black text-white mb-2">Strategic Planning Sessions</h3>
                            <p class="text-base text-gray-200">In-depth analysis and future visioning</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 8 -->
                <div class="strategic-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=800" 
                             alt="Team Collaboration" class="w-full h-full object-cover">
                        <div class="strategic-overlay">
                            <h3 class="text-2xl font-black text-white mb-2">Team Collaboration</h3>
                            <p class="text-base text-gray-200">Working together for institutional excellence</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 9 -->
                <div class="strategic-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1573164713714-d95e436ab8d6?auto=format&fit=crop&q=80&w=800" 
                             alt="Leadership Forum" class="w-full h-full object-cover">
                        <div class="strategic-overlay">
                            <h3 class="text-2xl font-black text-white mb-2">Leadership Forum</h3>
                            <p class="text-base text-gray-200">Senior officials and church leaders collaborate</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 10 -->
                <div class="strategic-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1542744173-8e7e53415bb0?auto=format&fit=crop&q=80&w=800" 
                             alt="Implementation Planning" class="w-full h-full object-cover">
                        <div class="strategic-overlay">
                            <h3 class="text-2xl font-black text-white mb-2">Implementation Planning</h3>
                            <p class="text-base text-gray-200">Developing actionable strategies and timelines</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 11 -->
                <div class="strategic-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&q=80&w=800" 
                             alt="Campus Tour" class="w-full h-full object-cover">
                        <div class="strategic-overlay">
                            <h3 class="text-2xl font-black text-white mb-2">Techiman Campus Tour</h3>
                            <p class="text-base text-gray-200">Exploring campus facilities and development sites</p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Item 12 -->
                <div class="strategic-card group bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-xl">
                    <div class="relative aspect-square">
                        <img src="https://images.unsplash.com/photo-1556761175-4b46a572b786?auto=format&fit=crop&q=80&w=800" 
                             alt="Closing Ceremony" class="w-full h-full object-cover">
                        <div class="strategic-overlay">
                            <h3 class="text-2xl font-black text-white mb-2">Closing Ceremony</h3>
                            <p class="text-base text-gray-200">Commitment to excellence and innovation</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Resolutions Section -->
    <section class="py-24 bg-blue-900 overflow-hidden relative">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-4xl mx-auto text-center mb-16">
                <h2 class="text-5xl sm:text-6xl font-black text-white mb-6">Key Resolutions</h2>
                <p class="text-2xl text-blue-100 font-medium leading-relaxed">Strategic decisions shaping VVU's future direction and growth.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 max-w-6xl mx-auto">
                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-10 border border-white/20">
                    <div class="flex items-start gap-6 mb-6">
                        <div class="w-16 h-16 rounded-2xl bg-yellow-400 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-3xl text-blue-900">account_balance_wallet</span>
                        </div>
                        <div>
                            <h3 class="text-3xl font-black text-white mb-4">Financial Decentralization</h3>
                            <p class="text-xl text-blue-100 leading-relaxed">Implementation by April 2025 to enhance operational efficiency and accountability across all campuses.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-10 border border-white/20">
                    <div class="flex items-start gap-6 mb-6">
                        <div class="w-16 h-16 rounded-2xl bg-yellow-400 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-3xl text-blue-900">groups</span>
                        </div>
                        <div>
                            <h3 class="text-3xl font-black text-white mb-4">Task Force Formation</h3>
                            <p class="text-xl text-blue-100 leading-relaxed">Establishment of specialized teams to analyze remuneration models and optimize resource allocation.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-10 border border-white/20">
                    <div class="flex items-start gap-6 mb-6">
                        <div class="w-16 h-16 rounded-2xl bg-yellow-400 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-3xl text-blue-900">construction</span>
                        </div>
                        <div>
                            <h3 class="text-3xl font-black text-white mb-4">Revenue-Sharing Formula</h3>
                            <p class="text-xl text-blue-100 leading-relaxed">Adoption of sustainable models for infrastructure maintenance and development funding.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-10 border border-white/20">
                    <div class="flex items-start gap-6 mb-6">
                        <div class="w-16 h-16 rounded-2xl bg-yellow-400 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-3xl text-blue-900">trending_up</span>
                        </div>
                        <div>
                            <h3 class="text-3xl font-black text-white mb-4">Program Expansion</h3>
                            <p class="text-xl text-blue-100 leading-relaxed">Strategic expansion of academic programs and campus specialization to enhance competitive advantage.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-24 overflow-hidden bg-gray-50 dark:bg-gray-950">
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-gray-900 dark:text-white mb-8 leading-tight tracking-tight">
                    Building Tomorrow, Today
                </h2>
                <p class="text-2xl sm:text-3xl md:text-4xl text-gray-600 dark:text-gray-400 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    Join us in our journey towards academic excellence, institutional sustainability, and innovative leadership in higher education.
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="strategic_plan.php" class="px-10 py-5 bg-blue-600 hover:bg-blue-700 text-white text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl text-white">description</span>
                        View Strategic Plan
                    </a>
                    <a href="about_us.php" class="px-10 py-5 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-white text-xl font-bold rounded-2xl transition-all border-2 border-gray-200 dark:border-gray-700 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl text-white">info</span>
                        Learn More About VVU
                    </a>
                </div>
                
                <div class="mt-20 grid grid-cols-1 sm:grid-cols-3 gap-12 border-t border-gray-200 dark:border-gray-800 pt-16">
                    <div>
                        <div class="text-6xl font-black text-blue-600 mb-2">5</div>
                        <div class="text-gray-600 dark:text-gray-400 uppercase tracking-widest text-2xl font-black">Strategic Pillars</div>
                    </div>
                    <div>
                        <div class="text-6xl font-black text-blue-600 mb-2">2025</div>
                        <div class="text-gray-600 dark:text-gray-400 uppercase tracking-widest text-2xl font-black">Implementation Year</div>
                    </div>
                    <div>
                        <div class="text-6xl font-black text-blue-600 mb-2">100%</div>
                        <div class="text-gray-600 dark:text-gray-400 uppercase tracking-widest text-2xl font-black">Commitment</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
