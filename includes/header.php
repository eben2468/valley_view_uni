<?php
require_once 'db_connect.php';
require_once 'navigation_helper.php';

// Fetch all navigation data
$topbar_settings = getTopbarSettings($pdo);
$main_nav = getNavItems($pdo, 'main');
$topbar_nav = getNavItems($pdo, 'topbar');
$quick_access_nav = getNavItems($pdo, 'quickaccess');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?php echo isset($page_title) ? $page_title : "Valley View University"; ?></title>

    <!-- FAVICON (VVU mark, cropped tight so it fills the tab tile) -->
    <link rel="icon" href="favicon.ico" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16.png">
    <link rel="icon" type="image/png" sizes="192x192" href="favicon-192.png">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <meta name="theme-color" content="#002147">

    <!-- GOOGLE FONT -->
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=Inter:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
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
    <!-- STICKY NAVBAR -->
    <link href="css/sticky-navbar.css" rel="stylesheet" />
</head>
<body>

    <!-- MOBILE MENU -->
    <section>
        <div class="ed-mob-menu">
            <div class="ed-mob-menu-con">
                <div class="ed-mm-left">
                    <div class="wed-logo">
                        <a href="index.php" style="display: flex; align-items: center; text-decoration: none;">
                            <img src="vvu_logo.jpg" alt="Valley View University" />
                            <span class="logo-text">Valley View University</span>
                        </a>
                    </div>
                </div>
                <div class="ed-mm-right">
                    <div class="ed-mm-menu">
                        <a href="#!" class="ed-micon"><i class="fa fa-bars"></i></a>
                        <div class="ed-mm-inn">
                            <a href="#!" class="ed-mi-close"><i class="fa fa-times"></i></a>
                            <h4>Main Menu</h4>
                            <ul class="mm-main-list">
                                <?php foreach ($main_nav as $item): ?>
                                    <?php if (!$item['has_megamenu']): ?>
                                        <li><a href="<?php echo htmlspecialchars($item['url']); ?>"><?php echo htmlspecialchars($item['title']); ?></a></li>
                                    <?php else: ?>
                                        <li class="has-sub">
                                            <a href="javascript:void(0)"><?php echo htmlspecialchars($item['title']); ?> <i class="fa fa-angle-down"></i></a>
                                            <ul class="mm-sub">
                                                <?php 
                                                if (isset($item['sections'])) {
                                                    foreach ($item['sections'] as $section) {
                                                        if ($section['section_type'] === 'links' && isset($section['links'])) {
                                                            foreach ($section['links'] as $link) {
                                                                echo '<li><a href="'.htmlspecialchars($link['url']).'">'.htmlspecialchars($link['title']).'</a></li>';
                                                            }
                                                        } elseif ($section['section_type'] === 'description' && $section['button_link']) {
                                                            echo '<li><a href="'.htmlspecialchars($section['button_link']).'">'.htmlspecialchars($section['button_text'] ?: $section['section_title']).'</a></li>';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                            <h4>Quick Access</h4>
                            <ul>
                                <?php foreach ($quick_access_nav as $item): ?>
                                    <li><a href="<?php echo htmlspecialchars($item['url']); ?>"><?php echo htmlspecialchars($item['title']); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--HEADER SECTION-->
    <section id="main-header">
        <!-- TOP BAR -->
        <div class="ed-top">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="ed-com-t1-left">
                            <ul>
                                <li><a href="#"><?php echo htmlspecialchars($topbar_settings['contact_address'] ?? 'Contact: Valley View University, Oyibi, Accra'); ?></a></li>
                                <li><a href="tel:<?php echo str_replace(' ', '', $topbar_settings['contact_phone'] ?? '+233 307 051149'); ?>">Phone: <?php echo htmlspecialchars($topbar_settings['contact_phone'] ?? '+233 307 051 149'); ?></a></li>
                            </ul>
                        </div>
                        <div class="ed-com-t1-right">
                            <ul>
                                <?php foreach ($topbar_nav as $item): ?>
                                    <li><a href="<?php echo htmlspecialchars($item['url']); ?>"><?php echo htmlspecialchars($item['title']); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LOGO AND MENU SECTION -->
        <div class="top-logo">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="wed-logo">
                            <a href="index.php" style="display: flex; align-items: center; text-decoration: none;">
                                <img src="vvu_logo.jpg" alt="Valley View University" style="max-height: 55px;" />
                                <span class="logo-text">Valley View University</span>
                            </a>
                        </div>
                        <div class="main-menu">
                            <ul>
                                <?php foreach ($main_nav as $item): ?>
                                    <?php 
                                        $active_class = (isset($active_page) && !empty($item['active_key']) && $active_page == $item['active_key']) ? 'active' : '';
                                        $has_mm = $item['has_megamenu'];
                                    ?>
                                    <li class="<?php echo htmlspecialchars($item['menu_class'] ?? ''); ?>">
                                        <a href="<?php echo htmlspecialchars($item['url']); ?>" 
                                           class="<?php echo ($has_mm ? 'mm-arr ' : '') . $active_class; ?>">
                                            <?php echo htmlspecialchars($item['title']); ?>
                                        </a>
                                        <?php if ($has_mm && !empty($item['sections'])): ?>
                                            <div class="mm-pos">
                                                <div class="<?php echo htmlspecialchars($item['megamenu_type'] ?? 'about-mm'); ?> m-menu">
                                                    <div class="m-menu-inn">
                                                        <?php 
                                                        $cols = [1 => [], 2 => [], 3 => [], 4 => []];
                                                        foreach ($item['sections'] as $section) {
                                                            $cols[$section['column_position']][] = $section;
                                                        }
                                                        ?>
                                                        
                                                        <div class="mm1-com mm1-s1">
                                                            <?php foreach ($cols[1] as $sec): ?>
                                                                <div class="ed-course-in">
                                                                    <a class="course-overlay menu-about" href="<?php echo htmlspecialchars($sec['featured_link'] ?? ''); ?>">
                                                                        <img src="<?php echo htmlspecialchars($sec['featured_image'] ?? ''); ?>" alt="">
                                                                        <span><?php echo htmlspecialchars($sec['featured_text'] ?? ''); ?></span>
                                                                    </a>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>

                                                        <div class="mm1-com mm1-s2">
                                                            <?php foreach ($cols[2] as $sec): ?>
                                                                <p><?php echo htmlspecialchars($sec['description_text'] ?? ''); ?></p>
                                                                <?php if (!empty($sec['button_link'])): ?>
                                                                    <a href="<?php echo htmlspecialchars($sec['button_link']); ?>" class="mm-r-m-btn">
                                                                        <?php echo htmlspecialchars($sec['button_text'] ?: 'Learn More'); ?>
                                                                    </a>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        </div>

                                                        <div class="mm1-com mm1-s3">
                                                            <?php foreach ($cols[3] as $sec): ?>
                                                                <h4><?php echo htmlspecialchars($sec['section_title'] ?? ''); ?></h4>
                                                                <ul>
                                                                    <?php if (isset($sec['links'])): ?>
                                                                        <?php foreach ($sec['links'] as $link): ?>
                                                                            <li><a href="<?php echo htmlspecialchars($link['url']); ?>"><?php echo htmlspecialchars($link['title']); ?></a></li>
                                                                        <?php endforeach; ?>
                                                                    <?php endif; ?>
                                                                </ul>
                                                            <?php endforeach; ?>
                                                        </div>

                                                        <div class="mm1-com mm1-s4">
                                                            <?php foreach ($cols[4] as $sec): ?>
                                                                <h4><?php echo htmlspecialchars($sec['section_title'] ?? ''); ?></h4>
                                                                <ul>
                                                                    <?php if (isset($sec['links'])): ?>
                                                                        <?php foreach ($sec['links'] as $link): ?>
                                                                            <li><a href="<?php echo htmlspecialchars($link['url']); ?>"><?php echo htmlspecialchars($link['title']); ?></a></li>
                                                                        <?php endforeach; ?>
                                                                    <?php endif; ?>
                                                                </ul>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--END HEADER SECTION-->

    <!-- Mobile Menu Submenu Toggle Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const hasSubItems = document.querySelectorAll('.has-sub > a');
        
        hasSubItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.parentElement;
                const subMenu = parent.querySelector('.mm-sub');
                
                // Toggle active class
                parent.classList.toggle('active');
                
                // Toggle submenu visibility
                if (subMenu.style.display === 'block') {
                    subMenu.style.display = 'none';
                } else {
                    subMenu.style.display = 'block';
                }
                
                // Close other submenus (optional, for accordion effect)
                document.querySelectorAll('.has-sub').forEach(otherParent => {
                    if (otherParent !== parent) {
                        otherParent.classList.remove('active');
                        const otherSub = otherParent.querySelector('.mm-sub');
                        if (otherSub) otherSub.style.display = 'none';
                    }
                });
            });
        });
    });
    </script>
</body>
</html>