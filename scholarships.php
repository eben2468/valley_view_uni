<?php
$pageTitle = "Scholarships - Valley View University";
$activePage = "scholarships";
include 'includes/header.php';
?>

<div class="relative flex min-h-screen w-full flex-col">
  <!-- TopNavBar -->
  <header class="sticky top-0 z-50 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm">
    <div class="flex items-center justify-between whitespace-nowrap border-b border-solid border-gray-200 dark:border-gray-800 px-4 sm:px-6 lg:px-10 py-3 max-w-7xl mx-auto">
      <div class="flex items-center gap-4">
        <div class="size-6 text-primary">
          <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
            <path clip-rule="evenodd" d="M24 4H42V17.3333V30.6667H24V44H6V30.6667V17.3333H24V4Z" fill="currentColor" fill-rule="evenodd"></path>
          </svg>
        </div>
        <h2 class="text-lg font-bold leading-tight tracking-[-0.015em] dark:text-white">Valley View University</h2>
      </div>
      <div class="hidden md:flex flex-1 justify-end gap-8">
        <nav class="flex items-center gap-9">
          <a class="text-sm font-medium leading-normal hover:text-primary dark:hover:text-primary" href="#">Admissions</a>
          <a class="text-sm font-medium leading-normal hover:text-primary dark:hover:text-primary" href="#">Academics</a>
          <a class="text-sm font-medium leading-normal hover:text-primary dark:hover:text-primary" href="#">Research</a>
          <a class="text-sm font-medium leading-normal hover:text-primary dark:hover:text-primary" href="#">Student Life</a>
          <a class="text-sm font-medium leading-normal hover:text-primary dark:hover:text-primary" href="#">About</a>
        </nav>
        <div class="flex gap-2">
          <button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-primary/90">
            <span class="truncate">Apply Now</span>
          </button>
          <button class="flex max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 bg-gray-200/80 dark:bg-gray-800 text-[#0d0d1b] dark:text-gray-200 gap-2 text-sm font-bold leading-normal tracking-[0.015em] min-w-0 px-2.5 hover:bg-gray-200 dark:hover:bg-gray-700">
            <span class="material-symbols-outlined text-xl">search</span>
          </button>
        </div>
      </div>
      <button class="md:hidden flex max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 bg-gray-200/80 dark:bg-gray-800 text-[#0d0d1b] dark:text-gray-200 gap-2 text-sm font-bold leading-normal tracking-[0.015em] min-w-0 px-2.5 hover:bg-gray-200 dark:hover:bg-gray-700">
        <span class="material-symbols-outlined text-xl">menu</span>
      </button>
    </div>
  </header>
  <main class="flex-grow">
    <!-- HeroSection -->
    <section class="w-full">
      <div class="container mx-auto px-4 py-16 sm:py-24">
        <div class="min-h-[480px] flex flex-col gap-8 rounded-xl items-center justify-center p-6 text-center bg-cover bg-center bg-no-repeat" data-alt="Diverse group of university students smiling and collaborating on a sunny campus green." style='background-image: linear-gradient(rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.5) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuAAFc3ceHHlSQdfCgiRz0iiVEhzXoEGQvP9J6bpkm2QEaVz01Lq81H7W0FogNK5yZ_L1qZmb3iTiAVfPvYMN0nYRLoMEFUlacxB-OArHoArYGK9h8WkNlIzdurDg-8Eu1Mmmi7QbIHCH_cJuraw591KIn813kqEnLTQjKyJrGOjnEp7trQ_aajlSyzk7OTlhAZBglXb2iK44heqR3V_Zw5kRsX7UMu9Uhrguf9YtVotTdfnWS0FwdImNsDG2sKyafa8oNS1pd8uuIcR");'>
          <div class="flex flex-col gap-4 max-w-3xl">
            <h1 class="text-white text-4xl font-black leading-tight tracking-[-0.033em] md:text-5xl">
              Your Future Starts Here: Scholarships at Valley View
            </h1>
            <p class="text-white text-base font-normal leading-normal md:text-lg">
              We are committed to helping you achieve your academic dreams through a wide range of financial aid opportunities.
            </p>
          </div>
          <button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90">
            <span class="truncate">Explore Scholarships</span>
          </button>
        </div>
      </div>
    </section>
    <!-- Scholarship Listings -->
    <section class="w-full container mx-auto px-4 pb-16">
      <!-- SectionHeader -->
      <h2 class="text-2xl md:text-3xl font-bold leading-tight tracking-[-0.015em] px-4 pb-4 pt-5 text-center md:text-left dark:text-white">Find Your Scholarship</h2>
      <!-- Chips/Filters -->
      <div class="flex flex-wrap gap-3 p-3">
        <button class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-primary/20 dark:bg-primary/30 pl-4 pr-4 ring-2 ring-primary">
          <p class="text-primary dark:text-white text-sm font-medium leading-normal">All</p>
        </button>
        <button class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-gray-200 dark:bg-gray-800 pl-4 pr-4 hover:bg-gray-300 dark:hover:bg-gray-700">
          <p class="text-sm font-medium leading-normal">Undergraduate</p>
        </button>
        <button class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-gray-200 dark:bg-gray-800 pl-4 pr-4 hover:bg-gray-300 dark:hover:bg-gray-700">
          <p class="text-sm font-medium leading-normal">Graduate</p>
        </button>
        <button class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-gray-200 dark:bg-gray-800 pl-4 pr-4 hover:bg-gray-300 dark:hover:bg-gray-700">
          <p class="text-sm font-medium leading-normal">Merit-Based</p>
        </button>
        <button class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-gray-200 dark:bg-gray-800 pl-4 pr-4 hover:bg-gray-300 dark:hover:bg-gray-700">
          <p class="text-sm font-medium leading-normal">Need-Based</p>
        </button>
      </div>
      <!-- Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4">
        <!-- Card 1 -->
        <div class="flex flex-col items-stretch justify-start rounded-xl overflow-hidden shadow-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800">
          <div class="w-full bg-center bg-no-repeat aspect-video bg-cover" data-alt="A student working with robotics equipment in a modern engineering lab." style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAkeW-zJ3nL3TSKVD87ad6ZT_rsIg3Z5Jz0x5ubjTrSY9Lc7cSw5wtS9juCtjDGg9iBBLP-Pr0gQHR_nY8bPoWeI77Ocab0ATklJOT0QHtcaV13t0h6HkLYuOu_DwzybrsERWGHUsSpXTIMglFdZ-7yvJCxy7wDgl1LSesHdpfZMLDySSFApsJFnmyyZzSuArW6LZQJLUT6hW4pgCXVCLklnxXkdCq9vrzFYXMRO_nTKnB4brDaSGnlZNiDnPF8uXGcnKCFdZry3mNz");'></div>
          <div class="flex w-full grow flex-col items-stretch justify-between gap-4 p-6">
            <div>
              <p class="text-primary dark:text-accent text-sm font-medium leading-normal">Merit-Based | For Engineering Students</p>
              <p class="text-lg font-bold leading-tight tracking-[-0.015em] mt-1 dark:text-white">Innovators of Tomorrow Scholarship</p>
              <p class="text-gray-600 dark:text-gray-400 text-base font-normal leading-normal mt-2">
                Awarded to outstanding undergraduate students in the Faculty of Engineering based on academic excellence and leadership potential.
              </p>
            </div>
            <div class="flex items-end justify-between gap-3 mt-4">
              <p class="text-gray-700 dark:text-gray-300 text-lg font-bold leading-normal">Value: $10,000</p>
              <button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-medium leading-normal hover:bg-primary/90">
                <span class="truncate">Learn More &amp; Apply</span>
              </button>
            </div>
          </div>
        </div>
        <!-- Card 2 -->
        <div class="flex flex-col items-stretch justify-start rounded-xl overflow-hidden shadow-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800">
          <div class="w-full bg-center bg-no-repeat aspect-video bg-cover" data-alt="A student painting on a canvas in a bright art studio." style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuA7ZOlh06V9KT--sAda4jBs9HTCUsGs3e8o8DoMK7sqKBkHf8dFhT_ItT2fUTIWTGyIMdV39eTbu3XW5ijRYdwB5Y9yY2guLdp0Z33x0uU9rhwqWwnA87Gb9iJ-MFQNUnvj-XsgN2MadjHUOb5q_n_Fdz4CUEyPoRqvTLU1RrCus_H4opdHiVUpmmsN9uhj-ieEqHvaGhzS26pAtdVNU4mfiRhbSOPCdELRf3QkSx-DXdFd9DbX2Fdyuiz_9UmLNpIRZY-yN6tH_fpU");'></div>
          <div class="flex w-full grow flex-col items-stretch justify-between gap-4 p-6">
            <div>
              <p class="text-primary dark:text-accent text-sm font-medium leading-normal">Merit-Based | For Arts &amp; Humanities</p>
              <p class="text-lg font-bold leading-tight tracking-[-0.015em] mt-1 dark:text-white">Creative Visionary Grant</p>
              <p class="text-gray-600 dark:text-gray-400 text-base font-normal leading-normal mt-2">
                Supports talented graduate students in the Arts &amp; Humanities who demonstrate exceptional creative potential and a unique artistic voice.
              </p>
            </div>
            <div class="flex items-end justify-between gap-3 mt-4">
              <p class="text-gray-700 dark:text-gray-300 text-lg font-bold leading-normal">Value: $8,500</p>
              <button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-medium leading-normal hover:bg-primary/90">
                <span class="truncate">Learn More &amp; Apply</span>
              </button>
            </div>
          </div>
        </div>
        <!-- Card 3 -->
        <div class="flex flex-col items-stretch justify-start rounded-xl overflow-hidden shadow-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800">
          <div class="w-full bg-center bg-no-repeat aspect-video bg-cover" data-alt="A diverse group of students studying together in a library." style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAC1txR_pa4b0trv1wfyuI2EG3j1ojIokwY7-aFwIs_3y201M1Hp5j9NyyWRDd_W0M1lTAriiSLx1zPsMLwBkQKp1LkcsBTn2TLwTbPYJm2cQKerQXLqyfJH0G38pTXnbeHHSLeOUCMs8HDFaaJPg8NHmmgS0hYvyovJBiFEtj3ZBdgMpkDJ16eig1OL6SC7QiGFPwX42PwOmUDoQFSFQ3MP3UNqVMuHeZjYoCjMCD1FiPEvGx0-ats0BhVlOLzY4-3dHjAxRqExtzq");'></div>
          <div class="flex w-full grow flex-col items-stretch justify-between gap-4 p-6">
            <div>
              <p class="text-primary dark:text-accent text-sm font-medium leading-normal">Need-Based | All Majors</p>
              <p class="text-lg font-bold leading-tight tracking-[-0.015em] mt-1 dark:text-white">Community Leader Award</p>
              <p class="text-gray-600 dark:text-gray-400 text-base font-normal leading-normal mt-2">
                For students who have demonstrated strong commitment to community service and leadership, with consideration for financial need.
              </p>
            </div>
            <div class="flex items-end justify-between gap-3 mt-4">
              <p class="text-gray-700 dark:text-gray-300 text-lg font-bold leading-normal">Value: $5,000</p>
              <button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-medium leading-normal hover:bg-primary/90">
                <span class="truncate">Learn More &amp; Apply</span>
              </button>
            </div>
          </div>
        </div>
        <!-- Card 4 -->
        <div class="flex flex-col items-stretch justify-start rounded-xl overflow-hidden shadow-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800">
          <div class="w-full bg-center bg-no-repeat aspect-video bg-cover" data-alt="Close-up of a scientist looking into a microscope in a laboratory." style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBXbPc32IPd4JDCWysH_hC0q17-AwaJru1IDWS0taFZbOcTbambVs1K9XY9BQy3wVOxExslw3Nhds5SkJDPFo7fGRydSpoj7q4FeRjv-S8MNZpiCkVpNZ49luWBwGtAn5jzJYmlSVn98CmZSV_g1ydBrH-xNTCSCD2GyNk4EB3_QNrsx3Ps4VCurmMQ3qIPepvuTJmeSQXn_iaySdIRbNXJdje4d2UiMvZ8vTYvefwDcSnD7DWJTjdf93nc31piZzvy97Iui_ktsK-y");'></div>
          <div class="flex w-full grow flex-col items-stretch justify-between gap-4 p-6">
            <div>
              <p class="text-primary dark:text-accent text-sm font-medium leading-normal">Research | Graduate</p>
              <p class="text-lg font-bold leading-tight tracking-[-0.015em] mt-1 dark:text-white">Future of Science Fellowship</p>
              <p class="text-gray-600 dark:text-gray-400 text-base font-normal leading-normal mt-2">
                A prestigious fellowship for graduate students pursuing groundbreaking research in the natural sciences.
              </p>
            </div>
            <div class="flex items-end justify-between gap-3 mt-4">
              <p class="text-gray-700 dark:text-gray-300 text-lg font-bold leading-normal">Value: $20,000</p>
              <button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-medium leading-normal hover:bg-primary/90">
                <span class="truncate">Learn More &amp; Apply</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- How to Apply Section -->
    <section class="w-full bg-white dark:bg-gray-900 py-16 sm:py-24">
      <div class="container mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-bold text-center leading-tight tracking-[-0.015em] dark:text-white">Simple Steps to Apply</h2>
        <p class="mt-4 max-w-2xl mx-auto text-center text-gray-600 dark:text-gray-400">Follow our straightforward process to secure your funding. We're here to help you every step of the way.</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12 max-w-5xl mx-auto">
          <!-- Step 1 -->
          <div class="flex flex-col items-center text-center">
            <div class="flex items-center justify-center size-16 rounded-full bg-primary/20 dark:bg-primary/30 text-primary dark:text-accent">
              <span class="material-symbols-outlined text-4xl">search</span>
            </div>
            <h3 class="text-lg font-bold mt-4 dark:text-white">1. Find a Scholarship</h3>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Use our filters to browse scholarships that match your profile and academic goals.</p>
          </div>
          <!-- Step 2 -->
          <div class="flex flex-col items-center text-center">
            <div class="flex items-center justify-center size-16 rounded-full bg-primary/20 dark:bg-primary/30 text-primary dark:text-accent">
              <span class="material-symbols-outlined text-4xl">description</span>
            </div>
            <h3 class="text-lg font-bold mt-4 dark:text-white">2. Prepare Documents</h3>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Gather your transcripts, essays, and letters of recommendation as required by the application.</p>
          </div>
          <!-- Step 3 -->
          <div class="flex flex-col items-center text-center">
            <div class="flex items-center justify-center size-16 rounded-full bg-primary/20 dark:bg-primary/30 text-primary dark:text-accent">
              <span class="material-symbols-outlined text-4xl">send</span>
            </div>
            <h3 class="text-lg font-bold mt-4 dark:text-white">3. Submit Application</h3>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Complete the online form and upload your documents before the deadline.</p>
          </div>
        </div>
      </div>
    </section>
    <!-- FAQ and Contact Section -->
    <section class="w-full py-16 sm:py-24">
      <div class="container mx-auto px-4 grid grid-cols-1 lg:grid-cols-5 gap-12">
        <!-- FAQ -->
        <div class="lg:col-span-3">
          <h2 class="text-2xl md:text-3xl font-bold leading-tight tracking-[-0.015em] dark:text-white">Have Questions? We Have Answers.</h2>
          <div class="mt-8 space-y-4">
            <details class="group rounded-lg bg-gray-100 dark:bg-gray-900 p-4 cursor-pointer">
              <summary class="flex items-center justify-between font-medium dark:text-white">
                When is the application deadline?
                <span class="material-symbols-outlined transition-transform duration-300 group-open:rotate-180">expand_more</span>
              </summary>
              <p class="mt-3 text-gray-600 dark:text-gray-400">Deadlines vary by scholarship. Please check the details on each scholarship card. Most applications for the fall semester are due by March 31st.</p>
            </details>
            <details class="group rounded-lg bg-gray-100 dark:bg-gray-900 p-4 cursor-pointer">
              <summary class="flex items-center justify-between font-medium dark:text-white">
                Can international students apply?
                <span class="material-symbols-outlined transition-transform duration-300 group-open:rotate-180">expand_more</span>
              </summary>
              <p class="mt-3 text-gray-600 dark:text-gray-400">Yes, many of our scholarships are open to international students. Look for the 'International Students Welcome' tag on the scholarship details page.</p>
            </details>
            <details class="group rounded-lg bg-gray-100 dark:bg-gray-900 p-4 cursor-pointer">
              <summary class="flex items-center justify-between font-medium dark:text-white">
                How will I be notified if I receive an award?
                <span class="material-symbols-outlined transition-transform duration-300 group-open:rotate-180">expand_more</span>
              </summary>
              <p class="mt-3 text-gray-600 dark:text-gray-400">Successful applicants will be notified via email approximately 6-8 weeks after the application deadline. You can also check your status on the student portal.</p>
            </details>
          </div>
        </div>
        <!-- Contact -->
        <div class="lg:col-span-2">
          <div class="rounded-xl bg-primary/10 dark:bg-primary/20 p-8">
            <h3 class="text-xl font-bold text-primary dark:text-white">Need Help? Get in Touch.</h3>
            <p class="mt-3 text-gray-700 dark:text-gray-300">Our financial aid advisors are ready to assist you with your questions.</p>
            <div class="mt-6 space-y-4">
              <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-primary dark:text-accent mt-1">mail</span>
                <div>
                  <h4 class="font-semibold dark:text-white">Email</h4>
                  <a class="text-primary dark:text-gray-300 hover:underline" href="mailto:finaid@valleyview.edu">finaid@valleyview.edu</a>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-primary dark:text-accent mt-1">call</span>
                <div>
                  <h4 class="font-semibold dark:text-white">Phone</h4>
                  <a class="text-primary dark:text-gray-300 hover:underline" href="tel:+1234567890">(123) 456-7890</a>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-primary dark:text-accent mt-1">schedule</span>
                <div>
                  <h4 class="font-semibold dark:text-white">Office Hours</h4>
                  <p class="text-gray-700 dark:text-gray-300">Mon - Fri, 9:00 AM - 5:00 PM</p>
                </div>
              </div>
            </div>
            <button class="mt-6 flex w-full min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90">
              <span class="truncate">Schedule an Appointment</span>
            </button>
          </div>
        </div>
      </div>
    </section>
  </main>
  <!-- Footer -->
  <footer class="bg-gray-100 dark:bg-gray-900">
    <div class="container mx-auto px-4 py-12">
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8">
        <div class="col-span-2 lg:col-span-1">
          <div class="flex items-center gap-2">
            <div class="size-6 text-primary">
              <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                <path clip-rule="evenodd" d="M24 4H42V17.3333V30.6667H24V44H6V30.6667V17.3333H24V4Z" fill="currentColor" fill-rule="evenodd"></path>
              </svg>
            </div>
            <h2 class="text-lg font-bold leading-tight tracking-[-0.015em] dark:text-white">Valley View University</h2>
          </div>
          <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">Empowering minds, shaping futures.</p>
        </div>
        <div>
          <h4 class="font-semibold dark:text-white">Prospective Students</h4>
          <ul class="mt-4 space-y-2 text-sm">
            <li><a class="text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-primary" href="#">Apply</a></li>
            <li><a class="text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-primary" href="#">Visit Campus</a></li>
            <li><a class="text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-primary" href="#">Programs</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold dark:text-white">Current Students</h4>
          <ul class="mt-4 space-y-2 text-sm">
            <li><a class="text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-primary" href="#">Portal</a></li>
            <li><a class="text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-primary" href="#">Library</a></li>
            <li><a class="text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-primary" href="#">Events</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold dark:text-white">Resources</h4>
          <ul class="mt-4 space-y-2 text-sm">
            <li><a class="text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-primary" href="#">News</a></li>
            <li><a class="text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-primary" href="#">Directory</a></li>
            <li><a class="text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-primary" href="#">Careers</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold dark:text-white">Contact</h4>
          <ul class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-400">
            <li>123 University Drive</li>
            <li>Valley View, USA 12345</li>
            <li>(123) 555-0101</li>
          </ul>
        </div>
      </div>
      <div class="mt-12 border-t border-gray-200 dark:border-gray-800 pt-8 flex flex-col sm:flex-row justify-between items-center">
        <p class="text-sm text-gray-500">© 2024 Valley View University. All rights reserved.</p>
        <div class="flex space-x-4 mt-4 sm:mt-0">
          <a class="text-gray-500 hover:text-primary dark:hover:text-primary" href="#">Privacy Policy</a>
          <a class="text-gray-500 hover:text-primary dark:hover:text-primary" href="#">Terms of Service</a>
        </div>
      </div>
    </div>
  </footer>
</div>

<?php
include 'includes/footer.php';
?>