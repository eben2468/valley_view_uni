<?php
$dir = dirname(__DIR__); // points to valley_view_uni
$files = glob($dir . '/*.php');

$updatedCount = 0;

foreach ($files as $file) {
    if (is_dir($file)) continue;
    
    // Skip the ones we already did just in case, though they shouldn't match
    $content = file_get_contents($file);
    $original = $content;

    // Typical h1 formatting replacements
    $content = str_replace(
        'text-6xl sm:text-7xl md:text-8xl lg:text-9xl',
        'text-5xl sm:text-6xl md:text-7xl lg:text-8xl',
        $content
    );
    
    $content = str_replace(
        'text-5xl sm:text-6xl md:text-7xl lg:text-[8rem]',
        'text-4xl sm:text-5xl md:text-6xl lg:text-7xl',
        $content
    );

    // Typical span highlight replacements
    $content = str_replace(
        'text-5xl sm:text-6xl md:text-7xl lg:text-7xl',
        'text-4xl sm:text-5xl md:text-6xl lg:text-6xl',
        $content
    );

    // Typical paragraph description replacements
    $content = str_replace(
        'text-xl sm:text-2xl md:text-3xl',
        'text-lg sm:text-xl md:text-2xl',
        $content
    );
    
    $content = str_replace(
        'text-xl sm:text-3xl md:text-4xl',
        'text-lg sm:text-xl md:text-2xl',
        $content
    );
    
    // Other variations found in pdf_books, online_resources, etc.
    $content = str_replace(
        'text-7xl md:text-9xl',
        'text-6xl md:text-8xl',
        $content
    );
    
    $content = str_replace(
        'text-6xl md:text-9xl',
        'text-5xl md:text-8xl',
        $content
    );

    if ($original !== $content) {
        file_put_contents($file, $content);
        echo "Updated: " . basename($file) . "\n";
        $updatedCount++;
    }
}

echo "Total updated: $updatedCount\n";
