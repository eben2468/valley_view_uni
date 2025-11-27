<?php
$pageTitle = "Mobile Money Fee Payment - Valley View University";
$activePage = "students";
?>

<?php include 'includes/header.php'; ?>

<!-- Main Content -->
<main class="flex-grow">
<div class="container mx-auto max-w-4xl px-4 py-8 md:py-16">
<div class="flex flex-col gap-8">
<!-- Breadcrumbs -->
<div class="flex flex-wrap items-center gap-2 text-sm font-medium text-text-secondary dark:text-gray-400">
<a class="hover:text-primary dark:hover:text-secondary" href="#">Home</a>
<span class="material-symbols-outlined text-base">chevron_right</span>
<a class="hover:text-primary dark:hover:text-secondary" href="#">Current Students</a>
<span class="material-symbols-outlined text-base">chevron_right</span>
<a class="hover:text-primary dark:hover:text-secondary" href="#">Fee Payment</a>
<span class="material-symbols-outlined text-base">chevron_right</span>
<span class="text-text-primary dark:text-gray-200 font-semibold">Mobile Money</span>
</div>
<!-- PageHeading -->
<div class="flex flex-col gap-2">
<h2 class="text-text-primary dark:text-white text-4xl md:text-5xl font-black tracking-tighter">Pay Your Fees with Mobile Money</h2>
<p class="text-text-secondary dark:text-gray-300 text-lg">A simple, secure, and convenient way to pay your university fees.</p>
</div>
<!-- Main Payment Container -->
<div class="bg-card-light dark:bg-card-dark rounded-xl shadow-lg border border-border-light dark:border-border-dark p-6 md:p-8">
<div class="flex flex-col gap-8">
<!-- SectionHeader and TextGrid -->
<div>
<h3 class="text-text-primary dark:text-white text-xl font-bold tracking-tight">Follow these 3 easy steps</h3>
<div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
<div class="flex flex-col gap-3 rounded-lg border border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark p-4">
<span class="material-symbols-outlined text-secondary text-3xl">phone_iphone</span>
<div class="flex flex-col">
<h4 class="text-text-primary dark:text-white font-bold">1. Select Provider</h4>
<p class="text-text-secondary dark:text-gray-400 text-sm">Choose your mobile money network.</p>
</div>
</div>
<div class="flex flex-col gap-3 rounded-lg border border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark p-4">
<span class="material-symbols-outlined text-secondary text-3xl">edit_note</span>
<div class="flex flex-col">
<h4 class="text-text-primary dark:text-white font-bold">2. Enter Details</h4>
<p class="text-text-secondary dark:text-gray-400 text-sm">Fill in student &amp; payment information.</p>
</div>
</div>
<div class="flex flex-col gap-3 rounded-lg border border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark p-4">
<span class="material-symbols-outlined text-secondary text-3xl">task_alt</span>
<div class="flex flex-col">
<h4 class="text-text-primary dark:text-white font-bold">3. Confirm &amp; Pay</h4>
<p class="text-text-secondary dark:text-gray-400 text-sm">Authorize the transaction on your phone.</p>
</div>
</div>
</div>
</div>
<!-- Payment Form Section -->
<div class="w-full space-y-6">
<!-- Mobile Network Selector -->
<div>
<label class="block text-sm font-medium text-text-primary dark:text-gray-200 mb-2">Select Mobile Network</label>
<div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
<button class="group flex flex-col items-center justify-center gap-2 rounded-lg border-2 border-primary dark:border-secondary bg-primary/10 dark:bg-secondary/10 p-4 ring-2 ring-primary dark:ring-secondary ring-offset-2 ring-offset-card-light dark:ring-offset-card-dark transition-all">
<img alt="MTN Mobile Money Logo" class="h-10 w-auto object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD-We3SwleX6w3Z3KRp3P6mDNsksmwSg5oE6w0upRs8zr2BXHt8vRjlvTxPQfKl2q53Gpb1P8jgoJDaImJhm5VX6y926neegllsoUoaWWen_omp-ZVlGlPztCdVSQwr2CfgzYXcppgUR8QqBc6ahx48zNLlgssKN6DiFIfqGM8bSqN3jTHZ5-3xRpRmWLUNRcbIm5IaMz2OVUhCZmeaHxZQRonM5uvTpLi9MdwAacxhd6q4ur8TyhWWbPD56SNRYO59FEHQ2ukIrv5f"/>
<span class="text-sm font-semibold text-text-primary dark:text-white">MTN MoMo</span>
</button>
<button class="group flex flex-col items-center justify-center gap-2 rounded-lg border border-border-light dark:border-border-dark p-4 hover:border-primary dark:hover:border-secondary transition-all">
<img alt="Vodafone Cash Logo" class="h-10 w-auto object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBQXK3bwT3UUUArEulV8thuSfB-vsRXTrjqY-vRReggWdnz_eMjbytd9fUtPWatPRCK5Cx_lKNZ9j9kWBKvsMpK6L92mBTnEtTQSTQgCf6zAibC1hr6BMB7ZLrAjCAKWpNvl-VHOiso7lvu5z1CfurGEIRn4eVlUJRghQJ5GYry7HwQF6KiEHgVXqVYt28n3UUldIJFewhulKCIRr8n1jwzgCa0D4aA0za6kJxU4Vnk6la7nrGUteN5PL7SIBhSRvVCGRfCdrUjv2jJ"/>
<span class="text-sm font-semibold text-text-primary dark:text-white">Vodafone Cash</span>
</button>
<button class="group flex flex-col items-center justify-center gap-2 rounded-lg border border-border-light dark:border-border-dark p-4 hover:border-primary dark:hover:border-secondary transition-all">
<img alt="AirtelTigo Money Logo" class="h-10 w-auto object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDMbAUyy7kjZu6N-uqWlBLRi3OD4xbw_5sHqc0lm8ewH3phB3VCFL0bQpt94vjgkpXdyBL4DWSjjmUramAmxssyIy7R6z_MaZm87SDM_IJCFYOmmPYGF50Z1T-MVci8nRqGldZq2R8Fsk8IgshTljVse3JykeJQqYG3w6SsoAkjGN8LIRQ2d_hmxlYZO0Cu1fywIyAB9-p6YWCBmzR9ewCakD0L4decE_1x44MwA72ezRg9IRbbm8Wl3p6HgaiTIfn26GkhwZd17oeG"/>
<span class="text-sm font-semibold text-text-primary dark:text-white">AirtelTigo Money</span>
</button>
</div>
</div>
<!-- Input Form -->
<form class="space-y-4">
<div>
<label class="block text-sm font-medium text-text-primary dark:text-gray-200" for="student-id">Student ID Number</label>
<div class="mt-1 relative">
<span class="material-symbols-outlined pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-text-secondary dark:text-gray-400">badge</span>
<input class="block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark py-2 pl-10 pr-3 focus:border-primary focus:ring-primary dark:focus:border-secondary dark:focus:ring-secondary" id="student-id" name="student-id" placeholder="e.g., 221001234" type="text"/>
</div>
</div>
<div>
<label class="block text-sm font-medium text-text-primary dark:text-gray-200" for="amount">Amount to Pay</label>
<div class="mt-1 relative">
<div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
<span class="text-text-secondary dark:text-gray-400 sm:text-sm">GHS</span>
</div>
<input class="block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark py-2 pl-12 pr-3 focus:border-primary focus:ring-primary dark:focus:border-secondary dark:focus:ring-secondary" id="amount" name="amount" placeholder="0.00" type="number"/>
</div>
</div>
<div>
<label class="block text-sm font-medium text-text-primary dark:text-gray-200" for="momo-number">Mobile Money Phone Number</label>
<div class="mt-1 relative">
<span class="material-symbols-outlined pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-text-secondary dark:text-gray-400">phone</span>
<input class="block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark py-2 pl-10 pr-3 focus:border-primary focus:ring-primary dark:focus:border-secondary dark:focus:ring-secondary" id="momo-number" name="momo-number" placeholder="e.g., 024xxxxxxx" type="tel"/>
</div>
</div>
<div class="pt-4">
<button class="w-full flex items-center justify-center gap-2 rounded-lg bg-primary px-6 py-3 text-base font-bold text-white shadow-lg transition-transform hover:scale-105" type="submit">
<span class="material-symbols-outlined">lock</span>
                                            Proceed to Payment
                                        </button>
