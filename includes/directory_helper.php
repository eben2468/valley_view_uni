<?php
/**
 * Shared rendering helpers for the Faculty & Staff Encyclopedia pages.
 *
 * Everything here is presentation-only: it derives badges, colours and
 * initials from what the admin panel stores in the `directory` table, so no
 * extra columns are needed just to make a card look right.
 */

if (!function_exists('vvu_dir_e')) {
    function vvu_dir_e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('vvu_dir_initials')) {
    /**
     * Two-letter monogram used when a person has no photo yet.
     * Honorifics are skipped so "Prof. Daniel Ganu" reads as DG, not PD.
     */
    function vvu_dir_initials(string $name): string
    {
        $skip = ['dr', 'dr.', 'prof', 'prof.', 'professor', 'pastor', 'mr', 'mr.',
                 'mrs', 'mrs.', 'ms', 'ms.', 'chief', 'insp.', 'inspec.'];

        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $words = [];
        foreach ($parts as $p) {
            $clean = preg_replace('/[^A-Za-z.]/', '', $p);
            if ($clean === '' || in_array(mb_strtolower($clean), $skip, true)) {
                continue;
            }
            $words[] = $clean;
        }
        if (!$words) {
            $words = $parts ?: ['?'];
        }

        $first = mb_strtoupper(mb_substr($words[0], 0, 1));
        $last  = count($words) > 1 ? mb_strtoupper(mb_substr(end($words), 0, 1)) : '';

        return $first . $last;
    }
}

if (!function_exists('vvu_dir_avatar_tone')) {
    /**
     * A stable colour pair per person, so the monogram wall looks varied but
     * never changes between page loads.
     */
    function vvu_dir_avatar_tone(string $seed): array
    {
        $palette = [
            ['#7f1d1d', '#b91c1c'], ['#134e4a', '#0f766e'], ['#1e3a8a', '#1d4ed8'],
            ['#3f2d0f', '#a16207'], ['#4c1d95', '#6d28d9'], ['#14532d', '#15803d'],
            ['#831843', '#be185d'], ['#0c4a6e', '#0369a1'], ['#3b0764', '#7e22ce'],
            ['#422006', '#b45309'], ['#164e63', '#0e7490'], ['#1f2937', '#374151'],
        ];
        return $palette[abs(crc32($seed)) % count($palette)];
    }
}

if (!function_exists('vvu_dir_rank')) {
    /**
     * Maps a free-text job title onto a rank tier used for the card badge.
     * Returns [label, css-modifier, weight] — weight drives "rank" sorting.
     */
    function vvu_dir_rank(string $jobTitle, string $type = 'faculty'): array
    {
        $j = mb_strtoupper($jobTitle);

        if ($type === 'faculty') {
            if (strpos($j, 'PROFESSOR EMERITUS') !== false) return ['Professor Emeritus', 'emeritus', 1];
            if (strpos($j, 'ASSO. PROFESSOR') !== false
                || strpos($j, 'ASSOC. PROFESSOR') !== false
                || strpos($j, 'ASSOCIATE PROFESSOR') !== false) return ['Assoc. Professor', 'assoc-prof', 2];
            if (strpos($j, 'PROFESSOR') !== false) return ['Professor', 'professor', 1];
            if (strpos($j, 'SENIOR LECTURER') !== false) return ['Senior Lecturer', 'senior-lecturer', 3];
            if (strpos($j, 'ASSISTANT LECTURER') !== false) return ['Assistant Lecturer', 'assistant-lecturer', 5];
            if (strpos($j, 'LECTURER') !== false) return ['Lecturer', 'lecturer', 4];
            return ['Academic Staff', 'lecturer', 6];
        }

        if (strpos($j, 'VICE-CHANCELLOR') !== false || strpos($j, 'CHANCELLOR') !== false) return ['Chancellery', 'chancellery', 1];
        if (strpos($j, 'REGISTRAR') !== false)  return ['Registry', 'registry', 3];
        if (strpos($j, 'DIRECTOR') !== false)   return ['Directorate', 'directorate', 2];
        if (strpos($j, 'DEAN') !== false)       return ['Deanery', 'chancellery', 2];
        if (strpos($j, 'ACCOUNT') !== false || strpos($j, 'AUDIT') !== false || strpos($j, 'FINANCIAL') !== false) return ['Finance', 'finance', 4];
        if (strpos($j, 'LIBRAR') !== false)     return ['Library', 'library', 4];
        if (strpos($j, 'PASTOR') !== false || strpos($j, 'CHAPLAIN') !== false) return ['Chaplaincy', 'chaplaincy', 3];
        if (strpos($j, 'SECURITY') !== false)   return ['Security', 'security', 6];
        return ['Staff', 'general', 5];
    }
}

