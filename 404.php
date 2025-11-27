<?php
$page_title = "Page Not Found - Valley View University";
include 'includes/header.php';
?>

<main class="flex-grow py-16">
<div class="container mx-auto px-4 max-w-4xl">
<div class="text-center py-16">
<div class="text-6xl font-bold text-primary dark:text-secondary mb-6">404</div>
<h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Page Not Found</h1>
<p class="text-xl text-gray-600 dark:text-gray-300 mb-8">Sorry, the page you're looking for doesn't exist or has been moved.</p>
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 max-w-2xl mx-auto">
<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">What would you like to do?</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<a href="homepage.php" class="bg-primary text-white py-3 px-4 rounded-lg font-medium text-center hover:bg-primary/90 transition-colors">Go to Homepage</a>
<a href="contact_us.php" class="bg-white dark:bg-gray-700 text-primary dark:text-white border border-primary py-3 px-4 rounded-lg font-medium text-center hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">Contact Us</a>
<a href="academics.php" class="bg-white dark:bg-gray-700 text-primary dark:text-white border border-primary py-3 px-4 rounded-lg font-medium text-center hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">Explore Academics</a>
<a href="admissions.php" class="bg-white dark:bg-gray-700 text-primary dark:text-white border border-primary py-3 px-4 rounded-lg font-medium text-center hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">Learn About Admissions</a>
</div>
</div>
</div>
</div>
</main>

<?php
include 'includes/footer.php';
?>