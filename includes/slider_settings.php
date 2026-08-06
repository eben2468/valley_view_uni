<?php
/**
 * Valley View University - Hero slider timing
 *
 * One place to create, read and normalise the slider timing settings so the
 * public homepage and the admin screens can never disagree about them.
 * See sql/slider_timing_migration.sql for the schema.
 */

if (!function_exists('vvu_slider_install')) {
    /**
     * Create the settings table / column if they are not there yet.
     * Safe to call on every request; returns false if the database
     * would not allow it (the caller then falls back to defaults).
     */
    function vvu_slider_install($pdo)
    {
        try {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS homepage_slider_settings (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    interval_seconds INT NOT NULL DEFAULT 5,
                    pause_on_hover TINYINT(1) NOT NULL DEFAULT 1,
                    autoplay TINYINT(1) NOT NULL DEFAULT 1,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            $has_row = $pdo->query("SELECT COUNT(*) FROM homepage_slider_settings WHERE id = 1")->fetchColumn();
            if (!$has_row) {
                $pdo->exec("INSERT INTO homepage_slider_settings (id, interval_seconds, pause_on_hover, autoplay) VALUES (1, 5, 1, 1)");
            }

            // Per-slide override column
            $col = $pdo->query("SHOW COLUMNS FROM homepage_sliders LIKE 'slide_interval'")->fetch();
            if (!$col) {
                $pdo->exec("ALTER TABLE homepage_sliders ADD COLUMN slide_interval INT DEFAULT NULL");
            }
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}

if (!function_exists('vvu_slider_settings')) {
    /**
     * Current slider timing, always returning a usable array even if the
     * table is missing.
     *
     * interval_seconds  how long each slide is shown (1–60)
     * pause_on_hover    stop the timer while the pointer is over the slider
     * autoplay          advance automatically at all
     */
    function vvu_slider_settings($pdo)
    {
        $defaults = [
            'interval_seconds' => 5,
            'pause_on_hover'   => 1,
            'autoplay'         => 1,
        ];

        try {
            $row = $pdo->query("SELECT interval_seconds, pause_on_hover, autoplay FROM homepage_slider_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $row = null;
        }

        if (!$row) {
            return $defaults;
        }

        return [
            'interval_seconds' => vvu_slider_clamp($row['interval_seconds'], 5),
            'pause_on_hover'   => (int)$row['pause_on_hover'] ? 1 : 0,
            'autoplay'         => (int)$row['autoplay'] ? 1 : 0,
        ];
    }
}

if (!function_exists('vvu_slider_clamp')) {
    /**
     * Keep a duration inside sane bounds: below ~1.5s a slide is unreadable,
     * above 60s the slider looks broken.
     */
    function vvu_slider_clamp($seconds, $fallback = 5)
    {
        $seconds = (int)$seconds;
        if ($seconds < 1) {
            return $fallback;
        }
        if ($seconds > 60) {
            return 60;
        }
        return $seconds;
    }
}

if (!function_exists('vvu_slider_save')) {
    /**
     * Persist the site-wide timing. Returns the values actually stored.
     */
    function vvu_slider_save($pdo, $interval_seconds, $pause_on_hover, $autoplay)
    {
        $interval = vvu_slider_clamp($interval_seconds, 5);
        $pause    = $pause_on_hover ? 1 : 0;
        $auto     = $autoplay ? 1 : 0;

        $stmt = $pdo->prepare("
            INSERT INTO homepage_slider_settings (id, interval_seconds, pause_on_hover, autoplay)
            VALUES (1, ?, ?, ?)
            ON DUPLICATE KEY UPDATE interval_seconds = VALUES(interval_seconds),
                                    pause_on_hover  = VALUES(pause_on_hover),
                                    autoplay        = VALUES(autoplay)
        ");
        $stmt->execute([$interval, $pause, $auto]);

        return [
            'interval_seconds' => $interval,
            'pause_on_hover'   => $pause,
            'autoplay'         => $auto,
        ];
    }
}
