<?php
/**
 * VVU Scholar — the researcher's own dashboard.
 *
 *   account.php                    overview (or claim status while pending)
 *   account.php?tab=profile        edit profile, photo and links
 *   account.php?tab=publications   my publications — remove what isn't mine
 *   account.php?tab=add            add a paper (DOI look-up or by hand)
 *   account.php?tab=import         import from ORCID or Google Scholar (BibTeX)
 *   account.php?tab=password       change password
 *
 * Only an account whose claim the Research Office approved gets past the
 * overview; everything is scoped to that account's own scholar profile.
 */

require_once __DIR__ . '/includes/research_account.php';
require_once __DIR__ . '/../includes/upload_helper.php';
require_once __DIR__ . '/../admin/includes/research_import.php';

if (!ra_ready($pdo)) {
    ra_page_open($pdo, 'My Research');
    echo '<section class="vvus__band"><div class="vvus__wrap ra-wrap">';
    ra_not_ready_notice();
    echo '</div></section>';
    ra_page_close();
    exit;
}

$acct   = ra_require($pdo, false);
$active = $acct['status'] === 'active' && (int) $acct['scholar_id'] > 0;
$tabs   = [
    'overview'     => ['Overview',          'fa-gauge-high'],
    'profile'      => ['Profile',           'fa-id-card'],
    'publications' => ['My Publications',   'fa-book-open'],
    'add'          => ['Add Publication',   'fa-circle-plus'],
    'import'       => ['Import',            'fa-cloud-arrow-down'],
    'password'     => ['Password',          'fa-key'],
];
$tab = $_GET['tab'] ?? 'overview';
if (!isset($tabs[$tab]) || (!$active && $tab !== 'password')) {
    $tab = 'overview';
}

$error = '';
$sid   = (int) $acct['scholar_id'];
$aid   = (int) $acct['id'];

/** The researcher's scholar row (with unit name). */
function ra_my_scholar(PDO $pdo, $sid)
{
    $stmt = $pdo->prepare(
        "SELECT s.*, u.name AS unit_name FROM research_scholars s
           LEFT JOIN research_units u ON u.id = s.unit_id WHERE s.id = ?"
    );
    $stmt->execute([(int) $sid]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

/** Is this publication one of mine (lead author or linked co-author)? */
function ra_is_mine(PDO $pdo, $pubId, $sid)
{
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM research_publications p
          WHERE p.id = :p AND (p.scholar_id = :s
                OR EXISTS (SELECT 1 FROM research_publication_authors a WHERE a.publication_id = p.id AND a.scholar_id = :s2))"
    );
    $stmt->execute([':p' => (int) $pubId, ':s' => (int) $sid, ':s2' => (int) $sid]);
    return (int) $stmt->fetchColumn() > 0;
}

/** Links a publication to the researcher (and makes them lead when it has none). */
function ra_attach(PDO $pdo, $pubId, $sid)
{
    // The researcher is saying it IS theirs: lift any earlier "Not mine".
    try {
        $pdo->prepare("DELETE FROM research_author_exclusions WHERE publication_id = ? AND scholar_id = ?")->execute([(int) $pubId, (int) $sid]);
    } catch (Exception $e) { /* table not migrated yet */ }
    $pdo->prepare("INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) VALUES (?,?,0)")
        ->execute([(int) $pubId, (int) $sid]);
    $pdo->prepare("UPDATE research_publications SET scholar_id = ? WHERE id = ? AND scholar_id IS NULL")
        ->execute([(int) $sid, (int) $pubId]);
}

/** Cleans a normalised publication array from a form or an importer. */
function ra_clean_pub(array $p)
{
    $types = array_keys(r_publication_types());
    $year  = (int) ($p['pub_year'] ?? 0);
    $doi   = trim((string) ($p['doi'] ?? ''));
    $doi   = preg_replace('#^https?://(dx\.)?doi\.org/#i', '', $doi);
    return [
        'title'          => mb_substr(trim(strip_tags((string) ($p['title'] ?? ''))), 0, 600),
        'authors'        => mb_substr(trim(strip_tags((string) ($p['authors'] ?? ''))), 0, 1200),
        'pub_type'       => in_array($p['pub_type'] ?? '', $types, true) ? $p['pub_type'] : 'journal',
        'venue'          => mb_substr(trim(strip_tags((string) ($p['venue'] ?? ''))), 0, 400),
        'publisher'      => mb_substr(trim(strip_tags((string) ($p['publisher'] ?? ''))), 0, 300),
        'pub_year'       => ($year >= 1950 && $year <= (int) date('Y') + 1) ? $year : null,
        'volume'         => mb_substr(trim((string) ($p['volume'] ?? '')), 0, 40),
        'issue'          => mb_substr(trim((string) ($p['issue'] ?? '')), 0, 40),
        'pages'          => mb_substr(trim((string) ($p['pages'] ?? '')), 0, 60),
        'doi'            => mb_substr($doi, 0, 200),
        'url'            => ra_clean_url($p['url'] ?? ''),
        'abstract'       => mb_substr(trim(strip_tags((string) ($p['abstract'] ?? ''))), 0, 8000),
        'keywords'       => mb_substr(trim(strip_tags((string) ($p['keywords'] ?? ''))), 0, 600),
        'citations'      => max(0, (int) ($p['citations'] ?? 0)),
        'is_open_access' => !empty($p['is_open_access']) ? 1 : 0,
        'source'         => in_array($p['source'] ?? '', ['manual', 'crossref', 'orcid', 'bibtex', 'csv'], true) ? $p['source'] : 'manual',
        'external_id'    => mb_substr(trim((string) ($p['external_id'] ?? '')), 0, 200),
    ];
}

/**
 * Adds a publication for the researcher, or links them to the existing record
 * when it is already in the catalogue. Returns 'added' | 'linked' | 'hidden'.
 */
function ra_add_publication(PDO $pdo, array $p, array $scholar, $aid)
{
    $dup = vvur_find_duplicate($pdo, $p);
    if ($dup) {
        $st = $pdo->prepare("SELECT is_active FROM research_publications WHERE id = ?");
        $st->execute([$dup]);
        if (!(int) $st->fetchColumn()) {
            return 'hidden'; // withdrawn by the Research Office (e.g. a duplicate copy)
        }
        ra_attach($pdo, $dup, $scholar['id']);
        return 'linked';
    }
    $pdo->prepare(
        "INSERT INTO research_publications
            (title, authors, scholar_id, unit_id, pub_type, venue, publisher, pub_year, volume, issue, pages,
             doi, url, abstract, keywords, citations, is_open_access, is_active, source, external_id, added_by_account)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1,?,?,?)"
    )->execute([
        $p['title'], $p['authors'] ?: $scholar['full_name'], $scholar['id'], $scholar['unit_id'] ?: null,
        $p['pub_type'], $p['venue'], $p['publisher'], $p['pub_year'], $p['volume'], $p['issue'], $p['pages'],
        $p['doi'] ?: null, $p['url'] ?: null, $p['abstract'], $p['keywords'], $p['citations'], $p['is_open_access'],
        $p['source'], $p['external_id'] ?: null, $aid,
    ]);
    ra_attach($pdo, (int) $pdo->lastInsertId(), $scholar['id']);
    return 'added';
}

