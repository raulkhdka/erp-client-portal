/**
 * Global Nepali Date Picker Helper
 * This utility provides easy initialization of Nepali Date Pickers
 */
class NepaliDatePickerHelper {
    constructor() {
        this.defaultOptions = {
            ndpYear: true,
            ndpMonth: true,
            ndpYearCount: 10,
            disableBefore: new Date(),
            // Enable mini English dates
            miniEnglishDates: true,
            // Set date format to YYYY-MM-DD
            dateFormat: "YYYY-MM-DD",
            onChange: function() {
                // Clear any validation errors when date is selected
                $(this).removeClass('is-invalid');
            }
        };
        this.darkModeClass = 'ndp-dark-theme';
        this.observers = new Map(); // Track observers for cleanup
        this.activeInput = null; // Track the currently active input
        this.setupDynamicContentObserver(); // New: Observe dynamic content
    }

    /**
     * Set up MutationObserver for dynamic content in modals
     */
    setupDynamicContentObserver() {
        // Observe changes in elements with class .modal-body or specific containers
        const modalBodies = document.querySelectorAll('.modal-body, #editTaskContent');
        modalBodies.forEach(container => {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach(node => {
                            if (node.nodeType === Node.ELEMENT_NODE) {
                                // Check for new inputs with nepali-date class or data attribute
                                const inputs = node.querySelectorAll('.nepali-date, [data-nepali-date]');
                                inputs.forEach(input => {
                                    this.init(input);
                                });
                            }
                        });
                    }
                });
            });

            observer.observe(container, {
                childList: true,
                subtree: true
            });

            // Store observer for cleanup
            this.observers.set(container, observer);
        });
    }

    /**
     * Find the modal container for an element
     * @param {HTMLElement} element - The input element
     * @returns {string|null} - The modal ID with # prefix or null
     */
    findModalContainer(element) {
        const modalSelectors = [
            '.modal',
            '[role="dialog"]',
            '.modal-content',
            '.modal-body',
            '[class*="modal"]',
            '[id*="modal"]'
        ];

        let parent = element.parentElement;
        while (parent && parent !== document.body) {
            for (const selector of modalSelectors) {
                if (parent.matches(selector)) {
                    // Look for the modal ID in the parent or its ancestors
                    let modal = parent;
                    while (modal && modal !== document.body) {
                        if (modal.id && modal.classList.contains('modal')) {
                            return `#${modal.id}`;
                        }
                        modal = modal.parentElement;
                    }
                }
            }
            parent = parent.parentElement;
        }
        return null;
    }

    /**
     * Initialize date picker for a single element
     * @param {string|HTMLElement} element - CSS selector or DOM element
     * @param {Object} customOptions - Custom options to override defaults
     */
    init(element, customOptions = {}) {
        const el = typeof element === 'string' ? document.getElementById(element) || document.querySelector(element) : element;

        if (!el) {
            console.warn(`Nepali Date Picker: Element not found - ${element}`);
            return;
        }

        // Make input readonly to prevent manual typing
        el.setAttribute('readonly', true);

        // Merge default options with custom options
        const options = { ...this.defaultOptions, ...customOptions };

        // Check for modal context and set container if applicable
        const modalContainer = this.findModalContainer(el);
        if (modalContainer) {
            options.container = modalContainer;
        }

        // Check for dark mode based on CSS class
        const isDarkMode = el.classList.contains('ndp-dark-mode') ||
                          el.dataset.mode === 'dark' ||
                          document.body.classList.contains('ndp-dark-mode') ||
                          document.documentElement.classList.contains('ndp-dark-mode');

        // Remove the mode property as it might not be supported by the original library
        delete options.mode;

        try {
            // Initialize the date picker
            if (typeof el.nepaliDatePicker === 'function') {
                el.nepaliDatePicker(options);
            } else if (typeof el.NepaliDatePicker === 'function') {
                el.NepaliDatePicker(options);
            } else if (typeof window.nepaliDatePicker === 'function') {
                window.nepaliDatePicker(el, options);
            } else if (typeof $ !== 'undefined' && typeof $.fn.nepaliDatePicker === 'function') {
                $(el).nepaliDatePicker(options);
            } else {
                console.error('Nepali Date Picker library not found. Make sure the library is loaded.');
                return;
            }

            // Apply dark mode and positioning fixes
            this.setupCalendarEnhancements(el, isDarkMode);

        } catch (error) {
            console.error('Error initializing Nepali Date Picker:', error);
        }
    }

    /**
     * Setup calendar enhancements including dark mode and positioning
     * @param {HTMLElement} element - The input element
     * @param {boolean} isDarkMode - Whether dark mode should be applied
     */
    setupCalendarEnhancements(element, isDarkMode) {
        // Clean up any existing observer
        if (this.observers.has(element)) {
            this.observers.get(element).disconnect();
        }

        // Update active input on click or focus
        element.addEventListener('click', () => {
            this.activeInput = element;
            setTimeout(() => this.detectAndStyleCalendar(isDarkMode, element), 100);
            setTimeout(() => this.detectAndStyleCalendar(isDarkMode, element), 300);
        });

        element.addEventListener('focus', () => {
            this.activeInput = element;
            setTimeout(() => this.detectAndStyleCalendar(isDarkMode, element), 100);
            setTimeout(() => this.detectAndStyleCalendar(isDarkMode, element), 300);
        });

        // Clear active input on blur with delay to allow calendar interaction
        element.addEventListener('blur', () => {
            setTimeout(() => {
                if (this.activeInput === element) {
                    this.activeInput = null;
                }
            }, 500);
        });

        // Create observer for calendar detection
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            this.processCalendarNodes(node, isDarkMode, element);
                        }
                    });
                }
            });
        });

        // Start observing
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        // Store observer for cleanup
        this.observers.set(element, observer);
    }

    /**
     * Process calendar nodes for styling
     * @param {Node} node - The DOM node to process
     * @param {boolean} isDarkMode - Whether to apply dark mode
     * @param {HTMLElement} inputElement - The input element
     */
    processCalendarNodes(node, isDarkMode, inputElement) {
        const calendarSelectors = [
            '.datepicker',
            '.ndp-calendar',
            '.nepali-calendar',
            '.nepali-datepicker',
            '.nepali-datepicker-calendar',
            '.nepali-date-picker',
            '.ndp-wrapper',
            '.calendar-wrapper',
            '.ndp-container',
            '[class*="datepicker"]',
            '[class*="nepali"]',
            '[id*="datepicker"]'
        ];

        // Check if the node itself is a calendar
        const isCalendar = calendarSelectors.some(selector => {
            try {
                return node.matches && node.matches(selector);
            } catch (e) {
                return false;
            }
        });

        if (isCalendar && this.activeInput === inputElement) {
            this.styleCalendar(node, isDarkMode, inputElement);
        }

        // Check for calendar children
        calendarSelectors.forEach(selector => {
            try {
                const calendars = node.querySelectorAll ? node.querySelectorAll(selector) : [];
                calendars.forEach(calendar => {
                    if (this.activeInput === inputElement) {
                        this.styleCalendar(calendar, isDarkMode, inputElement);
                    }
                });
            } catch (e) {
                // Ignore selector errors
            }
        });
    }

    /**
     * Detect and style existing calendars
     * @param {boolean} isDarkMode - Whether to apply dark mode
     * @param {HTMLElement} inputElement - The input element
     */
    detectAndStyleCalendar(isDarkMode, inputElement) {
        if (this.activeInput !== inputElement) return; // Only process for active input

        const calendarSelectors = [
            '.datepicker',
            '.ndp-calendar',
            '.nepali-calendar',
            '.nepali-datepicker',
            '.nepali-datepicker-calendar',
            '.nepali-date-picker',
            '.ndp-wrapper',
            '.calendar-wrapper',
            '.ndp-container',
            '[class*="datepicker"]',
            '[id*="datepicker"]'
        ];

        calendarSelectors.forEach(selector => {
            try {
                const calendars = document.querySelectorAll(selector);
                calendars.forEach(calendar => {
                    this.styleCalendar(calendar, isDarkMode, inputElement);
                });
            } catch (e) {
                // Ignore selector errors
            }
        });
    }

    /**
     * Apply styling to a calendar element
     * @param {HTMLElement} calendar - The calendar element
     * @param {boolean} isDarkMode - Whether to apply dark mode
     * @param {HTMLElement} inputElement - The input element
     */
    styleCalendar(calendar, isDarkMode, inputElement) {
        if (!calendar || calendar.dataset.styled === 'true') {
            return;
        }

        // Mark as styled to avoid duplicate processing
        calendar.dataset.styled = 'true';

        // Apply dark mode
        if (isDarkMode) {
            calendar.classList.add(this.darkModeClass);
            // Also add to parent containers
            let parent = calendar.parentElement;
            while (parent && parent !== document.body) {
                if (parent.classList.contains('datepicker') ||
                    parent.classList.contains('calendar') ||
                    parent.id && parent.id.includes('datepicker')) {
                    parent.classList.add(this.darkModeClass);
                }
                parent = parent.parentElement;
            }
        }

        // Mark Saturday (7th child) as holiday with red color
        const days = calendar.querySelectorAll('td, th');
        days.forEach((day, index) => {
            if ((index + 1) % 7 === 0) { // 7th child (Saturday)
                day.classList.add('ndp-saturday-holiday');
            }
        });

        // Fix positioning (special handling for hire_date)
        if (inputElement.id === 'hire_date') {
            this.fixCalendarPositionForHireDate(calendar, inputElement);
        } else if (this.activeInput === inputElement) {
            this.fixCalendarPosition(calendar, inputElement);
        }
    }

    /**
     * Fix calendar positioning relative to input (general case)
     * @param {HTMLElement} calendar - The calendar element
     * @param {HTMLElement} inputElement - The input element
     */
    fixCalendarPosition(calendar, inputElement) {
        // Apply positioning styles
        calendar.style.position = 'absolute';
        calendar.style.zIndex = '9999';
        const offset = parseInt(inputElement.dataset.offset || 0); // Use data-offset or 0

        // Function to update position
        const updatePosition = () => {
            if (this.activeInput !== inputElement) return; // Skip if not active
            const rect = inputElement.getBoundingClientRect();
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

            // Position just below the input, adjusted for scroll and offset
            calendar.style.top = (rect.bottom + scrollTop + offset) + 'px';
            calendar.style.left = (rect.left + scrollLeft) + 'px';

            // Ensure calendar doesn't go off screen horizontally
            const calendarRect = calendar.getBoundingClientRect();
            if (calendarRect.right > window.innerWidth) {
                calendar.style.left = (rect.right + scrollLeft - calendar.offsetWidth) + 'px';
            }
        };

        // Initial position
        updatePosition();

        // Update position on scroll and resize
        window.addEventListener('scroll', updatePosition, { passive: true });
        window.addEventListener('resize', updatePosition, { passive: true });

        // Store event listeners for cleanup
        if (!this.observers.has(inputElement)) {
            this.observers.set(inputElement, []);
        }
        this.observers.get(inputElement).push({ event: 'scroll', handler: updatePosition });
        this.observers.get(inputElement).push({ event: 'resize', handler: updatePosition });
    }

    /**
     * Fix calendar positioning for hire_date with scroll container awareness
     * @param {HTMLElement} calendar - The calendar element
     * @param {HTMLElement} inputElement - The input element
     */
    fixCalendarPositionForHireDate(calendar, inputElement) {
        // Apply positioning styles
        calendar.style.position = 'absolute';
        calendar.style.zIndex = '9999';

        // Function to update position
        const updatePosition = () => {
            if (this.activeInput !== inputElement) return; // Skip if not active
            const rect = inputElement.getBoundingClientRect();
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

            // Position just below the input, adjusted for scroll
            calendar.style.top = (rect.bottom + scrollTop + 5) + 'px'; // Small offset
            calendar.style.left = (rect.left + scrollLeft) + 'px';

            // Ensure calendar doesn't go off screen horizontally
            const calendarRect = calendar.getBoundingClientRect();
            if (calendarRect.right > window.innerWidth) {
                calendar.style.left = (rect.right + scrollLeft - calendar.offsetWidth) + 'px';
            }
        };

        // Initial position
        updatePosition();

        // Update position on scroll and resize
        window.addEventListener('scroll', updatePosition, { passive: true });
        window.addEventListener('resize', updatePosition, { passive: true });

        // Ensure calendar moves with input if input is in a scrolled container
        const parentContainer = inputElement.closest('.form-scroll') || document.body;
        const observer = new MutationObserver(() => updatePosition());
        observer.observe(parentContainer, { attributes: true, childList: true, subtree: true });
        this.observers.set(inputElement, observer);
    }

    /**
     * Initialize date pickers for multiple elements using class selector
     * @param {string} className - CSS class name (without dot)
     * @param {Object} customOptions - Custom options to override defaults
     */
    initByClass(className, customOptions = {}) {
        const elements = document.querySelectorAll(`.${className}`);
        elements.forEach(element => {
            this.init(element, customOptions);
        });
    }

    /**
     * Initialize date pickers for elements with data-nepali-date attribute
     * @param {Object} customOptions - Custom options to override defaults
     */
    initByAttribute(customOptions = {}) {
        const elements = document.querySelectorAll('[data-nepali-date]');
        elements.forEach(element => {
            // Get custom options from data attributes if any
            const dataOptions = this.getDataOptions(element);
            const mergedOptions = { ...customOptions, ...dataOptions };
            this.init(element, mergedOptions);
        });
    }

    /**
     * Extract options from data attributes
     * @param {HTMLElement} element
     * @returns {Object}
     */
    getDataOptions(element) {
        const options = {};

        if (element.dataset.disablePast === 'false') {
            options.disableBefore = null;
        }

        if (element.dataset.disableFuture === 'true') {
            options.disableAfter = new Date();
        }

        if (element.dataset.yearCount) {
            options.ndpYearCount = parseInt(element.dataset.yearCount);
        }

        if (element.dataset.miniEnglish === 'false') {
            options.miniEnglishDates = false;
        }

        if (element.dataset.dateFormat) {
            options.dateFormat = element.dataset.dateFormat;
        }

        if (element.dataset.mode) {
            options.mode = element.dataset.mode;
        }

        return options;
    }

    /**
     * Auto-initialize all date pickers on page load
     */
    autoInit() {
        // Initialize elements with 'nepali-date' class
        this.initByClass('nepali-date');

        // Initialize elements with data-nepali-date attribute
        this.initByAttribute();

        // Handle dynamic content in modals (optional, as MutationObserver handles this)
        if (typeof $ !== 'undefined') {
            $(document).on('shown.bs.modal', '.modal', (e) => {
                const modal = e.target;
                const inputs = modal.querySelectorAll('.nepali-date, [data-nepali-date]');
                inputs.forEach(input => {
                    this.init(input);
                });
            });
        } else {
            document.addEventListener('shown.bs.modal', (e) => {
                const modal = e.target;
                const inputs = modal.querySelectorAll('.nepali-date, [data-nepali-date]');
                inputs.forEach(input => {
                    this.init(input);
                });
            });
        }
    }

    /**
     * Toggle dark mode for all active date pickers
     * @param {boolean} enable - Whether to enable dark mode
     */
    toggleDarkMode(enable = true) {
        const calendarSelectors = [
            '.datepicker',
            '.ndp-calendar',
            '.nepali-calendar',
            '.nepali-datepicker',
            '.nepali-datepicker-calendar',
            '.nepali-date-picker',
            '.ndp-wrapper',
            '.calendar-wrapper',
            '.ndp-container'
        ];

        calendarSelectors.forEach(selector => {
            try {
                const calendars = document.querySelectorAll(selector);
                calendars.forEach(calendar => {
                    if (enable) {
                        calendar.classList.add(this.darkModeClass);
                    } else {
                        calendar.classList.remove(this.darkModeClass);
                    }
                });
            } catch (e) {
                // Ignore selector errors
            }
        });
    }

    /**
     * Cleanup observers when needed
     */
    cleanup() {
        this.observers.forEach((observers, element) => {
            if (observers instanceof MutationObserver) {
                observers.disconnect();
            } else if (Array.isArray(observers)) {
                observers.forEach(({ event, handler }) => {
                    window.removeEventListener(event, handler);
                });
            }
        });
        this.observers.clear();
    }
}

// Create global instance
window.NepaliDateHelper = new NepaliDatePickerHelper();

// Auto-initialize when DOM is ready
if (typeof $ !== 'undefined') {
    $(document).ready(function() {
        window.NepaliDateHelper.autoInit();
    });
} else {
    document.addEventListener('DOMContentLoaded', function() {
        window.NepaliDateHelper.autoInit();
    });
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (window.NepaliDateHelper) {
        window.NepaliDateHelper.cleanup();
    }
});