<?php
$pageTitle = "Faculty of Science - Valley View University";
$activePage = "academics";
?>

<?php include 'includes/header.php'; ?>

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
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.8s ease-out forwards; }
    .animate-float { animation: float 4s ease-in-out infinite; }
    
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
    
    .card-hover { transition: all 0.4s ease; }
    .card-hover:hover { 
        transform: translateY(-15px);
        box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.25);
    }
    
    .text-gradient {
        background: linear-gradient(to right, #fbbf24, #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section (Inspired by core_values.php) -->
    <section class="relative min-h-[75vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="images/faculty_of_science_hero.png" 
                 alt="Faculty of Science" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-32">
            <div class="max-w-7xl mx-auto text-center">
                <div class="inline-flex items-center gap-4 px-12 py-5 mb-12 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-4 h-4 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-2xl md:text-3xl font-black tracking-widest uppercase text-yellow-400">Faculty of Science</span>
                </div>
                
                <h1 class="text-7xl sm:text-8xl md:text-9xl lg:text-[10rem] font-black leading-none tracking-tighter text-white mb-12 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    Innovate. <br>
                    <span class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-6">Discover. Inspire.</span>
                </h1>
                
                <p class="text-2xl sm:text-3xl md:text-4xl text-white/90 leading-relaxed max-w-5xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "Shaping the future through scientific exploration and groundbreaking discovery at Valley View University."
                </p>
                
                <div class="mt-16 flex flex-wrap justify-center gap-6 animate-fadeInUp" style="animation-delay: 0.3s;">
                    <a href="#programs" class="px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-2xl font-black rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center gap-4">
                        <span class="material-symbols-outlined text-4xl">school</span>
                        Explore Programs
                    </a>
                    <a href="apply.php" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-2xl font-black rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-xl flex items-center gap-4">
                        <span class="material-symbols-outlined text-4xl">how_to_reg</span>
                        Apply Now
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Introduction Section -->
    <section class="py-40 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-24 items-center">
                    <div class="animate-fadeInUp">
                        <div class="inline-flex items-center gap-5 px-10 py-4 mb-10 rounded-full bg-blue-600 shadow-lg">
                            <span class="material-symbols-outlined text-4xl text-yellow-400">science</span>
                            <span class="text-2xl font-black uppercase tracking-wider text-yellow-400">About the Faculty</span>
                        </div>
                        <h2 class="text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-10 leading-tight">
                            Excellence in <span class="text-blue-600">Scientific Education</span>
                        </h2>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed mb-12">
                            The Faculty of Science (FOS) has a firm belief that students should have impeccable educational experience that encourages scholarship through the application of scientific knowledge in teaching, learning, and research.
                        </p>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed mb-12">
                            At FOS, we devote our time to promoting top-notch programmes, to develop highly skilled and globally-employable graduates. Our holistic education empowers our students in solving real-life problems and fulfilling the will of God.
                        </p>
                        <div class="flex items-center gap-8">
                            <div class="text-center">
                                <div class="text-6xl font-black text-blue-600 mb-2">20+</div>
                                <div class="text-xl font-bold text-gray-500 uppercase tracking-widest">Programs</div>
                            </div>
                            <div class="w-px h-16 bg-gray-200"></div>
                            <div class="text-center">
                                <div class="text-6xl font-black text-blue-600 mb-2">100%</div>
                                <div class="text-xl font-bold text-gray-500 uppercase tracking-widest">Commitment</div>
                            </div>
                        </div>
                    </div>
                    <div class="relative animate-fadeInUp" style="animation-delay: 0.2s;">
                        <div class="absolute -inset-4 bg-gradient-to-r from-blue-600 to-yellow-500 rounded-[4rem] blur-2xl opacity-20"></div>
                        <img src="https://images.unsplash.com/photo-1532094349884-543bc11b234d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                             alt="Science Lab" class="relative rounded-[4rem] shadow-2xl w-full h-[600px] object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="py-40 bg-gray-50 dark:bg-gray-950 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-600/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
                    <!-- Mission -->
                    <div class="glass p-16 rounded-[4rem] shadow-2xl border border-gray-100 dark:border-gray-800 card-hover">
                        <div class="w-24 h-24 rounded-3xl bg-blue-600 flex items-center justify-center text-white shadow-lg mb-10">
                            <span class="material-symbols-outlined text-5xl">target</span>
                        </div>
                        <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-8">Our Mission</h3>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                            To serve as an international center of excellence in the provision of high-quality holistic education, and professional training in computing sciences; as well as serve as a leading center for cutting-edge advanced research and development.
                        </p>
                    </div>
                    
                    <!-- Vision -->
                    <div class="glass p-16 rounded-[4rem] shadow-2xl border border-gray-100 dark:border-gray-800 card-hover">
                        <div class="w-24 h-24 rounded-3xl bg-yellow-500 flex items-center justify-center text-white shadow-lg mb-10">
                            <span class="material-symbols-outlined text-5xl">visibility</span>
                        </div>
                        <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-8">Our Vision</h3>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                            To be the most preferred center of excellence in research in Applied Sciences producing precocious graduates who are critical thinkers and creative with high ethical and professional standards of service to God and humanity.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Departments Section -->
    <section class="py-40 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-24">
                    <h2 class="text-7xl md:text-8xl font-black text-gray-900 dark:text-white mb-8">
                        Our <span class="text-blue-600">Departments</span>
                    </h2>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium max-w-4xl mx-auto">
                        The Faculty of Science is organized into specialized departments, each dedicated to excellence in their respective fields.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <!-- Computer Science -->
                    <div class="group p-12 bg-gray-50 dark:bg-gray-800 rounded-[3rem] hover:bg-blue-600 transition-all duration-500 hover:-translate-y-4 shadow-sm hover:shadow-2xl">
                        <div class="w-24 h-24 rounded-3xl bg-blue-600 group-hover:bg-white flex items-center justify-center mb-10 transition-colors shadow-lg">
                            <span class="material-symbols-outlined text-5xl text-white group-hover:text-blue-600">computer</span>
                        </div>
                        <h4 class="text-5xl font-black text-gray-900 dark:text-white group-hover:text-white mb-6 transition-colors">Computer Science</h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400 group-hover:text-blue-50 font-medium transition-colors leading-relaxed">
                            Leading the way in software engineering, artificial intelligence, and advanced computing research.
                        </p>
                    </div>

                    <!-- Information Technology -->
                    <div class="group p-12 bg-gray-50 dark:bg-gray-800 rounded-[3rem] hover:bg-blue-600 transition-all duration-500 hover:-translate-y-4 shadow-sm hover:shadow-2xl">
                        <div class="w-24 h-24 rounded-3xl bg-blue-600 group-hover:bg-white flex items-center justify-center mb-10 transition-colors shadow-lg">
                            <span class="material-symbols-outlined text-5xl text-white group-hover:text-blue-600">lan</span>
                        </div>
                        <h4 class="text-5xl font-black text-gray-900 dark:text-white group-hover:text-white mb-6 transition-colors">Information Technology</h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400 group-hover:text-blue-50 font-medium transition-colors leading-relaxed">
                            Empowering students with practical skills in network administration, cybersecurity, and digital systems.
                        </p>
                    </div>

                    <!-- Mathematical Science -->
                    <div class="group p-12 bg-gray-50 dark:bg-gray-800 rounded-[3rem] hover:bg-blue-600 transition-all duration-500 hover:-translate-y-4 shadow-sm hover:shadow-2xl">
                        <div class="w-24 h-24 rounded-3xl bg-blue-600 group-hover:bg-white flex items-center justify-center mb-10 transition-colors shadow-lg">
                            <span class="material-symbols-outlined text-5xl text-white group-hover:text-blue-600">functions</span>
                        </div>
                        <h4 class="text-5xl font-black text-gray-900 dark:text-white group-hover:text-white mb-6 transition-colors">Mathematical Science</h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400 group-hover:text-blue-50 font-medium transition-colors leading-relaxed">
                            Fostering analytical thinking and problem-solving through mathematics, statistics, and economics.
                        </p>
                    </div>

                    <!-- Biomedical Equipment and Technology -->
                    <div class="group p-12 bg-gray-50 dark:bg-gray-800 rounded-[3rem] hover:bg-blue-600 transition-all duration-500 hover:-translate-y-4 shadow-sm hover:shadow-2xl">
                        <div class="w-24 h-24 rounded-3xl bg-blue-600 group-hover:bg-white flex items-center justify-center mb-10 transition-colors shadow-lg">
                            <span class="material-symbols-outlined text-5xl text-white group-hover:text-blue-600">medical_services</span>
                        </div>
                        <h4 class="text-5xl font-black text-gray-900 dark:text-white group-hover:text-white mb-6 transition-colors">Biomedical Tech</h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400 group-hover:text-blue-50 font-medium transition-colors leading-relaxed">
                            Bridging the gap between engineering and medicine through advanced healthcare technology.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs Section -->
    <section id="programs" class="py-40 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-24">
                    <h2 class="text-7xl md:text-8xl font-black text-gray-900 dark:text-white mb-8">
                        Academic <span class="text-blue-600">Programs</span>
                    </h2>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium max-w-4xl mx-auto">
                        We offer a wide range of undergraduate and graduate programs designed to meet the demands of the modern world.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                    <!-- Graduate Programs -->
                    <div class="glass p-12 rounded-[4rem] shadow-xl border-t-[12px] border-blue-600">
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-10 flex items-center gap-4">
                            <span class="material-symbols-outlined text-blue-600 text-5xl">workspace_premium</span>
                            Graduate
                        </h3>
                        <ul class="space-y-6">
                            <li class="flex items-start gap-4">
                                <span class="material-symbols-outlined text-blue-600 mt-1">check_circle</span>
                                <span class="text-2xl font-bold text-gray-700 dark:text-gray-300">PhD Computer Science</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <span class="material-symbols-outlined text-blue-600 mt-1">check_circle</span>
                                <span class="text-2xl font-bold text-gray-700 dark:text-gray-300">MPhil Computer Science</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <span class="material-symbols-outlined text-blue-600 mt-1">check_circle</span>
                                <span class="text-2xl font-bold text-gray-700 dark:text-gray-300">MSc Computer Science</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Undergraduate Programs -->
                    <div class="glass p-12 rounded-[4rem] shadow-xl border-t-[12px] border-yellow-500">
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-10 flex items-center gap-4">
                            <span class="material-symbols-outlined text-yellow-500 text-5xl">school</span>
                            Undergraduate
                        </h3>
                        <ul class="space-y-6">
                            <li class="flex items-start gap-4">
                                <span class="material-symbols-outlined text-yellow-500 mt-1">check_circle</span>
                                <span class="text-2xl font-bold text-gray-700 dark:text-gray-300">BSc Computer Science</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <span class="material-symbols-outlined text-yellow-500 mt-1">check_circle</span>
                                <span class="text-2xl font-bold text-gray-700 dark:text-gray-300">BSc Information Technology</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <span class="material-symbols-outlined text-yellow-500 mt-1">check_circle</span>
                                <span class="text-2xl font-bold text-gray-700 dark:text-gray-300">BSc Mathematics with Statistics</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <span class="material-symbols-outlined text-yellow-500 mt-1">check_circle</span>
                                <span class="text-2xl font-bold text-gray-700 dark:text-gray-300">BSc Mathematics with Economics</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <span class="material-symbols-outlined text-yellow-500 mt-1">check_circle</span>
                                <span class="text-2xl font-bold text-gray-700 dark:text-gray-300">BSc Business Information Systems</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <span class="material-symbols-outlined text-yellow-500 mt-1">check_circle</span>
                                <span class="text-2xl font-bold text-gray-700 dark:text-gray-300">BSc Bio-Medical Equipment Tech</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Diploma Programs -->
                    <div class="glass p-12 rounded-[4rem] shadow-xl border-t-[12px] border-purple-600">
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-10 flex items-center gap-4">
                            <span class="material-symbols-outlined text-purple-600 text-5xl">description</span>
                            Diploma
                        </h3>
                        <ul class="space-y-6">
                            <li class="flex items-start gap-4">
                                <span class="material-symbols-outlined text-purple-600 mt-1">check_circle</span>
                                <span class="text-2xl font-bold text-gray-700 dark:text-gray-300">Diploma in Biomedical Equipment Tech</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <span class="material-symbols-outlined text-purple-600 mt-1">check_circle</span>
                                <span class="text-2xl font-bold text-gray-700 dark:text-gray-300">Diploma in Information Technology</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <span class="material-symbols-outlined text-purple-600 mt-1">check_circle</span>
                                <span class="text-2xl font-bold text-gray-700 dark:text-gray-300">Diploma in Computer Science</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose FOS Section (Suggested Content) -->
    <section class="py-40 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-24">
                    <h2 class="text-7xl md:text-8xl font-black text-gray-900 dark:text-white mb-8">
                        Why Choose <span class="text-blue-600">FOS?</span>
                    </h2>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium max-w-4xl mx-auto">
                        Discover what sets the Faculty of Science at Valley View University apart.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div class="text-center p-12">
                        <div class="w-32 h-32 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mx-auto mb-10 animate-float">
                            <span class="material-symbols-outlined text-6xl text-blue-600">biotech</span>
                        </div>
                        <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-6">Cutting-edge Research</h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                            Engage in advanced research projects in emerging fields of science and technology.
                        </p>
                    </div>

                    <div class="text-center p-12">
                        <div class="w-32 h-32 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center mx-auto mb-10 animate-float" style="animation-delay: 0.5s;">
                            <span class="material-symbols-outlined text-6xl text-yellow-600">psychology</span>
                        </div>
                        <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-6">Holistic Education</h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                            Develop critical thinking and creative skills through our comprehensive curriculum.
                        </p>
                    </div>

                    <div class="text-center p-12">
                        <div class="w-32 h-32 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mx-auto mb-10 animate-float" style="animation-delay: 1s;">
                            <span class="material-symbols-outlined text-6xl text-purple-600">church</span>
                        </div>
                        <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-6">Faith Integration</h4>
                        <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                            Integrate faith and learning to exert a positive moral and professional influence.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-40 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        
        <div class="container relative z-10">
            <div class="max-w-7xl mx-auto text-center">
                <h2 class="text-7xl sm:text-8xl md:text-9xl lg:text-[10rem] font-black text-white mb-12 leading-tight">
                    Ready to <br><span class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl text-yellow-400">Join Us?</span>
                </h2>
                <p class="text-4xl sm:text-5xl text-blue-100 mb-20 max-w-5xl mx-auto leading-relaxed font-medium">
                    Start your journey towards becoming a globally-employable science professional today.
                </p>
                <div class="flex flex-col sm:flex-row gap-10 justify-center">
                    <a href="apply.php" class="px-16 py-8 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-3xl font-bold rounded-[2rem] transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-5">
                        <span class="material-symbols-outlined text-5xl text-blue-900">how_to_reg</span>
                        Apply Now
                    </a>
                    <a href="contact_us.php" class="px-16 py-8 bg-white/10 hover:bg-white/20 text-white text-3xl font-bold rounded-[2rem] transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-5">
                        <span class="material-symbols-outlined text-5xl text-white">support_agent</span>
                        Contact Us
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