<div class="mt-3 flex items-center justify-center gap-2 text-xs text-text-secondary dark:text-gray-400">
<span class="material-symbols-outlined text-base text-success">verified_user</span>
<span>SSL Secured Payment</span>
</div>
</div>
</form>
</div>
<!-- FAQ Section -->
<div class="border-t border-border-light dark:border-border-dark pt-8">
<h3 class="text-text-primary dark:text-white text-xl font-bold tracking-tight">Frequently Asked Questions</h3>
<div class="mt-4 space-y-3">
<details class="group rounded-lg bg-background-light dark:bg-background-dark p-4 border border-border-light dark:border-border-dark cursor-pointer">
<summary class="flex items-center justify-between font-medium text-text-primary dark:text-white">
                                            What is the payment reference?
                                            <span class="material-symbols-outlined transition-transform group-open:rotate-180">expand_more</span>
</summary>
<p class="mt-2 text-sm text-text-secondary dark:text-gray-400">Your payment reference is your Student ID number. Please ensure it is entered correctly to avoid any delays in payment allocation.</p>
</details>
<details class="group rounded-lg bg-background-light dark:bg-background-dark p-4 border border-border-light dark:border-border-dark cursor-pointer">
<summary class="flex items-center justify-between font-medium text-text-primary dark:text-white">
                                            How long does it take for the payment to reflect?
                                            <span class="material-symbols-outlined transition-transform group-open:rotate-180">expand_more</span>
</summary>
<p class="mt-2 text-sm text-text-secondary dark:text-gray-400">Payments typically reflect on your student portal within 5-10 minutes. If your payment doesn't reflect after one hour, please contact the finance office.</p>
</details>
<details class="group rounded-lg bg-background-light dark:bg-background-dark p-4 border border-border-light dark:border-border-dark cursor-pointer">
<summary class="flex items-center justify-between font-medium text-text-primary dark:text-white">
                                            Who do I contact if I have an issue?
                                            <span class="material-symbols-outlined transition-transform group-open:rotate-180">expand_more</span>
</summary>
<p class="mt-2 text-sm text-text-secondary dark:text-gray-400">For any payment-related issues, please contact the University Finance Office at +233 30 299 9999 or email finance@vvu.edu.gh with your Student ID and transaction details.</p>
</details>
</div>
</div>
</div>
</div>
</div>
</div>
</main>

<?php include 'includes/footer.php'; ?>