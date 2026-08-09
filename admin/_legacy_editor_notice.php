<?php
/**
 * Warning banner for admin sections that write to legacy tables no public page
 * reads any more. Saving there reports success but changes nothing on the site.
 *
 * Set these before including:
 *   $legacy_page_name  - the page being edited, e.g. "The Campus"
 *   $legacy_target_url - the admin editor that actually works
 *   $legacy_public_url - the public page, for a "View page" link (optional)
 */
$legacy_page_name  = $legacy_page_name  ?? 'this page';
$legacy_target_url = $legacy_target_url ?? '#';
$legacy_public_url = $legacy_public_url ?? '';
?>
<div style="background:#fff7ed;border:2px solid #f97316;border-left-width:6px;border-radius:14px;padding:22px 26px;margin-bottom:28px;">
    <div style="display:flex;align-items:flex-start;gap:14px;">
        <div style="flex-shrink:0;width:44px;height:44px;border-radius:12px;background:#f97316;color:#fff;display:flex;align-items:center;justify-content:center;font-size:20px;">
            <i class="fas fa-triangle-exclamation"></i>
        </div>
        <div style="flex-grow:1;">
            <h5 style="margin:0 0 8px;font-weight:800;color:#9a3412;">
                Edits made here do <u>not</u> appear on the website
            </h5>
            <p style="margin:0 0 14px;color:#7c2d12;line-height:1.6;">
                This section still saves into an older set of database tables that
                <strong><?php echo htmlspecialchars($legacy_page_name); ?></strong>
                no longer reads. Saving shows a success message, but the public page is unchanged —
                which is why an uploaded image appears to do nothing.
                Use the editor below instead; it updates the tables the live page really uses.
            </p>
            <a href="<?php echo htmlspecialchars($legacy_target_url); ?>"
               style="display:inline-block;background:#ea580c;color:#fff;padding:11px 22px;border-radius:9px;text-decoration:none;font-weight:700;">
                <i class="fas fa-arrow-right me-2"></i>Open the working editor for <?php echo htmlspecialchars($legacy_page_name); ?>
            </a>
            <?php if ($legacy_public_url): ?>
            <a href="../<?php echo htmlspecialchars($legacy_public_url); ?>" target="_blank" rel="noopener"
               style="display:inline-block;margin-left:10px;color:#9a3412;font-weight:700;text-decoration:underline;padding:11px 0;">
                View the live page
            </a>
            <?php endif; ?>
            <p style="margin:14px 0 0;font-size:13px;color:#9a3412;opacity:.85;">
                The fields below are kept only so you can copy any wording you still need.
            </p>
        </div>
    </div>
</div>
