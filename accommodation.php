<?php
$page_title = "Accommodation - Valley View University";
$active_page = "life_at_vvu";
include 'includes/header.php';
require_once 'includes/campus_life_content_helper.php';

// Fetch content from database
$content = getAccommodationContent($pdo);

// Use default values if no content found
if (!$content) {
    $content = [
        'hero_title' => 'Accommodation',
        'hero_subtitle' => 'Comfortable and secure housing for students on campus',
        'hero_image' => 'images/accommodation_hero.jpg',
        'intro_heading' => 'Campus Housing',
        'intro_text' => 'Valley View University provides quality accommodation facilities for students who wish to live on campus.',
        'intro_image' => '',
        'facilities_description' => 'Our residence halls offer modern amenities and a safe environment.',
        'room_types_description' => 'Various room types available to suit different needs and budgets.',
        'application_process' => 'Apply through the student portal during registration.',
        'rules_and_regulations' => 'All residents must adhere to university housing policies.',
        'cta_heading' => 'Ready to Apply?',
        'cta_text' => 'Contact the Housing Office for more information about accommodation options.'
    ];
}
?>

<style>
    /* Animations from core_values.php */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
    @keyframes slowZoom {
        0% { transform: scale(1); }
        100% { transform: scale(1.1); }
    }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
    .animate-float { animation: float 4s ease-in-out infinite; }
    
    .glass {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .dark .glass {
        background: rgba(31, 41, 55, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .hall-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .hall-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    .amenity-icon {
        transition: all 0.3s ease;
    }
    .group:hover .amenity-icon {
        transform: scale(1.1) rotate(5deg);
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section (Core Values Design) -->
    <section class="relative min-h-[65vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($content['hero_image']); ?>" 
                 alt="VVU Accommodation" class="w-full h-full object-cover animate-slow-zoom opacity-50">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-24">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-10 py-4 mb-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
                    <span class="text-xl md:text-2xl font-black tracking-widest uppercase text-yellow-400">Student Life</span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-none tracking-tighter text-white mb-10 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($content['hero_title']); ?>
                </h1>

                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($content['hero_subtitle']); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Introduction Section -->
    <section class="py-20 md:py-24 bg-white dark:bg-gray-800 relative z-20 -mt-16 mx-auto max-w-[90rem] rounded-t-[2.5rem] shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
        <div class="container mx-auto px-8 md:px-16">
            <div class="text-center max-w-5xl mx-auto mb-20 animate-fadeInUp">
                <span class="inline-block px-5 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-base md:text-lg font-bold rounded-full mb-6 uppercase tracking-wider">
                    Residential Services
                </span>
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 dark:text-white mb-8">
                    <?php echo strip_tags($content['intro_heading']); ?>
                </h2>
                <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 leading-relaxed font-light">
                    <?php echo nl2br(strip_tags($content['intro_text'])); ?>
                </p>
                <?php if (!empty($content['intro_image'])): ?>
                    <div class="mt-14 rounded-3xl overflow-hidden shadow-xl border border-gray-100 dark:border-gray-700">
                        <img src="<?php echo strip_tags($content['intro_image']); ?>" alt="Accommodation Introduction" class="w-full h-auto object-cover max-h-[500px]">
                    </div>
                <?php endif; ?>
            </div>

            <!-- Halls Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16 lg:gap-20 mb-24 max-w-[90rem] mx-auto">
                <?php 
                $halls = getAccommodationHalls($pdo);
                foreach ($halls as $hall): 
                ?>
                <!-- Hall Card -->
                <div class="group bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm hover:shadow-2xl transition-all duration-300 flex flex-col h-full overflow-hidden">
                    <div class="aspect-[4/3] relative flex-shrink-0">
                         <!-- image layer -->
                         <img src="<?php echo strip_tags($hall['image']); ?>" alt="<?php echo strip_tags($hall['title']); ?>" class="w-full h-full object-cover">
                         
                         <!-- Top Overlays -->
                         <div class="absolute top-0 left-0 right-0 p-5 flex justify-between items-start z-10">
                              <div class="flex items-center gap-2">
                                  <!-- Type Badge -->
                                  <span class="px-4 py-1.5 text-sm md:text-base font-medium text-white rounded-full <?php echo $hall['type'] === 'male' ? 'bg-[#da3b45]' : 'bg-[#d88243]'; ?> shadow-sm shadow-black/20">
                                      <?php echo $hall['type'] === 'male' ? 'Men\'s Hall' : 'Women\'s Hall'; ?>
                                  </span>
                                  <!-- Icon Badge -->
                                  <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-[#202758] flex items-center justify-center text-white shadow-sm shadow-black/20">
                                      <span class="material-symbols-outlined text-[1.2rem] text-white"><?php echo strip_tags($hall['icon']); ?></span>
                                  </div>
                              </div>
                              <span class="px-4 md:px-5 py-1.5 text-sm md:text-base font-medium text-white bg-[#202758] rounded-full shadow-sm shadow-black/20">
                                  Residence
                              </span>
                         </div>
                         
                         <!-- Bottom Overlays -->
                         <div class="absolute bottom-0 left-0 right-0 p-5 flex justify-between items-end z-10">
                              <span class="px-5 py-2 text-base md:text-lg font-bold text-white bg-teal-600 rounded-[0.35rem] shadow-sm shadow-black/20 tracking-wider">
                                  Valley View Uni
                              </span>
                              <button class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-white flex items-center justify-center text-[#202758] hover:text-red-500 shadow-md transition-colors">
                                  <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 0;">favorite</span>
                              </button>
                         </div>
                    </div>
                    
                    <div class="p-6 md:p-8 flex-grow flex flex-col bg-white dark:bg-gray-800">
                        <!-- Location -->
                        <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400 mb-2 md:mb-3">
                            <span class="material-symbols-outlined text-[1.2rem]">location_on</span>
                            <span class="text-sm md:text-base font-medium">Accra, Greater Accra</span>
                        </div>
                        
                        <!-- Title -->
                        <h3 class="text-2xl md:text-3xl font-bold text-[#202758] dark:text-white mb-4 leading-tight"><?php echo strip_tags($hall['title']); ?></h3>
                        
                        <!-- Author & Date -->
                        <div class="flex items-center justify-between text-gray-500 dark:text-gray-400 text-sm md:text-base mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 md:w-8 md:h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center overflow-hidden shrink-0">
                                    <span class="material-symbols-outlined text-gray-500 text-sm md:text-base">domain</span>
                                </div>
                                <span class="font-medium whitespace-nowrap overflow-hidden text-ellipsis">Student Life Services</span>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="material-symbols-outlined text-lg md:text-xl">event</span>
                                <span class="font-medium">On Campus</span>
                            </div>
                        </div>
                        
                        <hr class="border-gray-200 dark:border-gray-700 my-2 md:my-4">
                        
                        <!-- Amenities Footer -->
                        <div class="flex flex-wrap items-center gap-4 md:gap-8 mt-4 md:mt-6 text-[#202758] dark:text-gray-300">
                            <?php 
                            $hall_items = parseLineItems($hall['halls_list']);
                            $icons = ['square_foot', 'bed', 'bathtub'];
                            $count = 0;
                            foreach ($hall_items as $idx => $item): 
                                if ($count >= 3) break;
                                // Grab max 3 words for short display to match design layout
                                $words = explode(' ', strip_tags($item));
                                $shortText = count($words) > 3 ? implode(' ', array_slice($words, 0, 3)) . '...' : strip_tags($item);
                                $icon = $icons[$idx % count($icons)];
                            ?>
                            <div class="flex items-center gap-2 text-base md:text-lg font-medium">
                                <span class="material-symbols-outlined text-2xl text-[#202758] dark:text-gray-400"><?php echo $icon; ?></span>
                                <span><?php echo $shortText; ?></span>
                            </div>
                            <?php 
                                $count++;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Off Campus Info -->
            <div class="bg-blue-50 dark:bg-gray-800/50 rounded-3xl p-10 md:p-16 border border-blue-100 dark:border-gray-700 relative overflow-hidden max-w-6xl mx-auto shadow-sm">
                <div class="absolute -right-10 -top-10 w-48 h-48 bg-blue-500 opacity-5 rounded-full"></div>
                <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-yellow-400 opacity-5 rounded-full"></div>
                <div class="flex flex-col md:flex-row gap-8 items-center md:items-start text-center md:text-left relative z-10">
                    <div class="w-24 h-24 rounded-3xl bg-white dark:bg-gray-700 flex items-center justify-center shadow-md shrink-0 border border-gray-100 dark:border-gray-600">
                        <span class="material-symbols-outlined text-5xl text-blue-600 dark:text-blue-400">home_work</span>
                    </div>
                    <div>
                        <h3 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3"><?php echo strip_tags($content['off_campus_heading']); ?></h3>
                        <p class="text-gray-600 dark:text-gray-400 text-xl md:text-2xl leading-relaxed">
                            <?php echo nl2br(strip_tags($content['off_campus_text'])); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dining Services -->
    <section class="py-24 bg-gray-50 dark:bg-gray-900 relative overflow-hidden">
        <div class="container mx-auto max-w-[90rem] px-8 md:px-16">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">
                <div class="order-2 lg:order-1 relative">
                    <div class="absolute -inset-4 bg-green-500/10 rounded-[3rem] blur-2xl"></div>
                    <img src="<?php echo strip_tags($content['dining_image']); ?>" alt="VVU Cafeteria" class="relative rounded-3xl shadow-2xl w-full h-auto object-cover border border-gray-100 dark:border-gray-800 aspect-[4/3]">
                </div>
                
                <div class="order-1 lg:order-2">
                    <div class="inline-flex items-center gap-3 px-5 py-2 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-base md:text-lg font-bold rounded-full mb-8 uppercase tracking-wider">
                        <span class="material-symbols-outlined text-2xl">restaurant</span>
                        <?php echo strip_tags($content['dining_heading']); ?>
                    </div>
                    <h2 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 dark:text-white mb-8 leading-tight">
                        <?php echo strip_tags($content['dining_subheading']); ?>
                    </h2>
                    <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-light leading-relaxed mb-10">
                        <?php echo nl2br(strip_tags($content['dining_text'])); ?>
                    </p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <?php 
                        $dining_items = parseLineItems($content['dining_list']);
                        foreach ($dining_items as $item): 
                        ?>
                        <div class="flex items-center gap-5 p-6 md:p-8 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-shadow">
                            <div class="w-16 h-16 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-green-500 text-2xl">done</span>
                            </div>
                            <p class="text-xl md:text-2xl font-semibold text-gray-700 dark:text-gray-300"><?php echo strip_tags($item); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Amenities Section -->
    <section class="py-24 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-800">
        <div class="container mx-auto max-w-[90rem] px-8 md:px-16">
            <div class="text-center max-w-4xl mx-auto mb-20">
                <span class="inline-flex items-center gap-3 px-5 py-2 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-base md:text-lg font-bold rounded-full mb-6 uppercase tracking-wider">
                    <span class="material-symbols-outlined text-2xl">star</span> Amenities
                </span>
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 dark:text-white mb-6">Facilities & Amenities</h2>
                <p class="text-gray-600 dark:text-gray-400 text-xl md:text-2xl font-light">Everything you need for a comfortable stay on campus.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 md:gap-10">
                <?php 
                $features = getAccommodationFeatures($pdo);
                foreach ($features as $feature): 
                ?>
                <div class="group flex flex-col items-center justify-center text-center p-10 md:p-12 bg-gray-50 dark:bg-gray-800/50 rounded-3xl hover:bg-white dark:hover:bg-gray-700 hover:shadow-xl transition-all duration-300 border border-transparent hover:border-gray-100 dark:hover:border-gray-600">
                    <div class="w-20 h-20 bg-blue-100 dark:bg-blue-900/30 rounded-[1.5rem] flex items-center justify-center text-blue-600 dark:text-blue-400 mb-6 group-hover:-translate-y-2 transition-transform duration-300">
                        <span class="material-symbols-outlined text-4xl"><?php echo strip_tags($feature['icon']); ?></span>
                    </div>
                    <h3 class="text-base md:text-lg lg:text-xl font-bold text-gray-900 dark:text-white uppercase tracking-wider"><?php echo strip_tags($feature['title']); ?></h3>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-32 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900 to-purple-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        
        <div class="container relative z-10 mx-auto max-w-7xl px-8">
            <div class="bg-white/10 backdrop-blur-xl rounded-[4rem] p-16 md:p-24 border border-white/20 text-center">
                <h2 class="text-5xl md:text-6xl lg:text-7xl font-black text-white mb-8"><?php echo strip_tags($content['cta_heading']); ?></h2>
                <p class="text-2xl md:text-3xl text-white/90 mb-16 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($content['cta_text']); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-8 justify-center">
                    <a href="https://admissions.vvu.edu.gh/" class="px-12 py-6 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-2xl font-black rounded-2xl transition-all transform hover:scale-105 shadow-xl flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl text-white">edit_document</span>
                        Apply for Housing
                    </a>
                    <a href="contact_us.php" class="px-12 py-6 bg-white/10 hover:bg-white/20 text-white text-2xl font-black rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-4">
                        <span class="material-symbols-outlined text-3xl text-white">contact_support</span>
                        Contact Us
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include 'includes/footer.php';
?>