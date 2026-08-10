<?php
/**
 * Migration: Diploma programmes run for 2 years, not 4.
 *
 * Undergraduate diplomas were seeded with the same "4 Years (Full Time)"
 * duration as the bachelor's degrees. This corrects them to 2 years.
 *
 * Postgraduate diplomas (e.g. PGD Education) are deliberately excluded — they
 * sit at Postgraduate level with their own duration.
 *
 * Safe to run more than once.
 */
require_once 'includes/db_connect.php';

header('Content-Type: text/plain; charset=utf-8');

$new_duration = '2 Years (Full Time)';

$select = $pdo->prepare("
    SELECT id, title, duration
    FROM academic_programs
    WHERE title LIKE 'Diploma%'
      AND level <> 'Postgraduate'
    ORDER BY id
");
$select->execute();
$programs = $select->fetchAll(PDO::FETCH_ASSOC);

if (!$programs) {
    echo "No undergraduate diploma programmes found.\n";
    exit;
}

$update = $pdo->prepare("UPDATE academic_programs SET duration = ? WHERE id = ?");
$changed = 0;

foreach ($programs as $program) {
    if ($program['duration'] === $new_duration) {
        echo "[skip]    #{$program['id']} {$program['title']} — already {$new_duration}\n";
        continue;
    }
    $update->execute([$new_duration, $program['id']]);
    $changed++;
    echo "[updated] #{$program['id']} {$program['title']} — '{$program['duration']}' -> '{$new_duration}'\n";
}

echo "\nDone. {$changed} programme(s) updated, " . (count($programs) - $changed) . " already correct.\n";
