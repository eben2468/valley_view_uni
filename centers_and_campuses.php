<?php
$page_title = "Centres and Campuses - Valley View University";
$active_page = "about";
require_once 'includes/db_connect.php';

include 'includes/header.php';
?>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
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
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(37, 99, 235, 0.3); }
        50% { box-shadow: 0 0 40px rgba(37, 99, 235, 0.6); }
    }
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-50px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(50px); }
        to { opacity: 1; transform: translateX(0); }
    }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.8s ease-out forwards; }
    .animate-float { animation: float 4s ease-in-out infinite; }
    .animate-pulse-glow { animation: pulse-glow 3s ease-in-out infinite; }
    .animate-slideInLeft { animation: slideInLeft 0.8s ease-out forwards; }
    .animate-slideInRight { animation: slideInRight 0.8s ease-out forwards; }
    
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
    
    .campus-card {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        overflow: hidden;
    }
    .campus-card:hover {
        transform: translateY(-15px) scale(1.02);
    }
    .campus-card:hover .campus-image {
        transform: scale(1.1);
    }
    .campus-card:hover .campus-overlay {
        opacity: 0.4;
    }
    .campus-image {
        transition: transform 0.6s ease;
    }
    .campus-overlay {
        transition: opacity 0.4s ease;
    }
    
    .feature-icon {
        transition: all 0.3s ease;
    }
    .feature-card:hover .feature-icon {
        transform: scale(1.15) rotate(5deg);
    }
    
    .map-container {
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .programme-badge {
        transition: all 0.3s ease;
    }
    .programme-badge:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section (Same design as core_values.php) -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1562774053-701939374585?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" 
                 alt="VVU Campuses" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400">Discover Our Locations</span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    Centres & <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4">Campuses</span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "Bringing quality education closer to you through our network of campuses and learning centres across Ghana"
                </p>
            </div>
        </div>
    </section>

    <!-- Introduction Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-5xl mx-auto text-center mb-16">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                    <span class="material-symbols-outlined text-2xl">location_on</span>
                    <span class="text-xl font-bold uppercase tracking-wider">Visit Our Locations</span>
                </div>
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-8">
                    You Can Visit Any of Our <span class="text-blue-600">Campuses</span> or <span class="text-yellow-500">Centres</span>
                </h2>
                <div class="h-2 w-40 bg-gradient-to-r from-blue-600 to-yellow-500 mx-auto rounded-full mb-10"></div>
                <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed max-w-4xl mx-auto">
                    Valley View University extends its reach beyond the main campus at Oyibi, offering accessible quality education through our strategically located campuses and centres throughout Ghana.
                </p>
            </div>
        </div>
    </section>

    <!-- Campuses Grid Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6">Our Campuses & Centres</h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">Strategically located to bring quality education closer to you.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <!-- Kumasi Campus -->
                <div class="campus-card glass rounded-3xl shadow-2xl overflow-hidden group">
                    <div class="relative h-72 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="Kumasi Campus" class="campus-image w-full h-full object-cover">
                        <div class="campus-overlay absolute inset-0 bg-gradient-to-t from-blue-900 via-blue-900/50 to-transparent opacity-70"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-8">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-4 py-1 bg-yellow-400 text-blue-900 font-bold rounded-full text-lg">Campus</span>
                                <span class="px-4 py-1 bg-white/20 backdrop-blur text-white font-semibold rounded-full text-lg">Est. 2004</span>
                            </div>
                            <h3 class="text-4xl md:text-5xl font-black text-white">Kumasi Campus</h3>
                        </div>
                    </div>
                    <div class="p-8 md:p-10">
                        <p class="text-xl md:text-2xl text-gray-700 dark:text-gray-300 mb-8 leading-relaxed font-medium">
                            You are warmly welcome to the Kumasi Extension Campus of Valley View University. In 2004, the University Administration set up a Study Centre at Amakom, Kumasi, Ghana. The Centre has since grown exponentially and now enjoys accreditation from both the NAB and AAA.
                        </p>
                        
                        <div class="grid grid-cols-2 gap-4 mb-8">
                            <div class="flex items-center gap-3 bg-blue-50 dark:bg-blue-900/30 p-4 rounded-2xl">
                                <span class="material-symbols-outlined text-blue-600 text-3xl">school</span>
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">Undergrad</span>
                            </div>
                            <div class="flex items-center gap-3 bg-green-50 dark:bg-green-900/30 p-4 rounded-2xl">
                                <span class="material-symbols-outlined text-green-600 text-3xl">workspace_premium</span>
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">Postgrad</span>
                            </div>
                            <div class="flex items-center gap-3 bg-purple-50 dark:bg-purple-900/30 p-4 rounded-2xl">
                                <span class="material-symbols-outlined text-purple-600 text-3xl">medical_services</span>
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">Nursing</span>
                            </div>
                            <div class="flex items-center gap-3 bg-yellow-50 dark:bg-yellow-900/30 p-4 rounded-2xl">
                                <span class="material-symbols-outlined text-yellow-600 text-3xl">business_center</span>
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">MBA</span>
                            </div>
                        </div>
                        
                        <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-2xl mb-6">
                            <div class="flex items-start gap-4">
                                <span class="material-symbols-outlined text-blue-600 text-3xl mt-1">location_on</span>
                                <div>
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Location & Contact</h4>
                                    <p class="text-lg text-gray-600 dark:text-gray-400">Oduom, Kumasi, Ghana</p>
                                    <p class="text-lg text-gray-600 dark:text-gray-400">P.O. Box UP 660</p>
                                    <p class="text-lg text-blue-600 dark:text-blue-400 font-semibold mt-2">📞 +233 26 581 8266</p>
                                </div>
                            </div>
                        </div>
                        
                        <a href="#" class="inline-flex items-center gap-3 px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg">
                            <span class="material-symbols-outlined text-2xl">arrow_forward</span>
                            Learn More
                        </a>
                    </div>
                </div>

                <!-- Tamale Centre -->
                <div class="campus-card glass rounded-3xl shadow-2xl overflow-hidden group">
                    <div class="relative h-72 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="Tamale Centre" class="campus-image w-full h-full object-cover">
                        <div class="campus-overlay absolute inset-0 bg-gradient-to-t from-green-900 via-green-900/50 to-transparent opacity-70"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-8">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-4 py-1 bg-green-400 text-green-900 font-bold rounded-full text-lg">Centre</span>
                                <span class="px-4 py-1 bg-white/20 backdrop-blur text-white font-semibold rounded-full text-lg">Northern Region</span>
                            </div>
                            <h3 class="text-4xl md:text-5xl font-black text-white">Tamale Centre</h3>
                        </div>
                    </div>
                    <div class="p-8 md:p-10">
                        <p class="text-xl md:text-2xl text-gray-700 dark:text-gray-300 mb-8 leading-relaxed font-medium">
                            The Tamale Centre brings Valley View University's quality education to the Northern Region of Ghana, offering accessible learning opportunities to students in the area with flexible study modes.
                        </p>
                        
                        <div class="grid grid-cols-2 gap-4 mb-8">
                            <div class="flex items-center gap-3 bg-green-50 dark:bg-green-900/30 p-4 rounded-2xl">
                                <span class="material-symbols-outlined text-green-600 text-3xl">schedule</span>
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">Flexible</span>
                            </div>
                            <div class="flex items-center gap-3 bg-blue-50 dark:bg-blue-900/30 p-4 rounded-2xl">
                                <span class="material-symbols-outlined text-blue-600 text-3xl">groups</span>
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">Community</span>
                            </div>
                            <div class="flex items-center gap-3 bg-purple-50 dark:bg-purple-900/30 p-4 rounded-2xl">
                                <span class="material-symbols-outlined text-purple-600 text-3xl">auto_stories</span>
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">Distance</span>
                            </div>
                            <div class="flex items-center gap-3 bg-orange-50 dark:bg-orange-900/30 p-4 rounded-2xl">
                                <span class="material-symbols-outlined text-orange-600 text-3xl">support_agent</span>
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">Support</span>
                            </div>
                        </div>
                        
                        <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-2xl mb-6">
                            <div class="flex items-start gap-4">
                                <span class="material-symbols-outlined text-green-600 text-3xl mt-1">location_on</span>
                                <div>
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Location & Contact</h4>
                                    <p class="text-lg text-gray-600 dark:text-gray-400">Tamale, Northern Region, Ghana</p>
                                    <p class="text-lg text-green-600 dark:text-green-400 font-semibold mt-2">📞 Contact Main Campus</p>
                                </div>
                            </div>
                        </div>
                        
                        <a href="#" class="inline-flex items-center gap-3 px-8 py-4 bg-green-600 hover:bg-green-700 text-white text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg">
                            <span class="material-symbols-outlined text-2xl">arrow_forward</span>
                            Learn More
                        </a>
                    </div>
                </div>

                <!-- Techiman Campus -->
                <div class="campus-card glass rounded-3xl shadow-2xl overflow-hidden group">
                    <div class="relative h-72 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="Techiman Campus" class="campus-image w-full h-full object-cover">
                        <div class="campus-overlay absolute inset-0 bg-gradient-to-t from-purple-900 via-purple-900/50 to-transparent opacity-70"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-8">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-4 py-1 bg-purple-400 text-purple-900 font-bold rounded-full text-lg">Campus</span>
                                <span class="px-4 py-1 bg-white/20 backdrop-blur text-white font-semibold rounded-full text-lg">Bono East</span>
                            </div>
                            <h3 class="text-4xl md:text-5xl font-black text-white">Techiman Campus</h3>
                        </div>
                    </div>
                    <div class="p-8 md:p-10">
                        <p class="text-xl md:text-2xl text-gray-700 dark:text-gray-300 mb-8 leading-relaxed font-medium">
                            The Techiman Campus extends VVU's educational reach to the Bono East Region, providing students with accredited programmes and quality education without the need to travel far from home.
                        </p>
                        
                        <div class="grid grid-cols-2 gap-4 mb-8">
                            <div class="flex items-center gap-3 bg-purple-50 dark:bg-purple-900/30 p-4 rounded-2xl">
                                <span class="material-symbols-outlined text-purple-600 text-3xl">school</span>
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">Accredited</span>
                            </div>
                            <div class="flex items-center gap-3 bg-blue-50 dark:bg-blue-900/30 p-4 rounded-2xl">
                                <span class="material-symbols-outlined text-blue-600 text-3xl">library_books</span>
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">Resources</span>
                            </div>
                            <div class="flex items-center gap-3 bg-teal-50 dark:bg-teal-900/30 p-4 rounded-2xl">
                                <span class="material-symbols-outlined text-teal-600 text-3xl">diversity_3</span>
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">Inclusive</span>
                            </div>
                            <div class="flex items-center gap-3 bg-pink-50 dark:bg-pink-900/30 p-4 rounded-2xl">
                                <span class="material-symbols-outlined text-pink-600 text-3xl">star</span>
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">Excellence</span>
                            </div>
                        </div>
                        
                        <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-2xl mb-6">
                            <div class="flex items-start gap-4">
                                <span class="material-symbols-outlined text-purple-600 text-3xl mt-1">location_on</span>
                                <div>
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Location & Contact</h4>
                                    <p class="text-lg text-gray-600 dark:text-gray-400">Techiman, Bono East Region, Ghana</p>
                                    <p class="text-lg text-purple-600 dark:text-purple-400 font-semibold mt-2">📞 Contact Main Campus</p>
                                </div>
                            </div>
                        </div>
                        
                        <a href="#" class="inline-flex items-center gap-3 px-8 py-4 bg-purple-600 hover:bg-purple-700 text-white text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg">
                            <span class="material-symbols-outlined text-2xl">arrow_forward</span>
                            Learn More
                        </a>
                    </div>
                </div>

                <!-- Takoradi Centre -->
                <div class="campus-card glass rounded-3xl shadow-2xl overflow-hidden group">
                    <div class="relative h-72 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1562774053-701939374585?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="Takoradi Centre" class="campus-image w-full h-full object-cover">
                        <div class="campus-overlay absolute inset-0 bg-gradient-to-t from-orange-900 via-orange-900/50 to-transparent opacity-70"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-8">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-4 py-1 bg-orange-400 text-orange-900 font-bold rounded-full text-lg">Centre</span>
                                <span class="px-4 py-1 bg-white/20 backdrop-blur text-white font-semibold rounded-full text-lg">Western Region</span>
                            </div>
                            <h3 class="text-4xl md:text-5xl font-black text-white">Takoradi Centre</h3>
                        </div>
                    </div>
                    <div class="p-8 md:p-10">
                        <p class="text-xl md:text-2xl text-gray-700 dark:text-gray-300 mb-8 leading-relaxed font-medium">
                            The Takoradi Centre serves students in the Western Region, offering VVU's renowned academic programmes with the flexibility to accommodate working professionals and local students.
                        </p>
                        
                        <div class="grid grid-cols-2 gap-4 mb-8">
                            <div class="flex items-center gap-3 bg-orange-50 dark:bg-orange-900/30 p-4 rounded-2xl">
                                <span class="material-symbols-outlined text-orange-600 text-3xl">beach_access</span>
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">Coastal</span>
                            </div>
                            <div class="flex items-center gap-3 bg-blue-50 dark:bg-blue-900/30 p-4 rounded-2xl">
                                <span class="material-symbols-outlined text-blue-600 text-3xl">business</span>
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">Business</span>
                            </div>
                            <div class="flex items-center gap-3 bg-green-50 dark:bg-green-900/30 p-4 rounded-2xl">
                                <span class="material-symbols-outlined text-green-600 text-3xl">eco</span>
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">Sustainable</span>
                            </div>
                            <div class="flex items-center gap-3 bg-red-50 dark:bg-red-900/30 p-4 rounded-2xl">
                                <span class="material-symbols-outlined text-red-600 text-3xl">workspace_premium</span>
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">Quality</span>
                            </div>
                        </div>
                        
                        <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-2xl mb-6">
                            <div class="flex items-start gap-4">
                                <span class="material-symbols-outlined text-orange-600 text-3xl mt-1">location_on</span>
                                <div>
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Location & Contact</h4>
                                    <p class="text-lg text-gray-600 dark:text-gray-400">Takoradi, Western Region, Ghana</p>
                                    <p class="text-lg text-orange-600 dark:text-orange-400 font-semibold mt-2">📞 Contact Main Campus</p>
                                </div>
                            </div>
                        </div>
                        
                        <a href="#" class="inline-flex items-center gap-3 px-8 py-4 bg-orange-600 hover:bg-orange-700 text-white text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg">
                            <span class="material-symbols-outlined text-2xl">arrow_forward</span>
                            Learn More
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Kumasi Campus Programmes Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-5xl mx-auto text-center mb-16">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                    <span class="material-symbols-outlined text-2xl">school</span>
                    <span class="text-xl font-bold uppercase tracking-wider">Kumasi Campus Programmes</span>
                </div>
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-8">
                    Academic <span class="text-blue-600">Programmes</span>
                </h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                    Explore our comprehensive range of accredited programmes at the Kumasi Campus.
                </p>
            </div>

            <!-- Programme Categories -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
                <!-- Undergraduate Programmes -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 p-8 rounded-3xl shadow-lg hover:shadow-2xl transition-all">
                    <div class="w-20 h-20 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg mb-8">
                        <span class="material-symbols-outlined text-4xl">school</span>
                    </div>
                    <h3 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-6">Undergraduate</h3>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-xl text-gray-700 dark:text-gray-300">
                            <span class="material-symbols-outlined text-blue-600">check_circle</span>
                            <span class="font-semibold">Bachelor of Business Administration</span>
                        </li>
                        <li class="flex items-center gap-3 text-xl text-gray-700 dark:text-gray-300">
                            <span class="material-symbols-outlined text-blue-600">check_circle</span>
                            <span class="font-semibold">Bachelor of Education</span>
                        </li>
                        <li class="flex items-center gap-3 text-xl text-gray-700 dark:text-gray-300">
                            <span class="material-symbols-outlined text-blue-600">check_circle</span>
                            <span class="font-semibold">B.Sc. Nursing</span>
                        </li>
                        <li class="flex items-center gap-3 text-xl text-gray-700 dark:text-gray-300">
                            <span class="material-symbols-outlined text-blue-600">check_circle</span>
                            <span class="font-semibold">B.Sc. Midwifery</span>
                        </li>
                    </ul>
                </div>

                <!-- Postgraduate Programmes -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30 p-8 rounded-3xl shadow-lg hover:shadow-2xl transition-all">
                    <div class="w-20 h-20 rounded-2xl bg-green-600 flex items-center justify-center text-white shadow-lg mb-8">
                        <span class="material-symbols-outlined text-4xl">workspace_premium</span>
                    </div>
                    <h3 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-6">Postgraduate</h3>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-xl text-gray-700 dark:text-gray-300">
                            <span class="material-symbols-outlined text-green-600">check_circle</span>
                            <span class="font-semibold">MBA (Strategic Mgmt)</span>
                        </li>
                        <li class="flex items-center gap-3 text-xl text-gray-700 dark:text-gray-300">
                            <span class="material-symbols-outlined text-green-600">check_circle</span>
                            <span class="font-semibold">MBA (HRM)</span>
                        </li>
                        <li class="flex items-center gap-3 text-xl text-gray-700 dark:text-gray-300">
                            <span class="material-symbols-outlined text-green-600">check_circle</span>
                            <span class="font-semibold">M.Ed. Curriculum</span>
                        </li>
                        <li class="flex items-center gap-3 text-xl text-gray-700 dark:text-gray-300">
                            <span class="material-symbols-outlined text-green-600">check_circle</span>
                            <span class="font-semibold">MPhil Programmes</span>
                        </li>
                    </ul>
                </div>

                <!-- Professional & Diploma -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-800/30 p-8 rounded-3xl shadow-lg hover:shadow-2xl transition-all">
                    <div class="w-20 h-20 rounded-2xl bg-purple-600 flex items-center justify-center text-white shadow-lg mb-8">
                        <span class="material-symbols-outlined text-4xl">verified</span>
                    </div>
                    <h3 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-6">Professional</h3>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-xl text-gray-700 dark:text-gray-300">
                            <span class="material-symbols-outlined text-purple-600">check_circle</span>
                            <span class="font-semibold">ICAG Classes</span>
                        </li>
                        <li class="flex items-center gap-3 text-xl text-gray-700 dark:text-gray-300">
                            <span class="material-symbols-outlined text-purple-600">check_circle</span>
                            <span class="font-semibold">ACCA Classes</span>
                        </li>
                        <li class="flex items-center gap-3 text-xl text-gray-700 dark:text-gray-300">
                            <span class="material-symbols-outlined text-purple-600">check_circle</span>
                            <span class="font-semibold">CIMA Classes</span>
                        </li>
                        <li class="flex items-center gap-3 text-xl text-gray-700 dark:text-gray-300">
                            <span class="material-symbols-outlined text-purple-600">check_circle</span>
                            <span class="font-semibold">Diploma Programmes</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Study Modes Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-5xl mx-auto text-center mb-16">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6">Flexible Study Modes</h2>
                <div class="h-2 w-40 bg-yellow-500 mx-auto rounded-full mb-8"></div>
                <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                    We offer various study modes to fit your lifestyle and schedule.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 max-w-7xl mx-auto">
                <div class="feature-card text-center p-8 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all">
                    <div class="feature-icon w-24 h-24 mx-auto rounded-3xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white shadow-xl mb-8">
                        <span class="material-symbols-outlined text-5xl">sunny</span>
                    </div>
                    <h4 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-4">Weekdays</h4>
                    <p class="text-xl text-gray-600 dark:text-gray-400 font-medium">Regular daytime classes for full-time students</p>
                </div>

                <div class="feature-card text-center p-8 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all">
                    <div class="feature-icon w-24 h-24 mx-auto rounded-3xl bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-white shadow-xl mb-8">
                        <span class="material-symbols-outlined text-5xl">nights_stay</span>
                    </div>
                    <h4 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-4">Evening</h4>
                    <p class="text-xl text-gray-600 dark:text-gray-400 font-medium">Perfect for working professionals</p>
                </div>

                <div class="feature-card text-center p-8 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all">
                    <div class="feature-icon w-24 h-24 mx-auto rounded-3xl bg-gradient-to-br from-yellow-500 to-orange-600 flex items-center justify-center text-white shadow-xl mb-8">
                        <span class="material-symbols-outlined text-5xl">calendar_today</span>
                    </div>
                    <h4 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-4">Sundays</h4>
                    <p class="text-xl text-gray-600 dark:text-gray-400 font-medium">Weekend classes for maximum flexibility</p>
                </div>

                <div class="feature-card text-center p-8 bg-white dark:bg-gray-900 rounded-3xl shadow-lg hover:shadow-2xl transition-all">
                    <div class="feature-icon w-24 h-24 mx-auto rounded-3xl bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center text-white shadow-xl mb-8">
                        <span class="material-symbols-outlined text-5xl">computer</span>
                    </div>
                    <h4 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-4">Distance</h4>
                    <p class="text-xl text-gray-600 dark:text-gray-400 font-medium">Learn from anywhere at your own pace</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Our Campuses Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-5xl mx-auto text-center mb-16">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6">Why Choose Our Campuses?</h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                    Experience the same quality education closer to home.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
                <div class="p-8 bg-gray-50 dark:bg-gray-800 rounded-3xl hover:shadow-2xl transition-all transform hover:-translate-y-2">
                    <div class="w-20 h-20 rounded-2xl bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-blue-600 text-4xl">verified</span>
                    </div>
                    <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4">NAB Accredited</h4>
                    <p class="text-xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        All our campuses enjoy full accreditation from the National Accreditation Board of Ghana.
                    </p>
                </div>

                <div class="p-8 bg-gray-50 dark:bg-gray-800 rounded-3xl hover:shadow-2xl transition-all transform hover:-translate-y-2">
                    <div class="w-20 h-20 rounded-2xl bg-green-100 dark:bg-green-900/50 flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-green-600 text-4xl">public</span>
                    </div>
                    <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4">AAA Recognized</h4>
                    <p class="text-xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        Recognized by the Adventist Accrediting Association, a global body overseeing 114+ universities worldwide.
                    </p>
                </div>

                <div class="p-8 bg-gray-50 dark:bg-gray-800 rounded-3xl hover:shadow-2xl transition-all transform hover:-translate-y-2">
                    <div class="w-20 h-20 rounded-2xl bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-purple-600 text-4xl">diversity_2</span>
                    </div>
                    <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4">Experienced Faculty</h4>
                    <p class="text-xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        Learn from dedicated and experienced university administrators committed to your success.
                    </p>
                </div>

                <div class="p-8 bg-gray-50 dark:bg-gray-800 rounded-3xl hover:shadow-2xl transition-all transform hover:-translate-y-2">
                    <div class="w-20 h-20 rounded-2xl bg-yellow-100 dark:bg-yellow-900/50 flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-yellow-600 text-4xl">location_on</span>
                    </div>
                    <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4">Convenient Locations</h4>
                    <p class="text-xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        Strategically located across Ghana to reduce travel time and costs for students.
                    </p>
                </div>

                <div class="p-8 bg-gray-50 dark:bg-gray-800 rounded-3xl hover:shadow-2xl transition-all transform hover:-translate-y-2">
                    <div class="w-20 h-20 rounded-2xl bg-red-100 dark:bg-red-900/50 flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-red-600 text-4xl">support</span>
                    </div>
                    <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4">Student Support</h4>
                    <p class="text-xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        Comprehensive support services to ensure your academic journey is smooth and successful.
                    </p>
                </div>

                <div class="p-8 bg-gray-50 dark:bg-gray-800 rounded-3xl hover:shadow-2xl transition-all transform hover:-translate-y-2">
                    <div class="w-20 h-20 rounded-2xl bg-teal-100 dark:bg-teal-900/50 flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-teal-600 text-4xl">payments</span>
                    </div>
                    <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-4">Easy Payments</h4>
                    <p class="text-xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        Multiple payment options including Mobile Money (*800*50#) for convenient fee payment.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-24 bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-white mb-6">Our Network at a Glance</h2>
                    <div class="h-2 w-40 bg-yellow-400 mx-auto rounded-full"></div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="stat-card text-center p-8 bg-white/10 backdrop-blur-md rounded-3xl border border-white/20">
                        <div class="text-6xl font-black text-yellow-400 mb-4">4</div>
                        <div class="text-2xl text-white font-bold uppercase tracking-wider">Campuses & Centres</div>
                    </div>
                    <div class="stat-card text-center p-8 bg-white/10 backdrop-blur-md rounded-3xl border border-white/20">
                        <div class="text-6xl font-black text-yellow-400 mb-4">20+</div>
                        <div class="text-2xl text-white font-bold uppercase tracking-wider">Programmes</div>
                    </div>
                    <div class="stat-card text-center p-8 bg-white/10 backdrop-blur-md rounded-3xl border border-white/20">
                        <div class="text-6xl font-black text-yellow-400 mb-4">4</div>
                        <div class="text-2xl text-white font-bold uppercase tracking-wider">Study Modes</div>
                    </div>
                    <div class="stat-card text-center p-8 bg-white/10 backdrop-blur-md rounded-3xl border border-white/20">
                        <div class="text-6xl font-black text-yellow-400 mb-4">100%</div>
                        <div class="text-2xl text-white font-bold uppercase tracking-wider">Accredited</div>
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
                    Ready to Join <br><span class="text-yellow-400 text-6xl sm:text-7xl md:text-8xl lg:text-6xl block mt-2">Our Growing Family?</span>
                </h2>
                <p class="text-2xl sm:text-3xl md:text-4xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    Visit any of our campuses or centres today and take the first step towards your future.
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="apply.php" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">how_to_reg</span>
                        Apply Online Free
                    </a>
                    <a href="contact_us.php" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">call</span>
                        Contact Us
                    </a>
                </div>
                
                <div class="mt-20 grid grid-cols-1 sm:grid-cols-3 gap-12 border-t border-white/10 pt-16">
                    <div>
                        <div class="text-6xl font-black text-yellow-400 mb-2">Kumasi</div>
                        <div class="text-blue-200 uppercase tracking-widest text-xl font-black">Main Campus</div>
                    </div>
                    <div>
                        <div class="text-6xl font-black text-yellow-400 mb-2">Tamale</div>
                        <div class="text-blue-200 uppercase tracking-widest text-xl font-black">Northern Centre</div>
                    </div>
                    <div>
                        <div class="text-6xl font-black text-yellow-400 mb-2">Takoradi</div>
                        <div class="text-blue-200 uppercase tracking-widest text-xl font-black">Western Centre</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
