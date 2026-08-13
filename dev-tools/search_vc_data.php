<?php
require_once('includes/db_connect.php');

echo "--- Checking fields for section keys related to VC ---\n";
// The schema says VC page has 'vc_profile' section key.
$stmt = $pdo->query("SELECT c.id as section_id, c.page_id, c.section_key, f.field_key, f.field_value 
                     FROM administration_content c 
                     JOIN administration_content_fields f ON c.id = f.content_id 
                     WHERE c.section_key = 'vc_profile' OR f.field_value LIKE '%Vice%Chancellor%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
