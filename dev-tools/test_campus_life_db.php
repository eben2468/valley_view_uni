<?php
/**
 * Test script to verify Campus Life CMS database connection and content
 */

require_once 'includes/db_connect.php';
require_once 'includes/campus_life_content_helper.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Campus Life CMS - Database Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        h2 { color: #666; margin-top: 30px; }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .info { background: #e3f2fd; padding: 15px; border-left: 4px solid #2196F3; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #4CAF50; color: white; }
        tr:hover { background: #f5f5f5; }
        .btn { display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
        .btn:hover { background: #45a049; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>🎓 Campus Life CMS - Database Connection Test</h1>";

// Test database connection
try {
    $pdo->query("SELECT 1");
    echo "<p class='success'>✓ Database connection successful!</p>";
} catch (PDOException $e) {
    echo "<p class='error'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Check if tables exist
$tables = [
    'philosophy_on_dress_content',
    'accommodation_content',
    'food_services_content',
    'work_study_content',
    'sld_content'
];

echo "<h2>📊 Database Tables Status</h2>";
echo "<table>";
echo "<tr><th>Table Name</th><th>Status</th><th>Records</th></tr>";

foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
        $count = $stmt->fetchColumn();
        echo "<tr><td>$table</td><td class='success'>✓ Exists</td><td>$count</td></tr>";
    } catch (PDOException $e) {
        echo "<tr><td>$table</td><td class='error'>✗ Missing</td><td>-</td></tr>";
    }
}

echo "</table>";

// Test content retrieval
echo "<h2>📄 Content Retrieval Test</h2>";

$pages = [
    'Philosophy on Dress' => 'getPhilosophyOnDressContent',
    'Accommodation' => 'getAccommodationContent',
    'Food Services' => 'getFoodServicesContent',
    'Work Study' => 'getWorkStudyContent',
    'SLD' => 'getSLDContent'
];

echo "<table>";
echo "<tr><th>Page</th><th>Status</th><th>Hero Title</th></tr>";

foreach ($pages as $pageName => $function) {
    try {
        $content = $function($pdo);
        if ($content) {
            echo "<tr><td>$pageName</td><td class='success'>✓ Content Found</td><td>" . strip_tags($content['hero_title']) . "</td></tr>";
        } else {
            echo "<tr><td>$pageName</td><td class='error'>✗ No Content</td><td>-</td></tr>";
        }
    } catch (Exception $e) {
        echo "<tr><td>$pageName</td><td class='error'>✗ Error</td><td>" . $e->getMessage() . "</td></tr>";
    }
}

echo "</table>";

// Show sample content
echo "<h2>📝 Sample Content (Philosophy on Dress)</h2>";
try {
    $content = getPhilosophyOnDressContent($pdo);
    if ($content) {
        echo "<div class='info'>";
        echo "<strong>Hero Title:</strong> " . strip_tags($content['hero_title']) . "<br>";
        echo "<strong>Hero Subtitle:</strong> " . strip_tags($content['hero_subtitle']) . "<br>";
        echo "<strong>Hero Image:</strong> " . strip_tags($content['hero_image']) . "<br>";
        echo "<strong>Status:</strong> " . strip_tags($content['status']) . "<br>";
        echo "<strong>Last Updated:</strong> " . strip_tags($content['updated_at']) . "<br>";
        echo "</div>";
    } else {
        echo "<p class='error'>No content found. Please run the installation script.</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}

// Action buttons
echo "<h2>🚀 Next Steps</h2>";
echo "<div class='info'>";

$allTablesExist = true;
foreach ($tables as $table) {
    try {
        $pdo->query("SELECT 1 FROM `$table` LIMIT 1");
    } catch (PDOException $e) {
        $allTablesExist = false;
        break;
    }
}

if (!$allTablesExist) {
    echo "<p><strong>⚠️ Some tables are missing!</strong></p>";
    echo "<p>Please run the installation script to create the database tables:</p>";
    echo "<a href='install_campus_life_cms.php' class='btn'>Run Installation Script</a>";
} else {
    echo "<p><strong>✓ All tables exist!</strong></p>";
    echo "<p>You can now:</p>";
    echo "<a href='admin/manage_campus_life_pages.php' class='btn'>Go to Admin Panel</a>";
    echo "<a href='philosophy_on_dress.php' class='btn'>View Philosophy on Dress Page</a>";
    echo "<a href='accommodation.php' class='btn'>View Accommodation Page</a>";
}

echo "</div>";

echo "</div>
</body>
</html>";
?>
