<?php
$page_title = "iSchool - Valley View University";
$active_page = "resources";
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
    .ischool-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .ischool-card:hover {
        transform: translateY(-15px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }
    .icon-container {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
    }
    .icon-container span {
        color: white !important;
        font-size: 40px;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=1920" 
                 alt="Digital Campus" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400">Student Online Services</span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    iSchool <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4">Digital Portal</span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "Empowering your academic journey with seamless digital access to registration, results, and support services."
                </p>
            </div>
        </div>
    </section>

    <!-- Main Portals Section -->
    <section class="py-24 bg-white dark:bg-gray-900 relative z-20 -mt-20 mx-auto max-w-[95%] rounded-[3rem] shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-800">
        <div class="w-full px-8 md:px-16">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-6xl sm:text-7xl font-black text-gray-900 dark:text-white mb-6">Access Your Portal</h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-4xl text-gray-600 dark:text-gray-400 font-bold leading-relaxed">Choose the appropriate portal to manage your academic records and university life.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <!-- Undergraduate iSchool -->
                <div class="ischool-card group p-10 bg-gray-50 dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 flex flex-col items-center text-center">
                    <div class="icon-container">
                        <span class="material-symbols-outlined">school</span>
                    </div>
                    <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-6">Undergraduate</h3>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 mb-10 flex-grow leading-relaxed font-medium">
                        Access registration, view semester results, and manage your undergraduate academic profile.
                    </p>
                    <a href="https://ischool.vvu.edu.gh/Default.aspx" target="_blank" class="w-full py-6 bg-blue-600 text-white text-2xl font-bold rounded-2xl hover:bg-blue-700 transition-all shadow-lg flex items-center justify-center gap-3">
                        Launch Portal
                        <span class="material-symbols-outlined text-3xl">open_in_new</span>
                    </a>
                </div>

                <!-- Graduate iSchool -->
                <div class="ischool-card group p-10 bg-gray-50 dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 flex flex-col items-center text-center">
                    <div class="icon-container bg-gradient-to-br from-purple-600 to-purple-800">
                        <span class="material-symbols-outlined">workspace_premium</span>
                    </div>
                    <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-6">Graduate</h3>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 mb-10 flex-grow leading-relaxed font-medium">
                        Dedicated portal for postgraduate students to manage research, courses, and graduation requirements.
                    </p>
                    <a href="https://grad.vvu.edu.gh/" target="_blank" class="w-full py-6 bg-purple-600 text-white text-2xl font-bold rounded-2xl hover:bg-purple-700 transition-all shadow-lg flex items-center justify-center gap-3">
                        Launch Portal
                        <span class="material-symbols-outlined text-3xl">open_in_new</span>
                    </a>
                </div>

                <!-- ITS Help Desk -->
                <div class="ischool-card group p-10 bg-gray-50 dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 flex flex-col items-center text-center">
                    <div class="icon-container bg-gradient-to-br from-yellow-500 to-yellow-600">
                        <span class="material-symbols-outlined">support_agent</span>
                    </div>
                    <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-6">ITS Help Desk</h3>
                    <p class="text-3xl text-gray-600 dark:text-gray-400 mb-10 flex-grow leading-relaxed font-medium">
                        Need technical assistance? Submit a ticket or browse our knowledge base for quick solutions.
                    </p>
                    <a href="https://support.vvu.edu.gh/" target="_blank" class="w-full py-6 bg-yellow-500 text-white text-2xl font-bold rounded-2xl hover:bg-yellow-600 transition-all shadow-lg flex items-center justify-center gap-3">
                        Get Support
                        <span class="material-symbols-outlined text-3xl">contact_support</span>
                    </a>
                </div>
            </div>

            <!-- Graduation Status Checker -->
            <div class="mt-16 max-w-6xl mx-auto">
                <a href="https://status.vvu.edu.gh/" target="_blank" rel="noopener"
                   class="group flex flex-col md:flex-row items-center gap-10 p-10 md:p-14 rounded-[2.5rem] bg-gradient-to-br from-emerald-600 to-emerald-800 shadow-2xl transition-all hover:shadow-emerald-900/40 hover:-translate-y-1">
                    <div class="flex-shrink-0 w-28 h-28 rounded-[2rem] bg-white/15 backdrop-blur-md border border-white/25 flex items-center justify-center">
                        <span class="material-symbols-outlined text-white" style="font-size: 4rem;">verified</span>
                    </div>
                    <div class="flex-grow text-center md:text-left">
                        <span class="inline-block px-5 py-2 mb-4 rounded-full bg-white/20 text-white text-lg font-black uppercase tracking-widest">Graduating Students</span>
                        <h3 class="text-4xl md:text-5xl font-black text-white mb-4">Graduation Status Checker</h3>
                        <p class="text-2xl md:text-3xl text-emerald-50 font-medium leading-relaxed">
                            Search with any of your names or your student ID number to confirm whether you have qualified to graduate.
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center gap-3 px-10 py-6 bg-white text-emerald-800 text-2xl font-black rounded-2xl shadow-lg transition-transform group-hover:scale-105">
                            Check Status
                            <span class="material-symbols-outlined text-3xl">open_in_new</span>
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Key Features Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="w-full max-w-[95%] mx-auto px-8 md:px-16">
            <div class="max-w-5xl mx-auto text-center mb-20">
                <h2 class="text-6xl sm:text-7xl font-black text-gray-900 dark:text-white mb-6">What You Can Do</h2>
                <p class="text-4xl text-gray-600 dark:text-gray-400 font-bold leading-relaxed">Our digital ecosystem is designed to simplify your academic life.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="flex gap-8 p-10 bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-lg border border-gray-100 dark:border-gray-800">
                    <div class="w-24 h-24 shrink-0 rounded-3xl bg-blue-600 flex items-center justify-center text-white shadow-lg">
                        <span class="material-symbols-outlined text-5xl text-white">how_to_reg</span>
                    </div>
                    <div>
                        <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4">Course Registration</h4>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium">Register for your semester courses online from anywhere in the world.</p>
                    </div>
                </div>

                <div class="flex gap-8 p-10 bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-lg border border-gray-100 dark:border-gray-800">
                    <div class="w-24 h-24 shrink-0 rounded-3xl bg-green-600 flex items-center justify-center text-white shadow-lg">
                        <span class="material-symbols-outlined text-5xl text-white">analytics</span>
                    </div>
                    <div>
                        <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4">Result Checking</h4>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium">Access your semester grades and cumulative GPA instantly as they are released.</p>
                    </div>
                </div>

                <div class="flex gap-8 p-10 bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-lg border border-gray-100 dark:border-gray-800">
                    <div class="w-24 h-24 shrink-0 rounded-3xl bg-yellow-500 flex items-center justify-center text-white shadow-lg">
                        <span class="material-symbols-outlined text-5xl text-white">payments</span>
                    </div>
                    <div>
                        <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4">Financial Status</h4>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium">Monitor your fee payments, balances, and financial statements in real-time.</p>
                    </div>
                </div>

                <div class="flex gap-8 p-10 bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-lg border border-gray-100 dark:border-gray-800">
                    <div class="w-24 h-24 shrink-0 rounded-3xl bg-purple-600 flex items-center justify-center text-white shadow-lg">
                        <span class="material-symbols-outlined text-5xl text-white">badge</span>
                    </div>
                    <div>
                        <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4">Profile Management</h4>
                        <p class="text-3xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium">Keep your personal information and contact details up to date with the university.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Support Section -->
    <section class="py-24 bg-blue-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        
        <div class="relative z-10 w-full max-w-[95%] mx-auto px-8 md:px-16">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-white mb-8">Having Trouble?</h2>
                <p class="text-2xl sm:text-3xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    Our dedicated ITS team is here to help you navigate any technical challenges you may encounter.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-12 max-w-6xl mx-auto">
                    <div class="p-12 bg-white/10 backdrop-blur-md rounded-[2.5rem] border border-white/20 text-left">
                        <h4 class="text-4xl font-black text-yellow-400 mb-6">Call Support</h4>
                        <p class="text-3xl text-white font-bold">+233 307011832</p>
                        <p class="text-2xl text-blue-200 mt-4 font-medium">Available Mon-Fri, 8am - 5pm</p>
                    </div>
                    <div class="p-12 bg-white/10 backdrop-blur-md rounded-[2.5rem] border border-white/20 text-left">
                        <h4 class="text-4xl font-black text-yellow-400 mb-6">Email Us</h4>
                        <p class="text-3xl text-white font-bold">support@vvu.edu.gh</p>
                        <p class="text-2xl text-blue-200 mt-4 font-medium">We typically respond within 24 hours</p>
                    </div>
                </div>
                <div class="mt-16">
                    <a href="https://support.vvu.edu.gh/" class="inline-flex items-center gap-4 px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl">
                        Visit Help Desk
                        <span class="material-symbols-outlined text-white">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
