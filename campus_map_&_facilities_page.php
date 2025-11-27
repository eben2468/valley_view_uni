<?php
$page_title = "Campus Map & Facilities - Valley View University";
$active_page = "about";
include 'includes/header.php';
?>

<!-- Main Content -->
<main class="flex-grow">
<div class="mx-auto max-w-screen-2xl p-4 sm:p-6 lg:p-8">
<!-- PageHeading Component -->
<div class="flex flex-wrap justify-between gap-4 p-4 mb-4">
<div class="flex flex-col gap-2">
<p class="text-4xl font-black leading-tight tracking-[-0.033em] text-text-light dark:text-text-dark">Campus Map &amp; Facilities</p>
<p class="text-base font-normal leading-normal text-text-muted-light dark:text-text-muted-dark">
                          Explore our vibrant campus, discover key locations, and find everything you need to know about our state-of-the-art facilities.
                        </p>
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 h-[calc(100vh-200px)] min-h-[700px]">
<!-- Sidebar Column -->
<aside class="lg:col-span-1 flex flex-col gap-6 bg-card-light dark:bg-card-dark p-6 rounded-xl shadow-sm border border-border-light dark:border-border-dark">
<!-- Search Bar -->
<div class="relative">
<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-text-muted-light dark:text-text-muted-dark">search</span>
<input class="w-full rounded-full border border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark py-3 pl-12 pr-4 text-sm focus:ring-2 focus:ring-primary/50 focus:border-primary/50" placeholder="Search for a building or facility..." type="search"/>
</div>
<!-- Chips Component (Category Filters) -->
<div class="flex gap-2 p-1 flex-wrap">
<button class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-full px-4 bg-accent text-primary ring-2 ring-accent">
<span class="material-symbols-outlined text-xl">school</span>
<p class="text-sm font-bold leading-normal">Academics</p>
</button>
<button class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-full px-4 bg-background-light dark:bg-background-dark hover:bg-gray-200 dark:hover:bg-gray-700/50 transition-colors border border-border-light dark:border-border-dark">
<span class="material-symbols-outlined text-xl">sports_soccer</span>
<p class="text-sm font-medium leading-normal">Athletics</p>
</button>
<button class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-full px-4 bg-background-light dark:bg-background-dark hover:bg-gray-200 dark:hover:bg-gray-700/50 transition-colors border border-border-light dark:border-border-dark">
<span class="material-symbols-outlined text-xl">bed</span>
<p class="text-sm font-medium leading-normal">Residence</p>
</button>
<button class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-full px-4 bg-background-light dark:bg-background-dark hover:bg-gray-200 dark:hover:bg-gray-700/50 transition-colors border border-border-light dark:border-border-dark">
<span class="material-symbols-outlined text-xl">restaurant</span>
<p class="text-sm font-medium leading-normal">Dining</p>
</button>
<button class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-full px-4 bg-background-light dark:bg-background-dark hover:bg-gray-200 dark:hover:bg-gray-700/50 transition-colors border border-border-light dark:border-border-dark">
<span class="material-symbols-outlined text-xl">help</span>
<p class="text-sm font-medium leading-normal">Services</p>
</button>
</div>
<!-- Card Component (Facility Details) -->
<div class="flex flex-col items-stretch justify-start rounded-xl overflow-hidden border border-border-light dark:border-border-dark flex-grow">
<div class="w-full bg-center bg-no-repeat aspect-video bg-cover" data-alt="A bright, modern library interior with students studying" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAcJJfU4dxd8FzjqcSfH8iv7P3_eZ2e5Ly-BuS2RqIWsdjhdzhPPuYzJpekxBX2j5-B1zmAlu5PAkuPDsIDSeeLWyzkG0RU7-EybI27soAamkeKWeX9-PyA9PeQ6Wexc7Sx3CGa-YvVRzeYy0S3-xvxBYwaCoBvcDnMy_lJzSuaihbwlwTFuSn76VGceLp1OnFJsjoCR7X_i-LcMcZSGKP_m23TBqtWJhtv0sRywKsCf4axkiiP06ERRehEHRS3tY53kj3_kJWjOqJJ");'></div>
<div class="flex w-full min-w-72 grow flex-col items-stretch justify-between gap-4 p-5">
<div>
<p class="text-sm font-medium leading-normal text-primary dark:text-accent">Library</p>
<p class="text-xl font-bold leading-tight tracking-[-0.015em] text-text-light dark:text-text-dark">Asa V. Call Law Library</p>
<div class="flex flex-col gap-2 mt-2">
<p class="text-sm font-normal leading-normal text-text-muted-light dark:text-text-muted-dark">
                                        The main library at Valley View University, offering a vast collection of resources, quiet study areas, and collaborative workspaces.
                                    </p>
<p class="text-sm font-normal leading-normal text-text-muted-light dark:text-text-muted-dark">Hours: 8:00 AM - 11:00 PM</p>
</div>
</div>
<button class="flex w-full min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-full h-11 px-4 bg-primary text-white text-sm font-bold leading-normal hover:opacity-90 transition-opacity">
<span class="material-symbols-outlined">directions</span>
<span class="truncate">Get Directions</span>
</button>
</div>
</div>
</aside>
<!-- Map Column -->
<main class="lg:col-span-2 min-h-[500px] lg:min-h-0">
<!-- Map Component -->
<div class="flex flex-col h-full rounded-xl overflow-hidden border border-border-light dark:border-border-dark">
<div class="bg-cover bg-center flex flex-1 flex-col justify-between p-6" data-alt="A stylized, colorful vector map of Valley View University campus" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBoHD698VqRiw6IrMCLsWVd1RQ4escK_MeregA0BFaOYafGHnI8RDqZuGhQ-ImOa2teNMAdktjxQzKBN368Yc3TDMepcRauqVIgIUSM2p-JnHJKLStKzpUubouj8t32C6JcjRn-i49Nqo_k3y6sHThzU94utujmsEzBi6pi0f7s025eV36R7jREF75uiSzrzlpeSa5mUqSnSgBmeZzOas4hc7Bh-oQLBUn0-bNQokyIEfrw2zctUyXcT50hXpmLM4aCbRTAkpHU3_w2");'>
<!-- Search bar on map for mobile/small screens is not needed as it's in the sidebar -->
<div></div>
<!-- Map Controls -->
<div class="flex flex-col items-end gap-3 self-end">
<div class="flex flex-col gap-0.5 rounded-lg shadow-lg">
<button class="flex size-11 items-center justify-center rounded-t-lg bg-card-light dark:bg-card-dark hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors">
<span class="material-symbols-outlined text-text-light dark:text-text-dark">add</span>
</button>
<button class="flex size-11 items-center justify-center rounded-b-lg bg-card-light dark:bg-card-dark hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors">
<span class="material-symbols-outlined text-text-light dark:text-text-dark">remove</span>
</button>
</div>
<button class="flex size-11 items-center justify-center rounded-lg bg-card-light dark:bg-card-dark shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors">
<span class="material-symbols-outlined text-text-light dark:text-text-dark">my_location</span>
</button>
</div>
</div>
</div>
</main>
</div>
</div>
</main>

<?php
include 'includes/footer.php';
?>