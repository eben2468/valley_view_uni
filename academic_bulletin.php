<?php
$page_title = "Academic Bulletin - Valley View University";
$active_page = "academics";
include 'includes/header.php';
?>

<main class="flex-1">
<div class="mx-auto w-full max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
<div class="flex flex-wrap gap-2 py-4">
<a class="text-[#4c4c9a] dark:text-[#aab4c4] text-sm font-medium leading-normal hover:underline" href="index.php">Home</a>
<span class="text-[#4c4c9a] dark:text-[#aab4c4] text-sm font-medium leading-normal">/</span>
<a class="text-[#4c4c9a] dark:text-[#aab4c4] text-sm font-medium leading-normal hover:underline" href="academics.php">Academics</a>
<span class="text-[#4c4c9a] dark:text-[#aab4c4] text-sm font-medium leading-normal">/</span>
<span class="text-[#0d0d1b] dark:text-[#f8f8fc] text-sm font-medium leading-normal">Academic Bulletin</span>
</div>
<div class="@container mt-4">
<div class="relative flex min-h-[480px] flex-col gap-6 overflow-hidden bg-cover bg-center bg-no-repeat @[480px]:gap-8 rounded-xl items-start justify-end px-6 pb-10 @[480px]:px-10" data-alt="Bright, modern university library with students studying">
<div class="absolute inset-0 bg-cover bg-center" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAt_csgFmCzW6LLfxfTp4O4pJFO3p5QFC4RX_8Z84ZTv5vlrzMu5GlATQSHHE-kTq3GC-liTD1ffWG4rLsAo3_P7IYR4y-4D4kC1KZEyd6im2iIKO9FElVsl2OvPZI3cUu2R4FKzHNxdGK59Nm_ZhrWFd8-x77sk5s4VK9hO4OAqIPXGT9yGMl8XSjkxPhTa6VSDLjhdnLceEQE3Vuapkaoo-UOMQWofTj5x-lNVv8XAgokZR0kdL-ZoTRFAlGemNS_Ytu-6XD96XY4");'></div>
<div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
<div class="relative z-10 flex flex-col gap-2 text-left">
<h1 class="text-white text-4xl font-black leading-tight tracking-[-0.033em] @[480px]:text-5xl">
                                    Academic Bulletin
                                </h1>
<p class="text-white text-sm font-normal leading-normal @[480px]:text-base max-w-xl">
                                    Your official guide to academic policies, programs, and course offerings at Valley View University.
                                </p>
</div>
<button class="relative z-10 flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 @[480px]:h-12 @[480px]:px-5 bg-primary text-[#f8f8fc] text-sm font-bold leading-normal tracking-[0.015em] @[480px]:text-base hover:bg-primary/90 transition-colors">
<span class="truncate">Download Full Bulletin (PDF)</span>
</button>
</div>
</div>
<div class="mt-12">
<label class="flex flex-col min-w-40 h-14 w-full">
<div class="flex w-full flex-1 items-stretch rounded-xl h-full">
<div class="text-[#4c4c9a] flex border-none bg-[#e7e7f3] dark:bg-[#e7e7f3]/10 items-center justify-center pl-4 rounded-l-xl border-r-0">
<span class="material-symbols-outlined text-2xl">search</span>
</div>
<input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#0d0d1b] dark:text-[#f8f8fc] focus:outline-0 focus:ring-2 focus:ring-primary/50 border-none bg-[#e7e7f3] dark:bg-[#e7e7f3]/10 h-full placeholder:text-[#4c4c9a] dark:placeholder:text-gray-400 px-4 rounded-l-none border-l-0 pl-2 text-base font-normal leading-normal" placeholder="Search courses, programs, or policies..." value=""/>
</div>
</label>
</div>
<h2 class="text-[#0d0d1b] dark:text-[#f8f8fc] text-[22px] font-bold leading-tight tracking-[-0.015em] pb-3 pt-12">Explore the Bulletin</h2>
<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mt-4">
<div class="flex flex-col gap-4 rounded-xl border border-[#e7e7f3] dark:border-[#e7e7f3]/10 bg-white dark:bg-background-dark p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer">
<div class="flex size-12 items-center justify-center rounded-lg bg-primary/10 text-primary">
<span class="material-symbols-outlined text-3xl">gavel</span>
</div>
<div class="flex flex-col">
<h3 class="text-base font-bold text-[#0d0d1b] dark:text-[#f8f8fc]">Academic Policies</h3>
<p class="text-sm text-[#4c4c9a] dark:text-gray-400 mt-1">Review official university policies and procedures.</p>
</div>
</div>
<div class="flex flex-col gap-4 rounded-xl border border-[#e7e7f3] dark:border-[#e7e7f3]/10 bg-white dark:bg-background-dark p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer">
<div class="flex size-12 items-center justify-center rounded-lg bg-primary/10 text-primary">
<span class="material-symbols-outlined text-3xl">school</span>
</div>
<div class="flex flex-col">
<h3 class="text-base font-bold text-[#0d0d1b] dark:text-[#f8f8fc]">Undergraduate Programs</h3>
<p class="text-sm text-[#4c4c9a] dark:text-gray-400 mt-1">Discover majors, minors, and degree requirements.</p>
</div>
</div>
<div class="flex flex-col gap-4 rounded-xl border border-[#e7e7f3] dark:border-[#e7e7f3]/10 bg-white dark:bg-background-dark p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer">
<div class="flex size-12 items-center justify-center rounded-lg bg-primary/10 text-primary">
<span class="material-symbols-outlined text-3xl">workspaces</span>
</div>
<div class="flex flex-col">
<h3 class="text-base font-bold text-[#0d0d1b] dark:text-[#f8f8fc]">Graduate Programs</h3>
<p class="text-sm text-[#4c4c9a] dark:text-gray-400 mt-1">Explore advanced degrees and professional studies.</p>
</div>
</div>
<div class="flex flex-col gap-4 rounded-xl border border-[#e7e7f3] dark:border-[#e7e7f3]/10 bg-white dark:bg-background-dark p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer">
<div class="flex size-12 items-center justify-center rounded-lg bg-primary/10 text-primary">
<span class="material-symbols-outlined text-3xl">menu_book</span>
</div>
<div class="flex flex-col">
<h3 class="text-base font-bold text-[#0d0d1b] dark:text-[#f8f8fc]">Course Catalog</h3>
<p class="text-sm text-[#4c4c9a] dark:text-gray-400 mt-1">Browse the complete list of available courses.</p>
</div>
</div>
</div>
</div>
</main>

<?php include 'includes/footer.php'; ?>