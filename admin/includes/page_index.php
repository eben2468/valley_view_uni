<?php
/**
 * Admin page search index.
 *
 * The sidebar lists 26 top-level managers, but most of the actual editable
 * pages live *inside* those managers as `?page=` tabs — "Work Study", for
 * example, is three clicks deep under "Campus Life Pages" and appears nowhere
 * in the sidebar. Finding a page therefore meant remembering which manager
 * owned it. This builds one flat, searchable list of every destination.
 *
 * The sub-page entries are parsed out of the manager files themselves rather
 * than duplicated here, so adding a page to a manager's $managed_pages array
 * automatically makes it searchable — there is no second list to keep in sync.
 *
 * The parsed result is cached to a file; it is rebuilt automatically whenever
 * any manager file changes.
 */

if (!function_exists('vvu_admin_page_index')) {

    /** Top-level sidebar destinations, with extra keywords for searching. */
    function vvu_admin_top_level_pages() {
        return [
            ['file' => 'index.php',                        'title' => 'Dashboard',                   'icon' => 'fa-tachometer-alt',   'keywords' => 'home overview stats'],
            ['file' => 'manage_homepage_content.php',      'title' => 'Homepage Content',            'icon' => 'fa-home',             'keywords' => 'front page slider hero banner'],
            ['file' => 'manage_about_pages.php',           'title' => 'About Pages',                 'icon' => 'fa-info-circle',      'keywords' => 'mission vision core values anthem ecology campus'],
            ['file' => 'manage_contact_page.php',          'title' => 'Contact Page',                'icon' => 'fa-address-book',     'keywords' => 'phone email address map'],
            ['file' => 'manage_academic_pages.php',        'title' => 'Academic Overview Pages',     'icon' => 'fa-file-alt',         'keywords' => 'programs campus learning outcomes'],
            ['file' => 'manage_admissions_pages.php',      'title' => 'Admissions Info Pages',       'icon' => 'fa-user-shield',      'keywords' => 'entry requirements scholarships apply'],
            ['file' => 'manage_info_pages.php',            'title' => 'Student Info Pages',          'icon' => 'fa-info-circle',      'keywords' => 'freshmen new to vvu tour forms'],
            ['file' => 'manage_departmental_resources.php','title' => 'Resource Dept CMS',           'icon' => 'fa-layer-group',      'keywords' => 'department resources'],
            ['file' => 'manage_faqs.php',                  'title' => 'Manage FAQs',                 'icon' => 'fa-question-circle',  'keywords' => 'questions answers help'],
            ['file' => 'manage_strategy_history.php',      'title' => 'Strategy & History',          'icon' => 'fa-history',          'keywords' => 'strategic plan timeline past'],
            ['file' => 'manage_administration_pages.php',  'title' => 'Resource & Admin Pages',      'icon' => 'fa-university',       'keywords' => 'offices registrar vice-chancellor vice chancellor cfo'],
            ['file' => 'manage_resources_pages.php',       'title' => 'Application & Resources',     'icon' => 'fa-file-invoice',     'keywords' => 'fees structure why choose vvu'],
            ['file' => 'manage_programs.php',              'title' => 'Academic Programs',           'icon' => 'fa-graduation-cap',   'keywords' => 'courses degrees faculty departments'],
            ['file' => 'manage_program_categories.php',    'title' => 'Program Categories',          'icon' => 'fa-sitemap',          'keywords' => 'course grouping'],
            ['file' => 'manage_directory.php',             'title' => 'Staff & Faculty',             'icon' => 'fa-users',            'keywords' => 'lecturers staff directory people'],
            ['file' => 'manage_university_directory.php',  'title' => 'Univ. Directory hierarchy',   'icon' => 'fa-sitemap',          'keywords' => 'org chart structure'],
            ['file' => 'manage_news.php',                  'title' => 'News & Events',               'icon' => 'fa-newspaper',        'keywords' => 'articles announcements notices'],
            ['file' => 'manage_navigation.php',            'title' => 'Navigation Settings',         'icon' => 'fa-bars',             'keywords' => 'menu links header'],
            ['file' => 'manage_encyclopedia_content.php',  'title' => 'Encyclopedia Content',        'icon' => 'fa-edit',             'keywords' => 'faculty staff encyclopedia'],
            ['file' => 'manage_campus_life_pages.php',     'title' => 'Campus Life Pages',           'icon' => 'fa-university',       'keywords' => 'student life dress accommodation food work study radio'],
            ['file' => 'manage_campus_life_lists.php',     'title' => 'Campus Life Lists',           'icon' => 'fa-list-ul',          'keywords' => 'benefits opportunities steps halls'],
            ['file' => 'manage_ventures_pages.php',        'title' => 'Ventures & Services',         'icon' => 'fa-store',            'keywords' => 'bakery water grocery post office radio'],
            ['file' => 'manage_alumni_page.php',           'title' => 'Alumni Network',              'icon' => 'fa-user-graduate',    'keywords' => 'graduates former students'],
            ['file' => 'manage_graduate_page.php',         'title' => 'Graduate School',             'icon' => 'fa-graduation-cap',   'keywords' => 'postgraduate masters phd'],
            ['file' => 'manage_gallery.php',               'title' => 'Gallery',                     'icon' => 'fa-images',           'keywords' => 'photos images pictures'],
            ['file' => 'manage_footer.php',                'title' => 'Footer Management',           'icon' => 'fa-shoe-prints',      'keywords' => 'bottom links social'],
            ['file' => 'manage_users.php',                 'title' => 'Manage Admin Users',          'icon' => 'fa-user-lock',        'keywords' => 'accounts permissions login'],
            ['file' => 'settings.php',                     'title' => 'Settings',                    'icon' => 'fa-cog',              'keywords' => 'configuration preferences'],
            ['file' => 'profile.php',                      'title' => 'My Profile',                  'icon' => 'fa-user',             'keywords' => 'account password'],
        ];
    }

    /**
     * Managers whose sub-pages are declared as a PHP array literal, keyed by
     * the variable holding them. Each becomes "Manager › Sub-page".
     */
    function vvu_admin_subpage_sources() {
        return [
            'manage_campus_life_pages.php'      => ['var' => 'page_titles',   'parent' => 'Campus Life Pages'],
            'manage_academic_pages.php'         => ['var' => 'managed_pages', 'parent' => 'Academic Overview Pages'],
            'manage_admissions_pages.php'       => ['var' => 'managed_pages', 'parent' => 'Admissions Info Pages'],
            'manage_info_pages.php'             => ['var' => 'managed_pages', 'parent' => 'Student Info Pages'],
            'manage_ventures_pages.php'         => ['var' => 'managed_pages', 'parent' => 'Ventures & Services'],
            'manage_resources_pages.php'        => ['var' => 'managed_pages', 'parent' => 'Application & Resources'],
            'manage_departmental_resources.php' => ['var' => 'managed_pages', 'parent' => 'Resource Dept CMS'],
        ];
    }

    /** Tab-based managers, which use #anchors instead of ?page=. */
    function vvu_admin_static_subpages() {
        return [
            ['file' => 'manage_about_pages.php#mission_vision', 'title' => 'Mission & Vision', 'parent' => 'About Pages', 'icon' => 'fa-bullseye',    'keywords' => 'mission vision pillars'],
            ['file' => 'manage_about_pages.php#core_values',    'title' => 'Core Values',      'parent' => 'About Pages', 'icon' => 'fa-heart',       'keywords' => 'values principles'],
            ['file' => 'manage_about_pages.php#anthem',         'title' => 'VVU Anthem',       'parent' => 'About Pages', 'icon' => 'fa-music',       'keywords' => 'song lyrics'],
            ['file' => 'manage_about_pages.php#ecology',        'title' => 'Ecology',          'parent' => 'About Pages', 'icon' => 'fa-leaf',        'keywords' => 'environment green'],
            ['file' => 'manage_about_pages.php#campus',         'title' => 'The Campus',       'parent' => 'About Pages', 'icon' => 'fa-map-location','keywords' => 'facilities map'],
        ];
    }

    /**
     * Extracts "'key' => [ ... 'title' => 'Name' ... ]" or the simpler
     * "'key' => 'Name'" pairs from a manager file.
     *
     * A regex is used rather than including the file, because including a
     * manager would execute its auth checks, database queries and POST
     * handling as a side effect.
     */
    function vvu_parse_manager_subpages($path, $varName) {
        if (!is_readable($path)) {
            return [];
        }

        $source = file_get_contents($path);

        // Isolate the array literal assigned to $varName.
        if (!preg_match('/\$' . preg_quote($varName, '/') . '\s*=\s*\[/', $source, $m, PREG_OFFSET_CAPTURE)) {
            return [];
        }

        $start = $m[0][1] + strlen($m[0][0]) - 1;   // position of the opening [
        $depth = 0;
        $end   = null;

        for ($i = $start, $len = strlen($source); $i < $len; $i++) {
            if ($source[$i] === '[') {
                $depth++;
            } elseif ($source[$i] === ']') {
                $depth--;
                if ($depth === 0) { $end = $i; break; }
            }
        }

        if ($end === null) {
            return [];
        }

        $block = substr($source, $start, $end - $start + 1);
        $out   = [];

        // Nested form: 'key' => [ ... 'title' => 'Title' ... ]
        //
        // Matched non-greedily up to the first ']' that is not inside a quoted
        // string. Anchoring on a newline instead would miss managers that
        // declare each entry on a single line (manage_ventures_pages.php).
        if (preg_match_all("/'([a-z0-9_-]+)'\s*=>\s*\[((?:[^\[\]']|'(?:[^'\\\\]|\\\\.)*')*)\]/is", $block, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $entry) {
                if (preg_match("/'title'\s*=>\s*'((?:[^'\\\\]|\\\\.)*)'/", $entry[2], $t)) {
                    $item = [
                        'key'   => $entry[1],
                        'title' => stripslashes($t[1]),
                    ];
                    if (preg_match("/'icon'\s*=>\s*'([^']*)'/", $entry[2], $ic)) {
                        $item['icon'] = $ic[1];
                    }
                    if (preg_match("/'description'\s*=>\s*'((?:[^'\\\\]|\\\\.)*)'/", $entry[2], $d)) {
                        $item['keywords'] = stripslashes($d[1]);
                    }
                    $out[$entry[1]] = $item;
                }
            }
        }

        // Flat form: 'key' => 'Title'   (used by manage_campus_life_pages.php)
        if (!$out && preg_match_all("/'([a-z0-9_-]+)'\s*=>\s*'((?:[^'\\\\]|\\\\.)*)'/", $block, $flat, PREG_SET_ORDER)) {
            foreach ($flat as $entry) {
                $out[$entry[1]] = [
                    'key'   => $entry[1],
                    'title' => stripslashes($entry[2]),
                ];
            }
        }

        return array_values($out);
    }

    /**
     * Returns the full flat index: [title, url, parent, icon, keywords].
     * Cached to admin/cache/page_index.json, rebuilt when a manager changes.
     */
    function vvu_admin_page_index() {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }

        $adminDir  = __DIR__ . '/..';
        $cacheFile = $adminDir . '/cache/page_index.json';
        $sources   = vvu_admin_subpage_sources();

        // Newest mtime across the parsed managers decides cache validity.
        $newest = 0;
        foreach (array_keys($sources) as $file) {
            $full = $adminDir . '/' . $file;
            if (is_file($full)) {
                $newest = max($newest, filemtime($full));
            }
        }
        $newest = max($newest, filemtime(__FILE__));

        if (is_file($cacheFile) && filemtime($cacheFile) >= $newest) {
            $decoded = json_decode(file_get_contents($cacheFile), true);
            if (is_array($decoded) && $decoded) {
                return $cached = $decoded;
            }
        }

        $index = [];

        foreach (vvu_admin_top_level_pages() as $page) {
            // Skip entries whose file is not present in this deployment.
            if (!is_file($adminDir . '/' . strtok($page['file'], '#'))) {
                continue;
            }
            $index[] = [
                'title'    => $page['title'],
                'url'      => $page['file'],
                'parent'   => '',
                'icon'     => $page['icon'],
                'keywords' => $page['keywords'] ?? '',
            ];
        }

        foreach ($sources as $file => $meta) {
            $full = $adminDir . '/' . $file;
            foreach (vvu_parse_manager_subpages($full, $meta['var']) as $sub) {
                $index[] = [
                    'title'    => $sub['title'],
                    'url'      => $file . '?page=' . $sub['key'],
                    'parent'   => $meta['parent'],
                    'icon'     => $sub['icon'] ?? 'fa-file-lines',
                    'keywords' => $sub['keywords'] ?? '',
                ];
            }
        }

        foreach (vvu_admin_static_subpages() as $sub) {
            if (!is_file($adminDir . '/' . strtok($sub['file'], '#'))) {
                continue;
            }
            $index[] = [
                'title'    => $sub['title'],
                'url'      => $sub['file'],
                'parent'   => $sub['parent'],
                'icon'     => $sub['icon'],
                'keywords' => $sub['keywords'],
            ];
        }

        // Best-effort cache write; a read-only deployment simply reparses.
        $cacheDir = dirname($cacheFile);
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }
        if (is_dir($cacheDir) && is_writable($cacheDir)) {
            @file_put_contents($cacheFile, json_encode($index));
        }

        return $cached = $index;
    }
}
