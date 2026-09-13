<?php
/**
 * Admin → Research Portal (VVU Scholar)
 *
 * The single editor behind everything under research/. Ten tabs:
 *
 *   Page Content · Sections · Stat Tiles · Researchers · Publications
 *   Faculties · Research Areas · Highlights · Partners · Import Data
 *
 * The six list-shaped tabs (sections, stats, units, areas, highlights,
 * partners) are driven by one declarative spec and one generic renderer near
 * the bottom of this file, rather than six near-identical blocks of markup —
 * adding a column to any of them is a one-line change to its spec.
 *
 * Researchers and Publications have their own forms because they carry
 * relationships (research areas, co-authors) and because saving one has to
 * recount the cached citation totals the public pages rank on.
 *
 * Schema: sql/research_portal_schema.sql
 * Import: admin/includes/research_import.php
 */

include 'header.php';
include 'sidebar.php';
require_once('../includes/db_connect.php');
require_once(__DIR__ . '/../research/includes/research_helper.php');
require_once(__DIR__ . '/includes/research_import.php');

/* ==========================================================================
   Tab definitions
   ========================================================================== */

$tabs = [
    'settings'     => ['Page Content',    'fa-pen-to-square'],
    'sections'     => ['Sections',        'fa-layer-group'],
    'stats'        => ['Stat Tiles',      'fa-chart-simple'],
    'scholars'     => ['Researchers',     'fa-user-graduate'],
    'publications' => ['Publications',    'fa-book-open'],
    'units'        => ['Faculties',       'fa-building-columns'],
    'areas'        => ['Research Areas',  'fa-flask'],
    'highlights'   => ['Highlights',      'fa-award'],
    'partners'     => ['Partners',        'fa-handshake-angle'],
    'import'       => ['Import Data',     'fa-cloud-arrow-down'],
];

$tab = $_GET['tab'] ?? 'settings';
if (!isset($tabs[$tab])) {
    $tab = 'settings';
}

$colorOptions = [
    'blue' => 'Blue', 'indigo' => 'Indigo', 'purple' => 'Purple', 'green' => 'Green',
    'teal' => 'Teal', 'amber' => 'Amber', 'orange' => 'Orange', 'red' => 'Red', 'slate' => 'Slate',
];

/** Reference lists the forms below need as <select> options. */
// Faculties first, each followed by its own departments, so the selector reads
// as the structure rather than as a flat alphabetical list.
$unitOptions = [];
$unitRows = $pdo->query(
    "SELECT id, name, parent_id, display_order FROM research_units ORDER BY display_order, name"
)->fetchAll(PDO::FETCH_ASSOC);
foreach ($unitRows as $row) {
    if ($row['parent_id'] !== null) {
        continue;
    }
    $unitOptions[$row['id']] = $row['name'];
    foreach ($unitRows as $child) {
        if ((int) $child['parent_id'] === (int) $row['id']) {
            $unitOptions[$child['id']] = '— ' . $child['name'];
        }
    }
}
// Anything orphaned by a deleted parent still has to be reachable.
foreach ($unitRows as $row) {
    if (!isset($unitOptions[$row['id']])) {
        $unitOptions[$row['id']] = $row['name'];
    }
}

/** Faculties only — what a department may be nested under. */
$parentUnitOptions = [];
foreach ($unitRows as $row) {
    if ($row['parent_id'] === null) {
        $parentUnitOptions[$row['id']] = $row['name'];
    }
}
$areaOptions = [];
foreach ($pdo->query("SELECT id, name FROM research_areas ORDER BY display_order, name") as $row) {
    $areaOptions[$row['id']] = $row['name'];
}
$scholarOptions = [];
foreach ($pdo->query("SELECT id, full_name FROM research_scholars ORDER BY full_name") as $row) {
    $scholarOptions[$row['id']] = $row['full_name'];
}

/* ==========================================================================
   The six list-shaped tabs, declared once
   ========================================================================== */

$specs = [
    'sections' => [
        'table'  => 'research_sections',
        'single' => 'section',
        'order'  => 'display_order, id',
        'title'  => 'section_title',
        'fields' => [
            'section_key'         => ['label' => 'Key', 'type' => 'text', 'required' => true, 'help' => 'Used by the page template. Do not rename an existing key — the band will stop rendering.', 'col' => 4],
            'section_title'       => ['label' => 'Heading', 'type' => 'text', 'required' => true, 'col' => 8],
            'section_subtitle'    => ['label' => 'Eyebrow line', 'type' => 'text', 'col' => 12],
            'section_description' => ['label' => 'Intro paragraph', 'type' => 'textarea', 'col' => 12],
            'display_order'       => ['label' => 'Order', 'type' => 'number', 'col' => 6],
            'is_active'           => ['label' => 'Show this band on the page', 'type' => 'checkbox', 'col' => 6, 'default' => 1],
        ],
    ],
    'stats' => [
        'table'  => 'research_stats',
        'single' => 'stat tile',
        'order'  => 'display_order, id',
        'title'  => 'stat_label',
        'fields' => [
            'stat_label'    => ['label' => 'Label', 'type' => 'text', 'required' => true, 'col' => 6],
            'auto_key'      => ['label' => 'Counted from', 'type' => 'select', 'col' => 6, 'options' => [
                '' => 'A fixed value I type below',
                'scholars' => 'Researchers in the portal', 'publications' => 'Publications indexed',
                'citations' => 'Total citations', 'h_index' => 'Institutional h-index',
                'units' => 'Faculties & schools', 'departments' => 'Departments',
                'areas' => 'Research areas',
                'open_access' => 'Open access share (%)', 'this_year' => 'Published this year',
                'collaborations' => 'Partner institutions',
            ], 'help' => 'Pick a live figure and the tile counts itself. Leave on the first option to type your own number.'],
            'stat_value'    => ['label' => 'Fixed value', 'type' => 'text', 'col' => 4, 'help' => 'Ignored when a live figure is selected above.'],
            'stat_suffix'   => ['label' => 'Suffix', 'type' => 'text', 'col' => 2, 'help' => 'e.g. % or +'],
            'stat_icon'     => ['label' => 'Icon', 'type' => 'icon', 'col' => 3, 'default' => 'fa-chart-line'],
            'stat_color'    => ['label' => 'Accent', 'type' => 'color', 'col' => 3],
            'display_order' => ['label' => 'Order', 'type' => 'number', 'col' => 6],
            'is_active'     => ['label' => 'Show this tile', 'type' => 'checkbox', 'col' => 6, 'default' => 1],
        ],
    ],
    'units' => [
        'table'  => 'research_units',
        'single' => 'faculty',
        'order'  => 'display_order, name',
        'title'  => 'name',
        'slug_from' => 'name',
        'fields' => [
            'name'          => ['label' => 'Name', 'type' => 'text', 'required' => true, 'col' => 8],
            'short_name'    => ['label' => 'Short name', 'type' => 'text', 'col' => 4],
            'unit_type'     => ['label' => 'Type', 'type' => 'select', 'col' => 4, 'options' => [
                'faculty' => 'Faculty', 'school' => 'School', 'college' => 'College',
                'department' => 'Department', 'centre' => 'Centre', 'institute' => 'Institute',
            ]],
            'parent_id'     => ['label' => 'Inside which faculty?', 'type' => 'select', 'col' => 4,
                'options_var' => 'parentUnitOptions', 'blank' => '— top level —',
                'help' => 'Set this on a department. Its researchers and publications then also count towards the faculty above it, and it is listed under that faculty rather than beside it.'],
            'icon'          => ['label' => 'Icon', 'type' => 'icon', 'col' => 4, 'default' => 'fa-building-columns'],
            'color'         => ['label' => 'Accent', 'type' => 'color', 'col' => 4],
            'description'   => ['label' => 'Description', 'type' => 'textarea', 'col' => 12],
            'head_name'     => ['label' => 'Dean / Head', 'type' => 'text', 'col' => 4],
            'email'         => ['label' => 'Email', 'type' => 'text', 'col' => 4],
            'website'       => ['label' => 'Faculty page', 'type' => 'text', 'col' => 4, 'help' => 'A path on this site (faculty_of_science.php) or a full URL.'],
            'image'         => ['label' => 'Image', 'type' => 'image', 'col' => 12],
            'display_order' => ['label' => 'Order', 'type' => 'number', 'col' => 6],
            'is_active'     => ['label' => 'Active', 'type' => 'checkbox', 'col' => 6, 'default' => 1],
        ],
    ],
    'areas' => [
        'table'  => 'research_areas',
        'single' => 'research area',
        'order'  => 'display_order, name',
        'title'  => 'name',
        'slug_from' => 'name',
        'fields' => [
            'name'          => ['label' => 'Name', 'type' => 'text', 'required' => true, 'col' => 8],
            'icon'          => ['label' => 'Icon', 'type' => 'icon', 'col' => 4, 'default' => 'fa-flask'],
            'description'   => ['label' => 'Description', 'type' => 'textarea', 'col' => 12],
            'sdg_goals'     => ['label' => 'UN SDG numbers', 'type' => 'text', 'col' => 4, 'help' => 'Comma-separated, e.g. 3,4,13'],
            'color'         => ['label' => 'Accent', 'type' => 'color', 'col' => 4],
            'image'         => ['label' => 'Image', 'type' => 'image', 'col' => 4],
            'display_order' => ['label' => 'Order', 'type' => 'number', 'col' => 6],
            'is_active'     => ['label' => 'Active', 'type' => 'checkbox', 'col' => 6, 'default' => 1],
        ],
    ],
    'highlights' => [
        'table'  => 'research_highlights',
        'single' => 'highlight',
        'order'  => 'display_order, id DESC',
        'title'  => 'title',
        'fields' => [
            'title'          => ['label' => 'Title', 'type' => 'text', 'required' => true, 'col' => 8],
            'highlight_type' => ['label' => 'Type', 'type' => 'select', 'col' => 4, 'options' => [
                'grant' => 'Grant', 'award' => 'Award', 'project' => 'Project',
                'spotlight' => 'Spotlight', 'news' => 'News', 'partnership' => 'Partnership',
                'facility' => 'Facility',
            ]],
            'subtitle'      => ['label' => 'Subtitle', 'type' => 'text', 'col' => 12],
            'description'   => ['label' => 'Description', 'type' => 'textarea', 'col' => 12],
            'amount'        => ['label' => 'Amount', 'type' => 'text', 'col' => 4, 'help' => 'e.g. USD 250,000'],
            'funder'        => ['label' => 'Funder', 'type' => 'text', 'col' => 4],
            'date_label'    => ['label' => 'Date label', 'type' => 'text', 'col' => 4, 'help' => 'e.g. 2024–2027'],
            'scholar_id'    => ['label' => 'Researcher', 'type' => 'select', 'col' => 4, 'options_var' => 'scholarOptions', 'blank' => '— none —'],
            'unit_id'       => ['label' => 'Faculty', 'type' => 'select', 'col' => 4, 'options_var' => 'unitOptions', 'blank' => '— none —'],
            'link'          => ['label' => 'Link', 'type' => 'text', 'col' => 4],
            'link_text'     => ['label' => 'Link text', 'type' => 'text', 'col' => 4],
            'icon'          => ['label' => 'Icon', 'type' => 'icon', 'col' => 4, 'default' => 'fa-award'],
            'color'         => ['label' => 'Accent', 'type' => 'color', 'col' => 4],
            'image'         => ['label' => 'Image', 'type' => 'image', 'col' => 12],
            'display_order' => ['label' => 'Order', 'type' => 'number', 'col' => 6],
            'is_active'     => ['label' => 'Active', 'type' => 'checkbox', 'col' => 6, 'default' => 1],
        ],
    ],
    'partners' => [
        'table'  => 'research_partners',
        'single' => 'partner',
        'order'  => 'display_order, name',
        'title'  => 'name',
        'fields' => [
            'name'         => ['label' => 'Institution', 'type' => 'text', 'required' => true, 'col' => 8],
            'partner_type' => ['label' => 'Type', 'type' => 'select', 'col' => 4, 'options' => [
                'university' => 'University', 'funder' => 'Funder', 'industry' => 'Industry',
                'government' => 'Government', 'ngo' => 'NGO', 'network' => 'Network',
            ]],
            'country'       => ['label' => 'Country', 'type' => 'text', 'col' => 4],
            'url'           => ['label' => 'Website', 'type' => 'text', 'col' => 8],
            'description'   => ['label' => 'One-line description', 'type' => 'text', 'col' => 12],
            'logo'          => ['label' => 'Logo', 'type' => 'image', 'col' => 12],
            'display_order' => ['label' => 'Order', 'type' => 'number', 'col' => 6],
            'is_active'     => ['label' => 'Active', 'type' => 'checkbox', 'col' => 6, 'default' => 1],
        ],
    ],
];

