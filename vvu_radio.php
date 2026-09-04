<?php
$page_title = "Valley View Radio 97.7 MHz - The Station Par Excellence";
$active_page = "stories";
include 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'includes/radio_schedule_data.php';

// Fetch main content
$stmt = $pdo->query("SELECT * FROM radio_content WHERE id = 1");
$content = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch programs
$stmt = $pdo->query("SELECT * FROM radio_programs WHERE status = 'active' ORDER BY display_order ASC");
$programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch features
$stmt = $pdo->query("SELECT * FROM radio_features ORDER BY display_order ASC");
$features = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fallbacks if database is empty (shouldn't be after migration)
$content = $content ?: [];
$content += [
    'hero_title' => 'Valley View Radio',
    'hero_subtitle' => '"Voice of the Valley — Your #1 Campus Station for Music, News, and Spiritual Inspiration."',
    'hero_image' => 'images/vvu_radio_hero_bg.png',
    'live_on_air_text' => 'Live On Air',
    'station_slogan' => 'The Station Par Excellence',
    'now_playing_heading' => 'On Air Now',
    'current_show' => '',
    'current_host' => '',
    'current_show_image' => '',
    'next_show_time' => '',
    'frequency' => '97.7 MHz',
    'stream_type' => 'none',
    'stream_url' => '',
    'stream_embed_code' => '',
    'stream_offline_note' => 'Our online stream is being set up. Tune in on 97.7 MHz across Oyibi and Greater Accra in the meantime.',
    'about_heading' => 'About Valley View Radio',
    'about_text' => 'Valley View Radio is the heartbeat of our campus, broadcasting a blend of news, student-led talk, and spiritual programming.',
    'programs_heading' => 'Our Shows',
    'programs_text' => 'Tune in to our shows throughout the week.',
    'schedule_pdf' => '',
    'cta_heading' => 'Join the Conversation',
    'cta_text' => 'Want to request a song, share a shoutout, or join our team of presenters? We\'d love to hear from you!',
    'cta_phone' => '+233 307 011 832',
    'cta_email' => 'radio@vvu.edu.gh',
    'whatsapp_number' => '',
    'location_text' => 'Mile 19 Off the Adenta-Dodowa Road, Oyibi, Accra',
    'facebook_url' => '#',
    'twitter_url' => '#',
    'instagram_url' => '#',
    'youtube_url' => '',
    'tiktok_url' => '',
    'hero_cta_1_text' => 'Listen Live',
    'hero_cta_1_link' => '#listen-live',
    'hero_cta_2_text' => 'Program Schedule',
    'hero_cta_2_link' => '#schedule',
    'about_image_1' => 'https://images.unsplash.com/photo-1478737270239-2f02b77fc618?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
    'about_image_2' => 'https://images.unsplash.com/photo-1520529011870-5c6b44c0bb8c?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
    'about_image_3' => 'https://images.unsplash.com/photo-1516280440614-37939bbacd81?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
    'about_image_4' => 'https://images.unsplash.com/photo-1493225255756-d9584f8606e9?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
];

/* --------------------------------------------------------------------------
   Schedule + on-air state
   The station clock is Africa/Accra, not the web server's timezone. The page
   renders the correct show server-side so it is right without JavaScript, and
   js/vvu-radio.js then keeps it ticking.
   -------------------------------------------------------------------------- */

$schedule_rows = vvr_schedule_rows();
$day_names     = vvr_day_names();
$station_now   = vvr_station_now();
$on_air        = vvr_on_air($station_now['day'], $station_now['minutes']);
$current_slot  = $on_air['current'];
$next_slot     = $on_air['next'];

// Category lookup for the client, keyed by show title.
$category_map = [];
foreach ($schedule_rows as $row) {
    foreach ($row['shows'] as $title) {
        $category_map[$title] = vvr_show_category($title);
    }
}

$category_labels = [
    'news'     => 'News & Current Affairs',
    'worship'  => 'Worship & Sermons',
    'devotion' => 'Devotion',
    'talk'     => 'Talk & Magazine',
    'youth'    => 'Youth & Campus',
    'sports'   => 'Sports',
    'music'    => 'Music',
];

/* --------------------------------------------------------------------------
   Live stream
   Three states, chosen by the admin panel:
     none  — no online stream yet; the player explains where to listen instead
     audio — a direct Icecast/Shoutcast/HLS URL played by the built-in player
     embed — a third-party player (Mixlr, Zeno.FM, Radio.co, YouTube Live)
   -------------------------------------------------------------------------- */

$stream_type = in_array($content['stream_type'], ['none', 'audio', 'embed'], true)
    ? $content['stream_type']
    : 'none';
$stream_url = trim((string) $content['stream_url']);

if ($stream_type === 'audio' && $stream_url === '') {
    $stream_type = 'none';
}
if ($stream_type === 'embed' && trim((string) $content['stream_embed_code']) === '') {
    $stream_type = 'none';
}

// The embed is admin-authored markup, so it is printed rather than escaped.
// <script> is stripped anyway: every supported provider embeds via <iframe>,
// and a pasted script tag would be an injection route through the CMS.
$stream_embed = preg_replace('#<script\b.*?</script>#is', '', (string) $content['stream_embed_code']);

/** A local file path only counts if it is actually on disk. */
$asset_exists = static function ($path) {
    $path = trim((string) $path);
    if ($path === '') {
        return false;
    }
    if (preg_match('#^https?://#i', $path)) {
        return true;
    }
    return is_file(__DIR__ . '/' . ltrim($path, '/'));
};

$schedule_pdf = $asset_exists($content['schedule_pdf']) ? $content['schedule_pdf'] : '';

/** Hero buttons: fall back to the on-page anchors if an editor blanks a link. */
$hero_link = static function ($value, $fallback) {
    $value = trim((string) $value);
    return ($value === '' || $value === '#') ? $fallback : $value;
};
$hero_cta_1_link = $hero_link($content['hero_cta_1_link'], '#listen-live');
$hero_cta_2_link = $hero_link($content['hero_cta_2_link'], '#schedule');
?>

<link href="css/vvu-radio.css?v=1.0" rel="stylesheet">

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slowZoom {
        0% { transform: scale(1); }
        100% { transform: scale(1.1); }
    }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .radio-gradient {
        background: linear-gradient(135deg, #150f3d 0%, #4c1d95 42%, #7c3aed 74%, #c026d3 100%);
    }
    @media (prefers-reduced-motion: reduce) {
        .animate-slow-zoom, .animate-fadeInUp { animation: none; }
    }
</style>

<main class="vvr flex-grow bg-white dark:bg-gray-900">
    <!-- ================================================================
         Hero
         ================================================================ -->
    <section class="relative min-h-[75vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo vvu_e($content['hero_image']); ?>"
                 alt="Valley View Radio studio" class="w-full h-full object-cover animate-slow-zoom opacity-50">
            <div class="absolute inset-0 bg-gradient-to-b from-purple-900/80 via-indigo-900/60 to-gray-900"></div>
        </div>

        <div class="container relative z-10 py-24">
            <div class="max-w-6xl mx-auto text-center">
                <div class="inline-flex items-center gap-4 px-8 py-3 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-600"></span>
                    </span>
                    <span class="text-lg md:text-xl font-black tracking-widest uppercase text-white"><?php echo vvu_e($content['live_on_air_text']); ?></span>
                </div>

                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php
                    $title_parts = explode('<br>', $content['hero_title']);
                    echo vvu_e($title_parts[0]);
                    if (isset($title_parts[1])):
                        echo "<br><span class='text-4xl sm:text-5xl md:text-6xl lg:text-6xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-pink-400 to-orange-400 block mt-4'>" . vvu_e($title_parts[1]) . "</span>";
                    endif;
                    ?>
                </h1>

                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic mb-12" style="animation-delay: 0.2s;">
                    <?php echo vvu_e($content['hero_subtitle']); ?>
                </p>
                <div class="flex flex-wrap justify-center gap-6 animate-fadeInUp" style="animation-delay: 0.3s;">
                    <a href="<?php echo vvu_e($hero_cta_1_link); ?>" class="px-10 py-5 radio-gradient text-white text-xl font-black rounded-2xl hover:scale-105 transition-all shadow-[0_0_30px_rgba(124,58,237,0.5)] flex items-center gap-4">
                        <span class="material-symbols-outlined text-3xl">play_circle</span>
                        <?php echo vvu_e($content['hero_cta_1_text']); ?>
                    </a>
                    <a href="<?php echo vvu_e($hero_cta_2_link); ?>" class="px-10 py-5 bg-white/10 backdrop-blur-md border border-white/20 text-white text-xl font-black rounded-2xl hover:bg-white/20 transition-all flex items-center gap-4">
                        <span class="material-symbols-outlined text-3xl">schedule</span>
                        <?php echo vvu_e($content['hero_cta_2_text']); ?>
                    </a>
                    <?php if ($schedule_pdf): ?>
                    <a href="<?php echo vvu_e($schedule_pdf); ?>" target="_blank" rel="noopener"
                       class="px-10 py-5 bg-white/10 backdrop-blur-md border border-white/20 text-white text-xl font-black rounded-2xl hover:bg-white/20 transition-all flex items-center gap-4">
                        <span class="material-symbols-outlined text-3xl">download</span>
                        Download Schedule
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ================================================================
         Live player
         ================================================================ -->
    <section id="listen-live" class="py-16 md:py-24 bg-white dark:bg-gray-900 scroll-mt-24">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto vvr-player vvr-gradient">
                <div class="vvr-player__inner">
                    <div class="grid grid-cols-1 lg:grid-cols-[1.35fr_1fr] gap-10 lg:gap-14 items-center">

                        <!-- Now playing -->
                        <div>
                            <div class="flex flex-wrap items-center gap-4 mb-6">
                                <span class="vvr-live" data-vvr="live-badge" data-state="<?php echo $stream_type === 'none' ? 'offline' : 'live'; ?>">
                                    <span class="vvr-live__dot"></span>
                                    <?php echo vvu_e($content['live_on_air_text']); ?>
                                </span>
                                <span class="vvr-eq" data-vvr="eq" aria-hidden="true">
                                    <span></span><span></span><span></span><span></span><span></span>
                                </span>
                                <span class="text-xs font-bold uppercase tracking-[.18em] text-white/60" data-vvr="clock">
                                    <?php echo vvu_e($station_now['label']); ?> GMT
                                </span>
                            </div>

                            <p class="text-xs font-black uppercase tracking-[.22em] text-white/60 mb-2">
                                <?php echo vvu_e($content['now_playing_heading']); ?>
                            </p>

                            <h2 class="text-4xl sm:text-5xl font-black leading-tight tracking-tight mb-3" data-vvr="show-title">
                                <?php echo vvu_e($current_slot['title']); ?>
                            </h2>

                            <div class="flex flex-wrap items-center gap-3 mb-6">
                                <span class="vvr-chip vvr-cat-<?php echo vvu_e($current_slot['category']); ?>" data-vvr="show-chip"><?php echo vvu_e($category_labels[$current_slot['category']]); ?></span>
                                <span class="text-white/80 font-semibold" data-vvr="show-time">
                                    <?php echo $current_slot['start']
                                        ? vvu_e(vvr_format_time($current_slot['start']) . ' – ' . vvr_format_time($current_slot['end']))
                                        : 'Between scheduled programmes'; ?>
                                </span>
                            </div>

                            <div class="vvr-progress mb-8" data-vvr="progress" <?php echo $current_slot['start'] ? '' : 'hidden'; ?>>
                                <div class="vvr-progress__bar" data-vvr="progress-bar" style="width:0%"></div>
                            </div>

                            <?php if ($stream_type === 'embed'): ?>
                                <div class="rounded-2xl overflow-hidden bg-black/25 p-2">
                                    <?php echo $stream_embed; ?>
                                </div>
                            <?php else: ?>
                                <div class="flex flex-wrap items-center gap-6">
                                    <button type="button" class="vvr-play" data-vvr="play"
                                            aria-label="Play the live stream"
                                            aria-pressed="false"
                                            <?php echo $stream_type === 'none' ? 'disabled' : ''; ?>>
                                        <span class="material-symbols-outlined">play_arrow</span>
                                    </button>

                                    <div class="flex-grow min-w-[14rem]">
                                        <p class="text-base font-semibold text-white/90 mb-3" data-vvr="status">
                                            <?php echo $stream_type === 'none'
                                                ? vvu_e($content['stream_offline_note'])
                                                : 'Tap play to listen live on ' . vvu_e($content['frequency']) . '.'; ?>
                                        </p>

                                        <?php if ($stream_type !== 'none'): ?>
                                        <div class="vvr-volume">
                                            <button type="button" data-vvr="mute" aria-label="Mute or unmute" aria-pressed="false">
                                                <span class="material-symbols-outlined">volume_up</span>
                                            </button>
                                            <input type="range" class="vvr-range" min="0" max="100" value="85"
                                                   data-vvr="volume" aria-label="Volume">
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if ($stream_type === 'audio'): ?>
                                   <audio id="vvr-audio" preload="none"></audio>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Station card -->
                        <div class="space-y-5">
                            <div class="vvr-dial">
                                <?php if ($asset_exists($content['current_show_image']) && $content['current_show_image']): ?>
                                    <img src="<?php echo vvu_e($content['current_show_image']); ?>" alt="">
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#150f3d]/90 via-[#150f3d]/40 to-transparent"></div>
                                <?php endif; ?>
                                <div class="relative">
                                    <p class="vvr-dial__frequency vvr-gold-text"><?php echo vvu_e($content['frequency']); ?></p>
                                    <p class="mt-3 text-sm font-bold uppercase tracking-[.2em] text-white/75">
                                        <?php echo vvu_e($content['station_slogan']); ?>
                                    </p>
                                </div>
                            </div>

                            <?php if ($next_slot): ?>
                            <div class="rounded-2xl bg-white/10 border border-white/15 backdrop-blur-md p-5 flex items-center gap-4">
                                <span class="material-symbols-outlined text-3xl text-white/70">skip_next</span>
                                <div class="min-w-0">
                                    <p class="text-[.7rem] font-black uppercase tracking-[.2em] text-white/60">Up Next</p>
                                    <p class="text-lg font-bold truncate" data-vvr="next-title"><?php echo vvu_e($next_slot['title']); ?></p>
                                    <p class="text-sm text-white/70" data-vvr="next-time">
                                        <?php echo vvu_e(($next_slot['day'] === $station_now['day'] ? 'Today' : $day_names[$next_slot['day']])
                                            . ' · ' . vvr_format_time($next_slot['start'])); ?>
                                    </p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($stream_type === 'none'): ?>
            <p class="max-w-6xl mx-auto mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                <span class="material-symbols-outlined align-middle text-base">info</span>
                Online streaming is not switched on yet. Once a stream address is added in the admin panel
                (Campus Life → Radio → Live Stream), this player goes live for listeners worldwide.
            </p>
            <?php endif; ?>
        </div>
    </section>

    <!-- ================================================================
         Shows
         ================================================================ -->
    <section id="shows" class="py-16 md:py-24 bg-gray-50 dark:bg-gray-800/40 scroll-mt-24">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mb-12">
                <p class="vvr-eyebrow mb-3"><span class="material-symbols-outlined text-base">graphic_eq</span> On Valley View Radio</p>
                <h2 class="vvr-heading mb-5"><?php echo vvu_e($content['programs_heading']); ?></h2>
                <div class="vvr-rule mb-6"></div>
                <p class="vvr-sub"><?php echo vvu_e($content['programs_text']); ?></p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($programs as $prog):
                    // category/logo only exist once sql/radio_live_stream_migration.sql
                    // has run; without it the show still renders from its title.
                    $category = ($prog['category'] ?? '') ?: vvr_show_category($prog['title']);
                    $category = isset($category_labels[$category]) ? $category : 'music';
                    $has_logo = $asset_exists($prog['logo'] ?? '');
                ?>
                <article class="vvr-card vvr-cat-<?php echo vvu_e($category); ?> flex flex-col">
                    <div class="vvr-show__logo">
                        <?php if ($has_logo): ?>
                            <img src="<?php echo vvu_e($prog['logo']); ?>" alt="<?php echo vvu_e($prog['title']); ?> logo" loading="lazy">
                        <?php else: ?>
                            <span class="vvr-show__fallback bg-<?php echo vvu_e($prog['icon_bg_color'] ?: 'purple-600'); ?>">
                                <span class="material-symbols-outlined"><?php echo vvu_e($prog['icon'] ?: 'radio'); ?></span>
                                <span class="text-xs font-black uppercase tracking-widest opacity-90">97.7 MHz</span>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <span class="vvr-chip mb-3 self-start"><?php echo vvu_e($category_labels[$category] ?? $category); ?></span>
                        <h3 class="text-xl font-extrabold text-gray-900 dark:text-white mb-1.5"><?php echo vvu_e($prog['title']); ?></h3>
                        <p class="text-sm font-bold text-purple-700 dark:text-purple-300 mb-3"><?php echo vvu_e($prog['schedule']); ?></p>
                        <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-400 flex-grow"><?php echo vvu_e($prog['description']); ?></p>
                        <?php if (!empty($prog['host'])): ?>
                        <p class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Hosted by <?php echo vvu_e($prog['host']); ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ================================================================
         Weekly schedule
         ================================================================ -->
    <section id="schedule" class="py-16 md:py-24 bg-white dark:bg-gray-900 scroll-mt-24">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-8 mb-10">
                <div class="max-w-2xl">
                    <p class="vvr-eyebrow mb-3"><span class="material-symbols-outlined text-base">calendar_month</span> Weekly Timetable</p>
                    <h2 class="vvr-heading mb-5">Program Schedule</h2>
                    <div class="vvr-rule mb-6"></div>
                    <p class="vvr-sub">
                        Everything Valley View Radio broadcasts, from the 4:30 AM devotion through to Hymns Night.
                        All times are Ghana time (GMT).
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <?php if ($schedule_pdf): ?>
                    <a href="<?php echo vvu_e($schedule_pdf); ?>" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-purple-700 text-white text-sm font-bold hover:bg-purple-800 transition-colors">
                        <span class="material-symbols-outlined text-xl">picture_as_pdf</span> Download PDF
                    </a>
                    <?php endif; ?>
                    <div class="vvr-viewswitch" role="tablist" aria-label="Schedule view">
                        <button type="button" class="vvr-viewtab" data-vvr-view="day" role="tab" aria-selected="true">Day by day</button>
                        <button type="button" class="vvr-viewtab" data-vvr-view="week" role="tab" aria-selected="false">Full week</button>
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="flex flex-wrap gap-2 mb-8">
                <?php foreach ($category_labels as $key => $label): ?>
                    <span class="vvr-chip vvr-cat-<?php echo vvu_e($key); ?>"><?php echo vvu_e($label); ?></span>
                <?php endforeach; ?>
            </div>

            <!-- Day-by-day view -->
            <div data-vvr-viewpanel="day">
                <div class="vvr-daytabs mb-6" role="tablist" aria-label="Day of the week">
                    <?php foreach ($day_names as $index => $name): ?>
                        <button type="button" class="vvr-daytab" role="tab"
                                data-vvr-daytab="<?php echo $index; ?>"
                                aria-selected="<?php echo $index === $station_now['day'] ? 'true' : 'false'; ?>">
                            <?php echo vvu_e($name); ?><?php echo $index === $station_now['day'] ? ' · Today' : ''; ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <?php foreach ($day_names as $index => $name): ?>
                <div class="vvr-daylist" data-vvr-daypanel="<?php echo $index; ?>" <?php echo $index === $station_now['day'] ? '' : 'hidden'; ?>>
                    <?php
                    // Collapse consecutive rows carrying the same show into one
                    // block, so "Drive Time" reads as 3:00 – 6:00 PM rather than
                    // as six identical half-hour lines.
                    $blocks = [];
                    foreach ($schedule_rows as $row) {
                        if (!isset($row['shows'][$index])) {
                            continue;
                        }
                        $title = $row['shows'][$index];
                        $last  = count($blocks) - 1;
                        if ($last >= 0 && $blocks[$last]['title'] === $title && $blocks[$last]['end'] === $row['start']) {
                            $blocks[$last]['end'] = $row['end'];
                            continue;
                        }
                        $blocks[] = ['title' => $title, 'start' => $row['start'], 'end' => $row['end']];
                    }

                    foreach ($blocks as $block):
                        $category = vvr_show_category($block['title']);
                        $is_now   = $index === $station_now['day']
                            && vvr_slot_matches($block, $station_now['minutes']);
                    ?>
                    <div class="vvr-dayrow vvr-cat-<?php echo vvu_e($category); ?><?php echo $is_now ? ' is-now' : ''; ?>"
                         data-day="<?php echo $index; ?>"
                         data-start="<?php echo vvu_e($block['start']); ?>"
                         data-end="<?php echo vvu_e($block['end']); ?>">
                        <p class="vvr-dayrow__time">
                            <?php echo vvu_e(vvr_format_time($block['start'])); ?><br class="hidden sm:inline">
                            <span class="sm:hidden"> – </span><span class="hidden sm:inline">–</span>
                            <?php echo vvu_e(vvr_format_time($block['end'])); ?>
                        </p>
                        <p class="vvr-dayrow__title"><?php echo vvu_e($block['title']); ?></p>
                        <span class="vvr-chip"><?php echo vvu_e($category_labels[$category]); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Full-week view -->
            <div data-vvr-viewpanel="week" hidden>
                <div class="vvr-grid-wrap">
                    <div class="vvr-grid-scroll">
                        <table class="vvr-grid">
                            <caption class="sr-only">Valley View Radio weekly broadcast schedule, all times Ghana time (GMT)</caption>
                            <thead>
                                <tr>
                                    <th scope="col">Time</th>
                                    <?php foreach ($day_names as $index => $name): ?>
                                        <th scope="col" class="<?php echo $index === $station_now['day'] ? 'is-today' : ''; ?>">
                                            <?php echo vvu_e(substr($name, 0, 3)); ?>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($schedule_rows as $row): ?>
                                <tr>
                                    <th scope="row">
                                        <?php echo vvu_e(vvr_format_time($row['start']) . ' – ' . vvr_format_time($row['end'])); ?>
                                    </th>
                                    <?php foreach ($day_names as $index => $name):
                                        $title    = $row['shows'][$index] ?? '';
                                        $category = $title ? vvr_show_category($title) : 'music';
                                        $is_now   = $title !== ''
                                            && $index === $station_now['day']
                                            && vvr_slot_matches($row, $station_now['minutes']);
                                    ?>
                                        <td class="<?php echo $title ? 'has-show vvr-cat-' . vvu_e($category) : 'is-empty'; ?><?php echo $is_now ? ' is-now' : ''; ?>"
                                            data-day="<?php echo $index; ?>"
                                            data-start="<?php echo vvu_e($row['start']); ?>"
                                            data-end="<?php echo vvu_e($row['end']); ?>">
                                            <?php echo vvu_e($title); ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Scroll sideways to see every day. Blank cells are unprogrammed music.</p>
            </div>
        </div>
    </section>

    <!-- ================================================================
         About
         ================================================================ -->
    <section class="py-16 md:py-24 bg-gray-50 dark:bg-gray-800/40">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div>
                    <p class="vvr-eyebrow mb-3"><span class="material-symbols-outlined text-base">campaign</span> 97.7 MHz Oyibi</p>
                    <h2 class="vvr-heading mb-5"><?php echo vvu_e($content['about_heading']); ?></h2>
                    <div class="vvr-rule mb-6"></div>
                    <p class="vvr-sub mb-8"><?php echo vvu_e($content['about_text']); ?></p>

                    <ul class="grid sm:grid-cols-2 gap-4">
                        <?php foreach ($features as $feat): ?>
                        <li class="flex items-center gap-4 p-4 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700">
                            <span class="w-12 h-12 flex-none rounded-xl bg-<?php echo vvu_e($feat['color_class']); ?>-100 dark:bg-<?php echo vvu_e($feat['color_class']); ?>-900/40 flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl text-<?php echo vvu_e($feat['color_class']); ?>-600"><?php echo vvu_e($feat['icon']); ?></span>
                            </span>
                            <span class="font-bold text-gray-800 dark:text-gray-200"><?php echo vvu_e($feat['title']); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div class="space-y-5 mt-8">
                        <img src="<?php echo vvu_e($content['about_image_1']); ?>" alt="" loading="lazy"
                             class="w-full h-52 object-cover rounded-2xl shadow-lg">
                        <img src="<?php echo vvu_e($content['about_image_2']); ?>" alt="" loading="lazy"
                             class="w-full h-64 object-cover rounded-2xl shadow-lg">
                    </div>
                    <div class="space-y-5">
                        <img src="<?php echo vvu_e($content['about_image_3']); ?>" alt="" loading="lazy"
                             class="w-full h-64 object-cover rounded-2xl shadow-lg">
                        <img src="<?php echo vvu_e($content['about_image_4']); ?>" alt="" loading="lazy"
                             class="w-full h-52 object-cover rounded-2xl shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================================================================
         Call to action
         ================================================================ -->
    <section class="py-16 md:py-24 vvr-gradient text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl md:text-5xl font-black mb-5"><?php echo vvu_e($content['cta_heading']); ?></h2>
            <p class="text-lg md:text-xl text-purple-100 max-w-3xl mx-auto mb-10 leading-relaxed">
                <?php echo vvu_e($content['cta_text']); ?>
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="tel:<?php echo vvu_e(str_replace(' ', '', $content['cta_phone'])); ?>"
                   class="px-8 py-4 bg-white text-purple-900 text-lg font-black rounded-xl hover:scale-105 transition-transform shadow-xl flex items-center gap-3">
                    <span class="material-symbols-outlined text-2xl">call</span> Call the Studio
                </a>
                <?php if (!empty($content['whatsapp_number'])): ?>
                <a href="https://wa.me/<?php echo vvu_e(preg_replace('/\D+/', '', $content['whatsapp_number'])); ?>"
                   target="_blank" rel="noopener"
                   class="px-8 py-4 bg-green-500 text-white text-lg font-black rounded-xl hover:bg-green-600 transition-colors shadow-xl flex items-center gap-3">
                    <i class="fab fa-whatsapp text-2xl"></i> WhatsApp the Studio
                </a>
                <?php endif; ?>
                <a href="mailto:<?php echo vvu_e($content['cta_email']); ?>"
                   class="px-8 py-4 bg-white/10 backdrop-blur-md border border-white/30 text-white text-lg font-black rounded-xl hover:bg-white/20 transition-colors flex items-center gap-3">
                    <span class="material-symbols-outlined text-2xl">mail</span> Send a Message
                </a>
            </div>
        </div>
    </section>

    <!-- ================================================================
         Contact
         ================================================================ -->
    <section class="py-16 md:py-24 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="vvr-card p-8 text-center">
                    <span class="w-14 h-14 mx-auto mb-5 rounded-2xl bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center">
                        <span class="material-symbols-outlined text-3xl text-purple-600">location_on</span>
                    </span>
                    <h3 class="text-xl font-extrabold text-gray-900 dark:text-white mb-2">Studio Location</h3>
                    <p class="text-gray-600 dark:text-gray-400"><?php echo vvu_e($content['location_text']); ?></p>
                </div>

                <div class="vvr-card p-8 text-center">
                    <span class="w-14 h-14 mx-auto mb-5 rounded-2xl bg-pink-100 dark:bg-pink-900/40 flex items-center justify-center">
                        <span class="material-symbols-outlined text-3xl text-pink-600">phone_in_talk</span>
                    </span>
                    <h3 class="text-xl font-extrabold text-gray-900 dark:text-white mb-2">On Air &amp; In Touch</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        <a class="hover:text-purple-600" href="tel:<?php echo vvu_e(str_replace(' ', '', $content['cta_phone'])); ?>"><?php echo vvu_e($content['cta_phone']); ?></a><br>
                        <a class="hover:text-purple-600" href="mailto:<?php echo vvu_e($content['cta_email']); ?>"><?php echo vvu_e($content['cta_email']); ?></a>
                    </p>
                </div>

                <div class="vvr-card p-8 text-center">
                    <span class="w-14 h-14 mx-auto mb-5 rounded-2xl bg-orange-100 dark:bg-orange-900/40 flex items-center justify-center">
                        <span class="material-symbols-outlined text-3xl text-orange-600">share</span>
                    </span>
                    <h3 class="text-xl font-extrabold text-gray-900 dark:text-white mb-2">Follow the Station</h3>
                    <div class="flex justify-center gap-5 mt-4 text-2xl text-gray-500 dark:text-gray-400">
                        <?php
                        $socials = [
                            'facebook_url'  => 'fab fa-facebook',
                            'twitter_url'   => 'fab fa-x-twitter',
                            'instagram_url' => 'fab fa-instagram',
                            'youtube_url'   => 'fab fa-youtube',
                            'tiktok_url'    => 'fab fa-tiktok',
                        ];
                        foreach ($socials as $field => $icon):
                            $url = trim((string) ($content[$field] ?? ''));
                            if ($url === '' || $url === '#') {
                                continue;
                            }
                        ?>
                        <a href="<?php echo vvu_e($url); ?>" target="_blank" rel="noopener"
                           class="hover:text-purple-600 transition-colors" aria-label="Valley View Radio on <?php echo vvu_e(str_replace('_url', '', $field)); ?>">
                            <i class="<?php echo $icon; ?>"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================================================================
         Sticky mini player
         ================================================================ -->
    <?php if ($stream_type === 'audio'): ?>
    <div class="vvr-mini vvr-gradient" data-vvr="mini">
        <div class="vvr-mini__inner">
            <button type="button" class="vvr-mini__btn" data-vvr="mini-play" aria-label="Play the live stream" aria-pressed="false">
                <span class="material-symbols-outlined">play_arrow</span>
            </button>
            <div class="vvr-mini__meta">
                <p class="text-[.65rem] font-black uppercase tracking-[.2em] text-white/60">Live · <?php echo vvu_e($content['frequency']); ?></p>
                <p class="font-bold" data-vvr="mini-title"><?php echo vvu_e($current_slot['title']); ?></p>
            </div>
            <button type="button" class="vvr-mini__close" data-vvr="mini-close" aria-label="Hide the mini player">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
    </div>
    <?php endif; ?>
</main>

<script>
    window.VVR_DATA = <?php echo json_encode([
        'rows'       => $schedule_rows,
        'dayNames'   => $day_names,
        'categories'      => $category_map,
        'categoryLabels'  => $category_labels,
        'stream'     => ['url' => $stream_type === 'audio' ? $stream_url : '', 'type' => $stream_type],
        'now'        => [
            'day'     => $station_now['day'],
            'minutes' => $station_now['minutes'],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>
<script src="js/vvu-radio.js?v=1.0" defer></script>

<?php include 'includes/footer.php'; ?>
