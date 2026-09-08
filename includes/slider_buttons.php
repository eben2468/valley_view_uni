<?php
/**
 * Valley View University - hero slider button placement & styling.
 *
 * One place that owns everything about the buttons that sit on a banner:
 * where they are placed, how they look, how they behave on a phone and how
 * they are turned into HTML. The public homepage and the admin editor both
 * render through the same helpers, so what an editor positions in the admin
 * preview is exactly what a visitor sees.
 *
 * Placement model
 * ---------------
 *   inherit   the buttons stay in the caption, under the title/description
 *             (the historic behaviour, kept for text-led slides)
 *   9 presets the buttons become their own layer, parked in one of the nine
 *             zones of the slide (top/middle/bottom x left/centre/right)
 *   custom    the buttons become their own layer centred on an exact point,
 *             given as a percentage of the slide's width and height
 *
 * Percentages - not pixels - are what make a position hold: the slide image
 * is always width:100%/height:auto, so a point at 12%/78% lands on the same
 * spot of the artwork on a 4K monitor and on a phone.
 *
 * See sql/slider_button_style_migration.sql for the schema.
 */

if (!function_exists('vvu_slide_button_columns')) {
    /**
     * The columns this feature adds to homepage_sliders, with their
     * definitions. Defaults reproduce the look the site had when these
     * values were hard-coded, so old rows need no data migration.
     */
    function vvu_slide_button_columns()
    {
        return [
            'button_position' => "VARCHAR(20) NOT NULL DEFAULT 'inherit'",
            'button_x'        => "DECIMAL(5,2) NOT NULL DEFAULT 50.00",
            'button_y'        => "DECIMAL(5,2) NOT NULL DEFAULT 80.00",
            'button_layout'   => "VARCHAR(10) NOT NULL DEFAULT 'row'",
            'button_size'     => "VARCHAR(10) NOT NULL DEFAULT 'medium'",
            'button_shape'    => "VARCHAR(10) NOT NULL DEFAULT 'pill'",
            'button_mobile'   => "VARCHAR(10) NOT NULL DEFAULT 'same'",
            // A phone is not a small desktop: the buttons may need their own
            // spot on the artwork and their own size there.
            'button_mobile_x'    => "DECIMAL(5,2) NOT NULL DEFAULT 50.00",
            'button_mobile_y'    => "DECIMAL(5,2) NOT NULL DEFAULT 85.00",
            'button_mobile_size'  => "VARCHAR(10) NOT NULL DEFAULT 'inherit'",
            'button_mobile_scale' => "DECIMAL(4,2) NOT NULL DEFAULT 1.00",
            'button1_style'   => "VARCHAR(10) DEFAULT NULL",
            'button1_bg'      => "VARCHAR(9) DEFAULT NULL",
            'button1_color'   => "VARCHAR(9) DEFAULT NULL",
            'button2_style'   => "VARCHAR(10) DEFAULT NULL",
            'button2_bg'      => "VARCHAR(9) DEFAULT NULL",
            'button2_color'   => "VARCHAR(9) DEFAULT NULL",
            'button3_style'   => "VARCHAR(10) DEFAULT NULL",
            'button3_bg'      => "VARCHAR(9) DEFAULT NULL",
            'button3_color'   => "VARCHAR(9) DEFAULT NULL",
        ];
    }
}

if (!function_exists('vvu_slide_buttons_install')) {
    /**
     * Add any missing column. Safe to call on every request; returns false
     * when the database would not allow it, in which case every helper below
     * still works from its defaults.
     */
    function vvu_slide_buttons_install($pdo)
    {
        static $done = null;
        if ($done !== null) {
            return $done;
        }

        try {
            $existing = [];
            foreach ($pdo->query("SHOW COLUMNS FROM homepage_sliders")->fetchAll(PDO::FETCH_ASSOC) as $col) {
                $existing[strtolower($col['Field'])] = true;
            }

            foreach (vvu_slide_button_columns() as $name => $definition) {
                if (!isset($existing[strtolower($name)])) {
                    $pdo->exec("ALTER TABLE homepage_sliders ADD COLUMN `$name` $definition");
                }
            }
            $done = true;
        } catch (PDOException $e) {
            error_log('VVU slider buttons: ' . $e->getMessage());
            $done = false;
        }

        return $done;
    }
}

if (!function_exists('vvu_slide_button_positions')) {
    /** Placement choices offered in the admin, in the order they are shown. */
    function vvu_slide_button_positions()
    {
        return [
            'inherit'       => 'With the text (under the title)',
            'top-left'      => 'Top left',
            'top-center'    => 'Top centre',
            'top-right'     => 'Top right',
            'middle-left'   => 'Middle left',
            'middle-center' => 'Middle centre',
            'middle-right'  => 'Middle right',
            'bottom-left'   => 'Bottom left',
            'bottom-center' => 'Bottom centre',
            'bottom-right'  => 'Bottom right',
            'custom'        => 'Exact spot (drag it on the preview)',
        ];
    }
}

