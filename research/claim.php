<?php
/**
 * VVU Scholar — "Claim Your Profile".
 *
 *   claim.php               step 1: find your profile by name
 *   claim.php?scholar=<id>  step 2: create your account and claim that profile
 *   claim.php?step=new      step 2: not listed — ask for a new profile
 *
 * Every claim waits for the Research Office (Admin → Research Portal →
 * Researcher Accounts) before the researcher can edit anything, so nobody can
 * take over a colleague's profile by signing up first.
 */

require_once __DIR__ . '/includes/research_account.php';

$ready = ra_ready($pdo);
$acct  = $ready ? ra_current($pdo) : null;
if ($acct) {
    header('Location: account.php');
    exit;
}

$scholarId = (int) ($_GET['scholar'] ?? $_POST['scholar_id'] ?? 0);
$isNew     = ($_GET['step'] ?? $_POST['step'] ?? '') === 'new';
$q         = trim((string) ($_GET['q'] ?? ''));
$error     = '';

$scholar = null;
if ($ready && $scholarId > 0) {
    $stmt = $pdo->prepare(
        "SELECT s.*, u.name AS unit_name FROM research_scholars s
           LEFT JOIN research_units u ON u.id = s.unit_id
          WHERE s.id = ? AND s.is_active = 1"
    );
    $stmt->execute([$scholarId]);
    $scholar = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    if ($scholar && ra_owner_of($pdo, $scholarId)) {
        $error = 'This profile has already been claimed. If it is yours, please contact the Research Office.';
        $scholar = null;
    } elseif ($scholar && ra_claim_pending($pdo, $scholarId)) {
        $error = 'Someone has already asked to claim this profile and the Research Office is reviewing it. If it is yours, please contact the Research Office.';
        $scholar = null;
    }
}
$showForm = $ready && ($scholar || $isNew);
$units    = $ready ? ra_unit_options($pdo) : [];
$domains  = $ready ? ra_domains($pdo) : ['vvu.edu.gh'];

/* ---------------------------------------------------------------- submit */
if ($showForm && $_SERVER['REQUEST_METHOD'] === 'POST') {
    ra_check_csrf();

    $name     = trim((string) ($_POST['full_name'] ?? ''));
    $email    = strtolower(trim((string) ($_POST['email'] ?? '')));
    $pass     = (string) ($_POST['password'] ?? '');
    $pass2    = (string) ($_POST['password_confirm'] ?? '');
    $position = trim((string) ($_POST['position'] ?? ''));
    $unitId   = (int) ($_POST['unit_id'] ?? 0);
    $orcidRaw = trim((string) ($_POST['orcid'] ?? ''));
    $orcid    = ra_clean_orcid($orcidRaw);
    $message  = trim((string) ($_POST['message'] ?? ''));

    if (($_POST['website_url'] ?? '') !== '') {          // honeypot: humans never see it
        $error = 'Your request could not be submitted.';
    } elseif ($name === '' || mb_strlen($name) > 200) {
        $error = 'Enter your full name.';
    } elseif (!ra_email_allowed($pdo, $email)) {
        $error = 'Use your VVU email address (ending in @' . implode(' or @', $domains) . ').';
    } elseif (ra_find_by_email($pdo, $email)) {
        $error = 'An account with that email already exists. Sign in instead, or reset your password.';
    } elseif (($p = ra_password_problem($pass, $pass2)) !== '') {
        $error = $p;
    } elseif ($orcidRaw !== '' && $orcid === '') {
        $error = 'That ORCID iD does not look right. It has the form 0000-0002-1825-0097.';
    } elseif ($unitId && !isset($units[$unitId])) {
        $error = 'Choose your faculty or department from the list.';
    } elseif (empty($_POST['confirm'])) {
        $error = $scholar
            ? 'Please confirm that this profile is yours.'
            : 'Please confirm that you are a member of VVU staff.';
    } else {
        try {
            $pdo->beginTransaction();
            $pdo->prepare(
                "INSERT INTO research_accounts (email, full_name, password_hash, status) VALUES (?,?,?, 'pending')"
            )->execute([$email, $name, password_hash($pass, PASSWORD_DEFAULT)]);
            $accountId = (int) $pdo->lastInsertId();

            $pdo->prepare(
                "INSERT INTO research_claims (account_id, scholar_id, position, unit_id, orcid, message)
                 VALUES (?,?,?,?,?,?)"
            )->execute([
                $accountId, $scholar ? (int) $scholar['id'] : null,
                mb_substr($position, 0, 200), $unitId ?: null, $orcid ?: null, mb_substr($message, 0, 2000),
            ]);
            $pdo->commit();

            ra_log($pdo, $accountId, $scholar ? (int) $scholar['id'] : null, 'claim_submitted',
                $scholar ? 'Claimed profile: ' . $scholar['full_name'] : 'Asked for a new profile');

            $office = ra_office_email($pdo);
            if ($office !== '') {
                ra_mail($pdo, $office, 'New VVU Scholar profile claim: ' . $name,
                    $name . ' <' . $email . '> has ' . ($scholar ? 'claimed the profile "' . $scholar['full_name'] . '"' : 'asked for a new researcher profile')
                    . ".\n\nPosition: " . ($position ?: '—')
                    . "\nMessage: " . ($message ?: '—')
                    . "\n\nReview it in Admin → Research Portal → Researcher Accounts:\n" . ra_base_url() . "admin/manage_research.php?tab=accounts\n");
            }

            $acctRow = ra_find_by_email($pdo, $email);
            ra_login_session($acctRow);
            ra_flash('ok', 'Thank you — your request has been sent to the Research Office. You can sign in at any time to see its status.');
            header('Location: account.php');
            exit;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            error_log('VVU Scholar: claim failed — ' . $e->getMessage());
            $error = 'Your request could not be saved. Please try again in a moment.';
        }
    }
}

