<?php
$page_title = "Apply Now - Valley View University";
include 'includes/header.php';
?>

<main class="flex-grow py-16">
<div class="container mx-auto px-4 max-w-4xl">
<div class="text-center mb-12">
<h1 class="text-4xl font-bold text-primary dark:text-secondary mb-4">Start Your Application</h1>
<p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">Take the first step toward your future at Valley View University. Our application process is designed to be straightforward and supportive.</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 mb-12">
<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
<div class="text-center">
<div class="w-16 h-16 bg-primary/10 dark:bg-secondary/20 rounded-full flex items-center justify-center mx-auto mb-4">
<span class="text-primary dark:text-secondary text-2xl font-bold">1</span>
</div>
<h3 class="text-xl font-bold mb-2">Create Account</h3>
<p class="text-gray-600 dark:text-gray-300">Set up your applicant portal to begin your application journey.</p>
</div>
<div class="text-center">
<div class="w-16 h-16 bg-primary/10 dark:bg-secondary/20 rounded-full flex items-center justify-center mx-auto mb-4">
<span class="text-primary dark:text-secondary text-2xl font-bold">2</span>
</div>
<h3 class="text-xl font-bold mb-2">Complete Application</h3>
<p class="text-gray-600 dark:text-gray-300">Fill out our comprehensive application form with your details.</p>
</div>
<div class="text-center">
<div class="w-16 h-16 bg-primary/10 dark:bg-secondary/20 rounded-full flex items-center justify-center mx-auto mb-4">
<span class="text-primary dark:text-secondary text-2xl font-bold">3</span>
</div>
<h3 class="text-xl font-bold mb-2">Submit Documents</h3>
<p class="text-gray-600 dark:text-gray-300">Send your transcripts, test scores, and recommendations to our admissions office.</p>
</div>
</div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Undergraduate Application</h2>
<p class="text-gray-600 dark:text-gray-300 mb-6">For students applying to begin their undergraduate studies at Valley View University.</p>
<ul class="space-y-3 mb-8">
<li class="flex items-start">
<span class="text-primary dark:text-secondary mr-2">✓</span>
<span>High school transcripts</span>
</li>
<li class="flex items-start">
<span class="text-primary dark:text-secondary mr-2">✓</span>
<span>Standardized test scores (SAT/ACT)</span>
</li>
<li class="flex items-start">
<span class="text-primary dark:text-secondary mr-2">✓</span>
<span>Letters of recommendation</span>
</li>
<li class="flex items-start">
<span class="text-primary dark:text-secondary mr-2">✓</span>
<span>Personal essay</span>
</li>
</ul>
<a href="#" class="block w-full text-center bg-primary text-white py-3 rounded-lg font-bold hover:bg-primary/90 transition-colors">Apply Now</a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Graduate Application</h2>
<p class="text-gray-600 dark:text-gray-300 mb-6">For students applying to our graduate programs and research opportunities.</p>
<ul class="space-y-3 mb-8">
<li class="flex items-start">
<span class="text-primary dark:text-secondary mr-2">✓</span>
<span>Undergraduate transcripts</span>
</li>
<li class="flex items-start">
<span class="text-primary dark:text-secondary mr-2">✓</span>
<span>GRE/GMAT scores (if required)</span>
</li>
<li class="flex items-start">
<span class="text-primary dark:text-secondary mr-2">✓</span>
<span>Letters of recommendation</span>
</li>
<li class="flex items-start">
<span class="text-primary dark:text-secondary mr-2">✓</span>
<span>Statement of purpose</span>
</li>
</ul>
<a href="#" class="block w-full text-center bg-primary text-white py-3 rounded-lg font-bold hover:bg-primary/90 transition-colors">Apply Now</a>
</div>
</div>

<div class="mt-16 bg-primary/5 dark:bg-secondary/10 rounded-xl p-8">
<h2 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-4">Need Help With Your Application?</h2>
<p class="text-center text-gray-600 dark:text-gray-300 mb-6 max-w-2xl mx-auto">Our admissions counselors are here to support you through every step of the application process.</p>
<div class="flex flex-wrap justify-center gap-4">
<a href="contact_us.php" class="bg-primary text-white px-6 py-3 rounded-lg font-bold hover:bg-primary/90 transition-colors">Contact Admissions</a>
<a href="admissions.php" class="bg-white dark:bg-gray-700 text-primary dark:text-white px-6 py-3 rounded-lg font-bold hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">Learn More</a>
</div>
</div>
</div>
</main>

<?php
include 'includes/footer.php';
?>