/* ==========================================================================
   Settings tab — grouped key/value fields
   ========================================================================== */

$settingGroups = [
    'Hero' => [
        'hero_badge'       => ['label' => 'Badge', 'type' => 'text', 'col' => 4],
        'hero_title'       => ['label' => 'Headline', 'type' => 'text', 'col' => 8],
        'hero_subtitle'    => ['label' => 'Lead sentence', 'type' => 'textarea', 'col' => 12, 'rows' => 2],
        'hero_description' => ['label' => 'Supporting paragraph', 'type' => 'textarea', 'col' => 12, 'rows' => 3],
        'hero_image'       => ['label' => 'Background image', 'type' => 'image', 'col' => 8],
        'hero_search_placeholder' => ['label' => 'Search box placeholder', 'type' => 'text', 'col' => 4],
    ],
    'Metrics & display' => [
        'metrics_note'       => ['label' => 'Note under the stat tiles', 'type' => 'textarea', 'col' => 12, 'rows' => 2],
        'leaderboard_size'   => ['label' => 'Researchers on the leaderboard', 'type' => 'number', 'col' => 3],
        'featured_pub_count' => ['label' => 'Featured publications', 'type' => 'number', 'col' => 3],
        'trend_years'        => ['label' => 'Years on the trend chart', 'type' => 'number', 'col' => 3],
        'show_trend_chart'   => ['label' => 'Show the trend chart', 'type' => 'toggle', 'col' => 3],
    ],
    'Call to action' => [
        'cta_title'         => ['label' => 'Heading', 'type' => 'text', 'col' => 12],
        'cta_subtitle'      => ['label' => 'Paragraph', 'type' => 'textarea', 'col' => 12, 'rows' => 2],
        'cta_button_text'   => ['label' => 'Primary button', 'type' => 'text', 'col' => 3],
        'cta_button_link'   => ['label' => 'Primary link', 'type' => 'text', 'col' => 3],
        'cta_button_text_2' => ['label' => 'Secondary button', 'type' => 'text', 'col' => 3],
        'cta_button_link_2' => ['label' => 'Secondary link', 'type' => 'text', 'col' => 3],
        'contact_email'     => ['label' => 'Research office email', 'type' => 'text', 'col' => 6],
        'contact_phone'     => ['label' => 'Research office phone', 'type' => 'text', 'col' => 6],
    ],
    'Search engines' => [
        'meta_title'       => ['label' => 'Browser / search title', 'type' => 'text', 'col' => 12],
        'meta_description' => ['label' => 'Meta description', 'type' => 'textarea', 'col' => 12, 'rows' => 2],
    ],
];

/* ==========================================================================
   Write handlers.  Post → act → redirect, so a refresh never re-submits.
   ========================================================================== */

/** Redirect back to a tab with a flash message. */
function vvur_redirect($tab, $type, $text, $extra = [])
{
    $_SESSION['vvur_flash'] = ['type' => $type, 'text' => $text];
    $query = array_merge(['tab' => $tab], $extra);
    header('Location: manage_research.php?' . http_build_query($query));
    exit;
}

