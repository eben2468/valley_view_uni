<?php
/**
 * Campus Life Content Helper Functions
 * Provides functions to fetch content from database for campus life pages
 */

/**
 * Get Philosophy on Dress content
 */
function getPhilosophyOnDressContent($pdo) {
    $stmt = $pdo->query("SELECT * FROM philosophy_on_dress_content WHERE id = 1 AND status = 'active'");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get Philosophy on Dress principles
 */
function getPhilosophyPrinciples($pdo) {
    $stmt = $pdo->query("SELECT * FROM philosophy_dress_principles WHERE status = 'active' ORDER BY display_order ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get Philosophy on Dress benefits
 */
function getPhilosophyBenefits($pdo) {
    $stmt = $pdo->query("SELECT * FROM philosophy_dress_benefits ORDER BY display_order ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get Accommodation content
 */
function getAccommodationContent($pdo) {
    $stmt = $pdo->query("SELECT * FROM accommodation_content WHERE id = 1 AND status = 'active'");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get Accommodation halls
 */
function getAccommodationHalls($pdo, $type = null) {
    if ($type) {
        $stmt = $pdo->prepare("SELECT * FROM accommodation_halls WHERE type = ? ORDER BY display_order ASC");
        $stmt->execute([$type]);
    } else {
        $stmt = $pdo->query("SELECT * FROM accommodation_halls ORDER BY type, display_order ASC");
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get Accommodation features
 */
function getAccommodationFeatures($pdo) {
    $stmt = $pdo->query("SELECT * FROM accommodation_features WHERE status = 'active' ORDER BY display_order ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get Food Services content
 */
function getFoodServicesContent($pdo) {
    $stmt = $pdo->query("SELECT * FROM food_services_content WHERE id = 1 AND status = 'active'");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get Food Services features
 */
function getFoodServicesFeatures($pdo) {
    $stmt = $pdo->query("SELECT * FROM food_services_features WHERE status = 'active' ORDER BY display_order ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get Work Study content
 */
function getWorkStudyContent($pdo) {
    $stmt = $pdo->query("SELECT * FROM work_study_content WHERE id = 1 AND status = 'active'");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get Work Study benefits
 */
function getWorkStudyBenefits($pdo) {
    $stmt = $pdo->query("SELECT * FROM work_study_benefits WHERE status = 'active' ORDER BY display_order ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get Work Study opportunities by category
 */
function getWorkStudyOpportunities($pdo, $category = null) {
    if ($category) {
        $stmt = $pdo->prepare("SELECT * FROM work_study_opportunities WHERE category = ? AND status = 'active' ORDER BY display_order ASC");
        $stmt->execute([$category]);
    } else {
        $stmt = $pdo->query("SELECT * FROM work_study_opportunities WHERE status = 'active' ORDER BY category, display_order ASC");
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get Work Study steps
 */
function getWorkStudySteps($pdo) {
    $stmt = $pdo->query("SELECT * FROM work_study_steps ORDER BY step_number ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get SLD (Spiritual Life & Development) content
 */
function getSLDContent($pdo) {
    $stmt = $pdo->query("SELECT * FROM sld_content WHERE id = 1 AND status = 'active'");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get SLD services
 */
function getSLDServices($pdo) {
    $stmt = $pdo->query("SELECT * FROM sld_services WHERE status = 'active' ORDER BY display_order ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get SLD staff members
 */
function getSLDStaff($pdo) {
    $stmt = $pdo->query("SELECT * FROM sld_staff WHERE status = 'active' ORDER BY display_order ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get SLD locations
 */
function getSLDLocations($pdo) {
    $stmt = $pdo->query("SELECT * FROM sld_locations ORDER BY display_order ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Parse line-separated items into array
 */
function parseLineItems($text) {
    if (empty($text)) {
        return [];
    }
    return array_filter(array_map('trim', explode("\n", $text)));
}

/**
 * Check if content exists for a page
 */
function hasContent($pdo, $table, $id = 1) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn() > 0;
}
?>
