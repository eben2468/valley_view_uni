<?php
/**
 * E-Books Access Gate
 *
 * Every route to the Google Drive collection (the "Access E-Books Collection"
 * button and the QR code) points here first. Visitors must supply a valid VVU
 * student/staff email address before they are forwarded to the Drive folder.
 */
require_once 'includes/db_connect.php';
require_once 'includes/ebook_access.php';

$resource_key = isset($_GET['r']) ? preg_replace('/[^a-z0-9_\-]/i', '', $_GET['r']) : 'ebooks';
if ($resource_key === '') {
    $resource_key = 'ebooks';
}
$resource = vvu_ebook_resource($resource_key, $pdo);

if (!$resource) {
    include '404.php';
    exit;
}

$error = '';
$submitted_email = '';
$lockout = vvu_ebook_lockout_remaining();

// "Not you?" — drop the current verification and show the form again.
if (isset($_GET['switch'])) {
    vvu_ebook_revoke_access();
    header('Location: ' . vvu_ebook_gate_url($resource_key));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_email = trim((string)($_POST['student_email'] ?? ''));

    if ($lockout > 0) {
        $error = 'Too many failed attempts. Please try again in ' . ceil($lockout / 60) . ' minute(s).';
    } elseif (!vvu_ebook_check_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Your session expired. Please submit the form again.';
    } elseif ($submitted_email === '') {
        $error = 'Please enter your university email address.';
    } elseif (!vvu_ebook_is_valid_email($submitted_email)) {
        vvu_ebook_record_failed_attempt();
        $error = 'That address is not recognised. Use your VVU email ending in @st.vvu.edu.gh or @vvu.edu.gh.';
        $lockout = vvu_ebook_lockout_remaining();
    } else {
        // Redirect back as a GET so a refresh does not re-post the form; the
        // block below then logs the visit and forwards to the resource.
        vvu_ebook_grant_access($submitted_email);
        header('Location: ' . vvu_ebook_gate_url($resource_key));
        exit;
    }
}

// Already verified (this visit, or just now) — send them straight through.
if ($error === '' && vvu_ebook_has_access() && !empty($resource['url'])) {
    vvu_ebook_log_access($pdo, vvu_ebook_verified_email(), $resource_key);
    header('Location: ' . $resource['url']);
    exit;
}

// Verified, but the library has not configured a destination link yet.
$unconfigured = ($error === '' && vvu_ebook_has_access() && empty($resource['url']));
if ($unconfigured) {
    $error = 'You are verified, but this collection has no link configured yet. Please contact the library.';
}

$page_title = 'Verify Your VVU Email - ' . $resource['label'];
$active_page = 'academics';
$csrf = vvu_ebook_csrf_token();

include 'includes/header.php';
?>

