/**
 * Real Estate System - Main JavaScript
 * Core functionality and utilities
 */

class RealEstateApp {
    constructor() {
        this.init();
    }

    init() {
        this.initDOMContentLoaded();
        this.initTheme();
        this.initMobileMenu();
        this.initModals();
        this.initForms();
        this.initPropertyGallery();
        this.initFilters();
        this.initSearch();
    }

    initDOMContentLoaded() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.onDOMReady();
            });
        } else {
            this.onDOMReady();
        }
    }

    onDOMReady() {
        // Initialize components that need DOM to be ready
        this.initTooltips();
        this.initLazyLoading();
        this.initScrollAnimations();
    }

    // Theme management
    initTheme() {
        const themeToggle = document.querySelector('[data-theme-toggle]');
        if (themeToggle) {
            themeToggle.addEventListener('click', this.toggleTheme.bind(this));
        }
        
        // Load saved theme
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.documentElement.setAttribute('data-theme', savedTheme);
        } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    }

    toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
    }

    // Mobile menu
    initMobileMenu() {
        const mobileToggle = document.querySelector('[data-mobile-toggle]');
        const mobileMenu = document.querySelector('[data-mobile-menu]');
        
        if (mobileToggle && mobileMenu) {
            mobileToggle.addEventListener('click', () => {
                mobileMenu.classList.toggle('active');
                mobileToggle.setAttribute('aria-expanded', 
                    mobileMenu.classList.contains('active'));
            });

            // Close on outside click
            document.addEventListener('click', (e) => {
                if (!mobileMenu.contains(e.target) && !mobileToggle.contains(e.target)) {
                    mobileMenu.classList.remove('active');
                    mobileToggle.setAttribute('aria-expanded', 'false');
                }
            });
        }
    }

    // Modal system
    initModals() {
        // Open modal triggers
        document.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-modal-open]');
            if (trigger) {
                e.preventDefault();
                const modalId = trigger.getAttribute('data-modal-open');
                this.openModal(modalId);
            }
        });

        // Close modal triggers
        document.addEventListener('click', (e) => {
            const closeTrigger = e.target.closest('[data-modal-close]');
            if (closeTrigger) {
                e.preventDefault();
                this.closeModal();
                return;
            }

            // Close on overlay click
            if (e.target.classList.contains('modal-overlay')) {
                this.closeModal();
            }
        });

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeModal();
            }
        });
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            // Focus management
            const focusableElements = modal.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            if (focusableElements.length > 0) {
                focusableElements[0].focus();
            }
        }
    }

    closeModal() {
        const activeModal = document.querySelector('.modal-overlay.active');
        if (activeModal) {
            activeModal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    // Form handling
    initForms() {
        // AJAX form submission
        document.addEventListener('submit', (e) => {
            const form = e.target.closest('[data-ajax-form]');
            if (form) {
                e.preventDefault();
                this.submitForm(form);
            }
        });

        // Real-time validation
        document.addEventListener('blur', (e) => {
            if (e.target.matches('input, select, textarea')) {
                this.validateField(e.target);
            }
        }, true);

        // File upload preview
        document.addEventListener('change', (e) => {
            if (e.target.type === 'file' && e.target.hasAttribute('data-preview')) {
                this.previewFiles(e.target);
            }
        });
    }

    async submitForm(form) {
        const submitBtn = form.querySelector('[type="submit"]');
        const originalText = submitBtn.textContent;
        
        try {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading"></span> Отправка...';
            
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: form.method || 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('success', result.message || 'Операция выполнена успешно');
                
                if (result.redirect) {
                    setTimeout(() => window.location.href = result.redirect, 1500);
                } else {
                    form.reset();
                }
            } else {
                this.showAlert('error', result.message || 'Произошла ошибка');
                
                // Show field errors
                if (result.errors) {
                    this.showFieldErrors(form, result.errors);
                }
            }
        } catch (error) {
            console.error('Form submission error:', error);
            this.showAlert('error', 'Произошла ошибка при отправке формы');
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    }

    validateField(field) {
        const value = field.value.trim();
        const rules = field.getAttribute('data-rules');
        
        if (!rules) return;

        const ruleList = rules.split('|');
        let isValid = true;
        let errorMessage = '';

        for (const rule of ruleList) {
            if (rule === 'required' && !value) {
                isValid = false;
                errorMessage = 'Это поле обязательно для заполнения';
                break;
            }
            
            if (rule === 'email' && value && !this.isValidEmail(value)) {
                isValid = false;
                errorMessage = 'Введите корректный email адрес';
                break;
            }
            
            if (rule.startsWith('min:')) {
                const min = parseInt(rule.split(':')[1]);
                if (value && value.length < min) {
                    isValid = false;
                    errorMessage = `Минимум ${min} символов`;
                    break;
                }
            }
        }

        this.toggleFieldError(field, isValid, errorMessage);
    }

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    toggleFieldError(field, isValid, message) {
        const errorEl = field.parentNode.querySelector('.form-error');
        
        if (!isValid) {
            field.classList.add('error');
            if (errorEl) {
                errorEl.textContent = message;
            } else {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'form-error';
                errorDiv.textContent = message;
                field.parentNode.appendChild(errorDiv);
            }
        } else {
            field.classList.remove('error');
            if (errorEl) {
                errorEl.remove();
            }
        }
    }

    showFieldErrors(form, errors) {
        // Clear previous errors
        form.querySelectorAll('.form-error').forEach(el => el.remove());
        form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));

        // Show new errors
        Object.entries(errors).forEach(([fieldName, messages]) => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (field && messages.length > 0) {
                this.toggleFieldError(field, false, messages[0]);
            }
        });
    }

    previewFiles(input) {
        const previewContainer = document.querySelector(input.getAttribute('data-preview'));
        if (!previewContainer) return;

        previewContainer.innerHTML = '';

        Array.from(input.files).forEach(file => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '100px';
                    img.style.maxHeight = '100px';
                    img.style.margin = '5px';
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Property gallery
    initPropertyGallery() {
        document.addEventListener('click', (e) => {
            const thumbnail = e.target.closest('.gallery-thumbnail');
            if (thumbnail) {
                this.switchGalleryImage(thumbnail);
                return;
            }

            const navBtn = e.target.closest('.gallery-nav');
            if (navBtn) {
                this.navigateGallery(navBtn.classList.contains('next'));
            }
        });
    }

    switchGalleryImage(thumbnail) {
        const gallery = thumbnail.closest('.property-gallery');
        const mainImage = gallery.querySelector('.gallery-main img');
        const newSrc = thumbnail.querySelector('img').src;

        // Update active thumbnail
        gallery.querySelectorAll('.gallery-thumbnail').forEach(t => 
            t.classList.remove('active'));
        thumbnail.classList.add('active');

        // Update main image
        if (mainImage) {
            mainImage.src = newSrc;
        }
    }

    navigateGallery(isNext) {
        const gallery = document.querySelector('.property-gallery');
        const thumbnails = gallery.querySelectorAll('.gallery-thumbnail');
        const currentActive = gallery.querySelector('.gallery-thumbnail.active');
        
        let currentIndex = Array.from(thumbnails).indexOf(currentActive);
        let newIndex;

        if (isNext) {
            newIndex = (currentIndex + 1) % thumbnails.length;
        } else {
            newIndex = currentIndex === 0 ? thumbnails.length - 1 : currentIndex - 1;
        }

        this.switchGalleryImage(thumbnails[newIndex]);
    }

    // Search and filters
    initSearch() {
        const searchInput = document.querySelector('[data-search-input]');
        if (searchInput) {
            let searchTimeout;
            
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.performSearch(e.target.value);
                }, 300);
            });
        }
    }

    initFilters() {
        // Price range sliders
        document.addEventListener('input', (e) => {
            if (e.target.matches('[data-price-min], [data-price-max]')) {
                this.updatePriceRange();
            }
        });

        // Filter form submission
        const filterForm = document.querySelector('[data-filter-form]');
        if (filterForm) {
            filterForm.addEventListener('change', () => {
                this.applyFilters();
            });
        }
    }

    updatePriceRange() {
        const minInput = document.querySelector('[data-price-min]');
        const maxInput = document.querySelector('[data-price-max]');
        const display = document.querySelector('[data-price-display]');
        
        if (minInput && maxInput && display) {
            const min = parseInt(minInput.value) || 0;
            const max = parseInt(maxInput.value) || 0;
            display.textContent = `${min.toLocaleString()} - ${max.toLocaleString()} руб.`;
        }
    }

    async performSearch(query) {
        if (query.length < 2) return;

        try {
            const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
            const results = await response.json();
            this.displaySearchResults(results);
        } catch (error) {
            console.error('Search error:', error);
        }
    }

    displaySearchResults(results) {
        const container = document.querySelector('[data-search-results]');
        if (!container) return;

        if (results.length === 0) {
            container.innerHTML = '<p>Ничего не найдено</p>';
            return;
        }

        const html = results.map(item => `
            <a href="/properties/${item.id}" class="search-result-item">
                <div class="search-result-title">${item.title}</div>
                <div class="search-result-address">${item.address}</div>
                <div class="search-result-price">${item.price.toLocaleString()} руб.</div>
            </a>
        `).join('');

        container.innerHTML = html;
    }

    async applyFilters() {
        const form = document.querySelector('[data-filter-form]');
        if (!form) return;

        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        
        try {
            const response = await fetch(`/properties/filter?${params}`);
            const html = await response.text();
            
            const resultsContainer = document.querySelector('[data-filter-results]');
            if (resultsContainer) {
                resultsContainer.innerHTML = html;
            }
        } catch (error) {
            console.error('Filter error:', error);
        }
    }

    // Utility functions
    showAlert(type, message) {
        const alertContainer = document.querySelector('[data-alerts]') || document.body;
        
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;
        
        alertContainer.appendChild(alert);
        
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }

    initTooltips() {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        tooltipElements.forEach(element => {
            this.createTooltip(element);
        });
    }

    createTooltip(element) {
        const text = element.getAttribute('data-tooltip');
        
        element.addEventListener('mouseenter', () => {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = text;
            tooltip.style.position = 'absolute';
            tooltip.style.zIndex = '9999';
            tooltip.style.background = '#333';
            tooltip.style.color = 'white';
            tooltip.style.padding = '8px 12px';
            tooltip.style.borderRadius = '4px';
            tooltip.style.fontSize = '14px';
            tooltip.style.pointerEvents = 'none';
            
            document.body.appendChild(tooltip);
            
            const rect = element.getBoundingClientRect();
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
            
            element._tooltip = tooltip;
        });
        
        element.addEventListener('mouseleave', () => {
            if (element._tooltip) {
                element._tooltip.remove();
                element._tooltip = null;
            }
        });
    }

    initLazyLoading() {
        const images = document.querySelectorAll('img[data-src]');
        
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    }

    initScrollAnimations() {
        const elements = document.querySelectorAll('[data-animate]');
        
        const animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, {
            threshold: 0.1
        });

        elements.forEach(el => animationObserver.observe(el));
    }
}

// Initialize app when DOM is ready
const app = new RealEstateApp();

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = RealEstateApp;
}