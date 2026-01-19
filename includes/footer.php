    </div> <!-- Close container div -->
    
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> PawCare - Smart Pet Care Platform. All rights reserved.</p>
            <p>Department of Computer Science - Web Technologies Project</p>
        </div>
    </footer>
    
    <!-- JavaScript Files -->
    <script src="<?php echo BASE_URL; ?>js/validation.js"></script>
    
    <!-- Mobile Menu Toggle Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.getElementById('menuToggle');
        const navMenu = document.getElementById('navMenu');
        
        if (menuToggle && navMenu) {
            menuToggle.addEventListener('click', function() {
                navMenu.classList.toggle('show');
                menuToggle.setAttribute('aria-expanded', 
                    navMenu.classList.contains('show') ? 'true' : 'false');
            });
            
            // Close menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!navMenu.contains(event.target) && !menuToggle.contains(event.target)) {
                    navMenu.classList.remove('show');
                    menuToggle.setAttribute('aria-expanded', 'false');
                }
            });
        }
        
        // Add accessibility features
        document.querySelectorAll('a, button, input, select, textarea').forEach(element => {
            if (!element.hasAttribute('title') && !element.hasAttribute('aria-label')) {
                const text = element.textContent.trim() || element.value || element.placeholder;
                if (text) {
                    element.setAttribute('title', text);
                }
            }
        });
    });
    </script>
    
    <?php if (isset($page_scripts)): ?>
        <?php echo $page_scripts; ?>
    <?php endif; ?>
</body>
</html>