<?php
/**
 * Migration: introduce departments beneath the faculties and schools.
 *
 * Adds a `program_departments` table and an `academic_programs.department_id`
 * column, seeds the departments for the three units that have them, and files
 * every existing programme under the right one.
 *
 * The School of Graduate Studies deliberately has no departments — its
 * programmes stay as a single flat list.
 *
 * Safe to run more than once.
 */
require_once 'includes/db_connect.php';

header('Content-Type: text/plain; charset=utf-8');

// Department => list of programme titles that belong to it.
$structure = [
    'Faculty of Arts & Social Sciences' => [
        'Department of Development and Communication Studies' => [
            'icon'     => 'campaign',
            'programs' => [
                'B.Sc Development Studies',
                'BA Communication Studies',
                'Diploma in Development Studies',
            ],
        ],
        'Department of Theological Studies and Mission' => [
            'icon'     => 'menu_book',
            'programs' => [
                'B.A Theological Studies',
            ],
        ],
        'Department of Teacher Education' => [
            'icon'     => 'school',
            'programs' => [
                'B.Ed Mathematics',
                'B.Ed Social Studies',
                'B.Ed Management',
                'B.Ed Accounting',
                'B.Ed information Technology',
                'B.Ed English Language',
                'Bachelor of Education in Music',
                'Diploma in Music',
                'Certificate in Early Childhood Education',
            ],
        ],
    ],
    'Faculty of Science' => [
        'Department of Computing Sciences and Engineering' => [
            'icon'     => 'computer',
            'programs' => [
                'BSc Computer Science',
                'BSc Information Technology',
                'B.Sc Business Information Systems',
                'BSc. Biomedical Engineering',
                'Diploma in Computer Science',
                'Diploma in Information Technology',
                'Diploma in Biomedical Equipment Technology',
                'Bachelor of Science in Agriculture',
                'BSc Agribusiness',
            ],
        ],
        'Department of Nursing and Health Sciences' => [
            'icon'     => 'health_and_safety',
            'programs' => [
                'B.Sc General Nursing',
                'BSc Mental Health Nursing',
                'BSc Midwifery',
            ],
        ],
    ],
    'School of Business' => [
        'Department of Accounting and Finance' => [
            'icon'     => 'account_balance',
            'programs' => [
                'BBA in Accounting',
                'BBA Banking and Finance',
            ],
        ],
        'Department of Management Studies' => [
            'icon'     => 'business_center',
            'programs' => [
                'BBA Management',
                'BBA Marketing',
                'BBA HRM',
                'Diploma in Business Administration',
            ],
        ],
    ],
];

// Programmes withdrawn from the catalogue (deactivated, not deleted).
$retire = ['BSc Mathematics with Statistics'];

try {
    // --- 1. Schema ----------------------------------------------------------
    // Run outside a transaction: MySQL commits implicitly on DDL, which would
    // otherwise tear down the transaction wrapping the data changes below.
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS program_departments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            category_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            icon VARCHAR(100) DEFAULT 'school',
            description TEXT NULL,
            display_order INT DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_department (category_id, name),
            CONSTRAINT fk_department_category FOREIGN KEY (category_id)
                REFERENCES program_categories (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "[schema]  program_departments ready\n";

    $has_column = $pdo->query("SHOW COLUMNS FROM academic_programs LIKE 'department_id'")->fetch();
    if (!$has_column) {
        $pdo->exec("ALTER TABLE academic_programs ADD COLUMN department_id INT NULL AFTER category_id");
        $pdo->exec("
            ALTER TABLE academic_programs
            ADD CONSTRAINT fk_program_department FOREIGN KEY (department_id)
                REFERENCES program_departments (id) ON DELETE SET NULL
        ");
        echo "[schema]  academic_programs.department_id added\n";
    } else {
        echo "[skip]    academic_programs.department_id already exists\n";
    }

    // --- 2. Departments and assignments -------------------------------------
    // No further DDL from here on, so the data changes can be wrapped safely.
    $pdo->beginTransaction();

    $find_category   = $pdo->prepare("SELECT id FROM program_categories WHERE name = ? LIMIT 1");
    $find_department = $pdo->prepare("SELECT id FROM program_departments WHERE category_id = ? AND name = ? LIMIT 1");
    $insert_dept     = $pdo->prepare("INSERT INTO program_departments (category_id, name, icon, display_order) VALUES (?, ?, ?, ?)");
    $update_dept     = $pdo->prepare("UPDATE program_departments SET icon = ?, display_order = ? WHERE id = ?");
    $lookup          = $pdo->prepare("SELECT id, department_id, category_id FROM academic_programs WHERE title = ? LIMIT 1");
    $assign          = $pdo->prepare("UPDATE academic_programs SET department_id = ?, category_id = ? WHERE id = ?");

    foreach ($structure as $category_name => $departments) {
        $find_category->execute([$category_name]);
        $category_id = $find_category->fetchColumn();
        if (!$category_id) {
            throw new RuntimeException("Category '{$category_name}' not found — aborting.");
        }

        echo "\n{$category_name}\n";
        $order = 0;

        foreach ($departments as $dept_name => $dept) {
            $find_department->execute([$category_id, $dept_name]);
            $dept_id = $find_department->fetchColumn();

            if ($dept_id) {
                $update_dept->execute([$dept['icon'], $order, $dept_id]);
                echo "  [exists]  {$dept_name}\n";
            } else {
                $insert_dept->execute([$category_id, $dept_name, $dept['icon'], $order]);
                $dept_id = $pdo->lastInsertId();
                echo "  [created] {$dept_name}\n";
            }
            $order++;

            foreach ($dept['programs'] as $title) {
                $lookup->execute([$title]);
                $program = $lookup->fetch(PDO::FETCH_ASSOC);

                if (!$program) {
                    echo "      ! NOT FOUND: {$title}\n";
                    continue;
                }
                if ((int)$program['department_id'] === (int)$dept_id
                    && (int)$program['category_id'] === (int)$category_id) {
                    echo "      = {$title} (already filed here)\n";
                    continue;
                }
                $assign->execute([$dept_id, $category_id, $program['id']]);
                echo "      - {$title}\n";
            }
        }
    }

    // --- 3. Withdraw programmes no longer offered ---------------------------
    echo "\nWithdrawn programmes\n";
    $withdraw = $pdo->prepare("UPDATE academic_programs SET is_active = 0 WHERE title = ? AND is_active = 1");
    foreach ($retire as $title) {
        $withdraw->execute([$title]);
        echo $withdraw->rowCount()
            ? "  [hidden]  {$title} (deactivated, record kept)\n"
            : "  [skip]    {$title} already inactive or absent\n";
    }

    // --- 4. Report anything left unfiled ------------------------------------
    $orphans = $pdo->query("
        SELECT ap.title, pc.name AS category
        FROM academic_programs ap
        JOIN program_categories pc ON pc.id = ap.category_id
        WHERE ap.is_active = 1
          AND ap.department_id IS NULL
          AND pc.name <> 'School of Graduate Studies'
        ORDER BY pc.name, ap.title
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo "\nUnfiled programmes (excluding School of Graduate Studies, which has no departments)\n";
    if ($orphans) {
        foreach ($orphans as $o) {
            echo "  ! {$o['category']}: {$o['title']}\n";
        }
    } else {
        echo "  none — every programme is filed under a department\n";
    }

    $pdo->commit();
    echo "\nDone.\n";
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "\nFAILED, no changes applied: " . $e->getMessage() . "\n";
}
