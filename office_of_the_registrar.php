<?php
$pageTitle = "Office of the Registrar - Valley View University";
$activePage = "office_of_the_registrar";
include 'includes/header.php';
?>

<div class="relative flex min-h-screen w-full flex-col group/design-root overflow-x-hidden">
  <div class="layout-container flex h-full grow flex-col">
    <!-- TopNavBar Component -->
    <div class="px-4 sm:px-8 md:px-16 lg:px-24 xl:px-40 flex justify-center py-3 border-b border-surface-light dark:border-surface-dark">
      <div class="w-full max-w-screen-xl">
        <header class="flex items-center justify-between whitespace-nowrap px-4 py-3">
          <div class="flex items-center gap-8">
            <div class="flex items-center gap-4 text-text-primary-light dark:text-text-primary-dark">
              <div class="size-6 text-secondary dark:text-accent">
                <span class="material-symbols-outlined !text-3xl">school</span>
              </div>
              <h2 class="text-xl font-bold leading-tight tracking-[-0.015em]">Valley View University</h2>
            </div>
            <nav class="hidden lg:flex items-center gap-9">
              <a class="text-sm font-medium leading-normal hover:text-secondary dark:hover:text-accent" href="#">Academics</a>
              <a class="text-sm font-medium leading-normal hover:text-secondary dark:hover:text-accent" href="#">Admissions</a>
              <a class="text-sm font-medium leading-normal hover:text-secondary dark:hover:text-accent" href="#">Research</a>
              <a class="text-sm font-medium leading-normal hover:text-secondary dark:hover:text-accent" href="#">Student Life</a>
              <a class="text-sm font-medium leading-normal hover:text-secondary dark:hover:text-accent" href="#">About</a>
            </nav>
          </div>
          <div class="flex flex-1 justify-end items-center gap-4">
            <label class="hidden md:flex flex-col min-w-40 !h-10 max-w-64">
              <div class="flex w-full flex-1 items-stretch rounded-lg h-full bg-surface-light dark:bg-surface-dark">
                <div class="text-text-secondary-light dark:text-text-secondary-dark flex items-center justify-center pl-3">
                  <span class="material-symbols-outlined">search</span>
                </div>
                <input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg focus:outline-0 focus:ring-0 border-none bg-transparent h-full placeholder:text-text-secondary-light/70 dark:placeholder:text-text-secondary-dark/70 px-2 text-base font-normal leading-normal" placeholder="Search" value=""/>
              </div>
            </label>
            <button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-secondary dark:bg-accent text-white dark:text-secondary text-sm font-bold leading-normal tracking-[0.015em]">
              <span class="truncate">Apply</span>
            </button>
          </div>
        </header>
      </div>
    </div>
    <!-- Main Content Area -->
    <div class="px-4 sm:px-8 md:px-16 lg:px-24 xl:px-40 flex flex-1 justify-center py-5">
      <div class="w-full max-w-screen-xl">
        <!-- HeroSection Component -->
        <div class="mb-8">
          <div class="flex min-h-[400px] flex-col gap-6 bg-cover bg-center bg-no-repeat rounded-xl items-center justify-center p-4 text-center" data-alt="Bright, modern university administration building under a clear blue sky" style='background-image: linear-gradient(rgba(0, 51, 102, 0.4) 0%, rgba(0, 51, 102, 0.7) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuAdHExs_SfkRASYoES-KYWziZLFeXa6CwRE1tFfcoJoSatmp3K87chu9ZaDIp4kjBmAC4kTIatiMlZ3XOe354S5VOhhunVP4Wo9_FMc1LLmh72jKzKTTlzaL4qCmkTEo6z_WERGbhxGfFNtdyLOIJMxOTvuW1sK-AmKP0QVv4GCOd6a1lt3FrWoQ9IVoflIKJeoTiDMa44B7wkgq0Ykb3ud1rt5gDR_byRW18BjRjWDIiNKKd4-z8QKco_zxFkDaYymChai--z4X8Hv");'>
            <div class="flex flex-col gap-2">
              <h1 class="text-white text-4xl font-black leading-tight tracking-[-0.033em] sm:text-5xl">Office of the Registrar</h1>
              <h2 class="text-white text-base font-normal leading-normal sm:text-lg max-w-2xl">Your partner in academic success. We are committed to providing exceptional service to the Valley View University community.</h2>
            </div>
          </div>
        </div>
        <!-- Tabs Component as Quick Links Bar -->
        <div class="mb-12">
          <div class="flex flex-wrap border-b border-surface-light dark:border-surface-dark justify-center sm:justify-between">
            <a class="flex flex-col items-center justify-center border-b-[3px] border-b-secondary dark:border-b-accent text-text-primary-light dark:text-text-primary-dark gap-2 pb-3 pt-2.5 flex-1 min-w-[180px]" href="#">
              <div class="text-secondary dark:text-accent">
                <span class="material-symbols-outlined !text-3xl">description</span>
              </div>
              <p class="text-sm font-bold leading-normal tracking-[0.015em]">Request Transcript</p>
            </a>
            <a class="flex flex-col items-center justify-center border-b-[3px] border-b-transparent gap-2 pb-3 pt-2.5 flex-1 min-w-[180px]" href="#">
              <div class=""><span class="material-symbols-outlined !text-3xl">app_registration</span></div>
              <p class="text-sm font-bold leading-normal tracking-[0.015em]">Register for Classes</p>
            </a>
            <a class="flex flex-col items-center justify-center border-b-[3px] border-b-transparent gap-2 pb-3 pt-2.5 flex-1 min-w-[180px]" href="#">
              <div class=""><span class="material-symbols-outlined !text-3xl">calendar_month</span></div>
              <p class="text-sm font-bold leading-normal tracking-[0.015em]">View Academic Calendar</p>
            </a>
            <a class="flex flex-col items-center justify-center border-b-[3px] border-b-transparent gap-2 pb-3 pt-2.5 flex-1 min-w-[180px]" href="#">
              <div class=""><span class="material-symbols-outlined !text-3xl">folder_open</span></div>
              <p class="text-sm font-bold leading-normal tracking-[0.015em]">Find Forms</p>
            </a>
          </div>
        </div>
        <div class="flex flex-col lg:flex-row gap-12">
          <!-- SideNavBar Component -->
          <aside class="w-full lg:w-1/4">
            <div class="flex flex-col gap-4 p-4 rounded-xl bg-surface-light dark:bg-surface-dark sticky top-5">
              <div class="flex flex-col gap-2">
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-secondary/10 dark:bg-accent/20" href="#">
                  <div class="text-secondary dark:text-accent"><span class="material-symbols-outlined">design_services</span></div>
                  <p class="text-secondary dark:text-accent text-sm font-bold leading-normal">Our Services</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-secondary/10 dark:hover:bg-accent/20" href="#">
                  <div class=""><span class="material-symbols-outlined">folder_shared</span></div>
                  <p class="text-sm font-medium leading-normal">Forms &amp; Documents</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-secondary/10 dark:hover:bg-accent/20" href="#">
                  <div class=""><span class="material-symbols-outlined">group</span></div>
                  <p class="text-sm font-medium leading-normal">Staff Directory</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-secondary/10 dark:hover:bg-accent/20" href="#">
                  <div class=""><span class="material-symbols-outlined">gavel</span></div>
                  <p class="text-sm font-medium leading-normal">Policies</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-secondary/10 dark:hover:bg-accent/20" href="#">
                  <div class=""><span class="material-symbols-outlined">quiz</span></div>
                  <p class="text-sm font-medium leading-normal">FAQ</p>
                </a>
              </div>
              <hr class="border-surface-light dark:border-surface-dark/20 my-4"/>
              <div class="p-4 rounded-lg bg-secondary/5 dark:bg-accent/10">
                <h4 class="font-bold text-text-primary-light dark:text-text-primary-dark mb-3">Contact Us</h4>
                <div class="flex flex-col gap-3 text-sm">
                  <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-base mt-0.5">location_on</span>
                    <p>123 University Drive,<br/>Valley View, USA 12345</p>
                  </div>
                  <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-base">call</span>
                    <p>(123) 456-7890</p>
                  </div>
                  <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-base">mail</span>
                    <p>registrar@vvu.edu</p>
                  </div>
                  <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-base mt-0.5">schedule</span>
                    <p>Mon-Fri, 9am - 5pm</p>
                  </div>
                </div>
              </div>
            </div>
          </aside>
          <!-- Main Content -->
          <main class="w-full lg:w-3/4 flex flex-col gap-10">
            <!-- About Us Section -->
            <section>
              <h3 class="text-text-primary-light dark:text-text-primary-dark text-2xl font-bold leading-tight tracking-[-0.015em] px-4 pb-2">About Us</h3>
              <p class="px-4 text-base leading-relaxed">The Office of the Registrar at Valley View University is dedicated to supporting the academic journey of our students from registration to graduation. We manage student records, course information, and academic policies with integrity and accuracy, ensuring a seamless experience for students, faculty, and staff.</p>
            </section>
            <!-- Our Services Section -->
            <section>
              <h3 class="text-text-primary-light dark:text-text-primary-dark text-2xl font-bold leading-tight tracking-[-0.015em] px-4 pb-4">Our Services</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 flex flex-col gap-3">
                  <div class="text-accent"><span class="material-symbols-outlined !text-3xl">verified_user</span></div>
                  <h4 class="font-bold text-text-primary-light dark:text-text-primary-dark">Enrollment Verification</h4>
                  <p class="text-sm">We provide official verification of student enrollment status for scholarships, insurance, and other needs.</p>
                  <a class="text-secondary dark:text-accent font-bold text-sm mt-2" href="#">Learn More →</a>
                </div>
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 flex flex-col gap-3">
                  <div class="text-accent"><span class="material-symbols-outlined !text-3xl">school</span></div>
                  <h4 class="font-bold text-text-primary-light dark:text-text-primary-dark">Graduation Services</h4>
                  <p class="text-sm">Assisting with diploma applications, degree audits, and commencement information to celebrate your achievement.</p>
                  <a class="text-secondary dark:text-accent font-bold text-sm mt-2" href="#">Learn More →</a>
                </div>
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 flex flex-col gap-3">
                  <div class="text-accent"><span class="material-symbols-outlined !text-3xl">history_edu</span></div>
                  <h4 class="font-bold text-text-primary-light dark:text-text-primary-dark">Student Records</h4>
                  <p class="text-sm">Maintaining the accuracy and privacy of your academic records, including grades and personal information.</p>
                  <a class="text-secondary dark:text-accent font-bold text-sm mt-2" href="#">Learn More →</a>
                </div>
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 flex flex-col gap-3">
                  <div class="text-accent"><span class="material-symbols-outlined !text-3xl">event_available</span></div>
                  <h4 class="font-bold text-text-primary-light dark:text-text-primary-dark">Course Scheduling</h4>
                  <p class="text-sm">Managing the university's academic timetable and classroom scheduling to facilitate your learning.</p>
                  <a class="text-secondary dark:text-accent font-bold text-sm mt-2" href="#">Learn More →</a>
                </div>
              </div>
            </section>
            <!-- FAQ Section -->
            <section>
              <h3 class="text-text-primary-light dark:text-text-primary-dark text-2xl font-bold leading-tight tracking-[-0.015em] px-4 pb-4">Frequently Asked Questions</h3>
              <div class="flex flex-col gap-3">
                <details class="group bg-surface-light dark:bg-surface-dark rounded-xl p-4 cursor-pointer">
                  <summary class="flex items-center justify-between font-medium text-text-primary-light dark:text-text-primary-dark">How do I request an official transcript? <span class="material-symbols-outlined group-open:rotate-180 transition-transform">expand_more</span></summary>
                  <p class="text-sm mt-3 pt-3 border-t border-surface-light dark:border-gray-600">You can request an official transcript through the National Student Clearinghouse. The link is available under our "Forms &amp; Documents" section. Both electronic and paper transcripts are available.</p>
                </details>
                <details class="group bg-surface-light dark:bg-surface-dark rounded-xl p-4 cursor-pointer">
                  <summary class="flex items-center justify-between font-medium text-text-primary-light dark:text-text-primary-dark">What is the deadline to add or drop a class? <span class="material-symbols-outlined group-open:rotate-180 transition-transform">expand_more</span></summary>
                  <p class="text-sm mt-3 pt-3 border-t border-surface-light dark:border-gray-600">The add/drop deadline varies by semester. Please refer to the official Academic Calendar for the most up-to-date information on important deadlines.</p>
                </details>
                <details class="group bg-surface-light dark:bg-surface-dark rounded-xl p-4 cursor-pointer">
                  <summary class="flex items-center justify-between font-medium text-text-primary-light dark:text-text-primary-dark">How do I change my major? <span class="material-symbols-outlined group-open:rotate-180 transition-transform">expand_more</span></summary>
                  <p class="text-sm mt-3 pt-3 border-t border-surface-light dark:border-gray-600">To change your major, you must complete the "Change of Major" form, obtain the required signatures from your new academic advisor, and submit it to our office for processing.</p>
                </details>
              </div>
            </section>
          </main>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
include 'includes/footer.php';
?>