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

<link rel="stylesheet" href="css/vvu-modern.css?v=1.0">
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

    <?php
    // Stored descriptions carry <strong> tags with old theme colour classes.
    // Keep the emphasis, drop everything else.
    $vm_rich = static function ($html) {
        $html = strip_tags((string) $html, '<strong><b><em><br>');
        return preg_replace('/<(strong|b|em)\b[^>]*>/i', '<$1>', $html);
    };
    ?>
    <!-- Official Accreditation -->
    <section class="vm-section vm-section--white">
        <div class="container">
            <div class="vm-head">
                <span class="vm-kicker">Quality Assured</span>
                <div class="vm-heading" role="heading" aria-level="2">Official Accreditation</div>
                <p class="vm-lead">Our programs are rigorously evaluated and accredited by leading educational authorities.</p>
            </div>

            <div class="vm-grid vm-wrap <?php echo count($cards) === 2 ? 'vm-grid--2' : (count($cards) >= 4 ? 'vm-grid--4' : ''); ?>">
                <?php foreach ($cards as $card): ?>
                <div class="vm-card vm-card--corner">
                    <div class="vm-card-title" role="heading" aria-level="3"><?php echo htmlspecialchars(strip_tags($card['title'])); ?></div>
                    <p class="vm-card-text"><?php echo $vm_rich($card['description']); ?></p>
                    <span class="vm-card-corner" aria-hidden="true"><span class="material-symbols-outlined"><?php echo strip_tags($card['icon']); ?></span></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Presidential Charter -->
    <section class="vm-section vm-section--tint">
        <div class="container">
            <div class="vm-split">
                <div>
                    <span class="vm-kicker"><?php echo htmlspecialchars($charter ? strip_tags($charter['badge_text']) : 'A Historic Milestone'); ?></span>
                    <div class="vm-heading vm-heading--left" role="heading" aria-level="2"><?php echo htmlspecialchars($charter ? strip_tags($charter['section_title']) : 'The Presidential Charter'); ?></div>
                    <div style="margin-top: 28px;">
                        <p class="vm-body"><?php echo $charter ? nl2br(htmlspecialchars(strip_tags($charter['paragraph_1']))) : 'In January 2006, Valley View University was granted a Presidential Charter by His Excellency, Mr. J. A. Kufuor, President of the Republic of Ghana.'; ?></p>
                        <p class="vm-body"><?php echo $charter ? nl2br($vm_rich($charter['paragraph_2'])) : 'This historic achievement made VVU the <strong>first Chartered Private University in Ghana</strong>, granting us the rights and privileges to operate as an autonomous degree-granting institution.'; ?></p>
                    </div>
                    <p class="vm-quote" style="margin-top: 26px; font-size: 18px;"><?php echo htmlspecialchars($charter ? strip_tags($charter['quote']) : '"Chartered status is granted after careful scrutiny of an institution\'s statutes, examination procedures, and quality assurance standards."'); ?></p>
                </div>
                <div class="vm-panel">
                    <span class="vm-panel-icon"><span class="material-symbols-outlined">workspace_premium</span></span>
                    <div class="vm-panel-title" role="heading" aria-level="3"><?php echo htmlspecialchars($charter ? strip_tags($charter['achievement_text']) : 'First Chartered Private University'); ?></div>
                    <span class="vm-panel-text"><?php echo htmlspecialchars($charter ? strip_tags($charter['achievement_location']) : 'Ghana • 2006'); ?></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Memberships & Linkages -->
    <section class="vm-section vm-section--white">
        <div class="container">
            <div class="vm-head">
                <span class="vm-kicker">Our Network</span>
                <div class="vm-heading" role="heading" aria-level="2">Memberships &amp; Linkages</div>
                <p class="vm-lead">The professional bodies we belong to and the universities we partner with around the world.</p>
            </div>

            <div class="vm-grid vm-grid--2 vm-wrap" style="align-items: start;">
                <div>
                    <div class="vm-group-head">
                        <span class="vm-icon"><span class="material-symbols-outlined">groups</span></span>
                        <div class="vm-group-title" role="heading" aria-level="3">Professional Memberships</div>
                    </div>
                    <ul class="vm-list">
                        <?php foreach ($memberships as $membership): ?>
                        <li>
                            <span class="vm-icon vm-icon--soft"><span class="material-symbols-outlined">check</span></span>
                            <span>
                                <span class="vm-list-title"><?php echo htmlspecialchars(strip_tags($membership['organization_name'])); ?></span>
                                <?php if (trim(strip_tags((string) $membership['organization_description'])) !== ''): ?>
                                <span class="vm-list-text"><?php echo htmlspecialchars(strip_tags($membership['organization_description'])); ?></span>
                                <?php endif; ?>
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div>
                    <div class="vm-group-head">
                        <span class="vm-icon"><span class="material-symbols-outlined">link</span></span>
                        <div class="vm-group-title" role="heading" aria-level="3">Global Linkages</div>
                    </div>
                    <ul class="vm-list">
                        <?php foreach ($linkages as $linkage): ?>
                        <li>
                            <span class="vm-icon vm-icon--soft"><span class="material-symbols-outlined">school</span></span>
                            <span>
                                <span class="vm-list-title"><?php echo htmlspecialchars(strip_tags($linkage['organization_name'])); ?></span>
                                <?php if (trim(strip_tags((string) $linkage['location'])) !== ''): ?>
                                <span class="vm-list-text"><?php echo htmlspecialchars(strip_tags($linkage['location'])); ?></span>
                                <?php endif; ?>
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to action -->
    <section class="vm-cta">
        <div class="container">
            <div class="vm-cta-head">
                <div class="vm-cta-heading" role="heading" aria-level="2"><?php echo htmlspecialchars($cta ? trim(strip_tags($cta['cta_title_1']) . ' ' . strip_tags($cta['cta_title_2'])) : 'Committed to Academic Excellence'); ?></div>
                <p class="vm-cta-lead"><?php echo htmlspecialchars($cta ? strip_tags($cta['cta_description']) : 'Our accreditation ensures that your degree is recognized and valued globally.'); ?></p>
                <div class="vm-actions">
                    <a href="<?php echo $cta ? strip_tags($cta['button_1_url']) : 'academic_programs_overview.php'; ?>" class="vm-btn vm-btn--gold">
                        <span class="material-symbols-outlined">school</span><?php echo htmlspecialchars($cta ? strip_tags($cta['button_1_text']) : 'Explore Programs'); ?>
                    </a>
                    <a href="<?php echo $cta ? strip_tags($cta['button_2_url']) : 'contact_us.php'; ?>" class="vm-btn vm-btn--ghost">
                        <span class="material-symbols-outlined">mail</span><?php echo htmlspecialchars($cta ? strip_tags($cta['button_2_text']) : 'Contact Us'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>