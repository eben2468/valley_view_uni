<?php
/**
 * Administration Content Helper Class
 * Provides methods to fetch and manage administration page content from database
 */

class AdministrationContent {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Get page data by slug
     */
    public function getPageBySlug($slug) {
        $stmt = $this->pdo->prepare("SELECT * FROM administration_pages WHERE page_slug = ? AND is_active = 1");
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all content for a page organized by sections
     */
    public function getPageContent($page_id) {
        $stmt = $this->pdo->prepare("
            SELECT c.*, 
                   cf.id as field_id,
                   cf.field_key,
                   cf.field_value,
                   cf.field_type
            FROM administration_content c
            LEFT JOIN administration_content_fields cf ON c.id = cf.content_id
            WHERE c.page_id = ? AND c.is_active = 1
            ORDER BY c.content_order ASC, cf.id ASC
        ");
        $stmt->execute([$page_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Organize by sections
        $sections = [];
        foreach ($results as $row) {
            $section_key = $row['section_key'];
            if (!isset($sections[$section_key])) {
                $sections[$section_key] = [
                    'id' => $row['id'],
                    'section_type' => $row['section_type'],
                    'section_key' => $row['section_key'],
                    'content_order' => $row['content_order'],
                    'fields' => []
                ];
            }
            
            if ($row['field_key']) {
                $sections[$section_key]['fields'][$row['field_key']] = $row['field_value'];
            }
        }
        
        return $sections;
    }
    
    /**
     * Get a specific section's content
     */
    public function getSection($page_id, $section_key) {
        $content = $this->getPageContent($page_id);
        return isset($content[$section_key]) ? $content[$section_key] : null;
    }
    
    /**
     * Get a specific field value
     */
    public function getField($page_id, $section_key, $field_key, $default = '') {
        $section = $this->getSection($page_id, $section_key);
        if ($section && isset($section['fields'][$field_key])) {
            return $section['fields'][$field_key];
        }
        return $default;
    }
    
    /**
     * Get all fields for a section as an associative array
     */
    public function getSectionFields($page_id, $section_key) {
        $section = $this->getSection($page_id, $section_key);
        return $section ? $section['fields'] : [];
    }
    
    /**
     * Clean HTML content - strips tags and decodes entities
     * Use this for displaying content that was edited with rich text editor
     */
    public static function cleanHtml($content) {
        if (empty($content)) {
            return $content;
        }
        
        // Decode HTML entities first
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Strip all HTML tags
        $content = strip_tags($content);
        
        // Remove excessive whitespace
        $content = preg_replace('/\s+/', ' ', $content);
        
        // Trim
        $content = trim($content);
        
        return $content;
    }
}
?>
