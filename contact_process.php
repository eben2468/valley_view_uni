<?php
// Include database connection
include 'includes/db_connect.php';

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $inquiry_type = htmlspecialchars(trim($_POST['inquiry-type']));
    $message = htmlspecialchars(trim($_POST['message']));
    
    // Validate required fields
    if (!empty($name) && !empty($email) && !empty($message)) {
        try {
            // Prepare SQL statement to prevent SQL injection
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, inquiry_type, message, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $inquiry_type, $message]);
            
            // Set success message
            $success_message = "Thank you for your message! We'll get back to you soon.";
        } catch (PDOException $e) {
            // Set error message
            $error_message = "Sorry, there was an error processing your request. Please try again.";
        }
    } else {
        $error_message = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Contact Confirmation - Valley View University</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;700;900&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#0A2A4E",
                        "secondary": "#D4AF37",
                        "background-light": "#F8F9FA",
                        "background-dark": "#101022",
                        "text-light": "#0d0d1b",
                        "text-dark": "#f8f8fc",
                    },
                    fontFamily: {
                        "display": ["Lexend", "sans-serif"]
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-text-light dark:text-text-dark">
<div class="relative flex min-h-screen w-full flex-col">
    <header class="sticky top-0 z-50 w-full bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-sm border-b border-gray-200 dark:border-gray-800">
        <div class="container mx-auto px-4">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center gap-4 text-text-light dark:text-text-dark">
                    <div class="text-primary dark:text-secondary size-6">
                        <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" d="M24 4H42V17.3333V30.6667H24V44H6V30.6667V17.3333H24V4Z" fill="currentColor" fill-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold">Valley View University</h2>
                </div>
                <nav class="hidden md:flex items-center gap-8">
                    <a class="text-sm font-medium hover:text-primary dark:hover:text-secondary" href="homepage.php">Home</a>
                    <a class="text-sm font-medium hover:text-primary dark:hover:text-secondary" href="academics.php">Academics</a>
                    <a class="text-sm font-medium hover:text-primary dark:hover:text-secondary" href="admissions.php">Admissions</a>
                    <a class="text-sm font-medium hover:text-primary dark:hover:text-secondary" href="about_us.php">About Us</a>
                    <a class="text-sm font-medium text-primary dark:text-secondary" href="contact_us.php">Contact Us</a>
                </nav>
            </div>
        </div>
    </header>
    
    <main class="flex-grow py-16">
        <div class="container mx-auto px-4 max-w-3xl">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 text-center">
                <?php if (isset($success_message)): ?>
                    <div class="text-green-600 dark:text-green-400 mb-6">
                        <svg class="mx-auto h-16 w-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h2 class="text-2xl font-bold mb-2">Message Sent Successfully!</h2>
                        <p class="text-lg"><?php echo $success_message; ?></p>
                    </div>
                <?php elseif (isset($error_message)): ?>
                    <div class="text-red-600 dark:text-red-400 mb-6">
                        <svg class="mx-auto h-16 w-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h2 class="text-2xl font-bold mb-2">Error Processing Your Request</h2>
                        <p class="text-lg"><?php echo $error_message; ?></p>
                    </div>
                <?php else: ?>
                    <div class="text-yellow-600 dark:text-yellow-400 mb-6">
                        <svg class="mx-auto h-16 w-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <h2 class="text-2xl font-bold mb-2">Invalid Request</h2>
                        <p class="text-lg">Please submit the contact form to send us a message.</p>
                    </div>
                <?php endif; ?>
                
                <div class="mt-8">
                    <a href="contact_us.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Send Another Message
                    </a>
                    <a href="homepage.php" class="ml-4 inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:text-white dark:bg-gray-700 dark:hover:bg-gray-600">
                        Return to Home
                    </a>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="bg-primary text-white/90 mt-16">
        <div class="container mx-auto px-4 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                <div class="flex flex-col gap-4">
                    <a class="flex items-center gap-3" href="homepage.php">
                        <div class="bg-white p-1 rounded-md text-primary size-8">
                            <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                <path clip-rule="evenodd" d="M24 4H42V17.3333V30.6667H24V44H6V30.6667V17.3333H24V4Z" fill="currentColor" fill-rule="evenodd"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white">Valley View University</h2>
                    </a>
                    <p class="text-sm text-white/70">123 University Drive,<br/>Innovation City, ST 12345</p>
                </div>
                <div>
                    <h3 class="font-bold text-white mb-4">Future Students</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a class="text-white/70 hover:text-secondary transition-colors" href="admissions.php">Undergraduate Admissions</a></li>
                        <li><a class="text-white/70 hover:text-secondary transition-colors" href="admissions.php">Graduate Admissions</a></li>
                        <li><a class="text-white/70 hover:text-secondary transition-colors" href="admissions.php#requirements">Financial Aid</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-white mb-4">Current Students</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a class="text-white/70 hover:text-secondary transition-colors" href="student_digital_hub.php">MyVVU Portal</a></li>
                        <li><a class="text-white/70 hover:text-secondary transition-colors" href="academic_calendar.php">Academic Calendar</a></li>
                        <li><a class="text-white/70 hover:text-secondary transition-colors" href="office_of_the_registrar.php">Registrar</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-white mb-4">Resources</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a class="text-white/70 hover:text-secondary transition-colors" href="faculty_encyclopedia.php">For Faculty & Staff</a></li>
                        <li><a class="text-white/70 hover:text-secondary transition-colors" href="alumni_network_page_1.php">For Alumni</a></li>
                        <li><a class="text-white/70 hover:text-secondary transition-colors" href="employment_opportunity.php">Careers at VVU</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 pt-8 border-t border-white/20 text-center text-sm text-white/50">
                <p>© 2024 Valley View University. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
</div>
</body>
</html>