<?php
require_once('includes/db_connect.php');

// Fetch Contact Page Data
$hero = $pdo->query("SELECT * FROM contact_hero WHERE is_active = 1 LIMIT 1")->fetch();
$quick_cards = $pdo->query("SELECT * FROM contact_quick_cards WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
$postal = $pdo->query("SELECT * FROM contact_postal_addresses WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
$socials = $pdo->query("SELECT * FROM contact_social_links WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
$emerg_ussd = $pdo->query("SELECT * FROM contact_emergency_ussd WHERE is_active = 1")->fetchAll();
$dept_header = $pdo->query("SELECT * FROM contact_departments_header LIMIT 1")->fetch();
$depts = $pdo->query("SELECT * FROM contact_departments WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
$faq_header = $pdo->query("SELECT * FROM contact_faq_header LIMIT 1")->fetch();
$faqs = $pdo->query("SELECT * FROM contact_faqs WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
$map_overlay = $pdo->query("SELECT * FROM contact_map_overlay LIMIT 1")->fetch();
$cta = $pdo->query("SELECT * FROM contact_cta LIMIT 1")->fetch();
$main_info = $pdo->query("SELECT * FROM contact_main_info WHERE is_active = 1 LIMIT 1")->fetch();

// Group emergency and ussd
$emergency = null;
$ussd = null;
foreach ($emerg_ussd as $sec) {
    if ($sec['section_type'] == 'emergency') $emergency = $sec;
    else $ussd = $sec;
}

include 'includes/header.php';
?>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    @keyframes slowZoom {
        from { transform: scale(1); }
        to { transform: scale(1.1); }
    }
    .contact-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .contact-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
    .dept-card { transition: all 0.3s ease; }
    .dept-card:hover { border-color: #2563eb; background: #f8fafc; transform: scale(1.02); }
    .glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
    .icon-box {
        width: 64px; height: 64px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 14px;
        box-shadow: 0 6px 16px -4px rgba(37, 99, 235, 0.3);
    }
    .faq-item { border-bottom: 1px solid rgba(229,231,235,0.5); }
    .faq-item:last-child { border-bottom: none; }
    .faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.4s ease, padding 0.3s ease; }
    .faq-item.active .faq-answer { max-height: 500px; padding-top: 8px; }
    .faq-item.active .faq-chevron { transform: rotate(180deg); }
    .faq-chevron { transition: transform 0.3s ease; }
    @media (max-width: 640px) {
        .dept-grid { grid-template-columns: 1fr !important; }
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['image_url'] ?? ''); ?>" 
                 alt="Contact VVU" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-16 md:py-20">
            <div class="max-w-4xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-7 py-3 mb-6 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-xl">
                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-base md:text-lg font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['badge_text'] ?? ''); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-6 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['title_1'] ?? ''); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-2"><?php echo strip_tags($hero['title_2'] ?? ''); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-3xl mx-auto animate-fadeInUp font-medium drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($hero['description'] ?? ''); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Quick Contact Cards -->
    <div class="container relative z-20 -mt-10 mb-12 px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php foreach ($quick_cards as $index => $card): ?>
            <div class="contact-card bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 flex items-center gap-4 animate-fadeInUp" style="animation-delay:<?php echo 0.1 + ($index * 0.05); ?>s">
                <div class="icon-box bg-gradient-to-br <?php echo strip_tags($card['bg_gradient'] ?? 'from-blue-500 to-blue-700'); ?>">
                    <span class="material-symbols-outlined text-3xl text-white"><?php echo strip_tags($card['icon'] ?? ''); ?></span>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white"><?php echo strip_tags($card['title'] ?? ''); ?></h4>
                    <p class="text-xl text-gray-500 dark:text-gray-400"><?php echo strip_tags($card['description'] ?? ''); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Main Content: Form + Sidebar -->
    <div class="container px-4 mb-16">
        <div class="grid grid-cols-1 xl:grid-cols-5 gap-8">
            <!-- Contact Form -->
            <div class="xl:col-span-3 bg-white dark:bg-gray-800 p-8 md:p-10 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-4 mb-8">
                    <div class="icon-box bg-gradient-to-br from-blue-500 to-blue-700">
                        <span class="material-symbols-outlined text-3xl text-white">send</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white">Send Us a Message</h2>
                </div>

                <form action="contact_process.php" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xl font-bold text-gray-700 dark:text-gray-300 ml-1" for="name">Full Name</label>
                            <input type="text" id="name" name="name" placeholder="John Doe" required
                                   class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-xl font-medium transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xl font-bold text-gray-700 dark:text-gray-300 ml-1" for="email">Email Address</label>
                            <input type="email" id="email" name="email" placeholder="john@example.com" required
                                   class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-xl font-medium transition-all outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xl font-bold text-gray-700 dark:text-gray-300 ml-1" for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" placeholder="+233 XX XXX XXXX"
                                   class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-xl font-medium transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xl font-bold text-gray-700 dark:text-gray-300 ml-1" for="topic">Topic of Inquiry</label>
                            <select id="topic" name="topic" 
                                    class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-xl font-medium transition-all appearance-none outline-none">
                                <option>Admissions Question</option>
                                <option>General Inquiry</option>
                                <option>Technical Support</option>
                                <option>Alumni Relations</option>
                                <option>Financial Aid</option>
                                <option>Academic Affairs</option>
                                <option>Student Life</option>
                                <option>Chaplaincy</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xl font-bold text-gray-700 dark:text-gray-300 ml-1" for="message">Your Message</label>
                        <textarea id="message" name="message" placeholder="How can we help you today?" rows="5" required
                                  class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-xl font-medium transition-all resize-none outline-none"></textarea>
                    </div>

                    <button type="submit" class="w-full md:w-auto px-12 py-4.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-lg font-bold rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all transform hover:scale-[1.02] shadow-lg flex items-center justify-center gap-2">
                        Send Message
                        <span class="material-symbols-outlined text-2xl text-white">send</span>
                    </button>
                </form>
            </div>

            <!-- Sidebar -->
            <div class="xl:col-span-2 space-y-6">
                <!-- Postal Addresses -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700">
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-5">Postal Addresses</h3>
                    <div class="space-y-4">
                        <?php foreach ($postal as $p): ?>
                        <div class="flex gap-3 items-start">
                            <div class="shrink-0 w-12 h-12 rounded-xl <?php echo strip_tags($p['icon_bg_color'] ?? 'bg-blue-100'); ?> flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl"><?php echo strip_tags($p['icon'] ?? ''); ?></span>
                            </div>
                            <div>
                                <p class="text-lg font-bold text-gray-900 dark:text-white"><?php echo strip_tags($p['title'] ?? ''); ?></p>
                                <p class="text-xl text-gray-500 dark:text-gray-400"><?php echo strip_tags($p['description'] ?? ''); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700">
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-4">Follow Our Journey</h3>
                    <div class="flex gap-3">
                        <?php foreach ($socials as $s): ?>
                        <a href="<?php echo strip_tags($s['url'] ?? '#'); ?>" class="w-12 h-12 rounded-xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center <?php echo strip_tags($s['color_class'] ?? ''); ?> transition-all shadow-sm">
                            <i class="fa <?php echo strip_tags($s['icon'] ?? ''); ?> text-lg"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Emergency Support -->
                <?php if ($emergency): ?>
                <div class="bg-gradient-to-br from-blue-900 to-blue-950 p-6 rounded-2xl shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-yellow-400/10 rounded-full -mr-10 -mt-10"></div>
                    <div class="absolute bottom-0 left-0 w-16 h-16 bg-blue-400/10 rounded-full -ml-6 -mb-6"></div>
                    <h3 class="text-xl font-black text-white mb-3 relative z-10"><?php echo strip_tags($emergency['title'] ?? ''); ?></h3>
                    <p class="text-xl text-blue-100 leading-relaxed font-medium mb-4 relative z-10">
                        <?php echo strip_tags($emergency['description'] ?? ''); ?>
                    </p>
                    <a href="tel:<?php echo strip_tags($emergency['main_value'] ?? ''); ?>" class="w-full py-3.5 bg-yellow-400 text-blue-900 text-lg font-black rounded-xl hover:bg-yellow-300 transition-all shadow-lg flex items-center justify-center gap-2 relative z-10">
                        <span class="material-symbols-outlined text-2xl">emergency</span>
                        <?php echo strip_tags($emergency['btn_text'] ?? ''); ?>
                    </a>
                </div>
                <?php endif; ?>

                <!-- USSD Code -->
                <?php if ($ussd): ?>
                <div class="bg-gradient-to-br from-green-600 to-green-800 p-6 rounded-2xl shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-white/5 rounded-full -mr-8 -mt-8"></div>
                    <h3 class="text-xl font-black text-white mb-2 relative z-10"><?php echo strip_tags($ussd['title'] ?? ''); ?></h3>
                    <p class="text-xl text-green-100 font-medium mb-3 relative z-10"><?php echo strip_tags($ussd['description'] ?? ''); ?></p>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 text-center relative z-10">
                        <span class="text-4xl font-black text-yellow-300 tracking-wider"><?php echo strip_tags($ussd['main_value'] ?? ''); ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Department Contacts Section -->
    <section class="py-16 bg-white dark:bg-gray-800/50">
        <div class="container px-4">
            <?php if ($main_info): ?>
            <div class="max-w-4xl mx-auto mb-20 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 p-8 md:p-12 rounded-3xl border border-blue-100 dark:border-blue-800/50 shadow-xl overflow-hidden relative group">
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl group-hover:bg-blue-500/20 transition-all duration-700"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl group-hover:bg-indigo-500/20 transition-all duration-700"></div>
                
                <div class="relative z-10 text-center">
                    <h3 class="text-4xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($main_info['title']); ?></h3>
                    
                    <div class="space-y-6">
                        <div class="flex flex-col items-center justify-center gap-4 text-2xl">
                            <div class="flex flex-col gap-3">
                                <p class="text-gray-700 dark:text-gray-300 font-medium"><?php echo strip_tags($main_info['address_1']); ?></p>
                                <p class="text-gray-700 dark:text-gray-300 font-medium"><?php echo strip_tags($main_info['address_2']); ?></p>
                                <p class="text-gray-700 dark:text-gray-300 font-medium"><?php echo strip_tags($main_info['address_3']); ?></p>
                            </div>
                        </div>
                        
                        <div class="h-px bg-gradient-to-r from-transparent via-blue-200 dark:via-blue-800 to-transparent w-2/3 mx-auto my-8"></div>
                        
                        <div class="flex flex-col md:flex-row items-center justify-center gap-12">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-blue-600 dark:text-blue-400 shadow-inner">
                                    <span class="material-symbols-outlined text-3xl">call</span>
                                </div>
                                <div class="text-left">
                                    <p class="text-xs uppercase tracking-widest font-black text-blue-600 dark:text-blue-400 mb-1">Telephone</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo strip_tags($main_info['telephone']); ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shadow-inner">
                                    <span class="material-symbols-outlined text-3xl">mail</span>
                                </div>
                                <div class="text-left">
                                    <p class="text-xs uppercase tracking-widest font-black text-indigo-600 dark:text-indigo-400 mb-1">Email Address</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo strip_tags($main_info['email']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="max-w-3xl mx-auto text-center mb-12">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 mb-4 rounded-full bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800">
                    <span class="material-symbols-outlined text-lg text-blue-600">apartment</span>
                    <span class="text-base font-bold text-blue-600 uppercase tracking-wider"><?php echo strip_tags($dept_header['badge_text'] ?? ''); ?></span>
                </div>
                <h2 class="text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-3"><?php echo strip_tags($dept_header['title'] ?? ''); ?></h2>
                <p class="text-xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($dept_header['description'] ?? ''); ?></p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 dept-grid">
                <?php foreach ($depts as $d): ?>
                <div class="dept-card bg-white dark:bg-gray-800 p-8 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center gap-4 mb-5">
                        <span class="material-symbols-outlined text-3xl <?php echo strip_tags($d['icon_color'] ?? 'text-blue-600'); ?>"><?php echo strip_tags($d['icon'] ?? 'school'); ?></span>
                        <h4 class="text-xl md:text-2xl font-black text-gray-900 dark:text-white"><?php echo strip_tags($d['name'] ?? ''); ?></h4>
                    </div>
                    <div class="space-y-3">
                        <?php if ($d['phone_1']): ?>
                        <a href="tel:<?php echo str_replace(' ', '', $d['phone_1']); ?>" class="flex items-center gap-3 text-xl text-gray-600 dark:text-gray-400 hover:text-blue-600 transition-colors">
                            <span class="material-symbols-outlined text-lg <?php echo strip_tags($d['icon_color'] ?? 'text-blue-500'); ?>">call</span>
                            <?php echo strip_tags($d['phone_1']); ?>
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($d['phone_2']): ?>
                        <a href="tel:<?php echo str_replace(' ', '', $d['phone_2']); ?>" class="flex items-center gap-3 text-2xl text-gray-600 dark:text-gray-400 hover:text-blue-600 transition-colors">
                            <span class="material-symbols-outlined text-lg <?php echo strip_tags($d['icon_color'] ?? 'text-blue-500'); ?>">call</span>
                            <?php echo strip_tags($d['phone_2']); ?>
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($d['email']): ?>
                        <a href="mailto:<?php echo strip_tags($d['email']); ?>" class="flex items-center gap-3 text-2xl text-gray-600 dark:text-gray-400 hover:text-blue-600 transition-colors">
                            <span class="material-symbols-outlined text-lg <?php echo strip_tags($d['icon_color'] ?? 'text-blue-500'); ?>">mail</span>
                            <?php echo strip_tags($d['email']); ?>
                        </a>
                        <?php endif; ?>

                        <?php if ($d['name'] == 'FASS'): ?>
                        <p class="text-base text-gray-400 italic ml-7">WhatsApp Only</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 bg-gray-50 dark:bg-gray-900">
        <div class="container px-4">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 mb-4 rounded-full bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800">
                        <span class="material-symbols-outlined text-base text-amber-600">help</span>
                        <span class="text-lg font-bold text-amber-600 uppercase tracking-wider"><?php echo strip_tags($faq_header['badge_text'] ?? ''); ?></span>
                    </div>
                    <h2 class="text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-3"><?php echo strip_tags($faq_header['title'] ?? ''); ?></h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400 font-medium"><?php echo strip_tags($faq_header['description'] ?? ''); ?></p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <?php foreach ($faqs as $f): ?>
                    <div class="faq-item p-6 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-0" onclick="this.classList.toggle('active')">
                        <div class="flex items-center justify-between gap-4">
                            <h4 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white"><?php echo strip_tags($f['question'] ?? ''); ?></h4>
                            <span class="material-symbols-outlined text-3xl text-gray-400 faq-chevron">expand_more</span>
                        </div>
                        <div class="faq-answer">
                            <p class="text-2xl text-gray-600 dark:text-gray-400 leading-relaxed font-medium mt-2"><?php echo $f['answer']; // Answer can contain HTML links ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="relative h-[500px] w-full bg-gray-200 overflow-hidden outline-none border-none">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3969.8051786191494!2d-0.0886846241130638!3d5.740889931720516!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfdf70f6c2f9d14f%3A0x6b4fb0f97576571a!2sValley%20View%20University!5e0!3m2!1sen!2sgh!4v1709123456789!5m2!1sen!2sgh" 
            class="absolute inset-0 w-full h-full border-0" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
        <div class="absolute top-6 left-6 glass p-6 rounded-2xl shadow-xl max-w-sm hidden md:block">
            <h4 class="text-xl font-black text-gray-900 dark:text-white mb-2"><?php echo strip_tags($map_overlay['title'] ?? ''); ?></h4>
            <p class="text-xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                <?php echo strip_tags($map_overlay['description'] ?? ''); ?>
            </p>
            <a href="<?php echo strip_tags($map_overlay['link_url'] ?? '#'); ?>" class="mt-3 inline-flex items-center gap-2 text-base font-bold text-blue-600 hover:gap-3 transition-all">
                <?php echo strip_tags($map_overlay['link_text'] ?? ''); ?> <span class="material-symbols-outlined text-lg">arrow_forward</span>
            </a>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-16 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-yellow-500/10 rounded-full blur-[120px] -mr-48 -mt-48"></div>
        <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-blue-500/10 rounded-full blur-[120px] -ml-48 -mb-48"></div>
        
        <div class="container relative z-10 px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-white mb-4 leading-tight tracking-tight">
                    <?php echo strip_tags($cta['title'] ?? ''); ?> <br>
                    <span class="text-yellow-400 text-2xl sm:text-3xl block mt-2"><?php echo strip_tags($cta['subtitle'] ?? ''); ?></span>
                </h2>
                <p class="text-xl sm:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($cta['description'] ?? ''); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo strip_tags($cta['btn1_url'] ?? '#'); ?>" class="px-8 py-4 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-lg font-bold rounded-xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-2xl">info</span>
                        <?php echo strip_tags($cta['btn1_text'] ?? ''); ?>
                    </a>
                    <a href="<?php echo strip_tags($cta['btn2_url'] ?? '#'); ?>" class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white text-lg font-bold rounded-xl transition-all backdrop-blur-md border border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-2xl">how_to_reg</span>
                        <?php echo strip_tags($cta['btn2_text'] ?? ''); ?>
                    </a>
                </div>
                
                <div class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-8 border-t border-white/10 pt-10">
                    <div>
                        <div class="text-5xl font-black text-yellow-400 mb-1"><?php echo strip_tags($cta['stat1_value'] ?? ''); ?></div>
                        <div class="text-blue-200 uppercase tracking-widest text-base font-bold"><?php echo strip_tags($cta['stat1_label'] ?? ''); ?></div>
                    </div>
                    <div>
                        <div class="text-5xl font-black text-yellow-400 mb-1"><?php echo strip_tags($cta['stat2_value'] ?? ''); ?></div>
                        <div class="text-blue-200 uppercase tracking-widest text-base font-bold"><?php echo strip_tags($cta['stat2_label'] ?? ''); ?></div>
                    </div>
                    <div>
                        <div class="text-5xl font-black text-yellow-400 mb-1"><?php echo strip_tags($cta['stat3_value'] ?? ''); ?></div>
                        <div class="text-blue-200 uppercase tracking-widest text-lg font-bold"><?php echo strip_tags($cta['stat3_label'] ?? ''); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>