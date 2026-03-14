    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded');
            if (typeof bootstrap !== 'undefined') {
                console.log('Bootstrap loaded:', bootstrap.fn ? 'Legacy' : 'Modern');
            } else {
                console.warn('Bootstrap NOT detected!');
            }

            const userDropdown = document.getElementById('userDropdown');
            if (userDropdown && typeof bootstrap !== 'undefined') {
                // Manually initialize dropdown if needed
                new bootstrap.Dropdown(userDropdown);
                userDropdown.addEventListener('click', function() {
                    console.log('User dropdown button clicked!');
                });
            }

            const menuToggle = document.getElementById('menuToggle');
            const body = document.body;
            
            if (menuToggle) {
                menuToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (window.innerWidth <= 992) {
                        body.classList.toggle('sidebar-active');
                        body.classList.remove('sidebar-collapsed');
                    } else {
                        body.classList.toggle('sidebar-collapsed');
                        body.classList.remove('sidebar-active');
                    }
                });
                
                // Close sidebar when clicking outside or on overlay
                document.addEventListener('click', function(e) {
                    if (body.classList.contains('sidebar-active')) {
                        const sidebar = document.getElementById('sidebar');
                        if (sidebar && !sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                            body.classList.remove('sidebar-active');
                        }
                    }
                });

                // Handle window resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 992) {
                        body.classList.remove('sidebar-active');
                    }
                });
            }
        });
    </script>
</body>

</html>
