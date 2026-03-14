# Campus Life Pages - Image Upload Guide

## Overview
All five Campus Life page editors now include built-in image upload functionality with security features and validation.

## Features

### ✅ Secure Upload System
- File type validation (JPG, PNG, GIF, WebP only)
- File size limit (5MB maximum)
- Unique filename generation
- Automatic directory creation
- MIME type verification

### ✅ Dual Input Method
Each image field offers two options:
1. **Upload New Image**: Use file input to upload from your computer
2. **Enter Path Manually**: Type the image path directly

### ✅ Image Preview
- Current images display as thumbnails
- Preview updates after upload
- Easy visual confirmation

## How to Upload Images

### Method 1: Upload New Image

1. **Navigate to Admin Panel**
   ```
   admin/manage_campus_life_pages.php
   ```

2. **Select Page Tab**
   - Click the tab for the page you want to edit

3. **Find Image Field**
   - Look for sections like "Hero Image" or "Introduction Image"

4. **Upload Image**
   - Click "Choose File" button
   - Select image from your computer
   - Supported formats: JPG, PNG, GIF, WebP
   - Maximum size: 5MB

5. **Save Changes**
   - Click "Update Content" button
   - Image will be uploaded and path saved automatically

### Method 2: Enter Path Manually

1. **Type Image Path**
   - Enter relative path in text field
   - Example: `uploads/campus_life/my-image.jpg`
   - Example: `images/hero-background.png`

2. **Save Changes**
   - Click "Update Content" button

## Image Storage

### Upload Directory
```
uploads/campus_life/
```

All uploaded images are stored in this directory with unique filenames.

### Filename Format
```
{prefix}_{unique_id}_{original_name}
```

Examples:
- `hero_65d4f2a1b3c4e_campus-photo.jpg`
- `intro_65d4f2a1b3c4f_students.png`

## Image Requirements

### File Types
- ✅ JPEG/JPG
- ✅ PNG
- ✅ GIF
- ✅ WebP
- ❌ Other formats not allowed

### File Size
- Maximum: 5MB
- Recommended: 1-2MB for optimal performance
- Compress large images before uploading

### Dimensions
- Hero images: 1920x1080px (recommended)
- Feature images: 1200x800px (recommended)
- Thumbnails: 400x300px (recommended)

## Security Features

### 1. File Type Validation
- MIME type checking
- Extension verification
- Prevents malicious file uploads

### 2. File Size Limits
- 5MB maximum
- Prevents server overload
- Ensures fast page loading

### 3. Unique Filenames
- Prevents file overwrites
- Avoids naming conflicts
- Maintains file organization

### 4. Directory Permissions
- Proper folder permissions (755)
- Secure file storage
- Protected from unauthorized access

## Pages with Image Upload

### 1. Philosophy on Dress
- Hero Image
- Introduction Image

### 2. Accommodation
- Hero Image
- Introduction Image

### 3. Food Services
- Hero Image
- Philosophy Image

### 4. Work Study
- Hero Image
- Overview Image

### 5. Spiritual Life & Development
- Hero Image
- Welcome Image

## Troubleshooting

### Upload Fails
**Problem**: Image won't upload
**Solutions**:
- Check file size (must be under 5MB)
- Verify file type (JPG, PNG, GIF, WebP only)
- Ensure uploads/campus_life/ directory exists
- Check folder permissions (755)

### Image Doesn't Display
**Problem**: Uploaded image not showing
**Solutions**:
- Clear browser cache
- Check image path in database
- Verify file exists in uploads/campus_life/
- Check file permissions

### Path Issues
**Problem**: Manual path not working
**Solutions**:
- Use relative paths (not absolute)
- Start from project root
- Example: `uploads/campus_life/image.jpg`
- Don't include leading slash

## Best Practices

### Image Optimization
1. **Compress Images**
   - Use tools like TinyPNG or ImageOptim
   - Reduce file size without quality loss
   - Faster page loading

2. **Proper Dimensions**
   - Resize images before upload
   - Match recommended dimensions
   - Avoid oversized images

3. **Descriptive Names**
   - Use clear, descriptive filenames
   - Example: `campus-library-exterior.jpg`
   - Avoid generic names like `image1.jpg`

### Organization
1. **Consistent Naming**
   - Use lowercase
   - Separate words with hyphens
   - Be descriptive

2. **Regular Cleanup**
   - Remove unused images
   - Keep directory organized
   - Archive old images

3. **Backup Images**
   - Regular backups of uploads folder
   - Keep original high-res versions
   - Document image sources

## Image Upload API

### Endpoint
```
admin/campus_life_image_upload.php
```

### Method
```
POST (multipart/form-data)
```

### Parameters
```
image: File (required)
```

### Response (JSON)
```json
{
  "success": true,
  "message": "Image uploaded successfully",
  "path": "uploads/campus_life/filename.jpg",
  "filename": "filename.jpg"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description"
}
```

## Directory Structure

```
valley_view_uni/
├── uploads/
│   └── campus_life/
│       ├── hero_65d4f2a1b3c4e_image1.jpg
│       ├── intro_65d4f2a1b3c4f_image2.png
│       └── philosophy_65d4f2a1b3c50_image3.jpg
├── admin/
│   ├── campus_life_image_upload.php
│   └── campus_life_editors/
│       ├── edit_philosophy_on_dress.php
│       ├── edit_accommodation.php
│       ├── edit_food_services.php
│       ├── edit_work_study.php
│       └── edit_sld.php
└── ...
```

## Quick Reference

### Upload Steps
1. Select page tab
2. Find image field
3. Click "Choose File"
4. Select image
5. Click "Update Content"

### Supported Formats
JPG, PNG, GIF, WebP

### Max File Size
5MB

### Upload Directory
`uploads/campus_life/`

### Image Preview
Displays below upload field

---

**Version**: 1.0  
**Last Updated**: February 2026  
**Status**: Production Ready