$results = ($ready && !$showForm) ? ra_claimable($pdo, $q) : [];

ra_page_open($pdo, 'Claim Your Profile',
    'Take charge of your research profile: keep your position, faculty and links up to date, and make sure all of your publications are counted.');
?>
<section class="vvus__band">
    <div class="vvus__wrap ra-wrap">
        <?php if (!$ready) { ra_not_ready_notice(); } ?>

        <ol class="ra-steps" aria-label="Steps">
            <li class="<?php echo !$showForm ? 'is-current' : 'is-done'; ?>"><span>1</span> Find your profile</li>
            <li class="<?php echo $showForm ? 'is-current' : ''; ?>"><span>2</span> Create your account</li>
            <li><span>3</span> Research Office approval</li>
        </ol>

        <?php ra_alerts($error); ?>

        <?php if (!$showForm): ?>
            <div class="vvus-card ra-card">
                <div class="ra-card__body">
                    <h2 class="ra-h2">Find your profile</h2>
                    <p class="ra-muted">Most VVU researchers already have a profile, created from their published work. Search for your name.</p>

                    <form method="get" action="claim.php" class="ra-searchrow">
                        <label for="ra-q" class="sr-only">Your name</label>
                        <input id="ra-q" class="vvus-input browser-default" type="search" name="q"
                               value="<?php echo r_e($q); ?>" placeholder="e.g. Kwame Mensah" minlength="2" required autofocus>
                        <button class="vvus-btn vvus-btn--gold" type="submit"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> Search</button>
                    </form>

                    <?php if ($q !== '' && mb_strlen($q) >= 2): ?>
                        <?php if ($results): ?>
                            <ul class="ra-results">
                                <?php foreach ($results as $s):
                                    $taken = (int) $s['owned'] > 0;
                                    $wait  = !$taken && (int) $s['pending'] > 0;
                                ?>
                                    <li class="ra-result">
                                        <?php r_avatar($s, 'vvus-avatar vvus-avatar--sm'); ?>
                                        <div class="ra-result__who">
                                            <a class="ra-result__name" href="scholar.php?p=<?php echo r_e(urlencode($s['slug'])); ?>" target="_blank" rel="noopener">
                                                <?php echo r_e(trim(($s['title'] ? $s['title'] . ' ' : '') . $s['full_name'])); ?>
                                            </a>
                                            <span class="ra-result__meta">
                                                <?php echo r_e($s['unit_name'] ?: ($s['position'] ?: 'Valley View University')); ?>
                                                · <?php echo (int) $s['publications_count']; ?> publications
                                            </span>
                                        </div>
                                        <?php if ($taken): ?>
                                            <span class="ra-badge">Already claimed</span>
                                        <?php elseif ($wait): ?>
                                            <span class="ra-badge">Claim under review</span>
                                        <?php else: ?>
                                            <a class="vvus-btn vvus-btn--sm" href="claim.php?scholar=<?php echo (int) $s['id']; ?>">This is me</a>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="ra-empty">No profile matches “<?php echo r_e($q); ?>”. Try another spelling or only your surname — or create a new profile below.</p>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="ra-divider"><span>or</span></div>
                    <div class="ra-notlisted">
                        <div>
                            <strong>Not listed?</strong>
                            <span class="ra-muted">Ask for a new profile — you can add your publications once it is approved.</span>
                        </div>
                        <a class="vvus-btn vvus-btn--ghost" href="claim.php?step=new">Create a new profile</a>
                    </div>
                </div>
            </div>
            <p class="ra-foot">Already have an account? <a href="login.php">Sign in</a></p>

        <?php else: ?>
            <div class="vvus-card ra-card">
                <div class="ra-card__body">
                    <?php if ($scholar): ?>
                        <div class="ra-claiming">
                            <?php r_avatar($scholar, 'vvus-avatar'); ?>
                            <div>
                                <span class="ra-muted">You are claiming</span>
                                <strong class="ra-claiming__name"><?php echo r_e($scholar['full_name']); ?></strong>
                                <span class="ra-muted"><?php echo r_e($scholar['unit_name'] ?: ($scholar['position'] ?: '')); ?></span>
                            </div>
                            <a class="ra-link" href="claim.php">Not you?</a>
                        </div>
                    <?php else: ?>
                        <h2 class="ra-h2">Request a new researcher profile</h2>
                        <p class="ra-muted">The Research Office will create your profile when it approves your request.</p>
                    <?php endif; ?>

                    <form method="post" action="claim.php<?php echo $scholar ? '?scholar=' . (int) $scholar['id'] : '?step=new'; ?>" class="ra-form" novalidate>
                        <?php echo vvu_csrf_field(); ?>
                        <input type="hidden" name="scholar_id" value="<?php echo $scholar ? (int) $scholar['id'] : 0; ?>">
                        <?php if ($isNew): ?><input type="hidden" name="step" value="new"><?php endif; ?>
                        <div class="ra-hp" aria-hidden="true">
                            <label>Leave this empty <input type="text" name="website_url" tabindex="-1" autocomplete="off"></label>
                        </div>

                        <div class="ra-grid">
                            <div class="vvus-field">
                                <label for="ra-name">Full name</label>
                                <input id="ra-name" class="vvus-input browser-default" name="full_name" required maxlength="200"
                                       value="<?php echo r_e($_POST['full_name'] ?? ($scholar['full_name'] ?? '')); ?>">
                            </div>
                            <div class="vvus-field">
                                <label for="ra-email">VVU email</label>
                                <input id="ra-email" class="vvus-input browser-default" type="email" name="email" required
                                       autocomplete="email" placeholder="you@<?php echo r_e($domains[0]); ?>"
                                       value="<?php echo r_e($_POST['email'] ?? ''); ?>">
                            </div>
                            <div class="vvus-field">
                                <label for="ra-pass">Password</label>
                                <input id="ra-pass" class="vvus-input browser-default" type="password" name="password" required
                                       autocomplete="new-password" minlength="<?php echo RA_MIN_PASSWORD; ?>">
                                <small class="ra-hint">At least <?php echo RA_MIN_PASSWORD; ?> characters, with a letter and a number.</small>
                            </div>
                            <div class="vvus-field">
                                <label for="ra-pass2">Confirm password</label>
                                <input id="ra-pass2" class="vvus-input browser-default" type="password" name="password_confirm" required autocomplete="new-password">
                            </div>
                            <div class="vvus-field">
                                <label for="ra-pos">Position</label>
                                <input id="ra-pos" class="vvus-input browser-default" name="position" maxlength="200" placeholder="e.g. Senior Lecturer"
                                       value="<?php echo r_e($_POST['position'] ?? ($scholar['position'] ?? '')); ?>">
                            </div>
                            <div class="vvus-field">
                                <label for="ra-unit">Faculty / department</label>
                                <select id="ra-unit" class="vvus-select browser-default" name="unit_id">
                                    <option value="">— Choose —</option>
                                    <?php $selUnit = (int) ($_POST['unit_id'] ?? ($scholar['unit_id'] ?? 0)); ?>
                                    <?php foreach ($units as $id => $label): ?>
                                        <option value="<?php echo (int) $id; ?>" <?php echo $selUnit === (int) $id ? 'selected' : ''; ?>><?php echo r_e($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="vvus-field ra-span2">
                                <label for="ra-orcid">ORCID iD <span class="ra-optional">(optional)</span></label>
                                <input id="ra-orcid" class="vvus-input browser-default" name="orcid" placeholder="0000-0002-1825-0097"
                                       value="<?php echo r_e($_POST['orcid'] ?? ($scholar['orcid'] ?? '')); ?>">
                            </div>
                            <div class="vvus-field ra-span2">
                                <label for="ra-msg">Message to the Research Office <span class="ra-optional">(optional)</span></label>
                                <textarea id="ra-msg" class="vvus-input ra-textarea browser-default" name="message" rows="3" maxlength="2000"
                                          placeholder="Anything that helps us confirm it is you — e.g. your staff ID or department."><?php echo r_e($_POST['message'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <label class="ra-check">
                            <input type="checkbox" name="confirm" value="1" <?php echo !empty($_POST['confirm']) ? 'checked' : ''; ?>>
                            <span><?php echo $scholar
                                ? 'I confirm that this is my research profile and that I am a member of VVU staff.'
                                : 'I confirm that I am a member of VVU staff and that I am not already listed in the portal.'; ?></span>
                        </label>

                        <div class="ra-actions">
                            <button class="vvus-btn vvus-btn--gold" type="submit"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i> Submit for approval</button>
                            <a class="vvus-btn vvus-btn--ghost" href="claim.php">Back</a>
                        </div>
                    </form>
                </div>
            </div>
            <p class="ra-foot">Already have an account? <a href="login.php">Sign in</a></p>
        <?php endif; ?>
    </div>
</section>
<?php ra_page_close(); ?>
