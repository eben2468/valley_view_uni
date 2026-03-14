# Frontend Integration Guide - Campus Life Pages CMS

## Current Status

✅ **Admin Panel**: Fully functional with image upload  
✅ **Database Schema**: Created and ready  
✅ **Helper Functions**: Available in `includes/campus_life_content_helper.php`  
⚠️ **Frontend Pages**: Partially integrated (Philosophy on Dress done, 4 remaining)

## What Was Done

### 1. Philosophy on Dress Page (✅ COMPLETE)
- Updated to fetch content from database
- Dynamic hero section
- Dynamic introduction
- Dynamic CTA section
- Uses helper functions

### 2. Remaining Pages (⚠️ PENDING)
- Accommodation
- Food Services
- Work Study
- SLD (Spiritual Life & Development)

## Testing the Integration

### Step 1: Run Database Test
Navigate to:
```
http://localhost/valley_view_uni/test_campus_life_db.php
```

This will show you:
- Database connection status
- Which tables exist
- Content availability
- Sample data

### Step 2: Run Installation (if needed)
If tables are missing:
```
http://localhost/valley_view_uni/install_campus_life_cms.php
```

### Step 3: Test Philosophy on Dress
```
http://localhost/valley_view_uni/philosophy_on_dress.php
```

This page is now fully dynamic and will show content from the database.

## How to Complete Integration

You have TWO options:

### Option A: Quick Integration (Recommended)
I can quickly update all 4 remaining pages to be dynamic. This will take about 5-10 minutes.

### Option B: Manual Integration
Follow the pattern used in `philosophy_on_dress.php`:

1. **Add at the top of each page:**
```php
<?php
$page_title = "Page Title - Valley View University";
$active_page = "page_key";
include 'includes/header.php';
require_once 'includes/campus_life_content_helper.php';

// Fetch content from database
$content = getPageContent($pdo); // Use appropriate function

// Use default values if no content found
if (!$content) {
    $content = [
        'hero_title' => 'Default Title',
        // ... other defaults
    ];
}
?>
```

2. **Replace static content with dynamic:**
```php
<!-- Before -->
<h1>Static Title</h1>

<!-- After -->
<h1><?php echo htmlspecialchars($content['hero_title']); ?></h1>
```

3. **For images:**
```php
<!-- Before -->
<img src="static/path/image.jpg" alt="Image">

<!-- After -->
<img src="<?php echo htmlspecialchars($content['hero_image']); ?>" alt="Image">
```

## Helper Functions Available

### For Each Page:
```php
// Philosophy on Dress
$content = getPhilosophyOnDressContent($pdo);

// Accommodation
$content = getAccommodationContent($pdo);
$features = getAccommodationFeatures($pdo);

// Food Services
$content = getFoodServicesContent($pdo);
$features = getFoodServicesFeatures($pdo);

// Work Study
$content = getWorkStudyContent($pdo);
$benefits = getWorkStudyBenefits($pdo);
$opportunities = getWorkStudyOpportunities($pdo);

// SLD
$content = getSLDContent($pdo);
$services = getSLDServices($pdo);
$staff = getSLDStaff($pdo);
```

### Utility Functions:
```php
// Parse line-separated items
$items = parseLineItems($text);

// Check if content exists
$exists = hasContent($pdo, 'table_name', 1);
```

## Content Fields by Page

### Philosophy on Dress
- `hero_title`, `hero_subtitle`, `hero_image`
- `intro_heading`, `intro_text`, `intro_image`
- `philosophy_statement`
- `encouraged_items`, `discouraged_items`
- `benefits_text`
- `cta_heading`, `cta_text`
- `status`

### Accommodation
- `hero_title`, `hero_subtitle`, `hero_image`
- `intro_heading`, `intro_text`, `intro_image`
- `facilities_description`
- `room_types_description`
- `application_process`
- `rules_and_regulations`
- `cta_heading`, `cta_text`
- `status`

### Food Services
- `hero_title`, `hero_subtitle`, `hero_image`
- `philosophy_heading`, `philosophy_text`, `philosophy_image`
- `breakfast_time`, `lunch_time`, `dinner_time`
- `meal_plans_description`
- `feedback_heading`, `feedback_text`
- `status`

### Work Study
- `hero_title`, `hero_subtitle`, `hero_image`
- `overview_heading`, `overview_text`, `overview_image`
- `minimum_hours`
- `spouse_policy_text`
- `application_process`
- `cta_heading`, `cta_text`
- `status`

### SLD
- `hero_title`, `hero_subtitle`, `hero_image`
- `welcome_heading`, `welcome_text`, `welcome_image`
- `mission_statement`
- `dean_name`, `dean_title`, `dean_description`
- `cta_heading`, `cta_text`
- `status`

## Common Integration Patterns

### 1. Hero Section
```php
<section class="hero">
    <img src="<?php echo htmlspecialchars($content['hero_image']); ?>" alt="Hero">
    <h1><?php echo htmlspecialchars($content['hero_title']); ?></h1>
    <p><?php echo htmlspecialchars($content['hero_subtitle']); ?></p>
</section>
```

### 2. Text Content
```php
<p><?php echo nl2br(htmlspecialchars($content['intro_text'])); ?></p>
```

### 3. Conditional Display
```php
<?php if (!empty($content['philosophy_statement'])): ?>
    <p><?php echo htmlspecialchars($content['philosophy_statement']); ?></p>
<?php endif; ?>
```

### 4. Lists (line-separated)
```php
<?php
$items = parseLineItems($content['encouraged_items']);
foreach ($items as $item):
?>
    <li><?php echo htmlspecialchars($item); ?></li>
<?php endforeach; ?>
```

## Troubleshooting

### Content Not Showing
1. Check database connection
2. Run `test_campus_life_db.php`
3. Verify tables exist
4. Check content status is 'active'

### Images Not Displaying
1. Verify image path in database
2. Check file exists in uploads folder
3. Use relative paths (not absolute)
4. Clear browser cache

### Database Errors
1. Run installation script
2. Check database credentials
3. Verify table structure
4. Check PHP error logs

## Next Steps

1. ✅ Run `test_campus_life_db.php` to verify setup
2. ⚠️ Complete integration for remaining 4 pages
3. ✅ Test all pages on frontend
4. ✅ Update content via admin panel
5. ✅ Verify changes reflect on frontend

## Quick Commands

### Test Database
```
http://localhost/valley_view_uni/test_campus_life_db.php
```

### Install/Reinstall
```
http://localhost/valley_view_uni/install_campus_life_cms.php
```

### Admin Panel
```
http://localhost/valley_view_uni/admin/manage_campus_life_pages.php
```

### Frontend Pages
```
http://localhost/valley_view_uni/philosophy_on_dress.php
http://localhost/valley_view_uni/accommodation.php
http://localhost/valley_view_uni/food_services.php
http://localhost/valley_view_uni/work_study.php
http://localhost/valley_view_uni/sld.php
```

## Summary

- ✅ 1 of 5 pages integrated (Philosophy on Dress)
- ⚠️ 4 pages remaining
- ✅ All admin functionality working
- ✅ Database schema ready
- ✅ Helper functions available
- ✅ Image upload functional

**Ready to complete the integration? Let me know and I'll update the remaining 4 pages!**
