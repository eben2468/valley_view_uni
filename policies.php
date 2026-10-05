<?php
$page_title = "University Policies - Valley View University";
$active_page = "about";
require_once 'includes/db_connect.php';

// Fetch data from database
$page_key = 'policies';
$stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = ? AND is_active = 1");
$stmt->execute([$page_key]);
$hero = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = ? ORDER BY display_order");
$stmt->execute([$page_key]);
$sections = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = ? AND is_active = 1 ORDER BY section_key, display_order");
$stmt->execute([$page_key]);
$all_items = $stmt->fetchAll();

$grouped_items = [];
foreach ($all_items as $item) {
    if ($item['extra_data']) {
        $item['documents'] = json_decode($item['extra_data'], true) ?: [];
    }
    $grouped_items[$item['section_key']][] = $item;
}

// ---- Policy search -------------------------------------------------------
// The box used to be decorative markup with no form and no handler. Filtering
// happens here so the search still works with JavaScript disabled; the script
// at the bottom of the page layers instant filtering on top.
$q = trim((string) ($_GET['q'] ?? ''));

/** Everything about an item that a visitor might reasonably search for. */
function vvu_policy_haystack(array $item) {
    $parts = [
        $item['item_title'] ?? '',
        $item['item_subtitle'] ?? '',
        $item['item_description'] ?? '',
    ];
    foreach ($item['documents'] ?? [] as $doc) {
        $parts[] = $doc['title'] ?? '';
    }
    return mb_strtolower(strip_tags(implode(' ', $parts)));
}

/**
 * Non-matching cards are HIDDEN, never removed. If PHP dropped them from the
 * markup, clearing the box in the browser could not bring them back — they
 * would not be in the DOM to un-hide.
 */
function vvu_policy_hidden(array $item, $q) {
    if ($q === '') {
        return false;
    }
    return mb_strpos(vvu_policy_haystack($item), mb_strtolower($q)) === false;
}

