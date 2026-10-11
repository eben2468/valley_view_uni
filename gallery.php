<?php
// The album is resolved BEFORE the header is included, because header.php
// prints <title> from $page_title — setting it afterwards would be too late
// and every album page would share the generic gallery title.
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/gallery_helper.php';

// Which gallery this page shows. The tables, helpers and admin screens are
// shared with src_gallery.php; only this key and the hero differ.
$vvu_gallery_key  = 'main';
$vvu_gallery_self = 'gallery.php';
$vvu_gallery_kicker = 'Photo Gallery';

// ?album=<slug> switches this same page into the album detail view, so every
// photo set stays on gallery.php instead of needing a second template.
$album_slug   = isset($_GET['album']) ? trim((string) $_GET['album']) : '';
$active_album = $album_slug !== '' ? vvu_gallery_album($pdo, $album_slug, $vvu_gallery_key) : null;

$page_title = $active_album
    ? $active_album['title'] . ' - Gallery - Valley View University'
    : 'Gallery - Valley View University';
$active_page = "student-life";

include 'includes/header.php';

$g_content    = vvu_gallery_content($pdo, $vvu_gallery_key);
$g_categories = vvu_gallery_categories($pdo, $vvu_gallery_key);
$g_stats      = vvu_gallery_stats($pdo, $vvu_gallery_key);
$album_images = $active_album ? vvu_gallery_album_images($pdo, $active_album['id']) : [];
$g_albums     = $active_album ? [] : vvu_gallery_albums($pdo, $vvu_gallery_key);
?>

<?php include 'includes/gallery_styles.php'; ?>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&q=80&w=1920" 
                 alt="VVU Gallery" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400">Photo Gallery</span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    VVU Gallery <br>
                    <span class="text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-4">Life In Pictures</span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "Capturing the vibrant moments and memorable experiences that define Valley View University"
                </p>
            </div>
        </div>
    </section>
<?php include 'includes/gallery_body.php'; ?>

<?php
include 'includes/footer.php';
?>
