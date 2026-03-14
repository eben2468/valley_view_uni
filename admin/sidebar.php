        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Valley View University</span>
                </div>
            </div>
            
            <div class="sidebar-nav">
                <div class="nav-section">
                    <p class="nav-label">Main Menu</p>
                    <ul>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                            <a href="index.php">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_homepage_content.php' ? 'active' : ''; ?>">
                            <a href="manage_homepage_content.php">
                                <i class="fas fa-home"></i>
                                <span>Homepage Content</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_about_pages.php' ? 'active' : ''; ?>">
                            <a href="manage_about_pages.php">
                                <i class="fas fa-info-circle"></i>
                                <span>About Pages</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_contact_page.php' ? 'active' : ''; ?>">
                            <a href="manage_contact_page.php">
                                <i class="fas fa-address-book"></i>
                                <span>Contact Page</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_academic_pages.php' ? 'active' : ''; ?>">
                            <a href="manage_academic_pages.php">
                                <i class="fas fa-file-alt"></i>
                                <span>Academic Overview Pages</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_admissions_pages.php' ? 'active' : ''; ?>">
                            <a href="manage_admissions_pages.php">
                                <i class="fas fa-user-shield"></i>
                                <span>Admissions Info Pages</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_info_pages.php' ? 'active' : ''; ?>">
                            <a href="manage_info_pages.php">
                                <i class="fas fa-info-circle"></i>
                                <span>Student Info Pages</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_departmental_resources.php' ? 'active' : ''; ?>">
                            <a href="manage_departmental_resources.php">
                                <i class="fas fa-layer-group"></i>
                                <span>Resource Dept CMS</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_faqs.php' ? 'active' : ''; ?>">
                            <a href="manage_faqs.php">
                                <i class="fas fa-question-circle"></i>
                                <span>Manage FAQs</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_strategy_history.php' ? 'active' : ''; ?>">
                            <a href="manage_strategy_history.php">
                                <i class="fas fa-history"></i>
                                <span>Strategy & History</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_administration_pages.php' || basename($_SERVER['PHP_SELF']) == 'edit_administration_page.php' ? 'active' : ''; ?>">
                            <a href="manage_administration_pages.php">
                                <i class="fas fa-university"></i>
                                <span>Resource & Admin Pages</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_resources_pages.php' ? 'active' : ''; ?>">
                            <a href="manage_resources_pages.php">
                                <i class="fas fa-file-invoice"></i>
                                <span>Application & Resources</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_programs.php' || basename($_SERVER['PHP_SELF']) == 'edit_program_detail.php' ? 'active' : ''; ?>">
                            <a href="manage_programs.php">
                                <i class="fas fa-graduation-cap"></i>
                                <span>Academic Programs</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_directory.php' || basename($_SERVER['PHP_SELF']) == 'edit_directory.php' ? 'active' : ''; ?>">
                            <a href="manage_directory.php">
                                <i class="fas fa-users"></i>
                                <span>Staff & Faculty</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_university_directory.php' ? 'active' : ''; ?>">
                            <a href="manage_university_directory.php">
                                <i class="fas fa-sitemap"></i>
                                <span>Univ. Directory hierarchy</span>
                            </a>
                        </li>
                        <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'manage_news.php' || basename($_SERVER['PHP_SELF']) == 'edit_news_article.php') ? 'active' : ''; ?>">
                            <a href="manage_news.php">
                                <i class="fas fa-newspaper"></i>
                                <span>News & Events</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fas fa-envelope"></i>
                                <span>Messages</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_navigation.php' ? 'active' : ''; ?>">
                            <a href="manage_navigation.php">
                                <i class="fas fa-bars"></i>
                                <span>Navigation Settings</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_encyclopedia_content.php' ? 'active' : ''; ?>">
                            <a href="manage_encyclopedia_content.php">
                                <i class="fas fa-edit"></i>
                                <span>Encyclopedia Content</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_campus_life_pages.php' ? 'active' : ''; ?>">
                            <a href="manage_campus_life_pages.php">
                                <i class="fas fa-university"></i>
                                <span>Campus Life Pages</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_campus_life_lists.php' ? 'active' : ''; ?>">
                            <a href="manage_campus_life_lists.php">
                                <i class="fas fa-list-ul"></i>
                                <span>Campus Life Lists</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_ventures_pages.php' ? 'active' : ''; ?>">
                            <a href="manage_ventures_pages.php">
                                <i class="fas fa-store"></i>
                                <span>Ventures & Services</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_alumni_page.php' ? 'active' : ''; ?>">
                            <a href="manage_alumni_page.php">
                                <i class="fas fa-user-graduate"></i>
                                <span>Alumni Network</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_graduate_page.php' ? 'active' : ''; ?>">
                            <a href="manage_graduate_page.php">
                                <i class="fas fa-graduation-cap"></i>
                                <span>Graduate School</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_footer.php' ? 'active' : ''; ?>">
                            <a href="manage_footer.php">
                                <i class="fas fa-shoe-prints"></i>
                                <span>Footer Management</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'active' : ''; ?>">
                            <a href="manage_users.php">
                                <i class="fas fa-user-lock"></i>
                                <span>Manage Admin Users</span>
                            </a>
                        </li>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                            <a href="settings.php">
                                <i class="fas fa-cog"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>
