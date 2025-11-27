<?php
$page_title = "Academic Calendar - Valley View University";
$active_page = "academics";
include 'includes/header.php';
?>

<!-- Main Content -->
<main class="w-full max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-10 py-8 lg:py-12">
<!-- PageHeading -->
<div class="mb-10">
<div class="flex flex-col gap-2">
<h1 class="text-4xl lg:text-5xl font-black leading-tight tracking-[-0.033em] text-text-light dark:text-text-dark">Academic Calendar 2024-2025</h1>
<p class="text-base lg:text-lg font-normal leading-normal text-text-light/70 dark:text-text-dark/70">Important dates, deadlines, and holidays for the academic year.</p>
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
<!-- Left Sidebar: Filters -->
<aside class="lg:col-span-3 space-y-8">
<div class="p-6 bg-white dark:bg-background-dark rounded-xl border border-border-light dark:border-border-dark">
<h3 class="text-lg font-bold leading-tight tracking-[-0.015em] pb-4">Filter Events</h3>
<!-- TextField (Semester Selector) -->
<div class="mb-6">
<label class="flex flex-col min-w-40 flex-1">
<p class="text-sm font-medium leading-normal pb-2">Semester</p>
<select class="form-select w-full rounded-lg text-text-light dark:text-text-dark focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark h-12 text-sm font-normal leading-normal">
<option selected="">Fall 2024</option>
<option>Spring 2025</option>
<option>Summer 2025</option>
</select>
</label>
</div>
<!-- Checklists (Categories) -->
<div class="space-y-1">
<h4 class="text-sm font-medium leading-normal pb-2">Category</h4>
<label class="flex items-center gap-x-3 py-2 cursor-pointer">
<input checked="" class="h-5 w-5 rounded border-border-light dark:border-border-dark border-2 bg-transparent text-primary checked:bg-primary checked:border-primary focus:ring-primary/50" type="checkbox"/>
<p class="text-sm font-normal">Registration Deadlines</p>
</label>
<label class="flex items-center gap-x-3 py-2 cursor-pointer">
<input checked="" class="h-5 w-5 rounded border-border-light dark:border-border-dark border-2 bg-transparent text-primary checked:bg-primary checked:border-primary focus:ring-primary/50" type="checkbox"/>
<p class="text-sm font-normal">Holidays</p>
</label>
<label class="flex items-center gap-x-3 py-2 cursor-pointer">
<input class="h-5 w-5 rounded border-border-light dark:border-border-dark border-2 bg-transparent text-primary checked:bg-primary checked:border-primary focus:ring-primary/50" type="checkbox"/>
<p class="text-sm font-normal">Examination Periods</p>
</label>
<label class="flex items-center gap-x-3 py-2 cursor-pointer">
<input class="h-5 w-5 rounded border-border-light dark:border-border-dark border-2 bg-transparent text-primary checked:bg-primary checked:border-primary focus:ring-primary/50" type="checkbox"/>
<p class="text-sm font-normal">Academic Events</p>
</label>
</div>
<button class="w-full mt-6 flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-11 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors">Apply Filters</button>
</div>
<div class="p-6 bg-white dark:bg-background-dark rounded-xl border border-border-light dark:border-border-dark">
<button class="w-full flex cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-11 px-4 bg-accent-teal/10 dark:bg-accent-teal/20 text-accent-teal text-sm font-bold leading-normal tracking-[0.015em] hover:bg-accent-teal/20 dark:hover:bg-accent-teal/30 transition-colors">
<span class="material-symbols-outlined text-lg">print</span>
<span>Print Calendar</span>
</button>
</div>
</aside>
<!-- Center: Calendar View -->
<main class="lg:col-span-6">
<div class="p-4 sm:p-6 bg-white dark:bg-background-dark rounded-xl border border-border-light dark:border-border-dark">
<div class="flex items-center justify-between mb-4">
<button class="p-2 rounded-full hover:bg-background-light dark:hover:bg-white/10 transition-colors">
<span class="material-symbols-outlined">chevron_left</span>
</button>
<h3 class="text-xl font-bold">October 2024</h3>
<button class="p-2 rounded-full hover:bg-background-light dark:hover:bg-white/10 transition-colors">
<span class="material-symbols-outlined">chevron_right</span>
</button>
</div>
<div class="grid grid-cols-7 text-center text-sm font-medium text-text-light/60 dark:text-text-dark/60">
<div class="py-2">Sun</div><div class="py-2">Mon</div><div class="py-2">Tue</div><div class="py-2">Wed</div><div class="py-2">Thu</div><div class="py-2">Fri</div><div class="py-2">Sat</div>
</div>
<div class="grid grid-cols-7 text-center">
<div class="h-20 sm:h-24 py-2 text-text-light/40 dark:text-text-dark/40 border-t border-border-light dark:border-border-dark">29</div>
<div class="h-20 sm:h-24 py-2 text-text-light/40 dark:text-text-dark/40 border-t border-border-light dark:border-border-dark">30</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">1</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">2</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">3</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">4</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">5</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">6</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">7</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark relative">8
                  <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1">