/** The POSTed value for a field, normalised for its type. */
function vvur_field_value(array $field, $name, array $existing = [])
{
    switch ($field['type']) {
        case 'checkbox':
            return isset($_POST[$name]) ? 1 : 0;

        case 'toggle':
            return isset($_POST[$name]) ? '1' : '0';

        case 'number':
            return $_POST[$name] === '' || !isset($_POST[$name]) ? 0 : (int) $_POST[$name];

        case 'image':
            // A new upload wins; otherwise keep what the hidden field carries,
            // which is either the stored path or "" when the editor cleared it.
            if (!empty($_FILES[$name]['name'])) {
                $path = handleAdminFileUpload($_FILES[$name], 'research', 'res_');
                if ($path) { return $path; }
            }
            return trim((string) ($_POST[$name . '_current'] ?? ($existing[$name] ?? '')));

        default:
            return isset($_POST[$name]) ? trim((string) $_POST[$name]) : '';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    vvu_require_csrf();
    $action = $_POST['action'] ?? '';

    try {
        switch ($action) {

            /* ---------------------------------------------------------- */
            case 'save_settings':
                $stmt = $pdo->prepare(
                    "INSERT INTO research_settings (setting_key, setting_value) VALUES (?, ?)
                     ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
                );
                $saved = 0;
                foreach ($settingGroups as $fields) {
                    foreach ($fields as $key => $field) {
                        $stmt->execute([$key, vvur_field_value($field, $key)]);
                        $saved++;
                    }
                }
                vvur_redirect('settings', 'success', "Page content saved ($saved fields).");
                break;

            /* ---------------------------------------------------------- */
            case 'save_row':
                $spec = $specs[$_POST['spec'] ?? ''] ?? null;
                if (!$spec) {
                    vvur_redirect($tab, 'danger', 'Unknown record type.');
                }
                $specKey = $_POST['spec'];
                $id      = (int) ($_POST['id'] ?? 0);

                $existing = [];
                if ($id) {
                    $get = $pdo->prepare("SELECT * FROM `{$spec['table']}` WHERE id = ?");
                    $get->execute([$id]);
                    $existing = $get->fetch(PDO::FETCH_ASSOC) ?: [];
                }

                $data = [];
                foreach ($spec['fields'] as $name => $field) {
                    $data[$name] = vvur_field_value($field, $name, $existing);
                    if (!empty($field['required']) && $data[$name] === '') {
                        vvur_redirect($specKey, 'danger', $field['label'] . ' is required.');
                    }
                    // A nullable foreign key must be NULL, not 0, or the JOIN
                    // on the public page matches a row that does not exist.
                    if (substr($name, -3) === '_id' && $data[$name] === '') {
                        $data[$name] = null;
                    }
                }

                if (!empty($spec['slug_from'])) {
                    $source = $data[$spec['slug_from']] ?? '';
                    $slug   = r_slugify($source, $specKey);
                    $data['slug'] = r_unique_slug($pdo, $spec['table'], $slug, $id);
                }

                $columns = array_keys($data);
                if ($id) {
                    $set = implode(', ', array_map(static function ($c) { return "`$c` = ?"; }, $columns));
                    $stmt = $pdo->prepare("UPDATE `{$spec['table']}` SET $set WHERE id = ?");
                    $stmt->execute(array_merge(array_values($data), [$id]));
                    $word = 'updated';
                } else {
                    $place = implode(', ', array_fill(0, count($columns), '?'));
                    $cols  = implode(', ', array_map(static function ($c) { return "`$c`"; }, $columns));
                    $stmt  = $pdo->prepare("INSERT INTO `{$spec['table']}` ($cols) VALUES ($place)");
                    $stmt->execute(array_values($data));
                    $word = 'added';
                }
                vvur_redirect($specKey, 'success', ucfirst($spec['single']) . " $word.");
                break;

            /* ---------------------------------------------------------- */
            case 'save_scholar':
                $id   = (int) ($_POST['id'] ?? 0);
                $name = trim((string) ($_POST['full_name'] ?? ''));
                if ($name === '') {
                    vvur_redirect('scholars', 'danger', 'The researcher needs a name.');
                }

                $existing = [];
                if ($id) {
                    $get = $pdo->prepare("SELECT * FROM research_scholars WHERE id = ?");
                    $get->execute([$id]);
                    $existing = $get->fetch(PDO::FETCH_ASSOC) ?: [];
                }

                $photo = '';
                if (!empty($_FILES['photo']['name'])) {
                    $photo = (string) handleAdminFileUpload($_FILES['photo'], 'research/people', 'sch_');
                }
                if ($photo === '') {
                    $photo = trim((string) ($_POST['photo_current'] ?? ''));
                }

                $slug = r_unique_slug($pdo, 'research_scholars', r_slugify($name, 'scholar'), $id);

                $fields = [
                    'full_name'         => $name,
                    'slug'              => $slug,
                    'title'             => trim((string) ($_POST['title'] ?? '')),
                    'position'          => trim((string) ($_POST['position'] ?? '')),
                    'unit_id'           => ($_POST['unit_id'] ?? '') === '' ? null : (int) $_POST['unit_id'],
                    'email'             => trim((string) ($_POST['email'] ?? '')),
                    'phone'             => trim((string) ($_POST['phone'] ?? '')),
                    'photo'             => $photo,
                    'bio'               => trim((string) ($_POST['bio'] ?? '')),
                    'interests'         => trim((string) ($_POST['interests'] ?? '')),
                    'orcid'             => trim((string) ($_POST['orcid'] ?? '')),
                    'google_scholar_id' => trim((string) ($_POST['google_scholar_id'] ?? '')),
                    'researchgate_url'  => trim((string) ($_POST['researchgate_url'] ?? '')),
                    'scopus_id'         => trim((string) ($_POST['scopus_id'] ?? '')),
                    'linkedin_url'      => trim((string) ($_POST['linkedin_url'] ?? '')),
                    'website'           => trim((string) ($_POST['website'] ?? '')),
                    'citations'         => (int) ($_POST['citations'] ?? 0),
                    'citations_5y'      => (int) ($_POST['citations_5y'] ?? 0),
                    'h_index'           => (int) ($_POST['h_index'] ?? 0),
                    'h_index_5y'        => (int) ($_POST['h_index_5y'] ?? 0),
                    'i10_index'         => (int) ($_POST['i10_index'] ?? 0),
                    'publications_count'=> (int) ($_POST['publications_count'] ?? 0),
                    'is_featured'       => isset($_POST['is_featured']) ? 1 : 0,
                    'is_active'         => isset($_POST['is_active']) ? 1 : 0,
                    'display_order'     => (int) ($_POST['display_order'] ?? 0),
                ];

                // A Scholar profile URL pasted whole is more common than the
                // bare id; keep only the id so the profile link resolves.
                if (preg_match('/[?&]user=([A-Za-z0-9_-]{8,})/', $fields['google_scholar_id'], $m)) {
                    $fields['google_scholar_id'] = $m[1];
                }
                $fields['orcid'] = preg_replace('#^https?://orcid\.org/#i', '', $fields['orcid']);

                if ($id) {
                    $set  = implode(', ', array_map(static function ($c) { return "`$c` = ?"; }, array_keys($fields)));
                    $stmt = $pdo->prepare("UPDATE research_scholars SET $set WHERE id = ?");
                    $stmt->execute(array_merge(array_values($fields), [$id]));
                } else {
                    $cols  = implode(', ', array_map(static function ($c) { return "`$c`"; }, array_keys($fields)));
                    $place = implode(', ', array_fill(0, count($fields), '?'));
                    $stmt  = $pdo->prepare("INSERT INTO research_scholars ($cols) VALUES ($place)");
                    $stmt->execute(array_values($fields));
                    $id = (int) $pdo->lastInsertId();
                }

                // Research areas: replace the set rather than diffing it.
                $pdo->prepare("DELETE FROM research_scholar_areas WHERE scholar_id = ?")->execute([$id]);
                $link = $pdo->prepare("INSERT IGNORE INTO research_scholar_areas (scholar_id, area_id) VALUES (?, ?)");
                foreach ((array) ($_POST['area_ids'] ?? []) as $areaId) {
                    if ((int) $areaId > 0) { $link->execute([$id, (int) $areaId]); }
                }

                vvur_redirect('scholars', 'success', 'Researcher saved.', ['edit' => $id]);
                break;

            /* ---------------------------------------------------------- */
            case 'save_publication':
                $id    = (int) ($_POST['id'] ?? 0);
                $title = trim((string) ($_POST['title'] ?? ''));
                if ($title === '') {
                    vvur_redirect('publications', 'danger', 'The publication needs a title.');
                }

                $pdf = '';
                if (!empty($_FILES['pdf_path']['name'])) {
                    $pdf = (string) handleAdminFileUpload($_FILES['pdf_path'], 'research/papers', 'pub_');
                }
                if ($pdf === '') {
                    $pdf = trim((string) ($_POST['pdf_path_current'] ?? ''));
                }

                $year = trim((string) ($_POST['pub_year'] ?? ''));
                $fields = [
                    'title'          => $title,
                    'authors'        => trim((string) ($_POST['authors'] ?? '')),
                    'scholar_id'     => ($_POST['scholar_id'] ?? '') === '' ? null : (int) $_POST['scholar_id'],
                    'unit_id'        => ($_POST['unit_id'] ?? '') === '' ? null : (int) $_POST['unit_id'],
                    'area_id'        => ($_POST['area_id'] ?? '') === '' ? null : (int) $_POST['area_id'],
                    'pub_type'       => isset(r_publication_types()[$_POST['pub_type'] ?? '']) ? $_POST['pub_type'] : 'journal',
                    'venue'          => trim((string) ($_POST['venue'] ?? '')),
                    'publisher'      => trim((string) ($_POST['publisher'] ?? '')),
                    'pub_year'       => $year === '' ? null : (int) $year,
                    'volume'         => trim((string) ($_POST['volume'] ?? '')),
                    'issue'          => trim((string) ($_POST['issue'] ?? '')),
                    'pages'          => trim((string) ($_POST['pages'] ?? '')),
                    'doi'            => preg_replace('#^https?://(dx\.)?doi\.org/#i', '', trim((string) ($_POST['doi'] ?? ''))),
                    'url'            => trim((string) ($_POST['url'] ?? '')),
                    'pdf_path'       => $pdf,
                    'abstract'       => trim((string) ($_POST['abstract'] ?? '')),
                    'keywords'       => trim((string) ($_POST['keywords'] ?? '')),
                    'citations'      => (int) ($_POST['citations'] ?? 0),
                    'is_open_access' => isset($_POST['is_open_access']) ? 1 : 0,
                    'is_featured'    => isset($_POST['is_featured']) ? 1 : 0,
                    'is_active'      => isset($_POST['is_active']) ? 1 : 0,
                ];

                if ($id) {
                    $set  = implode(', ', array_map(static function ($c) { return "`$c` = ?"; }, array_keys($fields)));
                    $stmt = $pdo->prepare("UPDATE research_publications SET $set WHERE id = ?");
                    $stmt->execute(array_merge(array_values($fields), [$id]));
                } else {
                    $cols  = implode(', ', array_map(static function ($c) { return "`$c`"; }, array_keys($fields)));
                    $place = implode(', ', array_fill(0, count($fields), '?'));
                    $stmt  = $pdo->prepare("INSERT INTO research_publications ($cols) VALUES ($place)");
                    $stmt->execute(array_values($fields));
                    $id = (int) $pdo->lastInsertId();
                }

                // Co-authors drive the "publishes with" panel and let a paper
                // appear on every VVU author's profile, not only the lead's.
                $pdo->prepare("DELETE FROM research_publication_authors WHERE publication_id = ?")->execute([$id]);
                $link  = $pdo->prepare("INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) VALUES (?, ?, ?)");
                $order = 0;
                $coauthorIds = (array) ($_POST['coauthor_ids'] ?? []);
                if ($fields['scholar_id']) {
                    array_unshift($coauthorIds, $fields['scholar_id']);
                }
                foreach (array_unique(array_map('intval', $coauthorIds)) as $sid) {
                    if ($sid > 0) { $link->execute([$id, $sid, $order++]); }
                }

                // Keep the ranked lists honest: recount everyone this paper
                // touches, including anyone just removed from it.
                foreach (array_unique(array_map('intval', array_merge($coauthorIds, [$fields['scholar_id']]))) as $sid) {
                    if ($sid > 0) { r_recount_scholar($pdo, $sid); }
                }

                vvur_redirect('publications', 'success', 'Publication saved.', ['edit' => $id]);
                break;

            /* ---------------------------------------------------------- */
            case 'recount':
                $n = r_recount_all($pdo);
                vvur_redirect($_POST['back'] ?? 'scholars', 'success',
                    "Recounted citation totals for $n researcher" . ($n === 1 ? '' : 's') . ' from the publication catalogue.');
                break;

            /* ---------------------------------------------------------- */
            case 'sync_scholar':
                $sid = (int) ($_POST['scholar_id'] ?? 0);
                $get = $pdo->prepare("SELECT * FROM research_scholars WHERE id = ?");
                $get->execute([$sid]);
                $person = $get->fetch(PDO::FETCH_ASSOC);
                if (!$person) {
                    vvur_redirect('scholars', 'danger', 'That researcher no longer exists.');
                }
                if (trim((string) $person['google_scholar_id']) === '') {
                    vvur_redirect('scholars', 'warning',
                        'Add a Google Scholar profile id to ' . $person['full_name'] . ' first.', ['edit' => $sid]);
                }

                $result = vvur_google_scholar_profile($person['google_scholar_id']);
                if (!$result['ok']) {
                    vvur_redirect('scholars', 'warning', $result['error'], ['edit' => $sid]);
                }

                $m = $result['metrics'];
                $pdo->prepare(
                    "UPDATE research_scholars
                        SET citations = ?, citations_5y = ?, h_index = ?, h_index_5y = ?, i10_index = ?,
                            interests = CASE WHEN interests = '' OR interests IS NULL THEN ? ELSE interests END,
                            last_synced_at = NOW()
                      WHERE id = ?"
                )->execute([
                    (int) ($m['citations'] ?? 0), (int) ($m['citations_5y'] ?? 0),
                    (int) ($m['h_index'] ?? 0), (int) ($m['h_index_5y'] ?? 0),
                    (int) ($m['i10_index'] ?? 0), (string) ($m['interests'] ?? ''), $sid,
                ]);

                $found = count($result['items']);
                $_SESSION['vvur_preview'] = ['items' => $result['items'], 'source' => 'google_scholar', 'scholar_id' => $sid];
                vvur_redirect('scholars', 'success',
                    'Metrics refreshed from Google Scholar: ' . number_format($m['citations'] ?? 0) . ' citations, h-index '
                    . (int) ($m['h_index'] ?? 0) . '.'
                    . ($found ? " $found publications were also read — review them on the Import Data tab." : ''),
                    ['edit' => $sid]);
                break;

            /* ---------------------------------------------------------- */
            case 'import_preview':
                $source = $_POST['source'] ?? '';
                $result = ['ok' => false, 'error' => 'Unknown import source.', 'items' => []];

                switch ($source) {
                    case 'openalex':
                        $result = vvur_openalex_works([
                            'institution' => trim((string) ($_POST['oa_institution'] ?? VVUR_OPENALEX_INSTITUTION)),
                            'author'      => trim((string) ($_POST['oa_author'] ?? '')),
                            'from_year'   => (int) ($_POST['oa_from_year'] ?? 0),
                            'pages'       => (int) ($_POST['oa_pages'] ?? 3),
                        ]);
                        break;
                    case 'crossref':
                        $result = vvur_crossref_search([
                            'author'      => trim((string) ($_POST['cr_author'] ?? '')),
                            'affiliation' => trim((string) ($_POST['cr_affiliation'] ?? '')),
                            'title'       => trim((string) ($_POST['cr_title'] ?? '')),
                            'doi'         => trim((string) ($_POST['cr_doi'] ?? '')),
                            'from_year'   => (int) ($_POST['cr_from_year'] ?? 0),
                            'rows'        => (int) ($_POST['cr_rows'] ?? 40),
                        ]);
                        break;
                    case 'orcid':
                        $result = vvur_orcid_works(
                            (string) ($_POST['orcid_id'] ?? ''),
                            !empty($_POST['orcid_enrich'])
                        );
                        break;
                    case 'bibtex':
                        $text = (string) ($_POST['bibtex_text'] ?? '');
                        if (!empty($_FILES['bibtex_file']['tmp_name']) && is_uploaded_file($_FILES['bibtex_file']['tmp_name'])) {
                            $text = (string) file_get_contents($_FILES['bibtex_file']['tmp_name']);
                        }
                        $result = vvur_parse_bibtex($text);
                        break;
                    case 'csv':
                        $text = (string) ($_POST['csv_text'] ?? '');
                        if (!empty($_FILES['csv_file']['tmp_name']) && is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
                            $text = (string) file_get_contents($_FILES['csv_file']['tmp_name']);
                        }
                        $result = vvur_parse_csv($text);
                        break;
                    case 'google_scholar':
                        $gs = vvur_google_scholar_profile((string) ($_POST['gs_id'] ?? ''));
                        $result = ['ok' => $gs['ok'], 'error' => $gs['error'], 'items' => $gs['items']];
                        break;
                    case 'researchgate':
                        $rg = vvur_researchgate_profile((string) ($_POST['rg_url'] ?? ''));
                        vvur_redirect('import', $rg['ok'] ? 'success' : 'warning',
                            $rg['ok']
                                ? 'ResearchGate profile read. Paste the URL onto the researcher\'s record to link it from their page.'
                                : $rg['error']);
                        break;
                }

                if (!$result['ok'] || !$result['items']) {
                    vvur_redirect('import', 'warning',
                        $result['error'] ?: 'Nothing was found to import.');
                }

                // Flag the ones already in the catalogue so a re-import does
                // not silently double the numbers on the public page.
                foreach ($result['items'] as $i => $item) {
                    $result['items'][$i]['_duplicate_of'] = vvur_find_duplicate($pdo, $item);
                }

                $_SESSION['vvur_preview'] = [
                    'items'      => $result['items'],
                    'source'     => $source,
                    'scholar_id' => (int) ($_POST['attach_scholar'] ?? 0),
                    'unit_id'    => (int) ($_POST['attach_unit'] ?? 0),
                    'area_id'    => (int) ($_POST['attach_area'] ?? 0),
                ];

                $new = count(array_filter($result['items'], static function ($i) { return empty($i['_duplicate_of']); }));
                vvur_redirect('import', 'success',
                    count($result['items']) . ' record(s) read — ' . $new . ' of them new. Review and confirm below.');
                break;

            /* ---------------------------------------------------------- */
            case 'import_commit':
                $preview = $_SESSION['vvur_preview'] ?? null;
                if (!$preview || empty($preview['items'])) {
                    vvur_redirect('import', 'warning', 'That preview has expired. Run the import again.');
                }

                $chosen     = array_map('intval', (array) ($_POST['pick'] ?? []));
                $scholarId  = ($_POST['attach_scholar'] ?? '') === '' ? null : (int) $_POST['attach_scholar'];
                $unitId     = ($_POST['attach_unit'] ?? '') === '' ? null : (int) $_POST['attach_unit'];
                $areaId     = ($_POST['attach_area'] ?? '') === '' ? null : (int) $_POST['attach_area'];
                $updateDupes = !empty($_POST['update_duplicates']);

                $insert = $pdo->prepare(
                    "INSERT INTO research_publications
                        (title, authors, scholar_id, unit_id, area_id, pub_type, venue, publisher,
                         pub_year, volume, issue, pages, doi, url, abstract, keywords, citations,
                         is_open_access, source, external_id, is_active)
                     VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1)"
                );
                $update = $pdo->prepare(
                    "UPDATE research_publications
                        SET citations = GREATEST(citations, ?), venue = COALESCE(NULLIF(venue,''), ?),
                            doi = COALESCE(NULLIF(doi,''), ?), abstract = COALESCE(NULLIF(abstract,''), ?)
                      WHERE id = ?"
                );

                $added = $refreshed = $skipped = 0;
                foreach ($chosen as $index) {
                    $p = $preview['items'][$index] ?? null;
                    if (!$p) { continue; }

                    $dupe = (int) ($p['_duplicate_of'] ?? 0);
                    if ($dupe) {
                        if ($updateDupes) {
                            $update->execute([(int) $p['citations'], $p['venue'], $p['doi'], $p['abstract'], $dupe]);
                            $refreshed++;
                        } else {
                            $skipped++;
                        }
                        continue;
                    }

                    $insert->execute([
                        $p['title'], $p['authors'], $scholarId, $unitId, $areaId,
                        $p['pub_type'], $p['venue'], $p['publisher'], $p['pub_year'],
                        $p['volume'], $p['issue'], $p['pages'], $p['doi'], $p['url'],
                        $p['abstract'], $p['keywords'], (int) $p['citations'],
                        (int) $p['is_open_access'], $p['source'], $p['external_id'],
                    ]);
                    $newId = (int) $pdo->lastInsertId();
                    if ($scholarId) {
                        $pdo->prepare("INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) VALUES (?, ?, 0)")
                            ->execute([$newId, $scholarId]);
                    }
                    $added++;
                }

                if ($scholarId) { r_recount_scholar($pdo, $scholarId); }
                unset($_SESSION['vvur_preview']);

                vvur_redirect('import', 'success',
                    "Imported $added new publication(s)"
                    . ($refreshed ? ", refreshed $refreshed existing" : '')
                    . ($skipped ? ", skipped $skipped already in the catalogue" : '') . '.');
                break;

            /* ---------------------------------------------------------- */
            case 'discard_preview':
                unset($_SESSION['vvur_preview']);
                vvur_redirect('import', 'success', 'Preview discarded.');
                break;
        }
    } catch (PDOException $e) {
        error_log('VVU Scholar admin: ' . $e->getMessage());
        vvur_redirect($tab, 'danger', 'The database rejected that. Nothing was changed. (Details are in the PHP error log.)');
    }
}

