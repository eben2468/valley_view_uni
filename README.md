# Valley View University Website

This is a PHP-based website for Valley View University, converted from HTML files to work with XAMPP and MySQL.

## Project Structure

- `includes/` - Contains header, footer, and database connection files
- `assets/` - Images and other static assets (if any)
- All PHP pages for different sections of the university website

## Setup Instructions

1. Place all files in your XAMPP htdocs directory (e.g., `C:\xampp\htdocs\valley_view_uni\`)
2. Start Apache and MySQL services in XAMPP Control Panel
3. Create the database using the `database_schema.sql` file
4. Update database credentials in `includes/db_connect.php` if needed
5. Access the website at `http://localhost/valley_view_uni/`

## Key Features

- Responsive design using Tailwind CSS
- PHP-based dynamic pages with consistent header/footer
- Database integration for contact form submissions
- Organized navigation between all university sections
- Mobile-friendly layout

## Database Setup

1. Open phpMyAdmin at `http://localhost/phpmyadmin/`
2. Create a new database named `valley_view_uni`
3. Import the `database_schema.sql` file to create tables and sample data

## Customization

- Modify `includes/header.php` and `includes/footer.php` to update site-wide elements
- Update database connection settings in `includes/db_connect.php`
- Add new pages by following the existing PHP file structure

## Support

For any issues or questions, please contact the web development team.