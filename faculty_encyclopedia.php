<?php
$page_title = "Faculty Encyclopedia - Valley View University";
$active_page = "academics";
include 'includes/header.php';
require_once 'includes/db_connect.php';

// Fetch dynamic content
$content_stmt = $pdo->prepare("SELECT * FROM encyclopedia_content WHERE page_key = 'faculty'");
$content_stmt->execute();
$page_content = $content_stmt->fetch();

// Pagination and Filters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$dept = isset($_GET['department']) ? $_GET['department'] : '';
$faculty_group = isset($_GET['faculty_group']) ? $_GET['faculty_group'] : '';

$query = "SELECT * FROM directory WHERE type = 'faculty'";
$params = [];

if ($search) {
    $query .= " AND (name LIKE ? OR job_title LIKE ? OR department LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($dept) {
    $query .= " AND department = ?";
    $params[] = $dept;
}
if ($faculty_group) {
    $query .= " AND faculty_group = ?";
    $params[] = $faculty_group;
}

$query .= " ORDER BY name ASC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$faculties = $stmt->fetchAll();

// Get unique departments and groups for filters
$depts = $pdo->query("SELECT DISTINCT department FROM directory WHERE type = 'faculty' AND department != ''")->fetchAll(PDO::FETCH_COLUMN);
$groups = $pdo->query("SELECT DISTINCT faculty_group FROM directory WHERE type = 'faculty' AND faculty_group != ''")->fetchAll(PDO::FETCH_COLUMN);
?>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slowZoom {
        0% { transform: scale(1); }
        100% { transform: scale(1.1); }
    }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
    
    .glass {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .dark .glass {
        background: rgba(31, 41, 55, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .faculty-card {
        background: white;
        border-radius: 0.75rem;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .faculty-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border-color: #800000;
    }

    .faculty-image-wrapper {
        position: relative;
        aspect-ratio: 1/1;
        overflow: hidden;
        background: #f8fafc;
    }

    .faculty-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .faculty-card:hover .faculty-image-wrapper img {
        transform: scale(1.05);
    }

    .job-badge {
        display: inline-block;
        background: #006400;
        color: white;
        padding: 2px 8px;
        font-size: 8px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-radius: 4px;
        margin-bottom: 4px;
    }

    .faculty-info {
        padding: 8px 10px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        gap: 2px;
        text-align: center;
    }

    .faculty-name {
        font-size: 13px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 2px;
        line-height: 1.2;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.4em;
    }

    .faculty-dept {
        font-size: 10px;
        color: #64748b;
        font-weight: 600;
        line-height: 1.2;
        margin-bottom: 6px;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 1.2em;
    }

    .btn-profile {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        width: 100%;
        padding: 5px;
        background: #f8fafc;
        color: #334155;
        font-weight: 700;
        font-size: 9px;
        border-radius: 6px;
        transition: all 0.2s ease;
        text-decoration: none !important;
        text-transform: uppercase;
        margin-top: auto;
        border: 1px solid #e2e8f0;
    }

    .faculty-card:hover .btn-profile {
        background: #800000;
        color: white;
        border-color: #800000;
    }

    /* Filters Layout Update */
    .filter-container {
        position: relative;
        background: white;
        border-radius: 2rem;
        padding: 0.75rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }

    .filter-group {
        position: relative;
        display: flex;
        align-items: center;
    }

    .filter-icon {
        position: absolute;
        left: 1.5rem;
        color: #64748b;
        font-size: 1.5rem;
        pointer-events: none;
    }

    .filter-input {
        width: 100%;
        padding: 1.25rem 1.5rem 1.25rem 3.5rem;
        border-radius: 1.5rem;
        border: 1px solid transparent;
        background: #f8fafc;
        font-size: 1.1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        outline: none;
        color: #1e293b;
    }

    .filter-input:focus {
        background: white;
        border-color: #800000;
        box-shadow: 0 0 0 4px rgba(128, 0, 0, 0.05);
    }

    .filter-select {
        padding-left: 3.5rem;
        padding-right: 3.5rem;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1.5rem center;
        background-size: 1.5rem;
    }

    .btn-search {
        width: 100%;
        height: 100%;
        padding: 1.25rem;
        border-radius: 1.5rem;
        background: #800000;
        color: white;
        font-weight: 800;
        border: none;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .btn-search:hover {
        background: #600000;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(128, 0, 0, 0.3);
    }

    .btn-search:active {
        transform: translateY(0);
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900 pb-20">
    <!-- Hero Section (Directly from faqs_about_vvu.php design) -->
    <section class="relative min-h-[60vh] flex items-center overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($page_content['hero_image'] ?: 'vvu_faq_hero_1766876441891.png'); ?>" 
                 alt="Faculty Hero" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-6 py-2 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xs md:text-sm font-black tracking-widest uppercase text-yellow-400">Academic Directory</span>
                </div>
                
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black leading-none tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($page_content['hero_title']); ?> <br>
                    <span class="text-3xl sm:text-4xl md:text-5xl lg:text-5xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-2">Faculty Profiles</span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-3xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($page_content['hero_subtitle']); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Search/Filter Section -->
    <section class="py-16 relative z-20 -mt-24">
        <div class="container">
            <div class="max-w-6xl mx-auto">
                <div class="filter-container">
                    <form action="" method="GET" class="m-0 w-full">
                        <div class="flex flex-col lg:flex-row gap-4 items-center w-full">
                            <div class="w-full lg:w-4/12">
                                <div class="filter-group w-full">
                                    <span class="material-symbols-outlined filter-icon">person_search</span>
                                    <input type="text" name="search" placeholder="Search by name or role..." 
                                           class="filter-input w-full" value="<?php echo strip_tags($search); ?>">
                                </div>
                            </div>
                            <div class="w-full lg:w-3/12">
                                <div class="filter-group w-full">
                                    <span class="material-symbols-outlined filter-icon">school</span>
                                    <select name="department" class="filter-input filter-select w-full">
                                        <option value="">All Departments</option>
                                        <?php foreach ($depts as $d): ?>
                                            <option value="<?php echo strip_tags($d); ?>" <?php echo $dept == $d ? 'selected' : ''; ?>><?php echo strip_tags($d); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="w-full lg:w-3/12">
                                <div class="filter-group w-full">
                                    <span class="material-symbols-outlined filter-icon">account_balance</span>
                                    <select name="faculty_group" class="filter-input filter-select w-full">
                                        <option value="">All Faculties</option>
                                        <?php foreach ($groups as $g): ?>
                                            <option value="<?php echo strip_tags($g); ?>" <?php echo $faculty_group == $g ? 'selected' : ''; ?>><?php echo strip_tags($g); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="w-full lg:w-2/12 h-full">
                                <button type="submit" class="btn-search w-full h-full">
                                    <span class="material-symbols-outlined">search</span>
                                    Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Faculty Grid Section -->
    <section class="py-12">
        <div class="container px-4">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
                <?php if (empty($faculties)): ?>
                    <div class="col-span-full text-center py-20">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                            <span class="material-symbols-outlined text-4xl text-gray-300">search_off</span>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-2">No results found</h3>
                        <p class="text-lg text-gray-500 mb-8">Try adjusting your filters or search term.</p>
                        <a href="faculty_encyclopedia.php" class="inline-flex items-center gap-2 text-wine font-black text-lg hover:gap-4 transition-all">
                            Reset All Filters <span class="material-symbols-outlined">restart_alt</span>
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($faculties as $fac): ?>
                        <div class="faculty-card animate-fadeInUp">
                                <div class="faculty-image-wrapper">
                                    <img src="<?php echo strip_tags($fac['image_url'] ?: 'images/default-profile.png'); ?>" alt="<?php echo strip_tags($fac['name']); ?>">
                                </div>
                                <div class="faculty-info">
                                    <h3 class="faculty-name"><?php echo strip_tags($fac['name']); ?></h3>
                                    <?php if ($fac['job_title']): ?>
                                        <div class="mb-2"><span class="job-badge"><?php echo strip_tags($fac['job_title']); ?></span></div>
                                    <?php endif; ?>
                                    <p class="faculty-dept"><?php echo strip_tags($fac['department']); ?></p>
                                    <a href="profile.php?id=<?php echo $fac['id']; ?>" class="btn-profile">
                                        View Full Profile
                                        <span class="material-symbols-outlined" style="font-size: 16px;">arrow_forward</span>
                                    </a>
                                </div>
                            </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section (Directly from faqs_about_vvu.php design) -->
    <section class="py-24 bg-white dark:bg-gray-900 mt-20">
        <div class="container">
            <div class="max-w-5xl mx-auto text-center glass p-20 rounded-[4rem] shadow-2xl">
                <h2 class="text-5xl sm:text-6xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($page_content['cta_title']); ?></h2>
                <p class="text-2xl text-gray-600 dark:text-gray-400 mb-12 font-medium leading-relaxed">
                    <?php echo strip_tags($page_content['cta_subtitle']); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="contact_us.php" class="px-12 py-6 bg-blue-600 hover:bg-blue-700 text-white text-2xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl">mail</span>
                        Contact Faculty Office
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
