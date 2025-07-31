/**
 * Sidebar JavaScript functionality
 * Handles mobile toggle, active states, and responsive behavior
 */

class SidebarManager {
    constructor() {
        this.sidebar = document.getElementById('sidebar');
        this.sidebarToggle = document.querySelector('.sidebar-toggle');
        this.backdrop = null;
        this.init();
    }

    init() {
        this.createBackdrop();
        this.bindEvents();
        this.handleResponsive();
    }

    createBackdrop() {
        this.backdrop = document.createElement('div');
        this.backdrop.className = 'sidebar-backdrop';
        document.body.appendChild(this.backdrop);
    }

    bindEvents() {
        // Toggle sidebar on mobile
        if (this.sidebarToggle) {
            this.sidebarToggle.addEventListener('click', () => {
                this.toggleSidebar();
            });
        }

        // Close sidebar when clicking backdrop
        if (this.backdrop) {
            this.backdrop.addEventListener('click', () => {
                this.closeSidebar();
            });
        }

        // Handle window resize
        window.addEventListener('resize', () => {
            this.handleResponsive();
        });

        // Close sidebar on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isSidebarOpen()) {
                this.closeSidebar();
            }
        });

        // Update active nav links
        this.updateActiveNavLinks();
    }

    toggleSidebar() {
        if (this.isSidebarOpen()) {
            this.closeSidebar();
        } else {
            this.openSidebar();
        }
    }

    openSidebar() {
        if (this.sidebar) {
            this.sidebar.classList.add('show');
        }
        if (this.backdrop) {
            this.backdrop.classList.add('show');
        }
        document.body.style.overflow = 'hidden';
    }

    closeSidebar() {
        if (this.sidebar) {
            this.sidebar.classList.remove('show');
        }
        if (this.backdrop) {
            this.backdrop.classList.remove('show');
        }
        document.body.style.overflow = '';
    }

    isSidebarOpen() {
        return this.sidebar && this.sidebar.classList.contains('show');
    }

    handleResponsive() {
        const isDesktop = window.innerWidth >= 768;

        if (isDesktop) {
            this.closeSidebar();
            document.body.style.overflow = '';
        }
    }

    updateActiveNavLinks() {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-link');

        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && (currentPath === href || currentPath.startsWith(href + '/'))) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }

    // Public method to programmatically set active nav item
    setActiveNavItem(route) {
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === route) {
                link.classList.add('active');
            }
        });
    }

    // Method to highlight nav section
    highlightSection(sectionTitle) {
        const sections = document.querySelectorAll('.menu-section');
        sections.forEach(section => {
            const title = section.querySelector('.menu-section-title');
            if (title && title.textContent.trim() === sectionTitle) {
                section.style.backgroundColor = '#f9fafb';
                setTimeout(() => {
                    section.style.backgroundColor = '';
                }, 2000);
            }
        });
    }
}

// Utility functions
const SidebarUtils = {
    // Smooth scroll to top when clicking nav links
    smoothScrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    },

    // Add loading state to nav links
    addLoadingState(link) {
        const icon = link.querySelector('i');
        if (icon) {
            icon.className = 'fas fa-spinner fa-spin';
        }
        link.style.opacity = '0.7';
    },

    // Remove loading state from nav links
    removeLoadingState(link) {
        const icon = link.querySelector('i');
        if (icon) {
            // Restore original icon (you might want to store this)
            icon.className = icon.dataset.originalClass || 'fas fa-home';
        }
        link.style.opacity = '1';
    },

    // Add notification badge to nav item
    addNotificationBadge(navLink, count = 1) {
        let badge = navLink.querySelector('.notification-badge');
        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'notification-badge';
            badge.style.cssText = `
                position: absolute;
                top: 8px;
                right: 8px;
                background: #ef4444;
                color: white;
                border-radius: 50%;
                width: 18px;
                height: 18px;
                font-size: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
            `;
            navLink.style.position = 'relative';
            navLink.appendChild(badge);
        }
        badge.textContent = count > 99 ? '99+' : count;
    },

    // Remove notification badge
    removeNotificationBadge(navLink) {
        const badge = navLink.querySelector('.notification-badge');
        if (badge) {
            badge.remove();
        }
    }
};

// Initialize sidebar when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.sidebarManager = new SidebarManager();

    // Add click handlers for nav links
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Add loading state (optional)
            // SidebarUtils.addLoadingState(this);

            // Close sidebar on mobile after clicking a link
            if (window.innerWidth < 768) {
                setTimeout(() => {
                    window.sidebarManager.closeSidebar();
                }, 150);
            }
        });
    });
});

// Export for use in other scripts
window.SidebarManager = SidebarManager;
window.SidebarUtils = SidebarUtils;
