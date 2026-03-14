<?php
// Database import script for Strategy & History pages
require_once 'includes/db_connect.php';

echo "<!DOCTYPE html><html><head><title>Database Import</title>";
echo "<style>body{font-family:sans-serif;max-width:800px;margin:50px auto;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";
echo "</head><body>";
echo "<h1>Database Import Tool</h1>";

try {
    // Import schema
    echo "<h2>Importing Schema...</h2>";
    $schema_sql = file_get_contents('strategy_history_schema.sql');
    
    // Split by semicolon and execute each statement
    $statements = explode(';', $schema_sql);
    $schema_count = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
            $schema_count++;
        }
    }
    
    echo "<p class='success'>✓ Schema imported successfully! ($schema_count statements executed)</p>";
    
    // Import data
    echo "<h2>Importing Sample Data...</h2>";
    $data_sql = file_get_contents('strategy_history_data.sql');
    
    // Split by semicolon and execute each statement
    $statements = explode(';', $data_sql);
    $data_count = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
            $data_count++;
        }
    }
    
    echo "<p class='success'>✓ Sample data imported successfully! ($data_count statements executed)</p>";
    
    echo "<h2>Database Summary:</h2>";
    echo "<ul>";
    
    // Check tables
    $tables = [
        'strategic_plan_hero', 'strategic_plan_president_message', 'strategic_plan_pillars',
        'strategic_plan_timeline', 'strategic_plan_stats', 'strategic_plan_cta',
        'policies_hero', 'policies_categories', 'policies_documents', 'policies_quick_links', 'policies_cta',
        'history_hero', 'history_overview', 'history_milestones', 'history_community', 'history_cta',
        'accreditation_hero', 'accreditation_cards', 'accreditation_charter', 'accreditation_memberships', 'accreditation_cta'
    ];
    
    foreach ($tables as $table) {
        $result = $pdo->query("SELECT COUNT(*) as count FROM $table")->fetch();
        echo "<li><strong>$table:</strong> {$result['count']} records</li>";
    }
    
    echo "</ul>";
    
    echo "<h2>Next Steps:</h2>";
    echo "<ol>";
    echo "<li class='info'>Visit the <a href='admin/manage_strategy_history.php'>Admin Panel</a> to manage content</li>";
    echo "<li class='info'>View the <a href='strategic_plan.php'>Strategic Plan</a> page</li>";
    echo "<li class='info'>View the <a href='policies.php'>Policies</a> page</li>";
    echo "<li class='info'>View the <a href='history.php'>History</a> page</li>";
    echo "<li class='info'>View the <a href='accreditation_and_charter.php'>Accreditation & Charter</a> page</li>";
    echo "</ol>";
    
    echo "<p class='success'><strong>✓ Import completed successfully!</strong></p>";
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>