if (!function_exists('vvu_dir_leadership')) {
    /**
     * Pulls a leadership role out of a combined title such as
     * "Senior Lecturer/Dean SOB" so the card can show it as a ribbon.
     */
    function vvu_dir_leadership(string $jobTitle): string
    {
        $keywords = ['VICE-CHANCELLOR', 'CHANCELLOR', 'RECTOR', 'DEAN', 'HOD', 'HEAD',
                     'DIRECTOR', 'REGISTRAR', 'COORDINATOR', 'CHIEF', 'MANAGER', 'PROVOST'];

        foreach (preg_split('#\s*/\s*#', $jobTitle) as $segment) {
            $upper = mb_strtoupper($segment);
            foreach ($keywords as $k) {
                if (strpos($upper, $k) !== false) {
                    return trim($segment);
                }
            }
        }
        return '';
    }
}

if (!function_exists('vvu_dir_primary_role')) {
    /** The part of the job title before any leadership suffix. */
    function vvu_dir_primary_role(string $jobTitle): string
    {
        $segments = preg_split('#\s*/\s*#', $jobTitle);
        return trim($segments[0] ?? $jobTitle);
    }
}

if (!function_exists('vvu_dir_image')) {
    /** Normalises a stored image path for use from the site root. */
    function vvu_dir_image(?string $path): string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return '';
        }
        if (preg_match('#^(https?:)?//#i', $path) || $path[0] === '/') {
            return $path;
        }
        return ltrim(str_replace('../', '', $path), '/');
    }
}

if (!function_exists('vvu_dir_card')) {
    /**
     * Renders one directory card.
     *
     * @param array  $person Row from `directory`.
     * @param string $type   'faculty' or 'staff' — only changes the accent.
     */
    function vvu_dir_card(array $person, string $type = 'faculty'): void
    {
        $name       = (string) ($person['name'] ?? '');
        $jobTitle   = (string) ($person['job_title'] ?? '');
        $department = (string) ($person['department'] ?? '');
        $image      = vvu_dir_image($person['image_url'] ?? '');
        [$rankLabel, $rankClass] = vvu_dir_rank($jobTitle, $type);
        $leadership = vvu_dir_leadership($jobTitle);
        $role       = vvu_dir_primary_role($jobTitle);
        [$c1, $c2]  = vvu_dir_avatar_tone($name);
        $searchBlob = mb_strtolower($name . ' ' . $jobTitle . ' ' . $department . ' ' . ($person['faculty_group'] ?? ''));
        ?>
        <article class="dir-card" data-search="<?php echo vvu_dir_e($searchBlob); ?>">
            <a class="dir-card__link" href="profile.php?id=<?php echo (int) ($person['id'] ?? 0); ?>">
                <div class="dir-card__media">
                    <?php if ($image !== ''): ?>
                        <img src="<?php echo vvu_dir_e($image); ?>" alt="<?php echo vvu_dir_e($name); ?>" loading="lazy">
                    <?php else: ?>
                        <span class="dir-card__monogram" style="--m1:<?php echo $c1; ?>;--m2:<?php echo $c2; ?>;">
                            <?php echo vvu_dir_e(vvu_dir_initials($name)); ?>
                        </span>
                    <?php endif; ?>
                    <span class="dir-rank dir-rank--<?php echo vvu_dir_e($rankClass); ?>"><?php echo vvu_dir_e($rankLabel); ?></span>
                    <?php if ($leadership !== ''): ?>
                        <span class="dir-card__ribbon" title="<?php echo vvu_dir_e($leadership); ?>">
                            <span class="material-symbols-outlined">workspace_premium</span>
                            <?php echo vvu_dir_e($leadership); ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="dir-card__body">
                    <h3 class="dir-card__name"><?php echo vvu_dir_e($name); ?></h3>
                    <p class="dir-card__role"><?php echo vvu_dir_e($role); ?></p>
                    <?php if ($department !== ''): ?>
                        <p class="dir-card__dept">
                            <span class="material-symbols-outlined">apartment</span>
                            <?php echo vvu_dir_e($department); ?>
                        </p>
                    <?php endif; ?>
                    <span class="dir-card__cta">
                        View Profile
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </span>
                </div>
            </a>
        </article>
        <?php
    }
}

