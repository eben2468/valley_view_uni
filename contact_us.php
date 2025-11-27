<?php
$page_title = "Contact Us - Valley View University";
$active_page = "contact";
include 'includes/header.php';
?>

<main class="flex-grow px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
<div class="w-full @container mb-16">
<div class="relative flex min-h-[400px] md:min-h-[480px] flex-col gap-6 overflow-hidden rounded-xl items-center justify-center p-4 text-center">
<div class="absolute inset-0 bg-black/50 z-10"></div>
<img alt="A modern, glass-fronted university building with students walking in front, representing a welcoming campus entrance." class="absolute inset-0 h-full w-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBxjYUbIY0YPgnQ9R4FVze8ei3b3gcjlo73g8r_ljDl9eg4-XJ-iZ9hGYClsmqDy901oV9GzzRCb-QcWu7bAWBqCe6_QzXVMoylmMpWv2DFpmU5PXaBOShmeZFt1JXiIqwnHnAJYpLnsiE5VVLP0Xiur_TqSnBjnULbhNEm0P-s5j45uHAlLtkoet4n73z7_Dwf3afMjE4xTBYEJpN08ciAfaNfhOf-T6GCHf4fgi9hmJTQ44mfu-hMMzXBysTOGHuAbZjBkzn5NNkj"/>
<div class="relative z-20">
<h1 class="text-white text-4xl font-black leading-tight tracking-[-0.033em] sm:text-5xl lg:text-6xl">Connect with Valley View</h1>
<p class="text-white/90 text-base sm:text-lg font-normal leading-normal max-w-2xl mt-4">We're here to help. Reach out to us through any of the methods below, or send us a message directly.</p>
</div>
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-5 gap-12 lg:gap-16">
<div class="lg:col-span-3 bg-neutral-light dark:bg-neutral-dark/30 p-8 rounded-xl">
<h2 class="text-primary dark:text-white text-2xl sm:text-3xl font-bold leading-tight tracking-[-0.015em] mb-6">Send Us a Message</h2>
<form class="space-y-6" action="contact_process.php" method="POST">
<div>
<label class="block text-sm font-medium mb-2" for="name">Full Name</label>
<input class="form-input w-full rounded-lg border-neutral-light/50 dark:border-neutral-dark/50 bg-background-light dark:bg-neutral-dark focus:ring-primary focus:border-primary" id="name" name="name" placeholder="John Doe" type="text" required/>
</div>
<div>
<label class="block text-sm font-medium mb-2" for="email">Email Address</label>
<input class="form-input w-full rounded-lg border-neutral-light/50 dark:border-neutral-dark/50 bg-background-light dark:bg-neutral-dark focus:ring-primary focus:border-primary" id="email" name="email" placeholder="you@example.com" type="email" required/>
</div>
<div>
<label class="block text-sm font-medium mb-2" for="inquiry-type">Topic of Inquiry</label>
<select class="form-select w-full rounded-lg border-neutral-light/50 dark:border-neutral-dark/50 bg-background-light dark:bg-neutral-dark focus:ring-primary focus:border-primary" id="inquiry-type" name="inquiry-type">
<option>Admissions Question</option>
<option>General Inquiry</option>
<option>Technical Support</option>
<option>Alumni Relations</option>
</select>
</div>
<div>
<label class="block text-sm font-medium mb-2" for="message">Message</label>
<textarea class="form-textarea w-full rounded-lg border-neutral-light/50 dark:border-neutral-dark/50 bg-background-light dark:bg-neutral-dark focus:ring-primary focus:border-primary" id="message" name="message" placeholder="Please write your message here..." rows="5" required></textarea>
</div>
<button class="flex w-full sm:w-auto min-w-[150px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-opacity-90 transition-colors" type="submit">
<span class="truncate">Send Message</span>
</button>
</form>
</div>
<div class="lg:col-span-2 space-y-8">
<h2 class="text-primary dark:text-white text-2xl sm:text-3xl font-bold leading-tight tracking-[-0.015em]">Get in Touch</h2>
<div class="space-y-6">
<div class="flex items-start gap-4">
<div class="text-primary dark:text-secondary flex items-center justify-center rounded-lg bg-primary/10 dark:bg-secondary/10 shrink-0 size-12">
<span class="material-symbols-outlined">location_on</span>
</div>
<div class="flex flex-col justify-center">
<p class="text-base font-medium leading-normal line-clamp-1">Main Campus Address</p>
<p class="text-neutral-dark/70 dark:text-neutral-light/70 text-sm font-normal leading-normal line-clamp-2">123 University Drive, Valley View, CA 90210</p>
</div>
</div>
<div class="flex items-start gap-4">
<div class="text-primary dark:text-secondary flex items-center justify-center rounded-lg bg-primary/10 dark:bg-secondary/10 shrink-0 size-12">
<span class="material-symbols-outlined">call</span>
</div>
<div class="flex flex-col justify-center">
<p class="text-base font-medium leading-normal line-clamp-1">General Inquiries</p>
<a class="text-neutral-dark/70 dark:text-neutral-light/70 text-sm font-normal leading-normal line-clamp-2 hover:text-primary dark:hover:text-secondary" href="tel:+11234567890">(123) 456-7890</a>
</div>
</div>
<div class="flex items-start gap-4">
<div class="text-primary dark:text-secondary flex items-center justify-center rounded-lg bg-primary/10 dark:bg-secondary/10 shrink-0 size-12">
<span class="material-symbols-outlined">mail</span>
</div>
<div class="flex flex-col justify-center">
<p class="text-base font-medium leading-normal line-clamp-1">Email Us</p>
<a class="text-neutral-dark/70 dark:text-neutral-light/70 text-sm font-normal leading-normal line-clamp-2 hover:text-primary dark:hover:text-secondary" href="mailto:contact@valleyview.edu">contact@valleyview.edu</a>
</div>
</div>
</div>
<div class="pt-4">
<p class="text-base font-medium leading-normal mb-3">Follow Us</p>
<div class="flex items-center gap-4">
<a aria-label="Facebook" class="text-neutral-dark/70 dark:text-neutral-light/70 hover:text-primary dark:hover:text-secondary" href="#">
<svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"></path></svg>
</a>
<a aria-label="Twitter" class="text-neutral-dark/70 dark:text-neutral-light/70 hover:text-primary dark:hover:text-secondary" href="#">
<svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M22.46 6c-.77.35-1.6.58-2.46.67.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.22-1.95-.55v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.94.07 4.28 4.28 0 0 0 4 2.98 8.52 8.52 0 0 1-5.33 1.84c-.34 0-.68-.02-1.01-.06C3.44 20.29 5.7 21 8.12 21c7.34 0 11.35-6.08 11.35-11.35 0-.17 0-.34-.01-.51.78-.57 1.45-1.29 1.99-2.09z"></path></svg>
</a>
<a aria-label="LinkedIn" class="text-neutral-dark/70 dark:text-neutral-light/70 hover:text-primary dark:hover:text-secondary" href="#">
<svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M20.45 20.45h-3.51v-6.02c0-1.44-.03-3.28-2-3.28-2.01 0-2.32 1.57-2.32 3.18v6.12h-3.51V9h3.37v1.54h.05c.47-.88 1.61-1.8 3.32-1.8 3.55 0 4.21 2.34 4.21 5.37v6.34zM5.33 7.44c-1.16 0-2.1-1-2.1-2.22s.94-2.22 2.1-2.22 2.1 1 2.1 2.22c0 1.22-.94 2.22-2.1 2.22zM7.09 20.45H3.58V9h3.51v11.45zM22 1.5H2C1.17 1.5.5 2.17.5 3v18c0 .83.67 1.5 1.5 1.5h20c.83 0 1.5-.67 1.5-1.5V3c0-.83-.67-1.5-1.5-1.5z"></path></svg>
</a>
</div>
</div>
</div>
</div>
<div class="mt-20">
<h2 class="text-primary dark:text-white text-2xl sm:text-3xl font-bold leading-tight tracking-[-0.015em] mb-8 text-center">Find Us &amp; Department Contacts</h2>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">
<div>
<div class="aspect-w-16 aspect-h-9 rounded-xl overflow-hidden shadow-lg">
<img class="w-full h-full object-cover" data-alt="An illustrative map showing the location of Valley View, CA" data-location="Valley View, CA" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDHrydCqv1OX2oBSvxzx0o75RLZUEa5Xcw-FEfDjqywehMdruqCBqMRsjh707r5l89u_RX3OQIteagKrwQg4a7DDstcyr3JwPxpF9sz5n9Jv0OTzjfYU1OLptmdYGc36jTDV3q40IiQ75qum51MjAo2gb67uT-fZ6jqF9TrqM9IMiPWXxXybUX_LY2TN6FJQt4G6PEP8tobQ0M-mA9jKpBbr5Or9bufSNffbzhrqRHKhQdspl903YlSCvpSJqKdfC-WmJcfSLp29Mga"/>
</div>
</div>
<div class="space-y-4">
<div class="flex flex-col border-b border-neutral-light/60 dark:border-neutral-dark/40 py-4">
<p class="text-lg font-semibold">Admissions Office</p>
<div class="text-sm text-neutral-dark/70 dark:text-neutral-light/70 mt-1">
<a class="hover:text-primary dark:hover:text-secondary" href="tel:1234567891">(123) 456-7891</a> | <a class="hover:text-primary dark:hover:text-secondary" href="mailto:admissions@valleyview.edu">admissions@valleyview.edu</a>
</div>
</div>
<div class="flex flex-col border-b border-neutral-light/60 dark:border-neutral-dark/40 py-4">
<p class="text-lg font-semibold">Financial Aid</p>
<div class="text-sm text-neutral-dark/70 dark:text-neutral-light/70 mt-1">
<a class="hover:text-primary dark:hover:text-secondary" href="tel:1234567892">(123) 456-7892</a> | <a class="hover:text-primary dark:hover:text-secondary" href="mailto:finaid@valleyview.edu">finaid@valleyview.edu</a>
</div>
</div>
<div class="flex flex-col border-b border-neutral-light/60 dark:border-neutral-dark/40 py-4">
<p class="text-lg font-semibold">Registrar's Office</p>
<div class="text-sm text-neutral-dark/70 dark:text-neutral-light/70 mt-1">
<a class="hover:text-primary dark:hover:text-secondary" href="tel:1234567893">(123) 456-7893</a> | <a class="hover:text-primary dark:hover:text-secondary" href="mailto:registrar@valleyview.edu">registrar@valleyview.edu</a>
</div>
</div>
<div class="flex flex-col pt-4">
<p class="text-lg font-semibold">Alumni Office</p>
<div class="text-sm text-neutral-dark/70 dark:text-neutral-light/70 mt-1">
<a class="hover:text-primary dark:hover:text-secondary" href="tel:1234567894">(123) 456-7894</a> | <a class="hover:text-primary dark:hover:text-secondary" href="mailto:alumni@valleyview.edu">alumni@valleyview.edu</a>
</div>
</div>
</div>
</div>
</div>
</main>

<?php
include 'includes/footer.php';
?>