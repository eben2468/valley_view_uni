<?php
$page_title = "Academics - Valley View University";
$active_page = "academics";
include 'includes/header.php';
?>

<!-- HeroSection -->
<main class="flex flex-col gap-12 py-10">
<div class="@container px-4">
<div class="flex min-h-[480px] flex-col gap-6 bg-cover bg-center bg-no-repeat @[480px]:gap-8 rounded-xl items-start justify-end px-4 pb-10 @[480px]:px-10" data-alt="Students collaborating in a modern university library with natural light." style='background-image: linear-gradient(rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0.4) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuDrcNrFI6F4Q-KTm5IfRdihit04w7qQMN_HVaTJAtKo7ahRCKV4k09-c7Yl1hHweDBrfdQ4hZMm73kjq2o87m3M268Q-_7x3t_DgV6eTBTBwEVuFYr87d5_yqwsiIVN46lt_Hhauk8m624gz4bYZdYtk0BVD_3eBZatqrpO5vFQ0vpuLxN7BK8-iJJHRsYPTXJqO-ueK-f4TSfQmpoBzuG5pArhzJe6zsNwE7tsiaua8iL-Rnz2jTHW4LrogYCU467va9KQnpbr5pvZ");'>
<div class="flex flex-col gap-2 text-left">
<h1 class="text-white text-4xl font-black leading-tight tracking-[-0.033em] @[480px]:text-5xl @[480px]:font-black @[480px]:leading-tight @[480px]:tracking-[-0.033em]">
                                        Explore Your Future at Valley View
                                    </h1>
<h2 class="text-white text-sm font-normal leading-normal @[480px]:text-base @[480px]:font-normal @[480px]:leading-normal">
                                        Discover a world of academic excellence and diverse programs designed to help you succeed.
                                    </h2>