if (!function_exists('vvu_slide_button_option_sets')) {
    /** The remaining admin dropdowns, kept next to the placement list. */
    function vvu_slide_button_option_sets()
    {
        return [
            'layout' => [
                'row'    => 'Side by side',
                'column' => 'Stacked',
            ],
            'size' => [
                'small'  => 'Small',
                'medium' => 'Medium',
                'large'  => 'Large',
            ],
            'shape' => [
                'pill'    => 'Pill',
                'rounded' => 'Rounded',
                'square'  => 'Square',
            ],
            'mobile' => [
                'same'    => 'Keep the same spot',
                'own'     => 'Its own spot (drag in phone view)',
                'bottom'  => 'Move to the bottom',
                'stacked' => 'Stack full width at the bottom',
                'hidden'  => 'Hide on phones',
            ],
            'mobile_size' => [
                'inherit' => 'Same as desktop',
                'xsmall'  => 'Extra small',
                'small'   => 'Small',
                'medium'  => 'Medium',
                'large'   => 'Large',
            ],
            'style' => [
                'solid'   => 'Solid',
                'outline' => 'Outline',
                'glass'   => 'Glass (blurred)',
                'ghost'   => 'Text link',
            ],
        ];
    }
}

if (!function_exists('vvu_slide_button_defaults')) {
    /**
     * Per-button fallback styling - the colours the three buttons had when
     * they were hard-coded in the stylesheet.
     */
    function vvu_slide_button_defaults()
    {
        return [
            1 => ['style' => 'solid',   'bg' => '#ff5722', 'color' => '#ffffff'],
            2 => ['style' => 'outline', 'bg' => '#ffffff', 'color' => '#ffffff'],
            3 => ['style' => 'solid',   'bg' => '#002147', 'color' => '#ffffff'],
        ];
    }
}

if (!function_exists('vvu_slide_button_hex')) {
    /**
     * Accept only a real hex colour; anything else falls back, so a stray
     * value can never break the page or smuggle CSS into the style attribute.
     */
    function vvu_slide_button_hex($value, $fallback)
    {
        $value = trim((string)$value);
        if ($value !== '' && preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value)) {
            return strtolower($value);
        }
        return $fallback;
    }
}

if (!function_exists('vvu_slide_button_ink')) {
    /**
     * Black or white, whichever stays readable on the given colour. Used for
     * the outline hover fill, where the label lands on the border colour.
     */
    function vvu_slide_button_ink($hex)
    {
        $hex = ltrim(vvu_slide_button_hex($hex, '#000000'), '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Perceived brightness (ITU-R BT.601), the usual quick contrast test.
        return (0.299 * $r + 0.587 * $g + 0.114 * $b) > 150 ? '#111111' : '#ffffff';
    }
}

if (!function_exists('vvu_slide_button_rgb')) {
    /** "255, 87, 34" - for the rgba() glow the stylesheet builds. */
    function vvu_slide_button_rgb($hex)
    {
        $hex = ltrim(vvu_slide_button_hex($hex, '#000000'), '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        return hexdec(substr($hex, 0, 2)) . ', ' . hexdec(substr($hex, 2, 2)) . ', ' . hexdec(substr($hex, 4, 2));
    }
}

if (!function_exists('vvu_slide_button_link')) {
    /**
     * Keep links a browser should follow. Anything carrying a scheme we do
     * not know (javascript:, data:) becomes '#'.
     */
    function vvu_slide_button_link($link)
    {
        $link = trim((string)$link);
        if ($link === '') {
            return '#';
        }
        if (preg_match('/^[a-z][a-z0-9+.\-]*:/i', $link)) {
            return preg_match('#^(https?://|mailto:|tel:)#i', $link) ? $link : '#';
        }
        // Relative path, anchor or query string - fine as it is.
        return $link;
    }
}

if (!function_exists('vvu_slide_button_clamp_percent')) {
    /** Keep a custom coordinate on the slide, with room for the button itself. */
    function vvu_slide_button_clamp_percent($value, $fallback)
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return $fallback;
        }
        return round(max(2, min(98, (float)$value)), 2);
    }
}

if (!function_exists('vvu_slide_button_scale')) {
    /**
     * The phone fine-tune, as a multiplier. Bounded because below ~0.6 the
     * label stops being readable and above ~1.4 the button owns the slide.
     */
    function vvu_slide_button_scale($value)
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return 1.0;
        }
        return round(max(0.6, min(1.4, (float)$value)), 2);
    }
}

