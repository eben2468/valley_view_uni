<?php
$pageTitle = "Office of the University Recorder - Valley View University";
$activePage = "recorders";
include 'includes/header.php';
?>

<div class="relative flex min-h-screen w-full flex-col">
  <!-- TopNavBar -->
  <header class="sticky top-0 z-50 w-full border-b border-neutral-light/50 dark:border-neutral-dark/50 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-md">
    <div class="mx-auto flex max-w-7xl items-center justify-between whitespace-nowrap px-4 py-3 sm:px-6 lg:px-8">
      <div class="flex items-center gap-4">
        <div class="size-6 text-primary dark:text-secondary">
          <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
            <path clip-rule="evenodd" d="M24 4H42V17.3333V30.6667H24V44H6V30.6667V17.3333H24V4Z" fill="currentColor" fill-rule="evenodd"></path>
          </svg>
        </div>
        <h2 class="text-lg font-bold tracking-tight">Valley View University</h2>
      </div>
      <nav class="hidden items-center gap-8 lg:flex">
        <a class="text-sm font-medium hover:text-accent dark:hover:text-accent" href="#">Admissions</a>
        <a class="text-sm font-medium hover:text-accent dark:hover:text-accent" href="#">Academics</a>
        <a class="text-sm font-medium hover:text-accent dark:hover:text-accent" href="#">Research</a>
        <a class="text-sm font-medium hover:text-accent dark:hover:text-accent" href="#">Campus Life</a>
        <a class="text-sm font-medium hover:text-accent dark:hover:text-accent" href="#">About</a>
      </nav>
      <div class="flex items-center gap-2">
        <button class="flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-wide transition-colors hover:bg-primary/90">
          <span class="truncate">Apply Now</span>
        </button>
        <button class="flex h-10 w-10 cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-neutral-light dark:bg-neutral-dark text-text-light dark:text-text-dark transition-colors hover:bg-neutral-light/80 dark:hover:bg-neutral-dark/80">
          <span class="material-symbols-outlined text-xl">search</span>
        </button>
      </div>
    </div>
  </header>
  <main class="flex-grow">
    <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
      <!-- HeroSection -->
      <div class="relative mb-16 overflow-hidden rounded-xl">
        <div class="flex min-h-[480px] flex-col items-start justify-end gap-6 bg-cover bg-center bg-no-repeat p-8 text-white md:p-12" data-alt="Abstract geometric pattern with shades of navy blue and gold" style='background-image: linear-gradient(rgba(10, 35, 66, 0.4) 0%, rgba(10, 35, 66, 0.8) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuBT0OEp80AUnjmN4vAtcFJa7fRqi8kwPLpU0bXaaHxP1DnmUJ5ZDWhQ-BWpSE3fCHpXMacVSsuA8Clvhz20CukBP9g8TXJ4RRPDWB6vumJ4_dEeDXT80XZHM4jntUZLJ6zCCAzG9tHy315itGuk1KE9b3qdAY2W60vWfbVxx1A751xWymneS4YI1PGWm1MbcyGT7_mDppIgSV28sdQsIF_5dz7uKpwG1htfft8p73hDOdMo-HeRJnfa-4SElhjanYnJhIEDdFre8pjw");'>
          <div class="flex max-w-2xl flex-col gap-2 text-left">
            <h1 class="text-4xl font-black leading-tight tracking-tighter md:text-5xl">
              Office of the University Recorder
            </h1>
            <h2 class="text-base font-normal leading-normal md:text-lg">
              Your official source for academic records, transcripts, and university archives.
            </h2>
          </div>
          <button class="flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-secondary text-primary text-base font-bold leading-normal tracking-wide transition-transform hover:scale-105">
            <span class="truncate">Request Transcript Online</span>
          </button>
        </div>
      </div>
      <!-- Introduction -->
      <section class="mb-16 text-center">
        <p class="mx-auto max-w-3xl text-lg leading-relaxed text-text-subtle-light dark:text-text-subtle-dark">
          The Office of the University Recorder is dedicated to upholding the academic integrity of Valley View University. We are the official stewards of student academic records and are committed to providing responsive, considerate, and professional service to students, alumni, faculty, and staff in support of the University's mission.
        </p>
      </section>
      <!-- Core Services Section -->
      <section class="mb-16">
        <h2 class="mb-8 text-center text-3xl font-bold tracking-tight">Core Services</h2>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <div class="flex flex-col gap-4 rounded-xl bg-neutral-light p-6 text-center dark:bg-neutral-dark">
            <span class="material-symbols-outlined mx-auto text-4xl text-accent">description</span>
            <h3 class="text-lg font-bold">Request a Transcript</h3>
            <p class="text-sm text-text-subtle-light dark:text-text-subtle-dark">Order official transcripts for academic or professional use.</p>
          </div>
          <div class="flex flex-col gap-4 rounded-xl bg-neutral-light p-6 text-center dark:bg-neutral-dark">
            <span class="material-symbols-outlined mx-auto text-4xl text-accent">verified_user</span>
            <h3 class="text-lg font-bold">Verify Enrollment</h3>
            <p class="text-sm text-text-subtle-light dark:text-text-subtle-dark">Obtain official proof of your enrollment status for loans or insurance.</p>
          </div>
          <div class="flex flex-col gap-4 rounded-xl bg-neutral-light p-6 text-center dark:bg-neutral-dark">
            <span class="material-symbols-outlined mx-auto text-4xl text-accent">school</span>
            <h3 class="text-lg font-bold">Graduation Information</h3>
            <p class="text-sm text-text-subtle-light dark:text-text-subtle-dark">Find details on applying for graduation and commencement.</p>
          </div>
          <div class="flex flex-col gap-4 rounded-xl bg-neutral-light p-6 text-center dark:bg-neutral-dark">
            <span class="material-symbols-outlined mx-auto text-4xl text-accent">calendar_month</span>
            <h3 class="text-lg font-bold">Academic Calendar</h3>
            <p class="text-sm text-text-subtle-light dark:text-text-subtle-dark">View key dates and deadlines for the current academic year.</p>
          </div>
          <div class="flex flex-col gap-4 rounded-xl bg-neutral-light p-6 text-center dark:bg-neutral-dark">
            <span class="material-symbols-outlined mx-auto text-4xl text-accent">policy</span>
            <h3 class="text-lg font-bold">Records Privacy (FERPA)</h3>
            <p class="text-sm text-text-subtle-light dark:text-text-subtle-dark">Understand your rights regarding educational records privacy.</p>
          </div>
          <div class="flex flex-col gap-4 rounded-xl bg-neutral-light p-6 text-center dark:bg-neutral-dark">
            <span class="material-symbols-outlined mx-auto text-4xl text-accent">download</span>
            <h3 class="text-lg font-bold">Forms &amp; Resources</h3>
            <p class="text-sm text-text-subtle-light dark:text-text-subtle-dark">Access downloadable PDF forms and other essential resources.</p>
          </div>
        </div>
      </section>
      <!-- FAQ Section -->
      <section class="mb-16">
        <h2 class="mb-8 text-center text-3xl font-bold tracking-tight">Frequently Asked Questions</h2>
        <div class="mx-auto max-w-3xl space-y-4">
          <details class="group rounded-lg bg-neutral-light p-4 dark:bg-neutral-dark">
            <summary class="flex cursor-pointer list-none items-center justify-between font-medium">
              How do I request an official transcript?
              <span class="transition group-open:rotate-180">
                <span class="material-symbols-outlined">expand_more</span>
              </span>
            </summary>
            <p class="mt-4 text-text-subtle-light dark:text-text-subtle-dark">You can request a transcript online through our secure portal, by mail, or in person at our office. The fastest method is online. Click the "Request Transcript Online" button at the top of the page to begin.</p>
          </details>
          <details class="group rounded-lg bg-neutral-light p-4 dark:bg-neutral-dark">
            <summary class="flex cursor-pointer list-none items-center justify-between font-medium">
              What is the processing time for transcript requests?
              <span class="transition group-open:rotate-180">
                <span class="material-symbols-outlined">expand_more</span>
              </span>
            </summary>
            <p class="mt-4 text-text-subtle-light dark:text-text-subtle-dark">Standard processing time for electronic transcripts is typically within 24 hours. Mailed transcripts may take 3-5 business days for processing, plus mailing time. Expedited options are available for an additional fee.</p>
          </details>
          <details class="group rounded-lg bg-neutral-light p-4 dark:bg-neutral-dark">
            <summary class="flex cursor-pointer list-none items-center justify-between font-medium">
              How can I verify my enrollment for insurance or a loan?
              <span class="transition group-open:rotate-180">
                <span class="material-symbols-outlined">expand_more</span>
              </span>
            </summary>
            <p class="mt-4 text-text-subtle-light dark:text-text-subtle-dark">You can generate an official Enrollment Verification Certificate through your student portal or by submitting a request form to our office. This certificate can be used for insurance companies, lenders, and other organizations.</p>
          </details>
        </div>
      </section>
      <!-- Meet the Team & Contact Info -->
      <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
        <!-- Meet the Team -->
        <section>
          <h2 class="mb-8 text-center text-3xl font-bold tracking-tight">Meet the Team</h2>
          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div class="flex flex-col items-center gap-4 text-center">
              <img class="h-32 w-32 rounded-full object-cover" data-alt="Portrait of a friendly man with glasses" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDLMlsgV_S2RgOy6fE6weOWSe2NgsC2qUCr0a5cgUezG9AxENJ8R6J5NZc9_EmgS2yasPcBDnvdN_gSAl7vOLPe1uHjPQjLwheqkg1wx4pNgUxBm44rXX9vaFz19jegSpCpyUnfHGe7zJOqYx0kmwQOJw2KYk6uSQMvqt867MTSLG1aNNPNu-J-Wvi4KJUcF9F5XxopVNKdWSVMt2VHBiamDOl-3SYW5UQ3r_E0zl_hNhxApSc_DvzRJOo_3C0XaEycKBmPcH9tdIuz"/>
              <div>
                <p class="font-bold">Dr. Alistair Finch</p>
                <p class="text-sm text-text-subtle-light dark:text-text-subtle-dark">University Recorder</p>
              </div>
            </div>
            <div class="flex flex-col items-center gap-4 text-center">
              <img class="h-32 w-32 rounded-full object-cover" data-alt="Portrait of a professional woman smiling" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC63Jxtj9xwikDh6MACtf67VWf97bqWuHQ5Q-r1e-ozP6tU5nOfPVctrC9nAaFt-FkX6cw44V09XgCn230cAmSvOE0KMdpDX_K8RdTM2O5iNb7HpuOckQ-68FNffVWVioMeVCUnCtmNzq4VdjGJVmwhtVwMk4oYg0vPLZsuTC3Qa7heYk-EwfIda2rYcU_Z5_RgT_ORRVOpYIPsDFiGlBICx_ChDmgayPwJc5-esoZ_G17vrDqu1ISf9GRqEZnmWaX-LTyKNqPlGDpT"/>
              <div>
                <p class="font-bold">Eleanor Vance</p>
                <p class="text-sm text-text-subtle-light dark:text-text-subtle-dark">Associate Recorder</p>
              </div>
            </div>
          </div>
        </section>
        <!-- Contact Information -->
        <section>
          <h2 class="mb-8 text-center text-3xl font-bold tracking-tight">Contact Information</h2>
          <div class="flex flex-col gap-6 rounded-xl bg-neutral-light p-6 dark:bg-neutral-dark">
            <div class="flex items-start gap-4">
              <span class="material-symbols-outlined mt-1 text-2xl text-accent">location_on</span>
              <div>
                <p class="font-semibold">Office Location</p>
                <p class="text-text-subtle-light dark:text-text-subtle-dark">Halloway Hall, Room 101<br/>123 University Drive, Valley View</p>
              </div>
            </div>
            <div class="flex items-start gap-4">
              <span class="material-symbols-outlined mt-1 text-2xl text-accent">schedule</span>
              <div>
                <p class="font-semibold">Office Hours</p>
                <p class="text-text-subtle-light dark:text-text-subtle-dark">Monday - Friday: 9:00 AM - 4:00 PM</p>
              </div>
            </div>
            <div class="flex items-start gap-4">
              <span class="material-symbols-outlined mt-1 text-2xl text-accent">call</span>
              <div>
                <p class="font-semibold">Phone</p>
                <p class="text-text-subtle-light dark:text-text-subtle-dark">(555) 123-4567</p>
              </div>
            </div>
            <div class="flex items-start gap-4">
              <span class="material-symbols-outlined mt-1 text-2xl text-accent">email</span>
              <div>
                <p class="font-semibold">Email</p>
                <p class="text-text-subtle-light dark:text-text-subtle-dark">recorder@valleyview.edu</p>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </main>
  <!-- Footer -->
  <footer class="bg-primary dark:bg-neutral-dark text-white dark:text-text-dark">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
      <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
        <div class="col-span-2 md:col-span-1">
          <div class="flex items-center gap-3">
            <div class="size-6 text-secondary">
              <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                <path clip-rule="evenodd" d="M24 4H42V17.3333V30.6667H24V44H6V30.6667V17.3333H24V4Z" fill="currentColor" fill-rule="evenodd"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold">Valley View University</h3>
          </div>
          <p class="mt-4 text-sm text-white/80 dark:text-text-dark/80">Excellence in Education Since 1902.</p>
        </div>
        <div>
          <h4 class="font-bold">Quick Links</h4>
          <ul class="mt-4 space-y-2 text-sm">
            <li><a class="text-white/80 dark:text-text-dark/80 hover:text-white dark:hover:text-accent" href="#">My VVU Portal</a></li>
            <li><a class="text-white/80 dark:text-text-dark/80 hover:text-white dark:hover:text-accent" href="#">Library</a></li>
            <li><a class="text-white/80 dark:text-text-dark/80 hover:text-white dark:hover:text-accent" href="#">Bookstore</a></li>
            <li><a class="text-white/80 dark:text-text-dark/80 hover:text-white dark:hover:text-accent" href="#">Careers</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-bold">Information For</h4>
          <ul class="mt-4 space-y-2 text-sm">
            <li><a class="text-white/80 dark:text-text-dark/80 hover:text-white dark:hover:text-accent" href="#">Future Students</a></li>
            <li><a class="text-white/80 dark:text-text-dark/80 hover:text-white dark:hover:text-accent" href="#">Current Students</a></li>
            <li><a class="text-white/80 dark:text-text-dark/80 hover:text-white dark:hover:text-accent" href="#">Faculty &amp; Staff</a></li>
            <li><a class="text-white/80 dark:text-text-dark/80 hover:text-white dark:hover:text-accent" href="#">Alumni</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-bold">Connect</h4>
          <div class="mt-4 flex space-x-4">
            <!-- Social icons would go here -->
          </div>
        </div>
      </div>
      <div class="mt-12 border-t border-white/20 dark:border-text-dark/20 pt-8 text-center text-sm text-white/80 dark:text-text-dark/80">
        <p>© 2024 Valley View University. All rights reserved. | <a class="hover:underline" href="#">Privacy Policy</a></p>
      </div>
    </div>
  </footer>
</div>

<?php
include 'includes/footer.php';
?>