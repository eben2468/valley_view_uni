<?php
include 'header.php';
include 'sidebar.php';
require_once('../includes/db_connect.php');

// Handle page selection
$selected_page = isset($_GET['page']) ? $_GET['page'] : 'philosophy_on_dress';

// Page titles mapping
$page_titles = [
    'philosophy_on_dress' => 'Philosophy on Dress',
    'accommodation' => 'Accommodation',
    'food_services' => 'Food Services',
    'work_study' => 'Work Study Program',
    'sld' => 'Spiritual Life & Development',
    'radio' => 'VVU Radio'
];
?>

<!-- Main Content -->
<main class="main-content">
    <div class="page-header">
        <div>
            <h1><i class="fas fa-university"></i> Campus Life Pages Management</h1>
            <p class="text-muted">Manage content for campus life pages</p>
        </div>
    </div>

    <!-- Page Selection Tabs -->
    <div class="dashboard-card mb-4">
        <div class="card-body">
            <div class="btn-group" role="group">
                <?php foreach ($page_titles as $key => $title): ?>
                    <a href="?page=<?php echo $key; ?>" 
                       class="btn <?php echo $selected_page === $key ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        <?php echo $title; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php
    // Include the appropriate page editor
    switch ($selected_page) {
        case 'philosophy_on_dress':
            include 'campus_life_editors/edit_philosophy_on_dress.php';
            break;
        case 'accommodation':
            include 'campus_life_editors/edit_accommodation.php';
            break;
        case 'food_services':
            include 'campus_life_editors/edit_food_services.php';
            break;
        case 'work_study':
            include 'campus_life_editors/edit_work_study.php';
            break;
        case 'sld':
            include 'campus_life_editors/edit_sld.php';
            break;
        case 'radio':
            include 'campus_life_editors/edit_radio.php';
            break;
        default:
            echo '<div class="alert alert-warning">Please select a page to edit.</div>';
    }
    ?>

</main>

<?php include 'footer.php'; ?>
