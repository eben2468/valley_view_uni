<?php
$page_title = "Donate - Valley View University";
$active_page = "about";
include 'includes/header.php';
?>

<main class="flex-grow">
<section class="relative h-[60vh] min-h-[480px] w-full flex items-center justify-center text-center text-white">
<div class="absolute inset-0 bg-cover bg-center" data-alt="Diverse group of smiling university students in graduation gowns tossing their caps in the air on a sunny day." style='background-image: linear-gradient(rgba(29, 41, 57, 0.6) 0%, rgba(29, 41, 57, 0.8) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuDyUCl2L2HtnQzwQKNc2UwtvArYnKO8Hk_mY0vCmkmBjbk-7ikpmVuvK3PC54-9nRYeWVJI2sbfb-HJJfVQTVfaYHqiO_KLm36a4YV8zpRjG8v88Kt1HRaVEmrLTGgPzdFDrLEgLhW9ZWRwIXXZIaPiQvjYmMsVROWWfVm8cl4HKWY55vwcrdERjHdOM3UB8MhTw8TMaLq1dFAu3rsEEzUDC3kn1lJrJzWvyG4d3mBVm3fGujK-mxRAJ7hoj3q8N6rLX2rxeIElO5sn");'></div>
<div class="relative z-10 flex flex-col items-center gap-6 px-4">
<h1 class="text-4xl font-black leading-tight tracking-tight md:text-6xl">Empower the Next Generation of Leaders.</h1>
<p class="max-w-2xl text-base font-normal leading-normal md:text-lg">Your contribution fuels innovation, opportunity, and excellence at Valley View, shaping the future for our students and community.</p>
<a class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-secondary text-primary text-base font-bold leading-normal tracking-[0.015em] hover:bg-opacity-90" href="#donate-form">
<span class="truncate">Donate Now</span>
</a>
</div>
</section>
<section class="py-16 sm:py-24 bg-surface-light dark:bg-surface-dark" id="donate-form">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-12">
<h2 class="text-3xl font-bold tracking-tight text-text-light dark:text-text-dark sm:text-4xl">Make Your Gift Today</h2>
<p class="mt-4 text-lg leading-8 text-text-light/80 dark:text-text-dark/80">Join us in our mission to provide exceptional education and create lasting impact. Every gift, no matter the size, makes a difference.</p>
</div>
<div class="w-full max-w-2xl mx-auto bg-background-light dark:bg-background-dark p-6 sm:p-8 rounded-xl shadow-lg">
<form action="#" class="space-y-6" method="POST">
<div>
<h3 class="text-lg font-bold text-text-light dark:text-text-dark">1. Select Amount &amp; Fund</h3>
<div class="mt-4">
<div class="flex h-12 flex-1 items-center justify-center rounded-lg bg-surface-light dark:bg-surface-dark p-1.5">
<label class="flex cursor-pointer h-full grow items-center justify-center overflow-hidden rounded-md px-2 text-sm font-bold leading-normal text-text-light/70 dark:text-text-dark/70 has-[:checked]:bg-primary has-[:checked]:text-white"><span class="truncate">$50</span><input class="sr-only" name="donation_amount" type="radio" value="50"/></label>
<label class="flex cursor-pointer h-full grow items-center justify-center overflow-hidden rounded-md px-2 text-sm font-bold leading-normal text-text-light/70 dark:text-text-dark/70 has-[:checked]:bg-primary has-[:checked]:text-white"><span class="truncate">$100</span><input checked="" class="sr-only" name="donation_amount" type="radio" value="100"/></label>
<label class="flex cursor-pointer h-full grow items-center justify-center overflow-hidden rounded-md px-2 text-sm font-bold leading-normal text-text-light/70 dark:text-text-dark/70 has-[:checked]:bg-primary has-[:checked]:text-white"><span class="truncate">$250</span><input class="sr-only" name="donation_amount" type="radio" value="250"/></label>
<label class="flex cursor-pointer h-full grow items-center justify-center overflow-hidden rounded-md px-2 text-sm font-bold leading-normal text-text-light/70 dark:text-text-dark/70 has-[:checked]:bg-primary has-[:checked]:text-white"><span class="truncate">Custom</span><input class="sr-only" name="donation_amount" type="radio" value="custom"/></label>
</div>
</div>
<div class="mt-4">
<label class="block text-sm font-medium text-text-light dark:text-text-dark" for="fund">Designate to a fund</label>
<select class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-surface-light dark:bg-surface-dark shadow-sm focus:border-primary focus:ring-primary sm:text-sm" id="fund" name="fund">
<option>General Fund</option>
<option>Scholarships</option>
<option>Campus Development</option>
<option>Athletics</option>
</select>
</div>
</div>
<hr class="border-surface-light dark:border-surface-dark"/>
<div>
<h3 class="text-lg font-bold text-text-light dark:text-text-dark">2. Personal Information</h3>
<div class="mt-4 grid grid-cols-1 gap-y-4 sm:grid-cols-2 sm:gap-x-4">
<div>
<label class="block text-sm font-medium text-text-light dark:text-text-dark" for="first-name">First name</label>
<input class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-surface-light dark:bg-surface-dark shadow-sm focus:border-primary focus:ring-primary sm:text-sm" id="first-name" name="first-name" type="text"/>
</div>
<div>
<label class="block text-sm font-medium text-text-light dark:text-text-dark" for="last-name">Last name</label>
<input class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-surface-light dark:bg-surface-dark shadow-sm focus:border-primary focus:ring-primary sm:text-sm" id="last-name" name="last-name" type="text"/>
</div>
<div class="sm:col-span-2">
<label class="block text-sm font-medium text-text-light dark:text-text-dark" for="email">Email address</label>
<input class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-surface-light dark:bg-surface-dark shadow-sm focus:border-primary focus:ring-primary sm:text-sm" id="email" name="email" type="email"/>
</div>
</div>
</div>
<hr class="border-surface-light dark:border-surface-dark"/>
<div>
<h3 class="text-lg font-bold text-text-light dark:text-text-dark">3. Payment Details</h3>
<div class="mt-4">
<label class="block text-sm font-medium text-text-light dark:text-text-dark" for="card-number">Card number</label>
<input class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-surface-light dark:bg-surface-dark shadow-sm focus:border-primary focus:ring-primary sm:text-sm" id="card-number" name="card-number" placeholder="•••• •••• •••• ••••" type="text"/>
</div>
<div class="mt-4 grid grid-cols-1 gap-y-4 sm:grid-cols-2 sm:gap-x-4">
<div>
<label class="block text-sm font-medium text-text-light dark:text-text-dark" for="expiration-date">Expiration date</label>
<input class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-surface-light dark:bg-surface-dark shadow-sm focus:border-primary focus:ring-primary sm:text-sm" id="expiration-date" name="expiration-date" placeholder="MM / YY" type="text"/>
</div>
<div>
<label class="block text-sm font-medium text-text-light dark:text-text-dark" for="cvc">CVC</label>
<input class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-surface-light dark:bg-surface-dark shadow-sm focus:border-primary focus:ring-primary sm:text-sm" id="cvc" name="cvc" placeholder="•••" type="text"/>
</div>
</div>
<div class="mt-4 flex items-center gap-2 text-sm text-gray-500">
<span class="material-symbols-outlined text-base">lock</span>
<span>Secure SSL Encrypted Payment</span>
</div>
</div>
<button class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-base font-bold text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" type="submit">
                                    Complete Donation
                                </button>
