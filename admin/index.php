<?php
include 'header.php';
include 'sidebar.php';
require_once('../includes/db_connect.php');

// ── Core Stats ──
$contact_count   = $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
$news_count      = $pdo->query("SELECT COUNT(*) FROM homepage_news")->fetchColumn();
$directory_count = $pdo->query("SELECT COUNT(*) FROM directory")->fetchColumn();
$programs_count  = $pdo->query("SELECT COUNT(*) FROM academic_programs")->fetchColumn();
$active_programs = $pdo->query("SELECT COUNT(*) FROM academic_programs WHERE is_active = 1")->fetchColumn();
$categories_count= $pdo->query("SELECT COUNT(*) FROM program_categories")->fetchColumn();
$admin_pages     = $pdo->query("SELECT COUNT(*) FROM administration_pages")->fetchColumn();
$sliders_count   = $pdo->query("SELECT COUNT(*) FROM homepage_sliders")->fetchColumn();
$gallery_count   = $pdo->query("SELECT COUNT(*) FROM homepage_gallery")->fetchColumn();

// ── Recent Enquiries ──
$recent_enquiries = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll();

// ── Recent News ──
$recent_news = $pdo->query("SELECT * FROM homepage_news ORDER BY event_date DESC LIMIT 4")->fetchAll();

// ── Programs by Category for Chart ──
$programs_by_cat = $pdo->query("SELECT pc.name, COUNT(ap.id) as cnt FROM program_categories pc LEFT JOIN academic_programs ap ON pc.id = ap.category_id GROUP BY pc.id ORDER BY cnt DESC LIMIT 8")->fetchAll();
$cat_labels = []; $cat_data = [];
foreach ($programs_by_cat as $c) { $cat_labels[] = $c['name']; $cat_data[] = (int)$c['cnt']; }

// ── Recent programs ──
$recent_programs = $pdo->query("SELECT ap.*, pc.name as category_name FROM academic_programs ap LEFT JOIN program_categories pc ON ap.category_id = pc.id ORDER BY ap.id DESC LIMIT 6")->fetchAll();

// ── Admin name ──
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$first_name = explode(' ', $admin_name)[0];

// ── Time-based greeting ──
$hour = (int)date('H');
if ($hour < 12) $greeting = 'Good Morning';
elseif ($hour < 17) $greeting = 'Good Afternoon';
else $greeting = 'Good Evening';
?>