if (!function_exists('vvu_dir_styles')) {
    /**
     * The card / filter stylesheet shared by both encyclopedia pages.
     * $accent is the page's primary colour, $accentDark its hover shade.
     */
    function vvu_dir_styles(string $accent, string $accentDark): void
    {
        ?>
        <style>
        .dir-scope {
            --accent: <?php echo $accent; ?>;
            --accent-dark: <?php echo $accentDark; ?>;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --surface: #ffffff;
            --page: #f6f7f9;
        }
        .dark .dir-scope {
            --ink: #f1f5f9;
            --muted: #94a3b8;
            --line: rgba(148, 163, 184, .22);
            --surface: #131c2b;
            --page: #0b1220;
        }

        /* The theme sets html{font-size:10px}, which shrinks every rem-based
           Tailwind utility to 62.5% of its nominal size. Everything below is
           therefore expressed in px so the directory renders at full size. */
        .dir-wrap { width: 100%; max-width: 1220px; margin-left: auto; margin-right: auto; }
        .dir-block { padding-top: 56px; }
        .dir-block--tight { padding-top: 34px; }
        .dir-block--cards { padding-top: 46px; padding-bottom: 60px; }

        /* ------------------------------------------------------------ */
        /*  Search + filter panel                                        */
        /* ------------------------------------------------------------ */
        .dir-panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 24px;
            padding: 18px;
            box-shadow: 0 24px 60px -28px rgba(15, 23, 42, .45);
        }
        .dir-panel__grid {
            display: grid;
            grid-template-columns: minmax(0, 1.5fr) minmax(0, 1.15fr) minmax(0, 1.15fr) auto;
            gap: 12px;
            align-items: stretch;
        }
        .dir-panel__grid--3 { grid-template-columns: minmax(0, 2.4fr) minmax(0, 1.4fr) auto; }

        .dir-field { position: relative; display: flex; align-items: center; min-width: 0; margin: 0; }
        .dir-field > .material-symbols-outlined {
            position: absolute; left: 16px; font-size: 20px; color: var(--muted);
            pointer-events: none; z-index: 1;
        }
        /* The theme ships Materialize + Bootstrap, both of which restyle bare
           inputs and selects. The extra specificity below is what keeps this
           control looking like a control and not a bare underline. */
        .dir-scope .dir-field input.dir-field__control,
        .dir-scope .dir-field select.dir-field__control {
            display: block;
            box-sizing: border-box;
            width: 100%;
            min-width: 0;
            max-width: none;
            height: 56px;
            margin: 0;
            padding: 0 18px 0 46px;
            border-radius: 16px;
            border: 1.5px solid var(--line);
            background-color: var(--page);
            color: var(--ink);
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            line-height: normal;
            box-shadow: none;
            transition: border-color .2s ease, background-color .2s ease, box-shadow .2s ease;
            outline: none;
            text-overflow: ellipsis;
        }
        .dir-scope .dir-field .dir-field__control::placeholder { color: var(--muted); font-weight: 500; opacity: 1; }
        .dir-scope .dir-field .dir-field__control:focus {
            background-color: var(--surface);
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(15, 23, 42, .06);
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--accent) 14%, transparent);
        }
        .dir-scope .dir-field select.dir-field__control {
            appearance: none; -webkit-appearance: none; -moz-appearance: none;
            padding-right: 38px; padding-left: 44px; font-size: 14px; cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='2.5'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
        }
        .dir-scope .dir-field select.dir-field__control option { color: #0f172a; background: #fff; }

        .dir-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            height: 56px; padding: 0 30px;
            border: none; border-radius: 16px;
            background: var(--accent); color: #fff;
            font-family: inherit; font-size: 14px; font-weight: 800;
            letter-spacing: .04em; text-transform: uppercase;
            cursor: pointer; white-space: nowrap; text-decoration: none;
            transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
        }
        .dir-btn:hover {
            background: var(--accent-dark); color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 14px 26px -12px color-mix(in srgb, var(--accent) 75%, transparent);
        }
        .dir-btn:active { transform: translateY(0); }
        .dir-btn--ghost {
            background: transparent; color: var(--muted);
            border: 1.5px solid var(--line); padding: 0 22px;
        }
        .dir-btn--ghost:hover { background: var(--page); color: var(--accent); border-color: var(--accent); }

        .dir-panel__foot {
            display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
            gap: 12px; margin-top: 14px; padding: 0 6px;
        }
        .dir-count { font-size: 14px; font-weight: 700; color: var(--muted); }
        .dir-count b { color: var(--accent); font-weight: 900; }

        /* ------------------------------------------------------------ */
        /*  Stats strip                                                  */
        /* ------------------------------------------------------------ */
        .dir-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 14px;
        }
        .dir-stat {
            background: var(--surface); border: 1px solid var(--line);
            border-radius: 18px; padding: 20px 22px;
            display: flex; align-items: center; gap: 14px;
        }
        .dir-stat__icon {
            width: 44px; height: 44px; flex: none; border-radius: 13px;
            display: grid; place-items: center;
            background: rgba(15, 23, 42, .07);
            background: color-mix(in srgb, var(--accent) 12%, transparent);
            color: var(--accent);
        }
        .dir-stat__icon .material-symbols-outlined { font-size: 22px; }
        .dir-stat__num { font-size: 26px; font-weight: 900; color: var(--ink); line-height: 1; }
        .dir-stat__label {
            font-size: 11px; font-weight: 800; color: var(--muted);
            text-transform: uppercase; letter-spacing: .06em; margin-top: 4px;
        }

        /* ------------------------------------------------------------ */
        /*  Category pills                                               */
        /* ------------------------------------------------------------ */
        .dir-tabs {
            display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;
        }
        .dir-tab {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 20px; border-radius: 999px;
            background: var(--surface); border: 1.5px solid var(--line);
            color: var(--muted); font-size: 13px; font-weight: 800;
            text-decoration: none; white-space: nowrap;
            transition: all .2s ease;
        }
        .dir-tab:hover { color: var(--accent); border-color: var(--accent); transform: translateY(-2px); }
        .dir-tab.is-active {
            background: var(--accent); border-color: var(--accent); color: #fff;
            box-shadow: 0 12px 24px -14px color-mix(in srgb, var(--accent) 85%, transparent);
        }
        .dir-tab__n {
            font-size: 11px; font-weight: 900; padding: 2px 8px; border-radius: 999px;
            background: rgba(100, 116, 139, .16);
            background: color-mix(in srgb, currentColor 14%, transparent);
        }
        /* The theme colours nested <span>s inside links, so the active pill has
           to restate white on its own children. */
        .dir-tab.is-active,
        .dir-tab.is-active .material-symbols-outlined { color: #fff; }
        .dir-tab.is-active .dir-tab__n { background: rgba(255, 255, 255, .26); color: #fff; }

        /* ------------------------------------------------------------ */
        /*  Section headings                                             */
        /* ------------------------------------------------------------ */
        .dir-section + .dir-section { margin-top: 58px; }
        .dir-section__head {
            display: flex; align-items: center; gap: 16px; margin-bottom: 26px;
        }
        .dir-section__bar { width: 5px; align-self: stretch; min-height: 44px; border-radius: 4px; background: var(--accent); }
        .dir-scope .dir-section__title {
            font-size: clamp(20px, 2.4vw, 28px); font-weight: 700 !important;
            color: var(--ink); line-height: 1.2; margin: 0;
        }
        .dir-section__meta { font-size: 13px; font-weight: 700; color: var(--muted); margin: 4px 0 0; }
        .dir-section__rule { flex: 1; height: 1px; background: var(--line); }

        .dir-scope .dir-subhead {
            font-family: 'Open Sans', system-ui, -apple-system, 'Segoe UI', sans-serif !important;
            font-weight: 900 !important;
            display: flex; align-items: center; gap: 12px;
            margin: 34px 0 18px; font-size: 12px;
            text-transform: uppercase; letter-spacing: .12em; color: var(--muted);
        }
        .dir-subhead::after { content: ''; flex: 1; height: 1px; background: var(--line); }
        .dir-subhead__n {
            font-size: 11px; padding: 3px 9px; border-radius: 999px;
            background: rgba(15, 23, 42, .07);
            background: color-mix(in srgb, var(--accent) 12%, transparent);
            color: var(--accent); letter-spacing: .04em;
        }

        /* ------------------------------------------------------------ */
        /*  Cards                                                        */
        /* ------------------------------------------------------------ */
        .dir-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(178px, 1fr));
            gap: 18px;
        }

        .dir-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 20px;
            overflow: hidden;
            transition: transform .28s cubic-bezier(.4, 0, .2, 1), box-shadow .28s ease, border-color .28s ease;
        }
        .dir-card:hover {
            transform: translateY(-6px);
            border-color: var(--accent);
            border-color: color-mix(in srgb, var(--accent) 55%, transparent);
            box-shadow: 0 22px 44px -22px rgba(15, 23, 42, .45);
        }
        .dir-card__link { display: flex; flex-direction: column; height: 100%; text-decoration: none !important; color: inherit; }

        .dir-card__media {
            position: relative; aspect-ratio: 1 / 1; overflow: hidden;
            background: var(--page);
        }
        .dir-card__media img {
            width: 100%; height: 100%; object-fit: cover; object-position: top center;
            transition: transform .5s ease;
        }
        .dir-card:hover .dir-card__media img { transform: scale(1.06); }
        .dir-card__monogram {
            position: absolute; inset: 0; display: grid; place-items: center;
            background: linear-gradient(140deg, var(--m1), var(--m2));
            color: rgba(255, 255, 255, .95);
            font-size: 42px; font-weight: 900; letter-spacing: .04em;
            transition: transform .5s ease;
        }
        .dir-card:hover .dir-card__monogram { transform: scale(1.05); }

        .dir-rank {
            position: absolute; top: 10px; left: 10px; z-index: 2;
            padding: 4px 9px; border-radius: 7px;
            font-size: 9.5px; font-weight: 900; letter-spacing: .06em;
            text-transform: uppercase; color: #fff;
            background: rgba(15, 23, 42, .8);
            backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, .3);
        }
        .dir-rank--professor,
        .dir-rank--emeritus       { background: linear-gradient(135deg, #a16207, #ca8a04); }
        .dir-rank--assoc-prof     { background: linear-gradient(135deg, #4c1d95, #6d28d9); }
        .dir-rank--senior-lecturer{ background: linear-gradient(135deg, #7f1d1d, #a02020); }
        .dir-rank--lecturer       { background: linear-gradient(135deg, #14532d, #16803c); }
        .dir-rank--assistant-lecturer { background: linear-gradient(135deg, #0f766e, #0d9488); }
        .dir-rank--chancellery    { background: linear-gradient(135deg, #a16207, #ca8a04); }
        .dir-rank--directorate    { background: linear-gradient(135deg, #4c1d95, #6d28d9); }
        .dir-rank--registry       { background: linear-gradient(135deg, #1e3a8a, #1d4ed8); }
        .dir-rank--finance        { background: linear-gradient(135deg, #14532d, #16803c); }
        .dir-rank--library        { background: linear-gradient(135deg, #0c4a6e, #0369a1); }
        .dir-rank--chaplaincy     { background: linear-gradient(135deg, #831843, #be185d); }
        .dir-rank--security       { background: linear-gradient(135deg, #7c2d12, #b45309); }
        .dir-rank--general        { background: linear-gradient(135deg, #334155, #475569); }

        .dir-card__ribbon {
            position: absolute; left: 0; right: 0; bottom: 0; z-index: 2;
            display: flex; align-items: center; gap: 5px;
            padding: 16px 10px 7px;
            background: linear-gradient(to top, rgba(2, 6, 23, .9), transparent);
            color: #fde68a; font-size: 10px; font-weight: 800;
            letter-spacing: .02em; line-height: 1.25;
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .dir-card__ribbon .material-symbols-outlined { font-size: 13px; flex: none; }

        .dir-card__body {
            padding: 14px 14px 15px;
            display: flex; flex-direction: column; gap: 5px; flex: 1;
            text-align: center;
        }
        /* The theme forces Cinzel at weight 500 on every h1–h6 with !important.
           Card names are set in the body face so they stay legible at 14px. */
        .dir-scope .dir-card__name {
            font-family: 'Open Sans', system-ui, -apple-system, 'Segoe UI', sans-serif !important;
            font-weight: 800 !important;
            font-size: 14.5px; color: var(--ink);
            line-height: 1.28; margin: 0; text-transform: none; letter-spacing: 0;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
            overflow: hidden; min-height: 2.36em;
        }
        .dir-card__role {
            font-size: 12px; font-weight: 700; color: var(--accent);
            margin: 0; line-height: 1.3;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .dir-card__dept {
            display: flex; align-items: center; justify-content: center; gap: 4px;
            font-size: 11px; font-weight: 600; color: var(--muted);
            margin: 0; line-height: 1.3;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .dir-card__dept .material-symbols-outlined { font-size: 12px; }
        .dir-card__cta {
            margin-top: auto; padding-top: 11px;
            display: inline-flex; align-items: center; justify-content: center; gap: 4px;
            font-size: 10.5px; font-weight: 900; text-transform: uppercase; letter-spacing: .07em;
            color: var(--muted); transition: color .2s ease, gap .2s ease;
        }
        .dir-card__cta .material-symbols-outlined { font-size: 14px; }
        .dir-card:hover .dir-card__cta { color: var(--accent); gap: 8px; }

        /* ------------------------------------------------------------ */
        /*  Empty state                                                  */
        /* ------------------------------------------------------------ */
        .dir-empty { text-align: center; padding: 70px 20px; }
        .dir-empty__icon {
            width: 84px; height: 84px; margin: 0 auto 22px; border-radius: 50%;
            display: grid; place-items: center;
            background: var(--surface); border: 1px solid var(--line); color: var(--muted);
        }
        .dir-empty__icon .material-symbols-outlined { font-size: 36px; }
        .dir-empty h3 { font-size: 24px; font-weight: 900; color: var(--ink); margin: 0 0 8px; }
        .dir-empty p { color: var(--muted); margin: 0 0 22px; }

        .dir-backtotop {
            position: fixed; right: 22px; bottom: 22px; z-index: 40;
            width: 48px; height: 48px; border-radius: 50%; border: none;
            background: var(--accent); color: #fff; cursor: pointer;
            display: grid; place-items: center;
            box-shadow: 0 12px 26px -10px rgba(15, 23, 42, .6);
            opacity: 0; visibility: hidden; transform: translateY(12px);
            transition: all .25s ease;
        }
        .dir-backtotop.is-visible { opacity: 1; visibility: visible; transform: translateY(0); }
        .dir-backtotop:hover { background: var(--accent-dark); }

        /* ------------------------------------------------------------ */
        /*  Responsive                                                   */
        /* ------------------------------------------------------------ */
        @media (max-width: 1024px) {
            .dir-panel__grid,
            .dir-panel__grid--3 { grid-template-columns: 1fr 1fr; }
            .dir-panel__grid > .dir-btn,
            .dir-panel__grid--3 > .dir-btn { grid-column: 1 / -1; }
        }
        @media (max-width: 640px) {
            .dir-panel { border-radius: 20px; padding: 14px; }
            .dir-panel__grid,
            .dir-panel__grid--3 { grid-template-columns: 1fr; }
            .dir-grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 14px; }
            .dir-card { border-radius: 16px; }
            .dir-card__monogram { font-size: 32px; }
            .dir-section + .dir-section { margin-top: 42px; }
        }
        @media (prefers-reduced-motion: reduce) {
            .dir-card, .dir-card__media img, .dir-card__monogram, .dir-btn, .dir-tab { transition: none !important; }
            .dir-card:hover { transform: none; }
        }
        </style>
        <?php
    }
}

if (!function_exists('vvu_dir_script')) {
    /** Instant client-side narrowing + result counter + back-to-top. */
    function vvu_dir_script(): void
    {
        ?>
        <script>
        (function () {
            var scope = document.querySelector('.dir-scope');
            if (!scope) return;

            var input    = scope.querySelector('[data-dir-search]');
            var counter  = scope.querySelector('[data-dir-count]');
            var cards    = Array.prototype.slice.call(scope.querySelectorAll('.dir-card'));
            var sections = Array.prototype.slice.call(scope.querySelectorAll('[data-dir-section]'));
            var emptyEl  = scope.querySelector('[data-dir-empty]');
            var total    = cards.length;

            function apply() {
                var q = (input && input.value || '').trim().toLowerCase();
                var shown = 0;

                cards.forEach(function (card) {
                    var hit = q === '' || (card.getAttribute('data-search') || '').indexOf(q) !== -1;
                    card.hidden = !hit;
                    if (hit) shown++;
                });

                // Hide any section (and its sub-group) left with nothing in it.
                sections.forEach(function (section) {
                    var visible = section.querySelectorAll('.dir-card:not([hidden])').length;
                    section.hidden = visible === 0;
                    Array.prototype.forEach.call(section.querySelectorAll('[data-dir-group]'), function (group) {
                        group.hidden = group.querySelectorAll('.dir-card:not([hidden])').length === 0;
                    });
                });

                if (counter) counter.innerHTML = '<b>' + shown + '</b> of ' + total + ' listed';
                if (emptyEl) emptyEl.hidden = shown !== 0 || total === 0;
            }

            if (input) {
                var timer;
                input.addEventListener('input', function () {
                    clearTimeout(timer);
                    timer = setTimeout(apply, 120);
                });
                // Enter still submits the form so the filter stays in the URL.
            }

            var top = scope.querySelector('[data-dir-top]');
            if (top) {
                window.addEventListener('scroll', function () {
                    top.classList.toggle('is-visible', window.scrollY > 700);
                }, { passive: true });
                top.addEventListener('click', function () {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }
        })();
        </script>
        <?php
    }
}