/* ==========================================================================
   Deletions.  GET with a confirm dialog in front of it; the CSRF token is
   carried so a crafted link on another site cannot trigger one.
   ========================================================================== */

if (isset($_GET['delete'], $_GET['type'])) {
    if (!vvu_csrf_valid($_GET['token'] ?? null)) {
        vvur_redirect($tab, 'danger', 'That delete link could not be verified. Reload the page and try again.');
    }

    $deletable = [
        'sections'     => 'research_sections',
        'stats'        => 'research_stats',
        'units'        => 'research_units',
        'areas'        => 'research_areas',
        'highlights'   => 'research_highlights',
        'partners'     => 'research_partners',
        'scholars'     => 'research_scholars',
        'publications' => 'research_publications',
    ];
    $type = $_GET['type'];
    if (!isset($deletable[$type])) {
        vvur_redirect($tab, 'danger', 'Unknown record type.');
    }

    $delId = (int) $_GET['delete'];
    try {
        // Clean up the join tables first — MySQL will not do it for us here,
        // and an orphaned row would keep counting towards a co-author panel.
        if ($type === 'scholars') {
            $pdo->prepare("DELETE FROM research_scholar_areas WHERE scholar_id = ?")->execute([$delId]);
            $pdo->prepare("DELETE FROM research_publication_authors WHERE scholar_id = ?")->execute([$delId]);
            $pdo->prepare("UPDATE research_publications SET scholar_id = NULL WHERE scholar_id = ?")->execute([$delId]);
        } elseif ($type === 'publications') {
            $pdo->prepare("DELETE FROM research_publication_authors WHERE publication_id = ?")->execute([$delId]);
        } elseif ($type === 'units') {
            $pdo->prepare("UPDATE research_scholars SET unit_id = NULL WHERE unit_id = ?")->execute([$delId]);
            $pdo->prepare("UPDATE research_publications SET unit_id = NULL WHERE unit_id = ?")->execute([$delId]);
        } elseif ($type === 'areas') {
            $pdo->prepare("DELETE FROM research_scholar_areas WHERE area_id = ?")->execute([$delId]);
            $pdo->prepare("UPDATE research_publications SET area_id = NULL WHERE area_id = ?")->execute([$delId]);
        }

        $pdo->prepare("DELETE FROM `{$deletable[$type]}` WHERE id = ?")->execute([$delId]);
        vvur_redirect($type, 'success', 'Record deleted.');
    } catch (PDOException $e) {
        error_log('VVU Scholar admin delete: ' . $e->getMessage());
        vvur_redirect($type, 'danger', 'That record could not be deleted.');
    }
}

/* ==========================================================================
   Read the state this render needs
   ========================================================================== */

$flash = $_SESSION['vvur_flash'] ?? null;
unset($_SESSION['vvur_flash']);

