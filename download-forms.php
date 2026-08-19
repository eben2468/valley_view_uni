<?php
$page_title = "Download Forms - Valley View University";
$active_page = "admissions";
require_once 'includes/db_connect.php';
include 'includes/header.php';

// Fetch page content
$stmt = $pdo->prepare("SELECT * FROM academic_pages_content WHERE page_key = 'download_forms'");
$stmt->execute();
$page_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch sections
$stmt = $pdo->prepare("SELECT * FROM academic_pages_sections WHERE page_key = 'download_forms' AND is_active = 1 ORDER BY display_order");
$stmt->execute();
$sections_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sections = [];
foreach ($sections_raw as $s) {
    $sections[$s['section_key']] = $s;
}

// Fetch items
$stmt = $pdo->prepare("SELECT * FROM academic_pages_items WHERE page_key = 'download_forms' AND is_active = 1 ORDER BY display_order");
$stmt->execute();
$items_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
$items = [];
foreach ($items_raw as $i) {
    $items[$i['section_key']][] = $i;
}

// Fetch stats
$stmt = $pdo->prepare("SELECT * FROM academic_pages_stats WHERE page_key = 'download_forms' AND is_active = 1 ORDER BY display_order");
$stmt->execute();
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

/**
 * A human-readable name for the saved file.
 *
 * Uploaded forms are stored under a generated name (form_1755….pdf) so that a
 * hostile filename can never be written into the web root. That is the right
 * thing on disk and the wrong thing in the visitor's Downloads folder, so the
 * anchor's download attribute renames it to the form's title on the way out.
 */
function vvu_form_filename($title, $href)
{
    $extension = strtolower(pathinfo(parse_url($href, PHP_URL_PATH), PATHINFO_EXTENSION));
    $name = trim(preg_replace('/[^A-Za-z0-9]+/', ' ', strip_tags((string) $title)));

    if ($name === '') {
        return '';
    }

    return str_replace(' ', '-', $name) . ($extension !== '' ? '.' . $extension : '');
}

/**
 * Work out what a stored item_link actually points at.
 *
 * item_link is free text typed into the admin panel (manage_resources_pages.php
 * "Link URL / File Path"), so a card can be one of two quite different things:
 *
 *   - a document to download — the usual case, a PDF in uploads/Download Forms/
 *   - a link to somewhere else — another page on the site, or an external URL
 *
 * The two cannot be rendered the same way. A page link given the `download`
 * attribute makes the browser save the HTML instead of opening it, so the card
 * has to know which kind it is holding.
 *
 * Returns null when the target is a file that is not on disk, so the caller can
 * render the card without a dead button instead of sending anyone to a 404.
 *
 * @return array{href:string,kind:string}|null  kind is 'file', 'page' or 'external'
 */
function vvu_form_target($link)
{
    $link = trim((string) $link);
    if ($link === '') {
        return null;
    }

    // Absolute URLs and protocol-relative links leave the site.
    if (preg_match('~^([a-z][a-z0-9+.-]*:|//)~i', $link)) {
        return ['href' => $link, 'kind' => 'external'];
    }

    // An internal page, with or without a query string or fragment.
    if (preg_match('~\.php($|[?#])~i', $link)) {
        return ['href' => $link, 'kind' => 'page'];
    }

    $path = ltrim(str_replace('\\', '/', $link), '/');
    $name = basename($path);

    // The path as typed first, then the two places the file is actually likely
    // to be. Anything that resolves is good enough — the DB is repaired
    // separately by tools/fix_download_form_links.php.
    foreach ([$path, 'uploads/Download Forms/' . $name, 'uploads/' . $name] as $candidate) {
        if ($name !== '' && is_file(__DIR__ . '/' . $candidate)) {
            return [
                'href' => implode('/', array_map('rawurlencode', explode('/', $candidate))),
                'kind' => 'file',
            ];
        }
    }

    // Not a file on disk. A bare path with no extension is almost certainly a
    // link the admin typed (a pretty URL or a folder), so treat it as one
    // rather than showing "Coming Soon" for something that works.
    if (!preg_match('~\.[a-z0-9]{2,5}$~i', $name)) {
        return ['href' => $path, 'kind' => 'page'];
    }

    return null;
}

