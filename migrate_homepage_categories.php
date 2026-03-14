<?php
require_once('includes/db_connect.php');

$mapping = [
    'Technology / Research / Innovation' => 'Faculty of Science',
    'Business / Management / Finance' => 'School of Business',
    'Healthcare / Nursing / Medical' => 'School of Nursing and Midwifery',
    'Teaching / Education / Development' => 'School of Education',
    'Arts / Humanities / Social Studies' => 'Faculty of Arts & Social Sciences',
    'Theology / Ministry / Religious Studies' => 'Faculty of Arts & Social Sciences',
    'Postgraduate / Masters / Research' => 'School of Graduate Studies',
    'Professional Development / Skills Training' => 'Center for Adult and Continuing Education'
];

foreach ($mapping as $old => $new) {
    $stmt = $pdo->prepare("UPDATE homepage_programs SET category = ? WHERE category = ?");
    $stmt->execute([$new, $old]);
}

echo "Homepage programs categories updated successfully.\n";
?>