<div class="size-2 rounded-full bg-accent-gold"></div>
</div>
</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">9</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">10</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">11</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">12</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">13</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark bg-primary/10 rounded-lg relative">14
                  <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 flex items-center justify-center font-bold text-primary dark:text-accent-gold bg-primary/20 dark:bg-accent-gold/20 size-8 rounded-full"></span>
<div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1">
<div class="size-2 rounded-full bg-accent-teal"></div>
<div class="size-2 rounded-full bg-accent-gold"></div>
</div>
</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">15</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">16</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">17</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">18</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">19</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">20</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">21</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">22</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">23</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">24</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">25</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">26</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">27</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">28</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">29</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">30</div>
<div class="h-20 sm:h-24 py-2 border-t border-border-light dark:border-border-dark">31</div>
<div class="h-20 sm:h-24 py-2 text-text-light/40 dark:text-text-dark/40 border-t border-border-light dark:border-border-dark">1</div>
<div class="h-20 sm:h-24 py-2 text-text-light/40 dark:text-text-dark/40 border-t border-border-light dark:border-border-dark">2</div>
</div>
</div>
</main>
<!-- Right Sidebar: Event List -->
<aside class="lg:col-span-3">
<div class="p-6 bg-white dark:bg-background-dark rounded-xl border border-border-light dark:border-border-dark h-full">
<h3 class="text-lg font-bold leading-tight tracking-[-0.015em] pb-4">Upcoming Events</h3>
<div class="space-y-4 max-h-[600px] overflow-y-auto pr-2">
<div class="flex items-start gap-4 p-4 rounded-lg bg-background-light dark:bg-white/5">
<div class="flex-shrink-0 text-center">
<p class="text-xs font-bold text-accent-teal uppercase">OCT</p>
<p class="text-2xl font-black text-primary dark:text-white">08</p>
</div>
<div class="flex-grow">
<p class="font-bold text-sm mb-1">Last Day to Add a Class</p>
<p class="text-xs text-text-light/70 dark:text-text-dark/70">Final deadline for Fall 2024 course registration without a late fee.</p>
<button class="text-xs font-bold text-accent-teal mt-2 flex items-center gap-1 hover:underline">
<span class="material-symbols-outlined text-sm">add_circle</span> Add to Calendar
                  </button>
</div>
</div>
<div class="flex items-start gap-4 p-4 rounded-lg bg-background-light dark:bg-white/5">
<div class="flex-shrink-0 text-center">
<p class="text-xs font-bold text-accent-teal uppercase">OCT</p>
<p class="text-2xl font-black text-primary dark:text-white">14</p>
</div>
<div class="flex-grow">
<p class="font-bold text-sm mb-1">Indigenous Peoples' Day</p>
<p class="text-xs text-text-light/70 dark:text-text-dark/70">University offices closed. No classes will be held.</p>
<button class="text-xs font-bold text-accent-teal mt-2 flex items-center gap-1 hover:underline">
<span class="material-symbols-outlined text-sm">add_circle</span> Add to Calendar
                  </button>
</div>
</div>
<div class="flex items-start gap-4 p-4 rounded-lg bg-background-light dark:bg-white/5">
<div class="flex-shrink-0 text-center">
<p class="text-xs font-bold text-accent-teal uppercase">NOV</p>
<p class="text-2xl font-black text-primary dark:text-white">11</p>
</div>
<div class="flex-grow">
<p class="font-bold text-sm mb-1">Veterans Day Holiday</p>
<p class="text-xs text-text-light/70 dark:text-text-dark/70">University closed in observance of Veterans Day.</p>
<button class="text-xs font-bold text-accent-teal mt-2 flex items-center gap-1 hover:underline">
<span class="material-symbols-outlined text-sm">add_circle</span> Add to Calendar
                  </button>
</div>
</div>
<div class="flex items-start gap-4 p-4 rounded-lg bg-background-light dark:bg-white/5">
<div class="flex-shrink-0 text-center">
<p class="text-xs font-bold text-accent-teal uppercase">NOV</p>
<p class="text-2xl font-black text-primary dark:text-white">28</p>
</div>
<div class="flex-grow">
<p class="font-bold text-sm mb-1">Thanksgiving Break</p>
<p class="text-xs text-text-light/70 dark:text-text-dark/70">Begins Nov 28, classes resume Dec 2.</p>
<button class="text-xs font-bold text-accent-teal mt-2 flex items-center gap-1 hover:underline">
<span class="material-symbols-outlined text-sm">add_circle</span> Add to Calendar
                  </button>
</div>
</div>
</div>
</div>
</aside>
</div>
</main>

<?php include 'includes/footer.php'; ?>