<style>
    /* ── Dashboard Specific Styles ── */
    .dash-welcome {
        background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 50%, #0891b2 100%);
        border-radius: 24px;
        padding: 35px 40px;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 30px;
        box-shadow: 0 20px 40px rgba(37, 99, 235, 0.2);
    }
    .dash-welcome::before {
        content: '';
        position: absolute;
        right: -60px;
        top: -60px;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }
    .dash-welcome::after {
        content: '';
        position: absolute;
        right: 80px;
        bottom: -80px;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }
    .dash-welcome h1 { font-size: 2.2rem; font-weight: 900; margin: 0 0 8px; }
    .dash-welcome p { font-size: 1.05rem; opacity: 0.85; margin: 0; }
    .dash-welcome .welcome-meta { display: flex; gap: 25px; margin-top: 18px; }
    .dash-welcome .welcome-meta span { display: flex; align-items: center; gap: 8px; font-size: .9rem; font-weight: 600; opacity: 0.8; }

    /* ── Mini Stat Cards ── */
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
    .mini-stat {
        background: #fff;
        border-radius: 20px;
        padding: 22px 25px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        gap: 18px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .mini-stat:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
    .mini-stat .stat-icon-box {
        width: 55px; height: 55px;
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .mini-stat .stat-val { font-size: 2rem; font-weight: 900; line-height: 1; }
    .mini-stat .stat-lbl { font-size: .85rem; font-weight: 700; color: #64748b; margin-top: 2px; }
    .mini-stat .stat-trend {
        font-size: .75rem; font-weight: 800; display: flex; align-items: center; gap: 3px; margin-top: 4px;
    }
    .mini-stat .bg-decor {
        position: absolute; right: -10px; bottom: -10px;
        font-size: 5rem; opacity: 0.04; transform: rotate(-15deg);
    }

    /* ── Dashboard Cards ── */
    .dash-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        overflow: hidden;
        height: 100%;
    }
    .dash-card-header {
        padding: 22px 28px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .dash-card-header h3 {
        margin: 0; font-size: 1.15rem; font-weight: 800; color: #1e293b;
        display: flex; align-items: center; gap: 10px;
    }
    .dash-card-header h3 i { font-size: 1rem; }
    .dash-card-body { padding: 25px 28px; }

    /* ── Quick Access Grid ── */
    .quick-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 30px; }
    .quick-link {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #f1f5f9;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
    .quick-link:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
    .quick-link .ql-icon {
        width: 50px; height: 50px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; transition: all 0.3s;
    }
    .quick-link:hover .ql-icon { transform: scale(1.1); }
    .quick-link span { font-weight: 700; font-size: .85rem; color: #334155; }

    /* ── Activity Item ── */
    .dash-activity {
        display: flex; align-items: flex-start; gap: 15px;
        padding: 14px 0;
        border-bottom: 1px solid #f8fafc;
    }
    .dash-activity:last-child { border-bottom: none; }
    .dash-activity .act-avatar {
        width: 42px; height: 42px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 800; font-size: .85rem; flex-shrink: 0;
    }
    .dash-activity .act-body h6 { margin: 0 0 3px; font-weight: 700; font-size: .95rem; color: #1e293b; }
    .dash-activity .act-body p { margin: 0; font-size: .85rem; color: #64748b; }
    .dash-activity .act-time { font-size: .75rem; color: #94a3b8; white-space: nowrap; margin-left: auto; }

    /* ── News Timeline ── */
    .news-item {
        display: flex; gap: 15px; padding: 16px 0;
        border-bottom: 1px solid #f8fafc;
    }
    .news-item:last-child { border-bottom: none; }
    .news-item .news-dot {
        width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; margin-top: 5px;
    }
    .news-item .news-body h6 { margin: 0 0 4px; font-weight: 700; font-size: .95rem; color: #1e293b; }
    .news-item .news-body p { margin: 0 0 4px; font-size: .85rem; color: #64748b; line-height: 1.4; }
    .news-item .news-body small { font-size: .75rem; color: #94a3b8; }

    /* ── Programs Mini Table ── */
    .prog-row {
        display: flex; align-items: center; gap: 14px; padding: 12px 0;
        border-bottom: 1px solid #f8fafc;
    }
    .prog-row:last-child { border-bottom: none; }
    .prog-row .prog-icon {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: .9rem; flex-shrink: 0;
    }
    .prog-row .prog-info { flex: 1; }
    .prog-row .prog-info h6 { margin: 0; font-weight: 700; font-size: .9rem; color: #1e293b; }
    .prog-row .prog-info small { color: #94a3b8; font-size: .8rem; }

    /* ── Content overview mini-cards ── */
    .content-overview { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .co-card {
        background: #f8fafc; border-radius: 14px; padding: 18px;
        display: flex; align-items: center; gap: 14px;
    }
    .co-card .co-icon {
        width: 45px; height: 45px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
    }
    .co-card .co-val { font-size: 1.5rem; font-weight: 900; line-height: 1; }
    .co-card .co-lbl { font-size: .8rem; font-weight: 700; color: #64748b; }

    /* ── Responsive ── */
    @media (max-width: 992px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .quick-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 576px) {
        .stats-grid { grid-template-columns: 1fr; }
        .quick-grid { grid-template-columns: 1fr 1fr; }
        .dash-welcome { padding: 25px; }
        .dash-welcome h1 { font-size: 1.6rem; }
        .content-overview { grid-template-columns: 1fr; }
    }
</style>

<main class="main-content">
<div class="content-wrapper">

    <!-- ══════════════ Welcome Banner ══════════════ -->
    <div class="dash-welcome">
        <div style="position:relative;z-index:1;">
            <h1><?php echo $greeting; ?>, <?php echo htmlspecialchars($first_name); ?>! 👋</h1>
            <p>Welcome to the Valley View University Admin Panel. Here's what's happening today.</p>
            <div class="welcome-meta">
                <span><i class="fas fa-calendar-day"></i> <?php echo date('l, F j, Y'); ?></span>
                <span><i class="fas fa-clock"></i> <?php echo date('g:i A'); ?></span>
                <span><i class="fas fa-globe-africa"></i> Oyibi, Accra - Ghana</span>
            </div>
        </div>
    </div>

    <!-- ══════════════ Key Stats ══════════════ -->
    <div class="stats-grid">
        <div class="mini-stat">
            <div class="stat-icon-box" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-graduation-cap"></i></div>
            <div>
                <div class="stat-val" style="color:#2563eb;"><?php echo $programs_count; ?></div>
                <div class="stat-lbl">Programs</div>
                <div class="stat-trend" style="color:#059669;"><i class="fas fa-check-circle"></i> <?php echo $active_programs; ?> active</div>
            </div>
            <i class="fas fa-graduation-cap bg-decor"></i>
        </div>
        <div class="mini-stat">
            <div class="stat-icon-box" style="background:#ecfdf5;color:#059669;"><i class="fas fa-newspaper"></i></div>
            <div>
                <div class="stat-val" style="color:#059669;"><?php echo $news_count; ?></div>
                <div class="stat-lbl">News & Events</div>
                <div class="stat-trend" style="color:#2563eb;"><i class="fas fa-rss"></i> Published</div>
            </div>
            <i class="fas fa-newspaper bg-decor"></i>
        </div>
        <div class="mini-stat">
            <div class="stat-icon-box" style="background:#f3e8ff;color:#7c3aed;"><i class="fas fa-users"></i></div>
            <div>
                <div class="stat-val" style="color:#7c3aed;"><?php echo $directory_count; ?></div>
                <div class="stat-lbl">Directory Profiles</div>
                <div class="stat-trend" style="color:#7c3aed;"><i class="fas fa-address-book"></i> Faculty & Staff</div>
            </div>
            <i class="fas fa-users bg-decor"></i>
        </div>
        <div class="mini-stat">
            <div class="stat-icon-box" style="background:#fff7ed;color:#ea580c;"><i class="fas fa-envelope-open-text"></i></div>
            <div>
                <div class="stat-val" style="color:#ea580c;"><?php echo $contact_count; ?></div>
                <div class="stat-lbl">Enquiries</div>
                <div class="stat-trend" style="color:#ea580c;"><i class="fas fa-inbox"></i> Messages</div>
            </div>
            <i class="fas fa-envelope bg-decor"></i>
        </div>
    </div>

    <!-- ══════════════ Quick Access ══════════════ -->
    <div class="quick-grid">
        <a href="manage_homepage.php" class="quick-link">
            <div class="ql-icon" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-home"></i></div>
            <span>Homepage</span>
        </a>
        <a href="manage_programs.php" class="quick-link">
            <div class="ql-icon" style="background:#ecfdf5;color:#059669;"><i class="fas fa-graduation-cap"></i></div>
            <span>Programs</span>
        </a>
        <a href="manage_contact.php" class="quick-link">
            <div class="ql-icon" style="background:#fef2f2;color:#ef4444;"><i class="fas fa-phone-alt"></i></div>
            <span>Contact Page</span>
        </a>
        <a href="manage_news.php" class="quick-link">
            <div class="ql-icon" style="background:#fff7ed;color:#ea580c;"><i class="fas fa-newspaper"></i></div>
            <span>News & Events</span>
        </a>
        <a href="manage_administration_pages.php" class="quick-link">
            <div class="ql-icon" style="background:#ecfeff;color:#0891b2;"><i class="fas fa-university"></i></div>
            <span>Resource Pages</span>
        </a>
        <a href="manage_directory.php" class="quick-link">
            <div class="ql-icon" style="background:#f3e8ff;color:#7c3aed;"><i class="fas fa-address-book"></i></div>
            <span>Directory</span>
        </a>
        <a href="manage_graduate_page.php" class="quick-link">
            <div class="ql-icon" style="background:#fefce8;color:#ca8a04;"><i class="fas fa-user-graduate"></i></div>
            <span>Graduate School</span>
        </a>
        <a href="manage_alumni_page.php" class="quick-link">
            <div class="ql-icon" style="background:#fdf2f8;color:#db2777;"><i class="fas fa-users-cog"></i></div>
            <span>Alumni Network</span>
        </a>
        <a href="manage_users.php" class="quick-link">
            <div class="ql-icon" style="background:#f1f5f9;color:#475569;"><i class="fas fa-user-lock"></i></div>
            <span>Admin Users</span>
        </a>
    </div>

    <!-- ══════════════ Charts Row ══════════════ -->
    <div class="row g-4 mb-4">
        <!-- Activity Chart -->
        <div class="col-lg-8">
            <div class="dash-card">
                <div class="dash-card-header">
                    <h3><i class="fas fa-chart-area" style="color:#2563eb;"></i> Website Activity Overview</h3>
                    <span style="font-size:.8rem;color:#94a3b8;font-weight:700;">Monthly Trends</span>
                </div>
                <div class="dash-card-body">
                    <canvas id="activityChart" height="85"></canvas>
                </div>
            </div>
        </div>

        <!-- Programs Distribution -->
        <div class="col-lg-4">
            <div class="dash-card">
                <div class="dash-card-header">
                    <h3><i class="fas fa-chart-pie" style="color:#7c3aed;"></i> Programs by Faculty</h3>
                </div>
                <div class="dash-card-body">
                    <canvas id="programsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════ Programs + Enquiries Row ══════════════ -->
    <div class="row g-4 mb-4">
        <!-- Recent Programs -->
        <div class="col-lg-6">
            <div class="dash-card">
                <div class="dash-card-header">
                    <h3><i class="fas fa-book-open" style="color:#059669;"></i> Recent Programs</h3>
                    <a href="manage_programs.php" style="font-size:.8rem;font-weight:700;color:#2563eb;text-decoration:none;">View All →</a>
                </div>
                <div class="dash-card-body" style="padding:15px 28px;">
                    <?php foreach ($recent_programs as $p): ?>
                    <div class="prog-row">
                        <div class="prog-icon" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-book-open"></i></div>
                        <div class="prog-info">
                            <h6><?php echo htmlspecialchars($p['title']); ?></h6>
                            <small><?php echo htmlspecialchars($p['category_name'] ?? 'Uncategorized'); ?> · <?php echo htmlspecialchars($p['duration']); ?></small>
                        </div>
                        <span style="padding:4px 10px;border-radius:8px;font-weight:800;font-size:.7rem;background:<?php echo $p['is_active'] ? '#ecfdf5' : '#fef2f2'; ?>;color:<?php echo $p['is_active'] ? '#059669' : '#ef4444'; ?>;"><?php echo $p['is_active'] ? 'Active' : 'Off'; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Recent Enquiries -->
        <div class="col-lg-6">
            <div class="dash-card">
                <div class="dash-card-header">
                    <h3><i class="fas fa-inbox" style="color:#ea580c;"></i> Recent Enquiries</h3>
                    <a href="manage_contact_messages.php" style="font-size:.8rem;font-weight:700;color:#2563eb;text-decoration:none;">View All →</a>
                </div>
                <div class="dash-card-body" style="padding:10px 28px;">
                    <?php
                    $gradient_colors = ['linear-gradient(135deg,#2563eb,#7c3aed)','linear-gradient(135deg,#059669,#0891b2)','linear-gradient(135deg,#ea580c,#ef4444)','linear-gradient(135deg,#ca8a04,#ea580c)','linear-gradient(135deg,#db2777,#7c3aed)'];
                    foreach ($recent_enquiries as $i => $msg):
                        $time_ago = date('M d', strtotime($msg['created_at']));
                    ?>
                    <div class="dash-activity">
                        <div class="act-avatar" style="background:<?php echo $gradient_colors[$i % 5]; ?>;">
                            <?php echo strtoupper(substr($msg['name'], 0, 1)); ?>
                        </div>
                        <div class="act-body">
                            <h6><?php echo htmlspecialchars($msg['name']); ?></h6>
                            <p><?php echo htmlspecialchars($msg['inquiry_type'] ?? 'General'); ?></p>
                        </div>
                        <span class="act-time"><?php echo $time_ago; ?></span>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($recent_enquiries)): ?>
                    <p style="text-align:center;color:#94a3b8;padding:20px;font-style:italic;">No enquiries yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════ Content Stats Row ══════════════ -->
    <div class="dash-card mb-4">
        <div class="dash-card-header">
            <h3><i class="fas fa-database" style="color:#0891b2;"></i> Content Overview</h3>
        </div>
        <div class="dash-card-body">
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;">
                <div class="co-card">
                    <div class="co-icon" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-file-alt"></i></div>
                    <div><div class="co-val" style="color:#2563eb;"><?php echo $admin_pages; ?></div><div class="co-lbl">Managed Pages</div></div>
                </div>
                <div class="co-card">
                    <div class="co-icon" style="background:#ecfdf5;color:#059669;"><i class="fas fa-tags"></i></div>
                    <div><div class="co-val" style="color:#059669;"><?php echo $categories_count; ?></div><div class="co-lbl">Program Categories</div></div>
                </div>
                <div class="co-card">
                    <div class="co-icon" style="background:#fff7ed;color:#ea580c;"><i class="fas fa-images"></i></div>
                    <div><div class="co-val" style="color:#ea580c;"><?php echo $sliders_count; ?></div><div class="co-lbl">Homepage Sliders</div></div>
                </div>
                <div class="co-card">
                    <div class="co-icon" style="background:#f3e8ff;color:#7c3aed;"><i class="fas fa-photo-video"></i></div>
                    <div><div class="co-val" style="color:#7c3aed;"><?php echo $gallery_count; ?></div><div class="co-lbl">Gallery Items</div></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════ News + System Row ══════════════ -->
    <div class="row g-4">
        <!-- Latest News -->
        <div class="col-lg-6">
            <div class="dash-card">
                <div class="dash-card-header">
                    <h3><i class="fas fa-bullhorn" style="color:#ca8a04;"></i> Latest News & Events</h3>
                    <a href="manage_news.php" style="font-size:.8rem;font-weight:700;color:#2563eb;text-decoration:none;">Manage →</a>
                </div>
                <div class="dash-card-body" style="padding:15px 28px;">
                    <?php
                    $dot_colors = ['#ef4444','#2563eb','#059669','#ea580c'];
                    foreach ($recent_news as $i => $event):
                    ?>
                    <div class="news-item">
                        <div class="news-dot" style="background:<?php echo $dot_colors[$i % 4]; ?>;"></div>
                        <div class="news-body">
                            <h6><?php echo htmlspecialchars($event['title']); ?></h6>
                            <p><?php echo htmlspecialchars(substr($event['description'], 0, 90)); ?>...</p>
                            <small><i class="far fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($event['event_date'])); ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($recent_news)): ?>
                    <p style="text-align:center;color:#94a3b8;padding:20px;font-style:italic;">No news posted yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="col-lg-6">
            <div class="dash-card">
                <div class="dash-card-header">
                    <h3><i class="fas fa-server" style="color:#64748b;"></i> System Information</h3>
                </div>
                <div class="dash-card-body">
                    <div style="display:flex;flex-direction:column;gap:14px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;background:#f8fafc;border-radius:12px;">
                            <span style="font-weight:700;color:#475569;display:flex;align-items:center;gap:8px;"><i class="fab fa-php" style="color:#7b7fb5;font-size:1.2rem;"></i> PHP Version</span>
                            <span style="font-weight:800;color:#1e293b;background:#e0e7ff;padding:4px 12px;border-radius:8px;font-size:.85rem;"><?php echo phpversion(); ?></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;background:#f8fafc;border-radius:12px;">
                            <span style="font-weight:700;color:#475569;display:flex;align-items:center;gap:8px;"><i class="fas fa-database" style="color:#0891b2;"></i> Database</span>
                            <span style="font-weight:800;color:#1e293b;background:#e0e7ff;padding:4px 12px;border-radius:8px;font-size:.85rem;">MySQL <?php echo $pdo->getAttribute(PDO::ATTR_SERVER_VERSION); ?></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;background:#f8fafc;border-radius:12px;">
                            <span style="font-weight:700;color:#475569;display:flex;align-items:center;gap:8px;"><i class="fas fa-globe" style="color:#2563eb;"></i> Server</span>
                            <span style="font-weight:800;color:#1e293b;background:#e0e7ff;padding:4px 12px;border-radius:8px;font-size:.85rem;">XAMPP Apache</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;background:#f8fafc;border-radius:12px;">
                            <span style="font-weight:700;color:#475569;display:flex;align-items:center;gap:8px;"><i class="fas fa-user-shield" style="color:#7c3aed;"></i> Logged In As</span>
                            <span style="font-weight:800;color:#1e293b;background:#e0e7ff;padding:4px 12px;border-radius:8px;font-size:.85rem;"><?php echo htmlspecialchars($admin_name); ?></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;background:#f8fafc;border-radius:12px;">
                            <span style="font-weight:700;color:#475569;display:flex;align-items:center;gap:8px;"><i class="fas fa-table" style="color:#059669;"></i> Database Tables</span>
                            <span style="font-weight:800;color:#1e293b;background:#e0e7ff;padding:4px 12px;border-radius:8px;font-size:.85rem;"><?php echo $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'valley_view_uni'")->fetchColumn(); ?> tables</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;background:#f8fafc;border-radius:12px;">
                            <span style="font-weight:700;color:#475569;display:flex;align-items:center;gap:8px;"><i class="fas fa-sync" style="color:#ea580c;"></i> Last Login</span>
                            <span style="font-weight:800;color:#1e293b;background:#e0e7ff;padding:4px 12px;border-radius:8px;font-size:.85rem;"><?php echo date('M d, Y g:i A'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</main>

<script>
// ── Activity Chart ──
const actCtx = document.getElementById('activityChart');
if (actCtx) {
    new Chart(actCtx, {
        type: 'line',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            datasets: [{
                label: 'Page Views',
                data: [1200,1900,1500,2100,1800,2400,2200,2600,2300,2800,2500,3000],
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.08)',
                tension: 0.4, fill: true,
                borderWidth: 3,
                pointBackgroundColor: '#2563eb',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            },{
                label: 'Enquiries',
                data: [800,1100,900,1400,1200,1600,1300,1700,1500,1900,1600,2000],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,0.08)',
                tension: 0.4, fill: true,
                borderWidth: 3,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top', labels: { usePointStyle: true, padding: 20, font: { weight: '600', size: 12 } } }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { weight: '600' }, color: '#94a3b8' } },
                x: { grid: { display: false }, ticks: { font: { weight: '600' }, color: '#94a3b8' } }
            }
        }
    });
}

// ── Programs Doughnut Chart ──
const progCtx = document.getElementById('programsChart');
if (progCtx) {
    new Chart(progCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($cat_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($cat_data); ?>,
                backgroundColor: ['#2563eb','#059669','#7c3aed','#ea580c','#ef4444','#ca8a04','#0891b2','#db2777'],
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 12, font: { size: 11, weight: '600' } } }
            }
        }
    });
}
</script>

<?php include 'footer.php'; ?>