/* ======================================================================
   POST actions
   ====================================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ra_check_csrf();
    $action  = $_POST['action'] ?? '';
    $scholar = $active ? ra_my_scholar($pdo, $sid) : null;
    $back    = 'account.php?tab=' . $tab;

    try {
        /* ---------------------------------------------- password (any status) */
        if ($action === 'change_password') {
            if (!password_verify((string) ($_POST['current'] ?? ''), $acct['password_hash'])) {
                throw new RuntimeException('Your current password is not correct.');
            }
            $p = ra_password_problem((string) ($_POST['password'] ?? ''), (string) ($_POST['password_confirm'] ?? ''));
            if ($p !== '') { throw new RuntimeException($p); }
            $pdo->prepare("UPDATE research_accounts SET password_hash = ? WHERE id = ?")
                ->execute([password_hash($_POST['password'], PASSWORD_DEFAULT), $aid]);
            session_regenerate_id(true);
            ra_log($pdo, $aid, $sid, 'password_changed');
            ra_flash('ok', 'Your password has been changed.');
            header('Location: ' . $back); exit;
        }

        if (!$scholar) {
            throw new RuntimeException('Your profile is not active yet.');
        }

        /* ---------------------------------------------- profile */
        if ($action === 'save_profile') {
            $units  = ra_unit_options($pdo);
            $titles = ['', 'Prof.', 'Assoc. Prof.', 'Dr.', 'Rev.', 'Rev. Dr.', 'Mr.', 'Mrs.', 'Ms.', 'Miss'];
            $name   = trim((string) ($_POST['full_name'] ?? ''));
            $unitId = (int) ($_POST['unit_id'] ?? 0);
            $orcidRaw = trim((string) ($_POST['orcid'] ?? ''));
            $gsRaw    = trim((string) ($_POST['google_scholar_id'] ?? ''));
            $orcid  = ra_clean_orcid($orcidRaw);
            $gs     = ra_clean_scholar_id($gsRaw);
            $email  = trim((string) ($_POST['email'] ?? ''));

            if ($name === '') { throw new RuntimeException('Your name cannot be empty.'); }
            if ($unitId && !isset($units[$unitId])) { throw new RuntimeException('Choose your faculty or department from the list.'); }
            if ($orcidRaw !== '' && $orcid === '') { throw new RuntimeException('That ORCID iD does not look right. It has the form 0000-0002-1825-0097.'); }
            if ($gsRaw !== '' && $gs === '') { throw new RuntimeException('Paste your Google Scholar profile link (it contains “user=…”).'); }
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) { throw new RuntimeException('The public email address is not valid.'); }

            $photo = $scholar['photo'];
            if (!empty($_FILES['photo']['name'])) {
                if (($_FILES['photo']['size'] ?? 0) > 5 * 1024 * 1024) {
                    throw new RuntimeException('The photo is larger than 5 MB. Choose a smaller picture.');
                }
                $up = handleAdminFileUpload($_FILES['photo'], 'research/scholars', 'scholar_' . $sid . '_');
                if (!$up || !preg_match('/\.(jpe?g|png|webp)$/i', $up)) {
                    throw new RuntimeException('The photo could not be uploaded. Use a JPG, PNG or WebP image.');
                }
                $photo = $up;
            } elseif (!empty($_POST['remove_photo'])) {
                $photo = null;
            }

            // Interests: comma-separated keywords, de-duplicated, max 12.
            $interests = [];
            foreach (explode(',', strip_tags((string) ($_POST['interests'] ?? ''))) as $i) {
                $i = trim($i);
                if ($i !== '' && !in_array(mb_strtolower($i), array_map('mb_strtolower', $interests), true)) {
                    $interests[] = mb_substr($i, 0, 60);
                }
            }
            $interests = implode(', ', array_slice($interests, 0, 12));

            $pdo->prepare(
                "UPDATE research_scholars
                    SET title = ?, full_name = ?, position = ?, unit_id = ?, bio = ?, interests = ?,
                        email = ?, phone = ?, photo = ?, orcid = ?, google_scholar_id = ?, researchgate_url = ?,
                        scopus_id = ?, linkedin_url = ?, website = ?
                  WHERE id = ?"
            )->execute([
                in_array($_POST['title'] ?? '', $titles, true) ? ($_POST['title'] ?: null) : $scholar['title'],
                mb_substr($name, 0, 200),
                mb_substr(trim(strip_tags((string) ($_POST['position'] ?? ''))), 0, 200) ?: null,
                $unitId ?: null,
                mb_substr(trim(strip_tags((string) ($_POST['bio'] ?? ''))), 0, 5000) ?: null,
                $interests ?: null,
                $email ?: null,
                mb_substr(trim(strip_tags((string) ($_POST['phone'] ?? ''))), 0, 60) ?: null,
                $photo,
                $orcid ?: null,
                $gs ?: null,
                ra_clean_url($_POST['researchgate_url'] ?? '') ?: null,
                mb_substr(preg_replace('/[^0-9]/', '', (string) ($_POST['scopus_id'] ?? '')), 0, 60) ?: null,
                ra_clean_url($_POST['linkedin_url'] ?? '') ?: null,
                ra_clean_url($_POST['website'] ?? '') ?: null,
                $sid,
            ]);

            // Research areas
            $areaIds = array_map('intval', (array) ($_POST['areas'] ?? []));
            $valid   = $pdo->query("SELECT id FROM research_areas WHERE is_active = 1")->fetchAll(PDO::FETCH_COLUMN);
            $areaIds = array_slice(array_values(array_intersect($areaIds, array_map('intval', $valid))), 0, 6);
            $pdo->prepare("DELETE FROM research_scholar_areas WHERE scholar_id = ?")->execute([$sid]);
            $ins = $pdo->prepare("INSERT IGNORE INTO research_scholar_areas (scholar_id, area_id) VALUES (?,?)");
            foreach ($areaIds as $a) { $ins->execute([$sid, $a]); }

            ra_log($pdo, $aid, $sid, 'profile_updated');
            ra_flash('ok', 'Your profile has been saved.');
            header('Location: account.php?tab=profile'); exit;
        }

        /* ---------------------------------------------- remove a paper that isn't mine */
        elseif ($action === 'save_citations') {
            // Google Scholar "Cited by" figures for the researcher's own papers.
            $set  = $pdo->prepare("UPDATE research_publications SET cites_manual = ? WHERE id = ?");
            $best = $pdo->prepare(r_best_citations_sql());
            $saved = 0;
            foreach ((array) ($_POST['cites'] ?? []) as $pid => $val) {
                $pid = (int) $pid;
                if (!ra_is_mine($pdo, $pid, $sid)) { continue; }
                $val = trim((string) $val);
                $set->execute([$val === '' ? null : max(0, min(100000, (int) $val)), $pid]);
                // With no source at all yet, a typed figure is the count.
                $pdo->prepare("UPDATE research_publications SET citations = COALESCE(cites_manual, citations) WHERE id = ? AND cites_openalex IS NULL AND cites_crossref IS NULL AND cites_s2 IS NULL")->execute([$pid]);
                $best->execute([$pid]);
                $saved++;
            }
            r_recount_scholar($pdo, $sid);
            ra_log($pdo, $aid, $sid, 'citations_updated', $saved . ' papers');
            ra_flash('ok', 'Citation figures saved. Your totals have been recalculated.');
            header('Location: account.php?tab=publications'); exit;
        }
        elseif ($action === 'remove_pub') {
            $pubId = (int) ($_POST['pub_id'] ?? 0);
            if (!ra_is_mine($pdo, $pubId, $sid)) { throw new RuntimeException('That publication is not on your profile.'); }

            $t = $pdo->prepare("SELECT title, scholar_id FROM research_publications WHERE id = ?");
            $t->execute([$pubId]);
            $pub = $t->fetch(PDO::FETCH_ASSOC);

            $pdo->prepare("DELETE FROM research_publication_authors WHERE publication_id = ? AND scholar_id = ?")->execute([$pubId, $sid]);
            // Remembered, so the next automatic import does not link it again.
            try {
                $pdo->prepare("INSERT IGNORE INTO research_author_exclusions (publication_id, scholar_id, reason) VALUES (?,?,?)")
                    ->execute([$pubId, $sid, 'Researcher: not mine']);
            } catch (Exception $e) { /* table not migrated yet */ }
            if ((int) $pub['scholar_id'] === $sid) {
                // Hand the lead to another VVU co-author; if none is left the
                // paper has no VVU author at all and leaves the catalogue.
                $o = $pdo->prepare("SELECT scholar_id FROM research_publication_authors WHERE publication_id = ? ORDER BY author_order LIMIT 1");
                $o->execute([$pubId]);
                $other = (int) $o->fetchColumn();
                if ($other) {
                    $pdo->prepare("UPDATE research_publications SET scholar_id = ? WHERE id = ?")->execute([$other, $pubId]);
                    r_recount_scholar($pdo, $other);
                } else {
                    $pdo->prepare("UPDATE research_publications SET scholar_id = NULL, is_active = 0 WHERE id = ?")->execute([$pubId]);
                }
            }
            r_recount_scholar($pdo, $sid);
            ra_log($pdo, $aid, $sid, 'publication_removed', '#' . $pubId . ' ' . $pub['title']);
            ra_flash('ok', 'Removed from your profile: “' . mb_substr($pub['title'], 0, 90) . '”.');
            header('Location: account.php?tab=publications'); exit;
        }

        /* ---------------------------------------------- add / edit by hand */
        elseif ($action === 'save_pub') {
            $editId = (int) ($_POST['pub_id'] ?? 0);
            $p = ra_clean_pub(array_merge($_POST, ['source' => $_POST['source'] ?? 'manual', 'citations' => $_POST['citations'] ?? 0]));
            if ($p['title'] === '') { throw new RuntimeException('Enter the title of the publication.'); }
            if (!$p['pub_year']) { throw new RuntimeException('Enter the year it was published.'); }

            if ($editId) {
                $own = $pdo->prepare("SELECT id FROM research_publications WHERE id = ? AND added_by_account = ?");
                $own->execute([$editId, $aid]);
                if (!$own->fetchColumn()) { throw new RuntimeException('You can only edit publications you added yourself.'); }
                $pdo->prepare(
                    "UPDATE research_publications
                        SET title=?, authors=?, pub_type=?, venue=?, publisher=?, pub_year=?, volume=?, issue=?, pages=?,
                            doi=?, url=?, abstract=?, keywords=?, is_open_access=?
                      WHERE id = ?"
                )->execute([
                    $p['title'], $p['authors'] ?: $scholar['full_name'], $p['pub_type'], $p['venue'], $p['publisher'], $p['pub_year'],
                    $p['volume'], $p['issue'], $p['pages'], $p['doi'] ?: null, $p['url'] ?: null, $p['abstract'], $p['keywords'],
                    $p['is_open_access'], $editId,
                ]);
                ra_log($pdo, $aid, $sid, 'publication_edited', '#' . $editId . ' ' . $p['title']);
                ra_flash('ok', 'Publication updated.');
            } else {
                $res = ra_add_publication($pdo, $p, $scholar, $aid);
                if (vvur_quality_ready($pdo)) { vvur_dedupe_publications($pdo); }
                r_recount_scholar($pdo, $sid);
                ra_log($pdo, $aid, $sid, 'publication_' . $res, $p['title']);
                ra_flash($res === 'hidden' ? 'error' : 'ok', [
                    'added'  => 'Publication added to your profile.',
                    'linked' => 'That publication was already in the portal — it is now linked to your profile.',
                    'hidden' => 'That publication was withdrawn from the portal by the Research Office (for example as a duplicate copy). Contact the Office if it should be restored.',
                ][$res]);
            }
            header('Location: account.php?tab=publications'); exit;
        }

        /* ---------------------------------------------- import: fetch a preview */
        elseif ($action === 'import_fetch') {
            $source = $_POST['source'] ?? '';
            if ($source === 'orcid') {
                $orcid = ra_clean_orcid($_POST['orcid'] ?? $scholar['orcid']);
                if ($orcid === '') { throw new RuntimeException('Enter your ORCID iD first (Profile tab, or the box below).'); }
                @set_time_limit(90);
                $res = vvur_orcid_works($orcid, false, 200);
                if (!$res['ok']) { throw new RuntimeException($res['error']); }
                if (empty($scholar['orcid'])) {
                    $pdo->prepare("UPDATE research_scholars SET orcid = ? WHERE id = ?")->execute([$orcid, $sid]);
                }
                $items = $res['items'];
            } elseif ($source === 'bibtex') {
                $text = (string) ($_POST['bibtex'] ?? '');
                if (!empty($_FILES['bibfile']['tmp_name']) && is_uploaded_file($_FILES['bibfile']['tmp_name'])) {
                    $text .= "\n" . file_get_contents($_FILES['bibfile']['tmp_name'], false, null, 0, 2 * 1024 * 1024);
                }
                if (trim($text) === '') { throw new RuntimeException('Paste your BibTeX export, or choose the .bib file.'); }
                $parsed = vvur_parse_bibtex($text);
                $items  = $parsed['items'] ?? [];
                if (!$items) { throw new RuntimeException('No publications were found in that BibTeX. Export it again from Google Scholar and paste the whole text.'); }
            } else {
                throw new RuntimeException('Choose where to import from.');
            }

            $preview = [];
            foreach (array_slice($items, 0, 200) as $it) {
                $c = ra_clean_pub($it);
                if ($c['title'] === '') { continue; }
                $dup = vvur_find_duplicate($pdo, $c);
                $state = 'new';
                if ($dup) {
                    $state = ra_is_mine($pdo, $dup, $sid) ? 'mine' : 'exists';
                }
                $c['_state'] = $state;
                $preview[] = $c;
            }
            $_SESSION['ra_import'] = ['source' => $source, 'items' => $preview, 'at' => time()];
            header('Location: account.php?tab=import&preview=1'); exit;
        }

        /* ---------------------------------------------- import: save the ticked ones */
        elseif ($action === 'import_save') {
            $bag = $_SESSION['ra_import'] ?? null;
            if (!$bag || time() - (int) $bag['at'] > 3600) { throw new RuntimeException('The preview expired. Fetch your publications again.'); }
            $picked = array_map('intval', (array) ($_POST['pick'] ?? []));
            if (!$picked) { throw new RuntimeException('Tick at least one publication to import.'); }

            @set_time_limit(180);
            $counts = ['added' => 0, 'linked' => 0, 'hidden' => 0];
            $budget = 30; // Crossref look-ups for citation counts
            foreach ($picked as $i) {
                if (!isset($bag['items'][$i]) || $bag['items'][$i]['_state'] === 'mine') { continue; }
                $p = $bag['items'][$i];
                unset($p['_state']);
                if ($p['doi'] !== '' && $budget-- > 0) {
                    $cr = vvur_crossref_search(['doi' => $p['doi']]);
                    if ($cr['ok'] && $cr['items']) {
                        $rich = ra_clean_pub($cr['items'][0]);
                        foreach (['authors', 'venue', 'publisher', 'volume', 'issue', 'pages', 'abstract', 'url'] as $k) {
                            if ($p[$k] === '' && $rich[$k] !== '') { $p[$k] = $rich[$k]; }
                        }
                        $p['citations'] = max($p['citations'], $rich['citations']);
                    }
                }
                $counts[ra_add_publication($pdo, $p, $scholar, $aid)]++;
            }
            unset($_SESSION['ra_import']);
            // The same work from two sources (or twice in one export) ends up
            // as one record.
            if (vvur_quality_ready($pdo)) { vvur_dedupe_publications($pdo); }
            r_recount_scholar($pdo, $sid);
            ra_log($pdo, $aid, $sid, 'publications_imported',
                $bag['source'] . ': ' . $counts['added'] . ' added, ' . $counts['linked'] . ' linked');
            ra_flash('ok', $counts['added'] . ' added and ' . $counts['linked'] . ' linked to your profile'
                . ($counts['hidden'] ? ' (' . $counts['hidden'] . ' skipped — withdrawn by the Research Office)' : '') . '.');
            header('Location: account.php?tab=publications'); exit;
        }

        /* ---------------------------------------------- DOI look-up for the add form */
        elseif ($action === 'lookup_doi') {
            $doi = trim((string) ($_POST['doi'] ?? ''));
            if ($doi === '') { throw new RuntimeException('Enter a DOI to look up.'); }
            $cr = vvur_crossref_search(['doi' => $doi]);
            if (!$cr['ok'] || !$cr['items']) { throw new RuntimeException('No record was found for that DOI. Check it, or fill in the form by hand.'); }
            $_SESSION['ra_prefill'] = ra_clean_pub(array_merge($cr['items'][0], ['source' => 'crossref']));
            header('Location: account.php?tab=add&prefill=1'); exit;
        }
    } catch (RuntimeException $e) {
        $error = $e->getMessage();
    } catch (Exception $e) {
        error_log('VVU Scholar account: ' . $e->getMessage());
        $error = 'Something went wrong while saving. Please try again.';
    }
}

