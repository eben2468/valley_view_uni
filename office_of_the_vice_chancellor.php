<?php
$pageTitle = "Office of the Vice Chancellor - Valley View University";
$activePage = "office_of_the_vice_chancellor";
include 'includes/header.php';
?>

<div class="relative flex min-h-screen w-full flex-col">
  <!-- TopNavBar Component -->
  <header class="sticky top-0 z-20 w-full bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm border-b border-border-light dark:border-border-dark">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
      <div class="flex items-center gap-4 py-3">
        <div class="h-6 w-6 text-primary">
          <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
            <path clip-rule="evenodd" d="M24 4H42V17.3333V30.6667H24V44H6V30.6667V17.3333H24V4Z" fill="currentColor" fill-rule="evenodd"></path>
          </svg>
        </div>
        <h2 class="text-lg font-bold tracking-[-0.015em] text-text-light dark:text-text-dark">Valley View University</h2>
      </div>
      <div class="hidden items-center gap-9 lg:flex">
        <a class="text-sm font-medium text-text-light dark:text-text-dark hover:text-primary dark:hover:text-primary" href="#">Admissions</a>
        <a class="text-sm font-medium text-text-light dark:text-text-dark hover:text-primary dark:hover:text-primary" href="#">Academics</a>
        <a class="text-sm font-medium text-text-light dark:text-text-dark hover:text-primary dark:hover:text-primary" href="#">Research</a>
        <a class="text-sm font-medium text-text-light dark:text-text-dark hover:text-primary dark:hover:text-primary" href="#">About</a>
        <a class="text-sm font-medium text-text-light dark:text-text-dark hover:text-primary dark:hover:text-primary" href="#">Student Life</a>
      </div>
      <div class="flex items-center gap-2">
        <button class="flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold tracking-[0.015em] hover:bg-primary/90 transition-colors">
          <span class="truncate">Apply Now</span>
        </button>
        <button class="flex h-10 w-10 cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-border-light dark:bg-border-dark text-text-light dark:text-text-dark hover:bg-border-light/80 dark:hover:bg-border-dark/80 transition-colors">
          <span class="material-symbols-outlined text-xl">search</span>
        </button>
        <button class="flex h-10 w-10 cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-border-light dark:bg-border-dark text-text-light dark:text-text-dark hover:bg-border-light/80 dark:hover:bg-border-dark/80 transition-colors lg:hidden">
          <span class="material-symbols-outlined text-xl">menu</span>
        </button>
      </div>
    </div>
  </header>
  <main class="flex-grow">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-4xl">
        <!-- ProfileHeader/Hero Section -->
        <section class="mb-12" id="hero">
          <div class="flex flex-col gap-8 @container md:flex-row md:items-center">
            <div class="md:w-1/3">
              <div class="aspect-[3/4] w-full rounded-xl bg-cover bg-center bg-no-repeat" data-alt="Professional portrait of Dr. Evelyn Reed, Vice Chancellor." style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCLxwRQaStcMjctmdRSUlFrbTzHrHZ4QmQ7_w-SjNu0YbuDefwcI5HThsfCdLLv2t2buwPecrNFBE0YG9eouPiuXF_v0W_iZyuf-LqyyZDM_LGTDg50yAveRJO1xUoJWTArmE9HlG_NBbpBogj3YzigfkiFnlpHCvldhseWxVTj3HaJpdFaTDwR34NqL0UJmX8pZa6aANMldz55PZSL0ZrzavkAeMjQv_pYGZbL4ObyJK-1ZZU9mW2rBo-Z6I1hzq_bCvv7QBKpvQy0");'></div>
            </div>
            <div class="flex flex-col md:w-2/3 md:pl-8">
              <h1 class="text-4xl font-bold tracking-tighter text-text-light dark:text-text-dark sm:text-5xl">Dr. Evelyn Reed</h1>
              <p class="mt-2 text-xl font-medium text-primary">Vice Chancellor, Valley View University</p>
              <p class="mt-4 text-base leading-relaxed text-text-muted-light dark:text-text-muted-dark">
                Welcome to the Office of the Vice Chancellor. It is an honor to lead this esteemed institution and to work alongside our dedicated faculty, staff, and students to foster an environment of academic excellence and innovation.
              </p>
            </div>
          </div>
        </section>
        <!-- Sticky Sub-Navigation (Chips) -->
        <nav class="sticky-nav bg-background-light/90 dark:bg-background-dark/90 backdrop-blur-sm border-b border-border-light dark:border-border-dark py-3 mb-12 overflow-x-auto">
          <div class="flex gap-2">
            <a class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-full px-4 bg-primary/20 hover:bg-primary/30 transition-colors" href="#welcome">
              <p class="text-sm font-medium text-primary">Welcome Message</p>
            </a>
            <a class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-full px-4 bg-border-light dark:bg-border-dark hover:bg-primary/20 dark:hover:bg-primary/20 transition-colors" href="#biography">
              <p class="text-sm font-medium text-text-light dark:text-text-dark">Biography</p>
            </a>
            <a class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-full px-4 bg-border-light dark:bg-border-dark hover:bg-primary/20 dark:hover:bg-primary/20 transition-colors" href="#initiatives">
              <p class="text-sm font-medium text-text-light dark:text-text-dark">Key Initiatives</p>
            </a>
            <a class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-full px-4 bg-border-light dark:bg-border-dark hover:bg-primary/20 dark:hover:bg-primary/20 transition-colors" href="#contact">
              <p class="text-sm font-medium text-text-light dark:text-text-dark">Contact</p>
            </a>
          </div>
        </nav>
        <!-- Welcome Message Section -->
        <section class="mb-16 scroll-mt-24" id="welcome">
          <h2 class="text-3xl font-bold tracking-tight text-text-light dark:text-text-dark">A Message from the Vice Chancellor</h2>
          <div class="mt-4 space-y-4 text-base leading-relaxed text-text-light dark:text-text-dark">
            <p>
              It is with immense pride and a deep sense of responsibility that I welcome you to Valley View University. Our institution is built on a legacy of academic rigor, innovative research, and a commitment to nurturing the next generation of leaders. We are dedicated to creating a dynamic and inclusive community where every student can thrive and achieve their full potential.
            </p>
            <!-- Pull Quote -->
            <blockquote class="border-l-4 border-primary pl-6 py-2 my-6">
              <p class="text-xl italic font-medium text-text-light dark:text-text-dark">
                "As we look to the future, we will continue to champion excellence, drive progress, and make a meaningful impact on the world."
              </p>
            </blockquote>
            <p>
              Our mission extends beyond the classroom. We are committed to fostering critical thinking, creativity, and a passion for lifelong learning. I invite you to explore our website, learn more about our vibrant community, and discover the endless opportunities that await you at Valley View.
            </p>
          </div>
        </section>
        <!-- Biography Section -->
        <section class="mb-16 scroll-mt-24" id="biography">
          <h2 class="text-3xl font-bold tracking-tight text-text-light dark:text-text-dark">Professional Biography</h2>
          <div class="mt-4 space-y-4 text-base leading-relaxed text-text-light dark:text-text-dark">
            <p>
              Dr. Evelyn Reed is a distinguished academic leader with over two decades of experience in higher education. Before her appointment as Vice Chancellor of Valley View University, she served as the Dean of the College of Arts and Sciences at Northwood University, where she was instrumental in launching several interdisciplinary programs and securing record levels of research funding.
            </p>
            <p>
              A renowned scholar in the field of computational linguistics, Dr. Reed holds a Ph.D. from Stanford University and has published extensively in peer-reviewed journals. Her research focuses on the intersection of artificial intelligence and human language. She is a passionate advocate for leveraging technology to enhance learning and is deeply committed to student success and faculty development.
            </p>
          </div>
        </section>
        <!-- Key Initiatives Section with Accordion -->
        <section class="mb-16 scroll-mt-24" id="initiatives">
          <h2 class="text-3xl font-bold tracking-tight text-text-light dark:text-text-dark">Key Initiatives &amp; Strategic Goals</h2>
          <div class="mt-6 space-y-4">
            <details class="group rounded-xl bg-foreground-light dark:bg-foreground-dark p-6 border border-border-light dark:border-border-dark">
              <summary class="flex cursor-pointer items-center justify-between text-lg font-medium text-text-light dark:text-text-dark">
                Digital Transformation in Education
                <span class="material-symbols-outlined transition-transform group-open:rotate-180">expand_more</span>
              </summary>
              <p class="mt-4 text-base leading-relaxed text-text-muted-light dark:text-text-muted-dark">
                Championing the integration of cutting-edge technology into our curriculum to create immersive, flexible, and accessible learning experiences for all students, preparing them for the digital economy.
              </p>
            </details>
            <details class="group rounded-xl bg-foreground-light dark:bg-foreground-dark p-6 border border-border-light dark:border-border-dark">
              <summary class="flex cursor-pointer items-center justify-between text-lg font-medium text-text-light dark:text-text-dark">
                Fostering Global Research Partnerships
                <span class="material-symbols-outlined transition-transform group-open:rotate-180">expand_more</span>
              </summary>
              <p class="mt-4 text-base leading-relaxed text-text-muted-light dark:text-text-muted-dark">
                Expanding our international collaborations to tackle global challenges. This initiative aims to increase joint research projects, faculty exchanges, and global learning opportunities for our students.
              </p>
            </details>
            <details class="group rounded-xl bg-foreground-light dark:bg-foreground-dark p-6 border border-border-light dark:border-border-dark">
              <summary class="flex cursor-pointer items-center justify-between text-lg font-medium text-text-light dark:text-text-dark">
                Sustainability and Community Engagement
                <span class="material-symbols-outlined transition-transform group-open:rotate-180">expand_more</span>
              </summary>
              <p class="mt-4 text-base leading-relaxed text-text-muted-light dark:text-text-muted-dark">
                Committing to environmental stewardship through campus-wide sustainability projects and strengthening our ties with the local community through service-learning and outreach programs.
              </p>
            </details>
          </div>
        </section>
        <!-- Contact Section -->
        <section class="scroll-mt-24" id="contact">
          <h2 class="text-3xl font-bold tracking-tight text-text-light dark:text-text-dark">Contact the Office</h2>
          <div class="mt-6 grid grid-cols-1 gap-8 rounded-xl bg-foreground-light dark:bg-foreground-dark p-8 border border-border-light dark:border-border-dark sm:grid-cols-2">
            <div class="space-y-4">
              <h3 class="text-lg font-semibold text-text-light dark:text-text-dark">Office Information</h3>
              <div class="flex items-start gap-3">
                <span class="material-symbols-outlined mt-1 text-primary">location_on</span>
                <p class="text-text-muted-light dark:text-text-muted-dark">
                  Founders Hall, Room 401<br/>
                  123 University Drive<br/>
                  Valley View, ST 12345
                </p>
              </div>
              <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary">mail</span>
                <a class="text-text-muted-light dark:text-text-muted-dark hover:text-primary" href="mailto:vc.office@vvu.edu">vc.office@vvu.edu</a>
              </div>
              <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary">call</span>
                <a class="text-text-muted-light dark:text-text-muted-dark hover:text-primary" href="tel:+1234567890">(123) 456-7890</a>
              </div>
            </div>
            <div class="space-y-4">
              <h3 class="text-lg font-semibold text-text-light dark:text-text-dark">Scheduling an Appointment</h3>
              <p class="text-text-muted-light dark:text-text-muted-dark">
                To schedule a meeting or make an inquiry, please contact the Executive Assistant to the Vice Chancellor via email or phone.
              </p>
              <a class="inline-flex items-center justify-center rounded-lg bg-primary px-5 py-2.5 text-sm font-medium text-white hover:bg-primary/90 transition-colors" href="#">
                Use Contact Form
              </a>
            </div>
          </div>
        </section>
      </div>
    </div>
  </main>
  <!-- Footer -->
  <footer class="bg-foreground-light dark:bg-foreground-dark border-t border-border-light dark:border-border-dark">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
      <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
        <div class="col-span-2 md:col-span-1">
          <div class="flex items-center gap-3">
            <div class="h-6 w-6 text-primary">
              <svg fill="none" viewbox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                <path clip-rule="evenodd" d="M24 4H42V17.3333V30.6667H24V44H6V30.6667V17.3333H24V4Z" fill="currentColor" fill-rule="evenodd"></path>
              </svg>
            </div>
            <h2 class="text-lg font-bold text-text-light dark:text-text-dark">Valley View University</h2>
          </div>
          <p class="mt-4 text-sm text-text-muted-light dark:text-text-muted-dark">Fostering excellence, innovation, and community since 1965.</p>
        </div>
        <div>
          <h3 class="font-semibold text-text-light dark:text-text-dark">Quick Links</h3>
          <ul class="mt-4 space-y-2 text-sm">
            <li><a class="text-text-muted-light dark:text-text-muted-dark hover:text-primary" href="#">Admissions</a></li>
            <li><a class="text-text-muted-light dark:text-text-muted-dark hover:text-primary" href="#">Campus Map</a></li>
            <li><a class="text-text-muted-light dark:text-text-muted-dark hover:text-primary" href="#">Library</a></li>
            <li><a class="text-text-muted-light dark:text-text-muted-dark hover:text-primary" href="#">Careers</a></li>
          </ul>
        </div>
        <div>
          <h3 class="font-semibold text-text-light dark:text-text-dark">Resources</h3>
          <ul class="mt-4 space-y-2 text-sm">
            <li><a class="text-text-muted-light dark:text-text-muted-dark hover:text-primary" href="#">For Students</a></li>
            <li><a class="text-text-muted-light dark:text-text-muted-dark hover:text-primary" href="#">For Faculty &amp; Staff</a></li>
            <li><a class="text-text-muted-light dark:text-text-muted-dark hover:text-primary" href="#">For Alumni</a></li>
            <li><a class="text-text-muted-light dark:text-text-muted-dark hover:text-primary" href="#">Media Relations</a></li>
          </ul>
        </div>
        <div>
          <h3 class="font-semibold text-text-light dark:text-text-dark">Contact</h3>
          <ul class="mt-4 space-y-2 text-sm text-text-muted-light dark:text-text-muted-dark">
            <li>123 University Drive</li>
            <li>Valley View, ST 12345</li>
            <li>(123) 456-7890</li>
          </ul>
        </div>
      </div>
      <div class="mt-12 border-t border-border-light dark:border-border-dark pt-8 text-center text-sm text-text-muted-light dark:text-text-muted-dark">
        <p>© 2024 Valley View University. All rights reserved. <a class="hover:text-primary" href="#">Privacy Policy</a></p>
      </div>
    </div>
  </footer>
</div>

<?php
include 'includes/footer.php';
?>