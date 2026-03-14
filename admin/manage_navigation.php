<?php
include 'header.php';
include 'sidebar.php';
require_once('../includes/db_connect.php');
require_once('../includes/upload_helper.php');

// Handle Form Submissions
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'add_item':
                    $stmt = $pdo->prepare("INSERT INTO navigation_items (title, url, has_megamenu, megamenu_type, sort_order) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$_POST['title'], $_POST['url'], isset($_POST['has_megamenu']) ? 1 : 0, $_POST['megamenu_type'] ?: null, $_POST['sort_order'] ?: 0]);
                    $message = "Main menu item added successfully!";
                    break;

                case 'update_item':
                    $stmt = $pdo->prepare("UPDATE navigation_items SET title = ?, url = ?, has_megamenu = ?, megamenu_type = ?, sort_order = ? WHERE id = ?");
                    $stmt->execute([$_POST['title'], $_POST['url'], isset($_POST['has_megamenu']) ? 1 : 0, $_POST['megamenu_type'] ?: null, $_POST['sort_order'], $_POST['id']]);
                    $message = "Menu item updated successfully!";
                    break;

                case 'delete_item':
                    $stmt = $pdo->prepare("DELETE FROM navigation_items WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    $message = "Menu item deleted successfully!";
                    break;

                case 'add_section':
                    $imagePath = handleAdminFileUpload($_FILES['featured_image_file'] ?? null, 'nav_featured/', 'nav_');
                    $featuredImage = $imagePath ?? ($_POST['featured_image'] ?: null);

                    $stmt = $pdo->prepare("INSERT INTO navigation_sections (navigation_item_id, section_title, section_type, column_position, featured_image, featured_link, featured_text, description_text, button_text, button_link, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['item_id'],
                        $_POST['title'],
                        $_POST['type'],
                        $_POST['column'],
                        $featuredImage,
                        $_POST['featured_link'] ?: null,
                        $_POST['featured_text'] ?: null,
                        $_POST['description'] ?: null,
                        $_POST['button_text'] ?: null,
                        $_POST['button_link'] ?: null,
                        $_POST['sort_order'] ?: 0
                    ]);
                    $message = "Section added successfully!";
                    break;

                case 'update_section':
                    $imagePath = handleAdminFileUpload($_FILES['featured_image_file'] ?? null, 'nav_featured/', 'nav_');
                    // If a new image was uploaded, use it. Otherwise, use the URL if provided, or keep existing.
                    $featuredImage = $imagePath ?? ($_POST['featured_image'] ?: null);

                    $stmt = $pdo->prepare("UPDATE navigation_sections SET section_title = ?, section_type = ?, column_position = ?, featured_image = ?, featured_link = ?, featured_text = ?, description_text = ?, button_text = ?, button_link = ?, sort_order = ? WHERE id = ?");
                    $stmt->execute([
                        $_POST['title'],
                        $_POST['type'],
                        $_POST['column'],
                        $featuredImage,
                        $_POST['featured_link'] ?: null,
                        $_POST['featured_text'] ?: null,
                        $_POST['description'] ?: null,
                        $_POST['button_text'] ?: null,
                        $_POST['button_link'] ?: null,
                        $_POST['sort_order'],
                        $_POST['id']
                    ]);
                    $message = "Section updated successfully!";
                    break;

                case 'delete_section':
                    $stmt = $pdo->prepare("DELETE FROM navigation_sections WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    $message = "Section deleted successfully!";
                    break;

                case 'add_link':
                    $stmt = $pdo->prepare("INSERT INTO navigation_links (section_id, title, url, sort_order) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$_POST['section_id'], $_POST['title'], $_POST['url'], $_POST['sort_order'] ?: 0]);
                    $message = "Link added successfully!";
                    break;

                case 'update_link':
                    $stmt = $pdo->prepare("UPDATE navigation_links SET title = ?, url = ?, sort_order = ? WHERE id = ?");
                    $stmt->execute([$_POST['title'], $_POST['url'], $_POST['sort_order'], $_POST['id']]);
                    $message = "Link updated successfully!";
                    break;

                case 'delete_link':
                    $stmt = $pdo->prepare("DELETE FROM navigation_links WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    $message = "Link deleted successfully!";
                    break;
            }
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch all navigation items with their sections and links
$items = $pdo->query("SELECT * FROM navigation_items ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0">Navigation Management</h2>
                        <p class="text-muted">Manage your website's header and mega menu content.</p>
                    </div>
                    <button class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="addItem()">
                        <i class="fas fa-plus me-2"></i> Add Main Menu Item
                    </button>
                </div>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12">
                <div class="accordion" id="navigationAccordion">
                    <?php foreach ($items as $item): ?>
                        <div class="accordion-item mb-3 border shadow-sm">
                            <h2 class="accordion-header position-relative" id="heading<?php echo $item['id']; ?>">
                                <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $item['id']; ?>">
                                    <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                        <div>
                                            <span class="badge bg-secondary me-2">Order: <?php echo $item['sort_order']; ?></span>
                                            <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                            <small class="ms-2 text-muted"><?php echo htmlspecialchars($item['url']); ?></small>
                                            <?php if ($item['has_megamenu']): ?>
                                                <span class="badge bg-info ms-2">Mega Menu (<?php echo htmlspecialchars($item['megamenu_type']); ?>)</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </button>
                                <div class="position-absolute end-0 top-50 translate-middle-y me-5 pe-2" style="z-index: 5;">
                                    <button class="btn btn-sm btn-light border shadow-sm me-2" onclick="event.stopPropagation(); editItem(<?php echo htmlspecialchars(json_encode($item)); ?>)" title="Edit">
                                        <i class="fas fa-edit text-primary"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light border shadow-sm" onclick="event.stopPropagation(); deleteItem(<?php echo $item['id']; ?>)" title="Delete">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </div>
                            </h2>
                            <div id="collapse<?php echo $item['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#navigationAccordion">
                                <div class="accordion-body bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">Sections / Columns</h5>
                                        <button class="btn btn-sm btn-success" onclick="addSection(<?php echo $item['id']; ?>)">
                                            <i class="fas fa-plus me-1"></i> Add Section
                                        </button>
                                    </div>

                                    <?php
                                    $sections = $pdo->prepare("SELECT * FROM navigation_sections WHERE navigation_item_id = ? ORDER BY sort_order ASC");
                                    $sections->execute([$item['id']]);
                                    $sections_data = $sections->fetchAll(PDO::FETCH_ASSOC);
                                    ?>

                                    <div class="row g-3">
                                        <?php if (empty($sections_data)): ?>
                                            <div class="col-12">
                                                <div class="alert alert-light border text-center py-4">
                                                    No sections defined for this menu item.
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php foreach ($sections_data as $section): ?>
                                            <div class="col-md-4">
                                                <div class="card h-100 shadow-sm border-0">
                                                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <span class="badge bg-light text-dark border me-1">Col: <?php echo $section['column_position']; ?></span>
                                                            <strong><?php echo htmlspecialchars($section['section_title']); ?></strong>
                                                        </div>
                                                        <div class="btn-group">
                                                            <button class="btn btn-xs btn-outline-primary" onclick='editSection(<?php echo json_encode($section); ?>)'>
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-xs btn-outline-danger" onclick="deleteSection(<?php echo $section['id']; ?>)">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <?php if ($section['section_type'] == 'links'): ?>
                                                            <ul class="list-group list-group-flush mb-3">
                                                                <?php
                                                                $links = $pdo->prepare("SELECT * FROM navigation_links WHERE section_id = ? ORDER BY sort_order ASC");
                                                                $links->execute([$section['id']]);
                                                                while ($link = $links->fetch()) {
                                                                    echo "<li class='list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent'>
                                                                        <div>
                                                                            <small class='text-muted me-2'>[{$link['sort_order']}]</small>
                                                                            <span>" . htmlspecialchars($link['title']) . "</span>
                                                                        </div>
                                                                        <div class='btn-group'>
                                                                            <button class='btn btn-link btn-sm p-0 me-2' onclick='editLink(" . json_encode($link) . ")'><i class='fas fa-edit text-primary'></i></button>
                                                                            <button class='btn btn-link btn-sm p-0' onclick='deleteLink({$link['id']})'><i class='fas fa-trash text-danger'></i></button>
                                                                        </div>
                                                                    </li>";
                                                                }
                                                                ?>
                                                            </ul>
                                                            <button class="btn btn-xs btn-outline-success w-100" onclick="addLink(<?php echo $section['id']; ?>)">
                                                                <i class="fas fa-plus me-1"></i> Add Link
                                                            </button>
                                                        <?php elseif ($section['section_type'] == 'description'): ?>
                                                            <p class="small text-muted mb-2"><?php echo htmlspecialchars($section['description_text']); ?></p>
                                                            <?php if ($section['button_text']): ?>
                                                                <a href="#" class="btn btn-xs btn-primary"><?php echo htmlspecialchars($section['button_text']); ?></a>
                                                            <?php endif; ?>
                                                        <?php elseif ($section['section_type'] == 'featured'): ?>
                                                            <?php if ($section['featured_image']): ?>
                                                                <img src="../<?php echo htmlspecialchars($section['featured_image']); ?>" class="img-fluid rounded border mb-2" alt="Featured">
                                                            <?php endif; ?>
                                                            <p class="mb-0 fw-bold"><?php echo htmlspecialchars($section['featured_text']); ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modals -->
<!-- Add/Edit Item Modal -->
<div class="modal fade" id="itemModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST">
            <input type="hidden" name="action" id="itemAction" value="add_item">
            <input type="hidden" name="id" id="itemId">
            <div class="modal-header">
                <h5 class="modal-title" id="itemModalTitle">Add Main Menu Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" id="itemTitle" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">URL / Destination</label>
                    <input type="text" name="url" id="itemUrl" class="form-control" required value="index.php">
                    <small class="text-muted">Use <code>javascript:void(0)</code> for items with mega menus.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" id="itemSortOrder" class="form-control" value="0">
                </div>
                <div class="mb-3 form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="has_megamenu" id="itemHasMegamenu">
                    <label class="form-check-label">Enable Mega Menu</label>
                </div>
                <div class="mb-3" id="megamenuTypeContainer" style="display:none;">
                    <label class="form-label">Mega Menu Type (CSS Class)</label>
                    <select name="megamenu_type" id="itemMegamenuType" class="form-select">
                        <option value="about-mm">about-mm (Orange accent)</option>
                        <option value="admi-mm">admi-mm (Full width)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Section Modal -->
<div class="modal fade" id="sectionModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="sectionAction" value="add_section">
            <input type="hidden" name="id" id="sectionId">
            <input type="hidden" name="item_id" id="sectionItemId">
            <div class="modal-header">
                <h5 class="modal-title" id="sectionModalTitle">Section Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Section Title</label>
                    <input type="text" name="title" id="sectionTitle" class="form-control" required>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Type</label>
                        <select name="type" id="sectionType" class="form-select">
                            <option value="links">List of Links</option>
                            <option value="description">Text Description</option>
                            <option value="featured">Featured (Img + Title)</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Column Position (1-4)</label>
                        <input type="number" name="column" id="sectionColumn" class="form-control" value="1" min="1" max="4">
                    </div>
                </div>
                <div id="typeLinksExtras">
                    <p class="small text-muted">Links are added after creating the section.</p>
                </div>
                <div id="typeDescriptionExtras" style="display:none;">
                    <div class="mb-3">
                        <label class="form-label">Description Text</label>
                        <textarea name="description" id="sectionDescription" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">Button Text</label>
                            <input type="text" name="button_text" id="sectionButtonText" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Button Link</label>
                            <input type="text" name="button_link" id="sectionButtonLink" class="form-control">
                        </div>
                    </div>
                </div>
                <div id="typeFeaturedExtras" style="display:none;">
                    <div class="mb-3">
                        <label class="form-label">Upload Featured Image</label>
                        <input type="file" name="featured_image_file" class="form-control" accept="image/*">
                        <small class="text-muted">Or provide a URL below</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Featured Image URL</label>
                        <input type="text" name="featured_image" id="sectionFeaturedImage" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">Featured Title</label>
                            <input type="text" name="featured_text" id="sectionFeaturedText" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Featured Link</label>
                            <input type="text" name="featured_link" id="sectionFeaturedLink" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="mb-3 mt-3">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" id="sectionSortOrder" class="form-control" value="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Section</button>
            </div>
        </form>
    </div>
</div>

<!-- Link Modal -->
<div class="modal fade" id="linkModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST">
            <input type="hidden" name="action" id="linkAction" value="add_link">
            <input type="hidden" name="id" id="linkId">
            <input type="hidden" name="section_id" id="linkSectionId">
            <div class="modal-header">
                <h5 class="modal-title">Link Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Link Label</label>
                    <input type="text" name="title" id="linkTitle" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">URL</label>
                    <input type="text" name="url" id="linkUrl" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" id="linkSortOrder" class="form-control" value="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Link</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display:none;">
    <input type="hidden" name="action" id="deleteAction">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
$(document).ready(function() {
    $('#itemHasMegamenu').change(function() {
        $('#megamenuTypeContainer').toggle(this.checked);
    });

    $('#sectionType').change(function() {
        const val = $(this).val();
        $('#typeLinksExtras').toggle(val === 'links');
        $('#typeDescriptionExtras').toggle(val === 'description');
        $('#typeFeaturedExtras').toggle(val === 'featured');
    });
});

function addItem() {
    $('#itemAction').val('add_item');
    $('#itemId').val('');
    $('#itemTitle').val('');
    $('#itemUrl').val('index.php');
    $('#itemSortOrder').val('0');
    $('#itemHasMegamenu').prop('checked', false).trigger('change');
    $('#itemModalTitle').text('Add Main Menu Item');
    new bootstrap.Modal(document.getElementById('itemModal')).show();
}

function editItem(item) {
    $('#itemAction').val('update_item');
    $('#itemId').val(item.id);
    $('#itemTitle').val(item.title);
    $('#itemUrl').val(item.url);
    $('#itemSortOrder').val(item.sort_order);
    $('#itemHasMegamenu').prop('checked', item.has_megamenu == 1).trigger('change');
    $('#itemMegamenuType').val(item.megamenu_type || 'about-mm');
    $('#itemModalTitle').text('Edit Menu Item');
    new bootstrap.Modal(document.getElementById('itemModal')).show();
}

function deleteItem(id) {
    if (confirm('Are you sure you want to delete this menu item and ALL its contents?')) {
        $('#deleteAction').val('delete_item');
        $('#deleteId').val(id);
        $('#deleteForm').submit();
    }
}

function addSection(itemId) {
    $('#sectionAction').val('add_section');
    $('#sectionId').val('');
    $('#sectionItemId').val(itemId);
    $('#sectionTitle').val('');
    $('#sectionType').val('links').trigger('change');
    $('#sectionModalTitle').text('Add New Section');
    new bootstrap.Modal(document.getElementById('sectionModal')).show();
}

function editSection(section) {
    $('#sectionAction').val('update_section');
    $('#sectionId').val(section.id);
    $('#sectionItemId').val(section.navigation_item_id);
    $('#sectionTitle').val(section.section_title);
    $('#sectionType').val(section.section_type).trigger('change');
    $('#sectionColumn').val(section.column_position);
    $('#sectionDescription').val(section.description_text);
    $('#sectionButtonText').val(section.button_text);
    $('#sectionButtonLink').val(section.button_link);
    $('#sectionFeaturedImage').val(section.featured_image);
    $('#sectionFeaturedText').val(section.featured_text);
    $('#sectionFeaturedLink').val(section.featured_link);
    $('#sectionSortOrder').val(section.sort_order);
    $('#sectionModalTitle').text('Edit Section');
    new bootstrap.Modal(document.getElementById('sectionModal')).show();
}

function deleteSection(id) {
    if (confirm('Delete this section and all its links?')) {
        $('#deleteAction').val('delete_section');
        $('#deleteId').val(id);
        $('#deleteForm').submit();
    }
}

function addLink(sectionId) {
    $('#linkAction').val('add_link');
    $('#linkId').val('');
    $('#linkSectionId').val(sectionId);
    $('#linkTitle').val('');
    $('#linkUrl').val('');
    $('#linkSortOrder').val('0');
    new bootstrap.Modal(document.getElementById('linkModal')).show();
}

function editLink(link) {
    $('#linkAction').val('update_link');
    $('#linkId').val(link.id);
    $('#linkSectionId').val(link.section_id);
    $('#linkTitle').val(link.title);
    $('#linkUrl').val(link.url);
    $('#linkSortOrder').val(link.sort_order);
    new bootstrap.Modal(document.getElementById('linkModal')).show();
}

function deleteLink(id) {
    if (confirm('Are you sure?')) {
        $('#deleteAction').val('delete_link');
        $('#deleteId').val(id);
        $('#deleteForm').submit();
    }
}
</script>

<style>
.btn-xs {
    padding: 0.1rem 0.4rem;
    font-size: 0.75rem;
}
.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
    color: inherit;
    box-shadow: none;
}
.accordion-item {
    overflow: visible;
}
</style>

<?php include 'footer.php'; ?>
