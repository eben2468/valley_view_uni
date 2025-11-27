<?php
$pageTitle = "Parents - Valley View University";
$activePage = "parents";
include 'includes/header.php';
?>

<div class="relative flex h-auto min-h-screen w-full flex-col bg-background-light dark:bg-background-dark group/design-root overflow-x-hidden">
  <div class="layout-container flex h-full grow flex-col">
    <div class="flex flex-1 justify-center">
      <div class="layout-content-container flex flex-col max-w-[1280px] flex-1">
        <!-- TopNavBar -->
        <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#e7e7f3] dark:border-b-background-dark px-10 py-3">
          <div class="flex items-center gap-8">
            <div class="flex items-center gap-4 text-[#0d0d1b] dark:text-white">
              <div class="size-6 text-primary">
                <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="M24 4H42V17.3333V30.6667H24V44H6V30.6667V17.3333H24V4Z" fill="currentColor" fill-rule="evenodd"></path></svg>
              </div>
              <h2 class="text-lg font-bold leading-tight tracking-[-0.015em]">Valley View University</h2>
            </div>
            <nav class="hidden lg:flex items-center gap-9">
              <a class="text-[#0d0d1b] dark:text-gray-300 text-sm font-medium leading-normal hover:text-primary dark:hover:text-white" href="#">Admissions</a>
              <a class="text-[#0d0d1b] dark:text-gray-300 text-sm font-medium leading-normal hover:text-primary dark:hover:text-white" href="#">Academics</a>
              <a class="text-[#0d0d1b] dark:text-gray-300 text-sm font-medium leading-normal hover:text-primary dark:hover:text-white" href="#">Student Life</a>
              <a class="text-[#0d0d1b] dark:text-gray-300 text-sm font-medium leading-normal hover:text-primary dark:hover:text-white" href="#">Research</a>
              <a class="text-primary dark:text-white text-sm font-bold leading-normal" href="#">Resources</a>
            </nav>
          </div>
          <div class="flex flex-1 justify-end gap-2">
            <label class="hidden md:flex flex-col min-w-40 !h-10 max-w-64">
              <div class="flex w-full flex-1 items-stretch rounded-lg h-full">
                <div class="text-[#4c4c9a] dark:text-gray-400 flex border-none bg-[#e7e7f3] dark:bg-background-dark items-center justify-center pl-4 rounded-l-lg border-r-0">
                  <span class="material-symbols-outlined text-xl">search</span>
                </div>
                <input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#0d0d1b] dark:text-white focus:outline-0 focus:ring-0 border-none bg-[#e7e7f3] dark:bg-background-dark focus:border-none h-full placeholder:text-[#4c4c9a] dark:placeholder:text-gray-400 px-4 rounded-l-none border-l-0 pl-2 text-base font-normal leading-normal" placeholder="Search" value=""/>
              </div>
            </label>
            <button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em]"><span class="truncate">Apply</span></button>
            <button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-[#e7e7f3] dark:bg-background-dark text-[#0d0d1b] dark:text-white text-sm font-bold leading-normal tracking-[0.015em]"><span class="truncate">Visit</span></button>
          </div>
        </header>
        <main>
          <!-- HeroSection -->
          <div class="@container px-4 sm:px-10 py-5">
            <div class="@[480px]:p-4">
              <div class="flex min-h-[480px] flex-col gap-6 bg-cover bg-center bg-no-repeat @[480px]:gap-8 @[480px]:rounded-xl items-start justify-end px-4 pb-10 @[480px]:px-10" data-alt="Warm, authentic photo of students and parents smiling on the Valley View University campus green" style='background-image: linear-gradient(rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.5) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuD2UitZ24QKM5rEf6IFgLAQKqW76fuY9Fg2V6fOTVzHVcw7ESCL9w5h5Yy-PKOQI3VAVNmP_Vg66gnBVRgPzp5vZSHa1RRWTHdeR4Qc4OX_4A981UbZT9uc01NdHPCxh7Pem8Fb9RmnqAiqYshnYlWIymgBnIUxnCugIfqD3OntV0ptuacacEaJ5W2nYohxsGHvGb81dSwTITIgIZE_WJKG7cpVIdcgjeNykpHDvWvu7wvX4U6jnCy0BEWC-I4Cj6QufGXC76XXxAKe");'>
                <div class="flex flex-col gap-2 text-left">
                  <h1 class="text-white text-4xl font-black leading-tight tracking-[-0.033em] @[480px]:text-5xl @[480px]:font-black @[480px]:leading-tight @[480px]:tracking-[-0.033em]">Welcome, Valley View Parents!</h1>
                  <h2 class="text-white text-sm font-normal leading-normal @[480px]:text-base @[480px]:font-normal @[480px]:leading-normal max-w-2xl">We believe in a strong partnership with our students' families. Discover the resources and community available to you.</h2>
                </div>
                <button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 @[480px]:h-12 @[480px]:px-5 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] @[480px]:text-base @[480px]:font-bold @[480px]:leading-normal @[480px]:tracking-[0.015em]"><span class="truncate">Schedule a Tour</span></button>
              </div>
            </div>
          </div>
          <!-- TextGrid (Quick Links) -->
          <div class="grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-4 px-4 sm:px-14">
            <div class="flex flex-1 gap-3 rounded-lg border border-[#cfcfe7] dark:border-gray-700 bg-background-light dark:bg-background-dark p-4 flex-col">
              <div class="text-primary"><span class="material-symbols-outlined">payments</span></div>
              <div class="flex flex-col gap-1">
                <h2 class="text-[#0d0d1b] dark:text-white text-base font-bold leading-tight">Financial Aid</h2>
                <p class="text-[#4c4c9a] dark:text-gray-400 text-sm font-normal leading-normal">Explore options</p>
              </div>
            </div>
            <div class="flex flex-1 gap-3 rounded-lg border border-[#cfcfe7] dark:border-gray-700 bg-background-light dark:bg-background-dark p-4 flex-col">
              <div class="text-primary"><span class="material-symbols-outlined">calendar_month</span></div>
              <div class="flex flex-col gap-1">
                <h2 class="text-[#0d0d1b] dark:text-white text-base font-bold leading-tight">Academic Calendar</h2>
                <p class="text-[#4c4c9a] dark:text-gray-400 text-sm font-normal leading-normal">Important dates</p>
              </div>
            </div>
            <div class="flex flex-1 gap-3 rounded-lg border border-[#cfcfe7] dark:border-gray-700 bg-background-light dark:bg-background-dark p-4 flex-col">
              <div class="text-primary"><span class="material-symbols-outlined">account_circle</span></div>
              <div class="flex flex-col gap-1">
                <h2 class="text-[#0d0d1b] dark:text-white text-base font-bold leading-tight">Parent Portal</h2>
                <p class="text-[#4c4c9a] dark:text-gray-400 text-sm font-normal leading-normal">Stay connected</p>
              </div>
            </div>
          </div>
          <!-- ImageGrid (Resource Categories) -->
          <div class="grid grid-cols-[repeat(auto-fit,minmax(250px,1fr))] gap-6 p-4 sm:p-14">
            <div class="flex flex-col gap-3 pb-3">
              <div class="w-full bg-center bg-no-repeat aspect-video bg-cover rounded-lg" data-alt="Students relaxing and studying in a modern university dormitory common area" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBzW7kid9Ot-nU5SAecuiykBGXEhzDSRavHnxSQPD34AQXk8zjVYSNi0HJXFEHgv_zJroQ4mNoU-mSqjoPwlXfn2oY50khpw-fYremwOYytzcTORkwHeCA2pvi0FVyOq80Cj86jxOPN2Gl2eANY6VaZrIDEZFcSXIvbLvDPQ2O5vlcV9EkW66qHSzkuke3HsLk9hzBn4oDeVF86vcwFIDjgsXaHBQWSUcme7byQSV7iog_bdS61VkkeXNiJrM_TSP2HWDZKlc1sGdK3");'></div>
              <div>
                <p class="text-[#0d0d1b] dark:text-white text-base font-medium leading-normal">Student Life &amp; Housing</p>
                <p class="text-[#4c4c9a] dark:text-gray-400 text-sm font-normal leading-normal">Information on dorms, dining, and extracurricular activities.</p>
              </div>
            </div>
            <div class="flex flex-col gap-3 pb-3">
              <div class="w-full bg-center bg-no-repeat aspect-video bg-cover rounded-lg" data-alt="A professor advising a student in a bright, modern office" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCWXbj7lMKlKuVYDGYSD6UpaGJSXhvLU3bj3iPLvzeukEOUJkjZ4JWvl5AJ_HEOxEGXJ7s4stJUnxfaoHDBK8yyq86AJatJh2jVmJPLSFoUHHLxM8mfcXFybWP8Ct3Cgy6BkN3hc1P9YkcqYDmCEPuCReuxWuhLuifJAIgEdcJOcnOmTh8Jvnrkjk7nPtYFI0NZ-rJDeCSDU_aNDcxMkOvf0m8i3wP45BE99Y-nHj6e0zfPbtoPpAxHhHf3s7jnLqX_O5r8UzWnDw5N");'></div>
              <div>
                <p class="text-[#0d0d1b] dark:text-white text-base font-medium leading-normal">Academic Support</p>
                <p class="text-[#4c4c9a] dark:text-gray-400 text-sm font-normal leading-normal">Details on tutoring, advising, and student success centers.</p>
              </div>
            </div>
            <div class="flex flex-col gap-3 pb-3">
              <div class="w-full bg-center bg-no-repeat aspect-video bg-cover rounded-lg" data-alt="Campus security officer speaking with a student in a friendly manner on campus" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCJQBsTHWaZEVwLRu2nfl4J-FpYuiAnsEeUVLzLdJ5XGHUGQy0QlnJaCmBvi-MAn-krAzHSfwO7yNVm2yrcz4Ae3mjACr80Kw-e3ylxBrrQ3VOnwAP-fcJ4TUb-p2kTVuzefiXaN-zd6drW3IeflbJYlqpmKSyez1zqXNtLbZ0YVTpZsUsFG6Ge3p-KE-6FpbLH0OcVjN6KddEsLyTFBCYT0RWwvrYaZ2dn4k_1mKDqUOH_nb7mx8fSGGW0xlwYELNP44REScJcDJmP");'></div>
              <div>
                <p class="text-[#0d0d1b] dark:text-white text-base font-medium leading-normal">Health &amp; Safety</p>
                <p class="text-[#4c4c9a] dark:text-gray-400 text-sm font-normal leading-normal">Overview of campus security, health services, and wellness programs.</p>
              </div>
            </div>
            <div class="flex flex-col gap-3 pb-3">
              <div class="w-full bg-center bg-no-repeat aspect-video bg-cover rounded-lg" data-alt="View of the university's financial aid office with staff assisting students" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuD_gya7EMb0u6LtfT-ezXLe_RUggrqpGROUnYkEYWlP8IGEx-FdNsMHFe0EJTt1RDwWHO07ddSu77dwb8F0mnU8616su8Fmi_mESVMLOMEcDMnAKENQXm0iv7FC59ohzs-bWYkDZOEJpcjO9rQGmY5JGivGlbv9Z2GYBCAXWIaNIANKnQ4jBL296aKsa4aYprYixDRh344e-8irqln2-R2eLb42Uyhs02kuXdaZDQCTCpBWlVuKC08Gq2zKAo4D6e7oo4_YP-c2MGQr");'></div>
              <div>
                <p class="text-[#0d0d1b] dark:text-white text-base font-medium leading-normal">Financial Information</p>
                <p class="text-[#4c4c9a] dark:text-gray-400 text-sm font-normal leading-normal">Links to tuition details, financial aid, and scholarships.</p>
              </div>
            </div>
            <div class="flex flex-col gap-3 pb-3">
              <div class="w-full bg-center bg-no-repeat aspect-video bg-cover rounded-lg" data-alt="Parents attending a university event on campus, smiling and engaged" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDS4DJ7nDDw9Ddi4JgRCOXsuAVjFBTmExHQYbFSWxjeiRnMpa1OnOxdl9TR2YxL38vD-GUszv3x5Eh8AKfbURQ16Q_WNHm9LNxFvVhKJ3L0WFWQ3lHivm6rnoQniGnlAJiJXHKGvcR643_RgdB-vjgupsXCju6D2jXAgHlasbOFhEE_CtOqaJCHnKC1vEbYZ9BslJEaz11xiWFpztRRUmr7DDnXSoHkuT6YSKhcKlkopzzOMuo3Hni9oGutMgDBugEllUPePNKgRK1j");'></div>
              <div>
                <p class="text-[#0d0d1b] dark:text-white text-base font-medium leading-normal">Get Involved</p>
                <p class="text-[#4c4c9a] dark:text-gray-400 text-sm font-normal leading-normal">Information on parent events and volunteer opportunities.</p>
              </div>
            </div>
            <div class="flex flex-col gap-3 pb-3">
              <div class="w-full bg-center bg-no-repeat aspect-video bg-cover rounded-lg" data-alt="A family taking a guided tour of the beautiful university campus" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCzGbE7V7cbpZ95LIVTvoHeVjdVZRBD_7GW_j0Kw-RcWD0G6vdKiYoT4vuLzTqvYIZ2PKOb6rrhM9HZ7HtiyodhzLzVspblXTas1yBUTKRi6UgHMyNIdow49KHWxC9_TGqY9rTlQIDnuqZmEADF7B3DjIrbVKrmFY1HP5AK4NLgjiYCKrqlr6_YoedSj8YXmkEazdUy77bqIw5t_75oJeZdjuDggKJ4OuvUrgNpW_fzyGBjF31d5T_5GqbDVOhn6xQMqzaRwjv_wNy6");'></div>
              <div>
                <p class="text-[#0d0d1b] dark:text-white text-base font-medium leading-normal">Visiting Campus</p>
                <p class="text-[#4c4c9a] dark:text-gray-400 text-sm font-normal leading-normal">Details on scheduling tours, campus maps, and local accommodations.</p>
              </div>
            </div>
          </div>
          <!-- FAQ and Testimonial Section -->
          <div class="px-4 sm:px-14 py-10 grid grid-cols-1 lg:grid-cols-5 gap-12">
            <!-- FAQ Accordion -->
            <div class="lg:col-span-3">
              <h2 class="text-[#0d0d1b] dark:text-white text-2xl font-bold leading-tight tracking-[-0.015em] pb-5">Frequently Asked Questions</h2>
              <div class="flex flex-col gap-2">
                <details class="group rounded-lg bg-white dark:bg-gray-800/50 p-4 border border-transparent dark:border-gray-700 hover:border-primary/20 dark:hover:border-primary/50" open="">
                  <summary class="flex cursor-pointer items-center justify-between">
                    <h3 class="text-base font-bold text-[#0d0d1b] dark:text-white">How can I support my student from afar?</h3>
                    <span class="material-symbols-outlined text-primary transition-transform duration-300 group-open:rotate-180">expand_more</span>
                  </summary>
                  <p class="mt-3 text-sm text-[#4c4c9a] dark:text-gray-400">Encourage regular communication, send care packages, and stay informed through the Parent Portal. Remind them about available campus resources like tutoring and counseling services. Trust them to navigate their new independence, but let them know you're there for support.</p>
                </details>
                <details class="group rounded-lg bg-white dark:bg-gray-800/50 p-4 border border-transparent dark:border-gray-700 hover:border-primary/20 dark:hover:border-primary/50">
                  <summary class="flex cursor-pointer items-center justify-between">
                    <h3 class="text-base font-bold text-[#0d0d1b] dark:text-white">What safety measures are in place on campus?</h3>
                    <span class="material-symbols-outlined text-primary transition-transform duration-300 group-open:rotate-180">expand_more</span>
                  </summary>
                  <p class="mt-3 text-sm text-[#4c4c9a] dark:text-gray-400">Our campus is monitored 24/7 by a dedicated security team. We have emergency blue light phones throughout the campus, a safe-walk program, and secure access to all residential halls. We conduct regular safety drills and provide ongoing education to students about personal safety.</p>
                </details>
                <details class="group rounded-lg bg-white dark:bg-gray-800/50 p-4 border border-transparent dark:border-gray-700 hover:border-primary/20 dark:hover:border-primary/50">
                  <summary class="flex cursor-pointer items-center justify-between">
                    <h3 class="text-base font-bold text-[#0d0d1b] dark:text-white">How do I access the Parent Portal?</h3>
                    <span class="material-symbols-outlined text-primary transition-transform duration-300 group-open:rotate-180">expand_more</span>
                  </summary>
                  <p class="mt-3 text-sm text-[#4c4c9a] dark:text-gray-400">You will receive an invitation to set up your Parent Portal account once your student has enrolled and granted you access. This portal provides access to grades, financial information, and campus announcements, based on your student's privacy settings.</p>
                </details>
              </div>
            </div>
            <!-- Testimonial Block -->
            <div class="lg:col-span-2">
              <div class="bg-primary/10 dark:bg-primary/20 p-8 rounded-xl h-full flex flex-col justify-center">
                <p class="text-lg italic text-[#0d0d1b] dark:text-white">"Sending our daughter to Valley View was the best decision we could have made. The support from faculty and the sense of community are outstanding. We feel connected and informed, even from hundreds of miles away."</p>
                <div class="mt-6 flex items-center gap-4">
                  <img alt="Photo of Johnathan Doe" class="h-14 w-14 rounded-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDzpg-Ee9cCAX2UuO94VdVb7-1aW6KE9eU-stOvmZVWrkirtoHRb1TX5viWhgRrK98mX_philYVM_03EUx0pPwANoAFhCISK2k2AyRXJz1e8hgES6DwO8mmBO-zptQ4DpUGseL5EXmcJP4tTZTvZaMmHACmYjqBxJostqYGW507bM-iZr9jbObfswilekVQnZtfpfgTgsWnEomgF_dUj6ISkWiAatOf8e3UA4l5MuUDrhDy9PQZydE7W1967ljDw5qw5tHkcIki9sW0"/>
                  <div>
                    <p class="font-bold text-[#0d0d1b] dark:text-white">Johnathan Doe</p>
                    <p class="text-sm text-[#4c4c9a] dark:text-gray-300">Parent of a '25 Graduate</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Newsletter Signup -->
          <div class="bg-white dark:bg-background-dark py-12 px-4 sm:px-14">
            <div class="bg-primary/5 dark:bg-gray-800/50 rounded-xl p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-8">
              <div class="max-w-xl text-center md:text-left">
                <h2 class="text-[#0d0d1b] dark:text-white text-2xl font-bold leading-tight tracking-[-0.015em]">Stay Connected</h2>
                <p class="mt-2 text-[#4c4c9a] dark:text-gray-400">Sign up for our parent newsletter to receive campus news, event invitations, and important updates directly in your inbox.</p>
              </div>
              <form class="flex w-full max-w-md flex-col sm:flex-row gap-3">
                <input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#0d0d1b] dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border-gray-300 dark:border-gray-600 bg-white dark:bg-background-dark h-12 placeholder:text-[#4c4c9a] dark:placeholder:text-gray-400 px-4 text-base font-normal leading-normal" placeholder="Enter your email" type="email"/>
                <button class="flex min-w-[120px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em]"><span class="truncate">Subscribe</span></button>
              </form>
            </div>
          </div>
        </main>
        <!-- Footer -->
        <footer class="bg-gray-100 dark:bg-background-dark/50 mt-10">
          <div class="mx-auto max-w-screen-xl px-4 sm:px-14 py-12">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
              <div class="lg:col-span-1">
                <div class="flex items-center gap-4 text-[#0d0d1b] dark:text-white">
                  <div class="size-6 text-primary">
                    <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="M24 4H42V17.3333V30.6667H24V44H6V30.6667V17.3333H24V4Z" fill="currentColor" fill-rule="evenodd"></path></svg>
                  </div>
                  <h2 class="text-lg font-bold">Valley View University</h2>
                </div>
                <p class="mt-4 text-sm text-[#4c4c9a] dark:text-gray-400">Fostering knowledge and community for a brighter future.</p>
              </div>
              <div class="grid grid-cols-2 gap-8 sm:grid-cols-3 lg:col-span-3">
                <div>
                  <p class="font-bold text-[#0d0d1b] dark:text-white">Quick Links</p>
                  <ul class="mt-4 space-y-2 text-sm">
                    <li><a class="text-[#4c4c9a] dark:text-gray-400 transition hover:opacity-75" href="#">About Us</a></li>
                    <li><a class="text-[#4c4c9a] dark:text-gray-400 transition hover:opacity-75" href="#">Contact</a></li>
                    <li><a class="text-[#4c4c9a] dark:text-gray-400 transition hover:opacity-75" href="#">Careers</a></li>
                    <li><a class="text-[#4c4c9a] dark:text-gray-400 transition hover:opacity-75" href="#">Campus Map</a></li>
                  </ul>
                </div>
                <div>
                  <p class="font-bold text-[#0d0d1b] dark:text-white">Resources</p>
                  <ul class="mt-4 space-y-2 text-sm">
                    <li><a class="text-[#4c4c9a] dark:text-gray-400 transition hover:opacity-75" href="#">Library</a></li>
                    <li><a class="text-[#4c4c9a] dark:text-gray-400 transition hover:opacity-75" href="#">Bookstore</a></li>
                    <li><a class="text-[#4c4c9a] dark:text-gray-400 transition hover:opacity-75" href="#">IT Support</a></li>
                    <li><a class="text-[#4c4c9a] dark:text-gray-400 transition hover:opacity-75" href="#">Alumni</a></li>
                  </ul>
                </div>
                <div>
                  <p class="font-bold text-[#0d0d1b] dark:text-white">Legal</p>
                  <ul class="mt-4 space-y-2 text-sm">
                    <li><a class="text-[#4c4c9a] dark:text-gray-400 transition hover:opacity-75" href="#">Privacy Policy</a></li>
                    <li><a class="text-[#4c4c9a] dark:text-gray-400 transition hover:opacity-75" href="#">Terms of Service</a></li>
                    <li><a class="text-[#4c4c9a] dark:text-gray-400 transition hover:opacity-75" href="#">Accessibility</a></li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="mt-8 border-t border-[#cfcfe7] dark:border-gray-700 pt-8 flex flex-col sm:flex-row justify-between items-center">
              <p class="text-sm text-[#4c4c9a] dark:text-gray-400">© 2024 Valley View University. All rights reserved.</p>
            </div>
          </div>
        </footer>
      </div>
    </div>
  </div>
</div>

<?php
include 'includes/footer.php';
?>