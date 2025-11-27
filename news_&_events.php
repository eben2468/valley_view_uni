<?php
$page_title = "News & Events - Valley View University";
$active_page = "news";
include 'includes/header.php';
?>

<!-- Main Content -->
<main class="flex flex-col gap-8 md:gap-12 py-8 md:py-12">
<!-- HeroSection -->
<div class="@container">
<div class="@[480px]:px-4">
<div class="flex min-h-[480px] flex-col gap-6 bg-cover bg-center bg-no-repeat @[480px]:gap-8 @[480px]:rounded-xl items-start justify-end px-6 pb-10 @[480px]:px-10" data-alt="University students collaborating on a project in a modern sunlit atrium" style='background-image: linear-gradient(rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0.5) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuCO5sFw8VIGPxuVQwMkKpPxF5EgpOsbNsc0bmLbGRvxBUi4fMPS3utyArLyQuWWJnns_186A78-76xftIUZKx6qklKyadYdAimbk2gJ03eH5wAwvYzcTwamMe41Dxs67adE2uZqt9dHn6WcNwde-hTJNgPAWngBo6Di_b5q2w1dGjXUNB0PlHZ-qvD5ICtz3XD5oolDZxDw2jDByTYhh1Q6HtKRLpGZU-rCoo2Mckl8jKsW3X34GjTn-Ai_m0h7-oqaocKT2GXrD5jS");'>
<div class="flex flex-col gap-2 text-left max-w-3xl">
<h1 class="text-white text-4xl font-black leading-tight tracking-[-0.033em] @[480px]:text-5xl @[480px]:font-black @[480px]:leading-tight @[480px]:tracking-[-0.033em]">Annual Innovation Fair: Shaping Tomorrow's World</h1>
<h2 class="text-white text-sm font-normal leading-normal @[480px]:text-base @[480px]:font-normal @[480px]:leading-normal">Join us for a week of groundbreaking projects, inspiring speakers, and networking opportunities that celebrate the spirit of innovation at Valley View University.</h2>
</div>
<button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 @[480px]:h-12 @[480px]:px-5 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] @[480px]:text-base @[480px]:font-bold @[480px]:leading-normal @[480px]:tracking-[0.015em] hover:opacity-90 transition-opacity">
<span class="truncate">Learn More</span>
</button>
</div>
</div>
</div>
<!-- Filters -->
<div class="flex flex-col md:flex-row gap-4 justify-between items-center p-4">
<div class="flex gap-2 flex-wrap">
<button class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-full bg-primary text-white px-4 text-sm font-medium leading-normal">All</button>
<button class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-full bg-card-light dark:bg-card-dark px-4 text-sm font-medium leading-normal hover:bg-primary/20 dark:hover:bg-primary/50">News</button>
<button class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-full bg-card-light dark:bg-card-dark px-4 text-sm font-medium leading-normal hover:bg-primary/20 dark:hover:bg-primary/50">Events</button>
<button class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-full bg-card-light dark:bg-card-dark px-4 text-sm font-medium leading-normal hover:bg-primary/20 dark:hover:bg-primary/50">Announcements</button>
<button class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-full bg-card-light dark:bg-card-dark px-4 text-sm font-medium leading-normal hover:bg-primary/20 dark:hover:bg-primary/50">Press Releases</button>
</div>
<label class="flex flex-col w-full md:min-w-40 md:max-w-64">
<div class="flex w-full flex-1 items-stretch rounded-lg h-10 bg-card-light dark:bg-card-dark">
<div class="text-text-light/70 dark:text-text-dark/70 flex items-center justify-center pl-3">
<span class="material-symbols-outlined text-xl">search</span>
</div>
<input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg focus:outline-0 focus:ring-0 border-none h-full placeholder:text-text-light/70 dark:placeholder:text-text-dark/70 pl-2 text-base font-normal leading-normal bg-transparent" placeholder="Search articles..." value=""/>
</div>
</label>
</div>
<!-- ImageGrid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 p-4">
<div class="flex flex-col gap-4 pb-3 rounded-xl bg-card-light dark:bg-card-dark overflow-hidden group">
<div class="w-full bg-center bg-no-repeat aspect-video bg-cover group-hover:scale-105 transition-transform duration-300" data-alt="A scientist looking into a microscope in a modern laboratory" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDL02aFvDrReZRn6lqG5RJQo557LINSup9kJnlIgcjh8XKruujYJAahT0664mFgPbNIOM1uBGNSYNUVAFoWTMx4DHIk83z_ZMd7k-GPmtMNasO8WqStUz8_fBlFoSgHr2XHwR_CGU8PK0Rwsx9ncKXZwNy2AG-CktjnSSXRPzLz5SlYWoxahrWcdjF-2nkJ2Y4wk0qN9xO9OUH8-FsuFtYQ6UQdQ1I6wjOghoLJiq_OPbAsH7sKwf56uFju_ihGGqxp5e9qlVyG8rJY");'></div>
<div class="p-4 flex flex-col gap-2">
<p class="text-secondary text-xs font-bold uppercase">News</p>
<p class="text-lg font-bold leading-tight">Research Breakthrough in the Science Dept</p>
<p class="text-text-light/80 dark:text-text-dark/80 text-sm font-normal leading-normal">October 26, 2023</p>
<a class="text-primary dark:text-secondary text-sm font-bold leading-normal mt-2" href="#">Read More →</a>
</div>
</div>
<div class="flex flex-col gap-4 pb-3 rounded-xl bg-card-light dark:bg-card-dark overflow-hidden group">
<div class="w-full bg-center bg-no-repeat aspect-video bg-cover group-hover:scale-105 transition-transform duration-300" data-alt="An auditorium filled with people listening to a speaker on stage" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuD55dScL5g2W2w7H74QbfzgN9vqYgVZyB4_uGI1hDhHNXkR05fV40TXChVqqFLopwrt6LNQ9scPsXdk5Fl6-tDojJQa5_Sp18v7JNQDhItbfiPHXVaFHthyQm7YGo61NIfX46k9jtiEIzv9sanIO2efiH0bl1F1EIbStMlH56xO8uAumYNgZGDtutTQP0oW6xOsHpe8G6ZaTJE3bWXVRwDwqgX6ikp_QCs9NUNa_Xc20xOH_RuYKGDPiadeTkZoWezsy2g7X72mA3So");'></div>
<div class="p-4 flex flex-col gap-2">
<p class="text-secondary text-xs font-bold uppercase">Event</p>
<p class="text-lg font-bold leading-tight">Upcoming Guest Lecture Series</p>
<p class="text-text-light/80 dark:text-text-dark/80 text-sm font-normal leading-normal">November 5, 2023 | 4:00 PM</p>
<a class="text-primary dark:text-secondary text-sm font-bold leading-normal mt-2" href="#">View Details →</a>
</div>
</div>
<div class="flex flex-col gap-4 pb-3 rounded-xl bg-card-light dark:bg-card-dark overflow-hidden group">
<div class="w-full bg-center bg-no-repeat aspect-video bg-cover group-hover:scale-105 transition-transform duration-300" data-alt="Portrait of a smiling professional woman in a modern office" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAXbNzPEf6pbrdHyA5d3gn7c6yhj_TIw84ihg5jiKlf0MSwfPSpWsvvMcL7UGb8ZXISmwzwDKBn7qk14l3b0nLcl0qEK_T0Lp3L06k3fh8aS5ApJr08nbhRrn9-BMEsKKbPB83-Gx7XLisVAs0TUGH-MOA0zKOlszixFLWNaPROYKWi4Ae3gmh0eV0kZIycA5XM0DHHv1x6VeBnKZk-U5ADXRO0ynWIYbXPRYEqAB5qYHix00ur36JuCW0CI_yFKeCFo-CO6NEgWxuq");'></div>
<div class="p-4 flex flex-col gap-2">
<p class="text-secondary text-xs font-bold uppercase">News</p>
<p class="text-lg font-bold leading-tight">Alumni Spotlight: CEO of Tech Startup</p>
<p class="text-text-light/80 dark:text-text-dark/80 text-sm font-normal leading-normal">October 24, 2023</p>
<a class="text-primary dark:text-secondary text-sm font-bold leading-normal mt-2" href="#">Read More →</a>
</div>
</div>
<div class="flex flex-col gap-4 pb-3 rounded-xl bg-card-light dark:bg-card-dark overflow-hidden group">
<div class="w-full bg-center bg-no-repeat aspect-video bg-cover group-hover:scale-105 transition-transform duration-300" data-alt="American football players in action on a university field" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuA59SOgvMvyLulumpWUAliJGRrv01kk0lu-p7hl3BH8HiceqBronQ8xiJQf6vFJvC1Jax74Ytvhvn8PNxe8_TeOF6mF9d_BVMyWzVOQYt0g5eDFyB2XYBy68TwAOKOePHXwNQtDpG2cJE3_rBkjytHzIkte-iOqYMy9wqb45hOkTb7a-1kl3uhAsgJDsJ56sRDXyUjMpe7xLmDDQEz1XktFqdBeUz0jNjimFGihnNNgumNzaUhqHqYeWeEpsEKvK2psr1rojelUsxZc");'></div>
<div class="p-4 flex flex-col gap-2">
<p class="text-secondary text-xs font-bold uppercase">Event</p>
<p class="text-lg font-bold leading-tight">Homecoming Football Game Details</p>
<p class="text-text-light/80 dark:text-text-dark/80 text-sm font-normal leading-normal">November 12, 2023 | 1:00 PM</p>
<a class="text-primary dark:text-secondary text-sm font-bold leading-normal mt-2" href="#">View Details →</a>
</div>
</div>
<div class="flex flex-col gap-4 pb-3 rounded-xl bg-card-light dark:bg-card-dark overflow-hidden group">
<div class="w-full bg-center bg-no-repeat aspect-video bg-cover group-hover:scale-105 transition-transform duration-300" data-alt="A university student studying in a library with books in the background" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuB3YoM-94ILNLPmqz45k0tpg7hb6Do8khUqCLLoS8K4qSiRFc7K-WT0Czm6dlHd5J8b2YE6_etT-bFek34RdVG52jwLeygmcb7W1xY70uIKH84aenTesOeTf9NsC_CBSN6TqoVuXs8FPIziI7nzp4IDYWDDoM_PJvi1P7qhSbECWL3ya6xmcMTYKOeegK7l5BQBDSyYgRYkCHspRSrquAkGnp0iVv2xivFiKCyu-RETjc5ylGBBg7u_iT2JC_X9VqQLNi7WeDkvDFsa");'></div>
<div class="p-4 flex flex-col gap-2">
<p class="text-secondary text-xs font-bold uppercase">Announcement</p>
<p class="text-lg font-bold leading-tight">Semester Registration Dates Announced</p>
<p class="text-text-light/80 dark:text-text-dark/80 text-sm font-normal leading-normal">October 22, 2023</p>
<a class="text-primary dark:text-secondary text-sm font-bold leading-normal mt-2" href="#">Read More →</a>
</div>
</div>
<div class="flex flex-col gap-4 pb-3 rounded-xl bg-card-light dark:bg-card-dark overflow-hidden group">
<div class="w-full bg-center bg-no-repeat aspect-video bg-cover group-hover:scale-105 transition-transform duration-300" data-alt="A modern university building with a large glass facade" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDRE0-qaEqpzoetavkzIHrCp5PNkMoOZQ1zHU-3O4yq6N-h5xMYoUw0gvSe9HSbF8zrTT5zo4J1zfAYoJr5Nx6vsRjhAMe7JSRcmx1zNJzkXB_v5F2i3FG3c2B2CrzsxMKVfh9YLEgPnPSbd_yLxt69Uws_Y5BtD2CzzttAX3tj84cIp5McNom3VlGu6pFbc7YbeZ3OuusEVLoox7zGBYEB_EGoTYgfu3L_zCokAXOw-m-Yp59XkWYULOy7jeMrikZTazVDxzzFppNO");'></div>
<div class="p-4 flex flex-col gap-2">
<p class="text-secondary text-xs font-bold uppercase">Press Release</p>
<p class="text-lg font-bold leading-tight">New Campus Wing Inauguration Ceremony</p>
<p class="text-text-light/80 dark:text-text-dark/80 text-sm font-normal leading-normal">October 20, 2023</p>
<a class="text-primary dark:text-secondary text-sm font-bold leading-normal mt-2" href="#">Read More →</a>
</div>
</div>
</div>
<!-- Pagination and CTA -->
<div class="flex flex-col md:flex-row justify-between items-center gap-6 p-4">
<nav aria-label="Pagination" class="flex items-center gap-2">
<a class="flex items-center justify-center size-10 rounded-lg bg-card-light dark:bg-card-dark hover:bg-primary/20 dark:hover:bg-primary/50" href="#">
<span class="material-symbols-outlined">chevron_left</span>
</a>
<a class="flex items-center justify-center size-10 rounded-lg bg-primary text-white" href="#">1</a>
<a class="flex items-center justify-center size-10 rounded-lg bg-card-light dark:bg-card-dark hover:bg-primary/20 dark:hover:bg-primary/50" href="#">2</a>
<a class="flex items-center justify-center size-10 rounded-lg bg-card-light dark:bg-card-dark hover:bg-primary/20 dark:hover:bg-primary/50" href="#">3</a>
<span class="flex items-center justify-center size-10">...</span>
<a class="flex items-center justify-center size-10 rounded-lg bg-card-light dark:bg-card-dark hover:bg-primary/20 dark:hover:bg-primary/50" href="#">8</a>
<a class="flex items-center justify-center size-10 rounded-lg bg-card-light dark:bg-card-dark hover:bg-primary/20 dark:hover:bg-primary/50" href="#">
<span class="material-symbols-outlined">chevron_right</span>
</a>
</nav>
<button class="flex min-w-[84px] w-full md:w-auto max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-secondary text-primary text-base font-bold leading-normal tracking-[0.015em] hover:opacity-90 transition-opacity">
<span class="truncate">Submit an Event/News</span>
</button>
</div>
</main>

<?php
include 'includes/footer.php';
?>