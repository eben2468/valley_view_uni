<?php
$pageTitle = "Faculty Encyclopedia - Valley View University";
$activePage = "academics";
?>

<?php include 'includes/header.php'; ?>

<main class="flex-grow">
<!-- HeroSection -->
<div class="relative @container">
<div class="flex min-h-[400px] flex-col gap-6 bg-cover bg-center bg-no-repeat items-center justify-center p-4 text-center" data-alt="Scenic view of the Valley View University campus library and clock tower" style='background-image: linear-gradient(rgba(0, 32, 91, 0.6) 0%, rgba(0, 32, 91, 0.8) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuCCng37sl3t7JawDCPwiDKr2qiXFkLIBlry7O3r9cd8gMZ_BFt1AHdubAUd2TkTl90cHHqSip4-y2SmSy9yZk11f1276GWALfc7m0KB9CCh6Odeubpi8jtMZVBWjFskyt3nYdTdZXC0MK-oYD4Ls5H8v9ZvhONo30zopmN2d9bPpkgedtjJq0tpVIGDKdvN4QDgLcXhpiQC7gioJK1JavyNW0uRYL6p73YqjSHUQr3xxWOzpIoht76OHRB7t5PGZdN2JExNlV40-wd0");'>
<h1 class="text-white text-4xl font-black leading-tight tracking-[-0.033em] @[480px]:text-5xl">Meet Our Faculty</h1>
<h2 class="text-white/90 text-sm font-normal leading-normal @[480px]:text-base max-w-2xl">Discover the brilliant minds and dedicated mentors shaping the future at Valley View University.</h2>
</div>
</div>
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
<!-- Chips / Filter Section -->
<div class="mb-8 p-4 sm:p-6 bg-card-light dark:bg-card-dark rounded-xl border border-border-light dark:border-border-dark shadow-sm">
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
<div class="col-span-1 sm:col-span-2 lg:col-span-2">
<label class="block text-sm font-medium mb-1" for="search">Search Faculty</label>
<div class="relative">
<div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
<span class="material-symbols-outlined text-gray-500">search</span>
</div>
<input class="w-full h-12 rounded-lg border-border-light dark:border-border-dark bg-subtle-light dark:bg-subtle-dark pl-10 pr-4 focus:ring-primary focus:border-primary" id="search" placeholder="Search by name, expertise..." type="text"/>
</div>
</div>
<div>
<label class="block text-sm font-medium mb-1" for="department">Department</label>
<select class="w-full h-12 rounded-lg border-border-light dark:border-border-dark bg-subtle-light dark:bg-subtle-dark focus:ring-primary focus:border-primary" id="department">
<option>All Departments</option>
<option>Computer Science</option>
<option>History</option>
<option>Biology</option>
</select>
</div>
<div>
<label class="block text-sm font-medium mb-1" for="school">School/College</label>
<select class="w-full h-12 rounded-lg border-border-light dark:border-border-dark bg-subtle-light dark:bg-subtle-dark focus:ring-primary focus:border-primary" id="school">
<option>All Schools</option>
<option>School of Engineering</option>
<option>College of Arts &amp; Sciences</option>
<option>School of Business</option>
</select>
</div>
</div>
</div>
<!-- ImageGrid -->
<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
<div class="flex flex-col gap-3 rounded-xl bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark p-4 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
<div class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-lg" data-alt="Professional headshot of Dr. Jane Doe" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuD2p23bvkA3HZe1Layjq7y0A3l2ZBFBPzKsFIy5QgClacfakibInvP_lkppTw1QoA5z5bqPLF-TN4htksQc8xGbcVcmBSx1f9JpXDOZAiJ_mw-qbIkDA_Rk7FRu62jxxfOnb1TjLb9yHGnXN2nYZrF3GdCGRT-EhHzitRive3ABEAWIN8djDTuztAeSb2JbousIUotxSIt3AS86O43UDScx7It99G80iNoo7Fuiu_ujdO6dOaHZZG0-uF_Defts7mZiFR_b9mpGAAXv");'></div>
<div>
<p class="font-bold text-lg">Dr. Jane Doe</p>
<p class="text-sm text-text-light/70 dark:text-text-dark/70">Professor, Computer Science</p>
<div class="flex flex-wrap gap-2 mt-2">
<span class="text-xs font-medium bg-primary/10 text-primary dark:bg-accent/20 dark:text-accent px-2 py-1 rounded-full">AI</span>
<span class="text-xs font-medium bg-primary/10 text-primary dark:bg-accent/20 dark:text-accent px-2 py-1 rounded-full">Ethics</span>
</div>
</div>
<a class="mt-auto w-full text-center rounded-lg bg-subtle-light dark:bg-subtle-dark h-10 flex items-center justify-center font-bold text-sm text-primary dark:text-accent hover:bg-primary/10 dark:hover:bg-accent/20 transition-colors" href="#">View Full Profile</a>
</div>
<div class="flex flex-col gap-3 rounded-xl bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark p-4 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
<div class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-lg" data-alt="Professional headshot of Dr. John Smith" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuB4T4PpdghGvXFFEXkFsVZhQ64XhXE96gTeveAztTO-Iuvfa_rgjuLpEVtgrgOUMrgPnEsWeb4uPQDQE7Gx9qzL284ZScOfDCbTXxitU99-DFRtxtHeysrPoJ3BVwapPIhNNOBH7gfpyWrswRxhmWrLHo9BHjCSBQ6yeklWxZ8pqsPVlHZc7RquierYjM-Nzwd3lrGcZsXmMkuyGsx1HdmSEjNeVYoKJ_5R5hXFk0zuGEB17BGoJxm8Xo9vQN5-oHwJtBxxDIfAHF67");'></div>
<div>
<p class="font-bold text-lg">Dr. John Smith</p>
<p class="text-sm text-text-light/70 dark:text-text-dark/70">Associate Professor, History</p>
<div class="flex flex-wrap gap-2 mt-2">
<span class="text-xs font-medium bg-primary/10 text-primary dark:bg-accent/20 dark:text-accent px-2 py-1 rounded-full">Medieval Europe</span>
</div>
</div>
<a class="mt-auto w-full text-center rounded-lg bg-subtle-light dark:bg-subtle-dark h-10 flex items-center justify-center font-bold text-sm text-primary dark:text-accent hover:bg-primary/10 dark:hover:bg-accent/20 transition-colors" href="#">View Full Profile</a>
</div>
<div class="flex flex-col gap-3 rounded-xl bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark p-4 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
<div class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-lg" data-alt="Professional headshot of Dr. Emily White" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDap7vy_X3_yiJfYCzEFnFl1GZTQMnMmP34tBHQr8waZj3NeYeMRar4cjb4wFf5Mp2rtmUqcrAtF7BB_nC-y0o5i0GAq8cEVjbKfONiQJDZSICPfLVxiicoD1m7RlRhe9UNSVPi4N6Hv937LJV81FPvaF-GpmIvgGFUiCZ4CX1mp1tcgyKFTLpXzCgK1SU79LIhzKv6-x12oFfL9dK4vVdxs15tKZSaxZtWjo4IY64-6b_7iZZICPzcLAqweo-Qm4X9ZaJWO1cEYtku");'></div>
<div>
<p class="font-bold text-lg">Dr. Emily White</p>
<p class="text-sm text-text-light/70 dark:text-text-dark/70">Assistant Professor, Biology</p>
<div class="flex flex-wrap gap-2 mt-2">
<span class="text-xs font-medium bg-primary/10 text-primary dark:bg-accent/20 dark:text-accent px-2 py-1 rounded-full">Genetics</span>
<span class="text-xs font-medium bg-primary/10 text-primary dark:bg-accent/20 dark:text-accent px-2 py-1 rounded-full">Molecular Biology</span>
</div>
</div>
<a class="mt-auto w-full text-center rounded-lg bg-subtle-light dark:bg-subtle-dark h-10 flex items-center justify-center font-bold text-sm text-primary dark:text-accent hover:bg-primary/10 dark:hover:bg-accent/20 transition-colors" href="#">View Full Profile</a>
</div>
<div class="flex flex-col gap-3 rounded-xl bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark p-4 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
<div class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-lg" data-alt="Professional headshot of Dr. Michael Brown" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBdbfV6raxwfTOgSRY5MgBY3Msu7tRgI3oA2cdxoFimfOU-1gQ_7x__SarN5b-PrbFV8OAvXh-Dy-C96rfmFlMMTHH-RA4u6zWAGlulQatuKF9q__fvIByOmy9LQm8rXpwIi99Mt7mNCBOlHm2Dy7HZwCQ7I1Qy9HiqTDahFkTiyT9JgiN_PHlRQe2pdaMuplj4eQYBow9Z2GwSRZFg7wU3JmTcnpkqB2p0_5GqoTpzRGjGCdlzjyCIe3prMdoZ34t6TDCwPwRezovK");'></div>
<div>
<p class="font-bold text-lg">Dr. Michael Brown</p>
<p class="text-sm text-text-light/70 dark:text-text-dark/70">Professor, Physics</p>
<div class="flex flex-wrap gap-2 mt-2">
<span class="text-xs font-medium bg-primary/10 text-primary dark:bg-accent/20 dark:text-accent px-2 py-1 rounded-full">Astrophysics</span>
</div>
</div>
<a class="mt-auto w-full text-center rounded-lg bg-subtle-light dark:bg-subtle-dark h-10 flex items-center justify-center font-bold text-sm text-primary dark:text-accent hover:bg-primary/10 dark:hover:bg-accent/20 transition-colors" href="#">View Full Profile</a>
</div>
<div class="flex flex-col gap-3 rounded-xl bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark p-4 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
<div class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-lg" data-alt="Professional headshot of Dr. Sarah Green" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAy0lSbC7Ogtrg-SL_RGvKjRWbAW2WHyu3uqg3Y_yQ_v3-zMUtlhLpI0DoGUMMpxpm6OxBeNmLLQfuDvyz0sO25wEde6Wn8apdWsMKINJZyb4ayRWOPW7__WgMEPaGihaKNJknPdkPg-4W-JPxHumLpmSK40eEth0uwW7yF1WWsh4DbX3g91ww35J1JLsC1Rc7pjbD8XO0jjKWYxiG1jrQevllXIza_9vjoCaQKlzmf5kHklQWbIIdf2UIMo0yMx_VyysJxbPleDdxP");'></div>
<div>
<p class="font-bold text-lg">Dr. Sarah Green</p>
<p class="text-sm text-text-light/70 dark:text-text-dark/70">Lecturer, English Literature</p>
<div class="flex flex-wrap gap-2 mt-2">
<span class="text-xs font-medium bg-primary/10 text-primary dark:bg-accent/20 dark:text-accent px-2 py-1 rounded-full">Shakespeare</span>
</div>
</div>
<a class="mt-auto w-full text-center rounded-lg bg-subtle-light dark:bg-subtle-dark h-10 flex items-center justify-center font-bold text-sm text-primary dark:text-accent hover:bg-primary/10 dark:hover:bg-accent/20 transition-colors" href="#">View Full Profile</a>
</div>
<div class="flex flex-col gap-3 rounded-xl bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark p-4 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
<div class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-lg" data-alt="Professional headshot of Dr. David Black" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCNHRVssX2gO7CJMnn1Ykb4ARyVxaZyB1OL_rZ2Cd3EB8e9GpCnzAwkGctc9YCfbYcS570cT2ehezMf81W7LD93l-RmgdzkvaAno4WdHuNY-i9zpgrfkNPUcCv3Fe9XrTP2NUUy02x40niWDRTZODtzhKDSfYl-BS_r6xxztZByJ8uHSef-cbnoZrBTX8aYPGEs4ZDtarGETdJyOpr42QLfPRbsP94IwxgFbbtm9Lzq1Zye1IpgZQCXvTkpfMsptOAV5hz-deKAHUF7");'></div>
<div>
<p class="font-bold text-lg">Dr. David Black</p>
<p class="text-sm text-text-light/70 dark:text-text-dark/70">Professor, Chemical Engineering</p>
<div class="flex flex-wrap gap-2 mt-2">
<span class="text-xs font-medium bg-primary/10 text-primary dark:bg-accent/20 dark:text-accent px-2 py-1 rounded-full">Nanotechnology</span>
</div>
</div>
<a class="mt-auto w-full text-center rounded-lg bg-subtle-light dark:bg-subtle-dark h-10 flex items-center justify-center font-bold text-sm text-primary dark:text-accent hover:bg-primary/10 dark:hover:bg-accent/20 transition-colors" href="#">View Full Profile</a>
</div>
<div class="flex flex-col gap-3 rounded-xl bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark p-4 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
<div class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-lg" data-alt="Professional headshot of Dr. Laura Blue" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDgFec7r7lGiR09jFOFgKAfig0OK0KgwaBkir6Bw1c8z4yAjJNpPwLZkDiGIMJGQ6oPqNIpj3OpaxELJDPCUtsADlpwaL6Gx-B4JpYbpvXiaRTeIQFThcqhfJwcBHkx-3i8zf3tExFNvQICXqyy2VSefPUSt-gJRYjTWhEkHCgpH4xOhUhVyUVLLa-5FlUCb_EAbp_-nV82_H11keSgChoKR9lACjAg7ypkTtAXsm5rB3meFCrZZsojJwsezAbOImH8QnPIr2eEtvpE");'></div>
<div>
<p class="font-bold text-lg">Dr. Laura Blue</p>
<p class="text-sm text-text-light/70 dark:text-text-dark/70">Associate Professor, Psychology</p>
<div class="flex flex-wrap gap-2 mt-2">
<span class="text-xs font-medium bg-primary/10 text-primary dark:bg-accent/20 dark:text-accent px-2 py-1 rounded-full">Cognitive Neuroscience</span>
</div>
</div>
<a class="mt-auto w-full text-center rounded-lg bg-subtle-light dark:bg-subtle-dark h-10 flex items-center justify-center font-bold text-sm text-primary dark:text-accent hover:bg-primary/10 dark:hover:bg-accent/20 transition-colors" href="#">View Full Profile</a>
</div>
<div class="flex flex-col gap-3 rounded-xl bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark p-4 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
<div class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-lg" data-alt="Professional headshot of Dr. Chris Yellow" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBZjIaSBeB1FnnDzBwEdbQECZ1TEi1g6cCdqQERhyecRIWypPs_tbizOT1tG82S_5-44qElNTKmWEOieD6MvdcwVRL79OJsgh7ybJCxmi-m_8c1TzfW37TyK0wfqkSxnqk4AIBLNCr8SZrEBjOnAufjywmPLCCCJKxtOX-ZFLEGG-tn-JqQpf8SVLI2VDb__I8wGubbkiHW8uvay-JjsfI_Dek4VrkL7yQag7uYG9jHAtpSwNu7ClG_IbZLYj7DdjIbui6MudVepaJI");'></div>
<div>
<p class="font-bold text-lg">Dr. Chris Yellow</p>
<p class="text-sm text-text-light/70 dark:text-text-dark/70">Assistant Professor, Art History</p>
<div class="flex flex-wrap gap-2 mt-2">
<span class="text-xs font-medium bg-primary/10 text-primary dark:bg-accent/20 dark:text-accent px-2 py-1 rounded-full">Museum Studies</span>
</div>
</div>
<a class="mt-auto w-full text-center rounded-lg bg-subtle-light dark:bg-subtle-dark h-10 flex items-center justify-center font-bold text-sm text-primary dark:text-accent hover:bg-primary/10 dark:hover:bg-accent/20 transition-colors" href="#">View Full Profile</a>
</div>
</div>
<!-- Pagination -->
<div class="flex items-center justify-center p-4 mt-8">
<a class="flex size-10 items-center justify-center rounded-lg hover:bg-subtle-light dark:hover:bg-subtle-dark transition-colors" href="#">
<span class="material-symbols-outlined">chevron_left</span>
</a>
<a class="text-sm font-normal leading-normal flex size-10 items-center justify-center rounded-lg hover:bg-subtle-light dark:hover:bg-subtle-dark transition-colors" href="#">Prev</a>
<a class="text-sm font-bold leading-normal flex size-10 items-center justify-center text-white rounded-lg bg-primary" href="#">1</a>
<a class="text-sm font-normal leading-normal flex size-10 items-center justify-center rounded-lg hover:bg-subtle-light dark:hover:bg-subtle-dark transition-colors" href="#">2</a>
<a class="text-sm font-normal leading-normal flex size-10 items-center justify-center rounded-lg hover:bg-subtle-light dark:hover:bg-subtle-dark transition-colors" href="#">3</a>
<span class="text-sm font-normal leading-normal flex size-10 items-center justify-center rounded-lg">...</span>
<a class="text-sm font-normal leading-normal flex size-10 items-center justify-center rounded-lg hover:bg-subtle-light dark:hover:bg-subtle-dark transition-colors" href="#">12</a>
<a class="text-sm font-normal leading-normal flex size-10 items-center justify-center rounded-lg hover:bg-subtle-light dark:hover:bg-subtle-dark transition-colors" href="#">Next</a>
<a class="flex size-10 items-center justify-center rounded-lg hover:bg-subtle-light dark:hover:bg-subtle-dark transition-colors" href="#">
<span class="material-symbols-outlined">chevron_right</span>
</a>
</div>
</div>
</main>

<?php include 'includes/footer.php'; ?>