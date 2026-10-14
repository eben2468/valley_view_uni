<?php
/**
 * VVU Scholar — researcher sign-in.
 */

require_once __DIR__ . '/includes/research_account.php';

$ready = ra_ready($pdo);
if ($ready && ra_current($pdo)) {
    header('Location: account.php');
    exit;
}

$error = '';
$email = '';
if ($ready && $_SERVER['REQUEST_METHOD'] === 'POST') {
    ra_check_csrf();
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    list($acct, $error) = ra_attempt_login($pdo, $email, (string) ($_POST['password'] ?? ''));
    if ($acct) {
        ra_login_session($acct);
        ra_log($pdo, (int) $acct['id'], (int) $acct['scholar_id'], 'signed_in');
        $next = ra_safe_next($_SESSION['ra_after_login'] ?? '');
        unset($_SESSION['ra_after_login']);
        header('Location: ' . $next);
        exit;
    }
}

ra_page_open($pdo, 'Researcher Sign In', 'Manage your research profile and publications.',
    ['Research Portal' => 'index.php', 'Sign in' => null]);
?>
<section class="vvus__band">
    <div class="vvus__wrap ra-wrap ra-wrap--narrow">
        <?php if (!$ready) { ra_not_ready_notice(); } ?>
        <?php ra_alerts($error); ?>

        <div class="vvus-card ra-card">
            <div class="ra-card__body">
                <h2 class="ra-h2">Sign in</h2>
                <form method="post" action="login.php" class="ra-form">
                    <?php echo vvu_csrf_field(); ?>
                    <div class="vvus-field">
                        <label for="ra-email">VVU email</label>
                        <input id="ra-email" class="vvus-input browser-default" type="email" name="email" required autocomplete="email" autofocus
                               value="<?php echo r_e($email); ?>">
                    </div>
                    <div class="vvus-field">
                        <label for="ra-pass">Password</label>
                        <input id="ra-pass" class="vvus-input browser-default" type="password" name="password" required autocomplete="current-password">
                    </div>
                    <div class="ra-actions ra-actions--split">
                        <button class="vvus-btn vvus-btn--gold" type="submit"><i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i> Sign in</button>
                        <a class="ra-link" href="forgot.php">Forgot your password?</a>
                    </div>
                </form>
            </div>
        </div>
        <p class="ra-foot">New here? <a href="claim.php">Claim your profile</a></p>
    </div>
</section>
<?php ra_page_close(); ?>
