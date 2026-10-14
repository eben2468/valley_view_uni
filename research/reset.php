<?php
/**
 * VVU Scholar — choose a new password from a reset link
 * (emailed by forgot.php, or issued by the Research Office in the admin).
 */

require_once __DIR__ . '/includes/research_account.php';

$ready = ra_ready($pdo);
$token = (string) ($_GET['token'] ?? $_POST['token'] ?? '');
$acct  = $ready ? ra_account_for_token($pdo, $token) : null;
$error = '';

if ($acct && $_SERVER['REQUEST_METHOD'] === 'POST') {
    ra_check_csrf();
    $pass  = (string) ($_POST['password'] ?? '');
    $error = ra_password_problem($pass, (string) ($_POST['password_confirm'] ?? ''));
    if ($error === '') {
        $pdo->prepare(
            "UPDATE research_accounts
                SET password_hash = ?, reset_token_hash = NULL, reset_expires_at = NULL,
                    failed_logins = 0, locked_until = NULL
              WHERE id = ?"
        )->execute([password_hash($pass, PASSWORD_DEFAULT), $acct['id']]);
        ra_log($pdo, (int) $acct['id'], (int) $acct['scholar_id'], 'password_reset');
        ra_flash('ok', 'Your password has been changed. Sign in with your new password.');
        header('Location: login.php');
        exit;
    }
}

ra_page_open($pdo, 'Choose a New Password', '', ['Research Portal' => 'index.php', 'Sign in' => 'login.php', 'New password' => null]);
?>
<section class="vvus__band">
    <div class="vvus__wrap ra-wrap ra-wrap--narrow">
        <?php if (!$ready) { ra_not_ready_notice(); } ?>
        <?php ra_alerts($error); ?>

        <div class="vvus-card ra-card">
            <div class="ra-card__body">
                <?php if (!$acct): ?>
                    <h2 class="ra-h2">This link has expired</h2>
                    <p class="ra-muted">Reset links work once and only for a limited time. Ask for a new one.</p>
                    <div class="ra-actions"><a class="vvus-btn vvus-btn--gold" href="forgot.php">Get a new link</a></div>
                <?php else: ?>
                    <h2 class="ra-h2">New password for <?php echo r_e($acct['email']); ?></h2>
                    <form method="post" action="reset.php" class="ra-form">
                        <?php echo vvu_csrf_field(); ?>
                        <input type="hidden" name="token" value="<?php echo r_e($token); ?>">
                        <div class="vvus-field">
                            <label for="ra-pass">New password</label>
                            <input id="ra-pass" class="vvus-input browser-default" type="password" name="password" required autocomplete="new-password" autofocus>
                            <small class="ra-hint">At least <?php echo RA_MIN_PASSWORD; ?> characters, with a letter and a number.</small>
                        </div>
                        <div class="vvus-field">
                            <label for="ra-pass2">Confirm new password</label>
                            <input id="ra-pass2" class="vvus-input browser-default" type="password" name="password_confirm" required autocomplete="new-password">
                        </div>
                        <div class="ra-actions"><button class="vvus-btn vvus-btn--gold" type="submit">Save new password</button></div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php ra_page_close(); ?>
