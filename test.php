<?php
// Simple test file to verify PHP is working
echo "<h1>Valley View University - PHP Test</h1>";
echo "<p>PHP is working correctly!</p>";

// Test database connection
include 'includes/db_connect.php';
echo "<p>Database connection: SUCCESS</p>";

// Show PHP info
phpinfo();
?>