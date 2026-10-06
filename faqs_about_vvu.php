<?php
require_once 'includes/db_connect.php';
$page_title = "FAQs - Valley View University";
$active_page = "about";

// Fetch FAQ data
$faq_hero = $pdo->query("SELECT * FROM faq_hero WHERE is_active = 1 LIMIT 1")->fetch();
$faq_trending = $pdo->query("SELECT * FROM faq_trending WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
$faq_categories = $pdo->query("SELECT * FROM faq_categories WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
$faqs_all = $pdo->query("SELECT * FROM faqs WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
$faq_docs = $pdo->query("SELECT * FROM faq_docs WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();
$faq_support = $pdo->query("SELECT * FROM faq_support WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll();

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
    .faq-card {
        transition: all 0.3s ease;
    }
    .faq-card:hover {
        transform: translateY(-5px);
    }
    details summary::-webkit-details-marker {
        display: none;
    }
    /* "Need Immediate Help?" support cards — compact */
    .fq-support { max-width: 1040px; gap: 18px; }
    .fq-support .vm-card { padding: 22px 22px; border-radius: 16px; }
    .fq-support .vm-icon { width: 40px; height: 40px; }
    .fq-support .vm-icon .material-symbols-outlined { font-size: 20px; }
    .fq-support .vm-card-title { font-size: 18px; font-weight: 600; margin-bottom: 8px; }
    .fq-support .vm-card-text { font-size: 14.5px; line-height: 1.6; }
    .fq-support .vm-card-foot { padding-top: 14px; }
    .fq-support .vm-link { font-size: 14px; }
    .fq-support .vm-link .material-symbols-outlined { font-size: 18px; }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($faq_hero['hero_image_url'] ?? 'vvu_faq_hero_1766876441891.png'); ?>" 
                 alt="VVU Help Desk" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($faq_hero['badge_text'] ?? 'Support Center'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($faq_hero['title_black'] ?? 'Frequently Asked'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($faq_hero['title_gradient'] ?? 'Questions'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($faq_hero['description'] ?? '"Find answers to common inquiries about admissions, academics, and life at Valley View University."'); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Trending Questions -->
    <?php if (!empty($faq_trending)): ?>
    <section class="vm-section vm-section--white">
        <div class="container">
            <div class="vm-head">
                <span class="vm-kicker"><span class="material-symbols-outlined">trending_up</span>Quick Help</span>
                <div class="vm-heading" role="heading" aria-level="2">Trending Questions</div>
                <p class="vm-lead">The questions we are asked most often right now.</p>
            </div>
            <div class="vm-grid vm-wrap">
                <?php foreach ($faq_trending as $trend): ?>
                <div class="vm-card vm-card--corner">
                    <div class="vm-card-title" role="heading" aria-level="3"><?php echo htmlspecialchars(strip_tags($trend['question'])); ?></div>
                    <p class="vm-card-text"><?php echo htmlspecialchars(strip_tags($trend['answer'])); ?></p>
                    <span class="vm-card-corner" aria-hidden="true"><span class="material-symbols-outlined"><?php echo strip_tags($trend['icon']); ?></span></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- FAQ explorer: one tab per category -->
    <section class="vm-section vm-section--tint">
        <div class="container">
            <div class="vm-head" style="margin-bottom: 32px;">
                <span class="vm-kicker">Browse by Topic</span>
                <div class="vm-heading" role="heading" aria-level="2">Support Categories</div>
                <p class="vm-lead">Select a category to find specialised answers.</p>
            </div>

            <?php if (!empty($faq_categories)): ?>
            <div class="vm-tabs" role="tablist">
                <?php foreach ($faq_categories as $index => $cat): $slug = strip_tags($cat['category_slug']); ?>
                <button type="button" class="vm-tab<?php echo $index === 0 ? ' is-active' : ''; ?>" role="tab"
                        aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                        data-faq-tab="<?php echo htmlspecialchars($slug); ?>">
                    <span class="material-symbols-outlined"><?php echo strip_tags($cat['icon']); ?></span>
                    <?php echo htmlspecialchars(strip_tags($cat['category_name'])); ?>
                </button>
                <?php endforeach; ?>
            </div>

            <?php foreach ($faq_categories as $index => $cat):
                $cat_faqs = array_filter($faqs_all, function ($f) use ($cat) {
                    return $f['category_id'] == $cat['id'];
                });
            ?>
            <div class="vm-faqs<?php echo $index === 0 ? '' : ' hidden'; ?>" role="tabpanel" data-faq-panel="<?php echo htmlspecialchars(strip_tags($cat['category_slug'])); ?>">
                <?php if (!empty($cat_faqs)): ?>
                    <?php foreach ($cat_faqs as $faq): ?>
                    <details class="vm-faq">
                        <summary>
                            <span class="vm-faq-q"><?php echo htmlspecialchars(strip_tags($faq['question'])); ?></span>
                            <span class="vm-faq-chev"><span class="material-symbols-outlined">expand_more</span></span>
                        </summary>
                        <div class="vm-faq-a"><?php echo nl2br(htmlspecialchars(strip_tags($faq['answer']))); ?></div>
                    </details>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="vm-empty" style="margin-top: 0;">
                        <span class="material-symbols-outlined">help_center</span>
                        <div class="vm-empty-title">No questions in this category yet</div>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Document Center -->
    <?php if (!empty($faq_docs)): ?>
    <section class="vm-section vm-section--white">
        <div class="container">
            <div class="vm-head">
                <span class="vm-kicker">Downloads</span>
                <div class="vm-heading" role="heading" aria-level="2">Document Center</div>
                <p class="vm-lead">Easy access to the most requested forms and guides.</p>
            </div>
            <div class="vm-grid vm-grid--4">
                <?php foreach ($faq_docs as $doc):
                    // A bullet saved with the wrong encoding shows as "ÔÇó"
                    $file_info = str_replace('ÔÇó', '•', strip_tags((string) $doc['file_info']));
                ?>
                <a href="<?php echo strip_tags($doc['file_url']); ?>" class="vm-card">
                    <span class="vm-icon" style="margin-bottom: 18px;"><span class="material-symbols-outlined"><?php echo strip_tags($doc['icon']); ?></span></span>
                    <div class="vm-card-title vm-card-title--plain" role="heading" aria-level="3" style="margin-bottom: 4px;"><?php echo htmlspecialchars(strip_tags($doc['title'])); ?></div>
                    <?php if (trim($file_info) !== ''): ?>
                    <p class="vm-card-text" style="font-size: 14.5px;"><?php echo htmlspecialchars($file_info); ?></p>
                    <?php endif; ?>
                    <div class="vm-card-foot">
                        <span class="vm-link">Download <span class="material-symbols-outlined">download</span></span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Immediate help + still have questions -->
    <section class="vm-cta">
        <div class="container">
            <div class="vm-cta-head">
                <span class="vm-kicker">We're Here for You</span>
                <div class="vm-cta-heading" role="heading" aria-level="2">Need Immediate Help?</div>
                <p class="vm-cta-lead">Our support networks are active 24/7 to ensure your university experience is smooth and rewarding.</p>
            </div>

            <?php if (!empty($faq_support)): ?>
            <div class="vm-grid vm-wrap fq-support" style="margin-top: 32px;">
                <?php foreach ($faq_support as $sup): ?>
                <div class="vm-card vm-card--glass">
                    <span class="vm-icon" style="margin-bottom: 12px;"><span class="material-symbols-outlined"><?php echo strip_tags($sup['icon']); ?></span></span>
                    <div class="vm-card-title vm-card-title--plain" role="heading" aria-level="3"><?php echo htmlspecialchars(strip_tags($sup['title'])); ?></div>
                    <p class="vm-card-text"><?php echo htmlspecialchars(strip_tags($sup['description'])); ?></p>
                    <div class="vm-card-foot">
                        <a href="<?php echo strip_tags($sup['btn_link']); ?>" class="vm-link"><?php echo htmlspecialchars(strip_tags($sup['btn_text'])); ?> <span class="material-symbols-outlined">arrow_forward</span></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="vm-cta-head" style="margin-top: 52px;">
                <div class="vm-cta-heading" role="heading" aria-level="2" style="font-size: clamp(24px, 2.8vw, 32px);">Still have questions?</div>
                <p class="vm-cta-lead">If you couldn't find the answer you were looking for, please reach out. Our team is always ready to help.</p>
                <div class="vm-actions">
                    <a href="contact_us.php" class="vm-btn vm-btn--gold"><span class="material-symbols-outlined">mail</span>Contact Us</a>
                    <a href="tel:+233307011867" class="vm-btn vm-btn--ghost"><span class="material-symbols-outlined">call</span>Call Admissions</a>
                </div>
            </div>
        </div>
    </section>

    <script>
    // FAQ category tabs
    (function () {
        var tabs = document.querySelectorAll('[data-faq-tab]');
        var panels = document.querySelectorAll('[data-faq-panel]');
        Array.prototype.forEach.call(tabs, function (tab) {
            tab.addEventListener('click', function () {
                var slug = tab.getAttribute('data-faq-tab');
                Array.prototype.forEach.call(tabs, function (t) {
                    var on = t === tab;
                    t.classList.toggle('is-active', on);
                    t.setAttribute('aria-selected', on ? 'true' : 'false');
                });
                Array.prototype.forEach.call(panels, function (p) {
                    p.classList.toggle('hidden', p.getAttribute('data-faq-panel') !== slug);
                });
            });
        });
    })();
    </script>
</main>

<?php
include 'includes/footer.php';
?>