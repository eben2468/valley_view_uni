<?php
$pageTitle = "Student Digital Hub - Valley View University";
$activePage = "student_hub";
include 'includes/header.php';
?>

<main class="flex-grow">
<!-- HeroSection -->
<section class="relative">
<div class="container mx-auto px-4 py-16 sm:py-24">
<div class="flex min-h-[400px] flex-col items-center justify-center gap-6 rounded-xl bg-cover bg-center p-8 text-center" data-alt="A photo of a university campus with students walking." style='background-image: linear-gradient(to right, rgba(0, 43, 91, 0.8), rgba(0, 167, 157, 0.7)), url("https://lh3.googleusercontent.com/aida-public/AB6AXuCU3rbHraTZ6v3pyoi__2vMUt-N0YzC2fiKm0EbIY9k4u_AnLmG_Mz04eE5shtDCbFTL5ISb-zPCzmJE5lz4A29IDfzLU2K3lgyywjr8vndpokHeuJIyHjMudD9_Bldv2Hucry81Zb1xhC5GPSJcqldtHlBuCqfDqeqMrAOtCJzd1It3-Y7URFxluvDVS-nqIR0s8mSRgPLLABJd7VLGVqM0m5bOWWkdGgdlPvUgnUht5IlGyMplmo8R-MoJm9jfnjzvrNJ2T6IOrtP");'>
<div class="flex max-w-3xl flex-col gap-2">
<h1 class="text-4xl font-black leading-tight tracking-tighter text-white sm:text-5xl md:text-6xl">
                                Welcome to Your Digital Hub
                            </h1>
<p class="text-base font-normal leading-normal text-white/90 sm:text-lg">
                                All your essential student resources, right at your fingertips.
                            </p>
</div>
</div>
</div>
</section>
<!-- Access Cards Section -->
<section class="container mx-auto -mt-24 px-4 pb-16 sm:pb-24">
<div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
<!-- Card 1: Email -->
<div class="flex flex-col rounded-xl border border-gray-200/80 bg-card-light p-6 shadow-lg shadow-gray-200/50 transition-all hover:-translate-y-1 hover:shadow-xl dark:border-gray-700/50 dark:bg-card-dark dark:shadow-none">
<div class="flex flex-col items-start gap-4">
<div class="rounded-lg bg-primary/10 p-3 text-primary dark:bg-secondary/20 dark:text-secondary">
<span class="material-symbols-outlined !text-4xl">mail</span>
</div>
<h3 class="text-xl font-bold">Student Email</h3>
<p class="text-sm font-normal text-text-muted-light dark:text-text-muted-dark flex-grow">Access your official university mailbox for important communications.</p>
</div>
<button class="mt-6 flex w-full items-center justify-center rounded-lg h-12 px-5 bg-primary text-white text-base font-bold tracking-wide transition-colors hover:bg-primary/90">
<span>Open Mailbox</span>
</button>
</div>
<!-- Card 2: iSchool -->
<div class="flex flex-col rounded-xl border border-gray-200/80 bg-card-light p-6 shadow-lg shadow-gray-200/50 transition-all hover:-translate-y-1 hover:shadow-xl dark:border-gray-700/50 dark:bg-card-dark dark:shadow-none">
<div class="flex flex-col items-start gap-4">
<div class="rounded-lg bg-accent/10 p-3 text-accent dark:bg-accent/20 dark:text-accent">
<span class="material-symbols-outlined !text-4xl">school</span>
</div>
<h3 class="text-xl font-bold">iSchool Portal</h3>
<p class="text-sm font-normal text-text-muted-light dark:text-text-muted-dark flex-grow">Check your grades, register for courses, and view your academic records.</p>
</div>
<button class="mt-6 flex w-full items-center justify-center rounded-lg h-12 px-5 bg-primary text-white text-base font-bold tracking-wide transition-colors hover:bg-primary/90">
<span>Go to iSchool</span>
</button>
</div>
<!-- Card 3: E-Learning -->
<div class="flex flex-col rounded-xl border border-gray-200/80 bg-card-light p-6 shadow-lg shadow-gray-200/50 transition-all hover:-translate-y-1 hover:shadow-xl dark:border-gray-700/50 dark:bg-card-dark dark:shadow-none">
<div class="flex flex-col items-start gap-4">
<div class="rounded-lg bg-secondary/20 p-3 text-yellow-600 dark:bg-secondary/20 dark:text-secondary">
<span class="material-symbols-outlined !text-4xl">laptop_chromebook</span>
</div>
<h3 class="text-xl font-bold">E-Learning Platform</h3>
<p class="text-sm font-normal text-text-muted-light dark:text-text-muted-dark flex-grow">Find your course materials, submit assignments, and engage in online classes.</p>
</div>
<button class="mt-6 flex w-full items-center justify-center rounded-lg h-12 px-5 bg-primary text-white text-base font-bold tracking-wide transition-colors hover:bg-primary/90">
<span>Enter E-Learning</span>
</button>
</div>
</div>
</section>
<!-- Quick Links/Announcements Section -->
<section class="bg-gray-100 dark:bg-card-dark/50 py-16 sm:py-24">
<div class="container mx-auto px-4">
<div class="mx-auto max-w-4xl text-center">
<h2 class="text-3xl font-bold tracking-tight">Announcements &amp; Quick Links</h2>
<p class="mt-2 text-base text-text-muted-light dark:text-text-muted-dark">Stay updated with the latest news and access other useful resources.</p>
</div>
<div class="mt-12 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4">
<div class="flex flex-col items-center text-center">
<div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 text-primary dark:bg-secondary/20 dark:text-secondary"><span class="material-symbols-outlined">event</span></div>
<h4 class="mt-4 font-bold">Academic Calendar</h4>
<p class="mt-1 text-sm text-text-muted-light dark:text-text-muted-dark">View key dates and deadlines for the semester.</p>
<a class="mt-2 text-sm font-bold text-primary hover:underline dark:text-secondary" href="#">View Calendar</a>
</div>
<div class="flex flex-col items-center text-center">
<div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 text-primary dark:bg-secondary/20 dark:text-secondary"><span class="material-symbols-outlined">local_library</span></div>
<h4 class="mt-4 font-bold">Library Resources</h4>
<p class="mt-1 text-sm text-text-muted-light dark:text-text-muted-dark">Access digital archives, databases, and research guides.</p>
<a class="mt-2 text-sm font-bold text-primary hover:underline dark:text-secondary" href="#">Visit Library</a>
</div>
<div class="flex flex-col items-center text-center">
<div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 text-primary dark:bg-secondary/20 dark:text-secondary"><span class="material-symbols-outlined">payments</span></div>
<h4 class="mt-4 font-bold">Financial Aid</h4>
<p class="mt-1 text-sm text-text-muted-light dark:text-text-muted-dark">Manage your financial aid and scholarship information.</p>
<a class="mt-2 text-sm font-bold text-primary hover:underline dark:text-secondary" href="#">Go to Portal</a>
</div>
<div class="flex flex-col items-center text-center">
<div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 text-primary dark:bg-secondary/20 dark:text-secondary"><span class="material-symbols-outlined">help_center</span></div>
<h4 class="mt-4 font-bold">IT Help Desk</h4>
<p class="mt-1 text-sm text-text-muted-light dark:text-text-muted-dark">Get technical support for university systems.</p>
<a class="mt-2 text-sm font-bold text-primary hover:underline dark:text-secondary" href="#">Get Help</a>
</div>
</div>
</div>
</section>
</main>

<?php
include 'includes/footer.php';
?>