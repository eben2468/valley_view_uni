<?php
require_once __DIR__ . '/security_headers.php';
require_once 'db_connect.php';
require_once 'navigation_helper.php';
require_once __DIR__ . '/image_helper.php';

/* --------------------------------------------------------------------------
   MASTHEAD DATA
   Everything below the <body> tag is driven by the navigation_* tables, so the
   admin panel remains the single source of truth for links and mega menus.
   -------------------------------------------------------------------------- */

$topbar_settings  = getTopbarSettings($pdo);
$main_nav         = getNavItems($pdo, 'main');
$topbar_nav       = getNavItems($pdo, 'topbar');
$quick_access_nav = getNavItems($pdo, 'quickaccess');

// Identity: header_settings wins, topbar_settings is the fallback.
$header_settings = [];
try {
    $header_settings = $pdo->query("SELECT setting_key, setting_value FROM header_settings")
        ->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Exception $e) {
    $header_settings = [];
}

// Social accounts are already maintained for the footer — reuse them.
$vvu_social = [];
try {
    $stmt = $pdo->query(
        "SELECT fl.label, fl.url, fl.icon_class
           FROM footer_links fl
           JOIN footer_sections fs ON fs.id = fl.section_id
          WHERE fl.is_active = 1 AND fs.is_active = 1 AND fs.title LIKE '%Social%'
          ORDER BY fl.display_order ASC"
    );
    $vvu_social = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $vvu_social = [];
}

if (!function_exists('vvu_url')) {
    /**
     * Normalises a stored link. Editors sometimes save bare hosts such as
     * "www.youtube.com/@vvu", which browsers would otherwise resolve as a
     * relative path inside the site.
     */
    function vvu_url($url)
    {
        $url = trim((string) $url);
        if ($url === '') {
            return '#';
        }
        if (preg_match('#^(https?:)?//#i', $url)) {
            return $url;
        }
        if (preg_match('#^(mailto:|tel:|/|\#|javascript:)#i', $url)) {
            return $url;
        }
        if (preg_match('#^www\.#i', $url)) {
            return 'https://' . $url;
        }
        return $url;
    }
}

