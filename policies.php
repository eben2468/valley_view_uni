<?php
$pageTitle = "University Policies - Valley View University";
$activePage = "policies";
include 'includes/header.php';
?>

<div class="relative flex min-h-screen w-full flex-col group/design-root overflow-x-hidden">
  <header class="sticky top-0 z-50 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm border-b border-gray-200 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <div class="flex items-center gap-4 text-brand-navy dark:text-white">
          <div class="w-8 h-8 flex-shrink-0 text-brand-teal">
            <svg fill="currentColor" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
              <path clip-rule="evenodd" d="M24 4H42V17.3333V30.6667H24V44H6V30.6667V17.3333H24V4Z" fill-rule="evenodd"></path>
            </svg>
          </div>
          <h2 class="text-xl font-bold tracking-tight">Valley View University</h2>
        </div>
        <nav class="hidden md:flex items-center gap-8">
          <a class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-brand-teal dark:hover:text-primary" href="#">Academics</a>
          <a class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-brand-teal dark:hover:text-primary" href="#">Admissions</a>
          <a class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-brand-teal dark:hover:text-primary" href="#">Student Life</a>
          <a class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-brand-teal dark:hover:text-primary" href="#">Research</a>
          <a class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-brand-teal dark:hover:text-primary" href="#">About</a>
        </nav>
        <div class="flex items-center gap-2">
          <button class="flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-5 bg-brand-teal text-white text-sm font-bold tracking-wide hover:bg-brand-teal/90 transition-colors">
            <span>Apply</span>
          </button>
          <button class="flex cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 w-10 bg-gray-200/50 dark:bg-gray-700/50 text-brand-navy dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
            <span class="material-symbols-outlined">search</span>
          </button>
        </div>
      </div>
    </div>
  </header>
  <main class="flex-grow">
    <div class="@container">
      <div class="flex min-h-[480px] flex-col gap-6 bg-cover bg-center bg-no-repeat @[480px]:gap-8 items-center justify-center p-4" data-alt="University library interior with high ceilings and bookshelves, conveying a sense of knowledge and authority." style='background-image: linear-gradient(rgba(10, 35, 66, 0.85) 0%, rgba(10, 35, 66, 0.95) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuDlpqAxUpsNTDcRAQIlxSNJQ8SojHcCq-EJUtGi1fL4Ks81Fov4uUGjJrsaziEer_Gb2EzOGjNFYzIvSXn8BgUcJTOJ60Ln7ogU_UGxoqMGsnyt1wEkW1636dKPzO17EdOyoT7GZLZ7-VADxDD39JsJ31e3yOzPXyo_69Va5FW22seP0WfrtmjXil3J2I1YDq8D9rg2aEcx572kdiJMjcAlfXPO3bQ46H2PtAA2WpbTZN8cvvoWSPdLKzgJaKL0f6lY99R4t-07NQsh");'>
        <div class="flex flex-col gap-2 text-center">
          <h1 class="text-white text-4xl font-black leading-tight tracking-tight @[480px]:text-5xl">
            University Policies &amp; Procedures
          </h1>
          <h2 class="text-gray-200 text-sm font-normal leading-normal @[480px]:text-base max-w-2xl mx-auto">
            A comprehensive and searchable guide to the principles and regulations at Valley View University.
          </h2>
        </div>
        <label class="flex flex-col min-w-40 h-14 w-full max-w-[580px] @[480px]:h-16">
          <div class="flex w-full flex-1 items-stretch rounded-full h-full shadow-lg">
            <div class="text-gray-500 flex bg-white items-center justify-center pl-6 rounded-l-full border-r-0">
              <span class="material-symbols-outlined">search</span>
            </div>
            <input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-full text-brand-navy focus:outline-0 focus:ring-2 focus:ring-brand-gold/50 bg-white h-full placeholder:text-gray-500 px-4 border-none text-base font-normal leading-normal" placeholder="Search for a policy..." value=""/>
            <div class="flex items-center justify-center rounded-r-full bg-white pr-2">
              <button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-5 @[480px]:h-12 @[480px]:px-6 bg-brand-gold text-brand-navy text-sm font-bold tracking-wide @[480px]:text-base hover:bg-brand-gold/90 transition-colors">
                <span>Search</span>
              </button>
            </div>
          </div>
        </label>
      </div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <aside class="lg:col-span-1">
          <h3 class="text-lg font-bold text-brand-navy dark:text-white mb-4 px-4">Policy Categories</h3>
          <nav class="flex flex-col space-y-1">
            <a class="flex items-center px-4 py-2 text-sm font-medium text-white bg-brand-teal rounded-lg" href="#">Academic Policies</a>
            <a class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-brand-gray dark:hover:bg-gray-800 rounded-lg" href="#">Student Conduct &amp; Rights</a>
            <a class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-brand-gray dark:hover:bg-gray-800 rounded-lg" href="#">Administrative &amp; Financial</a>
            <a class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-brand-gray dark:hover:bg-gray-800 rounded-lg" href="#">Health &amp; Safety</a>
            <a class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-brand-gray dark:hover:bg-gray-800 rounded-lg" href="#">Information Technology</a>
            <a class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-brand-gray dark:hover:bg-gray-800 rounded-lg" href="#">Faculty &amp; Staff Handbook</a>
          </nav>
        </aside>
        <div class="lg:col-span-3">
          <section id="frequently-accessed">
            <h2 class="text-brand-navy dark:text-white text-2xl font-bold leading-tight tracking-tight mb-6">Frequently Accessed Policies</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="flex flex-col justify-between gap-4 rounded-xl bg-white dark:bg-gray-800/50 p-6 border border-gray-200 dark:border-gray-700/50 shadow-sm hover:shadow-lg transition-shadow">
                <div class="flex flex-col gap-2">
                  <p class="text-brand-navy dark:text-white text-lg font-bold">Academic Integrity Policy</p>
                  <p class="text-gray-600 dark:text-gray-300 text-sm">Outlines the standards for honest academic work and consequences for plagiarism.</p>
                </div>
                <button class="flex items-center justify-center gap-2 w-fit px-4 h-9 rounded-full bg-brand-gray dark:bg-gray-700 text-brand-navy dark:text-white text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                  <span class="material-symbols-outlined !text-[18px]">arrow_forward</span>
                  <span>Read More</span>
                </button>
              </div>
              <div class="flex flex-col justify-between gap-4 rounded-xl bg-white dark:bg-gray-800/50 p-6 border border-gray-200 dark:border-gray-700/50 shadow-sm hover:shadow-lg transition-shadow">
                <div class="flex flex-col gap-2">
                  <p class="text-brand-navy dark:text-white text-lg font-bold">Code of Student Conduct</p>
                  <p class="text-gray-600 dark:text-gray-300 text-sm">Defines the expectations for student behavior and the disciplinary process.</p>
                </div>
                <button class="flex items-center justify-center gap-2 w-fit px-4 h-9 rounded-full bg-brand-gray dark:bg-gray-700 text-brand-navy dark:text-white text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                  <span class="material-symbols-outlined !text-[18px]">arrow_forward</span>
                  <span>Read More</span>
                </button>
              </div>
            </div>
          </section>
          <div class="border-t border-gray-200 dark:border-gray-700 my-10"></div>
          <section id="all-policies">
            <h2 class="text-brand-navy dark:text-white text-2xl font-bold leading-tight tracking-tight mb-6">Academic Policies</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="flex flex-col justify-between gap-4 rounded-xl bg-white dark:bg-gray-800/50 p-6 border border-gray-200 dark:border-gray-700/50 shadow-sm hover:shadow-lg transition-shadow">
                <div class="flex flex-col gap-2">
                  <div class="flex items-center justify-between">
                    <p class="text-brand-navy dark:text-white text-lg font-bold">Grading Policies</p>
                    <div class="h-6 shrink-0 items-center justify-center gap-x-2 rounded-full bg-brand-teal/10 px-3 flex">
                      <p class="text-brand-teal text-xs font-medium">Academic</p>
                    </div>
                  </div>
                  <p class="text-gray-600 dark:text-gray-300 text-sm">Details on the university's grading system, including pass/fail options and GPA calculation.</p>
                </div>
                <button class="flex items-center justify-center gap-2 w-fit px-4 h-9 rounded-full bg-brand-gray dark:bg-gray-700 text-brand-navy dark:text-white text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                  <span class="material-symbols-outlined !text-[18px]">download</span>
                  <span>View Details</span>
                </button>
              </div>
              <div class="flex flex-col justify-between gap-4 rounded-xl bg-white dark:bg-gray-800/50 p-6 border border-gray-200 dark:border-gray-700/50 shadow-sm hover:shadow-lg transition-shadow">
                <div class="flex flex-col gap-2">
                  <div class="flex items-center justify-between">
                    <p class="text-brand-navy dark:text-white text-lg font-bold">Course Registration</p>
                    <div class="h-6 shrink-0 items-center justify-center gap-x-2 rounded-full bg-brand-teal/10 px-3 flex">
                      <p class="text-brand-teal text-xs font-medium">Academic</p>
                    </div>
                  </div>
                  <p class="text-gray-600 dark:text-gray-300 text-sm">Procedures for adding, dropping, and withdrawing from courses each semester.</p>
                </div>
                <button class="flex items-center justify-center gap-2 w-fit px-4 h-9 rounded-full bg-brand-gray dark:bg-gray-700 text-brand-navy dark:text-white text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                  <span class="material-symbols-outlined !text-[18px]">download</span>
                  <span>View Details</span>
                </button>
              </div>
              <div class="flex flex-col justify-between gap-4 rounded-xl bg-white dark:bg-gray-800/50 p-6 border border-gray-200 dark:border-gray-700/50 shadow-sm hover:shadow-lg transition-shadow">
                <div class="flex flex-col gap-2">
                  <div class="flex items-center justify-between">
                    <p class="text-brand-navy dark:text-white text-lg font-bold">Degree Requirements</p>
                    <div class="h-6 shrink-0 items-center justify-center gap-x-2 rounded-full bg-brand-teal/10 px-3 flex">
                      <p class="text-brand-teal text-xs font-medium">Academic</p>
                    </div>
                  </div>
                  <p class="text-gray-600 dark:text-gray-300 text-sm">Information on credit requirements, major/minor declarations, and graduation criteria.</p>
                </div>
                <button class="flex items-center justify-center gap-2 w-fit px-4 h-9 rounded-full bg-brand-gray dark:bg-gray-700 text-brand-navy dark:text-white text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                  <span class="material-symbols-outlined !text-[18px]">download</span>
                  <span>View Details</span>
                </button>
              </div>
              <div class="flex flex-col justify-between gap-4 rounded-xl bg-white dark:bg-gray-800/50 p-6 border border-gray-200 dark:border-gray-700/50 shadow-sm hover:shadow-lg transition-shadow">
                <div class="flex flex-col gap-2">
                  <div class="flex items-center justify-between">
                    <p class="text-brand-navy dark:text-white text-lg font-bold">Transfer Credit Policy</p>
                    <div class="h-6 shrink-0 items-center justify-center gap-x-2 rounded-full bg-brand-teal/10 px-3 flex">
                      <p class="text-brand-teal text-xs font-medium">Academic</p>
                    </div>
                  </div>
                  <p class="text-gray-600 dark:text-gray-300 text-sm">Guidelines for the acceptance of academic credits from other institutions.</p>
                </div>
                <button class="flex items-center justify-center gap-2 w-fit px-4 h-9 rounded-full bg-brand-gray dark:bg-gray-700 text-brand-navy dark:text-white text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                  <span class="material-symbols-outlined !text-[18px]">download</span>
                  <span>View Details</span>
                </button>
              </div>
            </div>
          </section>
        </div>
      </div>
    </div>
  </main>
  <footer class="bg-brand-navy text-white">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
        <div class="space-y-4">
          <h4 class="font-bold tracking-wider uppercase">Contact</h4>
          <ul class="space-y-2 text-sm text-gray-300">
            <li>123 University Drive</li>
            <li>Valley View, ST 12345</li>
            <li>(123) 456-7890</li>
            <li>contact@vvu.edu</li>
          </ul>
        </div>
        <div class="space-y-4">
          <h4 class="font-bold tracking-wider uppercase">Quick Links</h4>
          <ul class="space-y-2 text-sm">
            <li><a class="text-gray-300 hover:text-brand-gold" href="#">Campus Map</a></li>
            <li><a class="text-gray-300 hover:text-brand-gold" href="#">Directory</a></li>
            <li><a class="text-gray-300 hover:text-brand-gold" href="#">Library</a></li>
            <li><a class="text-gray-300 hover:text-brand-gold" href="#">Bookstore</a></li>
          </ul>
        </div>
        <div class="space-y-4">
          <h4 class="font-bold tracking-wider uppercase">Resources</h4>
          <ul class="space-y-2 text-sm">
            <li><a class="text-gray-300 hover:text-brand-gold" href="#">Accessibility Statement</a></li>
            <li><a class="text-gray-300 hover:text-brand-gold" href="#">Privacy Policy</a></li>
            <li><a class="text-gray-300 hover:text-brand-gold" href="#">For The Media</a></li>
            <li><a class="text-gray-300 hover:text-brand-gold" href="#">Careers</a></li>
          </ul>
        </div>
        <div class="space-y-4">
          <h4 class="font-bold tracking-wider uppercase">Connect</h4>
          <div class="flex space-x-4">
          </div>
        </div>
      </div>
      <div class="mt-8 pt-8 border-t border-gray-700 text-center text-sm text-gray-400">
        © 2024 Valley View University. All Rights Reserved.
      </div>
    </div>
  </footer>
</div>

<?php
include 'includes/footer.php';
?>