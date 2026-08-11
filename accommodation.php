<?php
$page_title = "Accommodation - Valley View University";
$active_page = "life_at_vvu";
include 'includes/header.php';
require_once 'includes/campus_life_content_helper.php';

// Fetch content from database
$content = getAccommodationContent($pdo);

// Use default values if no content found
if (!$content) {
    $content = [
        'hero_title' => 'Accommodation',
        'hero_subtitle' => 'Comfortable and secure housing for students on campus',
        'hero_image' => 'images/accommodation_hero.jpg',
        'intro_heading' => 'Campus Housing',
        'intro_text' => 'Valley View University provides quality accommodation facilities for students who wish to live on campus.',
        'intro_image' => '',
        'facilities_description' => 'Our residence halls offer modern amenities and a safe environment.',
        'room_types_description' => 'Various room types available to suit different needs and budgets.',
        'application_process' => 'Apply through the student portal during registration.',
        'rules_and_regulations' => 'All residents must adhere to university housing policies.',
        'off_campus_heading' => 'Off-Campus Living',
        'off_campus_text' => '',
        'cta_heading' => 'Ready to Apply?',
        'cta_text' => 'Contact the Housing Office for more information about accommodation options.'
    ];
}

$halls    = getAccommodationHalls($pdo);
$features = getAccommodationFeatures($pdo);

// Split halls by residence type so each gets its own labelled group.
$halls_male   = array_values(array_filter($halls, fn($h) => $h['type'] === 'male'));
$halls_female = array_values(array_filter($halls, fn($h) => $h['type'] !== 'male'));

/**
 * "At a glance" figures. Every number is counted from the halls actually in
 * the CMS rather than hard-coded, so the strip stays true as halls are added.
 */
$stats = [
    ['value' => count($halls),        'label' => 'Residence Halls', 'icon' => 'apartment'],
    ['value' => count($halls_male),   'label' => "Men's Halls",     'icon' => 'man'],
    ['value' => count($halls_female), 'label' => "Women's Halls",   'icon' => 'woman'],
    ['value' => '24/7',               'label' => 'Campus Security', 'icon' => 'shield'],
];

// Housing essentials — each panel is filled from a CMS field that the admin
// already maintains under Campus Life → Accommodation.
$essentials = [
    [
        'icon'  => 'meeting_room',
        'eyebrow' => 'Rooms',
        'title' => 'Room Types',
        'text'  => $content['room_types_description'] ?? '',
    ],
    [
        'icon'  => 'domain',
        'eyebrow' => 'Facilities',
        'title' => 'Hall Facilities',
        'text'  => $content['facilities_description'] ?? '',
    ],
    [
        'icon'  => 'assignment_turned_in',
        'eyebrow' => 'Process',
        'title' => 'How to Apply',
        'text'  => $content['application_process'] ?? '',
    ],
    [
        'icon'  => 'gavel',
        'eyebrow' => 'Conduct',
        'title' => 'Rules & Regulations',
        'text'  => $content['rules_and_regulations'] ?? '',
    ],
];
$essentials = array_values(array_filter($essentials, fn($e) => trim((string) $e['text']) !== ''));

// Related pages that already exist on the site.
$related = [
    ['href' => 'food_services.php',              'icon' => 'restaurant',   'title' => 'Food Services',      'text' => 'Cafeteria meals and dining options on campus.'],
    ['href' => 'student_handbook.php',           'icon' => 'menu_book',    'title' => 'Student Handbook',   'text' => 'Policies and standards every resident agrees to.'],
    ['href' => 'campus_map_&_facilities_page.php', 'icon' => 'map',        'title' => 'Campus Map',         'text' => 'Find the halls and facilities around campus.'],
    ['href' => 'student_life.php',               'icon' => 'diversity_3',  'title' => 'Student Life',       'text' => 'Clubs, worship, sport and student associations.'],
];
$related = array_values(array_filter($related, fn($r) => is_file(__DIR__ . '/' . $r['href'])));
?>