$settings = [];
foreach ($pdo->query("SELECT setting_key, setting_value FROM research_settings") as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

$editId  = (int) ($_GET['edit'] ?? 0);
$editRow = [];
$metrics = r_metrics($pdo);
$csrf    = vvu_csrf_token();

/** The row being edited on a list tab, if any. */
if ($editId && isset($specs[$tab])) {
    $stmt = $pdo->prepare("SELECT * FROM `{$specs[$tab]['table']}` WHERE id = ?");
    $stmt->execute([$editId]);
    $editRow = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
} elseif ($editId && $tab === 'scholars') {
    $stmt = $pdo->prepare("SELECT * FROM research_scholars WHERE id = ?");
    $stmt->execute([$editId]);
    $editRow = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
} elseif ($editId && $tab === 'publications') {
    $stmt = $pdo->prepare("SELECT * FROM research_publications WHERE id = ?");
    $stmt->execute([$editId]);
    $editRow = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}

$editAreaIds = [];
if ($tab === 'scholars' && $editId) {
    $stmt = $pdo->prepare("SELECT area_id FROM research_scholar_areas WHERE scholar_id = ?");
    $stmt->execute([$editId]);
    $editAreaIds = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
}
$editCoauthorIds = [];
if ($tab === 'publications' && $editId) {
    $stmt = $pdo->prepare("SELECT scholar_id FROM research_publication_authors WHERE publication_id = ? ORDER BY author_order");
    $stmt->execute([$editId]);
    $editCoauthorIds = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
}

$preview = $_SESSION['vvur_preview'] ?? null;

/* ==========================================================================
   Render helpers
   ========================================================================== */

/** Escape. */
function h($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }

/**
 * One form control from a field spec. $value is the current value.
 * Kept deliberately dumb: every tab renders through it so they all look and
 * behave the same way.
 */
function vvur_control($name, array $field, $value, array $context = [])
{
    $id    = 'f_' . preg_replace('/[^a-z0-9_]/i', '_', $name);
    $col   = $field['col'] ?? 12;
    $label = $field['label'] ?? $name;
    $help  = $field['help'] ?? '';

    echo '<div class="col-md-' . (int) $col . ' mb-3">';

    if ($field['type'] !== 'checkbox') {
        echo '<label class="form-label fw-semibold" for="' . h($id) . '">' . h($label);
        if (!empty($field['required'])) { echo ' <span class="text-danger">*</span>'; }
        echo '</label>';
    }

    switch ($field['type']) {
        case 'textarea':
            printf(
                '<textarea class="form-control" id="%s" name="%s" rows="%d">%s</textarea>',
                h($id), h($name), (int) ($field['rows'] ?? 4), h($value)
            );
            break;

        case 'number':
            printf(
                '<input class="form-control" id="%s" name="%s" type="number" value="%s">',
                h($id), h($name), h($value)
            );
            break;

        case 'checkbox':
            printf(
                '<div class="form-check form-switch mt-4"><input class="form-check-input" type="checkbox" id="%s" name="%s" value="1"%s>'
                . '<label class="form-check-label fw-semibold" for="%s">%s</label></div>',
                h($id), h($name), $value ? ' checked' : '', h($id), h($label)
            );
            break;

        case 'toggle':
            printf(
                '<div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="%s" name="%s" value="1"%s>'
                . '<label class="form-check-label" for="%s">Enabled</label></div>',
                h($id), h($name), ((string) $value === '1') ? ' checked' : '', h($id)
            );
            break;

        case 'select':
            $options = $field['options'] ?? ($context[$field['options_var'] ?? ''] ?? []);
            echo '<select class="form-select" id="' . h($id) . '" name="' . h($name) . '">';
            if (isset($field['blank'])) {
                echo '<option value="">' . h($field['blank']) . '</option>';
            }
            foreach ($options as $key => $text) {
                printf('<option value="%s"%s>%s</option>',
                    h($key), ((string) $value === (string) $key) ? ' selected' : '', h($text));
            }
            echo '</select>';
            break;

        case 'color':
            $colors = $context['colorOptions'] ?? [];
            echo '<select class="form-select" id="' . h($id) . '" name="' . h($name) . '">';
            foreach ($colors as $key => $text) {
                printf('<option value="%s"%s>%s</option>',
                    h($key), ((string) $value === (string) $key) ? ' selected' : '', h($text));
            }
            echo '</select>';
            break;

        case 'icon':
            $value = $value !== '' ? $value : ($field['default'] ?? '');
            echo '<div class="input-group">'
               . '<span class="input-group-text"><i class="fa-solid ' . h(ltrim($value, ' ') ?: 'fa-circle-dot') . '"></i></span>';
            printf('<input class="form-control" id="%s" name="%s" type="text" value="%s" placeholder="fa-flask">',
                h($id), h($name), h($value));
            echo '</div>';
            $help = $help ?: 'A Font Awesome 6 name, e.g. fa-flask. <a href="https://fontawesome.com/search?o=r&m=free" target="_blank" rel="noopener">Browse icons</a>';
            echo '<div class="form-text">' . $help . '</div>';
            echo '</div>';
            return;

        case 'image':
            if ($value !== '') {
                printf(
                    '<div class="d-flex align-items-center gap-3 mb-2">'
                    . '<img src="../%s" alt="" style="height:54px;width:auto;border-radius:8px;object-fit:cover;border:1px solid #e2e8f0">'
                    . '<code class="small text-muted text-break">%s</code></div>',
                    h($value), h($value)
                );
            }
            printf('<input class="form-control" id="%s" name="%s" type="file" accept="image/*">', h($id), h($name));
            printf('<input type="hidden" name="%s_current" value="%s">', h($name), h($value));
            $help = $help ?: 'JPG, PNG, GIF or WebP. Leave empty to keep the current image.';
            break;

        default:
            printf(
                '<input class="form-control" id="%s" name="%s" type="text" value="%s"%s>',
                h($id), h($name), h($value),
                !empty($field['required']) ? ' required' : ''
            );
    }

    if ($help !== '') {
        echo '<div class="form-text">' . $help . '</div>';
    }
    echo '</div>';
}

/** A delete link with the confirm dialog and the CSRF token attached. */
function vvur_delete_link($type, $id, $what, $csrf)
{
    printf(
        '<a class="btn btn-sm btn-outline-danger" href="manage_research.php?tab=%s&type=%s&delete=%d&token=%s" '
        . 'onclick="return confirm(\'Delete %s? This cannot be undone.\')" title="Delete">'
        . '<i class="fas fa-trash"></i></a>',
        h($type), h($type), (int) $id, h($csrf), h(addslashes($what))
    );
}

?>

<main class="main-content">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Research Portal <span class="text-muted">(VVU Scholar)</span></h1>
            <p class="text-muted mb-0">
                Everything published at <code>/research/</code> — the landing page, the researcher
                directory, the publication catalogue and the faculty statistics.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="../research/index.php" target="_blank" rel="noopener">
                <i class="fas fa-external-link-alt me-1"></i> View portal
            </a>
            <form method="post" class="d-inline">
                <?php echo vvu_csrf_field(); ?>
                <input type="hidden" name="action" value="recount">
                <input type="hidden" name="back" value="<?php echo h($tab); ?>">
                <button class="btn btn-outline-primary" type="submit"
                        title="Rebuild every researcher's citation totals from the publications in the catalogue">
                    <i class="fas fa-rotate me-1"></i> Recount metrics
                </button>
            </form>
        </div>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-<?php echo h($flash['type']); ?> alert-dismissible fade show">
            <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'danger' ? 'triangle-exclamation' : 'circle-info'); ?> me-2"></i>
            <?php echo h($flash['text']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Live figures, so an editor can see the effect of what they just saved -->
    <div class="row g-3 mb-4">
        <?php
        $summary = [
            ['Researchers',   $metrics['scholars'],     'fa-user-graduate', 'primary'],
            ['Publications',  $metrics['publications'], 'fa-book-open',     'info'],
            ['Citations',     $metrics['citations'],    'fa-quote-right',   'success'],
            ['h-index',       $metrics['h_index'],      'fa-chart-line',    'warning'],
            ['Faculties',     $metrics['units'],        'fa-building-columns', 'secondary'],
            ['Research areas',$metrics['areas'],        'fa-flask',         'dark'],
        ];
        foreach ($summary as $card): ?>
            <div class="col-6 col-lg-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-2 mb-1 text-<?php echo h($card[3]); ?>">
                            <i class="fas <?php echo h($card[2]); ?>"></i>
                            <small class="text-muted text-uppercase" style="font-size:11px;letter-spacing:.08em"><?php echo h($card[0]); ?></small>
                        </div>
                        <div class="h4 mb-0 fw-bold"><?php echo number_format($card[1]); ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-pills flex-wrap gap-2 mb-4">
        <?php foreach ($tabs as $key => $meta): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo $tab === $key ? 'active' : ''; ?>"
                   href="manage_research.php?tab=<?php echo h($key); ?>">
                    <i class="fas <?php echo h($meta[1]); ?> me-1"></i> <?php echo h($meta[0]); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php
    /* ======================================================================
       TAB: Page content
       ====================================================================== */
    if ($tab === 'settings'): ?>
        <form method="post" enctype="multipart/form-data">
            <?php echo vvu_csrf_field(); ?>
            <input type="hidden" name="action" value="save_settings">

            <?php foreach ($settingGroups as $groupName => $fields): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h2 class="h6 mb-0 fw-bold"><?php echo h($groupName); ?></h2>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($fields as $key => $field) {
                                vvur_control($key, $field, $settings[$key] ?? '', ['colorOptions' => $colorOptions]);
                            } ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <button class="btn btn-primary btn-lg" type="submit">
                <i class="fas fa-save me-1"></i> Save page content
            </button>
        </form>

    <?php
    /* ======================================================================
       TAB: Researchers
       ====================================================================== */
    elseif ($tab === 'scholars'):
        $search  = trim((string) ($_GET['q'] ?? ''));
        $listSql = "SELECT s.*, u.name AS unit_name FROM research_scholars s
                    LEFT JOIN research_units u ON u.id = s.unit_id";
        $params  = [];
        if ($search !== '') {
            $listSql .= " WHERE s.full_name LIKE ? OR s.position LIKE ? OR s.interests LIKE ?";
            $params = ["%$search%", "%$search%", "%$search%"];
        }
        $listSql .= " ORDER BY s.citations DESC, s.full_name LIMIT 300";
        $stmt = $pdo->prepare($listSql);
        $stmt->execute($params);
        $scholars = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
        <div class="row g-4">
            <div class="col-xl-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h2 class="h6 mb-0 fw-bold">
                            <?php echo $editId ? 'Edit researcher' : 'Add a researcher'; ?>
                        </h2>
                        <?php if ($editId): ?>
                            <a class="btn btn-sm btn-outline-secondary" href="manage_research.php?tab=scholars">
                                <i class="fas fa-plus"></i> New
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <?php echo vvu_csrf_field(); ?>
                            <input type="hidden" name="action" value="save_scholar">
                            <input type="hidden" name="id" value="<?php echo (int) $editId; ?>">

                            <div class="row">
                                <?php
                                vvur_control('title', ['label' => 'Title', 'type' => 'text', 'col' => 3, 'help' => 'Prof., Dr., Mr., Mrs.'], $editRow['title'] ?? '');
                                vvur_control('full_name', ['label' => 'Full name', 'type' => 'text', 'col' => 9, 'required' => true], $editRow['full_name'] ?? '');
                                vvur_control('position', ['label' => 'Position', 'type' => 'text', 'col' => 12, 'help' => 'e.g. Senior Lecturer, Department of Computer Science'], $editRow['position'] ?? '');
                                vvur_control('unit_id', ['label' => 'Faculty / School', 'type' => 'select', 'col' => 12, 'options_var' => 'unitOptions', 'blank' => '— not assigned —'], $editRow['unit_id'] ?? '', ['unitOptions' => $unitOptions]);
                                vvur_control('email', ['label' => 'Email', 'type' => 'text', 'col' => 6], $editRow['email'] ?? '');
                                vvur_control('phone', ['label' => 'Phone', 'type' => 'text', 'col' => 6], $editRow['phone'] ?? '');
                                vvur_control('photo', ['label' => 'Photo', 'type' => 'image', 'col' => 12, 'help' => 'Square works best. Without one the profile shows the researcher\'s initials.'], $editRow['photo'] ?? '');
                                vvur_control('bio', ['label' => 'Biography', 'type' => 'textarea', 'col' => 12, 'rows' => 4], $editRow['bio'] ?? '');
                                vvur_control('interests', ['label' => 'Research interests', 'type' => 'text', 'col' => 12, 'help' => 'Comma-separated. Shown as tags and searchable.'], $editRow['interests'] ?? '');
                                ?>

                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold">Research areas</label>
                                    <div class="border rounded p-2" style="max-height:150px;overflow-y:auto">
                                        <?php if (!$areaOptions): ?>
                                            <p class="text-muted small mb-0">No research areas defined yet — add them on the Research Areas tab.</p>
                                        <?php endif; ?>
                                        <?php foreach ($areaOptions as $aid => $aname): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="area_ids[]"
                                                       value="<?php echo (int) $aid; ?>" id="area_<?php echo (int) $aid; ?>"
                                                       <?php echo in_array((int) $aid, $editAreaIds, true) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="area_<?php echo (int) $aid; ?>"><?php echo h($aname); ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <h3 class="h6 fw-bold mt-2 mb-3 text-muted text-uppercase" style="font-size:11px;letter-spacing:.1em">Scholarly profiles</h3>
                            <div class="row">
                                <?php
                                vvur_control('google_scholar_id', ['label' => 'Google Scholar', 'type' => 'text', 'col' => 6, 'help' => 'The id after <code>user=</code>, or paste the whole profile URL.'], $editRow['google_scholar_id'] ?? '');
                                vvur_control('orcid', ['label' => 'ORCID iD', 'type' => 'text', 'col' => 6, 'help' => '0000-0002-1825-0097'], $editRow['orcid'] ?? '');
                                vvur_control('researchgate_url', ['label' => 'ResearchGate URL', 'type' => 'text', 'col' => 6], $editRow['researchgate_url'] ?? '');
                                vvur_control('scopus_id', ['label' => 'Scopus author id', 'type' => 'text', 'col' => 6], $editRow['scopus_id'] ?? '');
                                vvur_control('linkedin_url', ['label' => 'LinkedIn', 'type' => 'text', 'col' => 6], $editRow['linkedin_url'] ?? '');
                                vvur_control('website', ['label' => 'Personal website', 'type' => 'text', 'col' => 6], $editRow['website'] ?? '');
                                ?>
                            </div>

                            <h3 class="h6 fw-bold mt-2 mb-3 text-muted text-uppercase" style="font-size:11px;letter-spacing:.1em">
                                Citation metrics
                            </h3>
                            <p class="form-text mt-0 mb-3">
                                Filled in automatically by &ldquo;Sync&rdquo; or by &ldquo;Recount metrics&rdquo;.
                                Recount rebuilds them from the publications in this portal; Sync reads them from
                                Google Scholar. Type them by hand if neither is available.
                            </p>
                            <div class="row">
                                <?php
                                vvur_control('citations', ['label' => 'Citations', 'type' => 'number', 'col' => 4], $editRow['citations'] ?? 0);
                                vvur_control('h_index', ['label' => 'h-index', 'type' => 'number', 'col' => 4], $editRow['h_index'] ?? 0);
                                vvur_control('i10_index', ['label' => 'i10-index', 'type' => 'number', 'col' => 4], $editRow['i10_index'] ?? 0);
                                vvur_control('citations_5y', ['label' => 'Citations (5y)', 'type' => 'number', 'col' => 4], $editRow['citations_5y'] ?? 0);
                                vvur_control('h_index_5y', ['label' => 'h-index (5y)', 'type' => 'number', 'col' => 4], $editRow['h_index_5y'] ?? 0);
                                vvur_control('publications_count', ['label' => 'Publications', 'type' => 'number', 'col' => 4], $editRow['publications_count'] ?? 0);
                                vvur_control('display_order', ['label' => 'Order', 'type' => 'number', 'col' => 4], $editRow['display_order'] ?? 0);
                                vvur_control('is_featured', ['label' => 'Feature this researcher', 'type' => 'checkbox', 'col' => 4], $editRow['is_featured'] ?? 0);
                                vvur_control('is_active', ['label' => 'Show on the portal', 'type' => 'checkbox', 'col' => 4], $editId ? ($editRow['is_active'] ?? 1) : 1);
                                ?>
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-save me-1"></i> <?php echo $editId ? 'Update' : 'Add'; ?> researcher
                                </button>
                                <?php if ($editId): ?>
                                    <a class="btn btn-outline-secondary" href="../research/scholar.php?p=<?php echo h(rawurlencode($editRow['slug'] ?? '')); ?>" target="_blank" rel="noopener">
                                        <i class="fas fa-external-link-alt me-1"></i> View profile
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>

                        <?php if ($editId && !empty($editRow['google_scholar_id'])): ?>
                            <form method="post" class="mt-3 pt-3 border-top">
                                <?php echo vvu_csrf_field(); ?>
                                <input type="hidden" name="action" value="sync_scholar">
                                <input type="hidden" name="scholar_id" value="<?php echo (int) $editId; ?>">
                                <button class="btn btn-outline-success w-100" type="submit">
                                    <i class="fab fa-google me-1"></i> Sync metrics from Google Scholar
                                </button>
                                <div class="form-text">
                                    Reads the public profile. Google blocks a lot of servers — if it fails,
                                    the Import Data tab explains the reliable route.
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-xl-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex flex-wrap gap-2 justify-content-between align-items-center">
                        <h2 class="h6 mb-0 fw-bold"><?php echo count($scholars); ?> researcher<?php echo count($scholars) === 1 ? '' : 's'; ?></h2>
                        <form class="d-flex gap-2" method="get">
                            <input type="hidden" name="tab" value="scholars">
                            <input class="form-control form-control-sm" type="search" name="q"
                                   value="<?php echo h($search); ?>" placeholder="Search by name or interest" style="width:220px">
                            <button class="btn btn-sm btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Researcher</th>
                                    <th>Faculty</th>
                                    <th class="text-end">Cites</th>
                                    <th class="text-end">h</th>
                                    <th class="text-end">Papers</th>
                                    <th class="text-center">Live</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$scholars): ?>
                                    <tr><td colspan="7" class="text-center text-muted py-4">
                                        No researchers yet. Add one on the left, or bulk-load publications from the Import Data tab.
                                    </td></tr>
                                <?php endif; ?>
                                <?php foreach ($scholars as $s): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <?php if (!empty($s['photo'])): ?>
                                                    <img src="../<?php echo h($s['photo']); ?>" alt=""
                                                         style="width:34px;height:34px;border-radius:50%;object-fit:cover">
                                                <?php else: ?>
                                                    <span class="d-inline-grid" style="width:34px;height:34px;border-radius:50%;background:#e2e8f0;place-items:center;font-size:12px;font-weight:700;color:#475569"><?php echo h(r_initials($s['full_name'])); ?></span>
                                                <?php endif; ?>
                                                <div>
                                                    <div class="fw-semibold"><?php echo h(trim(($s['title'] ? $s['title'] . ' ' : '') . $s['full_name'])); ?></div>
                                                    <?php if (!empty($s['position'])): ?>
                                                        <small class="text-muted"><?php echo h($s['position']); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td><small class="text-muted"><?php echo h($s['unit_name'] ?: '—'); ?></small></td>
                                        <td class="text-end"><?php echo number_format($s['citations']); ?></td>
                                        <td class="text-end"><?php echo (int) $s['h_index']; ?></td>
                                        <td class="text-end"><?php echo number_format($s['publications_count']); ?></td>
                                        <td class="text-center">
                                            <?php if ($s['is_active']): ?>
                                                <span class="badge bg-success-subtle text-success">Yes</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary">Hidden</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <a class="btn btn-sm btn-outline-primary" href="manage_research.php?tab=scholars&edit=<?php echo (int) $s['id']; ?>" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php vvur_delete_link('scholars', $s['id'], $s['full_name'], $csrf); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    <?php
    /* ======================================================================
       TAB: Publications
       ====================================================================== */
    elseif ($tab === 'publications'):
        $search = trim((string) ($_GET['q'] ?? ''));
        $pageNo = max(1, (int) ($_GET['p'] ?? 1));
        $perPg  = 25;
        $where  = '';
        $params = [];
        if ($search !== '') {
            $where  = "WHERE p.title LIKE ? OR p.authors LIKE ? OR p.venue LIKE ? OR p.doi LIKE ?";
            $params = ["%$search%", "%$search%", "%$search%", "%$search%"];
        }
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM research_publications p $where");
        $countStmt->execute($params);
        $pubTotal = (int) $countStmt->fetchColumn();
        $pubPages = max(1, (int) ceil($pubTotal / $perPg));
        $pageNo   = min($pageNo, $pubPages);
        $offset   = ($pageNo - 1) * $perPg;

        $stmt = $pdo->prepare(
            "SELECT p.*, s.full_name AS scholar_name
               FROM research_publications p
               LEFT JOIN research_scholars s ON s.id = p.scholar_id
               $where
              ORDER BY p.pub_year DESC, p.citations DESC, p.id DESC
              LIMIT $perPg OFFSET $offset"
        );
        $stmt->execute($params);
        $pubList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h2 class="h6 mb-0 fw-bold"><?php echo $editId ? 'Edit publication' : 'Add a publication'; ?></h2>
                <?php if ($editId): ?>
                    <a class="btn btn-sm btn-outline-secondary" href="manage_research.php?tab=publications">
                        <i class="fas fa-plus"></i> New
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <?php echo vvu_csrf_field(); ?>
                    <input type="hidden" name="action" value="save_publication">
                    <input type="hidden" name="id" value="<?php echo (int) $editId; ?>">

                    <div class="row">
                        <?php
                        vvur_control('title', ['label' => 'Title', 'type' => 'text', 'col' => 12, 'required' => true], $editRow['title'] ?? '');
                        vvur_control('authors', ['label' => 'Authors (byline)', 'type' => 'text', 'col' => 12, 'help' => 'As it should read on the page, in order: "K. Mensah, A. Serwaa, J. Boateng".'], $editRow['authors'] ?? '');
                        vvur_control('scholar_id', ['label' => 'Lead VVU author', 'type' => 'select', 'col' => 4, 'options_var' => 'scholarOptions', 'blank' => '— none —'], $editRow['scholar_id'] ?? '', ['scholarOptions' => $scholarOptions]);
                        vvur_control('unit_id', ['label' => 'Faculty', 'type' => 'select', 'col' => 4, 'options_var' => 'unitOptions', 'blank' => '— none —'], $editRow['unit_id'] ?? '', ['unitOptions' => $unitOptions]);
                        vvur_control('area_id', ['label' => 'Research area', 'type' => 'select', 'col' => 4, 'options_var' => 'areaOptions', 'blank' => '— none —'], $editRow['area_id'] ?? '', ['areaOptions' => $areaOptions]);
                        vvur_control('pub_type', ['label' => 'Type', 'type' => 'select', 'col' => 4, 'options' => r_publication_types()], $editRow['pub_type'] ?? 'journal');
                        vvur_control('pub_year', ['label' => 'Year', 'type' => 'number', 'col' => 2], $editRow['pub_year'] ?? '');
                        vvur_control('citations', ['label' => 'Citations', 'type' => 'number', 'col' => 2], $editRow['citations'] ?? 0);
                        vvur_control('venue', ['label' => 'Journal / conference / book', 'type' => 'text', 'col' => 4], $editRow['venue'] ?? '');
                        vvur_control('publisher', ['label' => 'Publisher', 'type' => 'text', 'col' => 3], $editRow['publisher'] ?? '');
                        vvur_control('volume', ['label' => 'Volume', 'type' => 'text', 'col' => 3], $editRow['volume'] ?? '');
                        vvur_control('issue', ['label' => 'Issue', 'type' => 'text', 'col' => 3], $editRow['issue'] ?? '');
                        vvur_control('pages', ['label' => 'Pages', 'type' => 'text', 'col' => 3], $editRow['pages'] ?? '');
                        vvur_control('doi', ['label' => 'DOI', 'type' => 'text', 'col' => 6, 'help' => '10.1000/xyz123 — a full doi.org URL is accepted and trimmed.'], $editRow['doi'] ?? '');
                        vvur_control('url', ['label' => 'Link', 'type' => 'text', 'col' => 6, 'help' => 'Used when there is no DOI.'], $editRow['url'] ?? '');
                        vvur_control('abstract', ['label' => 'Abstract', 'type' => 'textarea', 'col' => 12, 'rows' => 4], $editRow['abstract'] ?? '');
                        vvur_control('keywords', ['label' => 'Keywords', 'type' => 'text', 'col' => 12, 'help' => 'Comma-separated. Searchable.'], $editRow['keywords'] ?? '');
                        ?>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold" for="co-filter">VVU co-authors</label>
                            <input class="form-control form-control-sm mb-2" type="search" id="co-filter"
                                   data-filter-list="#coauthor-list" placeholder="Type to narrow the list&hellip;"
                                   autocomplete="off">
                            <div class="border rounded p-2" id="coauthor-list" style="max-height:200px;overflow-y:auto">
                                <?php if (!$scholarOptions): ?>
                                    <p class="text-muted small mb-0">No researchers yet.</p>
                                <?php endif; ?>
                                <?php foreach ($scholarOptions as $sid => $sname): ?>
                                    <div class="form-check" data-name="<?php echo h(mb_strtolower($sname)); ?>">
                                        <input class="form-check-input" type="checkbox" name="coauthor_ids[]"
                                               value="<?php echo (int) $sid; ?>" id="co_<?php echo (int) $sid; ?>"
                                               <?php echo in_array((int) $sid, $editCoauthorIds, true) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="co_<?php echo (int) $sid; ?>"><?php echo h($sname); ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="form-text">
                                The paper appears on every ticked person's profile, and drives the
                                &ldquo;publishes with&rdquo; panel. The lead author above is included automatically.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <?php
                                vvur_control('pdf_path', ['label' => 'Full text (PDF)', 'type' => 'image', 'col' => 12, 'help' => 'Optional. Offered as a download next to the record.'], $editRow['pdf_path'] ?? '');
                                vvur_control('is_open_access', ['label' => 'Open access', 'type' => 'checkbox', 'col' => 4], $editRow['is_open_access'] ?? 0);
                                vvur_control('is_featured', ['label' => 'Feature it', 'type' => 'checkbox', 'col' => 4], $editRow['is_featured'] ?? 0);
                                vvur_control('is_active', ['label' => 'Published', 'type' => 'checkbox', 'col' => 4], $editId ? ($editRow['is_active'] ?? 1) : 1);
                                ?>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-save me-1"></i> <?php echo $editId ? 'Update' : 'Add'; ?> publication
                    </button>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <h2 class="h6 mb-0 fw-bold"><?php echo number_format($pubTotal); ?> publication<?php echo $pubTotal === 1 ? '' : 's'; ?></h2>
                <form class="d-flex gap-2" method="get">
                    <input type="hidden" name="tab" value="publications">
                    <input class="form-control form-control-sm" type="search" name="q"
                           value="<?php echo h($search); ?>" placeholder="Title, author, journal or DOI" style="width:250px">
                    <button class="btn btn-sm btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Lead author</th>
                            <th class="text-end">Year</th>
                            <th class="text-end">Cites</th>
                            <th>Source</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$pubList): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">
                                Nothing in the catalogue yet. Add one above, or load a batch from the Import Data tab.
                            </td></tr>
                        <?php endif; ?>
                        <?php foreach ($pubList as $p): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold" style="max-width:520px"><?php echo h($p['title']); ?></div>
                                    <small class="text-muted"><?php echo h(mb_substr((string) $p['venue'], 0, 90)); ?></small>
                                    <?php if (!$p['is_active']): ?>
                                        <span class="badge bg-secondary-subtle text-secondary ms-1">Hidden</span>
                                    <?php endif; ?>
                                    <?php if ($p['is_featured']): ?>
                                        <span class="badge bg-warning-subtle text-warning ms-1">Featured</span>
                                    <?php endif; ?>
                                </td>
                                <td><small class="text-muted"><?php echo h($p['scholar_name'] ?: '—'); ?></small></td>
                                <td class="text-end"><?php echo $p['pub_year'] ? (int) $p['pub_year'] : '—'; ?></td>
                                <td class="text-end"><?php echo number_format($p['citations']); ?></td>
                                <td><span class="badge bg-light text-muted border"><?php echo h($p['source']); ?></span></td>
                                <td class="text-end text-nowrap">
                                    <a class="btn btn-sm btn-outline-primary" href="manage_research.php?tab=publications&edit=<?php echo (int) $p['id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php vvur_delete_link('publications', $p['id'], mb_substr($p['title'], 0, 50), $csrf); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($pubPages > 1): ?>
                <div class="card-footer bg-white d-flex justify-content-center gap-1 flex-wrap py-3">
                    <?php for ($i = 1; $i <= $pubPages; $i++): ?>
                        <a class="btn btn-sm <?php echo $i === $pageNo ? 'btn-primary' : 'btn-outline-secondary'; ?>"
                           href="manage_research.php?tab=publications&p=<?php echo $i; ?><?php echo $search !== '' ? '&q=' . urlencode($search) : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>

    <?php
    /* ======================================================================
       TAB: Import data
       ====================================================================== */
    elseif ($tab === 'import'): ?>

        <?php if ($preview && !empty($preview['items'])): ?>
            <div class="card border-0 shadow-sm mb-4 border-start border-4 border-primary">
                <div class="card-header bg-white py-3 d-flex flex-wrap gap-2 justify-content-between align-items-center">
                    <h2 class="h6 mb-0 fw-bold">
                        <i class="fas fa-list-check me-1"></i>
                        Review <?php echo count($preview['items']); ?> record(s) from
                        <?php echo h(str_replace('_', ' ', $preview['source'])); ?>
                    </h2>
                    <form method="post" class="d-inline">
                        <?php echo vvu_csrf_field(); ?>
                        <input type="hidden" name="action" value="discard_preview">
                        <button class="btn btn-sm btn-outline-secondary" type="submit">Discard</button>
                    </form>
                </div>
                <form method="post">
                    <?php echo vvu_csrf_field(); ?>
                    <input type="hidden" name="action" value="import_commit">
                    <div class="card-body border-bottom">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="ic_scholar">Credit to researcher</label>
                                <select class="form-select" id="ic_scholar" name="attach_scholar">
                                    <option value="">— none —</option>
                                    <?php foreach ($scholarOptions as $sid => $sname): ?>
                                        <option value="<?php echo (int) $sid; ?>" <?php echo (int) $preview['scholar_id'] === (int) $sid ? 'selected' : ''; ?>>
                                            <?php echo h($sname); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Their totals are recounted after the import.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="ic_unit">Faculty</label>
                                <select class="form-select" id="ic_unit" name="attach_unit">
                                    <option value="">— none —</option>
                                    <?php foreach ($unitOptions as $uid => $uname): ?>
                                        <option value="<?php echo (int) $uid; ?>" <?php echo (int) $preview['unit_id'] === (int) $uid ? 'selected' : ''; ?>>
                                            <?php echo h($uname); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="ic_area">Research area</label>
                                <select class="form-select" id="ic_area" name="attach_area">
                                    <option value="">— none —</option>
                                    <?php foreach ($areaOptions as $aid => $aname): ?>
                                        <option value="<?php echo (int) $aid; ?>" <?php echo (int) $preview['area_id'] === (int) $aid ? 'selected' : ''; ?>>
                                            <?php echo h($aname); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="update_duplicates" id="upd" value="1" checked>
                                    <label class="form-check-label" for="upd">
                                        For records already in the catalogue, refresh the citation count and
                                        fill in any blank journal, DOI or abstract — rather than skipping them.
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height:520px;overflow-y:auto">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width:36px"><input class="form-check-input" type="checkbox" id="pickAll" checked></th>
                                    <th>Title</th>
                                    <th>Authors</th>
                                    <th>Venue</th>
                                    <th class="text-end">Year</th>
                                    <th class="text-end">Cites</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($preview['items'] as $i => $item): ?>
                                    <tr class="<?php echo !empty($item['_duplicate_of']) ? 'table-warning' : ''; ?>">
                                        <td>
                                            <input class="form-check-input pick" type="checkbox" name="pick[]"
                                                   value="<?php echo (int) $i; ?>" checked>
                                        </td>
                                        <td style="max-width:380px"><?php echo h($item['title']); ?></td>
                                        <td style="max-width:220px"><small class="text-muted"><?php echo h(mb_substr((string) $item['authors'], 0, 90)); ?></small></td>
                                        <td style="max-width:200px"><small class="text-muted"><?php echo h(mb_substr((string) $item['venue'], 0, 60)); ?></small></td>
                                        <td class="text-end"><?php echo $item['pub_year'] ? (int) $item['pub_year'] : '—'; ?></td>
                                        <td class="text-end"><?php echo number_format((int) $item['citations']); ?></td>
                                        <td>
                                            <?php if (!empty($item['_duplicate_of'])): ?>
                                                <span class="badge bg-warning-subtle text-warning">Already indexed</span>
                                            <?php else: ?>
                                                <span class="badge bg-success-subtle text-success">New</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer bg-white py-3">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-download me-1"></i> Import the ticked records
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <div class="alert alert-info">
            <h3 class="h6 fw-bold"><i class="fas fa-circle-info me-1"></i> Which source should I use?</h3>
            <p class="mb-2">
                <strong>OpenAlex</strong> first — it is the one source that knows what
                &ldquo;Valley View University&rdquo; <em>is</em>, so a single run returns the whole
                institutional corpus with citation counts attached. This portal's catalogue was
                built from it; re-running it refreshes citation counts and picks up new papers.
            </p>
            <p class="mb-2">
                <strong>Crossref</strong> and <strong>ORCID</strong> are the other open APIs that
                answer servers reliably — use Crossref for a specific DOI and ORCID for one
                researcher's own record.
            </p>
            <p class="mb-0">
                <strong>Google Scholar</strong> and <strong>ResearchGate</strong> block most servers,
                so the direct fetch below is best-effort. When it fails, open the profile in your own
                browser, use its <em>Export → BibTeX</em> (or CSV) button, and paste the file into the
                BibTeX or CSV box — that always works, and it is what Google intends you to do.
            </p>
        </div>

        <div class="row g-4">
            <!-- OpenAlex ---------------------------------------------------- -->
            <div class="col-12">
                <div class="card border-0 shadow-sm border-start border-4 border-success">
                    <div class="card-header bg-white py-3">
                        <h2 class="h6 mb-0 fw-bold">
                            <i class="fas fa-globe me-1 text-success"></i> OpenAlex &mdash; the whole University
                        </h2>
                        <small class="text-muted">
                            Open API · citation counts included · the source this catalogue was built from
                        </small>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <?php echo vvu_csrf_field(); ?>
                            <input type="hidden" name="action" value="import_preview">
                            <input type="hidden" name="source" value="openalex">
                            <div class="row">
                                <?php
                                vvur_control('oa_institution', ['label' => 'OpenAlex institution id', 'type' => 'text', 'col' => 3,
                                    'help' => 'Valley View University is <code>' . h(VVUR_OPENALEX_INSTITUTION) . '</code>.'], VVUR_OPENALEX_INSTITUTION);
                                vvur_control('oa_author', ['label' => 'Or one author id', 'type' => 'text', 'col' => 3,
                                    'help' => 'Optional. Narrows the run to a single researcher.'], '');
                                vvur_control('oa_from_year', ['label' => 'Published from', 'type' => 'number', 'col' => 2,
                                    'help' => 'Leave blank for everything.'], '');
                                vvur_control('oa_pages', ['label' => 'Pages to fetch', 'type' => 'number', 'col' => 2,
                                    'help' => '100 works each. The full corpus is about 8 pages.'], 3);
                                vvur_control('attach_scholar', ['label' => 'Credit to', 'type' => 'select', 'col' => 2,
                                    'options_var' => 'scholarOptions', 'blank' => '— match by author —'], '', ['scholarOptions' => $scholarOptions]);
                                ?>
                            </div>
                            <button class="btn btn-success" type="submit">
                                <i class="fas fa-cloud-arrow-down me-1"></i> Fetch from OpenAlex
                            </button>
                            <div class="form-text mt-2">
                                Records already in the catalogue are matched on their OpenAlex id and DOI, so
                                a re-run updates citation counts rather than creating duplicates. Leave
                                &ldquo;Credit to&rdquo; unset when loading the whole University.
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Crossref ---------------------------------------------------- -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h2 class="h6 mb-0 fw-bold"><i class="fas fa-database me-1 text-success"></i> Crossref</h2>
                        <small class="text-muted">Open API · includes citation counts · recommended</small>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <?php echo vvu_csrf_field(); ?>
                            <input type="hidden" name="action" value="import_preview">
                            <input type="hidden" name="source" value="crossref">
                            <div class="row">
                                <?php
                                vvur_control('cr_author', ['label' => 'Author name', 'type' => 'text', 'col' => 6], '');
                                vvur_control('cr_affiliation', ['label' => 'Affiliation', 'type' => 'text', 'col' => 6, 'help' => 'e.g. Valley View University'], 'Valley View University');
                                vvur_control('cr_title', ['label' => 'Title contains', 'type' => 'text', 'col' => 12], '');
                                vvur_control('cr_doi', ['label' => 'Or a single DOI', 'type' => 'text', 'col' => 6], '');
                                vvur_control('cr_from_year', ['label' => 'Published from', 'type' => 'number', 'col' => 3], '');
                                vvur_control('cr_rows', ['label' => 'Max records', 'type' => 'number', 'col' => 3], 40);
                                ?>
                            </div>
                            <button class="btn btn-success" type="submit">
                                <i class="fas fa-magnifying-glass me-1"></i> Search Crossref
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ORCID ------------------------------------------------------- -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h2 class="h6 mb-0 fw-bold"><i class="fab fa-orcid me-1 text-success"></i> ORCID</h2>
                        <small class="text-muted">Open API · the author's own record of their work</small>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <?php echo vvu_csrf_field(); ?>
                            <input type="hidden" name="action" value="import_preview">
                            <input type="hidden" name="source" value="orcid">
                            <div class="row">
                                <?php
                                vvur_control('orcid_id', ['label' => 'ORCID iD', 'type' => 'text', 'col' => 8, 'help' => '0000-0002-1825-0097 or the full orcid.org URL.'], '');
                                vvur_control('attach_scholar', ['label' => 'Credit to', 'type' => 'select', 'col' => 4, 'options_var' => 'scholarOptions', 'blank' => '— choose later —'], '', ['scholarOptions' => $scholarOptions]);
                                ?>
                                <div class="col-12 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="orcid_enrich" id="orcid_enrich" value="1" checked>
                                        <label class="form-check-label" for="orcid_enrich">
                                            Look each DOI up in Crossref to fill in the byline, pagination and citation count
                                        </label>
                                    </div>
                                    <div class="form-text">Slower — up to 25 extra lookups per import — but the records come out complete.</div>
                                </div>
                            </div>
                            <button class="btn btn-success" type="submit">
                                <i class="fas fa-cloud-arrow-down me-1"></i> Fetch from ORCID
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- BibTeX ------------------------------------------------------ -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h2 class="h6 mb-0 fw-bold"><i class="fas fa-file-code me-1 text-primary"></i> BibTeX</h2>
                        <small class="text-muted">Google Scholar → Export → BibTeX, then paste it here</small>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <?php echo vvu_csrf_field(); ?>
                            <input type="hidden" name="action" value="import_preview">
                            <input type="hidden" name="source" value="bibtex">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold" for="bibtex_text">Paste BibTeX</label>
                                    <textarea class="form-control font-monospace" id="bibtex_text" name="bibtex_text" rows="7"
                                              placeholder="@article{mensah2024water,&#10;  title={...},&#10;  author={Mensah, K. and Serwaa, A.},&#10;  journal={...},&#10;  year={2024}&#10;}"></textarea>
                                </div>
                                <div class="col-md-7 mb-3">
                                    <label class="form-label fw-semibold" for="bibtex_file">…or upload a .bib file</label>
                                    <input class="form-control" type="file" id="bibtex_file" name="bibtex_file" accept=".bib,.txt,text/plain">
                                </div>
                                <?php vvur_control('attach_scholar', ['label' => 'Credit to', 'type' => 'select', 'col' => 5, 'options_var' => 'scholarOptions', 'blank' => '— choose later —'], '', ['scholarOptions' => $scholarOptions]); ?>
                            </div>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-file-import me-1"></i> Read BibTeX
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- CSV --------------------------------------------------------- -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h2 class="h6 mb-0 fw-bold"><i class="fas fa-file-csv me-1 text-primary"></i> CSV</h2>
                        <small class="text-muted">Google Scholar's CSV export, or your own spreadsheet</small>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <?php echo vvu_csrf_field(); ?>
                            <input type="hidden" name="action" value="import_preview">
                            <input type="hidden" name="source" value="csv">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold" for="csv_text">Paste CSV</label>
                                    <textarea class="form-control font-monospace" id="csv_text" name="csv_text" rows="7"
                                              placeholder="Title,Authors,Publication,Volume,Number,Pages,Year,Publisher,Citations"></textarea>
                                    <div class="form-text">
                                        The first line must be a header row. Recognised columns: title, authors,
                                        publication/journal, volume, number/issue, pages, year, publisher, doi, url,
                                        abstract, keywords, citations, type.
                                    </div>
                                </div>
                                <div class="col-md-7 mb-3">
                                    <label class="form-label fw-semibold" for="csv_file">…or upload a .csv file</label>
                                    <input class="form-control" type="file" id="csv_file" name="csv_file" accept=".csv,text/csv,text/plain">
                                </div>
                                <?php vvur_control('attach_scholar', ['label' => 'Credit to', 'type' => 'select', 'col' => 5, 'options_var' => 'scholarOptions', 'blank' => '— choose later —'], '', ['scholarOptions' => $scholarOptions]); ?>
                            </div>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-file-import me-1"></i> Read CSV
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Google Scholar ---------------------------------------------- -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h2 class="h6 mb-0 fw-bold"><i class="fab fa-google me-1 text-warning"></i> Google Scholar</h2>
                        <small class="text-muted">Best effort — Google blocks many servers</small>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <?php echo vvu_csrf_field(); ?>
                            <input type="hidden" name="action" value="import_preview">
                            <input type="hidden" name="source" value="google_scholar">
                            <div class="row">
                                <?php
                                vvur_control('gs_id', ['label' => 'Profile id or URL', 'type' => 'text', 'col' => 7, 'help' => 'The value after <code>user=</code> in the profile URL.'], '');
                                vvur_control('attach_scholar', ['label' => 'Credit to', 'type' => 'select', 'col' => 5, 'options_var' => 'scholarOptions', 'blank' => '— choose later —'], '', ['scholarOptions' => $scholarOptions]);
                                ?>
                            </div>
                            <button class="btn btn-warning" type="submit">
                                <i class="fas fa-cloud-arrow-down me-1"></i> Try Google Scholar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ResearchGate ------------------------------------------------ -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h2 class="h6 mb-0 fw-bold"><i class="fab fa-researchgate me-1 text-info"></i> ResearchGate</h2>
                        <small class="text-muted">Link the profile; publications come from the sources above</small>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <?php echo vvu_csrf_field(); ?>
                            <input type="hidden" name="action" value="import_preview">
                            <input type="hidden" name="source" value="researchgate">
                            <?php vvur_control('rg_url', ['label' => 'Profile URL', 'type' => 'text', 'col' => 12, 'help' => 'https://www.researchgate.net/profile/Name'], ''); ?>
                            <button class="btn btn-info text-white" type="submit">
                                <i class="fas fa-link me-1"></i> Check the profile
                            </button>
                            <div class="form-text mt-2">
                                ResearchGate does not permit automated harvesting of its listings. Save the URL on
                                the researcher's record so visitors can follow it, and import the publications
                                themselves from Crossref, ORCID or a BibTeX export.
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php
    /* ======================================================================
       TABS: the six declarative list tabs
       ====================================================================== */
    else:
        $spec    = $specs[$tab];
        $context = [
            'colorOptions'      => $colorOptions,
            'unitOptions'       => $unitOptions,
            'parentUnitOptions' => $parentUnitOptions,
            'areaOptions'       => $areaOptions,
            'scholarOptions'    => $scholarOptions,
        ];
        $rows = $pdo->query("SELECT * FROM `{$spec['table']}` ORDER BY {$spec['order']}")->fetchAll(PDO::FETCH_ASSOC);
    ?>
        <div class="row g-4">
            <div class="col-xl-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h2 class="h6 mb-0 fw-bold">
                            <?php echo $editId ? 'Edit ' . h($spec['single']) : 'Add a ' . h($spec['single']); ?>
                        </h2>
                        <?php if ($editId): ?>
                            <a class="btn btn-sm btn-outline-secondary" href="manage_research.php?tab=<?php echo h($tab); ?>">
                                <i class="fas fa-plus"></i> New
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <?php echo vvu_csrf_field(); ?>
                            <input type="hidden" name="action" value="save_row">
                            <input type="hidden" name="spec" value="<?php echo h($tab); ?>">
                            <input type="hidden" name="id" value="<?php echo (int) $editId; ?>">

                            <div class="row">
                                <?php foreach ($spec['fields'] as $name => $field) {
                                    $value = $editId
                                        ? ($editRow[$name] ?? '')
                                        : ($field['default'] ?? ($field['type'] === 'number' ? 0 : ''));
                                    vvur_control($name, $field, $value, $context);
                                } ?>
                            </div>

                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-save me-1"></i>
                                <?php echo $editId ? 'Update' : 'Add'; ?> <?php echo h($spec['single']); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h2 class="h6 mb-0 fw-bold">
                            <?php echo count($rows); ?> <?php echo h($spec['single']); ?><?php echo count($rows) === 1 ? '' : 's'; ?>
                        </h2>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:56px">Order</th>
                                    <th><?php echo h(ucfirst($spec['single'])); ?></th>
                                    <th class="text-center" style="width:80px">Active</th>
                                    <th class="text-end" style="width:110px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$rows): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-4">
                                        Nothing here yet — add the first <?php echo h($spec['single']); ?> on the left.
                                    </td></tr>
                                <?php endif; ?>
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td class="text-muted"><?php echo (int) ($row['display_order'] ?? 0); ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <?php if (!empty($row['icon']) || !empty($row['stat_icon'])): ?>
                                                    <span class="d-inline-grid" style="width:30px;height:30px;border-radius:8px;background:#f1f5f9;place-items:center">
                                                        <i class="fa-solid <?php echo h(ltrim($row['icon'] ?? $row['stat_icon'], ' ')); ?> text-secondary" style="font-size:12px"></i>
                                                    </span>
                                                <?php elseif (!empty($row['logo'])): ?>
                                                    <img src="../<?php echo h($row['logo']); ?>" alt="" style="width:30px;height:30px;object-fit:contain">
                                                <?php endif; ?>
                                                <div>
                                                    <div class="fw-semibold"><?php echo h($row[$spec['title']] ?? ''); ?></div>
                                                    <?php
                                                    $sub = $row['section_subtitle'] ?? $row['subtitle'] ?? $row['short_name']
                                                        ?? $row['country'] ?? $row['auto_key'] ?? $row['section_key'] ?? '';
                                                    if ($sub !== '' && $sub !== null): ?>
                                                        <small class="text-muted"><?php echo h(mb_substr((string) $sub, 0, 70)); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($row['is_active'])): ?>
                                                <span class="badge bg-success-subtle text-success">On</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary">Off</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <a class="btn btn-sm btn-outline-primary"
                                               href="manage_research.php?tab=<?php echo h($tab); ?>&edit=<?php echo (int) $row['id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php vvur_delete_link($tab, $row['id'], (string) ($row[$spec['title']] ?? 'this record'), $csrf); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Import preview: one checkbox drives the lot.
    var all = document.getElementById('pickAll');
    if (all) {
        all.addEventListener('change', function () {
            document.querySelectorAll('.pick').forEach(function (box) { box.checked = all.checked; });
        });
    }

    // Narrow a long checkbox list by typing. A ticked row always stays visible,
    // so filtering can never hide a selection the editor has already made.
    document.querySelectorAll('[data-filter-list]').forEach(function (input) {
        var list = document.querySelector(input.getAttribute('data-filter-list'));
        if (!list) { return; }
        input.addEventListener('input', function () {
            var q = input.value.trim().toLowerCase();
            list.querySelectorAll('[data-name]').forEach(function (row) {
                var box = row.querySelector('input[type="checkbox"]');
                var hit = q === '' || row.getAttribute('data-name').indexOf(q) !== -1;
                row.hidden = !(hit || (box && box.checked));
            });
        });
    });
});
</script>

<?php include 'footer.php'; ?>
