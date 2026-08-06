-- =====================================================
-- Valley View University - Hero Slider Timing Controls
--
-- Adds admin control over how long each hero image stays
-- on screen before the slider advances.
--   * homepage_slider_settings — site-wide default
--   * homepage_sliders.slide_interval — optional per-slide override
--     (NULL / 0 = use the site-wide default)
-- =====================================================

CREATE TABLE IF NOT EXISTS homepage_slider_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    interval_seconds INT NOT NULL DEFAULT 5,
    pause_on_hover TINYINT(1) NOT NULL DEFAULT 1,
    autoplay TINYINT(1) NOT NULL DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO homepage_slider_settings (id, interval_seconds, pause_on_hover, autoplay)
SELECT 1, 5, 1, 1
WHERE NOT EXISTS (SELECT 1 FROM homepage_slider_settings WHERE id = 1);

-- Per-slide override (run once; MySQL has no ADD COLUMN IF NOT EXISTS
-- before 8.0, so ignore the duplicate-column error if re-run)
ALTER TABLE homepage_sliders
    ADD COLUMN slide_interval INT DEFAULT NULL COMMENT 'Seconds this slide stays on screen; NULL = use site default';
