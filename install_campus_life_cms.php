<?php
/**
 * Installation Script for Campus Life Pages CMS
 * This script creates all necessary database tables and inserts default content
 */

require_once 'includes/db_connect.php';

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Campus Life CMS Installation</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body { background: #f8f9fa; padding: 40px 0; }
        .install-container { max-width: 900px; margin: 0 auto; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #17a2b8; }
    </style>
</head>
<body>
<div class='install-container'>
    <div class='card shadow'>
        <div class='card-header bg-primary text-white'>
            <h2 class='mb-0'><i class='fas fa-database'></i> Campus Life Pages CMS Installation</h2>
        </div>
        <div class='card-body'>";

try {
    // Read and execute SQL file
    $sql_file = 'sql/campus_life_pages_schema.sql';
    
    if (!file_exists($sql_file)) {
        throw new Exception("SQL file not found: $sql_file");
    }
    
    echo "<p class='info'><strong>Reading SQL file...</strong></p>";
    $sql = file_get_contents($sql_file);
    
    // Remove comments
    $sql = preg_replace('/--.*$/m', '', $sql);
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
    
    // Split SQL into individual statements
    $statements = explode(';', $sql);
    
    $success_count = 0;
    $error_count = 0;
    
    echo "<div class='mt-3'>";
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        
        // Skip empty statements
        if (empty($statement)) {
            continue;
        }
        
        try {
            $pdo->exec($statement);
            $success_count++;
            
            // Extract table name for display
            if (preg_match('/CREATE TABLE.*?`([^`]+)`/i', $statement, $matches)) {
                echo "<p class='success'>✓ Created table: <strong>{$matches[1]}</strong></p>";
            } elseif (preg_match('/INSERT INTO.*?`([^`]+)`/i', $statement, $matches)) {
                echo "<p class='success'>✓ Inserted data into: <strong>{$matches[1]}</strong></p>";
            } else {
                echo "<p class='success'>✓ Executed SQL statement</p>";
            }
        } catch (PDOException $e) {
            $error_count++;
            $error_msg = $e->getMessage();
            // Only show error if it's not "table already exists"
            if (strpos($error_msg, 'already exists') === false) {
                echo "<p class='error'>✗ Error: " . strip_tags($error_msg) . "</p>";
            } else {
                echo "<p class='info'>ℹ Table already exists (skipped)</p>";
                $success_count++; // Count as success since table exists
                $error_count--;
            }
        }
    }
    
    echo "</div>";
    
    echo "<div class='alert alert-success mt-4'>";
    echo "<h4>Installation Complete!</h4>";
    echo "<p><strong>Summary:</strong></p>";
    echo "<ul>";
    echo "<li>Successful operations: $success_count</li>";
    echo "<li>Errors: $error_count</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div class='alert alert-info'>";
    echo "<h5>Next Steps:</h5>";
    echo "<ol>";
    echo "<li>Go to the <a href='admin/manage_campus_life_pages.php' class='alert-link'>Admin Panel</a> to manage content</li>";
    echo "<li>Edit content for each of the five campus life pages</li>";
    echo "<li>Upload images to the appropriate directories</li>";
    echo "<li>Preview pages to ensure everything looks correct</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<div class='mt-4'>";
    echo "<a href='admin/manage_campus_life_pages.php' class='btn btn-primary btn-lg'>Go to Admin Panel</a> ";
    echo "<a href='index.php' class='btn btn-secondary btn-lg'>View Website</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<h4>Installation Failed!</h4>";
    echo "<p>" . strip_tags($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "        </div>
    </div>
</div>
</body>
</html>";
?>