if (!function_exists('vvu_slide_button_config')) {
    /**
     * Turn a homepage_sliders row into everything the renderer needs, with
     * every value already validated. Also works on rows read before the
     * columns existed, so callers never have to check.
     */
    function vvu_slide_button_config(array $slider)
    {
        $options   = vvu_slide_button_option_sets();
        $defaults  = vvu_slide_button_defaults();
        $positions = vvu_slide_button_positions();

        $pick = function ($value, array $allowed, $fallback) {
            $value = strtolower(trim((string)$value));
            return isset($allowed[$value]) ? $value : $fallback;
        };

        $buttons = [];
        foreach ([1, 2, 3] as $i) {
            $text = trim((string)($slider["button{$i}_text"] ?? ''));
            if ($text === '') {
                continue;
            }
            $bg = vvu_slide_button_hex($slider["button{$i}_bg"] ?? null, $defaults[$i]['bg']);
            $buttons[] = [
                'index' => $i,
                'text'  => $text,
                'link'  => vvu_slide_button_link($slider["button{$i}_link"] ?? ''),
                'style' => $pick($slider["button{$i}_style"] ?? '', $options['style'], $defaults[$i]['style']),
                'bg'    => $bg,
                'fg'    => vvu_slide_button_hex($slider["button{$i}_color"] ?? null, $defaults[$i]['color']),
                'ink'   => vvu_slide_button_ink($bg),
                'rgb'   => vvu_slide_button_rgb($bg),
            ];
        }

        return [
            'position' => $pick($slider['button_position'] ?? '', $positions, 'inherit'),
            'x'        => vvu_slide_button_clamp_percent($slider['button_x'] ?? null, 50),
            'y'        => vvu_slide_button_clamp_percent($slider['button_y'] ?? null, 80),
            'layout'   => $pick($slider['button_layout'] ?? '', $options['layout'], 'row'),
            'size'     => $pick($slider['button_size'] ?? '', $options['size'], 'medium'),
            'shape'    => $pick($slider['button_shape'] ?? '', $options['shape'], 'pill'),
            'mobile'   => $pick($slider['button_mobile'] ?? '', $options['mobile'], 'same'),
            'mobile_x' => vvu_slide_button_clamp_percent($slider['button_mobile_x'] ?? null, 50),
            'mobile_y' => vvu_slide_button_clamp_percent($slider['button_mobile_y'] ?? null, 85),
            'mobile_size' => $pick($slider['button_mobile_size'] ?? '', $options['mobile_size'], 'inherit'),
            'mobile_scale' => vvu_slide_button_scale($slider['button_mobile_scale'] ?? null),
            'buttons'  => $buttons,
        ];
    }
}

if (!function_exists('vvu_slide_buttons_group_html')) {
    /**
     * The row (or column) of buttons on its own, without a placement wrapper.
     * This is what goes inside the caption when the placement is "inherit".
     */
    function vvu_slide_buttons_group_html(array $config)
    {
        if (empty($config['buttons'])) {
            return '';
        }

        $e = function ($v) {
            return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
        };

        $classes = 'vvu-slide-btns lay-' . $config['layout']
                 . ' size-' . $config['size']
                 . ' shape-' . $config['shape'];

        if (($config['mobile_size'] ?? 'inherit') !== 'inherit') {
            $classes .= ' msize-' . $config['mobile_size'];
        }

        // The fine-tune only ever multiplies the phone sizes, so it is written
        // out only when it is actually doing something.
        $scale = $config['mobile_scale'] ?? 1.0;
        $style = ($scale != 1.0) ? ' style="--btn-mscale:' . $e($scale) . '"' : '';

        $html = '<div class="' . $e($classes) . '"' . $style . '>';
        foreach ($config['buttons'] as $button) {
            $vars = '--btn-bg:' . $button['bg']
                  . ';--btn-fg:' . $button['fg']
                  . ';--btn-ink:' . $button['ink']
                  . ';--btn-rgb:' . $button['rgb'];
            $html .= '<a class="vvu-slide-btn is-' . $e($button['style']) . '"'
                   . ' href="' . $e($button['link']) . '"'
                   . ' style="' . $e($vars) . '">'
                   . '<span>' . $e($button['text']) . '</span>'
                   . '</a>';
        }
        $html .= '</div>';

        return $html;
    }
}

if (!function_exists('vvu_slide_buttons_html')) {
    /**
     * The buttons ready to drop into a slide.
     *
     * With placement "inherit" this returns the bare group and the caller
     * puts it inside the caption. With any other placement it returns a
     * layer that covers the whole slide, so the buttons land on the exact
     * spot of the artwork no matter what the caption is doing.
     */
    function vvu_slide_buttons_html(array $slider)
    {
        $config = isset($slider['buttons']) && is_array($slider['buttons'])
            ? $slider
            : vvu_slide_button_config($slider);
        $group = vvu_slide_buttons_group_html($config);

        if ($group === '' || $config['position'] === 'inherit') {
            return $group;
        }

        $e = function ($v) {
            return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
        };

        $mobile = $config['mobile'];
        $styles = [];

        if ($config['position'] === 'custom') {
            $classes = 'vvu-btn-layer at-custom';
            $styles[] = '--btn-x:' . $config['x'] . '%';
            $styles[] = '--btn-y:' . $config['y'] . '%';
        } else {
            $classes = 'vvu-btn-layer at-' . $config['position'];
        }

        if ($mobile === 'own') {
            $styles[] = '--btn-mx:' . $config['mobile_x'] . '%';
            $styles[] = '--btn-my:' . $config['mobile_y'] . '%';
        }

        $classes .= ' mob-' . $mobile;

        return '<div class="' . $e($classes) . '"'
             . (!empty($styles) ? ' style="' . $e(implode(';', $styles)) . '"' : '')
             . '>' . $group . '</div>';
    }
}
