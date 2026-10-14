<?php
/**
 * VVU Scholar — "Forgot your password?".
 *
 * Emails a reset link when the server can send mail. The answer is the same
 * whether or not the address has an account, so the form cannot be used to
 * find out who is registered. The Research Office can also issue a link from
 * the admin, for hosts where mail() does not deliver.
 */

require_once __DIR__ . '/includes/research_account.php';

$ready = ra_ready($pdo);
$sent  = false;
$error = '';

if ($ready && $_SERVER['REQUEST_METHOD'] === 'POST') {
    ra_check_csrf();
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));

    // One request per minute per session is plenty for a person.
    $lastAt = (int) ($_SESSION['ra_forgot_at'] ?? 0);
    if (time() - $lastAt < 60) {
        $error = 'Please wait a minute before asking for another link.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter the email address you signed up with.';
    } else {
        $_SESSION['ra_forgot_at'] = time();
        $acct = ra_find_by_email($pdo, $email);
        if ($acct && $acct['status'] !== 'suspended') {
            $link = ra_issue_reset($pdo, (int) $acct['id'], 2);
            ra_mail($pdo, $acct['email'], 'Reset your VVU Scholar password',
                "Hello " . $acct['full_name'] . ",\n\nUse this link within 2 hours to choose a new password:\n" . $link
                . "\n\nIf you did not ask for this, you can ignore this email.\n\nVVU Research Office");
            ra_log($pdo, (int) $acct['id'], (int) $acct['scholar_id'], 'reset_requested');
        }
        $sent = true;
    }
}

ra_page_open($pdo, 'Reset Your Password', '', ['Research Portal' => 'index.php', 'Sign in' => 'login.php', 'Reset password' => null]);
$office = $ready ? r_set($pdo, 'contact_email', 'research@vvu.edu.gh') : 'research@vvu.edu.gh';
?>
<section class="vvus__band">
    <div class="vvus__wrap ra-wrap ra-wrap--narrow">
        <?php if (!$ready) { ra_not_ready_notice(); } ?>
        <?php ra_alerts($error); ?>

        <div class="vvus-card ra-card">
            <div class="ra-card__body">
                <?php if ($sent): ?>
                    <h2 class="ra-h2">Check your email</h2>
                    <p>If an account exists for that address, a link to choose a new password is on its way. It works for 2 hours.</p>
                    <p class="ra-muted">Nothing arrived after a few minutes? Check your spam folder, or email
                        <a href="mailto:<?php echo r_e($office); ?>"><?php echo r_e($office); ?></a> and the Research Office will send you a reset link.</p>
                    <div class="ra-actions"><a class="vvus-btn vvus-btn--ghost" href="login.php">Back to sign in</a></div>
                <?php else: ?>
                    <h2 class="ra-h2">Forgot your password?</h2>
                    <p class="ra-muted">Enter your VVU email and we will send you a link to choose a new one.</p>
                    <form method="post" action="forgot.php" class="ra-form">
                        <?php echo vvu_csrf_field(); ?>
                        <div class="vvus-field">
                            <label for="ra-email">VVU email</label>
                            <input id="ra-email" class="vvus-input browser-default" type="email" name="email" required autocomplete="email" autofocus>
                        </div>
                        <div class="ra-actions ra-actions--split">
                            <button class="vvus-btn vvus-btn--gold" type="submit">Send reset link</button>
                            <a class="ra-link" href="login.php">Back to sign in</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php ra_page_close(); ?>