<style>
    .gate-wrap {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 60%, #eef2ff 100%);
    }
    .dark .gate-wrap {
        background: linear-gradient(135deg, #0f172a 0%, #111827 60%, #1e1b4b 100%);
    }
    .gate-card {
        width: 100%;
        max-width: 720px;
        background: #fff;
        border-radius: 36px;
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.12);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .dark .gate-card {
        background: #1e293b;
        border-color: rgba(255,255,255,0.08);
    }
    .gate-head {
        padding: 44px 44px 28px;
        text-align: center;
        background: linear-gradient(135deg, #1e3a8a 0%, #4338ca 100%);
        color: #fff;
    }
    .gate-lock {
        width: 76px;
        height: 76px;
        margin: 0 auto 20px;
        border-radius: 26px;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .gate-lock .material-symbols-outlined { font-size: 2.6rem; }
    .gate-head h1 {
        font-size: 2.4rem;
        font-weight: 900;
        margin-bottom: 12px;
        line-height: 1.2;
        color: #fff;
    }
    .gate-head p {
        font-size: 1.35rem;
        line-height: 1.7;
        color: rgba(255,255,255,0.85);
        margin: 0;
    }
    .gate-body { padding: 36px 44px 44px; }
    .gate-resource {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 18px 22px;
        border-radius: 20px;
        background: #f1f5f9;
        margin-bottom: 28px;
    }
    .dark .gate-resource { background: rgba(255,255,255,0.05); }
    .gate-resource .material-symbols-outlined { color: #4338ca; font-size: 2rem; }
    .gate-resource strong {
        display: block;
        font-size: 1.4rem;
        font-weight: 800;
        color: #0f172a;
    }
    .dark .gate-resource strong { color: #f1f5f9; }
    .gate-resource span {
        font-size: 1.2rem;
        color: #64748b;
    }
    .gate-label {
        display: block;
        font-size: 1.25rem;
        font-weight: 800;
        color: #334155;
        margin-bottom: 10px;
        letter-spacing: 0.02em;
    }
    .dark .gate-label { color: #cbd5e1; }
    .gate-input {
        width: 100%;
        padding: 18px 20px;
        font-size: 1.35rem;
        border: 2px solid #cbd5e1;
        border-radius: 18px;
        background: #fff;
        color: #0f172a;
        transition: border-color .25s, box-shadow .25s;
        box-sizing: border-box;
    }
    .dark .gate-input {
        background: #0f172a;
        border-color: rgba(255,255,255,0.15);
        color: #f1f5f9;
    }
    .gate-input:focus {
        outline: none;
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79,70,229,0.15);
    }
    .gate-hint {
        margin-top: 12px;
        font-size: 1.15rem;
        color: #64748b;
        line-height: 1.6;
    }
    .dark .gate-hint { color: #94a3b8; }
    .gate-hint code {
        background: #eef2ff;
        color: #4338ca;
        padding: 2px 8px;
        border-radius: 8px;
        font-weight: 700;
    }
    .dark .gate-hint code { background: rgba(99,102,241,0.15); color: #a5b4fc; }
    .gate-btn {
        width: 100%;
        margin-top: 26px;
        padding: 18px 28px;
        border: none;
        border-radius: 18px;
        background: linear-gradient(135deg, #2563eb, #4f46e5);
        color: #fff;
        font-size: 1.35rem;
        font-weight: 900;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        transition: transform .25s, box-shadow .25s, opacity .25s;
        box-shadow: 0 14px 30px rgba(37,99,235,0.28);
    }
    .gate-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 18px 38px rgba(37,99,235,0.35);
    }
    .gate-btn:disabled { opacity: .55; cursor: not-allowed; box-shadow: none; }
    .gate-alert {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px 20px;
        border-radius: 16px;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        font-size: 1.2rem;
        line-height: 1.6;
        margin-bottom: 24px;
    }
    .dark .gate-alert {
        background: rgba(239,68,68,0.12);
        border-color: rgba(239,68,68,0.3);
        color: #fca5a5;
    }
    .gate-privacy {
        margin-top: 24px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 1.1rem;
        color: #64748b;
        line-height: 1.6;
    }
    .dark .gate-privacy { color: #94a3b8; }
    .gate-back {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-top: 26px;
        font-size: 1.2rem;
        font-weight: 700;
        color: #4f46e5;
        text-decoration: none;
    }
    .gate-back:hover { text-decoration: underline; }
    @media (max-width: 640px) {
        .gate-head { padding: 34px 24px 24px; }
        .gate-head h1 { font-size: 1.9rem; }
        .gate-body { padding: 28px 24px 34px; }
    }
</style>

<main>
    <div class="gate-wrap">
        <div class="gate-card">
            <div class="gate-head">
                <div class="gate-lock">
                    <span class="material-symbols-outlined">lock_person</span>
                </div>
                <h1>Student Verification Required</h1>
                <p>The VVU digital collection is reserved for members of the university. Enter your university email address to continue.</p>
            </div>

            <div class="gate-body">
                <div class="gate-resource">
                    <span class="material-symbols-outlined">library_books</span>
                    <div>
                        <strong><?php echo htmlspecialchars($resource['label']); ?></strong>
                        <span><?php echo htmlspecialchars($resource['description']); ?></span>
                    </div>
                </div>

                <?php if ($error !== ''): ?>
                    <div class="gate-alert">
                        <span class="material-symbols-outlined" style="font-size:1.4rem;">error</span>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?php echo htmlspecialchars(vvu_ebook_gate_url($resource_key)); ?>" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">

                    <label class="gate-label" for="student_email">University Email Address</label>
                    <input class="gate-input"
                           type="email"
                           id="student_email"
                           name="student_email"
                           inputmode="email"
                           autocomplete="email"
                           autocapitalize="off"
                           spellcheck="false"
                           placeholder="yourname@st.vvu.edu.gh"
                           value="<?php echo htmlspecialchars($submitted_email); ?>"
                           <?php echo $lockout > 0 ? 'disabled' : 'required autofocus'; ?>>

                    <p class="gate-hint">
                        Accepted domains: <code>@st.vvu.edu.gh</code> (students) and <code>@vvu.edu.gh</code> (staff &amp; faculty).
                    </p>

                    <button type="submit" class="gate-btn" <?php echo $lockout > 0 ? 'disabled' : ''; ?>>
                        <span class="material-symbols-outlined">verified_user</span>
                        <?php echo $lockout > 0 ? 'Temporarily Locked' : 'Verify &amp; Continue'; ?>
                    </button>
                </form>

                <div class="gate-privacy">
                    <span class="material-symbols-outlined" style="font-size:1.3rem;">shield</span>
                    <span>Your address is used only to confirm you belong to Valley View University and to keep a library access record. Verification lasts for 12 hours on this device.</span>
                </div>

                <a href="digital_books.php" class="gate-back">
                    <span class="material-symbols-outlined" style="font-size:1.3rem;">arrow_back</span>
                    Back to Digital Books
                </a>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
