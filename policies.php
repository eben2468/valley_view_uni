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

    <!-- Policy Categories Section -->
    <?php 
    $framework_section = array_values(array_filter($sections, fn($s) => $s['section_key'] === 'framework'))[0] ?? null;
    if ($framework_section): 
    ?>
    <section class="py-24 bg-white dark:bg-gray-900">
        <div class="container text-center">
            <div class="max-w-4xl mx-auto mb-20">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($framework_section['section_title']); ?></h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($framework_section['section_subtitle']); ?></p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
                <?php foreach ($grouped_items['framework'] ?? [] as $category): ?>
                <div class="policy-card relative group<?php echo vvu_policy_hidden($category, $q) ? ' hidden' : ''; ?>"
                     data-policy-searchable
                     data-search-text="<?php echo htmlspecialchars(vvu_policy_haystack($category)); ?>">
                    <div class="relative h-full glass p-10 rounded-3xl shadow-xl border-t-8 border-<?php echo strip_tags($category['item_color']); ?> flex flex-col text-left">
                        <div class="w-24 h-24 rounded-3xl bg-<?php echo strip_tags($category['item_color']); ?> flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-5xl text-white"><?php echo strip_tags($category['item_icon']); ?></span>
                        </div>
                        <h3 class="text-5xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($category['item_title']); ?></h3>
                        <p class="text-3xl text-gray-700 dark:text-gray-300 mb-8 flex-grow leading-relaxed">
                            <?php echo strip_tags($category['item_description']); ?>
                        </p>
                        <div class="space-y-4">
                            <?php if (!empty($category['documents'])): ?>
                                <?php foreach ($category['documents'] as $doc): ?>
                                <a href="<?php echo strip_tags($doc['url']); ?>" download class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-2xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group/link">
                                    <span class="material-symbols-outlined text-<?php echo strip_tags($doc['color'] ?? 'blue-600'); ?> text-4xl"><?php echo strip_tags($doc['icon'] ?? 'picture_as_pdf'); ?></span>
                                    <span class="text-2xl text-gray-700 dark:text-gray-300 font-bold"><?php echo strip_tags($doc['title']); ?></span>
                                    <span class="ml-auto text-sm bg-<?php echo strip_tags($category['item_color']); ?> text-white px-3 py-1 rounded-full opacity-0 group-hover/link:opacity-100 transition-opacity">Download PDF</span>
                                </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Search & Quick Access Section -->
    <?php 
    $links_section = array_values(array_filter($sections, fn($s) => $s['section_key'] === 'quick_links'))[0] ?? null;
    if ($links_section): 
    ?>
    <section class="py-24 bg-gray-50 dark:bg-gray-950">
        <div class="container text-center">
            <div class="max-w-4xl mx-auto mb-16">
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-6"><?php echo strip_tags($links_section['section_title']); ?></h2>
                <p class="text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($links_section['section_subtitle']); ?></p>
            </div>

            <!-- Explicit px width: this page sets a 10px root font, so Tailwind's
                 max-w-4xl resolves to 560px and left the bar looking pinched. -->
            <div class="max-w-[860px] mx-auto">
                <form method="GET" action="policies.php" id="policySearchForm" role="search" class="relative group">
                    <label for="policySearch" class="sr-only">Search policies</label>
                    <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-yellow-500 rounded-full blur opacity-25 group-focus-within:opacity-60 group-hover:opacity-50 transition duration-500"></div>
                    <div class="relative flex items-center gap-2 bg-white dark:bg-gray-900 rounded-full p-2 pl-6 shadow-2xl">
                        <span class="material-symbols-outlined text-3xl text-gray-400 flex-shrink-0" aria-hidden="true">search</span>

                        <input type="search" id="policySearch" name="q" autocomplete="off"
                               value="<?php echo htmlspecialchars($q); ?>"
                               placeholder="Search policies — try Governance, Academic, Staff…"
                               class="policy-search-input flex-grow min-w-0 bg-transparent text-xl py-4 px-3 text-gray-900 dark:text-white placeholder-gray-400">

                        <!-- Clear button: only shown once there is something to clear -->
                        <button type="button" id="policySearchClear"
                                class="flex-shrink-0 w-10 h-10 rounded-full items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors <?php echo $q === '' ? 'hidden' : 'flex'; ?>"
                                aria-label="Clear search">
                            <span class="material-symbols-outlined text-2xl">close</span>
                        </button>

                        <button type="submit"
                                class="flex-shrink-0 bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-full text-xl font-bold transition-all hover:scale-105 shadow-lg">
                            Search
                        </button>
                    </div>
                </form>

                <!-- Live result summary -->
                <p id="policySearchStatus" role="status" aria-live="polite"
                   class="mt-5 text-lg text-gray-600 dark:text-gray-400 font-medium <?php echo $q === '' ? 'hidden' : ''; ?>">
                    <?php if ($q !== ''): ?>
                        <?php echo $search_total; ?> result<?php echo $search_total === 1 ? '' : 's'; ?>
                        for &ldquo;<span class="font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($q); ?></span>&rdquo;
                    <?php endif; ?>
                </p>

                <!-- Empty state -->
                <div id="policySearchEmpty" class="<?php echo ($q !== '' && $search_total === 0) ? '' : 'hidden'; ?> mt-10 p-12 rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm">
                    <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-700">search_off</span>
                    <h4 class="mt-4 text-3xl font-black text-gray-900 dark:text-white">No policies matched</h4>
                    <p class="mt-3 text-xl text-gray-600 dark:text-gray-400">
                        Try a broader word, or <button type="button" id="policySearchReset" class="text-blue-600 font-bold underline hover:text-blue-700">clear the search</button> to see everything.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-20 text-left">
                <?php foreach ($grouped_items['quick_links'] ?? [] as $link): ?>
                <div class="group p-8 bg-white dark:bg-gray-900 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-800 hover:-translate-y-2<?php echo vvu_policy_hidden($link, $q) ? ' hidden' : ''; ?>"
                     data-policy-searchable
                     data-search-text="<?php echo htmlspecialchars(vvu_policy_haystack($link)); ?>">
                    <div class="w-16 h-16 rounded-2xl bg-<?php echo strip_tags($link['item_color']); ?> flex items-center justify-center text-white shadow-lg mb-8 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-3xl text-white"><?php echo strip_tags($link['item_icon']); ?></span>
                    </div>
                    <h4 class="text-4xl font-black text-gray-900 dark:text-white mb-4"><?php echo strip_tags($link['item_title']); ?></h4>
                    <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed mb-6">
                        <?php echo strip_tags($link['item_description']); ?>
                    </p>
                    <a href="<?php echo strip_tags($link['item_link']); ?>" class="text-<?php echo strip_tags($link['item_color']); ?> font-bold text-xl flex items-center gap-2 hover:gap-4 transition-all">
                        <?php echo strip_tags($link['item_subtitle']); ?> <span class="material-symbols-outlined">arrow_forward</span>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>
        
        <div class="container relative z-10 text-center">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white mb-8 leading-tight tracking-tight">
                    <?php echo strip_tags($hero['cta_title'] ?? 'Committed to'); ?> <br><span class="text-yellow-400 text-6xl sm:text-7xl md:text-8xl lg:text-6xl block mt-2"><?php echo strip_tags($hero['cta_subtitle'] ?? 'Integrity & Transparency'); ?></span>
                </h2>
                <p class="text-2xl sm:text-3xl md:text-4xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    Our policies are designed to protect and empower every member of the Valley View University family.
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="mission_and_vision.php" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">visibility</span>
                        <?php echo strip_tags($hero['cta_button_text'] ?? 'Our Mission'); ?>
                    </a>
                    <a href="core_values.php" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl">verified</span>
                        Our Values
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