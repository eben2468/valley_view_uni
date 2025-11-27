<?php
$pageTitle = "Student Handbook - Valley View University";
$activePage = "academics";
include 'includes/header.php';
?>

<main class="mt-8 flex flex-col gap-8">
<!-- Breadcrumbs -->
<div class="flex flex-wrap gap-2 px-4">
<a class="text-primary dark:text-secondary/80 text-base font-medium leading-normal hover:underline" href="index.php">Home</a>
<span class="text-neutral-text/50 dark:text-neutral-text-dark/50 text-base font-medium leading-normal">/</span>
<a class="text-primary dark:text-secondary/80 text-base font-medium leading-normal hover:underline" href="academics.php">Academics</a>
<span class="text-neutral-text/50 dark:text-neutral-text-dark/50 text-base font-medium leading-normal">/</span>
<span class="text-neutral-text dark:text-neutral-text-dark text-base font-medium leading-normal">Student Handbook</span>
</div>
<!-- HeroSection -->
<div class="@container px-4">
<div class="flex min-h-[480px] flex-col gap-6 bg-cover bg-center bg-no-repeat @[480px]:gap-8 rounded-xl items-start justify-end px-4 pb-10 @[480px]:px-10" data-alt="Diverse group of students collaborating in a bright, modern university library" style='background-image: linear-gradient(rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.6) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuA1qk11WLDP6FdWtUwaIDDL3PrYPP1wcb0maln_OjoKhrJEL6S8NQFler_HvXzSmnRXFFmMbsIknW7JwOFxqen3YFfNRU1g2vQb8GpTM8uuWm3QGVJaXVpt7CUio3qos3-WNFoMpYrOUMLY6eyAam66XfQNH61yDKF86jZVKa9K4fsKObcGqw4WgMXpYb9cMzoQM9BFf57cYyeCmfM1rJ_vdQieCM-k4IOVfA-sHUzRLp1h-Yqo6yY1CQHw2_3IlToinaEaEJGOWkQs");'>
<div class="flex flex-col gap-2 text-left">
<h1 class="text-white text-4xl font-black leading-tight tracking-[-0.033em] @[480px]:text-5xl">
                                    Valley View University Student Handbook
                                </h1>
<h2 class="text-white/90 text-sm font-normal leading-normal @[480px]:text-lg">
                                    Your guide to academic success and campus life.
                                </h2>
