<?php
$pageTitle = "VVU Anthem - Valley View University";
$activePage = "student_life";
require_once 'includes/db_connect.php';
require_once 'includes/upload_helper.php';

// Fetch content from database
$hero = $pdo->query("SELECT * FROM anthem_hero WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$stanzas = $pdo->query("SELECT * FROM anthem_stanzas WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$video = $pdo->query("SELECT * FROM anthem_video WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$about = $pdo->query("SELECT * FROM anthem_about WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$cta = $pdo->query("SELECT * FROM anthem_cta WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();

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
    @keyframes musicNote {
        0% { transform: translateY(0) rotate(0deg); opacity: 1; }
        50% { transform: translateY(-15px) rotate(10deg); opacity: 0.8; }
        100% { transform: translateY(0) rotate(0deg); opacity: 1; }
    }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
    .animate-float { animation: float 4s ease-in-out infinite; }
    .animate-music-note { animation: musicNote 2s ease-in-out infinite; }
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
    /* ============================================================
       Lyrics section, in the style of the Mission & Vision page.
       Palette: navy #1e3a8a / #172554, gold #fbbf24 / #f59e0b, greys.
       ============================================================ */
    main .an-heading {
        font-family: var(--vvu-title-font);
        font-weight: var(--vvu-title-weight);
        letter-spacing: normal;
    }
    .an-section { padding: 88px 0; }
    .an-head { max-width: 820px; margin: 0 auto 56px; text-align: center; }
    .an-kicker {
        display: inline-flex; align-items: center; gap: 8px; margin-bottom: 14px;
        font-size: 1rem; font-weight: 700; letter-spacing: .28em;
        text-transform: uppercase; color: #b45309;
    }
    .an-kicker .material-symbols-outlined { font-size: 20px; letter-spacing: 0; color: inherit; }
    .dark .an-kicker { color: #fbbf24; }
    .an-heading {
        color: #1e3a8a; font-size: clamp(2.25rem, 4.5vw, 3.5rem); line-height: 1.15;
    }
    .dark .an-heading { color: #fff; }
    .an-heading::after {
        content: ""; display: block; width: 56px; height: 4px; border-radius: 4px;
        background: #fbbf24; margin: 20px auto 0;
    }
    .an-lead {
        margin: 22px 0 0; color: #4b5563;
        font-size: 1.4rem; line-height: 1.6; font-weight: 400;
    }
    .dark .an-lead { color: #9ca3af; }

    /* Stanza bands: alternate dark/light; the number column and the
       lyrics swap sides on each band. */
    .an-stanzas { display: flex; flex-direction: column; gap: 20px; }
    .an-stanza { position: relative; overflow: hidden; padding: 64px 0; }
    .an-stanza--dark { background: linear-gradient(120deg, #1d4ed8 0%, #1e3a8a 55%, #172554 100%); }
    .an-stanza--light { background: linear-gradient(120deg, #e0e7f1 0%, #cfd9e8 100%); }
    .dark .an-stanza--light { background: linear-gradient(120deg, #1f2937 0%, #111827 100%); }
    /* Faint music note watermark */
    .an-stanza::after {
        content: "\266A"; position: absolute; right: 4%; bottom: -60px;
        font-size: 260px; line-height: 1; pointer-events: none;
        color: rgba(255, 255, 255, .05);
    }
    .an-stanza--light::after { color: rgba(30, 58, 138, .06); }
    .an-stanza--flip::after { right: auto; left: 4%; }

    .an-stanza-inner {
        position: relative; z-index: 1;
        display: flex; align-items: center; justify-content: center;
        gap: 64px; max-width: 1100px; margin: 0 auto;
    }
    .an-stanza--flip .an-stanza-inner { flex-direction: row-reverse; }

    .an-stanza-mark { flex: 0 0 auto; text-align: center; }
    .an-stanza-num {
        display: block; font-size: clamp(5.5rem, 11vw, 9rem);
        font-weight: 800; line-height: 1; letter-spacing: -.04em;
        color: transparent; -webkit-text-fill-color: transparent;
    }
    .an-stanza--dark .an-stanza-num { -webkit-text-stroke: 2px #fbbf24; }
    .an-stanza--light .an-stanza-num { -webkit-text-stroke: 2px #1e3a8a; }
    .dark .an-stanza--light .an-stanza-num { -webkit-text-stroke-color: #93c5fd; }
    .an-stanza-label {
        display: block; margin-top: 12px;
        font-size: 1rem; font-weight: 700; letter-spacing: .3em; text-transform: uppercase;
    }
    .an-stanza--dark .an-stanza-label { color: rgba(255, 255, 255, .75); }
    .an-stanza--light .an-stanza-label { color: #1e3a8a; }
    .dark .an-stanza--light .an-stanza-label { color: #93c5fd; }

    /* Lyrics. Every line is shown the same way, as one continuous stanza:
       the stored paragraphs get no gap between them and bold is ignored. */
    .an-lyrics { flex: 1 1 0; max-width: 620px; }
    .an-lyrics p {
        margin: 0; font-size: 1.4rem; line-height: 1.9; font-weight: 500;
    }
    .an-lyrics strong, .an-lyrics b, .an-lyrics em, .an-lyrics i {
        font-weight: inherit; font-style: normal; color: inherit;
    }
    .an-stanza--dark .an-lyrics p { color: rgba(255, 255, 255, .92); }
    .an-stanza--light .an-lyrics p { color: #1f2937; }
    .dark .an-stanza--light .an-lyrics p { color: #e5e7eb; }

    @media (max-width: 767px) {
        .an-section { padding: 60px 0; }
        .an-head { margin-bottom: 36px; }
        .an-kicker { font-size: .85rem; }
        .an-lead { font-size: 1.15rem; }
        .an-stanzas { gap: 14px; }
        .an-stanza { padding: 44px 0; }
        .an-stanza-inner, .an-stanza--flip .an-stanza-inner {
            flex-direction: column; align-items: flex-start; gap: 20px;
        }
        .an-stanza-mark { display: flex; align-items: baseline; gap: 14px; text-align: left; }
        .an-stanza-num { font-size: 4rem; }
        .an-stanza-label { margin-top: 0; font-size: .9rem; }
        .an-lyrics { max-width: none; }
        .an-lyrics p { font-size: 1.15rem; line-height: 1.85; }
        .an-stanza::after { font-size: 160px; bottom: -40px; }
    }

    /* ---- Shared section bits for the rest of the page ---- */
    main .an-card-title, main .an-cta-heading {
        font-family: var(--vvu-title-font);
        font-weight: var(--vvu-title-weight);
        letter-spacing: normal;
    }
    .an-section--white { background: #fff; }
    .dark .an-section--white { background: #111827; }

    /* Player: white mat with an offset navy panel and gold dots behind it
       (same treatment as the Mission & Vision photo) */
    .an-player-frame {
        position: relative; isolation: isolate;
        max-width: 960px; margin: 0 auto; padding: 0 24px 24px 0;
    }
    .an-player-frame::before {
        content: ""; position: absolute; z-index: -1;
        top: 24px; left: 24px; right: 0; bottom: 0; border-radius: 28px;
        background: linear-gradient(135deg, #1e3a8a 0%, #172554 100%);
    }
    .an-player-frame::after {
        content: ""; position: absolute; z-index: -1;
        top: -22px; left: -22px; width: 130px; height: 130px;
        background-image: radial-gradient(#f59e0b 2px, transparent 2.5px);
        background-size: 16px 16px; opacity: .7;
    }
    .an-player {
        position: relative; overflow: hidden; aspect-ratio: 16 / 9;
        border-radius: 26px; border: 8px solid #fff; background: #000;
        box-shadow: 0 30px 60px -30px rgba(15, 23, 42, .55);
    }
    .dark .an-player { border-color: #1f2937; }
    .an-player video, .an-player > img { width: 100%; height: 100%; object-fit: cover; display: block; border-radius: 18px; }
    .an-player-shade { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0, 0, 0, .75) 0%, rgba(0, 0, 0, .15) 45%, transparent 100%); }
    .an-player-audio { position: absolute; left: 0; right: 0; bottom: 0; padding: 24px; }
    .an-player-audio audio { width: 100%; }

    /* About cards: white cards with a navy corner icon (as on M&V) */
    .an-meta {
        display: flex; flex-wrap: wrap; justify-content: center; gap: 10px;
        margin-top: 22px;
    }
    .an-meta span {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 8px 16px; border-radius: 999px;
        background: rgba(30, 58, 138, .08); color: #1e3a8a;
        font-size: 1.05rem; font-weight: 600;
    }
    .an-meta .material-symbols-outlined { font-size: 20px; color: #f59e0b; }
    .dark .an-meta span { background: rgba(147, 197, 253, .12); color: #bfdbfe; }

    .an-cards {
        display: grid; grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px; max-width: 1100px; margin: 0 auto;
    }
    .an-card {
        position: relative; overflow: hidden;
        padding: 36px 36px 120px; border-radius: 22px;
        background: #fff; border: 1px solid #e5e7eb;
        box-shadow: 0 18px 40px -30px rgba(15, 23, 42, .45);
        transition: transform .35s ease, box-shadow .35s ease;
    }
    .an-card:hover { transform: translateY(-6px); box-shadow: 0 28px 50px -28px rgba(15, 23, 42, .5); }
    .dark .an-card { background: #1f2937; border-color: #374151; }
    .an-card-title { color: #1e3a8a; font-size: 2rem; line-height: 1.2; margin-bottom: 16px; }
    .an-card-title::after {
        content: ""; display: block; width: 40px; height: 3px;
        border-radius: 3px; background: #fbbf24; margin-top: 12px;
    }
    .dark .an-card-title { color: #bfdbfe; }
    .an-card-text { margin: 0; color: #4b5563; font-size: 1.3rem; line-height: 1.7; font-weight: 400; }
    .dark .an-card-text { color: #d1d5db; }
    .an-card-corner {
        position: absolute; right: 0; bottom: 0;
        width: 104px; height: 104px; background: #1e3a8a;
        border-top-left-radius: 100%;
        display: flex; align-items: flex-end; justify-content: flex-end;
        padding: 0 20px 20px 0;
    }
    .an-card-corner .material-symbols-outlined {
        font-size: 40px; width: 40px; height: 40px; line-height: 1; color: #fff;
        font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 48;
        transition: transform .35s ease;
    }
    .an-card:hover .an-card-corner .material-symbols-outlined { transform: scale(1.08) rotate(-4deg); }
    .dark .an-card-corner { background: #3b82f6; }

    /* Call to action: Vision-band gradient with soft circles */
    .an-cta {
        position: relative; overflow: hidden; padding: 64px 0 56px;
        background: linear-gradient(120deg, #1d4ed8 0%, #1e3a8a 55%, #172554 100%);
    }
    .an-cta::before, .an-cta::after {
        content: ""; position: absolute; border-radius: 50%; pointer-events: none;
        background: rgba(255, 255, 255, .05);
    }
    .an-cta::before { width: 520px; height: 520px; right: -180px; top: -200px; }
    .an-cta::after { width: 360px; height: 360px; left: -140px; bottom: -180px; }
    .an-cta .container { position: relative; z-index: 1; }
    .an-cta-head { max-width: 820px; margin: 0 auto; text-align: center; }
    .an-cta-heading { color: #fff; font-size: clamp(2.25rem, 4.5vw, 3.5rem); line-height: 1.15; }
    .an-cta-heading::after {
        content: ""; display: block; width: 56px; height: 4px; border-radius: 4px;
        background: #fbbf24; margin: 20px auto 0;
    }
    .an-cta-lead { margin: 22px 0 0; color: rgba(255, 255, 255, .85); font-size: 1.35rem; line-height: 1.6; font-weight: 400; }
    .an-cta-actions { display: flex; flex-wrap: wrap; gap: 14px; justify-content: center; margin-top: 34px; }
    .an-btn {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 14px 28px; border-radius: 999px;
        font-size: 1.1rem; font-weight: 700; text-decoration: none;
        transition: background-color .25s ease, color .25s ease, transform .25s ease;
    }
    .an-btn .material-symbols-outlined { font-size: 22px; color: inherit; }
    .an-btn:hover { transform: translateY(-2px); }
    .an-btn--gold { background: #fbbf24; color: #172554; box-shadow: 0 12px 24px -14px rgba(251, 191, 36, .9); }
    .an-btn--gold:hover { background: #fcd34d; color: #172554; }
    .an-btn--ghost { color: #fff; border: 1.5px solid rgba(255, 255, 255, .6); }
    .an-btn--ghost:hover { background: #fff; color: #1e3a8a; }
    .an-stats {
        display: grid; grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px 36px; max-width: 1000px; margin: 48px auto 0;
    }
    .an-stat { padding: 20px 4px 4px; text-align: center; border-top: 1px solid rgba(255, 255, 255, .25); }
    .an-stat-value { display: block; color: #fbbf24; font-size: clamp(1.75rem, 3.5vw, 2.75rem); font-weight: 700; line-height: 1.1; }
    .an-stat-label {
        display: block; margin-top: 8px; color: rgba(255, 255, 255, .75);
        font-size: .95rem; font-weight: 700; letter-spacing: .18em; text-transform: uppercase;
    }

    @media (max-width: 767px) {
        .an-player-frame { padding: 0 14px 14px 0; }
        .an-player-frame::before { top: 14px; left: 14px; border-radius: 22px; }
        .an-player-frame::after { top: -14px; left: -14px; width: 90px; height: 90px; }
        .an-player { border-width: 6px; border-radius: 20px; }
        .an-player video, .an-player > img { border-radius: 14px; }
        .an-player-audio { padding: 14px; }
        .an-cards { grid-template-columns: 1fr; gap: 16px; }
        .an-card { padding: 28px 24px 100px; }
        .an-card-title { font-size: 1.6rem; }
        .an-card-text { font-size: 1.15rem; }
        .an-card-corner { width: 84px; height: 84px; padding: 0 16px 16px 0; }
        .an-card-corner .material-symbols-outlined { font-size: 32px; width: 32px; height: 32px; }
        .an-cta { padding: 48px 0 44px; }
        .an-cta-lead { font-size: 1.15rem; }
        .an-cta-actions .an-btn { width: 100%; justify-content: center; }
        .an-stats { gap: 12px; margin-top: 36px; }
        .an-stat-label { font-size: .7rem; letter-spacing: .08em; }
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[55vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image_url'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuCO7K3MdvhJBsjnRN7t5ahbUnpEsN6IBzUuZZwH7CLb_OOZoqM3pwpXrQV7wTMDVY18bMLximB5Zpi0iNvsgzXDtOrZt20qiq3aKc6ohFAZ7FtlLVdEfxa6mSjbk6EnoF25ccqAEmVf4y-AF3Xq6laGg5Oxwl6WoCqTAcdqgl5ZHKssfYqfv0_HJmwgVa0RIAiC8lKcDETXxxgrOLnYn8C_ELq9y7H2k5L_YYT2-KC8QAIpSMdEOtygPw4fv94jht34itrHs6p5i4rl'); ?>" 
                 alt="VVU Choir" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-20">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="material-symbols-outlined text-yellow-400 text-3xl animate-music-note">music_note</span>
                    <span class="text-lg md:text-xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['page_subtitle'] ?? 'University Anthem'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-tight tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['hero_title'] ?? 'VVU Anthem'); ?> <br>
                    <span class="text-3xl sm:text-4xl md:text-5xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-3"><?php echo strip_tags($hero['hero_subtitle'] ?? 'The Spirit of Valley View'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($hero['hero_description'] ?? 'Through Excellence, Integrity and Service; Valley View sends us to the world with peace.'); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Anthem Lyrics Section -->
    <section class="an-section">
        <div class="container">
            <div class="an-head">
                <span class="an-kicker"><span class="material-symbols-outlined">music_note</span>Sing With Us</span>
                <div class="an-heading" role="heading" aria-level="2">Official Anthem Lyrics</div>
                <p class="an-lead">Composed by Pastor Emmanuel O. Abbey, September 2011</p>
            </div>
        </div>

        <!-- One full-width band per stanza, alternating dark/light -->
        <div class="an-stanzas">
            <?php foreach ($stanzas as $i => $stanza):
                $is_dark = ($i % 2 === 0);
            ?>
            <div class="an-stanza <?php echo $is_dark ? 'an-stanza--dark' : 'an-stanza--light an-stanza--flip'; ?>">
                <div class="container">
                    <div class="an-stanza-inner">
                        <div class="an-stanza-mark" role="heading" aria-level="3">
                            <span class="an-stanza-num" aria-hidden="true"><?php echo str_pad((int) $stanza['stanza_number'], 2, '0', STR_PAD_LEFT); ?></span>
                            <span class="an-stanza-label"><?php echo strip_tags($stanza['stanza_title']); ?></span>
                        </div>
                        <div class="an-lyrics">
                            <?php echo $stanza['content']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Video Section (Below Lyrics) -->
    <section class="an-section an-section--white">
        <div class="container">
            <?php
            // `video_url` holds either an audio recording or a video clip. The
            // poster image is the section's artwork either way — a video uses it
            // as its poster frame, audio shows it behind the player.
            $anthem_title  = trim(strip_tags((string) ($video['section_title'] ?? ''))) ?: 'Listen to the Anthem';
            $anthem_desc   = trim(strip_tags((string) ($video['section_description'] ?? ''))) ?: 'Experience the official VVU Anthem - Vocal Path Cover';
            $anthem_media  = trim(strip_tags((string) ($video['video_url'] ?? ''))) ?: 'uploads/vvu-anthem-video.mp4';
            $anthem_poster = trim(strip_tags((string) ($video['video_poster_url'] ?? ''))) ?: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCO7K3MdvhJBsjnRN7t5ahbUnpEsN6IBzUuZZwH7CLb_OOZoqM3pwpXrQV7wTMDVY18bMLximB5Zpi0iNvsgzXDtOrZt20qiq3aKc6ohFAZ7FtlLVdEfxa6mSjbk6EnoF25ccqAEmVf4y-AF3Xq6laGg5Oxwl6WoCqTAcdqgl5ZHKssfYqfv0_HJmwgVa0RIAiC8lKcDETXxxgrOLnYn8C_ELq9y7H2k5L_YYT2-KC8QAIpSMdEOtygPw4fv94jht34itrHs6p5i4rl';
            $anthem_mime   = vvu_media_mime($anthem_media);
            ?>
            <div class="an-head">
                <span class="an-kicker"><span class="material-symbols-outlined">headphones</span>Listen</span>
                <div class="an-heading" role="heading" aria-level="2"><?php echo htmlspecialchars($anthem_title); ?></div>
                <p class="an-lead"><?php echo htmlspecialchars($anthem_desc); ?></p>
            </div>
            <div class="an-player-frame">
                <div class="an-player">
                    <?php if (vvu_media_is_audio($anthem_media)): ?>
                        <img src="<?php echo htmlspecialchars($anthem_poster); ?>" alt="<?php echo htmlspecialchars($anthem_title); ?>">
                        <div class="an-player-shade"></div>
                        <div class="an-player-audio">
                            <audio controls preload="metadata">
                                <source src="<?php echo htmlspecialchars($anthem_media); ?>"<?php echo $anthem_mime ? ' type="' . htmlspecialchars($anthem_mime) . '"' : ''; ?>>
                                Your browser does not support the audio tag.
                                <a href="<?php echo htmlspecialchars($anthem_media); ?>" class="underline">Download the anthem</a>.
                            </audio>
                        </div>
                    <?php else: ?>
                        <video controls poster="<?php echo htmlspecialchars($anthem_poster); ?>">
                            <source src="<?php echo htmlspecialchars($anthem_media); ?>"<?php echo $anthem_mime ? ' type="' . htmlspecialchars($anthem_mime) . '"' : ''; ?>>
                            Your browser does not support the video tag.
                        </video>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- About the Anthem Section -->
    <section class="an-section">
        <div class="container">
            <div class="an-head">
                <span class="an-kicker">The Story</span>
                <div class="an-heading" role="heading" aria-level="2">About the Anthem</div>
                <p class="an-lead">Learn about the history and the talented composer behind the VVU anthem.</p>
                <div class="an-meta">
                    <span><span class="material-symbols-outlined">edit_note</span><?php echo strip_tags($about['composer_name'] ?? 'Pastor Emmanuel O. Abbey'); ?></span>
                    <span><span class="material-symbols-outlined">calendar_month</span><?php echo strip_tags($about['composition_date'] ?? 'September 2011'); ?></span>
                </div>
            </div>

            <div class="an-cards">
                <div class="an-card">
                    <div class="an-card-title" role="heading" aria-level="3"><?php echo strip_tags($about['history_title'] ?? 'History of the Anthem'); ?></div>
                    <p class="an-card-text"><?php echo nl2br(strip_tags($about['history_content'] ?? "The Valley View University anthem was composed by Pastor Emmanuel O. Abbey in September 2011. This inspiring piece encapsulates the university's enduring values of excellence, integrity, and service.")); ?></p>
                    <span class="an-card-corner" aria-hidden="true"><span class="material-symbols-outlined">history_edu</span></span>
                </div>
                <div class="an-card">
                    <div class="an-card-title" role="heading" aria-level="3"><?php echo strip_tags($about['composer_title'] ?? 'About the Composer'); ?></div>
                    <p class="an-card-text"><?php echo nl2br(strip_tags($about['composer_content'] ?? "Pastor Emmanuel O. Abbey crafted this anthem with deep reverence for the university's mission and vision. The anthem has become a cherished symbol of VVU's identity.")); ?></p>
                    <span class="an-card-corner" aria-hidden="true"><span class="material-symbols-outlined">person</span></span>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section: same dark-blue gradient as the dark stanza bands -->
    <?php if ($cta): ?>
    <section class="an-cta">
        <div class="container">
            <div class="an-cta-head">
                <div class="an-cta-heading" role="heading" aria-level="2"><?php echo strip_tags(trim(($cta['title_line_1'] ?? '') . ' ' . ($cta['title_line_2'] ?? ''))); ?></div>
                <?php if (trim($cta['description'] ?? '') !== ''): ?>
                <p class="an-cta-lead"><?php echo strip_tags($cta['description']); ?></p>
                <?php endif; ?>
                <div class="an-cta-actions">
                    <?php if (trim($cta['btn1_text'] ?? '') !== ''): ?>
                    <a href="<?php echo strip_tags($cta['btn1_url'] ?? '#'); ?>" class="an-btn an-btn--gold">
                        <span class="material-symbols-outlined"><?php echo strip_tags($cta['btn1_icon'] ?? 'download'); ?></span>
                        <?php echo strip_tags($cta['btn1_text']); ?>
                    </a>
                    <?php endif; ?>
                    <?php if (trim($cta['btn2_text'] ?? '') !== ''): ?>
                    <a href="<?php echo strip_tags($cta['btn2_url'] ?? '#'); ?>" class="an-btn an-btn--ghost">
                        <span class="material-symbols-outlined"><?php echo strip_tags($cta['btn2_icon'] ?? 'share'); ?></span>
                        <?php echo strip_tags($cta['btn2_text']); ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="an-stats">
                <?php foreach ([1, 2, 3] as $n): ?>
                <div class="an-stat">
                    <span class="an-stat-value"><?php echo strip_tags($cta["stat{$n}_value"] ?? ''); ?></span>
                    <span class="an-stat-label"><?php echo strip_tags($cta["stat{$n}_label"] ?? ''); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php
include 'includes/footer.php';
?>
