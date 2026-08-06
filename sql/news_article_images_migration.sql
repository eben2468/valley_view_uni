-- =====================================================
-- Valley View University - News Article Gallery Images
-- Adds support for multiple additional images per news
-- article. These are displayed in a gallery at the end
-- of the article on news_detail.php.
-- =====================================================

CREATE TABLE IF NOT EXISTS news_article_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    image_path VARCHAR(1000) NOT NULL,
    caption VARCHAR(500) DEFAULT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_article (article_id),
    INDEX idx_article_order (article_id, display_order),
    CONSTRAINT fk_news_article_images_article
        FOREIGN KEY (article_id) REFERENCES news_articles(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