$search_total = 0;
if ($q !== '') {
    foreach (['framework', 'quick_links'] as $key) {
        foreach ($grouped_items[$key] ?? [] as $item) {
            if (!vvu_policy_hidden($item, $q)) {
                $search_total++;
            }
        }
    }
}

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
    /* Materialize styles bare <input> elements with a grey bottom border and a
       focus box-shadow, which showed as a stray line under the placeholder and
       fought the rounded pill. Reset it just for this field. */
    .policy-search-input,
    .policy-search-input:focus,
    input[type="search"].policy-search-input:focus:not([readonly]) {
        border: none !important;
        border-bottom: none !important;
        box-shadow: none !important;
        outline: none !important;
        height: auto !important;
        margin: 0 !important;
        background-color: transparent !important;
        /* Materialize targets input[type=search] (specificity 0,1,1), which beats
           a Tailwind text-* class (0,1,0) — so the size must be set here or the
           field renders at Materialize's 1rem, which is 10px on this page. */
        font-size: 17px !important;
        line-height: 1.4 !important;
        font-weight: 500;
    }
    .policy-search-input::placeholder { color: #94a3b8; opacity: 1; font-weight: 400; }
    @media (max-width: 640px) {
        .policy-search-input { font-size: 15px !important; }
    }
    /* Hide the browser's own clear cross — we render our own */
    .policy-search-input::-webkit-search-cancel-button { -webkit-appearance: none; appearance: none; }

    /* Brief highlight on the card the search jumped to */
    @keyframes policyHit {
        0%   { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.55); }
        70%  { box-shadow: 0 0 0 14px rgba(37, 99, 235, 0); }
        100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); }
    }
    .policy-hit { animation: policyHit 1.4s ease-out; border-radius: 1.5rem; }

    .sr-only {
        position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
        overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0;
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
    .policy-card {
        transition: all 0.3s ease;
    }
    .policy-card:hover {
        transform: translateY(-10px);
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image'] ?? 'uploads/strategy/img_1770600004_69893644a6dec.jpg'); ?>" 
                 alt="University Policies" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['hero_badge'] ?? 'Governance & Standards'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['hero_title'] ?? 'University'); ?> <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo strip_tags($hero['hero_subtitle'] ?? 'Policies'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($hero['hero_description'] ?? '"A comprehensive guide to the principles, regulations, and procedures that govern Valley View University. We ensure transparency and fairness in all our operations."'); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Policy Framework -->
    <?php
    $framework_section = array_values(array_filter($sections, fn($s) => $s['section_key'] === 'framework'))[0] ?? null;
    if ($framework_section):
    ?>
    <section class="vm-section vm-section--white">
        <div class="container">
            <div class="vm-head">
                <span class="vm-kicker">Governance</span>
                <div class="vm-heading" role="heading" aria-level="2"><?php echo htmlspecialchars(strip_tags($framework_section['section_title'])); ?></div>
                <?php if (trim(strip_tags((string) $framework_section['section_subtitle'])) !== ''): ?>
                <p class="vm-lead"><?php echo htmlspecialchars(strip_tags($framework_section['section_subtitle'])); ?></p>
                <?php endif; ?>
            </div>

            <div class="vm-grid">
                <?php foreach ($grouped_items['framework'] ?? [] as $category): ?>
                <div class="vm-card<?php echo vvu_policy_hidden($category, $q) ? ' hidden' : ''; ?>"
                     data-policy-searchable
                     data-search-text="<?php echo htmlspecialchars(vvu_policy_haystack($category)); ?>">
                    <span class="vm-icon" style="margin-bottom: 18px;"><span class="material-symbols-outlined"><?php echo strip_tags($category['item_icon']); ?></span></span>
                    <div class="vm-card-title" role="heading" aria-level="3"><?php echo htmlspecialchars(strip_tags($category['item_title'])); ?></div>
                    <p class="vm-card-text"><?php echo htmlspecialchars(strip_tags($category['item_description'])); ?></p>
                    <?php if (!empty($category['documents'])): ?>
                    <div class="vm-docs">
                        <?php foreach ($category['documents'] as $doc): ?>
                        <a href="<?php echo strip_tags($doc['url']); ?>" download class="vm-doc">
                            <span class="material-symbols-outlined"><?php echo strip_tags($doc['icon'] ?? 'picture_as_pdf'); ?></span>
                            <span><?php echo htmlspecialchars(strip_tags($doc['title'])); ?></span>
                            <span class="material-symbols-outlined vm-doc-go" aria-hidden="true">download</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Search & Quick Access -->
    <?php
    $links_section = array_values(array_filter($sections, fn($s) => $s['section_key'] === 'quick_links'))[0] ?? null;
    if ($links_section):
    ?>
    <section class="vm-section vm-section--tint">
        <div class="container">
            <div class="vm-head" style="margin-bottom: 32px;">
                <span class="vm-kicker">Find a Policy</span>
                <div class="vm-heading" role="heading" aria-level="2"><?php echo htmlspecialchars(strip_tags($links_section['section_title'])); ?></div>
                <?php if (trim(strip_tags((string) $links_section['section_subtitle'])) !== ''): ?>
                <p class="vm-lead"><?php echo htmlspecialchars(strip_tags($links_section['section_subtitle'])); ?></p>
                <?php endif; ?>
            </div>

            <form method="GET" action="policies.php" id="policySearchForm" role="search" class="vm-search">
                <label for="policySearch" class="sr-only">Search policies</label>
                <span class="material-symbols-outlined" aria-hidden="true">search</span>
                <input type="search" id="policySearch" name="q" autocomplete="off"
                       value="<?php echo htmlspecialchars($q); ?>"
                       placeholder="Search policies — try Governance, Academic, Staff…">
                <!-- Clear button: only shown once there is something to clear -->
                <button type="button" id="policySearchClear" class="vm-search-clear <?php echo $q === '' ? 'hidden' : 'flex'; ?>" aria-label="Clear search">
                    <span class="material-symbols-outlined">close</span>
                </button>
                <button type="submit" class="vm-btn vm-btn--navy">Search</button>
            </form>

            <!-- Live result summary -->
            <p id="policySearchStatus" role="status" aria-live="polite" class="vm-search-status <?php echo $q === '' ? 'hidden' : ''; ?>">
                <?php if ($q !== ''): ?>
                    <?php echo $search_total; ?> result<?php echo $search_total === 1 ? '' : 's'; ?>
                    for &ldquo;<span class="font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($q); ?></span>&rdquo;
                <?php endif; ?>
            </p>

            <!-- Empty state -->
            <div id="policySearchEmpty" class="vm-empty <?php echo ($q !== '' && $search_total === 0) ? '' : 'hidden'; ?>">
                <span class="material-symbols-outlined">search_off</span>
                <div class="vm-empty-title">No policies matched</div>
                <p class="vm-empty-text">Try a broader word, or <button type="button" id="policySearchReset">clear the search</button> to see everything.</p>
            </div>

            <div class="vm-grid" style="margin-top: 48px;">
                <?php foreach ($grouped_items['quick_links'] ?? [] as $link): ?>
                <div class="vm-card vm-card--corner<?php echo vvu_policy_hidden($link, $q) ? ' hidden' : ''; ?>"
                     data-policy-searchable
                     data-search-text="<?php echo htmlspecialchars(vvu_policy_haystack($link)); ?>">
                    <div class="vm-card-title" role="heading" aria-level="3"><?php echo htmlspecialchars(strip_tags($link['item_title'])); ?></div>
                    <p class="vm-card-text"><?php echo htmlspecialchars(strip_tags($link['item_description'])); ?></p>
                    <?php if (!empty($link['item_link'])): ?>
                    <div class="vm-card-foot">
                        <a href="<?php echo strip_tags($link['item_link']); ?>" class="vm-link">
                            <?php echo htmlspecialchars(strip_tags($link['item_subtitle'] ?: 'Read more')); ?> <span class="material-symbols-outlined">arrow_forward</span>
                        </a>
                    </div>
                    <?php endif; ?>
                    <span class="vm-card-corner" aria-hidden="true"><span class="material-symbols-outlined"><?php echo strip_tags($link['item_icon']); ?></span></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Call to action -->
    <section class="vm-cta">
        <div class="container">
            <div class="vm-cta-head">
                <div class="vm-cta-heading" role="heading" aria-level="2"><?php echo htmlspecialchars(trim(strip_tags($hero['cta_title'] ?? 'Committed to') . ' ' . strip_tags($hero['cta_subtitle'] ?? 'Integrity & Transparency'))); ?></div>
                <p class="vm-cta-lead">Our policies are designed to protect and empower every member of the Valley View University family.</p>
                <div class="vm-actions">
                    <a href="mission_and_vision.php" class="vm-btn vm-btn--gold">
                        <span class="material-symbols-outlined">visibility</span><?php echo htmlspecialchars(strip_tags($hero['cta_button_text'] ?? 'Our Mission')); ?>
                    </a>
                    <a href="core_values.php" class="vm-btn vm-btn--ghost">
                        <span class="material-symbols-outlined">verified</span>Our Values
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
/* Policy search.
   The form still submits normally with JavaScript off (PHP filters on ?q=).
   This layers instant filtering on top so results update as you type. */
(function () {
    var form   = document.getElementById('policySearchForm');
    var input  = document.getElementById('policySearch');
    var clear  = document.getElementById('policySearchClear');
    var reset  = document.getElementById('policySearchReset');
    var status = document.getElementById('policySearchStatus');
    var empty  = document.getElementById('policySearchEmpty');
    if (!form || !input) return;

    var cards = [].slice.call(document.querySelectorAll('[data-policy-searchable]'));

    function apply(term) {
        term = term.trim().toLowerCase();
        var matches = 0;

        cards.forEach(function (card) {
            var hit = term === '' || (card.getAttribute('data-search-text') || '').indexOf(term) !== -1;
            card.classList.toggle('hidden', !hit);
            if (hit) matches++;
        });

        clear.classList.toggle('hidden', term === '');
        clear.classList.toggle('flex', term !== '');

        if (term === '') {
            status.classList.add('hidden');
            empty.classList.add('hidden');
        } else {
            status.classList.remove('hidden');
            status.innerHTML = matches + ' result' + (matches === 1 ? '' : 's') +
                ' for &ldquo;<span class="font-bold text-gray-900 dark:text-white"></span>&rdquo;';
            status.querySelector('span').textContent = term;   // textContent = no HTML injection
            empty.classList.toggle('hidden', matches !== 0);
        }

        // Keep the URL in step so results can be shared or reloaded
        var url = new URL(window.location.href);
        if (term === '') { url.searchParams.delete('q'); } else { url.searchParams.set('q', term); }
        window.history.replaceState({}, '', url);
    }

    var timer = null;
    input.addEventListener('input', function () {
        window.clearTimeout(timer);
        timer = window.setTimeout(function () { apply(input.value); }, 120);
    });

    // Enter (or the Search button) filters in place and jumps to the first
    // match — matches often sit in the section above the box, so without this
    // you are left looking at an empty area wondering if it worked.
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        window.clearTimeout(timer);
        apply(input.value);
        input.blur();

        if (input.value.trim() !== '') {
            var first = cards.find(function (c) { return !c.classList.contains('hidden'); });
            if (first) {
                first.scrollIntoView({ behavior: 'smooth', block: 'center' });
                first.classList.add('policy-hit');
                window.setTimeout(function () { first.classList.remove('policy-hit'); }, 1600);
            }
        }
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { input.value = ''; apply(''); }
    });

    function clearAll() { input.value = ''; apply(''); input.focus(); }
    if (clear) clear.addEventListener('click', clearAll);
    if (reset) reset.addEventListener('click', clearAll);

    // If the page arrived with ?q=, PHP already filtered — re-apply so the
    // counter and empty state match what is on screen.
    if (input.value.trim() !== '') apply(input.value);
})();
</script>

<?php include 'includes/footer.php'; ?>