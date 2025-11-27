<?php
$pageTitle = "Research Opportunities | Valley View University";
$activePage = "research_opportunities";
include 'includes/header.php';
?>

<div class="relative flex h-auto min-h-screen w-full flex-col bg-background-light dark:bg-background-dark group/design-root overflow-x-hidden">
  <div class="layout-container flex h-full grow flex-col">
    <div class="flex flex-1 justify-center py-5">
      <div class="layout-content-container flex flex-col w-full max-w-[1280px] flex-1 px-4 md:px-10">
        <!-- TopNavBar -->
        <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-neutral-light-gray dark:border-neutral-dark-gray/20 bg-neutral-white dark:bg-background-dark px-6 py-3 rounded-lg sticky top-5 z-50 shadow-sm">
          <div class="flex items-center gap-4 text-primary">
            <div class="size-6">
              <svg fill="currentColor" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                <path clip-rule="evenodd" d="M24 4H42V17.3333V30.6667H24V44H6V30.6667V17.3333H24V4Z" fill="currentColor" fill-rule="evenodd"></path>
              </svg>
            </div>
            <h2 class="text-lg font-bold leading-tight tracking-[-0.015em] text-primary dark:text-neutral-white">Valley View University</h2>
          </div>
          <div class="hidden lg:flex flex-1 justify-end gap-8">
            <div class="flex items-center gap-9">
              <a class="text-sm font-medium leading-normal text-neutral-dark-gray dark:text-neutral-light-gray hover:text-primary dark:hover:text-secondary" href="#">Admissions</a>
              <a class="text-sm font-medium leading-normal text-neutral-dark-gray dark:text-neutral-light-gray hover:text-primary dark:hover:text-secondary" href="#">Academics</a>
              <a class="text-sm font-medium leading-normal text-primary dark:text-secondary" href="#">Research</a>
              <a class="text-sm font-medium leading-normal text-neutral-dark-gray dark:text-neutral-light-gray hover:text-primary dark:hover:text-secondary" href="#">Campus Life</a>
              <a class="text-sm font-medium leading-normal text-neutral-dark-gray dark:text-neutral-light-gray hover:text-primary dark:hover:text-secondary" href="#">About</a>
            </div>
            <div class="flex gap-2">
              <button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-neutral-white text-sm font-bold leading-normal tracking-[0.015em] hover:opacity-90">
                <span class="truncate">Apply Now</span>
              </button>
              <button class="flex max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 bg-primary/10 dark:bg-primary/20 text-primary dark:text-neutral-light-gray gap-2 text-sm font-bold leading-normal tracking-[0.015em] min-w-0 px-2.5 hover:bg-primary/20 dark:hover:bg-primary/30">
                <span class="material-symbols-outlined text-lg">search</span>
              </button>
            </div>
          </div>
          <button class="lg:hidden flex max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 bg-primary/10 dark:bg-primary/20 text-primary dark:text-neutral-light-gray gap-2 text-sm font-bold leading-normal tracking-[0.015em] min-w-0 px-2.5">
            <span class="material-symbols-outlined text-2xl">menu</span>
          </button>
        </header>
        <main class="flex flex-col gap-12 md:gap-20 mt-5">
          <!-- HeroSection -->
          <div class="@container">
            <div class="@[480px]:p-4">
              <div class="flex min-h-[60vh] flex-col gap-6 bg-cover bg-center bg-no-repeat @[480px]:gap-8 @[480px]:rounded-xl items-center justify-center p-4 text-center" data-alt="Students and faculty collaborating in a modern university science laboratory." style='background-image: linear-gradient(rgba(0, 0, 0, 0.4) 0%, rgba(0, 51, 102, 0.6) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuCadKP2k3_PKGkKNqhnVODxvpR3NRaebh1UXVFAsy0jt6PDkInRSb2F-rtAgtuqJl_m9-w2bj5Z72FzcZQ8yKKwyqhjQiCMHeWvxMQ3NRMCpf7I1ZF-dVAVqj2qsDkrp7OMP53TmHYOod9j1ZenK_mPJFKu3bqe74G4SY3gTn8LsNnvpUdlacNL1j7bhjCcT9AsYhj55q3Ysv64sfaKoVn74dibMUMy2n-O-zOPtO2_4rx7Sgje0MrUKXCeUHGXjaAJbtQjOVCKWUlm");'>
                <div class="flex flex-col gap-4 max-w-3xl">
                  <h1 class="text-neutral-white text-4xl font-black leading-tight tracking-[-0.033em] @[480px]:text-6xl @[480px]:font-black @[480px]:leading-tight @[480px]:tracking-[-0.033em]">Shape the Future. Start Your Research Journey at Valley View.</h1>
                  <h2 class="text-neutral-light-gray text-base font-normal leading-normal @[480px]:text-lg @[480px]:font-normal @[480px]:leading-normal">Explore groundbreaking projects and collaborate with leading innovators to make your mark on the world.</h2>
                </div>
                <button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-secondary text-primary text-base font-bold leading-normal tracking-[0.015em] hover:scale-105 transition-transform">
                  <span class="truncate">Explore Research Areas</span>
                </button>
              </div>
            </div>
          </div>
          <!-- Explore Our Research Section -->
          <section>
            <h2 class="text-primary dark:text-neutral-white text-3xl font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5 text-center">Explore Our Research</h2>
            <div class="flex gap-3 p-3 flex-wrap items-center justify-center">
              <div class="flex h-10 shrink-0 cursor-pointer items-center justify-center gap-x-2 rounded-full bg-primary text-neutral-white px-5">
                <p class="text-sm font-medium leading-normal">All Projects</p>
              </div>
              <div class="flex h-10 shrink-0 cursor-pointer items-center justify-center gap-x-2 rounded-full bg-neutral-white dark:bg-primary/20 px-5 hover:bg-primary/10 dark:hover:bg-primary/30">
                <p class="text-neutral-dark-gray dark:text-neutral-light-gray text-sm font-medium leading-normal">Engineering</p>
              </div>
              <div class="flex h-10 shrink-0 cursor-pointer items-center justify-center gap-x-2 rounded-full bg-neutral-white dark:bg-primary/20 px-5 hover:bg-primary/10 dark:hover:bg-primary/30">
                <p class="text-neutral-dark-gray dark:text-neutral-light-gray text-sm font-medium leading-normal">Health Sciences</p>
              </div>
              <div class="flex h-10 shrink-0 cursor-pointer items-center justify-center gap-x-2 rounded-full bg-neutral-white dark:bg-primary/20 px-5 hover:bg-primary/10 dark:hover:bg-primary/30">
                <p class="text-neutral-dark-gray dark:text-neutral-light-gray text-sm font-medium leading-normal">Arts &amp; Humanities</p>
              </div>
              <div class="flex h-10 shrink-0 cursor-pointer items-center justify-center gap-x-2 rounded-full bg-neutral-white dark:bg-primary/20 px-5 hover:bg-primary/10 dark:hover:bg-primary/30">
                <p class="text-neutral-dark-gray dark:text-neutral-light-gray text-sm font-medium leading-normal">Physics</p>
              </div>
            </div>
            <div class="grid grid-cols-[repeat(auto-fit,minmax(280px,1fr))] gap-6 p-4">
              <div class="flex flex-col gap-3 pb-3 bg-neutral-white dark:bg-primary/10 rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 group">
                <div class="w-full bg-center bg-no-repeat aspect-video bg-cover group-hover:scale-105 transition-transform duration-300" data-alt="Close up shot of a complex robotic arm in a brightly lit lab." style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuB-fX8x_OIdtVASysBBnQZ0z_fDkXnqKOB7ci2b7ZtUAtm0jvDl7cunT08xENUPMBnAmgop24V7kRP0hXSe3zqG7EqnFzflC7OyI5pucjdcKgnzElrpCNg-HeuiHOAMbZd5uhPfka4TFE7Qk9HyeXfJg3BnrlnhIAPfsor85sdO9n8loeQISBPOOLILIO4sYqDozl9q53sXV9orsJMAkE8e91xizX3tenUEBGU_iSRgdbTATetnz9fOtD_aDtCbhbp_oYJGIpas97a4");'></div>
                <div class="p-4">
                  <p class="text-primary dark:text-neutral-white text-lg font-bold leading-normal">Advanced Robotics Lab</p>
                  <p class="text-neutral-dark-gray/70 dark:text-neutral-light-gray/70 text-sm font-normal leading-normal">Department of Engineering</p>
                </div>
              </div>
              <div class="flex flex-col gap-3 pb-3 bg-neutral-white dark:bg-primary/10 rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 group">
                <div class="w-full bg-center bg-no-repeat aspect-video bg-cover group-hover:scale-105 transition-transform duration-300" data-alt="An ancient manuscript is being digitized on a high-resolution scanner." style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCBcjKnkJu7PTHx-Y1Yj2WW7sm00NS6klx3U6JyBd6u6pSWtHPn0QAmktSDNycqmRKmlisF4gNmZDc1W7tmos0nZwvnwyLBfsOoUotYbhHxvD5LdThG0OAB6TtBaWTs--WyWn_FehSnqzraQSBCF_UaaBm7v_bRSqDJy36Zm6GRVgasKjSN9HxT6egSh3qNL7bLjy9gZrwPmHPqPF29eAm_ZA5QlTHjEVhnY-i6VWKQtpWRfTn-aggZGfGQNFnWHYmE2o4TB9oEy7MzZh8QCPOELIH2I8njVbp7lK79HxzF");'></div>
                <div class="p-4">
                  <p class="text-primary dark:text-neutral-white text-lg font-bold leading-normal">Digital Humanities Center</p>
                  <p class="text-neutral-dark-gray/70 dark:text-neutral-light-gray/70 text-sm font-normal leading-normal">Department of Arts &amp; Humanities</p>
                </div>
              </div>
              <div class="flex flex-col gap-3 pb-3 bg-neutral-white dark:bg-primary/10 rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 group">
                <div class="w-full bg-center bg-no-repeat aspect-video bg-cover group-hover:scale-105 transition-transform duration-300" data-alt="A scientist in a white coat examines petri dishes under a microscope." style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCLaqAy-2L9n-phrxAZdLILkVFWEj-FK6p-yaKZ5VHRQCJEW94H_KZ56FjSjyKUv9ZSbrytk9P-UXKzAacJUs2whjD59LZ8ndvGTogqQtFQI9w_AjTpcPdMsLo562OHrPuTfSxMCCafAm_ZA5QlTHjEVhnY-i6VWKQtpWRfTn-aggZGfGQNFnWHYmE2o4TB9oEy7MzZh8QCPOELIH2I8njVtpy8al1l3cuN8Rqt51JFh_ThGKQ4XNvE9cj1d0czlAHggTbBiI0GdcTU");'></div>
                <div class="p-4">
                  <p class="text-primary dark:text-neutral-white text-lg font-bold leading-normal">Bio-Science Innovation Hub</p>
                  <p class="text-neutral-dark-gray/70 dark:text-neutral-light-gray/70 text-sm font-normal leading-normal">Department of Health Sciences</p>
                </div>
              </div>
              <div class="flex flex-col gap-3 pb-3 bg-neutral-white dark:bg-primary/10 rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 group">
                <div class="w-full bg-center bg-no-repeat aspect-video bg-cover group-hover:scale-105 transition-transform duration-300" data-alt="A 3D model of a futuristic sustainable city with green rooftops and flying vehicles." style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAFzVaKQATUfLHiYp5xjSJbbiaQ6N2HM8HxvAJg--mtdC2IJ_8z2bZtufaCCaGeRVWq-7BwnIkzG_x-go4o2sNoP8uX04XxBgHVNy3Ec12lUf8tQE-3H4nj55SMwzBR48Y7q63uUJzNiP8hARWek_E3nLsEXh6zqKLmJIGRU1cCnfhgvUJCp6wQCaFtghzzk6um7NFCpIah0X-vNodHLhx5V2BOzJN_JldiBpBykq0O4_O1ye73xzficYXHr7OVdIFw_Uifaq3VFbrb");'></div>
                <div class="p-4">
                  <p class="text-primary dark:text-neutral-white text-lg font-bold leading-normal">Sustainable Urban Planning</p>
                  <p class="text-neutral-dark-gray/70 dark:text-neutral-light-gray/70 text-sm font-normal leading-normal">Department of Architecture</p>
                </div>
              </div>
              <div class="flex flex-col gap-3 pb-3 bg-neutral-white dark:bg-primary/10 rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 group">
                <div class="w-full bg-center bg-no-repeat aspect-video bg-cover group-hover:scale-105 transition-transform duration-300" data-alt="Abstract representation of quantum computing with glowing qubits and connections." style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDmlf7EcxbEZT9DGaQwsskvNbIicu3lIls5_FtOGvnEiirBM6kLJg6WPHoHPUHkzJF2_8AoNE6D8P9dbTlk01-y_ELvT49BawdsVVpvAKXSCPPlAYZ3XgtL0-lJGEWg7H6dKE4PP4O3PME2HuPFTsVb05-aFJ82uBi7IWHiJQ3dPvdqb9SN5tGFpDLH-oXX4i16dT2VA7X3b5LkUtq5ToCgqVUZ1wi2TArCMtf22Q1qOozZVAUayxsoO-klnFPFiMAONUMizR5_9xrS");'></div>
                <div class="p-4">
                  <p class="text-primary dark:text-neutral-white text-lg font-bold leading-normal">Quantum Computing Initiative</p>
                  <p class="text-neutral-dark-gray/70 dark:text-neutral-light-gray/70 text-sm font-normal leading-normal">Department of Physics</p>
                </div>
              </div>
              <div class="flex flex-col gap-3 pb-3 bg-neutral-white dark:bg-primary/10 rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 group">
                <div class="w-full bg-center bg-no-repeat aspect-video bg-cover group-hover:scale-105 transition-transform duration-300" data-alt="A colorful fMRI brain scan showing areas of high activity." style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCTnzDSEvqYEgR08E872Eq_jPlRr7HGxoV5R8H0v77zaDCEx15wHSv6m98iJV8sGjkowCPz9X0HgsLtyHhWiaRMwkRlBugATFAMu7GcE1a0NFL1RAiWV_TKX_RdTF_SrzGPD1wn_aOwcwaNCiBgUUtfrxQyC1A5I05Gl4Nxa1xVg-hhxr3scE5rgeFiQ0qO6GbMw_94wmfe2TQAYjlFY1qnCiBFpt75oiDjxZkEJnAIXjusY8nC0O97PH-SRxjMNM1daHIoZldaobI4");'></div>
                <div class="p-4">
                  <p class="text-primary dark:text-neutral-white text-lg font-bold leading-normal">Neuroscience Research Group</p>
                  <p class="text-neutral-dark-gray/70 dark:text-neutral-light-gray/70 text-sm font-normal leading-normal">Department of Psychology</p>
                </div>
              </div>
            </div>
          </section>
          <!-- Get Involved Section -->
          <section class="bg-neutral-white dark:bg-primary/10 p-8 md:p-12 rounded-xl">
            <div class="text-center mb-10">
              <h2 class="text-primary dark:text-neutral-white text-3xl font-bold leading-tight tracking-[-0.015em]">Your Path to Discovery</h2>
              <p class="text-neutral-dark-gray/80 dark:text-neutral-light-gray/80 mt-2 max-w-2xl mx-auto">Follow these steps to begin your research journey with us. We provide the resources and mentorship you need to succeed.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8 text-center relative">
              <!-- Dashed line connector for desktop -->
              <div class="hidden md:block absolute top-1/2 left-0 w-full h-px -translate-y-12">
                <svg class="stroke-current text-primary/30 dark:text-secondary/30" height="2" width="100%"><line stroke-dasharray="8 8" x1="0" x2="100%" y1="1" y2="1"></line></svg>
              </div>
              <div class="flex flex-col items-center gap-4 relative">
                <div class="flex items-center justify-center size-24 rounded-full bg-secondary text-primary font-bold text-4xl border-4 border-neutral-white dark:border-primary/20">1</div>
                <h3 class="text-xl font-bold text-primary dark:text-neutral-white mt-2">Find a Mentor</h3>
                <p class="text-sm text-neutral-dark-gray/80 dark:text-neutral-light-gray/80">Explore faculty profiles and current projects to find a researcher whose work inspires you.</p>
              </div>
              <div class="flex flex-col items-center gap-4 relative">
                <div class="flex items-center justify-center size-24 rounded-full bg-secondary text-primary font-bold text-4xl border-4 border-neutral-white dark:border-primary/20">2</div>
                <h3 class="text-xl font-bold text-primary dark:text-neutral-white mt-2">Apply for Funding</h3>
                <p class="text-sm text-neutral-dark-gray/80 dark:text-neutral-light-gray/80">We offer numerous grants and scholarships to support your undergraduate and graduate research.</p>
              </div>
              <div class="flex flex-col items-center gap-4 relative">
                <div class="flex items-center justify-center size-24 rounded-full bg-secondary text-primary font-bold text-4xl border-4 border-neutral-white dark:border-primary/20">3</div>
                <h3 class="text-xl font-bold text-primary dark:text-neutral-white mt-2">Present Your Work</h3>
                <p class="text-sm text-neutral-dark-gray/80 dark:text-neutral-light-gray/80">Showcase your findings at our annual research symposium and other national conferences.</p>
              </div>
            </div>
          </section>
          <!-- Testimonial Section -->
          <section class="text-center">
            <div class="bg-primary dark:bg-primary/20 p-8 md:p-16 rounded-xl relative overflow-hidden">
              <span class="material-symbols-outlined absolute -top-4 -left-4 text-9xl text-secondary opacity-20">format_quote</span>
              <div class="w-24 h-24 rounded-full mx-auto mb-4 bg-cover bg-center" data-alt="Portrait of student researcher Priya Sharma." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCgrMfBlN0kZSmPmfTEeioqCduJYY8BAA29mULAiEwKdhmLQdwiiZT8765WTRkAaDnCHYec1qguOeIJw5Il8ivB__o_zvqbpY-qqFDq6OY9_O3mX3D2Xj4esuBhFrCxotJjQyS9d2hI2FyBxiuKrhv76LP0Vsz6sFcpB5RwfouYMX9Ifoldmb6XmkrSBskqqoUanB4vwu635rF1gORAd6x5lZ81qlKhJd1v9CmJYtA2Tv4RQ0rF2nEfeeeF3pK2yvrfStUxmvnQ6B7p')"></div>
              <blockquote class="text-neutral-white text-xl italic max-w-3xl mx-auto">
                "Working in the Bio-Science lab was the highlight of my undergraduate career. It opened my eyes to the world of scientific discovery and gave me the confidence to pursue my PhD."
              </blockquote>
              <cite class="block text-secondary font-bold text-lg mt-4 not-italic">Priya Sharma</cite>
              <p class="text-neutral-light-gray/80 text-sm">B.S. in Molecular Biology, Class of '23</p>
              <span class="material-symbols-outlined absolute -bottom-4 -right-4 text-9xl text-secondary opacity-20 transform rotate-180">format_quote</span>
            </div>
          </section>
          <!-- Contact / Next Steps -->
          <section class="bg-neutral-white dark:bg-primary/10 p-8 md:p-12 rounded-xl text-center">
            <h2 class="text-primary dark:text-neutral-white text-3xl font-bold leading-tight tracking-[-0.015em]">Ready to Innovate?</h2>
            <p class="text-neutral-dark-gray/80 dark:text-neutral-light-gray/80 mt-2 mb-8 max-w-2xl mx-auto">Take the next step in your academic journey. Contact our research office, view application deadlines, or attend an info session.</p>
            <div class="flex flex-col md:flex-row gap-4 justify-center">
              <button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-primary text-neutral-white text-base font-bold leading-normal tracking-[0.015em] hover:opacity-90">
                <span class="truncate">Contact Research Office</span>
              </button>
              <button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-primary/10 dark:bg-primary/30 text-primary dark:text-neutral-light-gray text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/20 dark:hover:bg-primary/40">
                <span class="truncate">View Deadlines</span>
              </button>
            </div>
          </section>
        </main>
        <!-- Footer -->
        <footer class="mt-20 border-t border-neutral-light-gray dark:border-neutral-dark-gray/20 pt-10 pb-5">
          <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8">
            <div class="col-span-2 lg:col-span-1">
              <div class="flex items-center gap-2 text-primary dark:text-neutral-white">
                <div class="size-6">
                  <svg fill="currentColor" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="M24 4H42V17.3333V30.6667H24V44H6V30.6667V17.3333H24V4Z" fill="currentColor" fill-rule="evenodd"></path></svg>
                </div>
                <h3 class="text-lg font-bold">Valley View University</h3>
              </div>
              <p class="text-xs text-neutral-dark-gray/70 dark:text-neutral-light-gray/70 mt-2">123 University Drive, Mountain Top, CA 98765</p>
            </div>
            <div>
              <h4 class="font-bold mb-3 text-neutral-dark-gray dark:text-neutral-white">Quick Links</h4>
              <ul class="space-y-2 text-sm">
                <li><a class="hover:text-primary dark:hover:text-secondary" href="#">Admissions</a></li>
                <li><a class="hover:text-primary dark:hover:text-secondary" href="#">Academics</a></li>
                <li><a class="hover:text-primary dark:hover:text-secondary" href="#">Campus Life</a></li>
                <li><a class="hover:text-primary dark:hover:text-secondary" href="#">Library</a></li>
              </ul>
            </div>
            <div>
              <h4 class="font-bold mb-3 text-neutral-dark-gray dark:text-neutral-white">Resources</h4>
              <ul class="space-y-2 text-sm">
                <li><a class="hover:text-primary dark:hover:text-secondary" href="#">For Students</a></li>
                <li><a class="hover:text-primary dark:hover:text-secondary" href="#">For Faculty &amp; Staff</a></li>
                <li><a class="hover:text-primary dark:hover:text-secondary" href="#">For Alumni</a></li>
                <li><a class="hover:text-primary dark:hover:text-secondary" href="#">News &amp; Events</a></li>
              </ul>
            </div>
            <div>
              <h4 class="font-bold mb-3 text-neutral-dark-gray dark:text-neutral-white">Information</h4>
              <ul class="space-y-2 text-sm">
                <li><a class="hover:text-primary dark:hover:text-secondary" href="#">About VVU</a></li>
                <li><a class="hover:text-primary dark:hover:text-secondary" href="#">Contact Us</a></li>
                <li><a class="hover:text-primary dark:hover:text-secondary" href="#">Careers</a></li>
                <li><a class="hover:text-primary dark:hover:text-secondary" href="#">Give to VVU</a></li>
              </ul>
            </div>
            <div>
              <h4 class="font-bold mb-3 text-neutral-dark-gray dark:text-neutral-white">Follow Us</h4>
              <div class="flex gap-4">
                <!-- Social Icons would go here -->
              </div>
            </div>
          </div>
          <div class="text-center text-xs text-neutral-dark-gray/60 dark:text-neutral-light-gray/60 mt-10">
            © 2024 Valley View University. All Rights Reserved. | <a class="hover:underline" href="#">Privacy Policy</a>
          </div>
        </footer>
      </div>
    </div>
  </div>
</div>

<?php
include 'includes/footer.php';
?>