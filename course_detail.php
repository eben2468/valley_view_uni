<?php
$page_title = "B.S. in Computer Science - Valley View University";
$active_page = "academics";
include 'includes/header.php';
?>

<main class="flex-grow">
<!-- HeroSection -->
<section class="relative">
<div class="flex min-h-[560px] flex-col items-start justify-end bg-cover bg-center bg-no-repeat px-6 py-16 md:px-12 md:py-24" data-alt="Students collaborating on a project in a modern university computer lab." style='background-image: linear-gradient(rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0.5) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuAW95d0dbX4loaPFiLcG31lrT7GovHdxFE7MvbKfOtDYpQq2KFuRvuSnnOFQLZTlYTZwheikwsBDQ1cWYnpSyIaepQ4vhMFomCxo2jN5ARRgls5zHfhQZOaOQgV5_AALKEaOLE24yPM3rk_9a5iBQ4_XieMa0uMrHUgZcD1vnDql0ZP79_9sRvXf94xOeXgTbJmAtSDtkQkemgvqZjlVTyj2acoe8k9BInmWBEEwyUzYIbm7vY7ubTetXZbSzz4e3HJ_yQo9lJddl0G");'>
<div class="mx-auto w-full max-w-7xl">
<div class="flex flex-col gap-4 text-left max-w-3xl">
<h1 class="text-white text-4xl font-black leading-tight tracking-tighter md:text-6xl">B.S. in Computer Science</h1>
<p class="text-white/90 text-lg font-normal leading-relaxed md:text-xl">Shaping the future of technology, one line of code at a time. Discover our innovative curriculum and world-class faculty.</p>
</div>
<div class="mt-8 flex flex-wrap gap-4">
<button class="flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-12 px-6 bg-vibrant-blue text-white text-base font-bold tracking-wide hover:bg-vibrant-blue/90 transition-colors">
<span class="truncate">Apply Now</span>
</button>
<button class="flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-12 px-6 bg-off-white text-dark-charcoal text-base font-bold tracking-wide hover:bg-gray-200 transition-colors">
<span class="truncate">Request Info</span>
</button>
</div>
</div>
</div>
</section>
<!-- Sticky Tabs -->
<nav class="sticky top-[69px] z-40 bg-off-white/80 dark:bg-background-dark/80 backdrop-blur-md">
<div class="mx-auto max-w-7xl border-b border-gray-200 dark:border-gray-800">
<div class="flex px-6 gap-8">
<a class="flex flex-col items-center justify-center border-b-[3px] border-b-vibrant-blue text-dark-charcoal dark:text-off-white pb-3 pt-4" href="#overview">
<p class="text-sm font-bold tracking-wide">Overview</p>
</a>
<a class="flex flex-col items-center justify-center border-b-[3px] border-b-transparent text-gray-500 dark:text-gray-400 hover:text-dark-charcoal dark:hover:text-off-white hover:border-b-gray-400 pb-3 pt-4 transition-colors" href="#curriculum">
<p class="text-sm font-bold tracking-wide">Curriculum</p>
</a>
<a class="flex flex-col items-center justify-center border-b-[3px] border-b-transparent text-gray-500 dark:text-gray-400 hover:text-dark-charcoal dark:hover:text-off-white hover:border-b-gray-400 pb-3 pt-4 transition-colors" href="#faculty">
<p class="text-sm font-bold tracking-wide">Faculty</p>
</a>
<a class="flex flex-col items-center justify-center border-b-[3px] border-b-transparent text-gray-500 dark:text-gray-400 hover:text-dark-charcoal dark:hover:text-off-white hover:border-b-gray-400 pb-3 pt-4 transition-colors" href="#careers">
<p class="text-sm font-bold tracking-wide">Careers</p>
</a>
</div>
</div>
</nav>
<div class="mx-auto max-w-7xl px-6 py-16 sm:py-24 space-y-24">
<!-- Stats -->
<section class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
<div class="flex flex-row items-center gap-4 rounded-xl p-6 border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900/50">
<span class="material-symbols-outlined text-warm-gold text-3xl">school</span>
<div class="flex flex-col">
<p class="text-sm font-medium text-gray-600 dark:text-gray-400">Degree Type</p>
<p class="text-xl font-bold">Bachelor of Science</p>
</div>
</div>
<div class="flex flex-row items-center gap-4 rounded-xl p-6 border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900/50">
<span class="material-symbols-outlined text-warm-gold text-3xl">schedule</span>
<div class="flex flex-col">
<p class="text-sm font-medium text-gray-600 dark:text-gray-400">Duration</p>
<p class="text-xl font-bold">4 Years</p>
</div>
</div>
<div class="flex flex-row items-center gap-4 rounded-xl p-6 border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900/50">
<span class="material-symbols-outlined text-warm-gold text-3xl">location_on</span>
<div class="flex flex-col">
<p class="text-sm font-medium text-gray-600 dark:text-gray-400">Location</p>
<p class="text-xl font-bold">On-Campus</p>
</div>
</div>
<div class="flex flex-row items-center gap-4 rounded-xl p-6 border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900/50">
<span class="material-symbols-outlined text-warm-gold text-3xl">event_available</span>
<div class="flex flex-col">
<p class="text-sm font-medium text-gray-600 dark:text-gray-400">Application Deadline</p>
<p class="text-xl font-bold">January 15th</p>
</div>
</div>
</section>
<!-- Program Overview -->
<section class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-center" id="overview">
<div class="lg:col-span-2">
<h2 class="text-3xl font-bold tracking-tight mb-4">Program Overview</h2>
<div class="space-y-4 text-gray-700 dark:text-gray-300 leading-relaxed">
<p>The Bachelor of Science in Computer Science at Valley View University is designed to equip students with a strong foundation in computational theory and practical software development skills. Our program emphasizes a hands-on approach to learning, encouraging students to build, create, and innovate from their very first semester.</p>
<p>We foster a collaborative environment where students work closely with our distinguished faculty on cutting-edge research projects. From artificial intelligence and machine learning to cybersecurity and data science, our curriculum is constantly evolving to reflect the latest advancements in the field, ensuring our graduates are prepared to tackle the complex challenges of the digital age and become leaders in the tech industry.</p>
</div>
</div>
<div class="w-full h-80 rounded-xl bg-cover bg-center" data-alt="A diverse group of students engaged in a lively discussion around a table in a modern classroom." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCX1gJoza50Z9lFoE0Z9n-qhftF7gMf2y4cXW1E1XUx8B7kRE2-Eo6Rw9-fW07xzIianu154FoX-2YokMCDwBb9RzyDNWu8LviNDBN_GZJJKHdUi30ub0vLY1V3ELjynphEhoTnLyJa2fHGC-WYXJWIPoejkA1OUetk0cwRhsT5liU0oq3YllzPOumC5tMpsh64s2z33qBEALxcHdJGkumj5YrtkyCKpmYJznlggnVanKBZj8iefumHx0wzx8opijGcPR31FDtDaDOW')"></div>
</section>
<!-- Curriculum Section with Accordion -->
<section id="curriculum">
<h2 class="text-3xl font-bold tracking-tight mb-8 text-center">Curriculum</h2>
<div class="max-w-4xl mx-auto space-y-4">
<details class="group rounded-xl bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 p-6 cursor-pointer" open="">
<summary class="flex items-center justify-between font-bold text-lg list-none">
                                Year 1: Foundational Concepts
                                <span class="material-symbols-outlined transition-transform duration-300 group-open:rotate-180">expand_more</span>
