<?php
/**
 * Migration: the School of Education is renamed the Department of Teacher
 * Education and now sits under the Faculty of Arts & Social Sciences.
 *
 * Its programmes are re-assigned to the Faculty of Arts & Social Sciences
 * category and the standalone school category is retired, so Education no
 * longer appears as its own filter on the programmes page. Everywhere the unit
 * is named on the site it now reads "Department of Teacher Education".
 *
 * Safe to run more than once.
 */
require_once 'includes/db_connect.php';

header('Content-Type: text/plain; charset=utf-8');

$old_name     = 'School of Education';
$new_name     = 'Department of Teacher Education';
$faculty_name = 'Faculty of Arts & Social Sciences';

try {
    $pdo->beginTransaction();

    // --- Locate the two categories -----------------------------------------
    $find = $pdo->prepare("SELECT id, name FROM program_categories WHERE name = ? LIMIT 1");

    $find->execute([$faculty_name]);
    $faculty = $find->fetch(PDO::FETCH_ASSOC);
    if (!$faculty) {
        throw new RuntimeException("Category '{$faculty_name}' not found — aborting.");
    }

    $find->execute([$old_name]);
    $school = $find->fetch(PDO::FETCH_ASSOC);

    // --- 1. Move the programmes across --------------------------------------
    if ($school) {
        $list = $pdo->prepare("SELECT id, title FROM academic_programs WHERE category_id = ? ORDER BY id");
        $list->execute([$school['id']]);
        $programs = $list->fetchAll(PDO::FETCH_ASSOC);

        if ($programs) {
            $move = $pdo->prepare("UPDATE academic_programs SET category_id = ? WHERE category_id = ?");
            $move->execute([$faculty['id'], $school['id']]);
            foreach ($programs as $p) {
                echo "[moved]   #{$p['id']} {$p['title']} -> {$faculty_name}\n";
            }
        } else {
            echo "[skip]    no programmes left under '{$old_name}'\n";
        }

        // --- 2. Retire the now-empty school category -------------------------
        $del = $pdo->prepare("DELETE FROM program_categories WHERE id = ?");
        $del->execute([$school['id']]);
        echo "[removed] category '{$old_name}' (#{$school['id']})\n";
    } else {
        echo "[skip]    category '{$old_name}' already removed\n";
    }

    // --- 3. Rename the unit everywhere it is displayed -----------------------
    $renames = [
        'contact directory entry'      => "UPDATE contact_departments   SET name          = ? WHERE name          = ?",
        'homepage programme card'      => "UPDATE homepage_programs     SET category      = ? WHERE category      = ?",
        'programme tile subtitle'      => "UPDATE academic_pages_items  SET item_subtitle = ? WHERE item_subtitle = ?",
        'legacy navigation menu label' => "UPDATE navigation_menu       SET label         = ? WHERE label         = ?",
    ];

    foreach ($renames as $label => $sql) {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$new_name, $old_name]);
        echo $stmt->rowCount()
            ? "[renamed] {$stmt->rowCount()} {$label}(s) -> '{$new_name}'\n"
            : "[skip]    {$label} already renamed or absent\n";
    }

    // --- 4. Menu entry, if the live megamenu ever lists the school -----------
    $nav = $pdo->prepare("UPDATE navigation_links SET title = ? WHERE title = ?");
    $nav->execute([$new_name, $old_name]);
    echo $nav->rowCount()
        ? "[renamed] {$nav->rowCount()} megamenu link(s) -> '{$new_name}'\n"
        : "[skip]    megamenu does not list '{$old_name}'\n";

    $pdo->commit();
    echo "\nDone.\n";
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "\nFAILED, no changes applied: " . $e->getMessage() . "\n";
}
