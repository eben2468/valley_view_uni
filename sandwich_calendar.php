<?php
$pageTitle = "Sandwich Calendar - Valley View University";
$activePage = "sandwich_calendar";
include 'includes/header.php';
?>

<div class="relative flex min-h-screen w-full flex-col">
  <!-- TopNavBar -->
  <header class="sticky top-0 z-50 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between whitespace-nowrap border-b border-solid border-[#e6f4eb] dark:border-gray-700 py-3">
        <div class="flex items-center gap-8">
          <a class="flex items-center gap-3 text-[#0d1c12] dark:text-gray-100" href="#">
            <div class="text-[#0d1c12] dark:text-primary">
              <span class="material-symbols-outlined text-3xl">school</span>
            </div>
            <h2 class="text-lg font-bold leading-tight tracking-[-0.015em]">Valley View University</h2>
          </a>
          <nav class="hidden md:flex items-center gap-8">
            <a class="text-[#0d1c12] dark:text-gray-300 hover:text-primary dark:hover:text-primary text-sm font-medium leading-normal transition-colors" href="#">Home</a>
            <a class="text-primary text-sm font-bold leading-normal" href="#">Academics</a>
            <a class="text-[#0d1c12] dark:text-gray-300 hover:text-primary dark:hover:text-primary text-sm font-medium leading-normal transition-colors" href="#">Admissions</a>
            <a class="text-[#0d1c12] dark:text-gray-300 hover:text-primary dark:hover:text-primary text-sm font-medium leading-normal transition-colors" href="#">Student Life</a>
            <a class="text-[#0d1c12] dark:text-gray-300 hover:text-primary dark:hover:text-primary text-sm font-medium leading-normal transition-colors" href="#">Research</a>
          </nav>
        </div>
        <div class="flex flex-1 justify-end gap-4">
          <label class="hidden sm:flex flex-col min-w-40 !h-10 max-w-64">
            <div class="flex w-full flex-1 items-stretch rounded-xl h-full">
              <div class="text-[#479e64] flex border-none bg-[#e6f4eb] dark:bg-gray-800 items-center justify-center pl-4 rounded-l-xl border-r-0">
                <span class="material-symbols-outlined !text-2xl">search</span>
              </div>
              <input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#0d1c12] dark:text-gray-200 focus:outline-0 focus:ring-2 focus:ring-primary/50 border-none bg-[#e6f4eb] dark:bg-gray-800 focus:border-none h-full placeholder:text-[#479e64] dark:placeholder:text-gray-500 px-4 rounded-l-none border-l-0 pl-2 text-base font-normal leading-normal" placeholder="Search" value=""/>
            </div>
          </label>
          <button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-primary text-[#0d1c12] text-sm font-bold leading-normal tracking-[0.015em] hover:bg-opacity-90 transition-opacity">
            <span class="truncate">Apply Now</span>
          </button>
        </div>
      </div>
    </div>
  </header>
  <main class="flex-grow">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
      <div class="flex flex-col gap-8">
        <!-- Page Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-[#e6f4eb] dark:border-gray-700 pb-6">
          <div>
            <!-- PageHeading -->
            <h1 class="text-[#0d1c12] dark:text-white text-4xl sm:text-5xl font-black leading-tight tracking-[-0.033em]">Sandwich Program Calendar</h1>
            <p class="text-lg text-[#479e64] dark:text-gray-400 mt-2">Academic Year 2024/2025</p>
          </div>
          <div class="flex items-center gap-2">
            <button class="flex min-w-[84px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-xl h-10 px-4 bg-primary/20 dark:bg-primary/30 text-[#0d1c12] dark:text-primary text-sm font-bold leading-normal tracking-[0.015em] hover:bg-primary/30 dark:hover:bg-primary/40 transition-colors">
              <span class="material-symbols-outlined !text-xl">download</span>
              <span class="truncate">Download PDF</span>
            </button>
            <button class="flex min-w-[84px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-xl h-10 px-4 bg-[#0d1c12] dark:bg-white text-white dark:text-[#0d1c12] text-sm font-bold leading-normal tracking-[0.015em] hover:bg-opacity-90 transition-opacity">
              <span class="material-symbols-outlined !text-xl">calendar_add_on</span>
              <span class="truncate">Subscribe</span>
            </button>
          </div>
        </div>
        <!-- Chips/Filters -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
          <p class="font-bold shrink-0">Filter by:</p>
          <div class="flex gap-2 flex-wrap">
            <button class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-xl bg-primary text-[#0d1c12] px-4">
              <span class="material-symbols-outlined">checklist</span>
              <p class="text-sm font-medium leading-normal">All Events</p>
            </button>
            <button class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-xl bg-[#e6f4eb] dark:bg-gray-800 dark:hover:bg-gray-700 pl-3 pr-4 hover:bg-[#cee9d7] transition-colors">
              <span class="material-symbols-outlined text-[#28a745]">how_to_reg</span>
              <p class="text-[#0d1c12] dark:text-gray-300 text-sm font-medium leading-normal">Registration</p>
            </button>
            <button class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-xl bg-[#e6f4eb] dark:bg-gray-800 dark:hover:bg-gray-700 pl-3 pr-4 hover:bg-[#cee9d7] transition-colors">
              <span class="material-symbols-outlined text-[#fd7e14]">edit_document</span>
              <p class="text-[#0d1c12] dark:text-gray-300 text-sm font-medium leading-normal">Examinations</p>
            </button>
            <button class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-xl bg-[#e6f4eb] dark:bg-gray-800 dark:hover:bg-gray-700 pl-3 pr-4 hover:bg-[#cee9d7] transition-colors">
              <span class="material-symbols-outlined text-[#dc3545]">notification_important</span>
              <p class="text-[#0d1c12] dark:text-gray-300 text-sm font-medium leading-normal">Fee Deadlines</p>
            </button>
            <button class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-xl bg-[#e6f4eb] dark:bg-gray-800 dark:hover:bg-gray-700 pl-3 pr-4 hover:bg-[#cee9d7] transition-colors">
              <span class="material-symbols-outlined text-[#20c997]">beach_access</span>
              <p class="text-[#0d1c12] dark:text-gray-300 text-sm font-medium leading-normal">Holidays</p>
            </button>
          </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Main Calendar Content -->
          <div class="lg:col-span-2 flex flex-col gap-8">
            <!-- First Trimester -->
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-sm overflow-hidden border border-[#e6f4eb] dark:border-gray-700">
              <h2 class="text-[#0d1c12] dark:text-white text-2xl font-bold leading-tight tracking-[-0.015em] px-6 py-4 border-b border-[#e6f4eb] dark:border-gray-700">First Trimester (September - December 2024)</h2>
              <div class="grid grid-cols-[auto_1fr] gap-x-4 p-6">
                <div class="flex flex-col items-center gap-1">
                  <div class="rounded-full bg-green-100 dark:bg-green-900/50 p-2 text-[#28a745]"><span class="material-symbols-outlined !text-2xl">how_to_reg</span></div>
                  <div class="w-[2px] bg-[#e6f4eb] dark:bg-gray-700 grow"></div>
                </div>
                <div class="flex flex-1 flex-col pb-8">
                  <p class="text-[#0d1c12] dark:text-white text-base font-bold leading-normal">Reporting &amp; Registration for Sandwich Students</p>
                  <p class="text-[#479e64] dark:text-gray-400 text-base font-normal leading-normal">September 2-6, 2024</p>
                </div>
                <div class="flex flex-col items-center gap-1">
                  <div class="w-[2px] bg-[#e6f4eb] dark:bg-gray-700 h-8"></div>
                  <div class="rounded-full bg-green-100 dark:bg-green-900/50 p-2 text-[#28a745]"><span class="material-symbols-outlined !text-2xl">how_to_reg</span></div>
                  <div class="w-[2px] bg-[#e6f4eb] dark:bg-gray-700 grow"></div>
                </div>
                <div class="flex flex-1 flex-col pb-8">
                  <p class="text-[#0d1c12] dark:text-white text-base font-bold leading-normal">Late Registration Period</p>
                  <p class="text-[#479e64] dark:text-gray-400 text-base font-normal leading-normal">September 9-13, 2024</p>
                </div>
                <div class="flex flex-col items-center gap-1">
                  <div class="w-[2px] bg-[#e6f4eb] dark:bg-gray-700 h-8"></div>
                  <div class="rounded-full bg-gray-100 dark:bg-gray-700 p-2 text-[#0d1c12] dark:text-gray-300"><span class="material-symbols-outlined !text-2xl">celebration</span></div>
                  <div class="w-[2px] bg-[#e6f4eb] dark:bg-gray-700 grow"></div>
                </div>
                <div class="flex flex-1 flex-col pb-8">
                  <p class="text-[#0d1c12] dark:text-white text-base font-bold leading-normal">Matriculation Ceremony</p>
                  <p class="text-[#479e64] dark:text-gray-400 text-base font-normal leading-normal">September 20, 2024</p>
                </div>
                <div class="flex flex-col items-center gap-1">
                  <div class="w-[2px] bg-[#e6f4eb] dark:bg-gray-700 h-8"></div>
                  <div class="rounded-full bg-orange-100 dark:bg-orange-900/50 p-2 text-[#fd7e14]"><span class="material-symbols-outlined !text-2xl">edit_document</span></div>
                  <div class="w-[2px] bg-[#e6f4eb] dark:bg-gray-700 grow"></div>
                </div>
                <div class="flex flex-1 flex-col pb-8">
                  <p class="text-[#0d1c12] dark:text-white text-base font-bold leading-normal">Mid-Trimester Examinations</p>
                  <p class="text-[#479e64] dark:text-gray-400 text-base font-normal leading-normal">October 21-25, 2024</p>
                </div>
                <div class="flex flex-col items-center gap-1">
                  <div class="w-[2px] bg-[#e6f4eb] dark:bg-gray-700 h-8"></div>
                  <div class="rounded-full bg-red-100 dark:bg-red-900/50 p-2 text-[#dc3545]"><span class="material-symbols-outlined !text-2xl">notification_important</span></div>
                  <div class="w-[2px] bg-[#e6f4eb] dark:bg-gray-700 grow"></div>
                </div>
                <div class="flex flex-1 flex-col pb-8">
                  <p class="text-[#0d1c12] dark:text-white text-base font-bold leading-normal">Final Fee Payment Deadline</p>
                  <p class="text-[#479e64] dark:text-gray-400 text-base font-normal leading-normal">November 15, 2024</p>
                </div>
                <div class="flex flex-col items-center gap-1">
                  <div class="w-[2px] bg-[#e6f4eb] dark:bg-gray-700 h-8"></div>
                  <div class="rounded-full bg-orange-100 dark:bg-orange-900/50 p-2 text-[#fd7e14]"><span class="material-symbols-outlined !text-2xl">edit_document</span></div>
                  <div class="w-[2px] bg-[#e6f4eb] dark:bg-gray-700 grow"></div>
                </div>
                <div class="flex flex-1 flex-col pb-8">
                  <p class="text-[#0d1c12] dark:text-white text-base font-bold leading-normal">End-of-Trimester Examinations</p>
                  <p class="text-[#479e64] dark:text-gray-400 text-base font-normal leading-normal">December 9-13, 2024</p>
                </div>
                <div class="flex flex-col items-center gap-1">
                  <div class="w-[2px] bg-[#e6f4eb] dark:bg-gray-700 h-8"></div>
                  <div class="rounded-full bg-teal-100 dark:bg-teal-900/50 p-2 text-[#20c997]"><span class="material-symbols-outlined !text-2xl">beach_access</span></div>
                </div>
                <div class="flex flex-1 flex-col">
                  <p class="text-[#0d1c12] dark:text-white text-base font-bold leading-normal">Trimester Break Begins</p>
                  <p class="text-[#479e64] dark:text-gray-400 text-base font-normal leading-normal">December 16, 2024</p>
                </div>
              </div>
            </div>
            <!-- Second Trimester -->
            <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-sm overflow-hidden border border-[#e6f4eb] dark:border-gray-700">
              <h2 class="text-[#0d1c12] dark:text-white text-2xl font-bold leading-tight tracking-[-0.015em] px-6 py-4">Second Trimester (January - April 2025)</h2>
            </div>
          </div>
          <!-- Sidebar / Key Dates -->
          <aside class="lg:col-span-1">
            <div class="sticky top-28 flex flex-col gap-6">
              <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-sm border border-[#e6f4eb] dark:border-gray-700 p-6">
                <h3 class="text-xl font-bold text-[#0d1c12] dark:text-white mb-4">Key Dates</h3>
                <div class="flex flex-col gap-4">
                  <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 mt-1 rounded-full bg-red-100 dark:bg-red-900/50 p-2 text-[#dc3545]">
                      <span class="material-symbols-outlined !text-xl">notification_important</span>
                    </div>
                    <div>
                      <p class="font-semibold text-[#0d1c12] dark:text-white">Final Fee Payment Deadline</p>
                      <p class="text-sm text-[#479e64] dark:text-gray-400">November 15, 2024</p>
                    </div>
                  </div>
                  <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 mt-1 rounded-full bg-orange-100 dark:bg-orange-900/50 p-2 text-[#fd7e14]">
                      <span class="material-symbols-outlined !text-xl">edit_document</span>
                    </div>
                    <div>
                      <p class="font-semibold text-[#0d1c12] dark:text-white">End-of-Trimester Examinations</p>
                      <p class="text-sm text-[#479e64] dark:text-gray-400">December 9-13, 2024</p>
                    </div>
                  </div>
                  <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 mt-1 rounded-full bg-teal-100 dark:bg-teal-900/50 p-2 text-[#20c997]">
                      <span class="material-symbols-outlined !text-xl">beach_access</span>
                    </div>
                    <div>
                      <p class="font-semibold text-[#0d1c12] dark:text-white">Trimester Break Begins</p>
                      <p class="text-sm text-[#479e64] dark:text-gray-400">December 16, 2024</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="bg-white dark:bg-gray-800/50 rounded-xl shadow-sm border border-[#e6f4eb] dark:border-gray-700 p-6">
                <h3 class="text-xl font-bold text-[#0d1c12] dark:text-white mb-4">Academic Year</h3>
                <select class="form-select w-full rounded-xl bg-[#e6f4eb] dark:bg-gray-800 border-none text-[#0d1c12] dark:text-gray-200 focus:ring-2 focus:ring-primary/50 h-11">
                  <option selected="">2024 / 2025</option>
                  <option>2023 / 2024</option>
                  <option>2022 / 2023</option>
                </select>
              </div>
            </div>
          </aside>
        </div>
      </div>
    </div>
  </main>
  <!-- Footer -->
  <footer class="bg-white dark:bg-[#08140b] border-t border-[#e6f4eb] dark:border-gray-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8">
        <div class="col-span-2 lg:col-span-1">
          <a class="flex items-center gap-3 text-[#0d1c12] dark:text-gray-100" href="#">
            <div class="text-[#0d1c12] dark:text-primary">
              <span class="material-symbols-outlined text-3xl">school</span>
            </div>
            <h2 class="text-lg font-bold leading-tight tracking-[-0.015em]">Valley View University</h2>
          </a>
          <p class="mt-4 text-sm text-[#479e64] dark:text-gray-400">Excellence, Integrity, Service.</p>
          <div class="flex mt-6 space-x-4">
            <a class="text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#"><svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewbox="0 0 24 24"><path clip-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" fill-rule="evenodd"></path></svg></a>
            <a class="text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#"><svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewbox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.71v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path></svg></a>
            <a class="text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#"><svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewbox="0 0 24 24"><path clip-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12.001c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.009-.868-.014-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.031-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.03 1.595 1.03 2.688 0 3.848-2.338 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.001 10.001 0 0022 12.001C22 6.477 17.523 2 12 2z" fill-rule="evenodd"></path></svg></a>
          </div>
        </div>
        <div>
          <h3 class="text-sm font-bold text-[#0d1c12] dark:text-white tracking-wider uppercase">Academics</h3>
          <ul class="mt-4 space-y-2">
            <li><a class="text-base text-[#479e64] dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Undergraduate</a></li>
            <li><a class="text-base text-[#479e64] dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Graduate</a></li>
            <li><a class="text-base text-[#479e64] dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Sandwich Programs</a></li>
            <li><a class="text-base text-[#479e64] dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Departments</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-sm font-bold text-[#0d1c12] dark:text-white tracking-wider uppercase">Admissions</h3>
          <ul class="mt-4 space-y-2">
            <li><a class="text-base text-[#479e64] dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">How to Apply</a></li>
            <li><a class="text-base text-[#479e64] dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Tuition &amp; Fees</a></li>
            <li><a class="text-base text-[#479e64] dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Visit Campus</a></li>
            <li><a class="text-base text-[#479e64] dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Contact</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-sm font-bold text-[#0d1c12] dark:text-white tracking-wider uppercase">Resources</h3>
          <ul class="mt-4 space-y-2">
            <li><a class="text-base text-[#479e64] dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Library</a></li>
            <li><a class="text-base text-[#479e64] dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Careers</a></li>
            <li><a class="text-base text-[#479e64] dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">News &amp; Events</a></li>
            <li><a class="text-base text-[#479e64] dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors" href="#">Faculty Directory</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-sm font-bold text-[#0d1c12] dark:text-white tracking-wider uppercase">Contact Us</h3>
          <ul class="mt-4 space-y-2 text-base text-[#479e64] dark:text-gray-400">
            <li>123 University Drive,<br/>Valley View, ST 12345</li>
            <li>(123) 456-7890</li>
            <li>info@vvu.edu</li>
          </ul>
        </div>
      </div>
      <div class="mt-8 border-t border-[#e6f4eb] dark:border-gray-800 pt-8 text-center text-sm text-[#479e64] dark:text-gray-500">
        <p>© 2024 Valley View University. All rights reserved.</p>
      </div>
    </div>
  </footer>
</div>

<?php
include 'includes/footer.php';
?>