/* ======================================================================
   Data for the view
   ====================================================================== */
$scholar = $active ? ra_my_scholar($pdo, $sid) : null;
$claim   = ra_latest_claim($pdo, $aid);

$myPubs = [];
if ($active && in_array($tab, ['overview', 'publications'], true)) {
    $stmt = $pdo->prepare(
        "SELECT p.*, (p.added_by_account = :a) AS editable
           FROM research_publications p
          WHERE p.is_active = 1
            AND (p.scholar_id = :s OR p.id IN (SELECT publication_id FROM research_publication_authors WHERE scholar_id = :s2))
          ORDER BY p.pub_year DESC, p.title ASC"
    );
    $stmt->execute([':a' => $aid, ':s' => $sid, ':s2' => $sid]);
    $myPubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

ra_page_open($pdo, 'My Research', $active
    ? 'Keep your profile current and make sure every one of your publications is counted.'
    : 'Your account is waiting for the Research Office.',
    ['Research Portal' => 'index.php', 'My Research' => null]);
?>
<section class="vvus__band">
    <div class="vvus__wrap ra-wrap">
        <div class="ra-topbar">
            <div class="ra-topbar__who">
                <i class="fa-solid fa-circle-user" aria-hidden="true"></i>
                <span>Signed in as <strong><?php echo r_e($acct['email']); ?></strong></span>
            </div>
            <form method="post" action="logout.php">
                <?php echo vvu_csrf_field(); ?>
                <button class="vvus-btn vvus-btn--ghost vvus-btn--sm" type="submit"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i> Sign out</button>
            </form>
        </div>

        <?php if ($active): ?>
            <nav class="ra-tabs" aria-label="Account sections">
                <?php foreach ($tabs as $key => $meta): ?>
                    <a href="account.php?tab=<?php echo $key; ?>" class="<?php echo $tab === $key ? 'is-active' : ''; ?>"
                       <?php echo $tab === $key ? 'aria-current="page"' : ''; ?>>
                        <i class="fa-solid <?php echo $meta[1]; ?>" aria-hidden="true"></i> <?php echo r_e($meta[0]); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>

        <?php ra_alerts($error); ?>

<?php
/* ==========================================================================
   NOT YET APPROVED
   ========================================================================== */
if (!$active && $tab === 'overview'):
    $state = $acct['status'];
    $office = r_set($pdo, 'contact_email', 'research@vvu.edu.gh');
?>
        <div class="vvus-card ra-card">
            <div class="ra-card__body ra-status ra-status--<?php echo r_e($state); ?>">
                <?php if ($state === 'pending'): ?>
                    <span class="ra-status__icon"><i class="fa-solid fa-hourglass-half" aria-hidden="true"></i></span>
                    <h2 class="ra-h2">Your request is with the Research Office</h2>
                    <p>
                        <?php if ($claim && $claim['scholar_id']): ?>
                            You asked to claim the profile <strong><?php echo r_e($claim['scholar_name']); ?></strong>.
                        <?php else: ?>
                            You asked for a new researcher profile.
                        <?php endif; ?>
                        Once it is approved you can edit your profile and publications here. Approval normally takes a few working days.
                    </p>
                <?php elseif ($state === 'rejected'): ?>
                    <span class="ra-status__icon"><i class="fa-solid fa-circle-xmark" aria-hidden="true"></i></span>
                    <h2 class="ra-h2">Your request was not approved</h2>
                    <?php if ($claim && $claim['admin_note']): ?>
                        <p><strong>Note from the Research Office:</strong> <?php echo r_e($claim['admin_note']); ?></p>
                    <?php endif; ?>
                    <p>If you think this is a mistake, please contact the Research Office.</p>
                <?php else: ?>
                    <span class="ra-status__icon"><i class="fa-solid fa-ban" aria-hidden="true"></i></span>
                    <h2 class="ra-h2">Your account is not active</h2>
                    <p>Please contact the Research Office.</p>
                <?php endif; ?>
                <p class="ra-muted">Questions? Email <a href="mailto:<?php echo r_e($office); ?>"><?php echo r_e($office); ?></a>.</p>
            </div>
        </div>

<?php
/* ==========================================================================
   OVERVIEW
   ========================================================================== */
elseif ($tab === 'overview'):
    $checks = [
        'Photo'            => !empty($scholar['photo']),
        'Position'         => !empty($scholar['position']),
        'Faculty'          => !empty($scholar['unit_id']),
        'Biography'        => trim((string) $scholar['bio']) !== '',
        'Research interests' => trim((string) $scholar['interests']) !== '',
        'ORCID iD'         => !empty($scholar['orcid']),
        'Google Scholar'   => !empty($scholar['google_scholar_id']),
        'Publications'     => count($myPubs) > 0,
    ];
    $done = count(array_filter($checks));
    $pct  = (int) round($done / count($checks) * 100);
    $cites = array_sum(array_map(static function ($p) { return (int) $p['citations']; }, $myPubs));
    $h     = r_h_index(array_map(static function ($p) { return (int) $p['citations']; }, $myPubs));
    $act = $pdo->prepare("SELECT action, detail, created_at FROM research_activity WHERE account_id = ? ORDER BY id DESC LIMIT 6");
    $act->execute([$aid]);
    $recent = $act->fetchAll(PDO::FETCH_ASSOC);
    $labels = [
        'claim_submitted' => 'Claim submitted', 'claim_approved' => 'Claim approved', 'signed_in' => 'Signed in',
        'profile_updated' => 'Profile updated', 'publication_added' => 'Publication added', 'publication_linked' => 'Publication linked',
        'publication_removed' => 'Publication removed', 'publication_edited' => 'Publication edited',
        'publications_imported' => 'Publications imported', 'password_changed' => 'Password changed', 'password_reset' => 'Password reset',
    ];
?>
        <div class="ra-overview">
            <div class="vvus-card ra-card ra-me">
                <div class="ra-card__body">
                    <div class="ra-me__head">
                        <?php r_avatar($scholar, 'vvus-avatar vvus-avatar--lg'); ?>
                        <div>
                            <h2 class="ra-h2"><?php echo r_e(trim(($scholar['title'] ? $scholar['title'] . ' ' : '') . $scholar['full_name'])); ?></h2>
                            <p class="ra-muted"><?php echo r_e(implode(' · ', array_filter([$scholar['position'], $scholar['unit_name']])) ?: 'Add your position and faculty'); ?></p>
                        </div>
                    </div>
                    <div class="ra-nums">
                        <div><strong><?php echo count($myPubs); ?></strong><span>Publications</span></div>
                        <div><strong><?php echo number_format($cites); ?></strong><span>Citations</span></div>
                        <div><strong><?php echo $h; ?></strong><span>h-index</span></div>
                    </div>
                    <div class="ra-actions">
                        <a class="vvus-btn vvus-btn--gold" href="account.php?tab=profile"><i class="fa-solid fa-pen" aria-hidden="true"></i> Edit profile</a>
                        <a class="vvus-btn vvus-btn--ghost" href="scholar.php?p=<?php echo r_e(urlencode($scholar['slug'])); ?>" target="_blank" rel="noopener">
                            <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i> View public profile</a>
                    </div>
                </div>
            </div>

            <div class="vvus-card ra-card">
                <div class="ra-card__body">
                    <h3 class="ra-h3">Profile completeness <span class="ra-pct"><?php echo $pct; ?>%</span></h3>
                    <div class="ra-meter" role="progressbar" aria-valuenow="<?php echo $pct; ?>" aria-valuemin="0" aria-valuemax="100"><span style="width:<?php echo $pct; ?>%"></span></div>
                    <ul class="ra-checklist">
                        <?php foreach ($checks as $label => $ok): ?>
                            <li class="<?php echo $ok ? 'is-ok' : ''; ?>">
                                <i class="fa-solid <?php echo $ok ? 'fa-circle-check' : 'fa-circle'; ?>" aria-hidden="true"></i>
                                <?php echo r_e($label); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="vvus-card ra-card">
                <div class="ra-card__body">
                    <h3 class="ra-h3">Get all of your work counted</h3>
                    <ul class="ra-todo">
                        <li><i class="fa-solid fa-cloud-arrow-down" aria-hidden="true"></i>
                            <span><a href="account.php?tab=import">Import from ORCID or Google Scholar</a> — brings in the papers the portal is missing.</span></li>
                        <li><i class="fa-solid fa-circle-plus" aria-hidden="true"></i>
                            <span><a href="account.php?tab=add">Add a publication</a> by DOI or by hand.</span></li>
                        <li><i class="fa-solid fa-user-slash" aria-hidden="true"></i>
                            <span><a href="account.php?tab=publications">Check your list</a> and remove anything that is not yours.</span></li>
                    </ul>
                </div>
            </div>

            <div class="vvus-card ra-card">
                <div class="ra-card__body">
                    <h3 class="ra-h3">Recent activity</h3>
                    <?php if ($recent): ?>
                        <ul class="ra-activity">
                            <?php foreach ($recent as $r): ?>
                                <li>
                                    <span><?php echo r_e($labels[$r['action']] ?? ucfirst(str_replace('_', ' ', $r['action']))); ?>
                                        <?php if ($r['detail'] && $r['action'] !== 'signed_in'): ?><small class="ra-muted">— <?php echo r_e(mb_substr($r['detail'], 0, 70)); ?></small><?php endif; ?></span>
                                    <time><?php echo r_e(date('j M Y, H:i', strtotime($r['created_at']))); ?></time>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="ra-muted">Nothing yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

<?php
/* ==========================================================================
   PROFILE
   ========================================================================== */
elseif ($tab === 'profile'):
    $units   = ra_unit_options($pdo);
    $areas   = r_areas($pdo);
    $myAreas = array_map('intval', array_column(r_scholar_areas($pdo, $sid), 'id'));
    $titles  = ['', 'Prof.', 'Assoc. Prof.', 'Dr.', 'Rev.', 'Rev. Dr.', 'Mr.', 'Mrs.', 'Ms.', 'Miss'];
    $v = function ($k) use ($scholar) { return $_POST[$k] ?? ($scholar[$k] ?? ''); };
?>
        <form method="post" action="account.php?tab=profile" enctype="multipart/form-data" class="ra-form">
            <?php echo vvu_csrf_field(); ?>
            <input type="hidden" name="action" value="save_profile">

            <div class="vvus-card ra-card">
                <div class="ra-card__body">
                    <h3 class="ra-h3">About you</h3>
                    <div class="ra-photo">
                        <?php r_avatar($scholar, 'vvus-avatar vvus-avatar--lg'); ?>
                        <div class="vvus-field">
                            <label for="ra-photo">Photo</label>
                            <input id="ra-photo" type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="ra-file browser-default">
                            <small class="ra-hint">A clear, square head-and-shoulders photo works best. JPG, PNG or WebP, up to 5 MB.</small>
                            <?php if (!empty($scholar['photo'])): ?>
                                <label class="ra-check ra-check--inline"><input type="checkbox" name="remove_photo" value="1"> <span>Remove current photo</span></label>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="ra-grid">
                        <div class="vvus-field">
                            <label for="ra-title">Title</label>
                            <select id="ra-title" name="title" class="vvus-select browser-default">
                                <?php foreach ($titles as $t): ?>
                                    <option value="<?php echo r_e($t); ?>" <?php echo $v('title') === $t ? 'selected' : ''; ?>><?php echo $t === '' ? '— None —' : r_e($t); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="vvus-field">
                            <label for="ra-name">Full name</label>
                            <input id="ra-name" class="vvus-input browser-default" name="full_name" required maxlength="200" value="<?php echo r_e($v('full_name')); ?>">
                        </div>
                        <div class="vvus-field">
                            <label for="ra-pos">Position</label>
                            <input id="ra-pos" class="vvus-input browser-default" name="position" maxlength="200" placeholder="e.g. Senior Lecturer" value="<?php echo r_e($v('position')); ?>">
                        </div>
                        <div class="vvus-field">
                            <label for="ra-unit">Faculty / department</label>
                            <select id="ra-unit" name="unit_id" class="vvus-select browser-default">
                                <option value="">— Choose —</option>
                                <?php foreach ($units as $id => $label): ?>
                                    <option value="<?php echo (int) $id; ?>" <?php echo (int) $v('unit_id') === (int) $id ? 'selected' : ''; ?>><?php echo r_e($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="vvus-field ra-span2">
                            <label for="ra-bio">Biography</label>
                            <textarea id="ra-bio" class="vvus-input ra-textarea browser-default" name="bio" rows="6" maxlength="5000"
                                      placeholder="A short paragraph about your background, research and teaching."><?php echo r_e($v('bio')); ?></textarea>
                        </div>
                        <div class="vvus-field ra-span2">
                            <label for="ra-int">Research interests</label>
                            <input id="ra-int" class="vvus-input browser-default" name="interests" maxlength="600"
                                   placeholder="Separate with commas, e.g. Public health, Maternal care, Health policy" value="<?php echo r_e($v('interests')); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($areas): ?>
            <div class="vvus-card ra-card">
                <div class="ra-card__body">
                    <h3 class="ra-h3">Research areas <span class="ra-optional">choose up to 6</span></h3>
                    <div class="ra-chips">
                        <?php foreach ($areas as $a): ?>
                            <label class="ra-chip">
                                <input type="checkbox" name="areas[]" value="<?php echo (int) $a['id']; ?>" <?php echo in_array((int) $a['id'], $myAreas, true) ? 'checked' : ''; ?>>
                                <span><?php echo r_e($a['name']); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="vvus-card ra-card">
                <div class="ra-card__body">
                    <h3 class="ra-h3">Research identifiers & links</h3>
                    <div class="ra-grid">
                        <div class="vvus-field">
                            <label for="ra-orcid">ORCID iD</label>
                            <input id="ra-orcid" class="vvus-input browser-default" name="orcid" placeholder="0000-0002-1825-0097" value="<?php echo r_e($v('orcid')); ?>">
                        </div>
                        <div class="vvus-field">
                            <label for="ra-gs">Google Scholar profile</label>
                            <input id="ra-gs" class="vvus-input browser-default" name="google_scholar_id" placeholder="Paste your Scholar profile link"
                                   value="<?php echo r_e($v('google_scholar_id')); ?>">
                        </div>
                        <div class="vvus-field">
                            <label for="ra-rg">ResearchGate</label>
                            <input id="ra-rg" class="vvus-input browser-default" name="researchgate_url" placeholder="https://www.researchgate.net/profile/…" value="<?php echo r_e($v('researchgate_url')); ?>">
                        </div>
                        <div class="vvus-field">
                            <label for="ra-scopus">Scopus Author ID</label>
                            <input id="ra-scopus" class="vvus-input browser-default" name="scopus_id" inputmode="numeric" value="<?php echo r_e($v('scopus_id')); ?>">
                        </div>
                        <div class="vvus-field">
                            <label for="ra-li">LinkedIn</label>
                            <input id="ra-li" class="vvus-input browser-default" name="linkedin_url" placeholder="https://www.linkedin.com/in/…" value="<?php echo r_e($v('linkedin_url')); ?>">
                        </div>
                        <div class="vvus-field">
                            <label for="ra-web">Personal website</label>
                            <input id="ra-web" class="vvus-input browser-default" name="website" placeholder="https://" value="<?php echo r_e($v('website')); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="vvus-card ra-card">
                <div class="ra-card__body">
                    <h3 class="ra-h3">Public contact details <span class="ra-optional">shown on your profile — leave empty to hide</span></h3>
                    <div class="ra-grid">
                        <div class="vvus-field">
                            <label for="ra-pemail">Email</label>
                            <input id="ra-pemail" class="vvus-input browser-default" type="email" name="email" value="<?php echo r_e($v('email')); ?>">
                        </div>
                        <div class="vvus-field">
                            <label for="ra-phone">Phone</label>
                            <input id="ra-phone" class="vvus-input browser-default" name="phone" maxlength="60" value="<?php echo r_e($v('phone')); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="ra-actions ra-actions--sticky">
                <button class="vvus-btn vvus-btn--gold" type="submit"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Save profile</button>
                <a class="vvus-btn vvus-btn--ghost" href="scholar.php?p=<?php echo r_e(urlencode($scholar['slug'])); ?>" target="_blank" rel="noopener">View public profile</a>
            </div>
        </form>

<?php
/* ==========================================================================
   MY PUBLICATIONS
   ========================================================================== */
elseif ($tab === 'publications'):
    $types = r_publication_types();
?>
        <div class="ra-listhead">
            <p><strong><?php echo count($myPubs); ?></strong> publications on your profile.</p>
            <div class="ra-listhead__tools">
                <input type="search" class="vvus-input browser-default ra-filter" placeholder="Filter by title…" data-filter="#ra-publist" aria-label="Filter your publications">
                <a class="vvus-btn vvus-btn--gold vvus-btn--sm" href="account.php?tab=add"><i class="fa-solid fa-circle-plus" aria-hidden="true"></i> Add</a>
                <a class="vvus-btn vvus-btn--ghost vvus-btn--sm" href="account.php?tab=import"><i class="fa-solid fa-cloud-arrow-down" aria-hidden="true"></i> Import</a>
            </div>
        </div>

        <?php if (!$myPubs): ?>
            <div class="vvus-card ra-card"><div class="ra-card__body ra-empty">
                No publications are linked to your profile yet. <a href="account.php?tab=import">Import them from ORCID or Google Scholar</a>, or <a href="account.php?tab=add">add one</a>.
            </div></div>
        <?php else: ?>
            <?php $hasManual = array_key_exists('cites_manual', $myPubs[0]); ?>
            <?php if ($hasManual): ?>
                <form method="post" action="account.php?tab=publications" id="ra-cites" class="ra-citesbar">
                    <?php echo vvu_csrf_field(); ?>
                    <input type="hidden" name="action" value="save_citations">
                    <span><i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        Citations are counted automatically from OpenAlex, Crossref and Semantic Scholar. For papers those indexes miss,
                        type the <strong>“Cited by” number from your Google Scholar profile</strong> — the highest figure is shown.</span>
                    <button class="vvus-btn vvus-btn--gold vvus-btn--sm" type="submit"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Save citations</button>
                </form>
            <?php endif; ?>
            <ul class="ra-publist" id="ra-publist">
                <?php foreach ($myPubs as $p): ?>
                    <li class="vvus-card ra-pub" data-text="<?php echo r_e(mb_strtolower($p['title'] . ' ' . $p['venue'])); ?>">
                        <div class="ra-pub__main">
                            <span class="ra-pub__year"><?php echo r_e($p['pub_year'] ?: '—'); ?></span>
                            <div>
                                <a class="ra-pub__title" href="<?php echo r_e(r_publication_link($p) ?: '#'); ?>" target="_blank" rel="noopener"><?php echo r_e($p['title']); ?></a>
                                <span class="ra-pub__meta">
                                    <?php echo r_e(implode(' · ', array_filter([$types[$p['pub_type']] ?? '', $p['venue'], $p['citations'] ? $p['citations'] . ' citations' : '']))); ?>
                                </span>
                            </div>
                        </div>
                        <div class="ra-pub__tools">
                            <?php if (!empty($hasManual)): ?>
                                <label class="ra-gs" title="Google Scholar &quot;Cited by&quot;">
                                    <i class="fa-brands fa-google-scholar" aria-hidden="true"></i>
                                    <input type="number" min="0" max="100000" form="ra-cites" class="browser-default"
                                           name="cites[<?php echo (int) $p['id']; ?>]" value="<?php echo $p['cites_manual'] !== null ? (int) $p['cites_manual'] : ''; ?>"
                                           placeholder="—" aria-label="Google Scholar citations">
                                </label>
                            <?php endif; ?>
                            <?php if ((int) $p['editable']): ?>
                                <a class="vvus-btn vvus-btn--ghost vvus-btn--sm" href="account.php?tab=add&amp;edit=<?php echo (int) $p['id']; ?>">Edit</a>
                            <?php endif; ?>
                            <form method="post" action="account.php?tab=publications"
                                  onsubmit="return confirm('Remove this publication from your profile? If no other VVU author is linked, it will leave the portal.');">
                                <?php echo vvu_csrf_field(); ?>
                                <input type="hidden" name="action" value="remove_pub">
                                <input type="hidden" name="pub_id" value="<?php echo (int) $p['id']; ?>">
                                <button class="vvus-btn vvus-btn--ghost vvus-btn--sm ra-danger" type="submit" title="Not my publication">
                                    <i class="fa-solid fa-user-slash" aria-hidden="true"></i> Not mine</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

<?php
/* ==========================================================================
   ADD / EDIT PUBLICATION
   ========================================================================== */
elseif ($tab === 'add'):
    $editId = (int) ($_GET['edit'] ?? 0);
    $pf = [];
    if ($editId) {
        $st = $pdo->prepare("SELECT * FROM research_publications WHERE id = ? AND added_by_account = ?");
        $st->execute([$editId, $aid]);
        $pf = $st->fetch(PDO::FETCH_ASSOC) ?: [];
        if (!$pf) { $editId = 0; }
    } elseif (!empty($_GET['prefill']) && !empty($_SESSION['ra_prefill'])) {
        $pf = $_SESSION['ra_prefill'];
        unset($_SESSION['ra_prefill']);
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_pub') {
        $pf = array_merge($pf, $_POST);
    }
    $f = function ($k) use ($pf) { return $pf[$k] ?? ''; };
    $types = r_publication_types();
?>
        <?php if (!$editId): ?>
        <div class="vvus-card ra-card">
            <div class="ra-card__body">
                <h3 class="ra-h3">Quick add by DOI</h3>
                <p class="ra-muted">Paste the DOI and we will fill in the details for you from Crossref.</p>
                <form method="post" action="account.php?tab=add" class="ra-searchrow">
                    <?php echo vvu_csrf_field(); ?>
                    <input type="hidden" name="action" value="lookup_doi">
                    <label for="ra-doi-look" class="sr-only">DOI</label>
                    <input id="ra-doi-look" class="vvus-input browser-default" name="doi" placeholder="10.1186/s12912-020-00508-9 or https://doi.org/…" required>
                    <button class="vvus-btn vvus-btn--gold" type="submit"><i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i> Look up</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <form method="post" action="account.php?tab=add<?php echo $editId ? '&amp;edit=' . $editId : ''; ?>" class="ra-form">
            <?php echo vvu_csrf_field(); ?>
            <input type="hidden" name="action" value="save_pub">
            <input type="hidden" name="pub_id" value="<?php echo $editId; ?>">
            <input type="hidden" name="source" value="<?php echo r_e($f('source') ?: 'manual'); ?>">
            <input type="hidden" name="citations" value="<?php echo (int) $f('citations'); ?>">
            <input type="hidden" name="external_id" value="<?php echo r_e($f('external_id')); ?>">

            <div class="vvus-card ra-card">
                <div class="ra-card__body">
                    <h3 class="ra-h3"><?php echo $editId ? 'Edit publication' : (!empty($pf) ? 'Check the details and save' : 'Or enter the details'); ?></h3>
                    <div class="ra-grid">
                        <div class="vvus-field ra-span2">
                            <label for="ra-ptitle">Title</label>
                            <input id="ra-ptitle" class="vvus-input browser-default" name="title" required maxlength="600" value="<?php echo r_e($f('title')); ?>">
                        </div>
                        <div class="vvus-field ra-span2">
                            <label for="ra-pauth">Authors</label>
                            <input id="ra-pauth" class="vvus-input browser-default" name="authors" maxlength="1200" placeholder="In order, separated by commas" value="<?php echo r_e($f('authors')); ?>">
                        </div>
                        <div class="vvus-field">
                            <label for="ra-ptype">Type</label>
                            <select id="ra-ptype" name="pub_type" class="vvus-select browser-default">
                                <?php foreach ($types as $k => $label): ?>
                                    <option value="<?php echo r_e($k); ?>" <?php echo ($f('pub_type') ?: 'journal') === $k ? 'selected' : ''; ?>><?php echo r_e($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="vvus-field">
                            <label for="ra-pyear">Year</label>
                            <input id="ra-pyear" class="vvus-input browser-default" name="pub_year" type="number" min="1950" max="<?php echo (int) date('Y') + 1; ?>" required value="<?php echo r_e($f('pub_year')); ?>">
                        </div>
                        <div class="vvus-field ra-span2">
                            <label for="ra-pvenue">Journal / conference / book</label>
                            <input id="ra-pvenue" class="vvus-input browser-default" name="venue" maxlength="400" value="<?php echo r_e($f('venue')); ?>">
                        </div>
                        <div class="vvus-field">
                            <label for="ra-ppub">Publisher</label>
                            <input id="ra-ppub" class="vvus-input browser-default" name="publisher" maxlength="300" value="<?php echo r_e($f('publisher')); ?>">
                        </div>
                        <div class="ra-grid ra-grid--3">
                            <div class="vvus-field"><label for="ra-pvol">Volume</label><input id="ra-pvol" class="vvus-input browser-default" name="volume" maxlength="40" value="<?php echo r_e($f('volume')); ?>"></div>
                            <div class="vvus-field"><label for="ra-piss">Issue</label><input id="ra-piss" class="vvus-input browser-default" name="issue" maxlength="40" value="<?php echo r_e($f('issue')); ?>"></div>
                            <div class="vvus-field"><label for="ra-ppg">Pages</label><input id="ra-ppg" class="vvus-input browser-default" name="pages" maxlength="60" value="<?php echo r_e($f('pages')); ?>"></div>
                        </div>
                        <div class="vvus-field">
                            <label for="ra-pdoi">DOI</label>
                            <input id="ra-pdoi" class="vvus-input browser-default" name="doi" maxlength="200" placeholder="10.xxxx/…" value="<?php echo r_e($f('doi')); ?>">
                        </div>
                        <div class="vvus-field">
                            <label for="ra-purl">Link</label>
                            <input id="ra-purl" class="vvus-input browser-default" name="url" placeholder="https://" value="<?php echo r_e($f('url')); ?>">
                        </div>
                        <div class="vvus-field ra-span2">
                            <label for="ra-pabs">Abstract <span class="ra-optional">(optional)</span></label>
                            <textarea id="ra-pabs" class="vvus-input ra-textarea browser-default" name="abstract" rows="5"><?php echo r_e($f('abstract')); ?></textarea>
                        </div>
                        <div class="vvus-field ra-span2">
                            <label for="ra-pkw">Keywords <span class="ra-optional">(optional, comma-separated)</span></label>
                            <input id="ra-pkw" class="vvus-input browser-default" name="keywords" maxlength="600" value="<?php echo r_e($f('keywords')); ?>">
                        </div>
                    </div>
                    <label class="ra-check"><input type="checkbox" name="is_open_access" value="1" <?php echo $f('is_open_access') ? 'checked' : ''; ?>> <span>Open access (free to read)</span></label>
                    <div class="ra-actions">
                        <button class="vvus-btn vvus-btn--gold" type="submit"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> <?php echo $editId ? 'Save changes' : 'Add to my profile'; ?></button>
                        <a class="vvus-btn vvus-btn--ghost" href="account.php?tab=publications">Cancel</a>
                    </div>
                </div>
            </div>
        </form>

<?php
/* ==========================================================================
   IMPORT
   ========================================================================== */
elseif ($tab === 'import'):
    $bag = (!empty($_GET['preview']) && !empty($_SESSION['ra_import'])) ? $_SESSION['ra_import'] : null;
?>
        <?php if ($bag): ?>
            <?php
                $new  = count(array_filter($bag['items'], static function ($i) { return $i['_state'] === 'new'; }));
                $have = count(array_filter($bag['items'], static function ($i) { return $i['_state'] === 'exists'; }));
                $mine = count(array_filter($bag['items'], static function ($i) { return $i['_state'] === 'mine'; }));
            ?>
            <form method="post" action="account.php?tab=import" class="ra-form">
                <?php echo vvu_csrf_field(); ?>
                <input type="hidden" name="action" value="import_save">
                <div class="vvus-card ra-card">
                    <div class="ra-card__body">
                        <h3 class="ra-h3">Found <?php echo count($bag['items']); ?> publications</h3>
                        <p class="ra-muted">
                            <strong><?php echo $new; ?></strong> new to the portal ·
                            <strong><?php echo $have; ?></strong> already in the portal but not linked to you ·
                            <strong><?php echo $mine; ?></strong> already on your profile.
                            Tick the ones that are yours, then import.
                        </p>
                        <div class="ra-actions ra-actions--split">
                            <label class="ra-check ra-check--inline"><input type="checkbox" data-checkall="pick[]" checked> <span>Select all</span></label>
                            <button class="vvus-btn vvus-btn--gold" type="submit"><i class="fa-solid fa-file-import" aria-hidden="true"></i> Import selected</button>
                        </div>
                        <ul class="ra-importlist">
                            <?php foreach ($bag['items'] as $i => $it): ?>
                                <li class="ra-import ra-import--<?php echo r_e($it['_state']); ?>">
                                    <label>
                                        <input type="checkbox" name="pick[]" value="<?php echo (int) $i; ?>" <?php echo $it['_state'] === 'mine' ? 'disabled' : 'checked'; ?>>
                                        <span>
                                            <strong><?php echo r_e($it['title']); ?></strong>
                                            <small><?php echo r_e(implode(' · ', array_filter([$it['pub_year'], $it['venue'], $it['doi'] ? 'DOI ' . $it['doi'] : '']))); ?></small>
                                        </span>
                                    </label>
                                    <span class="ra-badge"><?php echo ['new' => 'New', 'exists' => 'In portal — will link', 'mine' => 'On your profile'][$it['_state']]; ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="ra-actions">
                            <button class="vvus-btn vvus-btn--gold" type="submit"><i class="fa-solid fa-file-import" aria-hidden="true"></i> Import selected</button>
                            <a class="vvus-btn vvus-btn--ghost" href="account.php?tab=import">Start again</a>
                        </div>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <div class="ra-import-sources">
                <div class="vvus-card ra-card">
                    <div class="ra-card__body">
                        <h3 class="ra-h3"><i class="fa-brands fa-orcid" aria-hidden="true"></i> From ORCID</h3>
                        <p class="ra-muted">Reads the public works on your ORCID record. Citation counts are added from Crossref.</p>
                        <form method="post" action="account.php?tab=import" class="ra-form">
                            <?php echo vvu_csrf_field(); ?>
                            <input type="hidden" name="action" value="import_fetch">
                            <input type="hidden" name="source" value="orcid">
                            <div class="vvus-field">
                                <label for="ra-iorcid">ORCID iD</label>
                                <input id="ra-iorcid" class="vvus-input browser-default" name="orcid" placeholder="0000-0002-1825-0097" value="<?php echo r_e($scholar['orcid'] ?? ''); ?>" required>
                            </div>
                            <div class="ra-actions"><button class="vvus-btn vvus-btn--gold" type="submit" data-busy="Reading your ORCID record…">Fetch my works</button></div>
                        </form>
                    </div>
                </div>

                <div class="vvus-card ra-card">
                    <div class="ra-card__body">
                        <h3 class="ra-h3"><i class="fa-brands fa-google-scholar" aria-hidden="true"></i> From Google Scholar</h3>
                        <ol class="ra-howto">
                            <li>Open your <a href="https://scholar.google.com/citations?view_op=list_works" target="_blank" rel="noopener">Google Scholar profile</a>.</li>
                            <li>Tick the box above your list to select all articles.</li>
                            <li>Click <strong>Export → BibTeX</strong>, copy everything, and paste it below (or save it and choose the file).</li>
                        </ol>
                        <form method="post" action="account.php?tab=import" enctype="multipart/form-data" class="ra-form">
                            <?php echo vvu_csrf_field(); ?>
                            <input type="hidden" name="action" value="import_fetch">
                            <input type="hidden" name="source" value="bibtex">
                            <div class="vvus-field">
                                <label for="ra-bib">BibTeX</label>
                                <textarea id="ra-bib" class="vvus-input ra-textarea ra-mono browser-default" name="bibtex" rows="6" placeholder="@article{…"></textarea>
                            </div>
                            <div class="vvus-field">
                                <label for="ra-bibfile">…or a .bib file</label>
                                <input id="ra-bibfile" type="file" name="bibfile" accept=".bib,.txt,text/plain" class="ra-file browser-default">
                            </div>
                            <div class="ra-actions"><button class="vvus-btn vvus-btn--gold" type="submit">Read publications</button></div>
                        </form>
                    </div>
                </div>
            </div>
            <p class="ra-foot">Nothing is added until you review the list and confirm.</p>
        <?php endif; ?>

<?php
/* ==========================================================================
   PASSWORD
   ========================================================================== */
elseif ($tab === 'password'):
?>
        <div class="vvus-card ra-card ra-card--narrow">
            <div class="ra-card__body">
                <h3 class="ra-h3">Change your password</h3>
                <form method="post" action="account.php?tab=password" class="ra-form">
                    <?php echo vvu_csrf_field(); ?>
                    <input type="hidden" name="action" value="change_password">
                    <div class="vvus-field">
                        <label for="ra-cur">Current password</label>
                        <input id="ra-cur" class="vvus-input browser-default" type="password" name="current" required autocomplete="current-password">
                    </div>
                    <div class="vvus-field">
                        <label for="ra-new">New password</label>
                        <input id="ra-new" class="vvus-input browser-default" type="password" name="password" required autocomplete="new-password">
                        <small class="ra-hint">At least <?php echo RA_MIN_PASSWORD; ?> characters, with a letter and a number.</small>
                    </div>
                    <div class="vvus-field">
                        <label for="ra-new2">Confirm new password</label>
                        <input id="ra-new2" class="vvus-input browser-default" type="password" name="password_confirm" required autocomplete="new-password">
                    </div>
                    <div class="ra-actions"><button class="vvus-btn vvus-btn--gold" type="submit">Change password</button></div>
                </form>
            </div>
        </div>
<?php endif; ?>
    </div>
</section>
<?php ra_page_close(); ?>
