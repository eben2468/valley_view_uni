<?php
$pageTitle = "Valley View University - Staff Encyclopedia";
$activePage = "staff_encyclopedia";
include 'includes/header.php';
?>

<div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
  <div class="layout-container flex h-full grow flex-col">
    <div class="px-4 sm:px-8 md:px-16 lg:px-24 xl:px-40 flex flex-1 justify-center py-5">
      <div class="layout-content-container flex flex-col max-w-[960px] flex-1">
        <!-- TopNavBar -->
        <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-[#e7e7f3] dark:border-[#2a2a4b] px-4 sm:px-6 md:px-10 py-3">
          <div class="flex items-center gap-4 text-primary">
            <div class="size-6">
              <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                <path clip-rule="evenodd" d="M24 4H42V17.3333V30.6667H24V44H6V30.6667V17.3333H24V4Z" fill="currentColor" fill-rule="evenodd"></path>
              </svg>
            </div>
            <h2 class="text-[#0d0d1b] dark:text-[#f8f8fc] text-lg font-bold leading-tight tracking-[-0.015em]">Valley View University</h2>
          </div>
          <div class="hidden lg:flex flex-1 justify-end gap-8">
            <div class="flex items-center gap-9">
              <a class="text-[#0d0d1b] dark:text-[#f8f8fc] text-sm font-medium leading-normal hover:text-primary dark:hover:text-primary" href="#">Admissions</a>
              <a class="text-[#0d0d1b] dark:text-[#f8f8fc] text-sm font-medium leading-normal hover:text-primary dark:hover:text-primary" href="#">Academics</a>
              <a class="text-[#0d0d1b] dark:text-[#f8f8fc] text-sm font-medium leading-normal hover:text-primary dark:hover:text-primary" href="#">Research</a>
              <a class="text-[#0d0d1b] dark:text-[#f8f8fc] text-sm font-medium leading-normal hover:text-primary dark:hover:text-primary" href="#">Campus Life</a>
              <a class="text-[#0d0d1b] dark:text-[#f8f8fc] text-sm font-medium leading-normal hover:text-primary dark:hover:text-primary" href="#">About</a>
            </div>
            <div class="flex gap-2 items-center">
              <button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-opacity-90 transition-colors">
                <span class="truncate">Apply Now</span>
              </button>
              <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-10" data-alt="User profile picture" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuC8y8uUB1Vb1wptfgJMp2c5oglSg2G3S7WLM8Rq1W7KRKXsPeQtO0ouhzO3gOMuNZOHAb1te1tjDaMD7CAb8oVqdZs9OaK7S5GMNqGHWTgHfDigAzLZtM_SrNI2x4IzEdjc1Sx56uKZIQgjOe8zQ7Lu81X6-XDVaHgfvPeEWG9Wgk6l9j_df-DI0tRjjNrG6xe7cerPzr_MT87obKdYGDdMcYEG7LHk7PZaRXYmHnuv-9WtQDm98UNVR2sNWrf1Zm-1NJS3RbErQAAi");'></div>
            </div>
          </div>
          <button class="lg:hidden flex max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 bg-[#e7e7f3] dark:bg-[#2a2a4b] text-[#0d0d1b] dark:text-[#f8f8fc] gap-2 text-sm font-bold leading-normal tracking-[0.015em] min-w-0 px-2.5">
            <span class="material-symbols-outlined !text-2xl">menu</span>
          </button>
        </header>
        <!-- PageHeading -->
        <div class="flex flex-wrap justify-between gap-3 p-4 mt-8">
          <div class="flex min-w-72 flex-col gap-3">
            <p class="text-[#0d0d1b] dark:text-[#f8f8fc] text-4xl font-black leading-tight tracking-[-0.033em]">Meet Our Dedicated Faculty &amp; Staff</p>
            <p class="text-[#4c4c9a] dark:text-[#a1a1e2] text-base font-normal leading-normal">
              Explore our directory to connect with the talented individuals who make our university a center for excellence.
            </p>
          </div>
        </div>
        <!-- SearchBar -->
        <div class="px-4 py-3">
          <label class="flex flex-col min-w-40 h-12 w-full">
            <div class="flex w-full flex-1 items-stretch rounded-xl h-full">
              <div class="text-[#4c4c9a] dark:text-[#a1a1e2] flex border-none bg-[#e7e7f3] dark:bg-[#2a2a4b] items-center justify-center pl-4 rounded-l-xl border-r-0">
                <span class="material-symbols-outlined !text-2xl">search</span>
              </div>
              <input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-r-xl text-[#0d0d1b] dark:text-[#f8f8fc] focus:outline-0 focus:ring-2 focus:ring-primary/50 border-none bg-[#e7e7f3] dark:bg-[#2a2a4b] h-full placeholder:text-[#4c4c9a] dark:placeholder:text-[#a1a1e2] px-4 text-base font-normal leading-normal" placeholder="Search by name, title, or keyword..." value=""/>
            </div>
          </label>
        </div>
        <!-- Chips/Filters -->
        <div class="flex gap-3 p-3 flex-wrap pr-4">
          <button class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-[#e7e7f3] dark:bg-[#2a2a4b] pl-4 pr-2 text-[#0d0d1b] dark:text-[#f8f8fc] hover:bg-primary/20 dark:hover:bg-primary/30 transition-colors">
            <p class="text-sm font-medium leading-normal">All Departments</p>
            <span class="material-symbols-outlined !text-xl">expand_more</span>
          </button>
          <button class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-[#e7e7f3] dark:bg-[#2a2a4b] pl-4 pr-2 text-[#0d0d1b] dark:text-[#f8f8fc] hover:bg-primary/20 dark:hover:bg-primary/30 transition-colors">
            <p class="text-sm font-medium leading-normal">All Campuses</p>
            <span class="material-symbols-outlined !text-xl">expand_more</span>
          </button>
          <button class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-[#e7e7f3] dark:bg-[#2a2a4b] pl-4 pr-2 text-[#0d0d1b] dark:text-[#f8f8fc] hover:bg-primary/20 dark:hover:bg-primary/30 transition-colors">
            <p class="text-sm font-medium leading-normal">All Titles</p>
            <span class="material-symbols-outlined !text-xl">expand_more</span>
          </button>
        </div>
        <hr class="border-[#e7e7f3] dark:border-[#2a2a4b] mx-4 mt-2 mb-4"/>
        <!-- ImageGrid -->
        <div class="grid grid-cols-[repeat(auto-fit,minmax(158px,1fr))] gap-4 p-4">
          <div class="flex flex-col gap-3 pb-3 group">
            <div class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-xl overflow-hidden transform group-hover:scale-105 transition-transform duration-300" data-alt="Professional headshot of Dr. Eleanor Vance" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAOSj5gAPN9F9F_pamaToOjE7-mI1ZUUoSQqCTYTs7LaDyArCzn7HcYwcn4veQcgCVGXEyzV1cvkNZg9umY49LTrSDezdUK7W65JxtfVEO0MfgRKzZE0O95sBH_EzzTYlwTpoV5ofajg8lj97di89gQ1n9qmwXXkBwmAdJl4TnTzafIMIvCLMa6dZ7rpGsH5pEFytiHj2H6ktgUuTZgwwYFxiF0uIzRB_b3DymUnhwEH1P1nLT8RiW5WpnfPiNyrWncxofSVOfS5eAj");'></div>
            <div>
              <p class="text-[#0d0d1b] dark:text-[#f8f8fc] text-base font-bold leading-normal">Dr. Eleanor Vance</p>
              <p class="text-[#4c4c9a] dark:text-[#a1a1e2] text-sm font-normal leading-normal">Professor of History</p>
              <p class="text-[#4c4c9a] dark:text-[#a1a1e2] text-sm font-normal leading-normal">School of Humanities</p>
            </div>
          </div>
          <div class="flex flex-col gap-3 pb-3 group">
            <div class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-xl overflow-hidden transform group-hover:scale-105 transition-transform duration-300" data-alt="Professional headshot of Prof. Samuel Reed" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuA_PzdQFfvFDgGfErT4eJ7Q4koH0S-knxZzt9-TKLJvn1kBYowXz7xEPsnVlu5-x50mq0x47OZwyXgp1U60Uc3F0AvUWOomKDWKC2GRj_0EXRZwIMgZ0ofDJTqApBoh-JMF1KPI1BjFnzQZ-Ch8AnUnt0_kpCA-uDVoIPjURbRb6yu6_UENLFsRRnWKHP-f1M6lMritfZwPugSDnf-MaRxi80dnvuvdkzUQ12invAA1c8fRSzrEpzqKETnsMmgu1o0m1tySqsJ1_dwX");'></div>
            <div>
              <p class="text-[#0d0d1b] dark:text-[#f8f8fc] text-base font-bold leading-normal">Prof. Samuel Reed</p>
              <p class="text-[#4c4c9a] dark:text-[#a1a1e2] text-sm font-normal leading-normal">Associate Professor</p>
              <p class="text-[#4c4c9a] dark:text-[#a1a1e2] text-sm font-normal leading-normal">College of Sciences</p>
            </div>
          </div>
          <div class="flex flex-col gap-3 pb-3 group">
            <div class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-xl overflow-hidden transform group-hover:scale-105 transition-transform duration-300" data-alt="Professional headshot of Dr. Beatrice Chen" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuB25rOHSpmGRKbN5YJBltxCrYnlsKIK12hWV9JloefEbsuV-sAn9gERvtI5azMiDLSF939lknkHG1MDNaQ-reJtmCMNl3xEDkwCXjM-Fp4-LFRvn0FFysxwVX53GjWGRadXZOH-vFCQFcRW2xEJ7S0_ksTZBzxJpfP1HbAeFUiF_C1mr7jLUvi5mA4ILt7V11X_7kWNej0H10imKUR2yb2eNCUm4X1217Bxs7eYuM-exXHFmCMgyAYv996Qt0bz9sHoDdht3TNQcTrn");'></div>
            <div>
              <p class="text-[#0d0d1b] dark:text-[#f8f8fc] text-base font-bold leading-normal">Dr. Beatrice Chen</p>
              <p class="text-[#4c4c9a] dark:text-[#a1a1e2] text-sm font-normal leading-normal">Head of Computer Science</p>
              <p class="text-[#4c4c9a] dark:text-[#a1a1e2] text-sm font-normal leading-normal">School of Engineering</p>
            </div>
          </div>
          <div class="flex flex-col gap-3 pb-3 group">
            <div class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-xl overflow-hidden transform group-hover:scale-105 transition-transform duration-300" data-alt="Professional headshot of Mr. David Grant" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCUsevv7LIBy0Ewq_uT0JKxewDp2Zu6Z_k14VGTL9BqNApyKlkp2r5OQz8ZY2MzG8RuoThADv4F8df9v6wlDiytJgtrcuXcHbe_rhRB9TnvsXmosMEHRFIISwi9bb7wZo9JfKzt_bcGFnKTA3yWnwVYJMMV1WFLTcjs0_zLoKES250uYqdd6651jcmwAsu4Otl8TfCI3wBHkVBUYBHBB3Vh4jJKyIv3KsEe4Lqi5m-Po9Q1TlAqCQ1fWDY2qIzMfFGGL2cJJ6Q8HWqk");'></div>
            <div>
              <p class="text-[#0d0d1b] dark:text-[#f8f8fc] text-base font-bold leading-normal">Mr. David Grant</p>
              <p class="text-[#4c4c9a] dark:text-[#a1a1e2] text-sm font-normal leading-normal">Director of Admissions</p>
              <p class="text-[#4c4c9a] dark:text-[#a1a1e2] text-sm font-normal leading-normal">Administration</p>
            </div>
          </div>
          <div class="flex flex-col gap-3 pb-3 group">
            <div class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-xl overflow-hidden transform group-hover:scale-105 transition-transform duration-300" data-alt="Professional headshot of Ms. Sofia Rodriguez" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuB26-VX19oq2ybmzNg-u6wPJDUfX5168kEoHBwwOpkdaao2zPCqSBEgVRkXk5yjAoYyivanahXxV_Hy4j45sYSZhbffAOTnZpSbXWfnWKTCrElADYd_K-U91zNjvIINLHEaAE3_v9fGIlUo5STHAO0dD0GiU1uEGryHpKMCtYSVE-QEWDzYhBhKbOiQtkpHAN4Pknu_rW0ORIui_Yu2ecpl5lcxo4A6L7vTZZrKs7CxwLXJ_EEr71s8Ve5jkQR_4rnyv3k975MVPhoY");'></div>
            <div>
              <p class="text-[#0d0d1b] dark:text-[#f8f8fc] text-base font-bold leading-normal">Ms. Sofia Rodriguez</p>
              <p class="text-[#4c4c9a] dark:text-[#a1a1e2] text-sm font-normal leading-normal">Librarian</p>
              <p class="text-[#4c4c9a] dark:text-[#a1a1e2] text-sm font-normal leading-normal">University Library</p>
            </div>
          </div>
          <div class="flex flex-col gap-3 pb-3 group">
            <div class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-xl overflow-hidden transform group-hover:scale-105 transition-transform duration-300" data-alt="Professional headshot of Dr. Michael Thompson" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDXjKNWkwTGTtkrM3Ic7tHGlRSSUYhdBrUPlD-JmAatoYF4cZ-tuMm3E_s15Q0_mz1HGZEbFe51bdGUKiKjNn7BrfB0Vl6kSEvP9sg7rMvHz5uMjfBizoJF0ag1OMRnZnEvtWa_3WKx0igY_hqwsRlKTlvEBpLJCYpyxvFs60ORNW3tP-GQs49iYYuWstGEUFPhpW2BoMrZqhzGqt8t_56b-xHLPjYTeMppqvqsQnVFNienvw3VuQLxJL3HGzyGYaVKIxorqOFhoizF");'></div>
            <div>
              <p class="text-[#0d0d1b] dark:text-[#f8f8fc] text-base font-bold leading-normal">Dr. Michael Thompson</p>
              <p class="text-[#4c4c9a] dark:text-[#a1a1e2] text-sm font-normal leading-normal">Dean of Arts &amp; Sciences</p>
              <p class="text-[#4c4c9a] dark:text-[#a1a1e2] text-sm font-normal leading-normal">Administration</p>
            </div>
          </div>
          <div class="flex flex-col gap-3 pb-3 group">
            <div class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-xl overflow-hidden transform group-hover:scale-105 transition-transform duration-300" data-alt="Professional headshot of Prof. Linda Harris" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuD-0kUzyO5TvC0_g7G2M-NZP2vh6pyBvGd9474CB5CordsyX6f0MtDNcyRhRMSaqxOGnvNk6vvSSpJn03MVJA5DJgHxYlrjs8OIbpMskCmt6PHCUtUQ-Hyc0OZE-6jtkxamWnMSPcg6qAw9vzB-8MzcDAAqdQ85L2Y7Dv2jr036Rm8-hM32xQl67O0uv346QNxlU71_ksbRIvguPo3-zUzOR-YpOAmLQdgn02QwNdjBk2GQqYgB9BITtGqXzYQxtlycLRJxjBv4io4o");'></div>
            <div>
              <p class="text-[#0d0d1b] dark:text-[#f8f8fc] text-base font-bold leading-normal">Prof. Linda Harris</p>
              <p class="text-[#4c4c9a] dark:text-[#a1a1e2] text-sm font-normal leading-normal">Professor of Fine Arts</p>
              <p class="text-[#4c4c9a] dark:text-[#a1a1e2] text-sm font-normal leading-normal">School of Arts</p>
            </div>
          </div>
          <div class="flex flex-col gap-3 pb-3 group">
            <div class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-xl overflow-hidden transform group-hover:scale-105 transition-transform duration-300" data-alt="Professional headshot of Dr. Robert Clark" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAKkLvJRBnEN5CMSpdFrn3FyQ8rXEGrpX468MXwKadIqpyH_7kZlzH3bR2sLHEi28YY5xoxWgNjU3gbC68eUeVgElXBDlcxYVsKCI3W0sCVDW6YLwfaUPO0yszJGzFc6FjrFdGvbQqARbk_qcUo7LWL0OsqC9453ze9Wx7lTXgJAsTRUrbXN2Ok5blo-TI970U8c0wROjHdYtvHUSPcAWz0Vc0quvTM00E-l6geyMvvfIF85FdI2UBzBemF49lBKv9U5phX0ah6e3uh");'></div>
            <div>
              <p class="text-[#0d0d1b] dark:text-[#f8f8fc] text-base font-bold leading-normal">Dr. Robert Clark</p>
              <p class="text-[#4c4c9a] dark:text-[#a1a1e2] text-sm font-normal leading-normal">Research Scientist</p>
              <p class="text-[#4c4c9a] dark:text-[#a1a1e2] text-sm font-normal leading-normal">Research Institute</p>
            </div>
          </div>
        </div>
        <!-- Pagination -->
        <div class="flex justify-center items-center gap-2 p-4 mt-8">
          <button class="flex items-center justify-center size-10 rounded-lg bg-[#e7e7f3] dark:bg-[#2a2a4b] text-[#4c4c9a] dark:text-[#a1a1e2] hover:bg-primary/20 dark:hover:bg-primary/30 hover:text-primary dark:hover:text-white transition-colors">
            <span class="material-symbols-outlined !text-2xl">chevron_left</span>
          </button>
          <button class="flex items-center justify-center size-10 rounded-lg bg-primary text-white font-bold text-sm">1</button>
          <button class="flex items-center justify-center size-10 rounded-lg bg-[#e7e7f3] dark:bg-[#2a2a4b] text-[#0d0d1b] dark:text-[#f8f8fc] font-bold text-sm hover:bg-primary/20 dark:hover:bg-primary/30 transition-colors">2</button>
          <button class="flex items-center justify-center size-10 rounded-lg bg-[#e7e7f3] dark:bg-[#2a2a4b] text-[#0d0d1b] dark:text-[#f8f8fc] font-bold text-sm hover:bg-primary/20 dark:hover:bg-primary/30 transition-colors">3</button>
          <span class="text-[#4c4c9a] dark:text-[#a1a1e2]">...</span>
          <button class="flex items-center justify-center size-10 rounded-lg bg-[#e7e7f3] dark:bg-[#2a2a4b] text-[#0d0d1b] dark:text-[#f8f8fc] font-bold text-sm hover:bg-primary/20 dark:hover:bg-primary/30 transition-colors">12</button>
          <button class="flex items-center justify-center size-10 rounded-lg bg-[#e7e7f3] dark:bg-[#2a2a4b] text-[#4c4c9a] dark:text-[#a1a1e2] hover:bg-primary/20 dark:hover:bg-primary/30 hover:text-primary dark:hover:text-white transition-colors">
            <span class="material-symbols-outlined !text-2xl">chevron_right</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
include 'includes/footer.php';
?>