<?php
/**
 * Navigation Data Helper
 * Fetches navigation content from the database for the dynamic header.
 */

// Function to get topbar settings
function getTopbarSettings($pdo) {
    try {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM topbar_settings");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (Exception $e) {
        return [];
    }
}

// Function to get navigation items by type
function getNavItems($pdo, $type = 'main') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM navigation_items WHERE menu_type = ? AND is_active = 1 ORDER BY sort_order ASC");
        $stmt->execute([$type]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // If it's main menu, we need sections and links
        if ($type === 'main') {
            foreach ($items as &$item) {
                if ($item['has_megamenu']) {
                    $item['sections'] = getNavSections($pdo, $item['id']);
                }
            }
        }
        return $items;
    } catch (Exception $e) {
        return [];
    }
}

// Function to get sections for a navigation item
function getNavSections($pdo, $itemId) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM navigation_sections WHERE navigation_item_id = ? AND is_active = 1 ORDER BY sort_order ASC");
        $stmt->execute([$itemId]);
        $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($sections as &$section) {
            if ($section['section_type'] === 'links') {
                $section['links'] = getNavLinks($pdo, $section['id']);
            }
        }
        return $sections;
    } catch (Exception $e) {
        return [];
    }
}

// Function to get links for a section
function getNavLinks($pdo, $sectionId) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM navigation_links WHERE section_id = ? AND is_active = 1 ORDER BY sort_order ASC");
        $stmt->execute([$sectionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}
?>
