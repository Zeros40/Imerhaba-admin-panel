            </main>
        </div>
    </div>

    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';
                    alert.style.transition = 'all 0.3s ease';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });

        // Confirm delete actions
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('confirm-delete') || e.target.closest('.confirm-delete')) {
                if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                    e.preventDefault();
                }
            }
        });

        // Mobile menu toggle
        const menuToggle = document.getElementById('mobile-menu-toggle');
        const sidebar = document.querySelector('.sidebar');

        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });
        }
    </script>
</body>
</html>