if (!function_exists('vvu_e')) {
    function vvu_e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

$vvu_logo  = $header_settings['logo_path'] ?? 'vvu_logo.jpg';
$vvu_name  = $header_settings['logo_text'] ?? 'Valley View University';
$vvu_motto = 'Excellence • Integrity • Service';
$vvu_since = '1979';

$vvu_phone = $header_settings['contact_phone']
    ?? ($topbar_settings['contact_phone'] ?? '+233 307 051 149');
$vvu_tel = $header_settings['contact_phone_link']
    ?? ('tel:' . preg_replace('/[^0-9+]/', '', $vvu_phone));

// The stored address carries a "Contact:" label that belongs to the old bar.
$vvu_address = $topbar_settings['contact_address'] ?? 'Valley View University, Oyibi, Accra';
$vvu_address = trim(preg_replace('/^\s*contact\s*:\s*/i', '', $vvu_address));

$vvu_apply_url  = $header_settings['apply_url'] ?? 'apply.php';
$vvu_search_url = 'search.php';

// The quick-access menu contains historical duplicates — collapse them.
$vvu_quick_links = [];
$vvu_seen_quick  = [];
foreach ($quick_access_nav as $vvu_item) {
    $vvu_key = strtolower(trim($vvu_item['title'])) . '|' . strtolower(trim($vvu_item['url']));
    if (isset($vvu_seen_quick[$vvu_key])) {
        continue;
    }
    $vvu_seen_quick[$vvu_key] = true;
    $vvu_quick_links[] = $vvu_item;
}

// Shortcuts offered inside the search panel.
$vvu_search_hints = [
    'Admissions'         => 'admissions.php',
    'Fees Structure'     => 'fees-structure.php',
    'Programmes'         => 'academic_programs_overview.php',
    'Academic Calendar'  => 'academic_calendar.php',
    'Scholarships'       => 'scholarships.php',
    'Contact Us'         => 'contact_us.php',
];

/**
 * Splits a mega-menu item's sections into the three shapes the panel renders:
 * a featured image, a short blurb with an optional button, and link groups.
 */
if (!function_exists('vvu_split_sections')) {
    function vvu_split_sections(array $sections)
    {
        $out = ['feature' => null, 'blurb' => null, 'groups' => []];
        foreach ($sections as $section) {
            switch ($section['section_type']) {
                case 'featured':
                    if (!empty($section['featured_image'])) {
                        $out['feature'] = $section;
                    }
                    break;
                case 'description':
                    $out['blurb'] = $section;
                    break;
                case 'links':
                    if (!empty($section['links'])) {
                        $out['groups'][] = $section;
                    }
                    break;
            }
        }
        return $out;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?php echo isset($page_title) ? vvu_e($page_title) : 'Valley View University'; ?></title>

    <!-- FAVICON (VVU mark, cropped tight so it fills the tab tile) -->
    <link rel="icon" href="favicon.ico" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16.png">
    <link rel="icon" type="image/png" sizes="192x192" href="favicon-192.png">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <meta name="theme-color" content="#002147">

    <!-- GOOGLE FONT — Cinzel for titles, Open Sans for body -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800;900&family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <!-- FONTAWESOME ICONS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- MATERIAL SYMBOLS -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <!-- TAILWIND CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- ALL CSS FILES -->
    <link href="Education-Website-and-AdminPanel/css/materialize.css" rel="stylesheet">
    <link href="Education-Website-and-AdminPanel/css/bootstrap.css" rel="stylesheet" />
    <link href="Education-Website-and-AdminPanel/css/style.css" rel="stylesheet" />
    <!-- RESPONSIVE.CSS ONLY FOR MOBILE AND TABLET VIEWS -->
    <link href="Education-Website-and-AdminPanel/css/style-mob.css" rel="stylesheet" />
    <!-- CUSTOM FIXES -->
    <link href="css/custom-fixes.css" rel="stylesheet" />
    <!-- MASTHEAD & NAVIGATION (loads last so it wins over the legacy theme) -->
    <link href="css/vvu-header.css" rel="stylesheet" />
    <script src="js/vvu-header.js" defer></script>
</head>

<body>

    <a class="vvu-skip" href="#main-content">Skip to main content</a>

    <header class="vvu-header" role="banner">

        <!-- ============================ TIER 1 — UTILITY STRIP ============================ -->
        <div class="vvu-utility">
            <div class="vvu-utility__inner">
                <div class="vvu-utility__main">

                    <nav class="vvu-utility__links" aria-label="Student and staff services">
                        <ul>
                            <?php foreach ($topbar_nav as $item): ?>
                                <li>
                                    <a href="<?php echo vvu_e(vvu_url($item['url'])); ?>"
                                        target="<?php echo vvu_e($item['target'] ?? '_self'); ?>"
                                        rel="noopener"><?php echo vvu_e($item['title']); ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </nav>

                    <div class="vvu-utility__meta">
                        <?php if ($vvu_social): ?>
                            <ul class="vvu-social">
                                <?php foreach (array_slice($vvu_social, 0, 5) as $social): ?>
                                    <li>
                                        <a href="<?php echo vvu_e(vvu_url($social['url'])); ?>"
                                            target="_blank" rel="noopener noreferrer"
                                            aria-label="<?php echo vvu_e($social['label']); ?>"
                                            title="<?php echo vvu_e($social['label']); ?>">
                                            <i class="fa-brands <?php echo vvu_e($social['icon_class']); ?>" aria-hidden="true"></i>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <div class="vvu-utility__contact">
                            <a href="<?php echo vvu_e($vvu_tel); ?>">
                                <i class="fa-solid fa-phone" aria-hidden="true"></i><?php echo vvu_e($vvu_phone); ?>
                            </a>
                            <span>
                                <i class="fa-solid fa-location-dot" aria-hidden="true"></i><?php echo vvu_e($vvu_address); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="vvu-utility__rail">
                    <button type="button" class="vvu-rail-btn" data-vvu-toggle="search"
                        aria-expanded="false" aria-controls="vvu-search">
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                        <span>Search</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ============================= TIER 2 — BRAND BAND ============================= -->
        <div class="vvu-brand">
            <div class="vvu-brand__inner">

                <div class="vvu-brand__aside vvu-brand__aside--left">
                    <span class="vvu-estab">
                        <strong>Est. <?php echo vvu_e($vvu_since); ?></strong>
                        <em>Chartered &bull; Accra, Ghana</em>
                    </span>
                </div>

                <a class="vvu-lockup" href="index.php">
                    <span class="vvu-lockup__crest">
                        <img src="<?php echo vvu_e($vvu_logo); ?>" alt="<?php echo vvu_e($vvu_name); ?> crest">
                    </span>
                    <span class="vvu-lockup__text">
                        <span class="vvu-lockup__name"><?php echo vvu_e($vvu_name); ?></span>
                        <span class="vvu-rule" aria-hidden="true"><i></i></span>
                        <span class="vvu-lockup__motto"><?php echo vvu_e($vvu_motto); ?></span>
                    </span>
                </a>

                <div class="vvu-brand__aside vvu-brand__aside--right">
                    <a class="vvu-cta" href="<?php echo vvu_e(vvu_url($vvu_apply_url)); ?>">
                        <span>Apply Now</span>
                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- ========================== TIER 3 — MAIN NAVIGATION =========================== -->
        <div class="vvu-nav">
            <div class="vvu-nav__inner">

                <a class="vvu-nav__mini" href="index.php" tabindex="-1" aria-hidden="true">
                    <img src="<?php echo vvu_e($vvu_logo); ?>" alt="">
                    <span><?php echo vvu_e($vvu_name); ?></span>
                </a>

                <nav class="vvu-nav__menu" aria-label="Main navigation">
                    <ul>
                        <?php foreach ($main_nav as $item): ?>
                            <?php
                            $has_mega = !empty($item['has_megamenu']) && !empty($item['sections']);
                            $is_active = isset($active_page) && !empty($item['active_key'])
                                && $active_page === $item['active_key'];
                            $parts = $has_mega ? vvu_split_sections($item['sections']) : null;
                            ?>
                            <li class="vvu-nav__item<?php echo $has_mega ? ' vvu-nav__item--mega' : ''; ?>">
                                <a class="vvu-nav__link<?php echo $is_active ? ' is-active' : ''; ?>"
                                    href="<?php echo vvu_e(vvu_url($item['url'])); ?>"
                                    <?php if ($has_mega): ?>aria-haspopup="true" aria-expanded="false" <?php endif; ?>
                                    <?php if ($is_active): ?>aria-current="page" <?php endif; ?>>
                                    <?php echo vvu_e($item['title']); ?>
                                    <?php if ($has_mega): ?>
                                        <i class="fa-solid fa-chevron-down vvu-caret" aria-hidden="true"></i>
                                    <?php endif; ?>
                                </a>

                                <?php if ($has_mega): ?>
                                    <div class="vvu-mega">
                                        <div class="vvu-mega__inner">

                                            <div class="vvu-mega__feature">
                                                <?php
                                                $feature = $parts['feature'];
                                                $blurb   = $parts['blurb'];
                                                $feature_href = '';
                                                if ($feature && !empty($feature['featured_link'])) {
                                                    $feature_href = $feature['featured_link'];
                                                } elseif ($blurb && !empty($blurb['button_link'])) {
                                                    $feature_href = $blurb['button_link'];
                                                }
                                                ?>
                                                <?php if ($feature): ?>
                                                    <a class="vvu-feature-card"
                                                        href="<?php echo vvu_e($feature_href !== '' ? vvu_url($feature_href) : '#'); ?>">
                                                        <?php // Originals here run 2-16 MB; the card is 320x172. ?>
                                                        <img src="<?php echo vvu_e(vvu_thumb($feature['featured_image'], 640, 344)); ?>"
                                                            alt="" width="640" height="344" loading="lazy" decoding="async">
                                                        <?php if (!empty($feature['featured_text'])): ?>
                                                            <span class="vvu-feature-card__label"><?php echo vvu_e($feature['featured_text']); ?></span>
                                                        <?php endif; ?>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if ($blurb && !empty($blurb['description_text'])): ?>
                                                    <p class="vvu-mega__blurb"><?php echo vvu_e($blurb['description_text']); ?></p>
                                                <?php endif; ?>

                                                <?php if ($blurb && !empty($blurb['button_link'])): ?>
                                                    <a class="vvu-textlink" href="<?php echo vvu_e(vvu_url($blurb['button_link'])); ?>">
                                                        <span><?php echo vvu_e($blurb['button_text'] ?: 'Learn more'); ?></span>
                                                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>

                                            <div class="vvu-mega__groups">
                                                <?php foreach ($parts['groups'] as $group): ?>
                                                    <div class="vvu-mega__group">
                                                        <?php if (!empty($group['section_title'])): ?>
                                                            <h3><?php echo vvu_e($group['section_title']); ?></h3>
                                                        <?php endif; ?>
                                                        <ul>
                                                            <?php foreach ($group['links'] as $link): ?>
                                                                <li>
                                                                    <a href="<?php echo vvu_e(vvu_url($link['url'])); ?>"><?php echo vvu_e($link['title']); ?></a>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>

                <div class="vvu-nav__tools">
                    <button type="button" class="vvu-icon-btn" data-vvu-toggle="search"
                        aria-expanded="false" aria-controls="vvu-search" aria-label="Search this site">
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    </button>
                    <a class="vvu-cta vvu-cta--sm" href="<?php echo vvu_e(vvu_url($vvu_apply_url)); ?>">
                        <span>Apply</span>
                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <!-- Search overlay, anchored under the nav band -->
            <div class="vvu-search" id="vvu-search">
                <button type="button" class="vvu-search__close" data-vvu-toggle="search-close" aria-label="Close search">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
                <div class="vvu-search__inner">
                    <form class="vvu-search-form" action="<?php echo vvu_e($vvu_search_url); ?>" method="get" role="search">
                        <div class="vvu-search__field">
                            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                            <input class="vvu-search__input browser-default" type="search" name="q"
                                placeholder="Search programmes, news, admissions&hellip;"
                                aria-label="Search Valley View University" autocomplete="off">
                            <button type="submit" class="vvu-search__submit">Search</button>
                        </div>
                    </form>
                    <div class="vvu-search__hints">
                        <span>Popular</span>
                        <?php foreach ($vvu_search_hints as $label => $href): ?>
                            <a href="<?php echo vvu_e($href); ?>"><?php echo vvu_e($label); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================ MOBILE BAR (< 1024px) ============================ -->
        <div class="vvu-mobilebar">
            <div class="vvu-mobilebar__inner">
                <button type="button" class="vvu-burger" data-vvu-toggle="drawer"
                    aria-expanded="false" aria-controls="vvu-drawer" aria-label="Open menu">
                    <span></span><span></span><span></span>
                </button>

                <a class="vvu-mobilebar__brand" href="index.php">
                    <img src="<?php echo vvu_e($vvu_logo); ?>" alt="">
                    <span><?php echo vvu_e($vvu_name); ?></span>
                </a>

                <div class="vvu-mobilebar__actions">
                    <button type="button" class="vvu-icon-btn" data-vvu-toggle="drawer-search" aria-label="Search this site">
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- ================================ MOBILE DRAWER ================================ -->
    <div class="vvu-scrim"></div>

    <aside class="vvu-drawer" id="vvu-drawer" aria-hidden="true" aria-label="Site menu">
        <div class="vvu-drawer__head">
            <a class="vvu-drawer__lockup" href="index.php">
                <img src="<?php echo vvu_e($vvu_logo); ?>" alt="">
                <span>
                    <strong><?php echo vvu_e($vvu_name); ?></strong>
                    <em><?php echo vvu_e($vvu_motto); ?></em>
                </span>
            </a>
            <button type="button" class="vvu-drawer__close" data-vvu-toggle="drawer-close" aria-label="Close menu">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </div>

        <div class="vvu-drawer__body">

            <div class="vvu-drawer__search">
                <form class="vvu-search-form" action="<?php echo vvu_e($vvu_search_url); ?>" method="get" role="search">
                    <div class="vvu-search__field">
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                        <input class="vvu-search__input browser-default" type="search" name="q"
                            placeholder="Search the site&hellip;" aria-label="Search Valley View University"
                            autocomplete="off">
                        <button type="submit" class="vvu-search__submit">Go</button>
                    </div>
                </form>
            </div>

            <ul class="vvu-acc">
                <?php foreach ($main_nav as $index => $item): ?>
                    <?php
                    $has_mega = !empty($item['has_megamenu']) && !empty($item['sections']);
                    $parts    = $has_mega ? vvu_split_sections($item['sections']) : null;
                    $has_body = $has_mega && (!empty($parts['groups']) || !empty($parts['blurb']['button_link']));
                    ?>
                    <li>
                        <div class="vvu-acc__row">
                            <?php if ($has_body): ?>
                                <button type="button" data-vvu-acc aria-expanded="false"
                                    aria-controls="vvu-acc-<?php echo (int) $index; ?>">
                                    <span><?php echo vvu_e($item['title']); ?></span>
                                    <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                                </button>
                            <?php else: ?>
                                <a href="<?php echo vvu_e(vvu_url($item['url'])); ?>">
                                    <span><?php echo vvu_e($item['title']); ?></span>
                                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                </a>
                            <?php endif; ?>
                        </div>

                        <?php if ($has_body): ?>
                            <div class="vvu-acc__panel" id="vvu-acc-<?php echo (int) $index; ?>">
                                <div>
                                    <div class="vvu-acc__panel-inner">
                                        <?php foreach ($parts['groups'] as $group): ?>
                                            <div class="vvu-acc__group">
                                                <?php if (!empty($group['section_title'])): ?>
                                                    <h4><?php echo vvu_e($group['section_title']); ?></h4>
                                                <?php endif; ?>
                                                <?php foreach ($group['links'] as $link): ?>
                                                    <a href="<?php echo vvu_e(vvu_url($link['url'])); ?>"><?php echo vvu_e($link['title']); ?></a>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endforeach; ?>

                                        <?php if (!empty($parts['blurb']['button_link'])): ?>
                                            <div class="vvu-acc__group">
                                                <a href="<?php echo vvu_e(vvu_url($parts['blurb']['button_link'])); ?>">
                                                    <?php echo vvu_e($parts['blurb']['button_text'] ?: 'Learn more'); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if ($vvu_quick_links): ?>
                <div class="vvu-drawer__section">
                    <p class="vvu-panel-title">Quick Links</p>
                    <div class="vvu-chips">
                        <?php foreach ($vvu_quick_links as $item): ?>
                            <a href="<?php echo vvu_e(vvu_url($item['url'])); ?>"
                                target="<?php echo vvu_e($item['target'] ?? '_self'); ?>"
                                rel="noopener"><?php echo vvu_e($item['title']); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($topbar_nav): ?>
                <div class="vvu-drawer__section">
                    <p class="vvu-panel-title">Services</p>
                    <div class="vvu-chips">
                        <?php foreach ($topbar_nav as $item): ?>
                            <a href="<?php echo vvu_e(vvu_url($item['url'])); ?>"
                                target="<?php echo vvu_e($item['target'] ?? '_self'); ?>"
                                rel="noopener"><?php echo vvu_e($item['title']); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="vvu-drawer__cta">
                <a class="vvu-cta" href="<?php echo vvu_e(vvu_url($vvu_apply_url)); ?>">
                    <span>Apply Now</span>
                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>

        <div class="vvu-drawer__foot">
            <?php if ($vvu_social): ?>
                <ul class="vvu-social">
                    <?php foreach ($vvu_social as $social): ?>
                        <li>
                            <a href="<?php echo vvu_e(vvu_url($social['url'])); ?>"
                                target="_blank" rel="noopener noreferrer"
                                aria-label="<?php echo vvu_e($social['label']); ?>">
                                <i class="fa-brands <?php echo vvu_e($social['icon_class']); ?>" aria-hidden="true"></i>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <div class="vvu-drawer__contact">
                <a href="<?php echo vvu_e($vvu_tel); ?>">
                    <i class="fa-solid fa-phone" aria-hidden="true"></i><?php echo vvu_e($vvu_phone); ?>
                </a>
                <span>
                    <i class="fa-solid fa-location-dot" aria-hidden="true"></i><?php echo vvu_e($vvu_address); ?>
                </span>
            </div>
        </div>
    </aside>

    <span id="main-content" tabindex="-1"></span>
