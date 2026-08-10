<?php
/**
 * Comprehensive diagnostic tool for Campus Life CMS
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/db_connect.php';
require_once 'includes/campus_life_content_helper.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Campus Life CMS - Diagnostics</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        h2 { color: #666; margin-top: 30px; background: #f0f0f0; padding: 10px; }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .warning { color: #ff9800; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
        th { background: #4CAF50; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .code { background: #f5f5f5; padding: 10px; border-left: 4px solid #2196F3; margin: 10px 0; font-family: monospace; }
        .btn { display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>🔍 Campus Life CMS - Complete Diagnostics</h1>";

// 1. Database Connection
echo "<h2>1. Database Connection</h2>";
try {
    $pdo->query("SELECT 1");
    echo "<p class='success'>✓ Database connection successful</p>";
} catch (PDOException $e) {
    echo "<p class='error'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// 2. Check Tables
echo "<h2>2. Database Tables</h2>";
$tables = [
    'philosophy_on_dress_content',
    'accommodation_content',
    'food_services_content',
    'work_study_content',
    'sld_content'
];

echo "<table>";
echo "<tr><th>Table Name</th><th>Exists</th><th>Records</th><th>Sample Data</th></tr>";

foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
        $count = $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT hero_title, hero_image, status FROM `$table` WHERE id = 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $sample = $row ? "Title: " . strip_tags(substr($row['hero_title'], 0, 30)) . "...<br>Image: " . strip_tags($row['hero_image']) . "<br>Status: " . $row['status'] : "No data";
        
        echo "<tr>";
        echo "<td>$table</td>";
        echo "<td class='success'>✓ Yes</td>";
        echo "<td>$count</td>";
        echo "<td style='font-size: 11px;'>$sample</td>";
        echo "</tr>";
    } catch (PDOException $e) {
        echo "<tr>";
        echo "<td>$table</td>";
        echo "<td class='error'>✗ No</td>";
        echo "<td>-</td>";
        echo "<td class='error'>" . $e->getMessage() . "</td>";
        echo "</tr>";
    }
}

echo "</table>";

// 3. Test Helper Functions
echo "<h2>3. Helper Functions Test</h2>";

$tests = [
    'Philosophy on Dress' => ['function' => 'getPhilosophyOnDressContent', 'url' => 'philosophy_on_dress.php'],
    'Accommodation' => ['function' => 'getAccommodationContent', 'url' => 'accommodation.php'],
    'Food Services' => ['function' => 'getFoodServicesContent', 'url' => 'food_services.php'],
    'Work Study' => ['function' => 'getWorkStudyContent', 'url' => 'work_study.php'],
    'SLD' => ['function' => 'getSLDContent', 'url' => 'sld.php']
];

echo "<table>";
echo "<tr><th>Page</th><th>Function</th><th>Status</th><th>Hero Title</th><th>Hero Image</th><th>Action</th></tr>";

foreach ($tests as $pageName => $test) {
    try {
        $function = $test['function'];
        $content = $function($pdo);
        
        if ($content && !empty($content['hero_title'])) {
            echo "<tr>";
            echo "<td>$pageName</td>";
            echo "<td class='success'>$function()</td>";
            echo "<td class='success'>✓ Working</td>";
            echo "<td>" . strip_tags($content['hero_title']) . "</td>";
            echo "<td style='font-size: 11px;'>" . strip_tags($content['hero_image']) . "</td>";
            echo "<td><a href='" . $test['url'] . "' target='_blank' class='btn' style='padding: 5px 10px; font-size: 12px;'>View Page</a></td>";
            echo "</tr>";
        } else {
            echo "<tr>";
            echo "<td>$pageName</td>";
            echo "<td class='warning'>$function()</td>";
            echo "<td class='warning'>⚠ No Content</td>";
            echo "<td colspan='3'>Content exists but is empty or inactive</td>";
            echo "</tr>";
        }
    } catch (Exception $e) {
        echo "<tr>";
        echo "<td>$pageName</td>";
        echo "<td class='error'>$function()</td>";
        echo "<td class='error'>✗ Error</td>";
        echo "<td colspan='3'>" . $e->getMessage() . "</td>";
        echo "</tr>";
    }
}

echo "</table>";

// 4. Check Image Paths
echo "<h2>4. Image Path Verification</h2>";
echo "<table>";
echo "<tr><th>Page</th><th>Image Field</th><th>Path</th><th>File Exists</th></tr>";

foreach ($tests as $pageName => $test) {
    try {
        $function = $test['function'];
        $content = $function($pdo);
        
        if ($content) {
            // Check hero image
            $heroPath = $content['hero_image'];
            $heroExists = !empty($heroPath) && (file_exists($heroPath) || strpos($heroPath, 'http') === 0);
            
            echo "<tr>";
            echo "<td>$pageName</td>";
            echo "<td>Hero Image</td>";
            echo "<td style='font-size: 11px;'>" . strip_tags($heroPath) . "</td>";
            echo "<td>" . ($heroExists ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . "</td>";
            echo "</tr>";
            
            // Check intro/welcome/philosophy image if exists
            $secondImage = $content['intro_image'] ?? $content['welcome_image'] ?? $content['philosophy_image'] ?? $content['overview_image'] ?? '';
            if (!empty($secondImage)) {
                $secondExists = file_exists($secondImage) || strpos($secondImage, 'http') === 0;
                echo "<tr>";
                echo "<td>$pageName</td>";
                echo "<td>Secondary Image</td>";
                echo "<td style='font-size: 11px;'>" . strip_tags($secondImage) . "</td>";
                echo "<td>" . ($secondExists ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . "</td>";
                echo "</tr>";
            }
        }
    } catch (Exception $e) {
        echo "<tr><td>$pageName</td><td colspan='3' class='error'>Error: " . $e->getMessage() . "</td></tr>";
    }
}

echo "</table>";

// 5. Test Actual Page Rendering
echo "<h2>5. Page Rendering Test</h2>";
echo "<p>Click each link to test if the page loads correctly:</p>";
echo "<div style='display: flex; gap: 10px; flex-wrap: wrap;'>";
foreach ($tests as $pageName => $test) {
    echo "<a href='" . $test['url'] . "' target='_blank' class='btn'>Test $pageName</a>";
}
echo "</div>";

// 6. Admin Panel Links
echo "<h2>6. Admin Panel Access</h2>";
echo "<div style='display: flex; gap: 10px; flex-wrap: wrap;'>";
echo "<a href='admin/manage_campus_life_pages.php' target='_blank' class='btn'>Open Admin Panel</a>";
echo "<a href='admin/manage_campus_life_pages.php?page=accommodation' target='_blank' class='btn'>Edit Accommodation</a>";
echo "<a href='admin/manage_campus_life_pages.php?page=food_services' target='_blank' class='btn'>Edit Food Services</a>";
echo "<a href='admin/manage_campus_life_pages.php?page=work_study' target='_blank' class='btn'>Edit Work Study</a>";
echo "<a href='admin/manage_campus_life_pages.php?page=sld' target='_blank' class='btn'>Edit SLD</a>";
echo "</div>";

// 7. Quick Fixes
echo "<h2>7. Quick Fixes</h2>";
echo "<div style='display: flex; gap: 10px; flex-wrap: wrap;'>";
echo "<a href='fix_campus_life_content.php' class='btn' style='background: #ff9800;'>Run Content Fix</a>";
echo "<a href='install_campus_life_cms.php' class='btn' style='background: #2196F3;'>Reinstall Database</a>";
echo "</div>";

// 8. Sample Code
echo "<h2>8. Sample Frontend Code</h2>";
echo "<p>This is how the pages fetch content:</p>";
echo "<div class='code'>";
echo strip_tags('<?php
require_once \'includes/campus_life_content_helper.php\';
$content = getAccommodationContent($pdo);
echo $content[\'hero_title\']; // Displays the title
echo $content[\'hero_image\']; // Displays the image path
?>');
echo "</div>";

// 9. Summary
echo "<h2>9. Summary & Recommendations</h2>";

$allGood = true;
foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
        $count = $stmt->fetchColumn();
        if ($count == 0) {
            $allGood = false;
            break;
        }
    } catch (PDOException $e) {
        $allGood = false;
        break;
    }
}

if ($allGood) {
    echo "<p class='success'>✓ All systems operational! Your CMS is ready to use.</p>";
    echo "<p>Next steps:</p>";
    echo "<ol>";
    echo "<li>Go to the <a href='admin/manage_campus_life_pages.php'>Admin Panel</a></li>";
    echo "<li>Edit content for each page</li>";
    echo "<li>Upload images</li>";
    echo "<li>View changes on frontend pages</li>";
    echo "</ol>";
} else {
    echo "<p class='error'>⚠ Some issues detected. Please:</p>";
    echo "<ol>";
    echo "<li>Run the <a href='fix_campus_life_content.php'>Content Fix Script</a></li>";
    echo "<li>If that doesn't work, <a href='install_campus_life_cms.php'>Reinstall the Database</a></li>";
    echo "<li>Check the error messages above for specific issues</li>";
    echo "</ol>";
}

echo "</div>
</body>
</html>";
?>
