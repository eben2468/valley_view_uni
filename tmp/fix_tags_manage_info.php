<?php
$file = 'admin/manage_info_pages.php';
$content = file_get_contents($file);

// We replace htmlspecialchars($xyz) with htmlspecialchars(strip_tags($xyz))
// We'll use a specific replacement to ensure we don't break PHP syntax.
// Since all the variables match an array, string, or simple variable...
// e.g. htmlspecialchars($page_content['hero_badge'] ?? '')
$content = preg_replace(
    '/htmlspecialchars\(\$(page_content|section|item|stat)\[([^\]]+)\]( \?\? [^)]+)?\)/',
    'htmlspecialchars(strip_tags(\$$1[$2]$3))',
    $content
);

file_put_contents($file, $content);
echo "Tags stripped inside htmlspecialchars directly in the code.";
