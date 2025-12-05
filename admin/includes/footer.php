<?php
/**
 * Admin Footer Component
 * Global Harmony Initiative Admin Dashboard
 */
?>
    <!-- Universal Modal Container -->
    <?php require_once __DIR__ . '/modal-container.php'; ?>
    
    <!-- Bootstrap 5 JS Bundle (Local) -->
    <script src="<?php echo BASE_URL; ?>/admin/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar Toggle Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.admin-sidebar');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 992) {
                    if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                        sidebar.classList.remove('show');
                    }
                }
            });
        }
    });
    </script>
    
    <!-- Admin Modern JavaScript with all packages -->
    <script type="module" src="<?php echo BASE_URL; ?>/dist/js/admin.js"></script>
</body>
</html>

