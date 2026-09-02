<?php
$pageTitle = "VVU Anthem - Valley View University";
$activePage = "student_life";
require_once 'includes/db_connect.php';
require_once 'includes/upload_helper.php';

// Fetch content from database
$hero = $pdo->query("SELECT * FROM anthem_hero WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$stanzas = $pdo->query("SELECT * FROM anthem_stanzas WHERE is_active=1 ORDER BY display_order ASC")->fetchAll();
$video = $pdo->query("SELECT * FROM anthem_video WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$about = $pdo->query("SELECT * FROM anthem_about WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();
$cta = $pdo->query("SELECT * FROM anthem_cta WHERE is_active=1 ORDER BY id DESC LIMIT 1")->fetch();

include 'includes/header.php';
?>

<style>
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
    @keyframes musicNote {
        0% { transform: translateY(0) rotate(0deg); opacity: 1; }
        50% { transform: translateY(-15px) rotate(10deg); opacity: 0.8; }
        100% { transform: translateY(0) rotate(0deg); opacity: 1; }
    }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
    .animate-float { animation: float 4s ease-in-out infinite; }
    .animate-music-note { animation: musicNote 2s ease-in-out infinite; }
    .glass {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .dark .glass {
        background: rgba(31, 41, 55, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .verse-card {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .verse-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, rgba(251, 191, 36, 0.05) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .verse-card:hover::before {
        opacity: 1;
    }
    .verse-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }
    .verse-number {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
    }
    .lyrics-line, .anthem-content-wrapper p {
        font-size: 1.4rem;
        line-height: 2;
        letter-spacing: 0.02em;
    }
    @media (min-width: 768px) {
        .lyrics-line, .anthem-content-wrapper p {
            font-size: 1.5rem;
            line-height: 2.1;
        }
    }
    @media (min-width: 1024px) {
        .lyrics-line, .anthem-content-wrapper p {
            font-size: 1.65rem;
            line-height: 2.2;
        }
    }
    .anthem-content-wrapper strong {
        font-weight: 800;
        color: #1e3a8a; /* deep blue */
    }
    .dark .anthem-content-wrapper strong {
        color: #93c5fd;
    }
</style>

<main class="flex-grow bg-gray-50 dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative min-h-[55vh] flex items-center overflow-hidden bg-gray-900">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="<?php echo strip_tags($hero['hero_image_url'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuCO7K3MdvhJBsjnRN7t5ahbUnpEsN6IBzUuZZwH7CLb_OOZoqM3pwpXrQV7wTMDVY18bMLximB5Zpi0iNvsgzXDtOrZt20qiq3aKc6ohFAZ7FtlLVdEfxa6mSjbk6EnoF25ccqAEmVf4y-AF3Xq6laGg5Oxwl6WoCqTAcdqgl5ZHKssfYqfv0_HJmwgVa0RIAiC8lKcDETXxxgrOLnYn8C_ELq9y7H2k5L_YYT2-KC8QAIpSMdEOtygPw4fv94jht34itrHs6p5i4rl'); ?>" 
                 alt="VVU Choir" class="w-full h-full object-cover animate-slow-zoom opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/40 to-gray-900"></div>
        </div>
        
        <div class="container relative z-10 py-20">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-3 px-8 py-3 mb-8 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-fadeInUp shadow-2xl">
                    <span class="material-symbols-outlined text-yellow-400 text-3xl animate-music-note">music_note</span>
                    <span class="text-lg md:text-xl font-black tracking-widest uppercase text-yellow-400"><?php echo strip_tags($hero['page_subtitle'] ?? 'University Anthem'); ?></span>
                </div>
                
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-tight tracking-tighter text-white mb-8 animate-fadeInUp drop-shadow-2xl" style="animation-delay: 0.1s;">
                    <?php echo strip_tags($hero['hero_title'] ?? 'VVU Anthem'); ?> <br>
                    <span class="text-3xl sm:text-4xl md:text-5xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-200 to-yellow-500 block mt-3"><?php echo strip_tags($hero['hero_subtitle'] ?? 'The Spirit of Valley View'); ?></span>
                </h1>
                
                <p class="text-lg sm:text-xl md:text-2xl text-white/90 leading-relaxed max-w-4xl mx-auto animate-fadeInUp font-bold drop-shadow-lg italic" style="animation-delay: 0.2s;">
                    "<?php echo strip_tags($hero['hero_description'] ?? 'Through Excellence, Integrity and Service; Valley View sends us to the world with peace.'); ?>"
                </p>
            </div>
        </div>
    </section>

    <!-- Anthem Lyrics Section -->
    <section class="py-20 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-16">
                <h2 class="text-4xl sm:text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-6">Official Anthem Lyrics</h2>
                <div class="h-2 w-32 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">Composed by Pastor Emmanuel O. Abbey, September 2011</p>
            </div>

            <!-- Horizontal 3-Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10">
                <?php foreach ($stanzas as $stanza): ?>
                <!-- Stanza <?php echo $stanza['stanza_number']; ?> -->
                <div class="verse-card glass p-8 lg:p-10 rounded-3xl shadow-xl border-t-8 border-<?php echo strip_tags($stanza['border_color'] ?? 'blue-600'); ?> flex flex-col h-full">
                    <div class="text-center mb-6">
                        <div class="verse-number inline-flex w-14 h-14 rounded-xl items-center justify-center text-white text-2xl font-black mb-4">
                            <?php echo $stanza['stanza_number']; ?>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white"><?php echo strip_tags($stanza['stanza_title']); ?></h3>
                    </div>
                    <div class="text-gray-700 dark:text-gray-300 space-y-2 flex-grow anthem-content-wrapper">
                        <?php echo $stanza['content']; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Video Section (Below Lyrics) -->
    <section class="py-20 bg-white dark:bg-gray-900">
        <div class="container">
            <?php
            // `video_url` holds either an audio recording or a video clip. The
            // poster image is the section's artwork either way — a video uses it
            // as its poster frame, audio shows it behind the player.
            $anthem_title  = trim(strip_tags((string) ($video['section_title'] ?? ''))) ?: 'Listen to the Anthem';
            $anthem_desc   = trim(strip_tags((string) ($video['section_description'] ?? ''))) ?: 'Experience the official VVU Anthem - Vocal Path Cover';
            $anthem_media  = trim(strip_tags((string) ($video['video_url'] ?? ''))) ?: 'uploads/vvu-anthem-video.mp4';
            $anthem_poster = trim(strip_tags((string) ($video['video_poster_url'] ?? ''))) ?: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCO7K3MdvhJBsjnRN7t5ahbUnpEsN6IBzUuZZwH7CLb_OOZoqM3pwpXrQV7wTMDVY18bMLximB5Zpi0iNvsgzXDtOrZt20qiq3aKc6ohFAZ7FtlLVdEfxa6mSjbk6EnoF25ccqAEmVf4y-AF3Xq6laGg5Oxwl6WoCqTAcdqgl5ZHKssfYqfv0_HJmwgVa0RIAiC8lKcDETXxxgrOLnYn8C_ELq9y7H2k5L_YYT2-KC8QAIpSMdEOtygPw4fv94jht34itrHs6p5i4rl';
            $anthem_mime   = vvu_media_mime($anthem_media);
            ?>
            <div class="max-w-4xl mx-auto text-center mb-12">
                <h2 class="text-4xl sm:text-5xl font-black text-gray-900 dark:text-white mb-4"><?php echo htmlspecialchars($anthem_title); ?></h2>
                <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium"><?php echo htmlspecialchars($anthem_desc); ?></p>
            </div>
            <div class="max-w-4xl mx-auto relative aspect-video rounded-3xl overflow-hidden shadow-2xl shadow-primary/20 dark:shadow-primary/10 bg-black">
                <?php if (vvu_media_is_audio($anthem_media)): ?>
                    <img
                        src="<?php echo htmlspecialchars($anthem_poster); ?>"
                        alt="<?php echo htmlspecialchars($anthem_title); ?>"
                        class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/25 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 p-5 sm:p-8">
                        <audio class="w-full" controls preload="metadata">
                            <source src="<?php echo htmlspecialchars($anthem_media); ?>"<?php echo $anthem_mime ? ' type="' . htmlspecialchars($anthem_mime) . '"' : ''; ?>>
                            Your browser does not support the audio tag.
                            <a href="<?php echo htmlspecialchars($anthem_media); ?>" class="underline">Download the anthem</a>.
                        </audio>
                    </div>
                <?php else: ?>
                    <video
                        class="w-full h-full"
                        controls
                        poster="<?php echo htmlspecialchars($anthem_poster); ?>">
                        <source src="<?php echo htmlspecialchars($anthem_media); ?>"<?php echo $anthem_mime ? ' type="' . htmlspecialchars($anthem_mime) . '"' : ''; ?>>
                        Your browser does not support the video tag.
                    </video>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- About the Anthem Section -->
    <section class="py-20 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-16">
                <h2 class="text-4xl sm:text-5xl md:text-6xl font-black text-gray-900 dark:text-white mb-6">About the Anthem</h2>
                <div class="h-2 w-32 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">Learn about the history and the talented composer behind the VVU anthem.</p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-14 max-w-7xl mx-auto">
                <!-- History Card -->
                <div class="group glass p-10 lg:p-14 rounded-3xl shadow-2xl border-t-8 border-blue-600 hover:-translate-y-2 transition-all duration-300">
                    <div class="inline-flex w-24 h-24 rounded-2xl bg-blue-600 items-center justify-center text-white shadow-xl mb-10 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">history_edu</span>
                    </div>
                    <h3 class="text-4xl lg:text-5xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($about['history_title'] ?? 'History of the Anthem'); ?></h3>
                    <div class="text-2xl lg:text-3xl text-gray-700 dark:text-gray-300 leading-relaxed mb-6 space-y-8">
                        <?php if (!empty($about['history_content'])): ?>
                            <p><?php echo nl2br(strip_tags($about['history_content'])); ?></p>
                        <?php else: ?>
                            <p>
                                The Valley View University anthem was composed by <strong class="text-blue-600 dark:text-blue-400">Pastor Emmanuel O. Abbey</strong> in September 2011. This inspiring piece encapsulates the university's enduring values of excellence, integrity, and service.
                            </p>
                            <p>
                                The anthem beautifully expresses VVU's commitment to providing balanced education grounded in Christian principles, training students to serve humanity and bring hope and light to the world.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Composer Card -->
                <div class="group glass p-10 lg:p-14 rounded-3xl shadow-2xl border-t-8 border-yellow-500 hover:-translate-y-2 transition-all duration-300">
                    <div class="inline-flex w-24 h-24 rounded-2xl bg-yellow-500 items-center justify-center text-white shadow-xl mb-10 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-5xl text-white">person</span>
                    </div>
                    <h3 class="text-4xl lg:text-5xl font-black text-gray-900 dark:text-white mb-8"><?php echo strip_tags($about['composer_title'] ?? 'About the Composer'); ?></h3>
                    <div class="text-2xl lg:text-3xl text-gray-700 dark:text-gray-300 leading-relaxed mb-6 space-y-8">
                        <?php if (!empty($about['composer_content'])): ?>
                            <p><?php echo nl2br(strip_tags($about['composer_content'])); ?></p>
                        <?php else: ?>
                            <p>
                                <strong class="text-yellow-600 dark:text-yellow-400">Pastor Emmanuel O. Abbey</strong> crafted this anthem with deep relevance for the university's mission and vision. His composition masterfully weaves together themes of faith, education, and service.
                            </p>
                            <p>
                                The anthem has become a cherished symbol of VVU's identity, sung with pride at graduation ceremonies, convocations, and other significant university events.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <?php if ($cta): ?>
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 bg-blue-900"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-yellow-500/10 rounded-full blur-[120px] -mr-60 -mt-60"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-500/10 rounded-full blur-[120px] -ml-60 -mb-60"></div>
        
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <div class="flex justify-center mb-8">
                    <span class="material-symbols-outlined text-7xl text-yellow-400 animate-float">music_note</span>
                </div>
                <h2 class="text-5xl sm:text-6xl md:text-7xl font-black text-white mb-8 leading-tight tracking-tight">
                    <?php echo strip_tags($cta['title_line_1'] ?? ''); ?> <br><span class="text-yellow-400 text-6xl sm:text-5xl md:text-6xl lg:text-7xl font-medium block mt-2"><?php echo strip_tags($cta['title_line_2'] ?? ''); ?></span>
                </h2>
                <p class="text-lg sm:text-xl md:text-2xl text-blue-100 mb-12 max-w-4xl mx-auto leading-relaxed font-medium">
                    <?php echo strip_tags($cta['description'] ?? ''); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center">
                    <a href="<?php echo strip_tags($cta['btn1_url'] ?? '#'); ?>" class="px-10 py-5 bg-yellow-400 hover:bg-yellow-300 text-blue-900 text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl"><?php echo strip_tags($cta['btn1_icon'] ?? 'download'); ?></span>
                        <?php echo strip_tags($cta['btn1_text'] ?? ''); ?>
                    </a>
                    <a href="<?php echo strip_tags($cta['btn2_url'] ?? '#'); ?>" class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white text-xl font-bold rounded-2xl transition-all backdrop-blur-md border-2 border-white/30 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined text-3xl"><?php echo strip_tags($cta['btn2_icon'] ?? 'share'); ?></span>
                        <?php echo strip_tags($cta['btn2_text'] ?? ''); ?>
                    </a>
                </div>
                
                <div class="mt-20 grid grid-cols-1 sm:grid-cols-3 gap-10 border-t border-white/10 pt-16">
                    <div>
                        <div class="text-5xl font-black text-yellow-400 mb-2"><?php echo strip_tags($cta['stat1_value'] ?? ''); ?></div>
                        <div class="text-blue-200 uppercase tracking-widest text-lg font-black"><?php echo strip_tags($cta['stat1_label'] ?? ''); ?></div>
                    </div>
                    <div>
                        <div class="text-5xl font-black text-yellow-400 mb-2"><?php echo strip_tags($cta['stat2_value'] ?? ''); ?></div>
                        <div class="text-blue-200 uppercase tracking-widest text-lg font-black"><?php echo strip_tags($cta['stat2_label'] ?? ''); ?></div>
                    </div>
                    <div>
                        <div class="text-5xl font-black text-yellow-400 mb-2"><?php echo strip_tags($cta['stat3_value'] ?? ''); ?></div>
                        <div class="text-blue-200 uppercase tracking-widest text-lg font-black"><?php echo strip_tags($cta['stat3_label'] ?? ''); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php
include 'includes/footer.php';
?>