<?php
/**
 * Fix missing image paths for Campus Life pages
 */

require_once 'includes/db_connect.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Fix Missing Images</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        h1 { color: #333; }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .btn { display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>🔧 Fixing Missing Images</h1>";

// Fix Accommodation - use a placeholder or existing image
try {
    $stmt = $pdo->prepare("UPDATE accommodation_content SET hero_image = ? WHERE id = 1");
    $stmt->execute(['https://images.unsplash.com/photo-1555854877-bab0e564b8d5?auto=format&fit=crop&q=80&w=1920']);
    echo "<p class='success'>✓ Fixed Accommodation hero image</p>";
} catch (PDOException $e) {
    echo "<p class='error'>✗ Error fixing Accommodation: " . $e->getMessage() . "</p>";
}

// Fix SLD - add a proper image
try {
    $stmt = $pdo->prepare("UPDATE sld_content SET hero_image = ? WHERE id = 1");
    $stmt->execute(['https://images.unsplash.com/photo-1438232992991-995b7058bbb3?auto=format&fit=crop&q=80&w=1920']);
    echo "<p class='success'>✓ Fixed SLD hero image</p>";
} catch (PDOException $e) {
    echo "<p class='error'>✗ Error fixing SLD: " . $e->getMessage() . "</p>";
}

// Also ensure all other images are set properly
try {
    // Accommodation intro image
    $stmt = $pdo->prepare("UPDATE accommodation_content SET intro_image = ? WHERE id = 1 AND (intro_image IS NULL OR intro_image = '')");
    $stmt->execute(['https://images.unsplash.com/photo-1555854877-bab0e564b8d5?auto=format&fit=crop&q=80&w=1200']);
    
    // SLD welcome image
    $stmt = $pdo->prepare("UPDATE sld_content SET welcome_image = ? WHERE id = 1 AND (welcome_image IS NULL OR welcome_image = '')");
    $stmt->execute(['https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=1200']);
    
    echo "<p class='success'>✓ Updated secondary images</p>";
} catch (PDOException $e) {
    echo "<p class='error'>✗ Error updating secondary images: " . $e->getMessage() . "</p>";
}

echo "<h2>✅ Image Paths Fixed!</h2>";
echo "<p>All pages should now display properly with working images.</p>";

echo "<h3>Test Your Pages:</h3>";
echo "<a href='accommodation.php' target='_blank' class='btn'>View Accommodation</a>";
echo "<a href='food_services.php' target='_blank' class='btn'>View Food Services</a>";
echo "<a href='work_study.php' target='_blank' class='btn'>View Work Study</a>";
echo "<a href='sld.php' target='_blank' class='btn'>View SLD</a>";
echo "<a href='philosophy_on_dress.php' target='_blank' class='btn'>View Philosophy</a>";

echo "<h3>Edit Content:</h3>";
echo "<a href='admin/manage_campus_life_pages.php' class='btn' style='background: #2196F3;'>Go to Admin Panel</a>";

echo "<h3>Run Diagnostics Again:</h3>";
echo "<a href='diagnose_campus_life.php' class='btn' style='background: #ff9800;'>Run Diagnostics</a>";

echo "</div>
</body>
</html>";
?>