<style>
    /* ==========================================================================
       ACCOMMODATION PAGE
       Namespaced `acc-` so nothing here can leak into other templates.
       ========================================================================== */
    .acc {
        --acc-navy: #002147;
        --acc-navy-deep: #00152e;
        --acc-gold: #f0b429;
        --acc-ember: #f26838;
        --acc-ink: #12233b;
        --acc-muted: #5b6b80;
        --acc-hair: #e4e9f0;
        --acc-paper: #fbfaf7;
        --acc-ease: cubic-bezier(.16, .84, .44, 1);
        background: #fff;
    }

    .acc-shell {
        width: 100%;
        max-width: 1240px;
        margin: 0 auto;
        padding: 0 22px;
    }

    .acc-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 7px 16px;
        border-radius: 999px;
        background: rgba(240, 180, 41, .16);
        color: #8a6410;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .16em;
        text-transform: uppercase;
    }

    .acc-eyebrow .material-symbols-outlined {
        font-size: 17px;
    }

    .acc-h2 {
        margin: 18px 0 0;
        font-family: 'Cinzel', Georgia, serif;
        font-size: clamp(26px, 3.4vw, 42px) !important;
        line-height: 1.18;
        color: var(--acc-navy) !important;
    }

    .acc-lede {
        margin: 16px auto 0;
        max-width: 62ch;
        font-size: clamp(15px, 1.3vw, 17px);
        line-height: 1.75;
        color: var(--acc-muted);
    }

    .acc-center {
        text-align: center;
    }

    .acc-rule {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        margin: 18px auto 0;
        max-width: 190px;
    }

    .acc-rule::before,
    .acc-rule::after {
        content: "";
        flex: 1 1 auto;
        height: 1px;
        background: linear-gradient(90deg, rgba(240, 180, 41, 0), var(--acc-gold));
    }

    .acc-rule::after {
        transform: rotate(180deg);
    }

    .acc-rule i {
        width: 6px;
        height: 6px;
        background: var(--acc-gold);
        transform: rotate(45deg);
    }

    /* ---------------------------------------------------------------- hero
       The hero keeps its original Core Values design and Tailwind markup —
       only the sections below it were rebuilt. */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slowZoom {
        0% {
            transform: scale(1);
        }

        100% {
            transform: scale(1.1);
        }
    }

    .animate-slow-zoom {
        animation: slowZoom 20s linear infinite alternate;
    }

    .animate-fadeInUp {
        animation: fadeInUp 0.6s ease-out forwards;
    }

    .acc-hero__cta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 30px;
    }

    .acc-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 26px;
        border-radius: 999px;
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: .11em;
        text-transform: uppercase;
        text-decoration: none;
        transition: transform .22s var(--acc-ease), box-shadow .22s var(--acc-ease),
            background-color .22s var(--acc-ease), color .22s var(--acc-ease);
    }

    .acc-btn .material-symbols-outlined {
        font-size: 19px;
    }

    .acc-btn--gold {
        background: linear-gradient(135deg, #f7d67e, var(--acc-gold));
        color: var(--acc-navy-deep) !important;
        box-shadow: 0 12px 26px -14px rgba(240, 180, 41, .95);
    }

    .acc-btn--ghost {
        background: rgba(255, 255, 255, .1);
        border: 1px solid rgba(255, 255, 255, .3);
        color: #fff !important;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    .acc-btn--navy {
        background: var(--acc-navy);
        color: #fff !important;
    }

    .acc-btn--outline {
        background: transparent;
        box-shadow: inset 0 0 0 1.5px var(--acc-navy);
        color: var(--acc-navy) !important;
    }

    .acc-btn:hover {
        transform: translateY(-2px);
        text-decoration: none;
    }

    .acc-btn--ghost:hover {
        background: rgba(255, 255, 255, .2);
    }

    .acc-btn--navy:hover {
        background: var(--acc-navy-deep);
    }

    .acc-btn--outline:hover {
        background: var(--acc-navy);
        color: #fff !important;
    }

    /* --------------------------------------------------------------- stats */
    /* Sits below the restored hero rather than overlapping it. */
    .acc-stats {
        position: relative;
        z-index: 2;
        padding-top: 46px;
    }

    .acc-stats__grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1px;
        background: var(--acc-hair);
        border: 1px solid var(--acc-hair);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 26px 50px -30px rgba(0, 33, 71, .45);
    }

    .acc-stat {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        padding: 26px 14px;
        background: #fff;
        text-align: center;
    }

    .acc-stat .material-symbols-outlined {
        font-size: 26px;
        color: var(--acc-gold);
    }

    .acc-stat b {
        font-family: 'Cinzel', Georgia, serif;
        font-size: clamp(22px, 2.6vw, 32px);
        font-weight: 600;
        line-height: 1;
        color: var(--acc-navy);
    }

    .acc-stat span.lbl {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--acc-muted);
    }

    /* ------------------------------------------------------------- section */
    .acc-section {
        padding: 76px 0;
    }

    .acc-section--tint {
        background: var(--acc-paper);
        border-top: 1px solid var(--acc-hair);
        border-bottom: 1px solid var(--acc-hair);
    }

    /* ---------------------------------------------------------------- intro */
    .acc-intro {
        display: grid;
        grid-template-columns: 1.05fr .95fr;
        gap: 56px;
        align-items: center;
    }

    .acc-intro__body p {
        margin: 16px 0 0;
        font-size: 16px;
        line-height: 1.8;
        color: var(--acc-muted);
    }

    .acc-intro__figure {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 30px 60px -34px rgba(0, 33, 71, .55);
    }

    .acc-intro__figure img {
        display: block;
        width: 100%;
        height: 100%;
        min-height: 320px;
        object-fit: cover;
    }

    /* ---------------------------------------------------------------- halls */
    .acc-group + .acc-group {
        margin-top: 54px;
    }

    .acc-group__head {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 24px;
    }

    .acc-group__head h3 {
        margin: 0;
        font-family: 'Cinzel', Georgia, serif;
        font-size: 15px !important;
        font-weight: 700 !important;
        letter-spacing: .17em;
        text-transform: uppercase;
        color: var(--acc-navy) !important;
        white-space: nowrap;
    }

    .acc-group__head .line {
        flex: 1 1 auto;
        height: 1px;
        background: linear-gradient(90deg, var(--acc-gold), rgba(240, 180, 41, 0));
    }

    .acc-group__head .count {
        padding: 4px 12px;
        border-radius: 999px;
        background: rgba(0, 33, 71, .07);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .1em;
        color: var(--acc-muted);
        white-space: nowrap;
    }

    .acc-halls {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
        gap: 26px;
    }

    .acc-hall {
        display: flex;
        flex-direction: column;
        background: #fff;
        border: 1px solid var(--acc-hair);
        border-radius: 18px;
        overflow: hidden;
        transition: transform .3s var(--acc-ease), box-shadow .3s var(--acc-ease),
            border-color .3s var(--acc-ease);
    }

    .acc-hall:hover {
        transform: translateY(-6px);
        border-color: rgba(240, 180, 41, .55);
        box-shadow: 0 28px 46px -26px rgba(0, 33, 71, .5);
    }

    .acc-hall__media {
        position: relative;
        aspect-ratio: 4 / 3;
        overflow: hidden;
        background: var(--acc-navy);
    }

    .acc-hall__media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .7s var(--acc-ease);
    }

    .acc-hall:hover .acc-hall__media img {
        transform: scale(1.07);
    }

    .acc-hall__media::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0, 21, 46, 0) 45%, rgba(0, 21, 46, .72) 100%);
    }

    .acc-tag {
        position: absolute;
        z-index: 2;
        top: 14px;
        left: 14px;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 13px;
        border-radius: 999px;
        background: rgba(0, 21, 46, .72);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        color: #fff;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .acc-tag .material-symbols-outlined {
        font-size: 15px;
        color: var(--acc-gold);
    }

    .acc-hall__body {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        padding: 22px 22px 24px;
    }

    .acc-hall__body h4 {
        margin: 0;
        font-family: 'Cinzel', Georgia, serif;
        font-size: 19px !important;
        line-height: 1.3;
        color: var(--acc-navy) !important;
    }

    .acc-hall__body p {
        margin: 10px 0 0;
        font-size: 14.5px;
        line-height: 1.65;
        color: var(--acc-muted);
    }

    .acc-hall__foot {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px solid var(--acc-hair);
        font-size: 12.5px;
        font-weight: 600;
        color: var(--acc-muted);
    }

    .acc-hall__foot .material-symbols-outlined {
        font-size: 17px;
        color: var(--acc-gold);
    }

    /* ----------------------------------------------------------- essentials */
    .acc-essentials {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(255px, 1fr));
        gap: 22px;
        margin-top: 44px;
    }

    .acc-card {
        position: relative;
        padding: 30px 26px 28px;
        background: #fff;
        border: 1px solid var(--acc-hair);
        border-radius: 18px;
        overflow: hidden;
        transition: transform .28s var(--acc-ease), box-shadow .28s var(--acc-ease),
            border-color .28s var(--acc-ease);
    }

    .acc-card::before {
        content: "";
        position: absolute;
        inset: 0 0 auto 0;
        height: 3px;
        background: linear-gradient(90deg, var(--acc-gold), rgba(240, 180, 41, 0));
        transform: scaleX(0);
        transform-origin: left;
        transition: transform .38s var(--acc-ease);
    }

    .acc-card:hover {
        transform: translateY(-5px);
        border-color: rgba(240, 180, 41, .5);
        box-shadow: 0 26px 44px -28px rgba(0, 33, 71, .45);
    }

    .acc-card:hover::before {
        transform: scaleX(1);
    }

    .acc-card__icon {
        display: grid;
        place-items: center;
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: rgba(0, 33, 71, .06);
        color: var(--acc-navy);
        margin-bottom: 18px;
    }

    .acc-card__icon .material-symbols-outlined {
        font-size: 26px;
    }

    .acc-card small {
        display: block;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .16em;
        text-transform: uppercase;
        color: var(--acc-gold);
    }

    .acc-card h3 {
        margin: 7px 0 0;
        font-family: 'Cinzel', Georgia, serif;
        font-size: 19px !important;
        color: var(--acc-navy) !important;
    }

    .acc-card p {
        margin: 11px 0 0;
        font-size: 14.5px;
        line-height: 1.7;
        color: var(--acc-muted);
    }

    /* ------------------------------------------------------------ amenities */
    .acc-amenities {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(215px, 1fr));
        gap: 20px;
        margin-top: 44px;
    }

    .acc-amenity {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 24px 22px;
        background: #fff;
        border: 1px solid var(--acc-hair);
        border-radius: 16px;
        transition: border-color .25s var(--acc-ease), box-shadow .25s var(--acc-ease);
    }

    .acc-amenity:hover {
        border-color: rgba(240, 180, 41, .55);
        box-shadow: 0 20px 36px -26px rgba(0, 33, 71, .45);
    }

    .acc-amenity__icon {
        display: grid;
        place-items: center;
        flex: 0 0 auto;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: rgba(240, 180, 41, .16);
        color: #8a6410;
    }

    .acc-amenity__icon .material-symbols-outlined {
        font-size: 22px;
    }

    .acc-amenity h3 {
        margin: 0;
        font-size: 15px !important;
        font-family: 'Open Sans', sans-serif !important;
        font-weight: 700 !important;
        color: var(--acc-navy) !important;
    }

    .acc-amenity p {
        margin: 6px 0 0;
        font-size: 13.5px;
        line-height: 1.6;
        color: var(--acc-muted);
    }

    /* ----------------------------------------------------------- off campus */
    .acc-offcampus {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 26px;
        align-items: start;
        padding: 38px;
        background: linear-gradient(135deg, #fff, var(--acc-paper));
        border: 1px solid var(--acc-hair);
        border-left: 4px solid var(--acc-gold);
        border-radius: 18px;
    }

    .acc-offcampus__icon {
        display: grid;
        place-items: center;
        width: 62px;
        height: 62px;
        border-radius: 16px;
        background: var(--acc-navy);
        color: var(--acc-gold);
    }

    .acc-offcampus__icon .material-symbols-outlined {
        font-size: 30px;
    }

    .acc-offcampus h3 {
        margin: 0;
        font-family: 'Cinzel', Georgia, serif;
        font-size: clamp(20px, 2.2vw, 26px) !important;
        color: var(--acc-navy) !important;
    }

    .acc-offcampus p {
        margin: 12px 0 0;
        font-size: 15px;
        line-height: 1.78;
        color: var(--acc-muted);
    }

    /* -------------------------------------------------------------- related */
    .acc-related {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 20px;
        margin-top: 44px;
    }

    .acc-related a {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 26px 24px;
        background: #fff;
        border: 1px solid var(--acc-hair);
        border-radius: 16px;
        text-decoration: none;
        transition: transform .25s var(--acc-ease), border-color .25s var(--acc-ease),
            box-shadow .25s var(--acc-ease);
    }

    .acc-related a:hover {
        transform: translateY(-4px);
        border-color: rgba(240, 180, 41, .55);
        box-shadow: 0 22px 38px -26px rgba(0, 33, 71, .45);
        text-decoration: none;
    }

    .acc-related .material-symbols-outlined {
        font-size: 26px;
        color: var(--acc-gold);
    }

    .acc-related strong {
        display: flex;
        align-items: center;
        gap: 8px;
        font-family: 'Cinzel', Georgia, serif;
        font-size: 16px;
        font-weight: 600;
        color: var(--acc-navy);
    }

    .acc-related strong .material-symbols-outlined {
        font-size: 16px;
        color: var(--acc-muted);
        transition: transform .25s var(--acc-ease);
    }

    .acc-related a:hover strong .material-symbols-outlined {
        transform: translateX(4px);
        color: var(--acc-gold);
    }

    .acc-related span.txt {
        font-size: 13.5px;
        line-height: 1.6;
        color: var(--acc-muted);
    }

    /* ------------------------------------------------------------------ cta */
    .acc-cta {
        position: relative;
        padding: 78px 0;
        overflow: hidden;
        background:
            linear-gradient(115deg, var(--acc-navy-deep) 0%, var(--acc-navy) 52%, #0a3161 100%);
    }

    .acc-cta::before {
        content: "";
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='56' height='56' viewBox='0 0 56 56'%3E%3Cg fill='none' stroke='%23ffffff' stroke-opacity='.06' stroke-width='1'%3E%3Cpath d='M28 0 56 28 28 56 0 28z'/%3E%3Cpath d='M28 18 38 28 28 38 18 28z'/%3E%3C/g%3E%3C/svg%3E");
    }

    .acc-cta__inner {
        position: relative;
        z-index: 1;
        text-align: center;
    }

    .acc-cta h2 {
        margin: 18px 0 0;
        font-family: 'Cinzel', Georgia, serif;
        font-size: clamp(27px, 3.8vw, 46px) !important;
        line-height: 1.16;
        color: #fff !important;
    }

    .acc-cta p {
        margin: 16px auto 0;
        max-width: 60ch;
        font-size: clamp(15px, 1.3vw, 17px);
        line-height: 1.75;
        color: rgba(255, 255, 255, .82);
    }

    .acc-cta__btns {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 14px;
        margin-top: 32px;
    }

    .acc-cta .acc-eyebrow {
        background: rgba(240, 180, 41, .18);
        color: var(--acc-gold);
    }

    /* -------------------------------------------------------------- responsive */
    @media (max-width: 900px) {
        .acc-intro {
            grid-template-columns: 1fr;
            gap: 32px;
        }

        .acc-stats__grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .acc-offcampus {
            grid-template-columns: 1fr;
            padding: 28px;
        }
    }

    @media (max-width: 600px) {
        .acc-section {
            padding: 54px 0;
        }

        .acc-stats {
            padding-top: 34px;
        }

        .acc-stat {
            padding: 20px 10px;
        }

        .acc-halls {
            grid-template-columns: 1fr;
        }

        .acc-btn {
            width: 100%;
            justify-content: center;
        }

        .acc-hero__cta {
            flex-direction: column;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .acc * {
            transition-duration: .01ms !important;
        }
    }
</style>

<main class="acc">

    <!-- Hero Section (Core Values Design) -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($content['hero_image']); ?>"
                 alt="VVU Accommodation" class="w-full h-full object-cover animate-slow-zoom opacity-50">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>

        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400">Student Life</span>
                </div>

                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($content['hero_title']); ?>
                </h1>

                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($content['hero_subtitle']); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- ============================= AT A GLANCE ============================= -->
    <div class="acc-stats">
        <div class="acc-shell">
            <div class="acc-stats__grid">
                <?php foreach ($stats as $s): ?>
                    <div class="acc-stat">
                        <span class="material-symbols-outlined"><?php echo htmlspecialchars($s['icon']); ?></span>
                        <b><?php echo htmlspecialchars((string) $s['value']); ?></b>
                        <span class="lbl"><?php echo htmlspecialchars($s['label']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- =============================== INTRO =============================== -->
    <section class="acc-section">
        <div class="acc-shell">
            <div class="acc-intro">
                <div class="acc-intro__body">
                    <span class="acc-eyebrow">
                        <span class="material-symbols-outlined">home_work</span> Residential Services
                    </span>
                    <h2 class="acc-h2"><?php echo htmlspecialchars(strip_tags($content['intro_heading'])); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars(strip_tags($content['intro_text']))); ?></p>
                    <div class="acc-hero__cta">
                        <a class="acc-btn acc-btn--outline" href="#essentials">
                            <span class="material-symbols-outlined">info</span> Housing Essentials
                        </a>
                    </div>
                </div>
                <?php
                // Fall back to the first hall photo so the panel is never empty.
                $intro_img = !empty($content['intro_image'])
                    ? $content['intro_image']
                    : ($halls[0]['image'] ?? '');
                ?>
                <?php if ($intro_img): ?>
                    <figure class="acc-intro__figure">
                        <img src="<?php echo htmlspecialchars(vvu_thumb(strip_tags($intro_img), 900, 700)); ?>"
                             alt="Campus housing at Valley View University" loading="lazy" decoding="async">
                    </figure>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- =============================== HALLS =============================== -->
    <section class="acc-section acc-section--tint" id="halls">
        <div class="acc-shell">
            <div class="acc-center">
                <span class="acc-eyebrow">
                    <span class="material-symbols-outlined">apartment</span> Residence Halls
                </span>
                <h2 class="acc-h2">Where You&rsquo;ll Live</h2>
                <span class="acc-rule"><i></i></span>
                <p class="acc-lede">Separate halls of residence for men and women, each supervised by
                    resident staff and within walking distance of lecture halls, the library and the cafeteria.</p>
            </div>

            <?php
            $groups = [
                ['label' => "Women's Halls", 'icon' => 'woman', 'items' => $halls_female],
                ['label' => "Men's Halls",   'icon' => 'man',   'items' => $halls_male],
            ];
            foreach ($groups as $group):
                if (!$group['items']) continue;
            ?>
                <div class="acc-group">
                    <div class="acc-group__head">
                        <h3><?php echo htmlspecialchars($group['label']); ?></h3>
                        <span class="line"></span>
                        <span class="count"><?php echo count($group['items']); ?>
                            <?php echo count($group['items']) === 1 ? 'Hall' : 'Halls'; ?></span>
                    </div>
                    <div class="acc-halls">
                        <?php foreach ($group['items'] as $hall): ?>
                            <article class="acc-hall">
                                <div class="acc-hall__media">
                                    <?php if (!empty($hall['image'])): ?>
                                        <img src="<?php echo htmlspecialchars(vvu_thumb(strip_tags($hall['image']), 640, 480)); ?>"
                                             alt="<?php echo htmlspecialchars(strip_tags($hall['title'])); ?>"
                                             loading="lazy" decoding="async">
                                    <?php endif; ?>
                                    <span class="acc-tag">
                                        <span class="material-symbols-outlined"><?php echo htmlspecialchars(strip_tags($hall['icon'])); ?></span>
                                        <?php echo htmlspecialchars($group['label'] === "Men's Halls" ? "Men's Residence" : "Women's Residence"); ?>
                                    </span>
                                </div>
                                <div class="acc-hall__body">
                                    <h4><?php echo htmlspecialchars(strip_tags($hall['title'])); ?></h4>
                                    <?php if (!empty($hall['description'])): ?>
                                        <p><?php echo htmlspecialchars(strip_tags($hall['description'])); ?></p>
                                    <?php endif; ?>
                                    <div class="acc-hall__foot">
                                        <span class="material-symbols-outlined">location_on</span>
                                        Main Campus, Oyibi
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- ============================ ESSENTIALS ============================ -->
    <?php if ($essentials): ?>
        <section class="acc-section" id="essentials">
            <div class="acc-shell">
                <div class="acc-center">
                    <span class="acc-eyebrow">
                        <span class="material-symbols-outlined">checklist</span> Housing Essentials
                    </span>
                    <h2 class="acc-h2">What You Need to Know</h2>
                    <span class="acc-rule"><i></i></span>
                    <p class="acc-lede">Room options, hall facilities, how to secure a place and the standards
                        every resident agrees to.</p>
                </div>

                <div class="acc-essentials">
                    <?php foreach ($essentials as $item): ?>
                        <div class="acc-card">
                            <div class="acc-card__icon">
                                <span class="material-symbols-outlined"><?php echo htmlspecialchars($item['icon']); ?></span>
                            </div>
                            <small><?php echo htmlspecialchars($item['eyebrow']); ?></small>
                            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars(strip_tags($item['text']))); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ============================= AMENITIES ============================= -->
    <?php if ($features): ?>
        <section class="acc-section acc-section--tint">
            <div class="acc-shell">
                <div class="acc-center">
                    <span class="acc-eyebrow">
                        <span class="material-symbols-outlined">star</span> Amenities
                    </span>
                    <h2 class="acc-h2">Facilities &amp; Amenities</h2>
                    <span class="acc-rule"><i></i></span>
                    <p class="acc-lede">Everything you need for a comfortable, secure and study-friendly
                        stay on campus.</p>
                </div>

                <div class="acc-amenities">
                    <?php foreach ($features as $feature): ?>
                        <div class="acc-amenity">
                            <div class="acc-amenity__icon">
                                <span class="material-symbols-outlined"><?php echo htmlspecialchars(strip_tags($feature['icon'])); ?></span>
                            </div>
                            <div>
                                <h3><?php echo htmlspecialchars(strip_tags($feature['title'])); ?></h3>
                                <?php if (!empty($feature['description'])): ?>
                                    <p><?php echo htmlspecialchars(strip_tags($feature['description'])); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ============================ OFF CAMPUS ============================ -->
    <?php if (!empty($content['off_campus_text'])): ?>
        <section class="acc-section">
            <div class="acc-shell">
                <div class="acc-offcampus">
                    <div class="acc-offcampus__icon">
                        <span class="material-symbols-outlined">holiday_village</span>
                    </div>
                    <div>
                        <h3><?php echo htmlspecialchars(strip_tags($content['off_campus_heading'])); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars(strip_tags($content['off_campus_text']))); ?></p>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ============================== RELATED ============================== -->
    <?php if ($related): ?>
        <section class="acc-section acc-section--tint">
            <div class="acc-shell">
                <div class="acc-center">
                    <span class="acc-eyebrow">
                        <span class="material-symbols-outlined">explore</span> Explore Further
                    </span>
                    <h2 class="acc-h2">Life Around Your Hall</h2>
                    <span class="acc-rule"><i></i></span>
                </div>
                <div class="acc-related">
                    <?php foreach ($related as $r): ?>
                        <a href="<?php echo htmlspecialchars($r['href']); ?>">
                            <span class="material-symbols-outlined"><?php echo htmlspecialchars($r['icon']); ?></span>
                            <strong><?php echo htmlspecialchars($r['title']); ?>
                                <span class="material-symbols-outlined">arrow_forward</span></strong>
                            <span class="txt"><?php echo htmlspecialchars($r['text']); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ================================ CTA ================================ -->
    <section class="acc-cta">
        <div class="acc-shell acc-cta__inner">
            <span class="acc-eyebrow">
                <span class="material-symbols-outlined">bed</span> Housing
            </span>
            <h2><?php echo htmlspecialchars(strip_tags($content['cta_heading'])); ?></h2>
            <p><?php echo htmlspecialchars(strip_tags($content['cta_text'])); ?></p>
            <div class="acc-cta__btns">
                <a class="acc-btn acc-btn--gold" href="https://admissions.vvu.edu.gh/">
                    <span class="material-symbols-outlined">edit_document</span> Apply for Housing
                </a>
                <a class="acc-btn acc-btn--ghost" href="contact_us.php">
                    <span class="material-symbols-outlined">contact_support</span> Contact Housing Office
                </a>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