?>

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
    
    .download-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .download-card:hover { 
        transform: translateY(-10px);
        box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.15);
    }

    .section-spacing {
        padding-top: 8rem;
        padding-bottom: 8rem;
    }

    /* Force white color for icons in colored containers */
    [class*="bg-gradient"] .material-symbols-outlined,
    [class*="bg-blue-600"] .material-symbols-outlined,
    [class*="bg-purple-600"] .material-symbols-outlined,
    [class*="bg-green-600"] .material-symbols-outlined,
    [class*="bg-yellow-500"] .material-symbols-outlined,
    [class*="bg-gray-700"] .material-symbols-outlined,
    .download-card [class*="from-"] .material-symbols-outlined {
        color: white !important;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_data['hero_image'] ?? 'Education-Website-and-AdminPanel/images/pro-bg.jpg'); ?>" 
                 alt="VVU Downloads" class="w-full h-full object-cover animate-slow-zoom opacity-50">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <?php if ($page_data['hero_badge']): ?>
                <div class="inline-flex items-center gap-4 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($page_data['hero_badge']); ?></span>
                </div>
                <?php endif; ?>
                
                <h1 class="text-7xl sm:text-8xl md:text-9xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo $page_data['hero_title']; ?> <br>
                    <span class="text-5xl sm:text-6xl md:text-7xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4"><?php echo $page_data['hero_subtitle']; ?></span>
                </h1>
                
                <p class="text-2xl sm:text-3xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    <?php echo strip_tags($page_data['hero_description']); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-28 bg-white dark:bg-gray-800 relative z-20 -mt-20 mx-auto max-w-[1800px] rounded-[3rem] shadow-2xl overflow-hidden">
        <div class="container px-8 md:px-16">
            <!-- Header -->
            <div class="mb-20">
                <div class="flex flex-col lg:flex-row gap-10 items-start lg:items-center justify-between">
                    <div class="flex items-center gap-8">
                        <div class="w-28 h-28 rounded-3xl bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center text-white shadow-2xl">
                            <span class="material-symbols-outlined text-6xl">folder_open</span>
                        </div>
                        <div>
                            <h2 class="text-6xl md:text-7xl font-black text-gray-900 dark:text-white mb-4">Available Documents</h2>
                            <p class="text-2xl md:text-3xl text-gray-500 dark:text-gray-400 font-medium">Browse and download official university forms</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories Grid -->
            <div class="space-y-24">
                <?php
                $sections_list = [
                    'undergrad' => ['color' => 'blue', 'icon' => 'school'],
                    'postgrad' => ['color' => 'purple', 'icon' => 'workspace_premium'],
                    'nursing' => ['color' => 'green', 'icon' => 'medical_services'],
                    'french' => ['color' => 'yellow', 'icon' => 'public'],
                    'others' => ['color' => 'gray', 'icon' => 'folder_special']
                ];

                foreach ($sections_list as $key => $config): 
                    if (!isset($sections[$key])) continue;
                    $s = $sections[$key];
                    $section_items = $items[$key] ?? [];
                ?>
                <div class="bg-gradient-to-br from-<?php echo $config['color']; ?>-50 to-white dark:from-<?php echo $config['color']; ?>-900/20 dark:to-gray-800 rounded-[2.5rem] p-12 md:p-16">
                    <div class="flex items-center gap-8 mb-12">
                        <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-<?php echo $config['color']; ?>-500 to-<?php echo $config['color']; ?>-700 flex items-center justify-center text-white shadow-xl">
                            <span class="material-symbols-outlined text-5xl"><?php echo $config['icon']; ?></span>
                        </div>
                        <div>
                            <h3 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white"><?php echo strip_tags($s['section_title']); ?></h3>
                            <p class="text-2xl text-<?php echo $config['color']; ?>-<?php echo $config['color'] == 'yellow' ? '600' : '600'; ?> dark:text-<?php echo $config['color']; ?>-400 font-medium mt-2"><?php echo count($section_items); ?> forms available</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                        <?php
                        foreach ($section_items as $item):
                            $target = vvu_form_target($item['item_link']);
                            // A file is saved, a link is followed. Only the
                            // first gets the download attribute; giving it to a
                            // page link would save the HTML instead of opening it.
                            $is_file  = $target !== null && $target['kind'] === 'file';
                            $is_away  = $target !== null && $target['kind'] === 'external';
                            $btn_icon = $is_file ? 'download' : ($is_away ? 'open_in_new' : 'arrow_forward');
                            $btn_text = $is_file ? 'Download Form' : 'Open Page';
                            // The badge defaults to the file type; a link is not a PDF.
                            $badge = $item['item_subtitle'] ?: ($is_file ? 'PDF' : 'Link');
                        ?>
                        <div class="download-card bg-white dark:bg-gray-900 p-10 rounded-[2rem] shadow-lg hover:shadow-2xl border border-<?php echo $config['color']; ?>-100 dark:border-gray-700 group">
                            <div class="flex flex-col gap-5">
                                <div class="flex items-center gap-5">
                                    <div class="w-18 h-18 rounded-xl bg-gradient-to-br from-<?php echo $config['color']; ?>-500 to-<?php echo $config['color']; ?>-600 flex items-center justify-center text-white shrink-0 shadow-lg group-hover:scale-110 transition-transform" style="width: 4.5rem; height: 4.5rem;">
                                        <span class="material-symbols-outlined text-3xl"><?php echo strip_tags($item['item_icon'] ?: 'description'); ?></span>
                                    </div>
                                    <span class="px-4 py-2 <?php echo $is_file ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'; ?> text-lg font-bold rounded-lg"><?php echo strip_tags($badge); ?></span>
                                </div>
                                <h4 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white group-hover:text-<?php echo $config['color']; ?>-600 transition-colors"><?php echo strip_tags($item['item_title']); ?></h4>
                                <p class="text-xl text-gray-500 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($item['item_description']); ?></p>
                                <?php if ($target !== null): ?>
                                <a href="<?php echo htmlspecialchars($target['href'], ENT_QUOTES); ?>"
                                   <?php if ($is_file): ?>download="<?php echo htmlspecialchars(vvu_form_filename($item['item_title'], $target['href']), ENT_QUOTES); ?>"<?php endif; ?>
                                   <?php if ($is_away): ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
                                   class="inline-flex items-center justify-center gap-4 w-full px-8 py-5 bg-<?php echo $config['color']; ?>-<?php echo $config['color'] == 'yellow' ? '500' : '600'; ?> hover:bg-<?php echo $config['color']; ?>-700 text-white rounded-xl text-xl font-bold transition-all shadow-md hover:shadow-xl mt-2">
                                    <span class="material-symbols-outlined text-2xl"><?php echo $btn_icon; ?></span>
                                    <?php echo $btn_text; ?>
                                </a>
                                <?php else: ?>
                                <span class="inline-flex items-center justify-center gap-4 w-full px-8 py-5 bg-gray-300 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-xl text-xl font-bold shadow-inner mt-2 cursor-not-allowed" title="This form has not been uploaded yet.">
                                    <span class="material-symbols-outlined text-2xl">hourglass_empty</span>
                                    Coming Soon
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Stats Section -->
            <?php if (!empty($stats)): ?>
            <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-8">
                <?php foreach ($stats as $stat): ?>
                <div class="bg-gradient-to-br from-blue-600 to-blue-800 p-8 rounded-[2rem] text-center text-white shadow-xl">
                    <span class="material-symbols-outlined text-5xl mb-4"><?php echo strip_tags($stat['stat_icon']); ?></span>
                    <p class="text-5xl font-black mb-2"><?php echo strip_tags($stat['stat_value']); ?><?php echo strip_tags($stat['stat_suffix']); ?></p>
                    <p class="text-xl font-medium opacity-90"><?php echo strip_tags($stat['stat_label']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- How to Apply Section -->
    <?php if (isset($sections['help'])): ?>
    <section class="section-spacing bg-gray-50 dark:bg-gray-950">
        <div class="container mx-auto max-w-[1600px] px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-24 items-center">
                <div class="animate-fadeInUp">
                    <h2 class="text-6xl md:text-7xl lg:text-8xl font-black text-gray-900 dark:text-white mb-10 leading-tight">
                        <?php echo $sections['help']['section_title']; ?>
                    </h2>
                    <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed mb-14">
                        <?php echo strip_tags($sections['help']['section_subtitle']); ?>
                    </p>
                    <div class="space-y-10">
                        <?php if (isset($items['help'])): ?>
                        <?php foreach ($items['help'] as $index => $item): ?>
                        <div class="flex items-start gap-8">
                            <div class="w-16 h-16 rounded-full bg-blue-600 flex items-center justify-center text-white shrink-0 font-black text-2xl"><?php echo $index + 1; ?></div>
                            <div>
                                <h4 class="text-3xl font-black text-gray-900 dark:text-white mb-3"><?php echo strip_tags($item['item_title']); ?></h4>
                                <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed"><?php echo strip_tags($item['item_description']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute -inset-4 bg-gradient-to-r from-blue-600 to-yellow-400 rounded-[3rem] blur-2xl opacity-20"></div>
                    <img src="<?php echo strip_tags($sections['help']['section_image'] ?? 'Education-Website-and-AdminPanel/images/h-adm.jpg'); ?>" alt="Application Support" class="relative rounded-[3rem] shadow-2xl w-full h-[500px] object-cover">
                    <div class="absolute -bottom-10 -right-10 glass p-10 rounded-[2.5rem] shadow-2xl max-w-xs animate-float">
                        <div class="flex items-center gap-4 mb-4">
                            <span class="material-symbols-outlined text-blue-600 text-4xl">support_agent</span>
                            <span class="text-2xl font-black text-gray-900 dark:text-white">Live Support</span>
                        </div>
                        <p class="text-lg font-bold text-gray-600 dark:text-gray-400">Our advisors are available 24/7 to assist you.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <?php if ($page_data['cta_title']): ?>
    <section class="relative py-32 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        
        <div class="container relative z-10 mx-auto max-w-7xl px-8">
            <div class="bg-white/10 backdrop-blur-xl rounded-[4rem] p-16 md:p-24 border border-white/20 text-center">
                <h2 class="text-3xl md:text-5xl font-black text-white mb-8"><?php echo $page_data['cta_title']; ?></h2>
                <p class="text-xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-normal">
                    <?php echo $page_data['cta_subtitle']; ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-8 justify-center">
                    <a href="<?php echo strip_tags($page_data['cta_button_link'] ?? 'https://admissions.vvu.edu.gh/'); ?>" class="px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-2xl font-black rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl">rocket_launch</span>
                        <?php echo strip_tags($page_data['cta_button_text'] ?? 'Apply Online Now'); ?>
                    </a>
                    <a href="contact_us.php" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-2xl font-black rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl">chat</span>
                        Contact Admissions
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php
include 'includes/footer.php';
?>