</div>
<button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-opacity-90 transition-colors">
<span class="truncate">Download Full Handbook (PDF)</span>
</button>
</div>
</div>
<!-- Search and Content Section -->
<div class="px-4">
<!-- SearchBar -->
<div class="py-3">
<label class="flex flex-col min-w-40 h-12 w-full">
<div class="flex w-full flex-1 items-stretch rounded-lg h-full">
<div class="text-neutral-text/70 dark:text-neutral-text-dark/70 flex border-none bg-white dark:bg-neutral-subtle-dark items-center justify-center pl-4 rounded-l-lg border-r-0">
<span class="material-symbols-outlined">search</span>
</div>
<input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-r-lg text-neutral-text dark:text-neutral-text-dark focus:outline-0 focus:ring-2 focus:ring-primary/50 border-none bg-white dark:bg-neutral-subtle-dark h-full placeholder:text-neutral-text/70 dark:placeholder:text-neutral-text-dark/70 px-4 pl-2 text-base font-normal leading-normal" placeholder="Search the handbook..." value=""/>
</div>
</label>
</div>
</div>
<div class="flex flex-col lg:flex-row gap-8 px-4">
<!-- Table of Contents Sidebar -->
<aside class="w-full lg:w-1/4 lg:sticky top-28 h-fit">
<div class="bg-white dark:bg-neutral-subtle-dark rounded-xl p-6">
<h3 class="text-lg font-bold text-primary dark:text-secondary mb-4">Table of Contents</h3>
<ul class="space-y-3">
<li><a class="text-neutral-text dark:text-neutral-text-dark hover:text-primary dark:hover:text-secondary transition-colors font-medium" href="#academic-integrity">Academic Integrity</a></li>
<li><a class="text-neutral-text dark:text-neutral-text-dark hover:text-primary dark:hover:text-secondary transition-colors font-medium" href="#code-of-conduct">Student Code of Conduct</a></li>
<li><a class="text-neutral-text dark:text-neutral-text-dark hover:text-primary dark:hover:text-secondary transition-colors font-medium" href="#campus-resources">Campus Resources</a></li>
<li><a class="text-neutral-text dark:text-neutral-text-dark hover:text-primary dark:hover:text-secondary transition-colors font-medium" href="#health-wellness">Health &amp; Wellness</a></li>
<li><a class="text-neutral-text dark:text-neutral-text-dark hover:text-primary dark:hover:text-secondary transition-colors font-medium" href="#financial-policies">Financial Policies</a></li>
<li><a class="text-neutral-text dark:text-neutral-text-dark hover:text-primary dark:hover:text-secondary transition-colors font-medium" href="#residential-life">Residential Life</a></li>
</ul>
</div>
</aside>
<!-- Main Content - Interactive Handbook -->
<div class="w-full lg:w-3/4 flex flex-col gap-8">
<!-- SectionHeader -->
<h2 class="text-neutral-text dark:text-neutral-text-dark text-[22px] font-bold leading-tight tracking-[-0.015em] pb-3 pt-5">Browse by Section</h2>
<!-- Section Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<a class="group flex flex-col gap-4 bg-white dark:bg-neutral-subtle-dark rounded-xl p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300" href="#academic-integrity">
<div class="bg-primary/10 dark:bg-secondary/20 rounded-full size-12 flex items-center justify-center">
<span class="material-symbols-outlined text-primary dark:text-secondary text-3xl">school</span>
</div>
<div class="flex flex-col">
<h3 class="text-lg font-bold text-neutral-text dark:text-neutral-text-dark">Academic Integrity</h3>
<p class="text-neutral-text/80 dark:text-neutral-text-dark/80 text-sm mt-1">Policies on plagiarism, cheating, and academic honesty.</p>
</div>
</a>
<a class="group flex flex-col gap-4 bg-white dark:bg-neutral-subtle-dark rounded-xl p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300" href="#code-of-conduct">
<div class="bg-primary/10 dark:bg-secondary/20 rounded-full size-12 flex items-center justify-center">
<span class="material-symbols-outlined text-primary dark:text-secondary text-3xl">gavel</span>
</div>
<div class="flex flex-col">
<h3 class="text-lg font-bold text-neutral-text dark:text-neutral-text-dark">Student Code of Conduct</h3>
<p class="text-neutral-text/80 dark:text-neutral-text-dark/80 text-sm mt-1">Expectations for student behavior and disciplinary procedures.</p>
</div>
</a>
<a class="group flex flex-col gap-4 bg-white dark:bg-neutral-subtle-dark rounded-xl p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300" href="#campus-resources">
<div class="bg-primary/10 dark:bg-secondary/20 rounded-full size-12 flex items-center justify-center">
<span class="material-symbols-outlined text-primary dark:text-secondary text-3xl">map</span>
</div>
<div class="flex flex-col">
<h3 class="text-lg font-bold text-neutral-text dark:text-neutral-text-dark">Campus Resources</h3>
<p class="text-neutral-text/80 dark:text-neutral-text-dark/80 text-sm mt-1">Library, tutoring, IT services, and other support systems.</p>
</div>
</a>
<a class="group flex flex-col gap-4 bg-white dark:bg-neutral-subtle-dark rounded-xl p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300" href="#health-wellness">
<div class="bg-primary/10 dark:bg-secondary/20 rounded-full size-12 flex items-center justify-center">
<span class="material-symbols-outlined text-primary dark:text-secondary text-3xl">favorite</span>
</div>
<div class="flex flex-col">
<h3 class="text-lg font-bold text-neutral-text dark:text-neutral-text-dark">Health &amp; Wellness</h3>
<p class="text-neutral-text/80 dark:text-neutral-text-dark/80 text-sm mt-1">Information on health services, counseling, and wellness programs.</p>
</div>
</a>
<a class="group flex flex-col gap-4 bg-white dark:bg-neutral-subtle-dark rounded-xl p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300" href="#financial-policies">
<div class="bg-primary/10 dark:bg-secondary/20 rounded-full size-12 flex items-center justify-center">
<span class="material-symbols-outlined text-primary dark:text-secondary text-3xl">payments</span>
</div>
<div class="flex flex-col">
<h3 class="text-lg font-bold text-neutral-text dark:text-neutral-text-dark">Financial Policies</h3>
<p class="text-neutral-text/80 dark:text-neutral-text-dark/80 text-sm mt-1">Tuition, fees, financial aid, and payment information.</p>
</div>
</a>
<a class="group flex flex-col gap-4 bg-white dark:bg-neutral-subtle-dark rounded-xl p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300" href="#residential-life">
<div class="bg-primary/10 dark:bg-secondary/20 rounded-full size-12 flex items-center justify-center">
<span class="material-symbols-outlined text-primary dark:text-secondary text-3xl">cottage</span>
</div>
<div class="flex flex-col">
<h3 class="text-lg font-bold text-neutral-text dark:text-neutral-text-dark">Residential Life</h3>
<p class="text-neutral-text/80 dark:text-neutral-text-dark/80 text-sm mt-1">Guidelines for living in university housing.</p>
</div>
</a>
</div>
</div>
</div>
</main>

<?php
include 'includes/footer.php';
?>