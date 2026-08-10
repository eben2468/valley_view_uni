<?php
// Fetch Footer Settings
$stmt = $pdo->query("SELECT * FROM footer_settings");
$footer_settings_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
$footer_settings = [];
foreach ($footer_settings_raw as $s) {
    $footer_settings[$s['setting_key']] = $s['setting_value'];
}

// Fetch Footer Sections and Links
$stmt = $pdo->query("SELECT * FROM footer_sections WHERE is_active = 1 ORDER BY display_order");
$footer_sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM footer_links WHERE is_active = 1 ORDER BY section_id, display_order");
$footer_links_all = $stmt->fetchAll(PDO::FETCH_ASSOC);

$footer_links = [];
foreach ($footer_links_all as $link) {
    $footer_links[$link['section_id']][] = $link;
}
?>

    <!-- FOOTER -->
    <section class="wed-hom-footer">
        <div class="container">
            <div class="row wed-foot-link">
                <?php 
                // Display first 3 sections (Link Columns)
                for ($i = 0; $i < 3; $i++): 
                    if (isset($footer_sections[$i])):
                        $section = $footer_sections[$i];
                ?>
                <div class="col-md-4 <?php echo $i == 0 ? 'foot-tc-mar-t-o' : ''; ?>">
                    <h4><?php echo htmlspecialchars($section['title']); ?></h4>
                    <ul>
                        <?php if (isset($footer_links[$section['id']])): ?>
                            <?php foreach ($footer_links[$section['id']] as $link): ?>
                                <li><a href="<?php echo htmlspecialchars($link['url']); ?>"><?php echo htmlspecialchars($link['label']); ?></a></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php 
                    endif;
                endfor; 
                ?>
            </div>
            <div class="row wed-foot-link-1">
                <div class="col-md-4 foot-tc-mar-t-o">
                    <h4>Get In Touch</h4>
                    <p>Address: <?php echo htmlspecialchars($footer_settings['contact_address'] ?? ''); ?></p>
                    <p>Phone: <a href="tel:<?php echo htmlspecialchars($footer_settings['contact_phone'] ?? ''); ?>"><?php echo htmlspecialchars($footer_settings['contact_phone'] ?? ''); ?></a></p>
                    <p>Email: <a href="mailto:<?php echo htmlspecialchars($footer_settings['contact_email'] ?? ''); ?>"><?php echo htmlspecialchars($footer_settings['contact_email'] ?? ''); ?></a></p>
                </div>
                <div class="col-md-4">
                    <h4>Connect With Us</h4>
                    <p><?php echo htmlspecialchars($footer_settings['connect_description'] ?? ''); ?></p>
                </div>
                <div class="col-md-4">
                    <h4>SOCIAL MEDIA</h4>
                    <ul>
                        <?php 
                        // Section 4 is usually Social Media
                        $social_section_id = 4;
                        if (isset($footer_links[$social_section_id])):
                            foreach ($footer_links[$social_section_id] as $social):
                        ?>
                        <li><a href="<?php echo htmlspecialchars($social['url']); ?>"><i class="fa-brands <?php echo htmlspecialchars($social['icon_class']); ?>" aria-hidden="true"></i></a></li>
                        <?php 
                            endforeach;
                        endif; 
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- COPY RIGHTS -->
    <section class="wed-rights">
        <div class="container">
            <div class="row">
                <div class="copy-right">
                   <p><?php echo htmlspecialchars($footer_settings['copyright_text'] ?? '© 2026 Valley View University. All Rights Reserved.'); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!--Import jQuery before materialize.js-->
    <!-- Self-hosted 3.7.1. Was jQuery 3.6.0 from code.jquery.com (pentest
         Finding 8): 3.6.0 carries known XSS issues fixed in 3.7.x, and loading
         it from a third-party CDN gave that CDN script-execution rights on
         every page of the site. -->
    <script src="js/vendor/jquery-3.7.1.min.js"></script>
    <script src="Education-Website-and-AdminPanel/js/bootstrap.min.js"></script>
    <script src="Education-Website-and-AdminPanel/js/materialize.min.js"></script>
    <script src="Education-Website-and-AdminPanel/js/custom.js"></script>
    <!-- Header Auto-hide on Scroll -->
    <!-- <script src="js/header-scroll.js"></script> -->
</body>
</html>