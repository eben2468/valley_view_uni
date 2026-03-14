<?php
$page_title = "Course Details - Valley View University";
$active_page = "academics";
include 'includes/header.php';
require_once('includes/db_connect.php');

// Get course ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch Program Details with Category
$stmt = $pdo->prepare("
    SELECT ap.*, pc.name as category_name, pc.color_1, pc.color_2 
    FROM academic_programs ap 
    JOIN program_categories pc ON ap.category_id = pc.id 
    WHERE ap.id = ? AND ap.is_active = 1
");
$stmt->execute([$id]);
$course = $stmt->fetch();

if (!$course) {
    echo "<div class='container py-24 text-center'><h1 class='text-4xl font-black'>Course not found</h1><a href='academic_programs_overview.php' class='text-blue-600 mt-4 block'>Return to Programs</a></div>";
    include 'includes/footer.php';
    exit;
}

// Decode JSON fields
$learning_points = json_decode($course['learning_points'], true) ?? [];
$career_paths = json_decode($course['career_paths'], true) ?? [];
?>


<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
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
    .detail-card {
        border-radius: 2.5rem;
        padding: 3rem;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
        border: 1px solid #f3f4f6;
    }
    .dark .detail-card {
        border-color: #374151;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative py-24 overflow-hidden" style="background: linear-gradient(135deg, <?php echo $course['color_1']; ?>, <?php echo $course['color_2']; ?>);">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto">
                <a href="academic_programs_overview.php" class="inline-flex items-center gap-2 text-blue-200 hover:text-white mb-12 transition-colors text-xl font-bold">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Back to Programs
                </a>
                
                <div class="inline-block px-6 py-2 rounded-full text-white font-black text-lg mb-8 uppercase tracking-widest" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.3);">
                    <?php echo strip_tags($course['category_name']); ?>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl font-black text-white mb-8 leading-tight animate-fadeInUp">
                    <?php echo strip_tags($course['title']); ?>
                </h1>
                
                <div class="flex flex-wrap gap-8 text-white/80 text-2xl font-medium animate-fadeInUp" style="animation-delay: 0.1s;">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-yellow-400 text-3xl">calendar_today</span>
                        <?php echo strip_tags($course['duration']); ?>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-yellow-400 text-3xl">layers</span>
                        <?php echo strip_tags($course['level']); ?>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-yellow-400 text-3xl">location_on</span>
                        <?php echo strip_tags($course['campus']); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="py-24 -mt-12 relative z-20">
        <div class="container">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 max-w-7xl mx-auto">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-12">
                    <div class="detail-card bg-white dark:bg-gray-800 animate-fadeInUp" style="animation-delay: 0.2s;">
                        <h2 class="text-4xl font-black text-gray-900 dark:text-white mb-8">Program Overview</h2>
                        <!-- Short Description (Lead-in) -->
                        <p class="text-3xl font-bold text-blue-700 dark:text-blue-400 leading-tight mb-8 italic">
                            <?php echo strip_tags($course['description']); ?>
                        </p>

                        <!-- Full Description -->
                        <?php if (!empty($course['full_description'])): ?>
                            <div class="text-2xl text-gray-600 dark:text-gray-400 leading-relaxed space-y-4">
                                <?php echo nl2br(strip_tags($course['full_description'])); ?>
                            </div>
                        <?php else: ?>
                            <p class="text-2xl text-gray-600 dark:text-gray-400 leading-relaxed">
                                The <?php echo strip_tags($course['title']); ?> at Valley View University is designed to provide students with a deep understanding of the core principles and advanced practices in <?php echo strip_tags($course['category_name']); ?>. Our curriculum combines theoretical knowledge with practical applications, ensuring that graduates are well-prepared for the challenges of the modern professional world.
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="detail-card bg-white dark:bg-gray-800 animate-fadeInUp" style="animation-delay: 0.3s;">
                        <h2 class="text-4xl font-black text-gray-900 dark:text-white mb-8">What You Will Learn</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php if (empty($learning_points)): ?>
                                <p class="text-xl text-gray-500 italic col-span-2">Learning outcomes information is currently being updated.</p>
                            <?php else: ?>
                                <?php foreach ($learning_points as $point): ?>
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 shrink-0">
                                        <span class="material-symbols-outlined text-2xl">check</span>
                                    </div>
                                    <p class="text-xl text-gray-700 dark:text-gray-300 font-medium"><?php echo strip_tags($point); ?></p>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-12">
                    <div class="detail-card bg-blue-600 text-white animate-fadeInUp shadow-2xl shadow-blue-600/30" style="animation-delay: 0.4s;">
                        <h3 class="text-3xl font-black mb-6">Apply Today</h3>
                        <p class="text-xl text-blue-50 mb-8 leading-relaxed">Take the first step towards your future career. Applications are now open for the next academic session.</p>
                        <a href="admissions.php" class="w-full py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-xl font-bold rounded-2xl transition-all flex items-center justify-center gap-3 shadow-xl">
                            Start Application
                            <span class="material-symbols-outlined">rocket_launch</span>
                        </a>
                        <p class="text-center mt-6 text-blue-100 font-medium">Need help? <a href="contact.php" class="text-white underline">Contact an Advisor</a></p>
                    </div>

                    <div class="detail-card bg-white dark:bg-gray-800 animate-fadeInUp" style="animation-delay: 0.5s;">
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-6">Career Paths</h3>
                        <ul class="space-y-4">
                            <?php if (empty($career_paths)): ?>
                                <li class="text-xl text-gray-500 italic font-medium">Career path information available upon request.</li>
                            <?php else: ?>
                                <?php foreach ($career_paths as $path): ?>
                                <li class="flex items-center gap-4 text-xl text-gray-600 dark:text-gray-400 font-medium">
                                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                    <?php echo strip_tags($path); ?>
                                </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
