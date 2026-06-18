<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
<script>
    // Sidebar Toggle for Mobile
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    function openSidebar() {
        if (sidebar) sidebar.classList.remove('-translate-x-full');
        if (overlay) overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        if (sidebar) sidebar.classList.add('-translate-x-full');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (sidebar.classList.contains('-translate-x-full')) {
                openSidebar();
            } else {
                closeSidebar();
            }
        });
    }

    // Close sidebar on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeSidebar();
    });

    // Handle responsive: auto-show sidebar on large screens
    const mql = window.matchMedia('(min-width: 1024px)');
    function handleResize(e) {
        if (e.matches) {
            // Desktop: always show sidebar
            if (sidebar) sidebar.classList.remove('-translate-x-full');
            if (overlay) overlay.classList.remove('active');
            document.body.style.overflow = '';
        } else {
            // Mobile: hide sidebar by default
            if (sidebar) sidebar.classList.add('-translate-x-full');
        }
    }
    mql.addEventListener('change', handleResize);
</script>
</body>
</html>