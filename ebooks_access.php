<?php
/**
 * E-Books — Sign-in signpost
 *
 * Both routes to the collection (the "Access E-Books Collection" button and the
 * QR code) land here. The page does not grant anything: the Drive folder is
 * shared with the st.vvu.edu.gh domain, so Google asks for a student sign-in
 * when the folder opens. This screen exists to say *which* account to use, so
 * a student already signed into a personal Gmail is not dumped on Google's
 * "You need access — Request access" page with no explanation.
 *
 * ?open=1 records the hand-off and forwards to Drive.
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

$domain      = vvu_ebook_student_domain();
$destination = $resource['url'];
$ready       = vvu_ebook_is_safe_destination($destination);

// Hand-off: log the click, then let Google take over the access decision.
if ($ready && isset($_GET['open'])) {
    vvu_ebook_log_open($pdo, $resource_key);
    header('Location: ' . $destination);
    exit;
}

$page_title  = 'Open ' . $resource['label'] . ' - Valley View University';
$active_page = 'academics';
$open_url    = vvu_ebook_gate_url($resource_key) . '&open=1';

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
        padding: 44px 44px 32px;
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
    .gate-lock .material-symbols-outlined { font-size: 2.6rem; color: #fff; }
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
    .gate-resource span { font-size: 1.2rem; color: #64748b; }

    /* Numbered "what will happen" list */
    .gate-steps {
        list-style: none;
        margin: 0 0 30px;
        padding: 0;
        counter-reset: gatestep;
    }
    .gate-steps li {
        position: relative;
        counter-increment: gatestep;
        padding: 0 0 22px 54px;
        font-size: 1.22rem;
        line-height: 1.65;
        color: #334155;
    }
    .dark .gate-steps li { color: #cbd5e1; }
    .gate-steps li:last-child { padding-bottom: 0; }
    .gate-steps li::before {
        content: counter(gatestep);
        position: absolute;
        left: 0;
        top: 0;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #eef2ff;
        color: #4338ca;
        font-weight: 900;
        font-size: 1.15rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .dark .gate-steps li::before { background: rgba(99,102,241,0.18); color: #a5b4fc; }
    .gate-steps code, .gate-hint code {
        background: #eef2ff;
        color: #4338ca;
        padding: 2px 8px;
        border-radius: 8px;
        font-weight: 700;
        white-space: nowrap;
    }
    .dark .gate-steps code, .dark .gate-hint code { background: rgba(99,102,241,0.15); color: #a5b4fc; }

    .gate-btn {
        width: 100%;
        padding: 18px 28px;
        border: none;
        border-radius: 18px;
        background: linear-gradient(135deg, #2563eb, #4f46e5);
        color: #fff !important;
        font-size: 1.35rem;
        font-weight: 900;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        text-decoration: none !important;
        transition: transform .25s, box-shadow .25s;
        box-shadow: 0 14px 30px rgba(37,99,235,0.28);
    }
    .gate-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 38px rgba(37,99,235,0.35);
    }
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
    .gate-help {
        margin-top: 26px;
        padding: 18px 22px;
        border-radius: 16px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        font-size: 1.15rem;
        line-height: 1.65;
        color: #92400e;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .dark .gate-help {
        background: rgba(245,158,11,0.12);
        border-color: rgba(245,158,11,0.3);
        color: #fcd34d;
    }
    .gate-help .material-symbols-outlined { font-size: 1.5rem; flex-shrink: 0; }
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
        .gate-head { padding: 34px 24px 26px; }
        .gate-head h1 { font-size: 1.9rem; }
        .gate-body { padding: 28px 24px 34px; }
        .gate-steps li { font-size: 1.15rem; padding-left: 48px; }
    }
</style>

<main>
    <div class="gate-wrap">
        <div class="gate-card">
            <div class="gate-head">
                <div class="gate-lock">
                    <span class="material-symbols-outlined">school</span>
                </div>
                <h1>Sign in with your student email</h1>
                <p>This collection is shared only with Valley View University student accounts on <strong><?php echo htmlspecialchars('@' . $domain); ?></strong>.</p>
            </div>

            <div class="gate-body">
                <div class="gate-resource">
                    <span class="material-symbols-outlined">library_books</span>
                    <div>
                        <strong><?php echo htmlspecialchars($resource['label']); ?></strong>
                        <span><?php echo htmlspecialchars($resource['description']); ?></span>
                    </div>
                </div>

                <?php if (!$ready): ?>
                    <div class="gate-alert">
                        <span class="material-symbols-outlined" style="font-size:1.4rem;">error</span>
                        <span>This collection has no Google Drive link configured yet. Please contact the library.</span>
                    </div>
                <?php else: ?>
                    <ol class="gate-steps">
                        <li>Click <strong>Open in Google Drive</strong> below.</li>
                        <li>Google will ask you to sign in. Use your student address — <code><?php echo htmlspecialchars('yourname@' . $domain); ?></code> — not a personal Gmail account.</li>
                        <li>The collection opens. You can read and download any title in it.</li>
                    </ol>

                    <a href="<?php echo htmlspecialchars($open_url); ?>" class="gate-btn" rel="noopener">
                        <span class="material-symbols-outlined">open_in_new</span>
                        Open in Google Drive
                    </a>

                    <div class="gate-help">
                        <span class="material-symbols-outlined">help</span>
                        <span>
                            Seeing <strong>&ldquo;You need access&rdquo;</strong>? You are signed into the wrong Google account.
                            Click <strong>Switch accounts</strong> on that page and pick your
                            <code><?php echo htmlspecialchars('@' . $domain); ?></code> address, or open the link in a private/incognito window.
                        </span>
                    </div>
                <?php endif; ?>

                <a href="digital_books.php" class="gate-back">
                    <span class="material-symbols-outlined" style="font-size:1.3rem;">arrow_back</span>
                    Back to Digital Books
                </a>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
