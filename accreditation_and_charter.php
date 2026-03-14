<?php
$page_title = "Accreditation & Charter - Valley View University";
$active_page = "about";
require_once 'includes/db_connect.php';

// Fetch data from database
$hero = $pdo->query("SELECT * FROM accreditation_hero WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$cards = $pdo->query("SELECT * FROM accreditation_cards WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$charter = $pdo->query("SELECT * FROM accreditation_charter WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$memberships = $pdo->query("SELECT * FROM accreditation_memberships WHERE is_active=1 AND membership_type='membership' ORDER BY display_order ASC")->fetchAll();
$linkages = $pdo->query("SELECT * FROM accreditation_memberships WHERE is_active=1 AND membership_type='linkage' ORDER BY display_order ASC")->fetchAll();
$cta = $pdo->query("SELECT * FROM accreditation_cta WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();

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
    .accreditation-card {
        transition: all 0.3s ease;
    }
    .accreditation-card:hover {
        transform: translateY(-10px);
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image_url'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuBT_9onDZsW2FiO7PENWLZ2-zS-pH_w_0fx3u39rY8cLStB2LjjTqB_NPnq0lt2LmdWHLAzaopeU6I9zjaUkGISXnPVoe1MkE_vBUUM8fr-BTT82YhFdDVvGv_gnYuMw_90H1Bwgk-XZwEVJuSa1lsZ1KcgaBA0zyrOQ79syt1j9--cEd2d8A70P0b85kpPxbccquV8y__dCuLp29-lsMWdKu4P4i2zCriI0j3fszUQio1xwXzRactEz8y9Wswe6Lxfec9HTLdXILKs'); ?>" 
                 alt="VVU Campus" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo $hero ? strip_tags($hero['page_subtitle']) : 'Quality Assurance'; ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo $hero ? strip_tags($hero['hero_title']) : 'Accreditation'; ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo $hero ? strip_tags($hero['hero_subtitle']) : '& University Charter'; ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo $hero ? strip_tags($hero['hero_description']) : '"Valley View University is fully accredited and committed to the highest standards of academic excellence, recognized by national and international governing bodies."'; ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Accreditation Section -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6">Official Accreditation</h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">Our programs are rigorously evaluated and accredited by leading educational authorities.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
                <?php foreach ($cards as $card): ?>
                <div class="accreditation-card relative group">
                    <div class="relative h-full glass p-10 rounded-3xl shadow-xl border-t-8 border-<?php echo strip_tags($card['border_color']); ?> flex flex-col">
                        <div class="w-24 h-24 rounded-3xl bg-<?php echo strip_tags($card['border_color']); ?> flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($card['icon']); ?></span>
                        </div>
                        <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($card['title']); ?></h3>
                        <p class="text-3xl text-gray-700 dark:text-gray-300 mb-8 flex-grow leading-relaxed">
                            <?php echo $card['description']; ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Presidential Charter Section -->
    <section class="py-24 bg-gray-50 dark:bg-gray-950 relative overflow-hidden">
        <div class="container relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="animate-fadeInUp">
                    <div class="inline-flex items-center gap-3 px-6 py-2 mb-6 rounded-full bg-yellow-500/10 border border-yellow-500/20">
                        <span class="text-lg font-bold text-yellow-600 uppercase tracking-widest"><?php echo $charter ? strip_tags($charter['badge_text']) : 'A Historic Milestone'; ?></span>
                    </div>
                    <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-8"><?php echo $charter ? $charter['section_title'] : 'The Presidential <span class="text-blue-600">Charter</span>'; ?></h2>
                    <p class="text-3xl text-gray-700 dark:text-gray-300 leading-relaxed mb-6 font-medium">
                        <?php echo $charter ? nl2br(strip_tags($charter['paragraph_1'])) : 'In January 2006, Valley View University was granted a Presidential Charter by His Excellency, Mr. J. A. Kufuor, President of the Republic of Ghana.'; ?>
                    </p>
                    <p class="text-3xl text-gray-700 dark:text-gray-300 leading-relaxed mb-8 font-medium">
                        <?php echo $charter ? nl2br($charter['paragraph_2']) : 'This historic achievement made VVU the <strong class="text-blue-600">first Chartered Private University in Ghana</strong>, granting us the rights and privileges to operate as an autonomous degree-granting institution.'; ?>
                    </p>
                    <div class="glass p-8 rounded-2xl border-l-8 border-yellow-500">
                        <p class="text-2xl text-gray-600 dark:text-gray-400 italic font-medium">
                            <?php echo $charter ? strip_tags($charter['quote']) : '"Chartered status is granted after careful scrutiny of an institution\'s statutes, examination procedures, and quality assurance standards."'; ?>
                        </p>
                    </div>
                </div>
                <div class="relative animate-fadeInUp" style="animation-delay: 0.2s;">
                    <div class="absolute -inset-4 bg-gradient-to-r from-blue-600 to-yellow-500 rounded-3xl blur-2xl opacity-20"></div>
                    <div class="relative bg-white dark:bg-gray-900 p-12 rounded-3xl shadow-2xl text-center">
                        <span class="material-symbols-outlined text-9xl text-yellow-500 mb-6">workspace_premium</span>
                        <h4 class="text-5xl font-black text-gray-900 dark:text-white mb-4"><?php echo $charter ? strip_tags($charter['achievement_text']) : 'First Chartered Private University'; ?></h4>
                        <p class="text-2xl text-gray-500 font-bold uppercase tracking-widest"><?php echo $charter ? strip_tags($charter['achievement_location']) : 'Ghana • 2006'; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Memberships & Linkages -->
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                <!-- Memberships -->
                <div>
                    <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-10 flex items-center gap-4">
                        <span class="material-symbols-outlined text-blue-600 text-6xl">groups</span>
                        Professional Memberships
                    </h3>
                    <ul class="space-y-6">
                        <?php foreach ($memberships as $membership): ?>
                        <li class="flex items-start gap-4 group">
                            <span class="material-symbols-outlined text-yellow-500 text-4xl group-hover:scale-125 transition-transform">check_circle</span>
                            <div>
                                <h5 class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo strip_tags($membership['organization_name']); ?></h5>
                                <p class="text-2xl text-gray-600 dark:text-gray-400"><?php echo strip_tags($membership['organization_description']); ?></p>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Linkages -->
                <div>
                    <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-10 flex items-center gap-4">
                        <span class="material-symbols-outlined text-blue-600 text-6xl">link</span>
                        Global Linkages
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <?php foreach ($linkages as $linkage): ?>
                        <div class="glass p-6 rounded-2xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                            <h5 class="text-3xl font-bold text-gray-900 dark:text-white"><?php echo strip_tags($linkage['organization_name']); ?></h5>
                            <p class="text-lg text-gray-500 font-bold uppercase"><?php echo strip_tags($linkage['location']); ?></p>
                        </div>
                        <?php endforeach; ?>
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
                    <?php echo $cta ? strip_tags($cta['cta_title_1']) : 'Committed to'; ?> <br><span class="text-yellow-400 text-6xl sm:text-7xl md:text-8xl lg:text-6xl block mt-2"><?php echo $cta ? strip_tags($cta['cta_title_2']) : 'Academic Excellence'; ?></span>
                </h2>
                <p class="text-2xl sm:text-3xl md:text-4xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo $cta ? strip_tags($cta['cta_description']) : 'Our accreditation ensures that your degree is recognized and valued globally.'; ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo $cta ? strip_tags($cta['button_1_url']) : 'academic_programs_overview.php'; ?>" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">school</span>
                        <?php echo $cta ? strip_tags($cta['button_1_text']) : 'Explore Programs'; ?>
                    </a>
                    <a href="<?php echo $cta ? strip_tags($cta['button_2_url']) : 'contact_us.php'; ?>" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">mail</span>
                        <?php echo $cta ? strip_tags($cta['button_2_text']) : 'Contact Us'; ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>