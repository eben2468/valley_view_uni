<?php
$page_title = "Our History - Valley View University";
$active_page = "about";
require_once 'includes/db_connect.php';

// Fetch data from database
$hero = $pdo->query("SELECT * FROM history_hero WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$overview = $pdo->query("SELECT * FROM history_overview WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$milestones = $pdo->query("SELECT * FROM history_milestones WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$community = $pdo->query("SELECT * FROM history_community WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$cta = $pdo->query("SELECT * FROM history_cta WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();

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
    .history-card {
        transition: all 0.3s ease;
    }
    .history-card:hover {
        transform: translateY(-10px);
    }
    .timeline-line {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        width: 4px;
        height: 100%;
        background: linear-gradient(to bottom, #2563eb, #fbbf24);
        border-radius: 2px;
    }
    @media (max-width: 768px) {
        .timeline-line {
            left: 20px;
        }
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image_url'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuDlpqAxUpsNTDcRAQIlxSNJQ8SojHcCq-EJUtGi1fL4Ks81Fov4uUGjJrsaziEer_Gb2EzOGjNFYzIvSXn8BgUcJTOJ60Ln7ogU_UGxoqMGsnyt1wEkW1636dKPzO17EdOyoT7GZLZ7-VADxDD39JsJ31e3yOzPXyo_69Va5FW22seP0WfrtmjXil3J2I1YDq8D9rg2aEcx572kdiJMjcAlfXPO3bQ46H2PtAA2WpbTZN8cvvoWSPdLKzgJaKL0f6lY99R4t-07NQsh'); ?>" 
                 alt="VVU History" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo $hero ? strip_tags($hero['page_subtitle']) : 'Our Legacy'; ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo $hero ? strip_tags($hero['hero_title']) : 'The Journey'; ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo $hero ? strip_tags($hero['hero_subtitle']) : 'Of Excellence'; ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo $hero ? strip_tags($hero['hero_description']) : '"From our humble beginnings in 1979 to becoming Ghana\'s first chartered private university, our history is a testament to faith, vision, and academic brilliance."'; ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Historical Overview -->
    <section class="vm-section vm-section--white">
        <div class="container">
            <div class="vm-split">
                <div>
                    <span class="vm-kicker">Where It Began</span>
                    <div class="vm-heading vm-heading--left" role="heading" aria-level="2"><?php echo htmlspecialchars($overview ? strip_tags($overview['section_title']) : 'A Visionary Beginning'); ?></div>
                    <div style="margin-top: 28px;">
                        <p class="vm-body"><?php echo $overview ? nl2br(htmlspecialchars(strip_tags($overview['paragraph_1']))) : 'Valley View University was established in 1979 by the West African Union Mission of Seventh-day Adventists. What started as a focused mission to provide quality Christian education has grown into a beacon of higher learning in West Africa.'; ?></p>
                        <p class="vm-body"><?php echo $overview ? nl2br(htmlspecialchars(strip_tags($overview['paragraph_2']))) : 'In 1997, the institution was absorbed into the Adventist University system operated by the West Central African Division of Seventh-day Adventists, headquartered in Abidjan, Cote d\'Ivoire, further strengthening its global academic ties.'; ?></p>
                    </div>
                    <div class="vm-figures">
                        <div class="vm-figure">
                            <span class="vm-figure-value"><?php echo htmlspecialchars($overview ? strip_tags($overview['founded_year']) : '1979'); ?></span>
                            <span class="vm-figure-label">Founded</span>
                        </div>
                        <div class="vm-figure">
                            <span class="vm-figure-value"><?php echo htmlspecialchars($overview ? strip_tags($overview['chartered_year']) : '2006'); ?></span>
                            <span class="vm-figure-label">Chartered</span>
                        </div>
                    </div>
                </div>
                <div class="vm-frame">
                    <div class="vm-frame-media">
                        <img src="<?php echo strip_tags($overview['overview_image_url'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuDlpqAxUpsNTDcRAQIlxSNJQ8SojHcCq-EJUtGi1fL4Ks81Fov4uUGjJrsaziEer_Gb2EzOGjNFYzIvSXn8BgUcJTOJ60Ln7ogU_UGxoqMGsnyt1wEkW1636dKPzO17EdOyoT7GZLZ7-VADxDD39JsJ31e3yOzPXyo_69Va5FW22seP0WfrtmjXil3J2I1YDq8D9rg2aEcx572kdiJMjcAlfXPO3bQ46H2PtAA2WpbTZN8cvvoWSPdLKzgJaKL0f6lY99R4t-07NQsh'); ?>"
                             alt="Valley View University in its founding era" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Historical Milestones -->
    <?php if (!empty($milestones)): ?>
    <section class="vm-section vm-section--tint">
        <div class="container">
            <div class="vm-head">
                <span class="vm-kicker">Our Journey</span>
                <div class="vm-heading" role="heading" aria-level="2">Historical Milestones</div>
                <p class="vm-lead">Tracing our path from a mission-driven college to a premier chartered university.</p>
            </div>

            <div class="vm-timeline">
                <?php foreach ($milestones as $milestone): ?>
                <div class="vm-tl-item">
                    <span class="vm-tl-dot" aria-hidden="true"></span>
                    <div class="vm-card vm-tl-card">
                        <span class="vm-tl-year"><?php echo htmlspecialchars(strip_tags($milestone['year'])); ?></span>
                        <div class="vm-card-title" role="heading" aria-level="3"><?php echo htmlspecialchars(strip_tags($milestone['milestone_title'])); ?></div>
                        <p class="vm-card-text"><?php echo htmlspecialchars(strip_tags($milestone['milestone_description'])); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- A Global Community -->
    <section class="vm-section vm-section--white">
        <div class="container">
            <div class="vm-head" style="margin-bottom: 40px;">
                <span class="vm-kicker"><span class="material-symbols-outlined">public</span>Today</span>
                <div class="vm-heading" role="heading" aria-level="2"><?php echo htmlspecialchars($community ? strip_tags($community['section_title']) : 'A Global Community'); ?></div>
                <p class="vm-lead"><?php echo htmlspecialchars($community ? strip_tags($community['section_description']) : 'Today, Valley View University serves undergraduate and graduate students from all over the world. We admit qualified students regardless of their religious background, provided they accept the Christian principles and lifestyle that form the basis of our operations.'); ?></p>
            </div>
            <div class="vm-grid vm-wrap">
                <?php
                $community_features = [
                    ['title' => $community['feature_1_title'] ?? 'Global',    'label' => $community['feature_1_label'] ?? 'Reach',      'icon' => 'public'],
                    ['title' => $community['feature_2_title'] ?? 'Inclusive', 'label' => $community['feature_2_label'] ?? 'Community',  'icon' => 'diversity_3'],
                    ['title' => $community['feature_3_title'] ?? 'Chartered', 'label' => $community['feature_3_label'] ?? 'Excellence', 'icon' => 'workspace_premium'],
                ];
                foreach ($community_features as $feat): ?>
                <div class="vm-card" style="align-items: center; text-align: center;">
                    <span class="vm-icon" style="margin-bottom: 16px;"><span class="material-symbols-outlined"><?php echo $feat['icon']; ?></span></span>
                    <div class="vm-card-title vm-card-title--plain" role="heading" aria-level="3" style="font-size: 26px; margin-bottom: 4px;"><?php echo htmlspecialchars(strip_tags($feat['title'])); ?></div>
                    <span class="vm-card-label" style="margin: 0;"><?php echo htmlspecialchars(strip_tags($feat['label'])); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Call to action -->
    <section class="vm-cta">
        <div class="container">
            <div class="vm-cta-head">
                <div class="vm-cta-heading" role="heading" aria-level="2"><?php echo htmlspecialchars($cta ? trim(strip_tags($cta['cta_title_1']) . ' ' . strip_tags($cta['cta_title_2'])) : 'Be Part of Our Future History'); ?></div>
                <p class="vm-cta-lead"><?php echo htmlspecialchars($cta ? strip_tags($cta['cta_description']) : 'Join a legacy of excellence and innovation. Your journey at Valley View University starts here.'); ?></p>
                <div class="vm-actions">
                    <a href="<?php echo $cta ? strip_tags($cta['button_1_url']) : 'admissions.php'; ?>" class="vm-btn vm-btn--gold">
                        <span class="material-symbols-outlined">school</span><?php echo htmlspecialchars($cta ? strip_tags($cta['button_1_text']) : 'Apply Now'); ?>
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
