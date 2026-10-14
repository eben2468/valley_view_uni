<?php
/**
 * Admin → Research Portal → Researcher Accounts.
 *
 * Where the Research Office reviews "Claim Your Profile" requests from
 * research/claim.php, manages researcher logins, issues password-reset links
 * and reads what researchers have changed. Included by
 * admin/manage_research.php for both its POST handler and its tab body.
 *
 * Schema: sql/research_accounts_migration.sql
 */

require_once __DIR__ . '/../../research/includes/research_account.php';

if (!function_exists('vvura_handle_post')) {

/** Handles every acct_* action, then redirects back to the tab. */
function vvura_handle_post(PDO $pdo)
{
    $action  = $_POST['action'] ?? '';
    $adminId = (int) ($_SESSION['admin_id'] ?? 0);

    if (!ra_ready($pdo)) {
        vvur_redirect('accounts', 'danger', 'Import sql/research_accounts_migration.sql first.');
    }

    try {
        switch ($action) {
            /* ---------------------------------------------- approve a claim */
            case 'acct_approve':
                $claim = vvura_claim($pdo, (int) ($_POST['claim_id'] ?? 0));
                if (!$claim || $claim['status'] !== 'pending') {
                    vvur_redirect('accounts', 'warning', 'That claim has already been dealt with.');
                }
                $acct = vvura_account($pdo, (int) $claim['account_id']);
                $sid  = (int) ($_POST['scholar_id'] ?? 0) ?: (int) $claim['scholar_id'];
                $note = mb_substr(trim((string) ($_POST['note'] ?? '')), 0, 500);

                $pdo->beginTransaction();
                if ($sid) {
                    $s = $pdo->prepare("SELECT * FROM research_scholars WHERE id = ?");
                    $s->execute([$sid]);
                    $scholar = $s->fetch(PDO::FETCH_ASSOC);
                    if (!$scholar) {
                        throw new RuntimeException('That researcher profile no longer exists.');
                    }
                    $owner = ra_owner_of($pdo, $sid);
                    if ($owner && $owner !== (int) $acct['id']) {
                        throw new RuntimeException('That profile is already managed by another account.');
                    }
                    // Fill gaps from the claim; never overwrite what is there.
                    $pdo->prepare(
                        "UPDATE research_scholars
                            SET position = COALESCE(NULLIF(position, ''), ?),
                                unit_id  = COALESCE(unit_id, ?),
                                orcid    = COALESCE(NULLIF(orcid, ''), ?),
                                is_active = 1
                          WHERE id = ?"
                    )->execute([$claim['position'] ?: null, $claim['unit_id'] ?: null, $claim['orcid'] ?: null, $sid]);
                } else {
                    $slug = r_unique_slug($pdo, 'research_scholars', r_slugify($acct['full_name'], 'researcher'));
                    $pdo->prepare(
                        "INSERT INTO research_scholars (full_name, slug, position, unit_id, orcid, is_active)
                         VALUES (?,?,?,?,?,1)"
                    )->execute([$acct['full_name'], $slug, $claim['position'] ?: null, $claim['unit_id'] ?: null, $claim['orcid'] ?: null]);
                    $sid = (int) $pdo->lastInsertId();
                }

                $pdo->prepare("UPDATE research_accounts SET scholar_id = ?, status = 'active' WHERE id = ?")
                    ->execute([$sid, $acct['id']]);
                $pdo->prepare(
                    "UPDATE research_claims SET status = 'approved', scholar_id = ?, admin_note = ?, reviewed_by = ?, reviewed_at = NOW() WHERE id = ?"
                )->execute([$sid, $note ?: null, $adminId ?: null, $claim['id']]);
                $pdo->commit();

                r_recount_scholar($pdo, $sid);
                ra_log($pdo, (int) $acct['id'], $sid, 'claim_approved', 'Approved by the Research Office');
                ra_mail($pdo, $acct['email'], 'Your VVU Scholar profile is ready',
                    "Hello " . $acct['full_name'] . ",\n\nThe Research Office has approved your request. You can now sign in and manage your research profile and publications:\n"
                    . ra_base_url() . "research/login.php\n" . ($note ? "\nNote: " . $note . "\n" : '') . "\nVVU Research Office");
                vvur_redirect('accounts', 'success', 'Approved: ' . $acct['full_name'] . ' can now manage their profile.');

            /* ---------------------------------------------- reject a claim */
            case 'acct_reject':
                $claim = vvura_claim($pdo, (int) ($_POST['claim_id'] ?? 0));
                if (!$claim || $claim['status'] !== 'pending') {
                    vvur_redirect('accounts', 'warning', 'That claim has already been dealt with.');
                }
                $acct = vvura_account($pdo, (int) $claim['account_id']);
                $note = mb_substr(trim((string) ($_POST['note'] ?? '')), 0, 500);
                $pdo->prepare("UPDATE research_claims SET status = 'rejected', admin_note = ?, reviewed_by = ?, reviewed_at = NOW() WHERE id = ?")
                    ->execute([$note ?: null, $adminId ?: null, $claim['id']]);
                if ($acct && $acct['status'] === 'pending') {
                    $pdo->prepare("UPDATE research_accounts SET status = 'rejected' WHERE id = ?")->execute([$acct['id']]);
                }
                ra_log($pdo, (int) $claim['account_id'], $claim['scholar_id'], 'claim_rejected', $note);
                if ($acct) {
                    ra_mail($pdo, $acct['email'], 'Your VVU Scholar profile request',
                        "Hello " . $acct['full_name'] . ",\n\nThe Research Office could not approve your profile request."
                        . ($note ? "\n\nReason: " . $note : '') . "\n\nIf you think this is a mistake, reply to this email.\n\nVVU Research Office");
                }
                vvur_redirect('accounts', 'success', 'Claim rejected.');

            /* ---------------------------------------------- account status */
            case 'acct_suspend':
            case 'acct_activate':
                $acct = vvura_account($pdo, (int) ($_POST['account_id'] ?? 0));
                if (!$acct) { vvur_redirect('accounts', 'warning', 'Account not found.'); }
                if ($action === 'acct_activate' && !(int) $acct['scholar_id']) {
                    vvur_redirect('accounts', 'warning', 'Approve a claim for this account first — it is not linked to a profile.');
                }
                $new = $action === 'acct_suspend' ? 'suspended' : 'active';
                $pdo->prepare("UPDATE research_accounts SET status = ? WHERE id = ?")->execute([$new, $acct['id']]);
                ra_log($pdo, (int) $acct['id'], (int) $acct['scholar_id'], 'account_' . $new, 'By the Research Office');
                vvur_redirect('accounts', 'success', $acct['full_name'] . ($new === 'suspended' ? ' has been suspended.' : ' has been re-activated.'));

            /* ---------------------------------------------- password reset link */
            case 'acct_reset_link':
                $acct = vvura_account($pdo, (int) ($_POST['account_id'] ?? 0));
                if (!$acct) { vvur_redirect('accounts', 'warning', 'Account not found.'); }
                $link = ra_issue_reset($pdo, (int) $acct['id'], 72);
                $sent = ra_mail($pdo, $acct['email'], 'Set a new VVU Scholar password',
                    "Hello " . $acct['full_name'] . ",\n\nUse this link within 3 days to choose a new password:\n" . $link . "\n\nVVU Research Office");
                ra_log($pdo, (int) $acct['id'], (int) $acct['scholar_id'], 'reset_link_issued', 'By the Research Office');
                $_SESSION['vvura_reset_link'] = ['name' => $acct['full_name'], 'email' => $acct['email'], 'link' => $link, 'sent' => $sent];
                vvur_redirect('accounts', 'success', 'Reset link created for ' . $acct['full_name'] . '.');

            /* ---------------------------------------------- delete an account */
            case 'acct_delete':
                $acct = vvura_account($pdo, (int) ($_POST['account_id'] ?? 0));
                if (!$acct) { vvur_redirect('accounts', 'warning', 'Account not found.'); }
                // The profile and its publications stay; only the login goes.
                $pdo->prepare("DELETE FROM research_claims WHERE account_id = ?")->execute([$acct['id']]);
                $pdo->prepare("DELETE FROM research_accounts WHERE id = ?")->execute([$acct['id']]);
                ra_log($pdo, null, (int) $acct['scholar_id'], 'account_deleted', $acct['full_name'] . ' <' . $acct['email'] . '>');
                vvur_redirect('accounts', 'success', 'Account deleted. The researcher profile itself was kept.');

            /* ---------------------------------------------- settings */
            case 'acct_settings':
                $domains = [];
                foreach (preg_split('/[\s,;]+/', strtolower((string) ($_POST['claim_email_domains'] ?? ''))) as $d) {
                    $d = ltrim(trim($d), '@');
                    if ($d !== '' && preg_match('/^[a-z0-9.-]+\.[a-z]{2,}$/', $d)) { $domains[] = $d; }
                }
                if (!$domains) { vvur_redirect('accounts', 'danger', 'Enter at least one email domain, e.g. vvu.edu.gh.'); }
                $notify = trim((string) ($_POST['claim_notify_email'] ?? ''));
                if ($notify !== '' && !filter_var($notify, FILTER_VALIDATE_EMAIL)) {
                    vvur_redirect('accounts', 'danger', 'The notification email address is not valid.');
                }
                $st = $pdo->prepare(
                    "INSERT INTO research_settings (setting_key, setting_value) VALUES (?, ?)
                     ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
                );
                $st->execute(['claim_email_domains', implode(', ', array_unique($domains))]);
                $st->execute(['claim_notify_email', $notify]);
                vvur_redirect('accounts', 'success', 'Account settings saved.');
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
        $msg = $e instanceof RuntimeException ? $e->getMessage() : 'Could not save: ' . $e->getMessage();
        vvur_redirect('accounts', 'danger', $msg);
    }
    vvur_redirect('accounts', 'warning', 'Unknown action.');
}

function vvura_claim(PDO $pdo, $id)
{
    $st = $pdo->prepare("SELECT * FROM research_claims WHERE id = ?");
    $st->execute([(int) $id]);
    return $st->fetch(PDO::FETCH_ASSOC) ?: null;
}

function vvura_account(PDO $pdo, $id)
{
    $st = $pdo->prepare("SELECT * FROM research_accounts WHERE id = ?");
    $st->execute([(int) $id]);
    return $st->fetch(PDO::FETCH_ASSOC) ?: null;
}

/** Pending claims, for the tab badge. */
function vvura_pending_count(PDO $pdo)
{
    try {
        return (int) $pdo->query("SELECT COUNT(*) FROM research_claims WHERE status = 'pending'")->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

/** The tab body. */
function vvura_render(PDO $pdo)
{
    if (!ra_ready($pdo)) {
        echo '<div class="alert alert-warning"><strong>Researcher accounts are not set up yet.</strong> '
           . 'Import <code>sql/research_accounts_migration.sql</code> in phpMyAdmin, then reload this page.</div>';
        return;
    }

    $claims = $pdo->query(
        "SELECT c.*, a.full_name AS acct_name, a.email AS acct_email,
                s.full_name AS scholar_name, s.slug AS scholar_slug, s.publications_count,
                u.name AS unit_name
           FROM research_claims c
           JOIN research_accounts a ON a.id = c.account_id
           LEFT JOIN research_scholars s ON s.id = c.scholar_id
           LEFT JOIN research_units u ON u.id = c.unit_id
          WHERE c.status = 'pending'
          ORDER BY c.created_at ASC"
    )->fetchAll(PDO::FETCH_ASSOC);

    $accounts = $pdo->query(
        "SELECT a.*, s.full_name AS scholar_name, s.slug AS scholar_slug
           FROM research_accounts a
           LEFT JOIN research_scholars s ON s.id = a.scholar_id
          ORDER BY FIELD(a.status, 'pending', 'active', 'suspended', 'rejected'), a.full_name"
    )->fetchAll(PDO::FETCH_ASSOC);

    $activity = $pdo->query(
        "SELECT l.*, a.full_name AS acct_name, s.full_name AS scholar_name, s.slug AS scholar_slug
           FROM research_activity l
           LEFT JOIN research_accounts a ON a.id = l.account_id
           LEFT JOIN research_scholars s ON s.id = l.scholar_id
          WHERE l.action <> 'signed_in'
          ORDER BY l.id DESC LIMIT 60"
    )->fetchAll(PDO::FETCH_ASSOC);

    $scholarList = $pdo->query(
        "SELECT s.id, s.full_name FROM research_scholars s
          WHERE s.is_active = 1
            AND NOT EXISTS (SELECT 1 FROM research_accounts a WHERE a.scholar_id = s.id AND a.status IN ('active','suspended'))
          ORDER BY s.full_name"
    )->fetchAll(PDO::FETCH_KEY_PAIR);

    $resetInfo = $_SESSION['vvura_reset_link'] ?? null;
    unset($_SESSION['vvura_reset_link']);

    $statusBadge = ['pending' => 'warning text-dark', 'active' => 'success', 'suspended' => 'secondary', 'rejected' => 'danger'];
    $claimUrl = '../research/claim.php';
    ?>
    <?php if ($resetInfo): ?>
        <div class="alert alert-info">
            <strong>Password reset link for <?php echo h($resetInfo['name']); ?></strong> (valid for 3 days, works once).
            <?php echo $resetInfo['sent'] ? 'It was also emailed to ' . h($resetInfo['email']) . '.' : 'Email could not be sent from this server — copy it and send it to ' . h($resetInfo['email']) . ' yourself.'; ?>
            <div class="input-group mt-2">
                <input type="text" class="form-control" readonly value="<?php echo h($resetInfo['link']); ?>" id="vvuraResetLink">
                <button class="btn btn-outline-primary" type="button"
                        onclick="var i=document.getElementById('vvuraResetLink');i.select();navigator.clipboard&&navigator.clipboard.writeText(i.value);this.textContent='Copied';">Copy</button>
            </div>
        </div>
    <?php endif; ?>

    <div class="alert alert-light border d-flex flex-wrap align-items-center gap-2">
        <i class="fas fa-circle-info text-primary"></i>
        Researchers sign up at <a href="<?php echo h($claimUrl); ?>" target="_blank"><code>research/claim.php</code></a>
        (the “Claim Your Profile” button). Nothing they submit can be edited until you approve their claim here.
    </div>

    <!-- Pending claims -->
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0"><i class="fas fa-hourglass-half me-2 text-warning"></i>Claims waiting for review
                <span class="badge bg-warning text-dark ms-1"><?php echo count($claims); ?></span></h5>
        </div>
        <div class="card-body">
            <?php if (!$claims): ?>
                <p class="text-muted mb-0">No claims are waiting. New requests appear here.</p>
            <?php endif; ?>
            <?php foreach ($claims as $c): ?>
                <div class="border rounded p-3 mb-3">
                    <div class="row g-3">
                        <div class="col-lg-5">
                            <div class="fw-bold"><?php echo h($c['acct_name']); ?></div>
                            <div><a href="mailto:<?php echo h($c['acct_email']); ?>"><?php echo h($c['acct_email']); ?></a></div>
                            <div class="small text-muted mt-1">
                                <?php echo h(implode(' · ', array_filter([$c['position'], $c['unit_name'], $c['orcid'] ? 'ORCID ' . $c['orcid'] : '']))); ?>
                            </div>
                            <div class="small text-muted">Submitted <?php echo h(date('j M Y, H:i', strtotime($c['created_at']))); ?></div>
                            <?php if (trim((string) $c['message']) !== ''): ?>
                                <div class="small bg-light rounded p-2 mt-2"><i class="fas fa-quote-left text-muted me-1"></i><?php echo nl2br(h($c['message'])); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-lg-3">
                            <div class="small text-muted text-uppercase fw-bold" style="letter-spacing:.06em">Wants to manage</div>
                            <?php if ($c['scholar_id']): ?>
                                <a href="../research/scholar.php?p=<?php echo h(urlencode($c['scholar_slug'])); ?>" target="_blank" class="fw-semibold">
                                    <?php echo h($c['scholar_name']); ?> <i class="fas fa-arrow-up-right-from-square small"></i></a>
                                <div class="small text-muted"><?php echo (int) $c['publications_count']; ?> publications</div>
                                <?php if (mb_strtolower(trim($c['scholar_name'])) !== mb_strtolower(trim($c['acct_name']))): ?>
                                    <div class="small text-danger mt-1"><i class="fas fa-triangle-exclamation"></i> Name differs from the account name — check carefully.</div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge bg-info text-dark">New profile</span>
                                <div class="small text-muted">A profile will be created on approval.</div>
                            <?php endif; ?>
                        </div>
                        <div class="col-lg-4">
                            <form method="post" class="mb-2">
                                <?php echo vvu_csrf_field(); ?>
                                <input type="hidden" name="action" value="acct_approve">
                                <input type="hidden" name="claim_id" value="<?php echo (int) $c['id']; ?>">
                                <select name="scholar_id" class="form-select form-select-sm mb-2" title="Profile to link">
                                    <option value="<?php echo (int) $c['scholar_id']; ?>">
                                        <?php echo $c['scholar_id'] ? 'Link to: ' . h($c['scholar_name']) . ' (as requested)' : 'Create a new profile (as requested)'; ?>
                                    </option>
                                    <optgroup label="…or link to an existing profile instead">
                                        <?php foreach ($scholarList as $id => $name): if ((int) $id === (int) $c['scholar_id']) { continue; } ?>
                                            <option value="<?php echo (int) $id; ?>"><?php echo h($name); ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                                <input type="text" name="note" class="form-control form-control-sm mb-2" maxlength="500" placeholder="Note to the researcher (optional)">
                                <button class="btn btn-success btn-sm w-100"><i class="fas fa-check"></i> Approve</button>
                            </form>
                            <form method="post" onsubmit="return confirm('Reject this claim?');">
                                <?php echo vvu_csrf_field(); ?>
                                <input type="hidden" name="action" value="acct_reject">
                                <input type="hidden" name="claim_id" value="<?php echo (int) $c['id']; ?>">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="note" class="form-control" maxlength="500" placeholder="Reason (shown to them)">
                                    <button class="btn btn-outline-danger"><i class="fas fa-xmark"></i> Reject</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Accounts -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-users me-2"></i>Researcher accounts <span class="badge bg-secondary ms-1"><?php echo count($accounts); ?></span></h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Researcher</th><th>Profile</th><th>Status</th><th>Last sign-in</th><th class="text-end">Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (!$accounts): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">No researcher accounts yet.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($accounts as $a): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?php echo h($a['full_name']); ?></div>
                                    <small class="text-muted"><?php echo h($a['email']); ?></small>
                                </td>
                                <td>
                                    <?php if ($a['scholar_id']): ?>
                                        <a href="../research/scholar.php?p=<?php echo h(urlencode((string) $a['scholar_slug'])); ?>" target="_blank"><?php echo h($a['scholar_name']); ?></a>
                                    <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                                </td>
                                <td><span class="badge bg-<?php echo $statusBadge[$a['status']] ?? 'secondary'; ?>"><?php echo h(ucfirst($a['status'])); ?></span></td>
                                <td class="small text-muted"><?php echo $a['last_login_at'] ? h(date('j M Y, H:i', strtotime($a['last_login_at']))) : 'Never'; ?></td>
                                <td class="text-end text-nowrap">
                                    <form method="post" class="d-inline">
                                        <?php echo vvu_csrf_field(); ?>
                                        <input type="hidden" name="account_id" value="<?php echo (int) $a['id']; ?>">
                                        <button class="btn btn-sm btn-outline-primary" name="action" value="acct_reset_link" title="Create a password reset link">
                                            <i class="fas fa-key"></i></button>
                                        <?php if ($a['status'] === 'active'): ?>
                                            <button class="btn btn-sm btn-outline-secondary" name="action" value="acct_suspend" title="Suspend"
                                                    onclick="return confirm('Suspend this account? They will not be able to sign in.');"><i class="fas fa-ban"></i></button>
                                        <?php elseif (in_array($a['status'], ['suspended'], true)): ?>
                                            <button class="btn btn-sm btn-outline-success" name="action" value="acct_activate" title="Re-activate"><i class="fas fa-rotate-left"></i></button>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-outline-danger" name="action" value="acct_delete" title="Delete account (keeps the profile)"
                                                onclick="return confirm('Delete this login? The researcher profile and its publications are kept.');"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Activity -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-clock-rotate-left me-2"></i>What researchers changed</h5></div>
                <div class="card-body p-0" style="max-height:520px;overflow:auto">
                    <table class="table table-sm mb-0">
                        <tbody>
                            <?php if (!$activity): ?>
                                <tr><td class="text-muted p-3">No activity yet.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($activity as $l): ?>
                                <tr>
                                    <td class="small text-muted text-nowrap ps-3"><?php echo h(date('j M, H:i', strtotime($l['created_at']))); ?></td>
                                    <td class="small">
                                        <strong><?php echo h($l['acct_name'] ?: ($l['scholar_name'] ?: '—')); ?></strong>
                                        <?php echo h(str_replace('_', ' ', $l['action'])); ?>
                                        <?php if ($l['detail']): ?><span class="text-muted">— <?php echo h(mb_substr($l['detail'], 0, 120)); ?></span><?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Settings -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-sliders me-2"></i>Settings</h5></div>
                <div class="card-body">
                    <form method="post">
                        <?php echo vvu_csrf_field(); ?>
                        <input type="hidden" name="action" value="acct_settings">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Who can sign up</label>
                            <input type="text" name="claim_email_domains" class="form-control" value="<?php echo h(implode(', ', ra_domains($pdo))); ?>">
                            <small class="text-muted">Email domains, comma-separated. Only these addresses can create an account.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notify about new claims</label>
                            <input type="email" name="claim_notify_email" class="form-control" value="<?php echo h(r_set($pdo, 'claim_notify_email', '')); ?>"
                                   placeholder="<?php echo h(r_set($pdo, 'contact_email', 'research@vvu.edu.gh')); ?>">
                            <small class="text-muted">Leave empty to use the portal’s contact email. Needs working email on the server.</small>
                        </div>
                        <button class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
}

} // vvura_handle_post
