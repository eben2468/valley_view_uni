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

// Some stored titles start with a broken emoji (mis-encoded bytes such
// as "­ƒÜ¿ Emergency Support"). Drop a leading run of non-ASCII/symbol
// characters when it is followed by a space.
$clean_title = static function ($s) {
    $s = strip_tags((string) $s);
    return preg_replace('/^[^A-Za-z0-9]+\s+/u', '', $s) ?? $s;
};

// Quick cards become links when their value is a phone number or email.
$quick_link = static function ($card) {
    $value = trim(strip_tags((string) ($card['description'] ?? '')));
    switch ($card['icon'] ?? '') {
        case 'call':        return 'tel:' . preg_replace('/[^\d+]/', '', $value);
        case 'mail':        return 'mailto:' . $value;
        case 'location_on': return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode('Valley View University, Oyibi');
        default:            return '';
    }
};

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

    /* ============================================================
       Contact page, in the style of the Mission & Vision page.
       Palette: navy #1e3a8a / #172554, gold #fbbf24 / #f59e0b, greys.
       Section headings are divs with role="heading" given the site's
       title font (Cinzel); the universal `*` rule in custom-fixes.css
       sets the body font on every element, and the legacy theme paints
       every <span> grey, so spans get explicit colours below.
       ============================================================ */
    main .ct-heading, main .ct-cta-heading, main .ct-card-heading {
        font-family: var(--vvu-title-font);
        font-weight: var(--vvu-title-weight);
        letter-spacing: normal;
    }
    .ct-section { padding: 88px 0; }
    .ct-section--white { background: #fff; }
    .dark .ct-section--white { background: #111827; }
    .ct-head { max-width: 820px; margin: 0 auto 48px; text-align: center; }
    .ct-kicker {
        display: inline-flex; align-items: center; gap: 8px; margin-bottom: 14px;
        font-size: 1rem; font-weight: 700; letter-spacing: .28em;
        text-transform: uppercase; color: #b45309;
    }
    .ct-kicker .material-symbols-outlined { font-size: 20px; letter-spacing: 0; color: inherit; }
    .dark .ct-kicker { color: #fbbf24; }
    .ct-heading { color: #1e3a8a; font-size: clamp(2.25rem, 4.5vw, 3.5rem); line-height: 1.15; }
    .dark .ct-heading { color: #fff; }
    .ct-heading::after {
        content: ""; display: block; width: 56px; height: 4px; border-radius: 4px;
        background: #fbbf24; margin: 20px auto 0;
    }
    .ct-lead { margin: 22px 0 0; color: #4b5563; font-size: 1.4rem; line-height: 1.6; font-weight: 400; }
    .dark .ct-lead { color: #9ca3af; }

    /* Shared white card */
    .ct-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 22px;
        box-shadow: 0 18px 40px -30px rgba(15, 23, 42, .45);
    }
    .dark .ct-card { background: #1f2937; border-color: #374151; }
    .ct-card-heading { color: #1e3a8a; font-size: 1.75rem; line-height: 1.2; }
    .ct-card-heading::after {
        content: ""; display: block; width: 40px; height: 3px; border-radius: 3px;
        background: #fbbf24; margin-top: 12px;
    }
    .dark .ct-card-heading { color: #bfdbfe; }
    .ct-icon {
        flex: 0 0 auto; display: inline-flex; align-items: center; justify-content: center;
        width: 48px; height: 48px; border-radius: 50%; background: #1e3a8a;
    }
    .ct-icon .material-symbols-outlined {
        font-size: 24px; color: #fff;
        font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24;
    }
    .dark .ct-icon { background: #3b82f6; }

    /* Quick contact cards, overlapping the bottom of the hero */
    .ct-quick {
        position: relative; z-index: 20; margin-top: -44px;
        display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px;
    }
    .ct-quick-card {
        display: flex; align-items: center; gap: 16px; padding: 22px;
        text-decoration: none; transition: transform .3s ease, box-shadow .3s ease;
    }
    a.ct-quick-card:hover { transform: translateY(-4px); box-shadow: 0 26px 48px -28px rgba(15, 23, 42, .55); }
    .ct-quick-label {
        display: block; color: #b45309; font-size: .85rem; font-weight: 700;
        letter-spacing: .18em; text-transform: uppercase;
    }
    .dark .ct-quick-label { color: #fbbf24; }
    .ct-quick-value { display: block; margin-top: 4px; color: #1f2937; font-size: 1.15rem; font-weight: 600; line-height: 1.4; }
    .dark .ct-quick-value { color: #f3f4f6; }

    /* Form + sidebar */
    .ct-main { display: grid; grid-template-columns: minmax(0, 3fr) minmax(0, 2fr); gap: 28px; align-items: start; }
    .ct-form-card { padding: 40px; }
    .ct-form-card .ct-kicker { margin-bottom: 10px; }
    .ct-form-card .ct-heading::after { margin-left: 0; }
    .ct-form { margin-top: 32px; display: grid; gap: 20px; }
    .ct-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
    .ct-field label {
        display: block; margin-bottom: 8px; color: #1e3a8a;
        font-size: 1.05rem; font-weight: 700;
    }
    .dark .ct-field label { color: #bfdbfe; }
    .ct-input {
        width: 100%; padding: 14px 18px; border-radius: 14px;
        background: #f8fafc; border: 1px solid #e5e7eb; color: #111827;
        font-size: 1.15rem; outline: none;
        transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
    }
    .ct-input:focus { background: #fff; border-color: #1e3a8a; box-shadow: 0 0 0 4px rgba(30, 58, 138, .12); }
    .dark .ct-input { background: #111827; border-color: #374151; color: #f3f4f6; }
    .ct-select-wrap { position: relative; }
    .ct-select-wrap select { appearance: none; -webkit-appearance: none; padding-right: 48px; }
    .ct-select-wrap .material-symbols-outlined {
        position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
        color: #1e3a8a; pointer-events: none;
    }
    textarea.ct-input { resize: vertical; min-height: 150px; }

    .ct-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 10px;
        padding: 14px 28px; border-radius: 999px; border: 0; cursor: pointer;
        font-size: 1.1rem; font-weight: 700; text-decoration: none;
        transition: background-color .25s ease, color .25s ease, transform .25s ease;
    }
    .ct-btn .material-symbols-outlined { font-size: 22px; color: inherit; }
    .ct-btn:hover { transform: translateY(-2px); }
    .ct-btn--navy { background: #1e3a8a; color: #fff; justify-self: start; }
    .ct-btn--navy:hover { background: #172554; color: #fff; }
    .ct-btn--gold { background: #fbbf24; color: #172554; box-shadow: 0 12px 24px -14px rgba(251, 191, 36, .9); }
    .ct-btn--gold:hover { background: #fcd34d; color: #172554; }
    .ct-btn--ghost { color: #fff; border: 1.5px solid rgba(255, 255, 255, .6); }
    .ct-btn--ghost:hover { background: #fff; color: #1e3a8a; }

    .ct-side { display: grid; gap: 20px; }
    .ct-side-card { padding: 28px; }
    .ct-list { display: grid; gap: 18px; margin-top: 22px; }
    .ct-list-item { display: flex; gap: 14px; align-items: flex-start; }
    .ct-list-title { display: block; color: #1f2937; font-size: 1.1rem; font-weight: 700; }
    .dark .ct-list-title { color: #f3f4f6; }
    .ct-list-text { display: block; margin-top: 2px; color: #4b5563; font-size: 1.05rem; line-height: 1.5; overflow-wrap: anywhere; }
    .dark .ct-list-text { color: #9ca3af; }

    .ct-socials { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 22px; }
    .ct-social {
        display: inline-flex; align-items: center; justify-content: center;
        width: 46px; height: 46px; border-radius: 50%;
        border: 1.5px solid #1e3a8a; color: #1e3a8a; text-decoration: none;
        transition: background-color .25s ease, color .25s ease, transform .25s ease;
    }
    .ct-social i { font-size: 18px; color: inherit; }
    .ct-social:hover { background: #1e3a8a; color: #fff; transform: translateY(-2px); }
    .dark .ct-social { border-color: #93c5fd; color: #93c5fd; }
    .dark .ct-social:hover { background: #93c5fd; color: #111827; }

    .ct-alert {
        position: relative; overflow: hidden; padding: 28px; border-radius: 22px;
        background: linear-gradient(120deg, #1d4ed8 0%, #1e3a8a 55%, #172554 100%);
    }
    .ct-alert::after {
        content: ""; position: absolute; right: -60px; top: -60px;
        width: 180px; height: 180px; border-radius: 50%; background: rgba(255, 255, 255, .06);
    }
    .ct-alert > * { position: relative; z-index: 1; }
    .ct-alert-title { color: #fff; font-size: 1.4rem; font-weight: 700; }
    .ct-alert-text { margin: 8px 0 18px; color: rgba(255, 255, 255, .85); font-size: 1.1rem; line-height: 1.55; }
    .ct-alert .ct-btn { width: 100%; }

    .ct-dial { padding: 28px; border-radius: 22px; background: linear-gradient(120deg, #e0e7f1 0%, #cfd9e8 100%); }
    .dark .ct-dial { background: linear-gradient(120deg, #1f2937 0%, #111827 100%); }
    .ct-dial-title { color: #1e3a8a; font-size: 1.4rem; font-weight: 700; }
    .dark .ct-dial-title { color: #bfdbfe; }
    .ct-dial-text { margin: 8px 0 16px; color: #374151; font-size: 1.1rem; line-height: 1.55; }
    .dark .ct-dial-text { color: #d1d5db; }
    .ct-dial-code {
        display: block; padding: 14px; border-radius: 16px; text-align: center;
        background: #fff; color: #1e3a8a; font-size: 2rem; font-weight: 800; letter-spacing: .08em;
    }
    .dark .ct-dial-code { background: #111827; color: #fbbf24; }

    /* University contact information: Vision-band panel */
    .ct-info {
        position: relative; overflow: hidden; padding: 56px 0;
        background: linear-gradient(120deg, #1d4ed8 0%, #1e3a8a 55%, #172554 100%);
    }
    .ct-info::after {
        content: ""; position: absolute; right: -120px; top: -120px;
        width: 360px; height: 360px; border-radius: 50%; background: rgba(255, 255, 255, .06);
    }
    .ct-info .container { position: relative; z-index: 1; }
    .ct-info .ct-kicker { color: #fbbf24; display: flex; justify-content: center; }
    .ct-info .ct-heading { color: #fff; text-align: center; }
    .ct-info-grid {
        display: grid; grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px 32px; max-width: 1100px; margin: 40px auto 0;
    }
    .ct-info-item { padding-top: 18px; border-top: 1px solid rgba(255, 255, 255, .25); }
    .ct-info-label {
        display: block; color: #fbbf24; font-size: .85rem; font-weight: 700;
        letter-spacing: .18em; text-transform: uppercase;
    }
    .ct-info-value { display: block; margin-top: 8px; color: rgba(255, 255, 255, .92); font-size: 1.15rem; line-height: 1.55; }
    a.ct-info-value { text-decoration: none; }
    a.ct-info-value:hover { color: #fcd34d; }

    /* Departments */
    .ct-depts {
        display: grid; grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px; max-width: 1280px; margin: 0 auto;
    }
    .ct-dept { padding: 22px; transition: transform .3s ease, border-color .3s ease, box-shadow .3s ease; }
    .ct-dept:hover { transform: translateY(-4px); border-color: #1e3a8a; box-shadow: 0 26px 48px -30px rgba(15, 23, 42, .55); }
    .ct-dept-head { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
    .ct-dept-head .ct-icon { width: 42px; height: 42px; }
    .ct-dept-head .ct-icon .material-symbols-outlined { font-size: 22px; }
    .ct-dept-name { color: #1e3a8a; font-size: 1.15rem; font-weight: 700; line-height: 1.3; }
    .dark .ct-dept-name { color: #bfdbfe; }
    .ct-dept-links { display: grid; gap: 6px; }
    .ct-dept-link {
        display: inline-flex; align-items: center; gap: 8px;
        color: #374151; font-size: 1.05rem; text-decoration: none; overflow-wrap: anywhere;
        transition: color .2s ease;
    }
    .ct-dept-link .material-symbols-outlined { font-size: 18px; color: #f59e0b; }
    .ct-dept-link:hover { color: #1e3a8a; }
    .dark .ct-dept-link { color: #d1d5db; }
    .dark .ct-dept-link:hover { color: #93c5fd; }
    .ct-dept-note { color: #6b7280; font-size: .95rem; font-style: italic; padding-left: 26px; }

    /* FAQ accordion */
    .ct-faqs { max-width: 900px; margin: 0 auto; display: grid; gap: 12px; }
    .ct-faq { padding: 0; overflow: hidden; transition: border-color .3s ease; }
    .ct-faq.active { border-color: #1e3a8a; }
    .ct-faq-q {
        width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 16px;
        padding: 20px 24px; background: none; border: 0; cursor: pointer; text-align: left;
    }
    .ct-faq-q-text { color: #1e3a8a; font-size: 1.25rem; font-weight: 700; line-height: 1.4; }
    .dark .ct-faq-q-text { color: #bfdbfe; }
    .ct-faq-chev {
        flex: 0 0 auto; display: inline-flex; align-items: center; justify-content: center;
        width: 36px; height: 36px; border-radius: 50%; background: rgba(30, 58, 138, .08);
        transition: background-color .3s ease, transform .3s ease;
    }
    .ct-faq-chev .material-symbols-outlined { color: #1e3a8a; font-size: 24px; }
    .ct-faq.active .ct-faq-chev { background: #fbbf24; transform: rotate(180deg); }
    .ct-faq.active .ct-faq-chev .material-symbols-outlined { color: #172554; }
    .ct-faq-a { max-height: 0; overflow: hidden; transition: max-height .4s ease; }
    .ct-faq.active .ct-faq-a { max-height: 600px; }
    .ct-faq-a p { margin: 0; padding: 0 24px 22px; color: #4b5563; font-size: 1.15rem; line-height: 1.7; }
    .ct-faq-a a { color: #1e3a8a; font-weight: 600; text-decoration: underline; }
    .dark .ct-faq-a p { color: #d1d5db; }

    /* Map: white mat with an offset navy panel, plus an info card */
    .ct-map-frame { position: relative; isolation: isolate; padding: 0 24px 24px 0; max-width: 1200px; margin: 0 auto; }
    .ct-map-frame::before {
        content: ""; position: absolute; z-index: -1;
        top: 24px; left: 24px; right: 0; bottom: 0; border-radius: 28px;
        background: linear-gradient(135deg, #1e3a8a 0%, #172554 100%);
    }
    .ct-map-frame::after {
        content: ""; position: absolute; z-index: -1;
        top: -22px; left: -22px; width: 130px; height: 130px;
        background-image: radial-gradient(#f59e0b 2px, transparent 2.5px);
        background-size: 16px 16px; opacity: .7;
    }
    .ct-map {
        position: relative; overflow: hidden; height: 460px;
        border-radius: 26px; border: 8px solid #fff; background: #e5e7eb;
        box-shadow: 0 30px 60px -30px rgba(15, 23, 42, .55);
    }
    .dark .ct-map { border-color: #1f2937; }
    .ct-map iframe { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; border-radius: 18px; }
    .ct-map-card { position: absolute; top: 20px; left: 20px; max-width: 340px; padding: 24px; }
    .ct-map-card-title { color: #1e3a8a; font-size: 1.35rem; font-weight: 700; }
    .dark .ct-map-card-title { color: #bfdbfe; }
    .ct-map-card-text { margin: 8px 0 14px; color: #4b5563; font-size: 1.05rem; line-height: 1.55; }
    .dark .ct-map-card-text { color: #d1d5db; }
    .ct-map-card-link {
        display: inline-flex; align-items: center; gap: 6px; color: #b45309;
        font-size: .95rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; text-decoration: none;
    }
    .ct-map-card-link .material-symbols-outlined { font-size: 20px; color: inherit; transition: transform .3s ease; }
    .ct-map-card-link:hover .material-symbols-outlined { transform: translateX(4px); }

    /* Call to action */
    .ct-cta {
        position: relative; overflow: hidden; padding: 64px 0 56px;
        background: linear-gradient(120deg, #1d4ed8 0%, #1e3a8a 55%, #172554 100%);
    }
    .ct-cta::before, .ct-cta::after {
        content: ""; position: absolute; border-radius: 50%; pointer-events: none;
        background: rgba(255, 255, 255, .05);
    }
    .ct-cta::before { width: 520px; height: 520px; right: -180px; top: -200px; }
    .ct-cta::after { width: 360px; height: 360px; left: -140px; bottom: -180px; }
    .ct-cta .container { position: relative; z-index: 1; }
    .ct-cta-head { max-width: 820px; margin: 0 auto; text-align: center; }
    .ct-cta-head .ct-kicker { color: #fbbf24; }
    .ct-cta-heading { color: #fff; font-size: clamp(2.25rem, 4.5vw, 3.5rem); line-height: 1.15; }
    .ct-cta-heading::after {
        content: ""; display: block; width: 56px; height: 4px; border-radius: 4px;
        background: #fbbf24; margin: 20px auto 0;
    }
    .ct-cta-lead { margin: 22px 0 0; color: rgba(255, 255, 255, .85); font-size: 1.35rem; line-height: 1.6; font-weight: 400; }
    .ct-cta-actions { display: flex; flex-wrap: wrap; gap: 14px; justify-content: center; margin-top: 34px; }
    .ct-stats {
        display: grid; grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px 36px; max-width: 1000px; margin: 48px auto 0;
    }
    .ct-stat { padding: 20px 4px 4px; text-align: center; border-top: 1px solid rgba(255, 255, 255, .25); }
    .ct-stat-value { display: block; color: #fbbf24; font-size: clamp(1.75rem, 3.5vw, 2.75rem); font-weight: 700; line-height: 1.1; }
    .ct-stat-label {
        display: block; margin-top: 8px; color: rgba(255, 255, 255, .75);
        font-size: .95rem; font-weight: 700; letter-spacing: .18em; text-transform: uppercase;
    }

    @media (max-width: 1279px) {
        .ct-depts { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (max-width: 1099px) {
        .ct-quick { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ct-main { grid-template-columns: 1fr; }
        .ct-side { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 1023px) {
        .ct-depts { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ct-info-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 767px) {
        .ct-section { padding: 60px 0; }
        .ct-head { margin-bottom: 32px; }
        .ct-kicker { font-size: .85rem; }
        .ct-lead, .ct-cta-lead { font-size: 1.15rem; }
        .ct-quick { margin-top: -32px; gap: 12px; }
        .ct-quick-card { padding: 18px; }
        .ct-form-card { padding: 26px 20px; }
        .ct-row { grid-template-columns: 1fr; }
        .ct-btn--navy { width: 100%; }
        .ct-side { grid-template-columns: 1fr; }
        .ct-map-frame { padding: 0 14px 14px 0; }
        .ct-map-frame::before { top: 14px; left: 14px; border-radius: 22px; }
        .ct-map-frame::after { top: -14px; left: -14px; width: 90px; height: 90px; }
        .ct-map { height: 360px; border-width: 6px; border-radius: 20px; }
        .ct-map iframe { border-radius: 14px; }
        .ct-map-card { position: static; max-width: none; margin-top: 16px; }
        .ct-faq-q { padding: 18px; }
        .ct-faq-q-text { font-size: 1.1rem; }
        .ct-faq-a p { padding: 0 18px 18px; font-size: 1.05rem; }
        .ct-cta { padding: 48px 0 44px; }
        .ct-cta-actions .ct-btn { width: 100%; }
        .ct-stats { gap: 12px; margin-top: 36px; }
        .ct-stat-label { font-size: .7rem; letter-spacing: .08em; }
    }
    @media (max-width: 639px) {
        .ct-quick, .ct-depts { grid-template-columns: 1fr; }
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

    <!-- Quick Contact Cards, overlapping the hero -->
    <div class="container">
        <div class="ct-quick">
            <?php foreach ($quick_cards as $card):
                $href = $quick_link($card);
                $tag = $href ? 'a' : 'div';
            ?>
            <<?php echo $tag; ?> class="ct-card ct-quick-card"<?php if ($href): ?> href="<?php echo htmlspecialchars($href); ?>"<?php if (strpos($href, 'http') === 0): ?> target="_blank" rel="noopener"<?php endif; ?><?php endif; ?>>
                <span class="ct-icon"><span class="material-symbols-outlined"><?php echo strip_tags($card['icon'] ?? ''); ?></span></span>
                <span>
                    <span class="ct-quick-label"><?php echo strip_tags($card['title'] ?? ''); ?></span>
                    <span class="ct-quick-value"><?php echo strip_tags($card['description'] ?? ''); ?></span>
                </span>
            </<?php echo $tag; ?>>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Main Content: Form + Sidebar -->
    <section class="ct-section">
        <div class="container">
            <div class="ct-main">
                <!-- Contact Form -->
                <div class="ct-card ct-form-card">
                    <span class="ct-kicker"><span class="material-symbols-outlined">send</span>Write to Us</span>
                    <div class="ct-heading" role="heading" aria-level="2">Send Us a Message</div>

                    <form action="contact_process.php" method="POST" class="ct-form">
                        <div class="ct-row">
                            <div class="ct-field">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" placeholder="Ebenezer Owusu" required class="ct-input">
                            </div>
                            <div class="ct-field">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" placeholder="ebenezer@example.com" required class="ct-input">
                            </div>
                        </div>

                        <div class="ct-row">
                            <div class="ct-field">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" placeholder="+233 XX XXX XXXX" class="ct-input">
                            </div>
                            <div class="ct-field">
                                <label for="topic">Topic of Inquiry</label>
                                <div class="ct-select-wrap">
                                    <select id="topic" name="topic" class="ct-input">
                                        <option>Admissions Question</option>
                                        <option>General Inquiry</option>
                                        <option>Technical Support</option>
                                        <option>Alumni Relations</option>
                                        <option>Financial Aid</option>
                                        <option>Academic Affairs</option>
                                        <option>Student Life</option>
                                        <option>Chaplaincy</option>
                                    </select>
                                    <span class="material-symbols-outlined" aria-hidden="true">expand_more</span>
                                </div>
                            </div>
                        </div>

                        <div class="ct-field">
                            <label for="message">Your Message</label>
                            <textarea id="message" name="message" placeholder="How can we help you today?" rows="5" required class="ct-input"></textarea>
                        </div>

                        <button type="submit" class="ct-btn ct-btn--navy">
                            Send Message
                            <span class="material-symbols-outlined">send</span>
                        </button>
                    </form>
                </div>

                <!-- Sidebar -->
                <div class="ct-side">
                    <div class="ct-card ct-side-card">
                        <div class="ct-card-heading" role="heading" aria-level="3">Postal Addresses</div>
                        <div class="ct-list">
                            <?php foreach ($postal as $p): ?>
                            <div class="ct-list-item">
                                <span class="ct-icon"><span class="material-symbols-outlined"><?php echo strip_tags($p['icon'] ?? 'mail'); ?></span></span>
                                <span>
                                    <span class="ct-list-title"><?php echo strip_tags($p['title'] ?? ''); ?></span>
                                    <span class="ct-list-text"><?php echo strip_tags($p['description'] ?? ''); ?></span>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if ($socials): ?>
                    <div class="ct-card ct-side-card">
                        <div class="ct-card-heading" role="heading" aria-level="3">Follow Our Journey</div>
                        <div class="ct-socials">
                            <?php foreach ($socials as $s): ?>
                            <a href="<?php echo strip_tags($s['url'] ?? '#'); ?>" class="ct-social" aria-label="<?php echo htmlspecialchars(ucfirst(str_replace(['fa-', '-play'], '', $s['icon'] ?? 'social'))); ?>">
                                <i class="fa <?php echo strip_tags($s['icon'] ?? ''); ?>"></i>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($emergency): ?>
                    <div class="ct-alert">
                        <div class="ct-alert-title"><?php echo htmlspecialchars($clean_title($emergency['title'] ?? '')); ?></div>
                        <p class="ct-alert-text"><?php echo strip_tags($emergency['description'] ?? ''); ?></p>
                        <a href="tel:<?php echo preg_replace('/[^\d+]/', '', strip_tags($emergency['main_value'] ?? '')); ?>" class="ct-btn ct-btn--gold">
                            <span class="material-symbols-outlined">emergency</span>
                            <?php echo strip_tags($emergency['btn_text'] ?? ''); ?>
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if ($ussd): ?>
                    <div class="ct-dial">
                        <div class="ct-dial-title"><?php echo htmlspecialchars($clean_title($ussd['title'] ?? '')); ?></div>
                        <p class="ct-dial-text"><?php echo strip_tags($ussd['description'] ?? ''); ?></p>
                        <span class="ct-dial-code"><?php echo strip_tags($ussd['main_value'] ?? ''); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- University Contact Information -->
    <?php if ($main_info): ?>
    <section class="ct-info">
        <div class="container">
            <span class="ct-kicker"><span class="material-symbols-outlined">location_city</span>Our Campuses</span>
            <div class="ct-heading" role="heading" aria-level="2"><?php echo strip_tags($main_info['title']); ?></div>
            <div class="ct-info-grid">
                <?php foreach (['address_1', 'address_2', 'address_3'] as $a): if (trim($main_info[$a] ?? '') === '') continue; ?>
                <div class="ct-info-item">
                    <span class="ct-info-label">Address</span>
                    <span class="ct-info-value"><?php echo strip_tags($main_info[$a]); ?></span>
                </div>
                <?php endforeach; ?>
                <?php if (trim($main_info['telephone'] ?? '') !== ''): ?>
                <div class="ct-info-item">
                    <span class="ct-info-label">Telephone</span>
                    <span class="ct-info-value"><?php echo strip_tags($main_info['telephone']); ?></span>
                </div>
                <?php endif; ?>
                <?php if (trim($main_info['email'] ?? '') !== ''): ?>
                <div class="ct-info-item">
                    <span class="ct-info-label">Email Address</span>
                    <span class="ct-info-value"><?php echo strip_tags($main_info['email']); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Department Contacts Section -->
    <section class="ct-section ct-section--white">
        <div class="container">
            <div class="ct-head">
                <span class="ct-kicker"><?php echo strip_tags($dept_header['badge_text'] ?? 'Directory'); ?></span>
                <div class="ct-heading" role="heading" aria-level="2"><?php echo strip_tags($dept_header['title'] ?? 'Departmental Contacts'); ?></div>
                <p class="ct-lead"><?php echo strip_tags($dept_header['description'] ?? ''); ?></p>
            </div>

            <div class="ct-depts">
                <?php foreach ($depts as $d): ?>
                <div class="ct-card ct-dept">
                    <div class="ct-dept-head">
                        <span class="ct-icon"><span class="material-symbols-outlined"><?php echo strip_tags($d['icon'] ?? 'school'); ?></span></span>
                        <div class="ct-dept-name" role="heading" aria-level="3"><?php echo strip_tags($d['name'] ?? ''); ?></div>
                    </div>
                    <div class="ct-dept-links">
                        <?php foreach (['phone_1', 'phone_2'] as $ph): if (empty($d[$ph])) continue; ?>
                        <a href="tel:<?php echo str_replace(' ', '', strip_tags($d[$ph])); ?>" class="ct-dept-link">
                            <span class="material-symbols-outlined">call</span><?php echo strip_tags($d[$ph]); ?>
                        </a>
                        <?php endforeach; ?>
                        <?php if (!empty($d['email'])): ?>
                        <a href="mailto:<?php echo strip_tags($d['email']); ?>" class="ct-dept-link">
                            <span class="material-symbols-outlined">mail</span><?php echo strip_tags($d['email']); ?>
                        </a>
                        <?php endif; ?>
                        <?php if (($d['name'] ?? '') == 'FASS'): ?>
                        <span class="ct-dept-note">WhatsApp Only</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="ct-section">
        <div class="container">
            <div class="ct-head">
                <span class="ct-kicker"><span class="material-symbols-outlined">help</span><?php echo strip_tags($faq_header['badge_text'] ?? 'Quick Help'); ?></span>
                <div class="ct-heading" role="heading" aria-level="2"><?php echo strip_tags($faq_header['title'] ?? 'Frequently Asked Questions'); ?></div>
                <p class="ct-lead"><?php echo strip_tags($faq_header['description'] ?? ''); ?></p>
            </div>

            <div class="ct-faqs">
                <?php foreach ($faqs as $f): ?>
                <div class="ct-card ct-faq">
                    <button type="button" class="ct-faq-q" aria-expanded="false"
                            onclick="var c=this.parentNode;c.classList.toggle('active');this.setAttribute('aria-expanded',c.classList.contains('active'));">
                        <span class="ct-faq-q-text"><?php echo strip_tags($f['question'] ?? ''); ?></span>
                        <span class="ct-faq-chev"><span class="material-symbols-outlined">expand_more</span></span>
                    </button>
                    <div class="ct-faq-a">
                        <p><?php echo $f['answer']; // Answer can contain HTML links ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="ct-section ct-section--white">
        <div class="container">
            <div class="ct-head">
                <span class="ct-kicker"><span class="material-symbols-outlined">map</span>Find Us</span>
                <div class="ct-heading" role="heading" aria-level="2">Getting to VVU</div>
            </div>
            <div class="ct-map-frame">
                <div class="ct-map">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3969.8051786191494!2d-0.0886846241130638!3d5.740889931720516!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfdf70f6c2f9d14f%3A0x6b4fb0f97576571a!2sValley%20View%20University!5e0!3m2!1sen!2sgh!4v1709123456789!5m2!1sen!2sgh"
                        title="Map of Valley View University"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                    <?php if ($map_overlay): ?>
                    <div class="ct-card ct-map-card">
                        <div class="ct-map-card-title"><?php echo strip_tags($map_overlay['title'] ?? ''); ?></div>
                        <p class="ct-map-card-text"><?php echo strip_tags($map_overlay['description'] ?? ''); ?></p>
                        <a href="<?php echo strip_tags($map_overlay['link_url'] ?? '#'); ?>" class="ct-map-card-link">
                            <?php echo strip_tags($map_overlay['link_text'] ?? ''); ?> <span class="material-symbols-outlined">arrow_forward</span>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="ct-cta">
        <div class="container">
            <div class="ct-cta-head">
                <?php if (trim($cta['subtitle'] ?? '') !== ''): ?>
                <span class="ct-kicker"><?php echo strip_tags($cta['subtitle']); ?></span>
                <?php endif; ?>
                <div class="ct-cta-heading" role="heading" aria-level="2"><?php echo strip_tags($cta['title'] ?? ''); ?></div>
                <?php if (trim($cta['description'] ?? '') !== ''): ?>
                <p class="ct-cta-lead"><?php echo strip_tags($cta['description']); ?></p>
                <?php endif; ?>
                <div class="ct-cta-actions">
                    <?php if (trim($cta['btn1_text'] ?? '') !== ''): ?>
                    <a href="<?php echo strip_tags($cta['btn1_url'] ?? '#'); ?>" class="ct-btn ct-btn--gold">
                        <span class="material-symbols-outlined">info</span>
                        <?php echo strip_tags($cta['btn1_text']); ?>
                    </a>
                    <?php endif; ?>
                    <?php if (trim($cta['btn2_text'] ?? '') !== ''): ?>
                    <a href="<?php echo strip_tags($cta['btn2_url'] ?? '#'); ?>" class="ct-btn ct-btn--ghost">
                        <span class="material-symbols-outlined">how_to_reg</span>
                        <?php echo strip_tags($cta['btn2_text']); ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="ct-stats">
                <?php foreach ([1, 2, 3] as $n): ?>
                <div class="ct-stat">
                    <span class="ct-stat-value"><?php echo strip_tags($cta["stat{$n}_value"] ?? ''); ?></span>
                    <span class="ct-stat-label"><?php echo strip_tags($cta["stat{$n}_label"] ?? ''); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>