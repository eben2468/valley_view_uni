-- Update Modern Lecture Halls to Radio in the_campus features
UPDATE academic_pages_items 
SET 
    item_title = 'Radio',
    item_description = 'Campus radio station broadcasting news, music, and student programs.',
    item_icon = 'radio'
WHERE page_key = 'the_campus' 
  AND section_key = 'features' 
  AND item_title = 'Modern Lecture Halls';