</form>
</div>
</div>
</section>
<section class="py-16 sm:py-24 bg-background-light dark:bg-background-dark">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="max-w-2xl mx-auto lg:mx-0">
<h2 class="text-3xl font-bold tracking-tight text-text-light dark:text-text-dark sm:text-4xl">The Impact of Your Gift</h2>
<p class="mt-4 text-lg text-text-light/80 dark:text-text-dark/80">See how contributions from donors like you have transformed lives and fueled innovation at Valley View University.</p>
</div>
<div class="mt-12 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
<div class="flex flex-col overflow-hidden rounded-xl shadow-lg bg-surface-light dark:bg-surface-dark">
<img class="h-48 w-full object-cover" data-alt="A female student in a science lab, smiling as she looks into a microscope." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDCLK3JDxm0n3blvbzj47xi-SsAdAEmN0m9S2rkoCTNMunEvUXYVrT3cv0FoG95LrtuYdJZPcWanOhxtsPWoqeK5kgvzLMci9QJpL3hepZltMp95t94IT3KI6eKrHyyV5EDCop-IteTp0Kqdl-mwj2TrfMhUzNL-G4ydk_-UsyiaCnYrE_CIi2KtuXzdz92kTRTmeVg-o79bGuL30uvH2w_goHUN0SNfGM6QzhF3JZKgKOByYcXb81VZfxce0F3Nia1rNNASBw3bYeT"/>
<div class="flex flex-1 flex-col justify-between p-6">
<div class="flex-1">
<h3 class="text-xl font-semibold text-text-light dark:text-text-dark">New Labs, New Discoveries</h3>
<p class="mt-3 text-base text-text-light/80 dark:text-text-dark/80">"The new state-of-the-art lab, funded by our generous donors, has allowed my research to accelerate in ways I never thought possible."</p>
</div>
<p class="mt-4 text-sm font-medium text-primary dark:text-secondary">- Dr. Alisha Chen, Faculty</p>
</div>
</div>
<div class="flex flex-col overflow-hidden rounded-xl shadow-lg bg-surface-light dark:bg-surface-dark">
<img class="h-48 w-full object-cover" data-alt="A young male student wearing a backpack smiles while standing in a university library aisle." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDJMQec1GKV3qdDAnkarDik_jQQENHCh80T1mA6QRtWmKhjADF-aq9qhqYvoBWv4OVMffQNsgHeVMbulpuY1iOSp5laJZMa58wdwJUsnz5r6u03TiBXAeGtpPDJx4SsWQ3_lZYdxom6iFpfKENFOl3QiIsQUX7BmwBHNmI4yd0kBqL7qNJ2qaGHFjR2h5aTrij8fjz_BRDvJczS8T5AyITIK4rFmnM5izi_tP21AyiM8_EKPqeaVQLSJ2cLnq9kJa8-vrUAI3RL15ZE"/>
<div class="flex flex-1 flex-col justify-between p-6">
<div class="flex-1">
<h3 class="text-xl font-semibold text-text-light dark:text-text-dark">My Scholarship Changed Everything</h3>
<p class="mt-3 text-base text-text-light/80 dark:text-text-dark/80">"Without the scholarship I received, I wouldn't have been able to afford my degree. It's truly a life-changing gift."</p>
</div>
<p class="mt-4 text-sm font-medium text-primary dark:text-secondary">- Michael R., Class of '24</p>
</div>
</div>
<div class="flex flex-col overflow-hidden rounded-xl shadow-lg bg-surface-light dark:bg-surface-dark">
<img class="h-48 w-full object-cover" data-alt="University campus building with a modern architectural design, surrounded by green lawns." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZX-NZb0nEBzi1spTtNS0BQ9WFZN7BllU7wi6qiuP277JVWC1AVmfpjmNP2W3VA8_-7W37JMTijCIDcwmDW3KRs3JQxcV8nunVkSwT4kvGwORhgjE_YyYXLRVmc67MAHvItZrCGbf_zMWOT7VL9lwHoe9qzSJdJIe2HHnSU7oKJUORkJwyY9jYePML4q43rwsG-2cLvnoWdq1CODV3P-XomlU_h9hycvlGd8tx80jnho0pXMoUWEVQjDz3SENak8Z0g8rVjcjSfmh2"/>
<div class="flex flex-1 flex-col justify-between p-6">
<div class="flex-1">
<h3 class="text-xl font-semibold text-text-light dark:text-text-dark">A Campus for the Future</h3>
<p class="mt-3 text-base text-text-light/80 dark:text-text-dark/80">"The campus development fund has created inspiring new spaces for students to collaborate, learn, and grow together."</p>
</div>
<p class="mt-4 text-sm font-medium text-primary dark:text-secondary">- Campus Development Team</p>
</div>
</div>
</div>
</div>
</section>
<section class="py-16 sm:py-24 bg-surface-light dark:bg-surface-dark">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center">
<h2 class="text-3xl font-bold tracking-tight text-text-light dark:text-text-dark sm:text-4xl">More Ways to Give</h2>
<p class="mt-4 max-w-2xl mx-auto text-lg text-text-light/80 dark:text-text-dark/80">Discover other ways you can support the mission of Valley View University and make a lasting impact.</p>
</div>
<div class="mt-16 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4 text-center">
<div class="p-6 bg-background-light dark:bg-background-dark rounded-xl">
<div class="flex items-center justify-center h-12 w-12 rounded-lg bg-primary/20 dark:bg-secondary/20 text-primary dark:text-secondary mx-auto">
<span class="material-symbols-outlined">volunteer_activism</span>
</div>
<h3 class="mt-5 text-lg font-semibold text-text-light dark:text-text-dark">Planned Giving</h3>
<p class="mt-2 text-sm text-text-light/80 dark:text-text-dark/80">Create a lasting legacy by including the university in your estate plans.</p>
</div>
<div class="p-6 bg-background-light dark:bg-background-dark rounded-xl">
<div class="flex items-center justify-center h-12 w-12 rounded-lg bg-primary/20 dark:bg-secondary/20 text-primary dark:text-secondary mx-auto">
<span class="material-symbols-outlined">apartment</span>
</div>
<h3 class="mt-5 text-lg font-semibold text-text-light dark:text-text-dark">Corporate Matching</h3>
<p class="mt-2 text-sm text-text-light/80 dark:text-text-dark/80">Double your impact through your employer's matching gift program.</p>
</div>
<div class="p-6 bg-background-light dark:bg-background-dark rounded-xl">
<div class="flex items-center justify-center h-12 w-12 rounded-lg bg-primary/20 dark:bg-secondary/20 text-primary dark:text-secondary mx-auto">
<span class="material-symbols-outlined">account_balance</span>
</div>
<h3 class="mt-5 text-lg font-semibold text-text-light dark:text-text-dark">Endowments</h3>
<p class="mt-2 text-sm text-text-light/80 dark:text-text-dark/80">Establish a permanent fund that provides support for generations to come.</p>
</div>
<div class="p-6 bg-background-light dark:bg-background-dark rounded-xl">
<div class="flex items-center justify-center h-12 w-12 rounded-lg bg-primary/20 dark:bg-secondary/20 text-primary dark:text-secondary mx-auto">
<span class="material-symbols-outlined">redeem</span>
</div>
<h3 class="mt-5 text-lg font-semibold text-text-light dark:text-text-dark">Gifts of Stock</h3>
<p class="mt-2 text-sm text-text-light/80 dark:text-text-dark/80">Make a tax-wise gift by donating appreciated securities.</p>
</div>
</div>
</div>
</section>
<section class="py-16 sm:py-24 bg-background-light dark:bg-background-dark">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
<h2 class="text-center text-3xl font-bold tracking-tight text-text-light dark:text-text-dark sm:text-4xl">Frequently Asked Questions</h2>
<div class="mt-12 space-y-4">
<details class="group rounded-lg bg-surface-light dark:bg-surface-dark p-6 [&amp;_summary::-webkit-details-marker]:hidden">
<summary class="flex cursor-pointer items-center justify-between gap-1.5 text-text-light dark:text-text-dark">
<h3 class="font-semibold">Is my online donation secure?</h3>
<span class="relative h-5 w-5 shrink-0">
<span class="material-symbols-outlined absolute transition-transform duration-300 group-open:rotate-180">expand_more</span>
</span>
</summary>
<p class="mt-4 leading-relaxed text-text-light/80 dark:text-text-dark/80">Yes, absolutely. We use industry-standard SSL (Secure Sockets Layer) encryption to protect your personal and payment information. Your data is safe and secure with us.</p>
</details>
<details class="group rounded-lg bg-surface-light dark:bg-surface-dark p-6 [&amp;_summary::-webkit-details-marker]:hidden">
<summary class="flex cursor-pointer items-center justify-between gap-1.5 text-text-light dark:text-text-dark">
<h3 class="font-semibold">Will I receive a tax receipt for my donation?</h3>
<span class="relative h-5 w-5 shrink-0">
<span class="material-symbols-outlined absolute transition-transform duration-300 group-open:rotate-180">expand_more</span>
</span>
</summary>
<p class="mt-4 leading-relaxed text-text-light/80 dark:text-text-dark/80">Yes. A confirmation email with your tax receipt will be sent to the email address you provide immediately after your donation is successfully processed.</p>
</details>
<details class="group rounded-lg bg-surface-light dark:bg-surface-dark p-6 [&amp;_summary::-webkit-details-marker]:hidden">
<summary class="flex cursor-pointer items-center justify-between gap-1.5 text-text-light dark:text-text-dark">
<h3 class="font-semibold">Can I designate my gift to a specific area?</h3>
<span class="relative h-5 w-5 shrink-0">
<span class="material-symbols-outlined absolute transition-transform duration-300 group-open:rotate-180">expand_more</span>
</span>
</summary>
<p class="mt-4 leading-relaxed text-text-light/80 dark:text-text-dark/80">Of course. Our donation form includes a dropdown menu where you can select a specific fund, such as scholarships, campus development, or athletics. If you don't make a selection, your gift will go to the General Fund where it's needed most.</p>
</details>
<details class="group rounded-lg bg-surface-light dark:bg-surface-dark p-6 [&amp;_summary::-webkit-details-marker]:hidden">
<summary class="flex cursor-pointer items-center justify-between gap-1.5 text-text-light dark:text-text-dark">
<h3 class="font-semibold">Who can I contact if I have questions?</h3>
<span class="relative h-5 w-5 shrink-0">
<span class="material-symbols-outlined absolute transition-transform duration-300 group-open:rotate-180">expand_more</span>
</span>
</summary>
<p class="mt-4 leading-relaxed text-text-light/80 dark:text-text-dark/80">Our advancement office is happy to help. You can reach us by email at giving@valleyview.edu or by phone at (555) 123-4567 during regular business hours.</p>
</details>
</div>
</div>
</section>
</main>

<?php include 'includes/footer.php'; ?>