<?php
require_once 'includes/db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$id) {
    header("Location: faculty_encyclopedia.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM directory WHERE id = ?");
$stmt->execute([$id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    header("Location: index.php");
    exit();
}

$page_title = $profile['name'] . " - Valley View University";
include 'includes/header.php';

// Determine accent color based on type
$accentColor = $profile['type'] === 'faculty' ? '#006400' : '#002147';
$accentColorLight = $profile['type'] === 'faculty' ? 'rgba(0, 100, 0, 0.1)' : 'rgba(0, 33, 71, 0.1)';
?>

<style>
    :root {
        --accent-color: <?php echo $accentColor; ?>;
        --accent-light: <?php echo $accentColorLight; ?>;
        --vvu-wine: #800000;
    }

    .profile-page {
        min-height: 100vh;
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
    }

    /* Hero Section */
    .profile-hero {
        background: linear-gradient(135deg, var(--accent-color) 0%, #1e293b 100%);
        padding: 80px 0 120px;
        position: relative;
        overflow: hidden;
    }

    .profile-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .hero-nav-row {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 20px;
        flex-wrap: wrap;
    }

    .breadcrumb-modern {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 50px;
        padding: 12px 24px;
        display: inline-flex;
        gap: 8px;
        align-items: center;
    }

    .breadcrumb-modern a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: color 0.2s;
    }

    .breadcrumb-modern a:hover {
        color: white;
    }

    .breadcrumb-modern .separator {
        color: rgba(255, 255, 255, 0.4);
        font-size: 12px;
    }

    .breadcrumb-modern .current {
        color: white;
        font-weight: 600;
        font-size: 14px;
    }

    /* Profile Card */
    .profile-main-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.08);
        margin-top: -80px;
        position: relative;
        z-index: 10;
        overflow: hidden;
    }

    .profile-left {
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        padding: 40px;
        border-right: 1px solid #e2e8f0;
    }

    @media (max-width: 991px) {
        .profile-left {
            border-right: none;
            border-bottom: 1px solid #e2e8f0;
        }
    }

    .profile-image-wrapper {
        position: relative;
        margin-bottom: 24px;
    }

    .profile-image {
        width: 100%;
        aspect-ratio: 1/1;
        object-fit: cover;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
    }

    .type-badge {
        position: absolute;
        bottom: 12px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.85) 100%);
        color: var(--accent-color);
        padding: 6px 16px;
        border-radius: 30px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    /* Contact Info */
    .contact-section {
        margin-top: 30px;
    }

    .contact-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 16px;
        transition: all 0.3s ease;
    }

    .contact-card:hover {
        border-color: var(--accent-color);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        transform: translateY(-2px);
    }

    .contact-card-icon {
        width: 48px;
        height: 48px;
        background: var(--accent-light);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent-color);
        font-size: 22px;
        flex-shrink: 0;
    }

    .contact-card-label {
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 700;
        color: #94a3b8;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .contact-card-value {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
        word-break: break-word;
    }

    .contact-card-value a {
        color: var(--accent-color);
        text-decoration: none;
    }

    .contact-card-value a:hover {
        text-decoration: underline;
    }

    /* Profile Right Content */
    .profile-right {
        padding: 40px;
    }

    @media (max-width: 767px) {
        .profile-right {
            padding: 24px;
        }
        .profile-left {
            padding: 24px;
        }
    }

    .job-title-badge {
        display: inline-block;
        background: var(--accent-light);
        color: var(--accent-color);
        padding: 8px 18px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 16px;
    }

    .profile-name {
        font-size: clamp(28px, 5vw, 42px);
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 8px;
        line-height: 1.2;
    }

    .profile-department {
        font-size: clamp(16px, 3vw, 20px);
        color: #64748b;
        font-weight: 500;
        margin-bottom: 32px;
    }

    /* Content Sections */
    .content-section {
        margin-bottom: 40px;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .section-icon {
        width: 40px;
        height: 40px;
        background: var(--accent-light);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent-color);
        font-size: 20px;
    }

    .section-title {
        font-size: 20px;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
    }

    .section-content {
        font-size: 16px;
        line-height: 1.8;
        color: #475569;
    }

    /* Education List */
    .education-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .education-item {
        display: flex;
        gap: 16px;
        padding: 16px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .education-item:last-child {
        border-bottom: none;
    }

    .education-icon {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, var(--accent-color), #334155);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        flex-shrink: 0;
    }

    .education-text {
        font-size: 15px;
        color: #334155;
        font-weight: 500;
        line-height: 1.6;
    }

    /* Tags for Research Interests */
    .interest-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .interest-tag {
        background: #f1f5f9;
        color: #475569;
        padding: 10px 18px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .interest-tag:hover {
        background: var(--accent-light);
        color: var(--accent-color);
    }

    /* Publications */
    .publication-item {
        background: #f8fafc;
        border-left: 4px solid var(--accent-color);
        padding: 20px;
        border-radius: 0 12px 12px 0;
        margin-bottom: 16px;
    }

    .publication-item p {
        margin: 0;
        font-size: 15px;
        color: #475569;
        line-height: 1.7;
    }

    /* Status Badge */
    .status-section {
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid #e2e8f0;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #dcfce7;
        color: #166534;
        padding: 10px 18px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 700;
    }

    .status-badge .dot {
        width: 8px;
        height: 8px;
        background: #22c55e;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* Back Button */
    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.15);
        color: white;
        padding: 12px 24px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .back-button:hover {
        background: rgba(255, 255, 255, 0.25);
        color: white;
        text-decoration: none;
        transform: translateX(-5px);
    }

    /* Animation */
    .animate-fadeIn {
        animation: fadeIn 0.6s ease-out forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<main class="profile-page">
    <!-- Hero Section -->
    <section class="profile-hero">
        <div class="container">
            <div class="hero-nav-row">
                <a href="<?php echo $profile['type'] == 'faculty' ? 'faculty_encyclopedia.php' : 'staff_encyclopedia.php'; ?>" class="back-button">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Back to <?php echo $profile['type'] == 'faculty' ? 'Faculty' : 'Staff'; ?> Directory
                </a>

                <div class="breadcrumb-modern">
                    <a href="index.php">Home</a>
                    <span class="separator">›</span>
                    <a href="<?php echo $profile['type'] == 'faculty' ? 'faculty_encyclopedia.php' : 'staff_encyclopedia.php'; ?>">
                        <?php echo $profile['type'] == 'faculty' ? 'Faculty' : 'Staff'; ?> Encyclopedia
                    </a>
                    <span class="separator">›</span>
                    <span class="current"><?php echo strip_tags($profile['name']); ?></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Profile Card -->
    <section class="container pb-5">
        <div class="profile-main-card animate-fadeIn">
            <div class="row g-0">
                <!-- Left Sidebar -->
                <div class="col-lg-4">
                    <div class="profile-left">
                        <div class="profile-image-wrapper">
                            <img src="<?php echo strip_tags($profile['image_url'] ?: 'images/default-profile.png'); ?>" 
                                 alt="<?php echo strip_tags($profile['name']); ?>" 
                                 class="profile-image">
                            <span class="type-badge">
                                <?php echo $profile['type'] == 'faculty' ? 'Faculty Member' : 'Staff Member'; ?>
                            </span>
                        </div>

                        <!-- Contact Information -->
                        <div class="contact-section">
                            <?php if ($profile['email']): ?>
                            <div class="contact-card d-flex gap-3 align-items-center">
                                <div class="contact-card-icon">
                                    <span class="material-symbols-outlined">mail</span>
                                </div>
                                <div>
                                    <div class="contact-card-label">Email Address</div>
                                    <div class="contact-card-value">
                                        <a href="mailto:<?php echo strip_tags($profile['email']); ?>">
                                            <?php echo strip_tags($profile['email']); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ($profile['phone']): ?>
                            <div class="contact-card d-flex gap-3 align-items-center">
                                <div class="contact-card-icon">
                                    <span class="material-symbols-outlined">call</span>
                                </div>
                                <div>
                                    <div class="contact-card-label">Phone Number</div>
                                    <div class="contact-card-value">
                                        <?php echo strip_tags($profile['phone']); ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ($profile['office_location']): ?>
                            <div class="contact-card d-flex gap-3 align-items-center">
                                <div class="contact-card-icon">
                                    <span class="material-symbols-outlined">location_on</span>
                                </div>
                                <div>
                                    <div class="contact-card-label">Office Location</div>
                                    <div class="contact-card-value">
                                        <?php echo strip_tags($profile['office_location']); ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Employment Status -->
                            <div class="status-section">
                                <div class="contact-card-label mb-2">Employment Status</div>
                                <div class="status-badge">
                                    <span class="dot"></span>
                                    <?php echo strip_tags($profile['employment_status'] ?: 'Full-time'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Content -->
                <div class="col-lg-8">
                    <div class="profile-right">
                        <!-- Header -->
                        <?php if ($profile['job_title']): ?>
                        <span class="job-title-badge"><?php echo strip_tags($profile['job_title']); ?></span>
                        <?php endif; ?>

                        <h1 class="profile-name"><?php echo strip_tags($profile['name']); ?></h1>
                        <p class="profile-department"><?php echo strip_tags($profile['department']); ?></p>

                        <!-- Biography -->
                        <div class="content-section">
                            <div class="section-header">
                                <div class="section-icon">
                                    <span class="material-symbols-outlined">person</span>
                                </div>
                                <h3 class="section-title">Biography</h3>
                            </div>
                            <div class="section-content">
                                <?php 
                                $bio_content = $profile['bio'] ?: 'No biography available for this profile.';
                                // Allow safe HTML tags from CKEditor
                                echo strip_tags($bio_content, '<p><br><strong><b><em><i><u><ul><ol><li><a><blockquote><h1><h2><h3><h4><h5><h6>'); 
                                ?>
                            </div>
                        </div>

                        <!-- Education -->
                        <?php if ($profile['education']): ?>
                        <div class="content-section">
                            <div class="section-header">
                                <div class="section-icon">
                                    <span class="material-symbols-outlined">school</span>
                                </div>
                                <h3 class="section-title">Education</h3>
                            </div>
                            <div class="section-content">
                                <?php echo strip_tags($profile['education'], '<p><br><strong><b><em><i><u><ul><ol><li><a>'); ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Research Interests -->
                        <?php if ($profile['research_interests']): ?>
                        <div class="content-section">
                            <div class="section-header">
                                <div class="section-icon">
                                    <span class="material-symbols-outlined">science</span>
                                </div>
                                <h3 class="section-title">Research Interests</h3>
                            </div>
                            <div class="section-content">
                                <?php echo strip_tags($profile['research_interests'], '<p><br><strong><b><em><i><u><ul><ol><li><a>'); ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Publications -->
                        <?php if ($profile['publications']): ?>
                        <div class="content-section">
                            <div class="section-header">
                                <div class="section-icon">
                                    <span class="material-symbols-outlined">article</span>
                                </div>
                                <h3 class="section-title">Key Publications</h3>
                            </div>
                            <div class="section-content">
                                <?php echo strip_tags($profile['publications'], '<p><br><strong><b><em><i><u><ul><ol><li><a>'); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
