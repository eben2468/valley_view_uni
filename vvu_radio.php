<?php
$pageTitle = "VVU Radio - Valley View University";
$activePage = "ventures";
include 'includes/header.php';
?>

<main class="layout-container flex h-full grow flex-col items-center">
<div class="layout-content-container flex flex-col w-full max-w-5xl flex-1 px-4 py-8 md:py-12 gap-12 md:gap-16">
<!-- Hero Section -->
<section class="@container">
<div class="flex flex-col md:flex-row gap-6 md:gap-8 rounded-xl bg-white dark:bg-surface-dark overflow-hidden shadow-lg shadow-black/5">
<div class="md:w-1/2">
<div class="bg-center bg-no-repeat aspect-video md:aspect-auto h-full w-full bg-cover" data-alt="An artistic shot of radio broadcasting equipment with warm lighting." style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCuV1fVEA4U_1sYAz5mO3v9Rnn9d50aoD8q6hMKpSlNbVm-p2WXdPJ3Ya_oE96icuPxI8JhWZQc6KSAZEtJLCRhtECkpm8QyBFyVyjmI8rPPhSC22CSy5OphAU3ldjrZeAjjDU8MaPgP0A6UcAlqPVITkD31Pk-EmPG4-UST4DobBYXIEbTZguarhlcUCknDmD0P0qs8yvve69kk5HYCjhnCRGdxrXo_-2qGozbkKs1iYPH5GsnDPVUj1c0W1nHK2bzm0kW466BNNtQ");'></div>
</div>
<div class="flex flex-col items-start justify-center p-6 md:p-8 md:w-1/2">
<span class="on-air text-sm font-bold uppercase tracking-widest text-primary-light mb-4">On Air</span>
<h1 class="text-4xl md:text-5xl font-black leading-tight tracking-tighter mb-2">VVU Radio</h1>
<p class="text-base text-text-muted-light dark:text-text-muted-dark mb-6">Now Playing: Morning Inspiration with Dr. Jane Doe</p>
<div class="w-full flex items-center justify-between gap-4 p-4 rounded-lg bg-gray-500/10">
<button class="flex shrink-0 items-center justify-center rounded-full size-12 bg-primary text-white hover:bg-opacity-90">
<span class="material-symbols-outlined text-3xl">play_arrow</span>
</button>
<div class="w-full h-1.5 rounded-full bg-gray-500/20">
<div class="w-1/2 h-full rounded-full bg-primary-light"></div>
</div>
<span class="material-symbols-outlined text-2xl cursor-pointer">volume_up</span>
</div>
</div>
</div>
</section>
<!-- Today's Schedule -->
<section>
<div class="flex items-center justify-between mb-6">
<h2 class="text-3xl font-bold leading-tight tracking-[-0.015em]">On The Air Today</h2>
<button class="hidden sm:flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-gray-500/10 hover:bg-gray-500/20 text-sm font-bold leading-normal tracking-[0.015em]">
<span class="truncate">View Full Weekly Schedule</span>
</button>
</div>
<div class="grid grid-cols-[auto_1fr] gap-x-4">
<div class="flex flex-col items-center gap-1 pt-3">
<span class="material-symbols-outlined text-2xl text-primary-light">wb_sunny</span>
<div class="w-[2px] bg-primary/20 h-full"></div>
</div>
<div class="flex flex-1 flex-col py-3 border-b border-primary/10">
<p class="text-base font-medium leading-normal">Morning Inspiration</p>
<p class="text-text-muted-light dark:text-text-muted-dark text-sm leading-normal">8:00 AM - 10:00 AM</p>
</div>
<div class="flex flex-col items-center gap-1">
<div class="w-[2px] bg-primary/20 h-2"></div>
<span class="material-symbols-outlined text-2xl text-primary-light">newspaper</span>
<div class="w-[2px] bg-primary/20 h-full"></div>
</div>
<div class="flex flex-1 flex-col py-3 border-b border-primary/10">
<p class="text-base font-medium leading-normal">Campus Current Affairs</p>
<p class="text-text-muted-light dark:text-text-muted-dark text-sm leading-normal">10:00 AM - 12:00 PM</p>
</div>
<div class="flex flex-col items-center gap-1">
<div class="w-[2px] bg-primary/20 h-2"></div>
<span class="material-symbols-outlined text-2xl text-primary-light">music_note</span>
<div class="w-[2px] bg-primary/20 h-full"></div>
</div>
<div class="flex flex-1 flex-col py-3 border-b border-primary/10">
<p class="text-base font-medium leading-normal">The Midday Mix</p>
<p class="text-text-muted-light dark:text-text-muted-dark text-sm leading-normal">12:00 PM - 3:00 PM</p>
</div>
<div class="flex flex-col items-center gap-1 pb-3">
<div class="w-[2px] bg-primary/20 h-2"></div>
<span class="material-symbols-outlined text-2xl text-primary-light">piano</span>
</div>
<div class="flex flex-1 flex-col py-3">
<p class="text-base font-medium leading-normal">Evening Jazz Session</p>
<p class="text-text-muted-light dark:text-text-muted-dark text-sm leading-normal">6:00 PM - 8:00 PM</p>
</div>
</div>
<button class="flex sm:hidden w-full mt-6 min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-12 px-4 bg-gray-500/10 hover:bg-gray-500/20 text-sm font-bold leading-normal tracking-[0.015em]">
<span class="truncate">View Full Weekly Schedule</span>
</button>
</section>
<!-- Featured Podcasts & Archives -->
<section>
<div class="flex items-center justify-between mb-6">
<h2 class="text-3xl font-bold leading-tight tracking-[-0.015em]">Catch Up On Demand</h2>
<button class="hidden sm:flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-gray-500/10 hover:bg-gray-500/20 text-sm font-bold leading-normal tracking-[0.015em]">
<span class="truncate">Explore Archives</span>
</button>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
<div class="flex flex-col gap-4 group">
<div class="aspect-square bg-cover bg-center rounded-lg overflow-hidden">
<div class="bg-center bg-no-repeat aspect-square bg-cover rounded-lg group-hover:scale-105 transition-transform duration-300" data-alt="Abstract soundwaves in blue and yellow." style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuA9tz_g5jmanaNHK4SyVc7dMs7nsMgvzWMRCQizRsmzRKR5cifInVJM36gBN0Vp5qadcfxafdb5Dc02-jSuF7dB4-mVbJ2fN9hLYk7CydYHsC3d7qFLY2w-YTR4oVxi2bEs1pq8q4iHZ8dv-2eARdgLpPlT1-hveM6BYXFbmxIIknS3wavbXBWc4FPxJv0zIx1MyYQsHCAFbHT_RKWnRdtMdwi0z64TYhDq6C02ua7k3OMSbdF8yDvAng6-Q8Z6F0Hn2s5oY1iZhkkC");'></div>
</div>
<div class="flex flex-col">
<h3 class="font-bold text-lg">Campus Life Highlights</h3>
<p class="text-text-muted-light dark:text-text-muted-dark text-sm">A weekly recap of events and stories from around Valley View.</p>
<a class="flex items-center gap-2 mt-2 text-primary-light font-bold text-sm" href="#">Listen Now <span class="material-symbols-outlined text-base">arrow_forward</span></a>
</div>
</div>
<div class="flex flex-col gap-4 group">
<div class="aspect-square bg-cover bg-center rounded-lg overflow-hidden">
<div class="bg-center bg-no-repeat aspect-square bg-cover rounded-lg group-hover:scale-105 transition-transform duration-300" data-alt="Abstract soundwaves in gold and purple." style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAmaIJjf4AW8kjyPBUzIco7Lyd06q8E9RfQYdAz7kx8HIdA401bH7irAegOz--ilJO9e-bWYNJsmvn40Mo7DUtLXwB4QCiSQ6kBCeJdgFPfw0PXWx1wrFt0Z_wAy8oKIyl4H8o1y_A9wLe8a-hdXGwkGSWofmyEXx-KVBnxjIbTLeq5JsQFTjWBr0amEChcl0HLvJXVyeP-L8j-aIS0OD9i9zP7dmX93vg3uucnX7yfy84izwrlhGxSXRCQQcczeJhXH4LadwGkPveL");'></div>
</div>
<div class="flex flex-col">
<h3 class="font-bold text-lg">Guest Lecture Series</h3>
<p class="text-text-muted-light dark:text-text-muted-dark text-sm">Full recordings of the insightful lectures from visiting experts.</p>
<a class="flex items-center gap-2 mt-2 text-primary-light font-bold text-sm" href="#">Listen Now <span class="material-symbols-outlined text-base">arrow_forward</span></a>
</div>
</div>
<div class="flex flex-col gap-4 group">
<div class="aspect-square bg-cover bg-center rounded-lg overflow-hidden">
<div class="bg-center bg-no-repeat aspect-square bg-cover rounded-lg group-hover:scale-105 transition-transform duration-300" data-alt="Abstract soundwaves in yellow and pink." style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAxF1a-swdI5RKaSXSrFTWqB01nvF3P7AZxeIjc1OwtPImO6AZAJC7WMHZSsbo7Xtpr_qSr88qaLWkQPeS5njzoHGmZTZeNHCVCiBPksNbflLXVBzGpm-msMjqWyUyHEGWiprWymMN1NnJmD53zdiH16hZXCB26XPiBG3yCV2nQtyUn3Xm92lKLbcniZbGRFkDylwNtAqC8ucSX6CTzMWfAds_PvDEurvc3IiRRjf4UA0P1_8VU69G18l-095jrdYe_VBw7Z0u8iKCh");'></div>
</div>
<div class="flex flex-col">
<h3 class="font-bold text-lg">Alumni Spotlights</h3>
<p class="text-text-muted-light dark:text-text-muted-dark text-sm">Inspiring stories and career journeys from VVU graduates.</p>
<a class="flex items-center gap-2 mt-2 text-primary-light font-bold text-sm" href="#">Listen Now <span class="material-symbols-outlined text-base">arrow_forward</span></a>
</div>
</div>
</div>
<button class="flex sm:hidden w-full mt-6 min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-12 px-4 bg-gray-500/10 hover:bg-gray-500/20 text-sm font-bold leading-normal tracking-[0.015em]">
<span class="truncate">Explore Archives</span>
</button>
</section>
<!-- About Section -->
<section class="rounded-xl bg-white dark:bg-surface-dark overflow-hidden shadow-lg shadow-black/5">
<div class="grid grid-cols-1 md:grid-cols-2">
<div class="p-8 md:p-12 flex flex-col justify-center order-2 md:order-1">
<h2 class="text-3xl font-bold leading-tight tracking-[-0.015em] mb-4">The Voice of Valley View</h2>
<p class="text-text-muted-light dark:text-text-muted-dark mb-6">VVU Radio is the student-run heart of our campus, broadcasting live 24/7. Our mission is to inform, entertain, and connect the Valley View community through diverse programming, from music and news to talk shows and special events. Get involved, tune in, and be part of the conversation!</p>
<button class="flex w-fit min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-12 px-5 bg-primary-light text-primary text-base font-bold leading-normal tracking-[0.015em] hover:opacity-90">
<span class="truncate">Get Involved</span>
</button>
</div>
<div class="min-h-[250px] bg-cover bg-center order-1 md:order-2" data-alt="A photo of students working together in a modern radio studio." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuB_T6q-soKTxi9Y49c4lfMVrVUdPo6SCKQaLUGdE8bh9fHzAOi20qKFbmwV-o-0RSxoj44wzbCGt-z_fMcVAVmDNnWnN6PQVbjEGE_5rh_pOFY3oQ7wWtgOo65jjmte_squhnVInXl3sT_RbuO9UB5HB_T22Lhwqok-luSbsdVgK2WUR7d82wLWXRhS88dDQALZ00dWhn8hL8PIuuiWaJQeKe-3D_rgwsw68jTtbAb27tZMYhheArslub-NSMDGonzdcIfSdSxz0GQg')"></div>
</div>
</section>
</div>
</main>

<?php
include 'includes/footer.php';
?>