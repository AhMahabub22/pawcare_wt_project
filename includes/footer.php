    </div>
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> PawCare - Smart Pet Care Platform. All rights reserved.</p>
            <p>Department of Computer Science - Web Technologies Project</p>
        </div>
    </footer>
    
    <script src="js/validation.js"></script>
    <?php if (isset($page_scripts)): ?>
        <?php echo $page_scripts; ?>
    <?php endif; ?>
</body>
</html>