        </main>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        // Toggle sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.main-content').classList.toggle('expanded');
        });
        
        // Close sidebar on mobile
        if (window.innerWidth < 1024) {
            document.querySelector('.sidebar').classList.add('collapsed');
        }
        
        // Topbar dropdowns
        const notificationBtn = document.getElementById('notificationBtn');
        const messageBtn = document.getElementById('messageBtn');
        
        if (notificationBtn) {
            notificationBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                this.closest('.topbar-dropdown').classList.toggle('active');
                document.getElementById('messageBtn')?.closest('.topbar-dropdown')?.classList.remove('active');
            });
        }
        
        if (messageBtn) {
            messageBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                this.closest('.topbar-dropdown').classList.toggle('active');
                document.getElementById('notificationBtn')?.closest('.topbar-dropdown')?.classList.remove('active');
            });
        }
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function() {
            document.querySelectorAll('.topbar-dropdown.active').forEach(function(dropdown) {
                dropdown.classList.remove('active');
            });
        });
        
        // Prevent closing when clicking inside dropdown
        document.querySelectorAll('.topbar-dropdown-menu').forEach(function(menu) {
            menu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>