</summary>
<div class="mt-4 text-gray-700 dark:text-gray-300 space-y-2">
<p><strong>Fall Semester:</strong> Introduction to Programming, Calculus I, Digital Logic Design</p>
<p><strong>Spring Semester:</strong> Data Structures, Calculus II, Discrete Mathematics</p>
</div>
</details>
<details class="group rounded-xl bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 p-6 cursor-pointer">
<summary class="flex items-center justify-between font-bold text-lg list-none">
                                Year 2: Core Competencies
                                <span class="material-symbols-outlined transition-transform duration-300 group-open:rotate-180">expand_more</span>
</summary>
<div class="mt-4 text-gray-700 dark:text-gray-300 space-y-2">
<p><strong>Fall Semester:</strong> Algorithms, Computer Architecture, Linear Algebra</p>
<p><strong>Spring Semester:</strong> Operating Systems, Software Engineering I, Probability &amp; Statistics</p>
</div>
</details>
<details class="group rounded-xl bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 p-6 cursor-pointer">
<summary class="flex items-center justify-between font-bold text-lg list-none">
                                Year 3: Advanced Topics &amp; Specialization
                                <span class="material-symbols-outlined transition-transform duration-300 group-open:rotate-180">expand_more</span>
</summary>
<div class="mt-4 text-gray-700 dark:text-gray-300 space-y-2">
<p><strong>Fall Semester:</strong> Programming Languages, Introduction to AI, Computer Networks</p>
<p><strong>Spring Semester:</strong> Database Systems, CS Elective, Technical Writing</p>
</div>
</details>
<details class="group rounded-xl bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 p-6 cursor-pointer">
<summary class="flex items-center justify-between font-bold text-lg list-none">
                                Year 4: Capstone &amp; Electives
                                <span class="material-symbols-outlined transition-transform duration-300 group-open:rotate-180">expand_more</span>