</div>
</div>
</div>
<!-- Faculties Section with Tabs -->
<section class="flex flex-col gap-6">
<div class="px-4">
<h3 class="text-2xl font-bold text-[#0d0d1b] dark:text-white">Our Faculties</h3>
<p class="text-base text-[#4c4c9a] dark:text-gray-400">Select a faculty to explore departments and programs.</p>
</div>
<div class="pb-3">
<div class="flex border-b border-[#cfcfe7] dark:border-gray-700 px-4 gap-8">
<a class="flex flex-col items-center justify-center border-b-[3px] border-b-primary text-[#0d0d1b] dark:text-white pb-[13px] pt-4" href="#">
<p class="text-sm font-bold leading-normal tracking-[0.015em]">Faculty of Science &amp; Technology</p>
</a>
<a class="flex flex-col items-center justify-center border-b-[3px] border-b-transparent text-[#4c4c9a] dark:text-gray-400 dark:hover:text-white pb-[13px] pt-4" href="#">
<p class="text-sm font-bold leading-normal tracking-[0.015em]">Faculty of Business</p>
</a>
<a class="flex flex-col items-center justify-center border-b-[3px] border-b-transparent text-[#4c4c9a] dark:text-gray-400 dark:hover:text-white pb-[13px] pt-4" href="#">
<p class="text-sm font-bold leading-normal tracking-[0.015em]">Faculty of Arts &amp; Social Sciences</p>
</a>
</div>
</div>
<!-- Departments and Courses Accordion -->
<div class="p-4">
<div class="flex flex-col">
<details class="flex flex-col border-t border-t-[#cfcfe7] dark:border-t-gray-700 py-2 group" open="">
<summary class="flex cursor-pointer items-center justify-between gap-6 py-2">
<p class="text-[#0d0d1b] dark:text-white text-base font-medium leading-normal">Computer Science</p>
<div class="text-[#0d0d1b] dark:text-white group-open:rotate-180 transition-transform">
<span class="material-symbols-outlined">expand_more</span>
</div>
</summary>
<p class="text-[#4c4c9a] dark:text-gray-400 text-sm font-normal leading-normal pb-4">A comprehensive program covering software development, artificial intelligence, and data structures. Learn more about curriculum and career opportunities.</p>
<div class="flex flex-col pl-4 border-l border-l-[#cfcfe7] dark:border-l-gray-700 gap-2">
<a class="text-[#4c4c9a] dark:text-gray-400 dark:hover:text-primary hover:text-primary text-sm font-normal leading-normal" href="#">BSc. Computer Science</a>
<a class="text-[#4c4c9a] dark:text-gray-400 dark:hover:text-primary hover:text-primary text-sm font-normal leading-normal" href="#">BSc. Information Technology</a>
<a class="text-[#4c4c9a] dark:text-gray-400 dark:hover:text-primary hover:text-primary text-sm font-normal leading-normal" href="#">BSc. Cybersecurity</a>
</div>
</details>
<details class="flex flex-col border-t border-t-[#cfcfe7] dark:border-t-gray-700 py-2 group">
<summary class="flex cursor-pointer items-center justify-between gap-6 py-2">
<p class="text-[#0d0d1b] dark:text-white text-base font-medium leading-normal">Physics</p>
<div class="text-[#0d0d1b] dark:text-white group-open:rotate-180 transition-transform">
<span class="material-symbols-outlined">expand_more</span>
</div>
</summary>
<p class="text-[#4c4c9a] dark:text-gray-400 text-sm font-normal leading-normal pb-2">Unlocking the fundamental principles of the universe. Explore quantum mechanics, astrophysics, and classical physics.</p>
</details>
<details class="flex flex-col border-t border-t-[#cfcfe7] dark:border-t-gray-700 py-2 group">
<summary class="flex cursor-pointer items-center justify-between gap-6 py-2">
<p class="text-[#0d0d1b] dark:text-white text-base font-medium leading-normal">Biology</p>
<div class="text-[#0d0d1b] dark:text-white group-open:rotate-180 transition-transform">
<span class="material-symbols-outlined">expand_more</span>
</div>
</summary>
<p class="text-[#4c4c9a] dark:text-gray-400 text-sm font-normal leading-normal pb-2">Studying the intricate systems of life and living organisms, from molecular biology to ecology.</p>
</details>
</div>
</div>
</section>
<!-- Image Gallery Section -->
<section class="flex flex-col gap-6">
<div class="px-4">
<h3 class="text-2xl font-bold text-[#0d0d1b] dark:text-white">A Glimpse into Our Learning Environment</h3>
<p class="text-base text-[#4c4c9a] dark:text-gray-400">Experience the vibrant atmosphere of our campus and facilities.</p>
</div>
<div class="grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-4 p-4">
<div class="flex flex-col gap-3">
<div class="w-full bg-center bg-no-repeat aspect-video bg-cover rounded-lg" data-alt="A modern science laboratory with students using microscopes." style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAJkFyQAUGgPNGcwabV5D4UB9B1PIO8ZpcNbIJVNHiWA_YdD3MFG98w2WZ-K1mCimIox2bck7vp1274Zc9giAAmFAjvVA1McLSi6Qmdfx0trKqg8KUSlOZUjMr5vWy3eNcqWBDY4rQOZMpHLdlNDrS5PSlfwaNQLQmQEINvsZldmtaa0W5J-rOskFw9gFc8YP727VYoy0sJb-yCpdw1dYcSHZeKL4FA-JYth7Q0u1bQawvC_oTLl3L1kOigKkTZKmgfpfDfUMeaKPeU");'></div>
<div>
<p class="text-[#0d0d1b] dark:text-white text-base font-medium leading-normal">State-of-the-Art Labs</p>
<p class="text-[#4c4c9a] dark:text-gray-400 text-sm font-normal leading-normal">Hands-on learning in advanced facilities.</p>
</div>
</div>
<div class="flex flex-col gap-3">
<div class="w-full bg-center bg-no-repeat aspect-video bg-cover rounded-lg" data-alt="A large, sunlit university library with rows of bookshelves and study areas." style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAfshJDauJeSAxi6r8Fmf2mGG2ZpjKRHFoqbKzlQWUHHCxGxG-WRwcMd_eyaJepTqVfaKzC3qO71tU-0scmE4nJLHOXJytDv3mvvwDrGjPHXlg9DVLHMwanl9772EdBSy134Q01i4bplQcwipk0FYEy4IUhtq0qe03Of21qeko1uSgMp7ogt6_dsq7zVFkxDW3d3pyxNuEuI7I_ga1GDiy6kX_usoxWuh_0leSbcaDaR2S_L4f_eMmnUA1H6td2ANJIoeZaCahgAziw");'></div>
<div>
<p class="text-[#0d0d1b] dark:text-white text-base font-medium leading-normal">Collaborative Library</p>
<p class="text-[#4c4c9a] dark:text-gray-400 text-sm font-normal leading-normal">Spaces for research, study, and collaboration.</p>
</div>
</div>
<div class="flex flex-col gap-3">
<div class="w-full bg-center bg-no-repeat aspect-video bg-cover rounded-lg" data-alt="A university lecture hall with tiered seating, filled with attentive students." style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCXgM9N5671VJwfG27jZbeLYRu1WyZencRp9-PptQEnyvvbhy9CLB0juAFxRuqIjm-tzQC5s5ozzBFtUrFq77_230og1FlndsiZtRTgDPHCEMkXZeTlCV5Z4B4x0HXquTGQYZ9IDxKk2liGYiyycU-Yqz-ln_B2I4dal3a1Z3mdrsqN-uORt8o4HPvXpZfyqdXzNHMt7SICJ1dvZMH2m_WxJvWPSPJ2Of2ZJhtNcGITGpkga8_wBtJroH_Hxaf0KTzNhNIBuSFGfAvq");'></div>
<div>
<p class="text-[#0d0d1b] dark:text-white text-base font-medium leading-normal">Engaging Lecture Halls</p>
<p class="text-[#4c4c9a] dark:text-gray-400 text-sm font-normal leading-normal">Dynamic environments for inspired teaching.</p>
</div>
</div>
</div>
</section>
</main>

<?php
include 'includes/footer.php';
?>