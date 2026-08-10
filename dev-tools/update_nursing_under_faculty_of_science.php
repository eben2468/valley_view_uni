<?php
/**
 * Migration: the School of Nursing and Midwifery now sits under the
 * Faculty of Science.
 *
 * Its programmes are re-assigned to the Faculty of Science category and the
 * standalone school category is retired, so Nursing no longer appears as its
 * own filter on the programmes page.
 *
 * Also corrects the Certificate in Early Childhood Education to 1 year.
 *
 * Safe to run more than once.
 */
require_once 'includes/db_connect.php';

header('Content-Type: text/plain; charset=utf-8');

$school_name  = 'School of Nursing and Midwifery';
$faculty_name = 'Faculty of Science';

try {
    $pdo->beginTransaction();

    // --- Locate the two categories -----------------------------------------
    $find = $pdo->prepare("SELECT id, name FROM program_categories WHERE name = ? LIMIT 1");

    $find->execute([$faculty_name]);
    $faculty = $find->fetch(PDO::FETCH_ASSOC);
    if (!$faculty) {
        throw new RuntimeException("Category '{$faculty_name}' not found — aborting.");
    }

    $find->execute([$school_name]);
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
            echo "[skip]    no programmes left under '{$school_name}'\n";
        }

        // --- 2. Retire the now-empty school category -------------------------
        $del = $pdo->prepare("DELETE FROM program_categories WHERE id = ?");
        $del->execute([$school['id']]);
        echo "[removed] category '{$school_name}' (#{$school['id']})\n";
    } else {
        echo "[skip]    category '{$school_name}' already removed\n";
    }

    // --- 3. Hide the standalone menu entry ----------------------------------
    $nav = $pdo->prepare("UPDATE navigation_links SET is_active = 0 WHERE title = ? AND is_active = 1");
    $nav->execute([$school_name]);
    echo $nav->rowCount()
        ? "[updated] menu link '{$school_name}' deactivated\n"
        : "[skip]    menu link '{$school_name}' already inactive or absent\n";

    // --- 4. Re-label the homepage programme cards ---------------------------
    // The second spelling is a typo that exists in the seeded data.
    $home = $pdo->prepare("
        UPDATE homepage_programs
        SET category = ?
        WHERE category IN ('School of Nursing and Midwifery', 'School Of Nursing and Midwiferey')
    ");
    $home->execute([$faculty_name]);
    echo $home->rowCount()
        ? "[updated] {$home->rowCount()} homepage programme card(s) re-labelled to '{$faculty_name}'\n"
        : "[skip]    homepage programme cards already re-labelled\n";

    // --- 5. Re-label the admissions page programme tiles --------------------
    $items = $pdo->prepare("UPDATE academic_pages_items SET item_subtitle = ? WHERE item_subtitle = ?");
    $items->execute([$faculty_name, $school_name]);
    echo $items->rowCount()
        ? "[updated] {$items->rowCount()} programme tile subtitle(s) re-labelled\n"
        : "[skip]    programme tile subtitles already re-labelled\n";

    // --- 6. Certificate in Early Childhood Education is a 1-year programme ---
    $cert = $pdo->prepare("
        UPDATE academic_programs
        SET duration = '1 Year (Full Time)'
        WHERE title = 'Certificate in Early Childhood Education'
          AND duration <> '1 Year (Full Time)'
    ");
    $cert->execute();
    echo $cert->rowCount()
        ? "[updated] Certificate in Early Childhood Education -> 1 Year (Full Time)\n"
        : "[skip]    Certificate in Early Childhood Education already 1 year\n";

    $pdo->commit();
    echo "\nDone.\n";
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "\nFAILED, no changes applied: " . $e->getMessage() . "\n";
}