</summary>
<div class="mt-4 text-gray-700 dark:text-gray-300 space-y-2">
<p><strong>Fall Semester:</strong> Senior Capstone Project I, CS Elective, Ethics in Technology</p>
<p><strong>Spring Semester:</strong> Senior Capstone Project II, CS Elective, Free Elective</p>
</div>
</details>
</div>
</section>
<!-- Faculty Profile Cards -->
<section id="faculty">
<h2 class="text-3xl font-bold tracking-tight mb-8">Meet the Faculty</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
<div class="text-center">
<div class="w-full aspect-square rounded-full mx-auto mb-4 bg-cover bg-center" data-alt="Portrait of Dr. Evelyn Reed, a female professor with glasses, smiling warmly." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDxXD2_MlzdAIv_4p8Oct3FpegqSV1TVrLRrkjtySblLxYGx19asuGE3i8S0tUKzAd1YN67SdmSDOt-vDh0aGMkrhzWt6lUdYoWGNxVke4wUKZ6yfeTyWmOiuJX2y708GE_bAq0U_kKMITMZWYxThXQUmm9k35T4iIvyZJJj0UncT5LtVNHCH1NqyqiP4xSDYRO8lTw-00XTRZB1eEQmUqad5eZN5bFCHZQphMv27tcgIscO9ifvYxmT9Frsb88vQQlPLhBdUxs-dtx')"></div>
<h3 class="font-bold text-lg">Dr. Evelyn Reed</h3>
<p class="text-sm text-vibrant-blue">Department Chair</p>
<p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Ph.D. in AI, Stanford</p>
</div>
<div class="text-center">
<div class="w-full aspect-square rounded-full mx-auto mb-4 bg-cover bg-center" data-alt="Portrait of Dr. Ben Carter, a male professor in a suit, looking thoughtfully at the camera." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCnIqG2X3R5Sekt-Drp1CntxbE-y-Wrqyu9FfxP5pa_7MCT0FSD_ZvKd6JeOGNvzrJSV1w--ic0puwFiXJwTdj89bN73qTS8vuQvYTq1u3tIHsPu_EulluT3CeAFhUDCchyz1yJ1SkbQ1qISINKJ7JaIkAsSrx_DdWatooVRvgH1jWlscFK9f0PHLzZprCkp4mcCJ7e9ntiKGTrR5RApdzO-C9yaiJab5bFv8BlCMUTZwAEhiUQDVwBMJadHBsiWZUWdF_qZf5O3XFr')"></div>
<h3 class="font-bold text-lg">Dr. Ben Carter</h3>
<p class="text-sm text-vibrant-blue">Professor of Cybersecurity</p>
<p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Ph.D. in Cybersecurity, MIT</p>
</div>
<div class="text-center">
<div class="w-full aspect-square rounded-full mx-auto mb-4 bg-cover bg-center" data-alt="Portrait of Dr. Anika Patel, a female professor of Indian descent, smiling." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBeLniHWg50VerDjEFSiMD3ltQdgk6eFpOUpILUlPVjoz7FAKXBjWE7EvJoOS-mOVspo6TBxNqXlOT3BWAjyknj9Dr2n7Zbz6VQgDflIweQ1X2062OkUODl-1lqEEWPGeEO_ho8iRjGfLqb2JTJXTo4JPNlZPHw1384pFyY1ZedvZ_lXxB5aci6PvnHytlH_xUspt1NBHrVAmhaJFFfZ-dUCOAl6OCCZb2Ld3EHBIT9wmvwJ17O7LIZToVjwriybA1a-NpGxSnBNsI2')"></div>
<h3 class="font-bold text-lg">Dr. Anika Patel</h3>
<p class="text-sm text-vibrant-blue">Associate Professor</p>
<p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Ph.D. in Data Science, Carnegie Mellon</p>
</div>
<div class="text-center">
<div class="w-full aspect-square rounded-full mx-auto mb-4 bg-cover bg-center" data-alt="Portrait of Dr. Marcus Chen, a male professor of East Asian descent." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBaWZ4VKfxLcOI4GyslLB7kdmfKrlgRmhMEPY_AnT6-uKwz7STv32rz1he-7CH_Y39jPdOnQodteCmFRU5gxfXGDuRy-SjeRtmouJrYsBmSwRS6CJGZPKG4aqq6x4c2ny0LYLLyHYMslYAOlF9AhVIXxzCKoKj2-t3k2NIxnkMnl3JCd9l0MudXuHSTXAzG6_8ZAZcj6JGcRBIGVv0177Bs67rnKLlUmKf5aGHaY3Vmmn9RtscZWw6Fbv8VpVX_6ItaZgKDPiYvpD4G')"></div>
<h3 class="font-bold text-lg">Dr. Marcus Chen</h3>
<p class="text-sm text-vibrant-blue">Assistant Professor</p>
<p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Ph.D. in HCI, UC Berkeley</p>
</div>
</div>
</section>
<!-- Career Outcomes -->
<section class="bg-deep-teal text-white rounded-xl p-8 md:p-12 text-center" id="careers">
<h2 class="text-3xl font-bold tracking-tight mb-8">Career Outcomes</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
<div class="flex flex-col items-center">
<p class="text-5xl font-black text-warm-gold">95%</p>
<p class="mt-2 font-semibold">Employed or in grad school within 6 months of graduation.</p>
</div>
<div class="flex flex-col items-center">
<p class="text-5xl font-black text-warm-gold">$92,000</p>
<p class="mt-2 font-semibold">Average starting salary for our graduates.</p>
</div>
<div class="flex flex-col items-center">
<p class="text-lg font-bold mb-3">Our graduates work at top companies:</p>
<div class="flex flex-wrap justify-center items-center gap-6 opacity-80">
<span class="text-2xl font-bold">Google</span>
<span class="text-2xl font-bold">Microsoft</span>
<span class="text-2xl font-bold">Amazon</span>
<span class="text-2xl font-bold">Apple</span>
<span class="text-2xl font-bold">Meta</span>
</div>
</div>
</div>
</section>
<!-- Testimonial Slider -->
<section>
<h2 class="text-3xl font-bold tracking-tight mb-8 text-center">Student Success Stories</h2>
<div class="relative max-w-3xl mx-auto rounded-xl bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 p-8 text-center">
<div class="w-20 h-20 rounded-full mx-auto mb-4 bg-cover bg-center" data-alt="Portrait of alumni Sarah Johnson smiling." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBuUxOQUmMV44_mhIbfhmFknzxy_BQ2PQLOpUsWLGVegcDK76Lky7le_4KEsFXu8rKpN02AbHRBgUKaGjJlkCufGqCnUWD29nhsZZR68ttp5MGvTBG2q0CETa4t5UApJm8bS9kkIdgwzZEevQoNkgorVf-2FoTSWuj4m_uoiIPi0o6-FxuxiC_tM1ugXfrGWlsG9ImBZoguT1d0UamZFs5MgEoRkU0YJERqnvT2RR-z0gQyQBookZKKP0iEjM9bieRv2GqL7bIZ3Qmb')"></div>
<blockquote class="text-lg italic text-gray-700 dark:text-gray-300">
                            "The computer science program at Valley View gave me the skills and confidence to land my dream job at a top tech company. The hands-on projects and supportive faculty were instrumental to my success."
                        </blockquote>
<cite class="block font-bold mt-4">Sarah Johnson '22</cite>
<p class="text-sm text-gray-500">Software Engineer, Google</p>
</div>
</section>
</div>
</main>

<?php include 'includes/footer.php'; ?>