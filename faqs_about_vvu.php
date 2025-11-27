<?php
$pageTitle = "Valley View University - FAQs";
$activePage = "about";
?>

<?php include 'includes/header.php'; ?>

<main class="flex w-full justify-center">
<div class="flex w-full max-w-4xl flex-col px-4 sm:px-6 lg:px-8 py-12 sm:py-16 md:py-24">
<!-- PageHeading & SearchBar Section -->
<section class="flex flex-col items-center text-center gap-6 mb-12">
<div class="flex flex-col gap-3">
<p class="text-gray-900 dark:text-white text-4xl sm:text-5xl font-black leading-tight tracking-[-0.033em]">Have Questions? We Have Answers.</p>
<p class="text-gray-500 dark:text-[#9292c9] text-base md:text-lg font-normal leading-normal max-w-2xl mx-auto">Find answers to the most common questions about Valley View University.</p>
</div>
<div class="w-full max-w-2xl mt-4">
<label class="flex flex-col h-14 w-full">
<div class="flex w-full flex-1 items-stretch rounded-xl h-full shadow-sm">
<div class="text-gray-400 dark:text-[#9292c9] flex bg-white dark:bg-[#232348] items-center justify-center pl-4 rounded-l-xl">
<span class="material-symbols-outlined text-2xl">search</span>
</div>
<input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-gray-900 dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border-none bg-white dark:bg-[#232348] h-full placeholder:text-gray-400 dark:placeholder:text-[#9292c9] px-4 rounded-l-none border-l-0 pl-2 text-base font-normal leading-normal" placeholder="Search for a question..." value=""/>
</div>
</label>
</div>
</section>
<!-- Tabs Component -->
<section class="w-full mb-10">
<div class="border-b border-gray-200 dark:border-[#323267]">
<div class="flex flex-wrap -mb-px px-4 gap-x-8 gap-y-2">
<a class="flex flex-col items-center justify-center border-b-[3px] border-b-primary text-gray-900 dark:text-white pb-3 pt-4" href="#">
<p class="text-sm font-bold leading-normal tracking-[0.015em]">Admissions</p>
</a>
<a class="flex flex-col items-center justify-center border-b-[3px] border-b-transparent text-gray-500 dark:text-[#9292c9] hover:text-gray-700 dark:hover:text-white pb-3 pt-4 transition-colors" href="#">
<p class="text-sm font-bold leading-normal tracking-[0.015em]">Academics</p>
</a>
<a class="flex flex-col items-center justify-center border-b-[3px] border-b-transparent text-gray-500 dark:text-[#9292c9] hover:text-gray-700 dark:hover:text-white pb-3 pt-4 transition-colors" href="#">
<p class="text-sm font-bold leading-normal tracking-[0.015em]">Campus Life</p>
</a>
<a class="flex flex-col items-center justify-center border-b-[3px] border-b-transparent text-gray-500 dark:text-[#9292c9] hover:text-gray-700 dark:hover:text-white pb-3 pt-4 transition-colors" href="#">
<p class="text-sm font-bold leading-normal tracking-[0.015em]">Financial Aid</p>
</a>
</div>
</div>
</section>
<!-- Accordions Component -->
<section class="flex flex-col w-full">
<details class="flex flex-col border-t border-gray-200 dark:border-t-[#323267] py-2 group" open="">
<summary class="flex cursor-pointer items-center justify-between gap-6 py-4 list-none">
<p class="text-gray-800 dark:text-white text-base font-medium leading-normal">What are the application deadlines?</p>
<div class="text-gray-600 dark:text-white transition-transform duration-300 group-open:rotate-180">
<span class="material-symbols-outlined">expand_more</span>
</div>
</summary>
<p class="text-gray-500 dark:text-[#9292c9] text-sm font-normal leading-relaxed pb-4 pr-8">The application deadline for the Fall semester is July 1st, and for the Spring semester, it is December 1st. We encourage early applications as admissions are processed on a rolling basis.</p>
</details>
<details class="flex flex-col border-t border-gray-200 dark:border-t-[#323267] py-2 group">
<summary class="flex cursor-pointer items-center justify-between gap-6 py-4 list-none">
<p class="text-gray-800 dark:text-white text-base font-medium leading-normal">What are the entry requirements for undergraduate programs?</p>
<div class="text-gray-600 dark:text-white transition-transform duration-300 group-open:rotate-180">
<span class="material-symbols-outlined">expand_more</span>
</div>
</summary>
<p class="text-gray-500 dark:text-[#9292c9] text-sm font-normal leading-relaxed pb-4 pr-8">Entry requirements vary by program, but generally include a high school diploma or equivalent, standardized test scores (SAT/ACT), letters of recommendation, and a personal essay. Please check the specific program page for detailed requirements.</p>
</details>
<details class="flex flex-col border-t border-gray-200 dark:border-t-[#323267] py-2 group">
<summary class="flex cursor-pointer items-center justify-between gap-6 py-4 list-none">
<p class="text-gray-800 dark:text-white text-base font-medium leading-normal">How can I apply as an international student?</p>
<div class="text-gray-600 dark:text-white transition-transform duration-300 group-open:rotate-180">
<span class="material-symbols-outlined">expand_more</span>
</div>
</summary>
<p class="text-gray-500 dark:text-[#9292c9] text-sm font-normal leading-relaxed pb-4 pr-8">International students should follow the standard application process and also submit proof of English proficiency (e.g., TOEFL or IELTS scores) and a financial statement. Visit our International Admissions page for more information.</p>
</details>
<details class="flex flex-col border-t border-t-gray-200 dark:border-t-[#323267] border-b border-b-gray-200 dark:border-b-[#323267] py-2 group">
<summary class="flex cursor-pointer items-center justify-between gap-6 py-4 list-none">
<p class="text-gray-800 dark:text-white text-base font-medium leading-normal">Is it possible to visit the campus before applying?</p>
<div class="text-gray-600 dark:text-white transition-transform duration-300 group-open:rotate-180">
<span class="material-symbols-outlined">expand_more</span>
</div>
</summary>
<p class="text-gray-500 dark:text-[#9292c9] text-sm font-normal leading-relaxed pb-4 pr-8">Absolutely! We highly recommend visiting our beautiful campus. You can schedule a campus tour through our website to see our facilities, meet current students, and speak with an admissions counselor.</p>
</details>
</section>
<!-- CTA Section -->
<section class="mt-20 text-center bg-gray-100 dark:bg-primary/20 rounded-xl p-8 sm:p-12">
<div class="flex flex-col items-center gap-4">
<h3 class="text-2xl font-bold text-gray-900 dark:text-white">Still Looking for an Answer?</h3>
<p class="text-gray-600 dark:text-gray-300 max-w-md">Our team is here to help. Get in touch with us for any further questions you may have.</p>
<button class="flex mt-4 min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors">
<span class="truncate">Contact Us</span>
</button>
</div>
</section>
</div>
</main>

<?php include 'includes/footer.php'; ?>