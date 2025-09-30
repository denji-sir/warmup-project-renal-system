/**
 * Real Estate System - Property Components
 * Specialized JavaScript for property-related functionality
 */

class PropertyMap {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        this.options = {
            center: [55.7558, 37.6176], // Moscow
            zoom: 10,
            ...options
        };
        this.markers = [];
        this.init();
    }

    init() {
        if (!this.container) return;

        // For demo purposes, create a simple map placeholder
        // In real implementation, you would integrate with Yandex Maps or Google Maps
        this.container.innerHTML = `
            <div class="map-placeholder">
                <div class="map-controls">
                    <button class="map-btn" data-action="zoom-in">+</button>
                    <button class="map-btn" data-action="zoom-out">-</button>
                    <button class="map-btn" data-action="locate">@</button>
                </div>
                <div class="map-content">
                    <p>Карта недвижимости</p>
                    <small>Интеграция с Яндекс.Карты</small>
                </div>
            </div>
        `;

        this.bindEvents();
    }

    bindEvents() {
        this.container.addEventListener('click', (e) => {
            const action = e.target.getAttribute('data-action');
            if (action) {
                e.preventDefault();
                this.handleMapAction(action);
            }
        });
    }

    handleMapAction(action) {
        switch (action) {
            case 'zoom-in':
                this.zoomIn();
                break;
            case 'zoom-out':
                this.zoomOut();
                break;
            case 'locate':
                this.locate();
                break;
        }
    }

    zoomIn() {
        console.log('Zoom in');
    }

    zoomOut() {
        console.log('Zoom out');
    }

    locate() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    console.log('User location:', position.coords);
                },
                (error) => {
                    console.error('Geolocation error:', error);
                }
            );
        }
    }

    addMarker(lat, lng, info) {
        const marker = { lat, lng, info };
        this.markers.push(marker);
        // In real implementation, add marker to the map
    }

    clearMarkers() {
        this.markers = [];
    }
}

class PropertyComparison {
    constructor() {
        this.properties = JSON.parse(localStorage.getItem('comparison') || '[]');
        this.maxItems = 3;
        this.init();
    }

    init() {
        this.updateUI();
        this.bindEvents();
    }

    bindEvents() {
        // Add to comparison
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-compare-add]');
            if (btn) {
                e.preventDefault();
                const propertyId = btn.getAttribute('data-compare-add');
                this.addProperty(propertyId);
            }
        });

        // Remove from comparison
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-compare-remove]');
            if (btn) {
                e.preventDefault();
                const propertyId = btn.getAttribute('data-compare-remove');
                this.removeProperty(propertyId);
            }
        });

        // Clear all
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-compare-clear]')) {
                e.preventDefault();
                this.clearAll();
            }
        });
    }

    addProperty(propertyId) {
        if (this.properties.includes(propertyId)) {
            this.showMessage('Недвижимость уже добавлена в сравнение');
            return;
        }

        if (this.properties.length >= this.maxItems) {
            this.showMessage(`Можно сравнить максимум ${this.maxItems} объекта`);
            return;
        }

        this.properties.push(propertyId);
        this.save();
        this.updateUI();
        this.showMessage('Добавлено в сравнение');
    }

    removeProperty(propertyId) {
        this.properties = this.properties.filter(id => id !== propertyId);
        this.save();
        this.updateUI();
        this.showMessage('Удалено из сравнения');
    }

    clearAll() {
        this.properties = [];
        this.save();
        this.updateUI();
        this.showMessage('Сравнение очищено');
    }

    save() {
        localStorage.setItem('comparison', JSON.stringify(this.properties));
    }

    updateUI() {
        // Update comparison counter
        const counter = document.querySelector('[data-compare-count]');
        if (counter) {
            counter.textContent = this.properties.length;
            counter.style.display = this.properties.length > 0 ? 'block' : 'none';
        }

        // Update comparison buttons
        document.querySelectorAll('[data-compare-add]').forEach(btn => {
            const propertyId = btn.getAttribute('data-compare-add');
            const isInComparison = this.properties.includes(propertyId);
            
            btn.style.display = isInComparison ? 'none' : 'inline-flex';
        });

        document.querySelectorAll('[data-compare-remove]').forEach(btn => {
            const propertyId = btn.getAttribute('data-compare-remove');
            const isInComparison = this.properties.includes(propertyId);
            
            btn.style.display = isInComparison ? 'inline-flex' : 'none';
        });

        // Update comparison page
        this.updateComparisonTable();
    }

    updateComparisonTable() {
        const table = document.querySelector('[data-comparison-table]');
        if (!table || this.properties.length === 0) return;

        // This would fetch property data and build comparison table
        // For now, just show placeholder
        table.innerHTML = `
            <div class="comparison-placeholder">
                <p>Выбрано объектов: ${this.properties.length}</p>
                <p>ID: ${this.properties.join(', ')}</p>
            </div>
        `;
    }

    showMessage(message) {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #333;
            color: white;
            padding: 12px 16px;
            border-radius: 4px;
            z-index: 9999;
            animation: slideIn 0.3s ease;
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
}

class PropertyFavorites {
    constructor() {
        this.favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
        this.init();
    }

    init() {
        this.updateUI();
        this.bindEvents();
    }

    bindEvents() {
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-favorite-toggle]');
            if (btn) {
                e.preventDefault();
                const propertyId = btn.getAttribute('data-favorite-toggle');
                this.toggleFavorite(propertyId);
            }
        });
    }

    toggleFavorite(propertyId) {
        const index = this.favorites.indexOf(propertyId);
        
        if (index > -1) {
            this.favorites.splice(index, 1);
            this.showMessage('Удалено из избранного');
        } else {
            this.favorites.push(propertyId);
            this.showMessage('Добавлено в избранное');
        }

        this.save();
        this.updateUI();
    }

    save() {
        localStorage.setItem('favorites', JSON.stringify(this.favorites));
    }

    updateUI() {
        document.querySelectorAll('[data-favorite-toggle]').forEach(btn => {
            const propertyId = btn.getAttribute('data-favorite-toggle');
            const isFavorite = this.favorites.includes(propertyId);
            
            btn.classList.toggle('active', isFavorite);
            btn.setAttribute('aria-pressed', isFavorite);
            
            const icon = btn.querySelector('.icon');
            if (icon) {
                    icon.textContent = isFavorite ? 'heart' : 'heart-o';
            }
        });

        // Update favorites counter
        const counter = document.querySelector('[data-favorites-count]');
        if (counter) {
            counter.textContent = this.favorites.length;
        }
    }

    showMessage(message) {
        // Simple toast notification
        const existing = document.querySelector('.toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--color-primary);
            color: white;
            padding: 12px 20px;
            border-radius: 25px;
            z-index: 9999;
            font-size: 14px;
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 2500);
    }
}

class PropertyFilter {
    constructor(formSelector) {
        this.form = document.querySelector(formSelector);
        this.resultsContainer = document.querySelector('[data-filter-results]');
        this.loadingIndicator = document.querySelector('[data-loading]');
        this.init();
    }

    init() {
        if (!this.form) return;

        this.bindEvents();
        this.loadSavedFilters();
    }

    bindEvents() {
        // Debounced form change
        let timeout;
        this.form.addEventListener('change', () => {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                this.applyFilters();
            }, 300);
        });

        // Reset filters
        const resetBtn = this.form.querySelector('[data-filter-reset]');
        if (resetBtn) {
            resetBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.resetFilters();
            });
        }

        // Save filters
        const saveBtn = this.form.querySelector('[data-filter-save]');
        if (saveBtn) {
            saveBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.saveFilters();
            });
        }
    }

    async applyFilters() {
        if (!this.resultsContainer) return;

        try {
            this.showLoading(true);

            const formData = new FormData(this.form);
            const params = new URLSearchParams();

            // Build clean params
            for (const [key, value] of formData.entries()) {
                if (value && value.trim()) {
                    params.append(key, value);
                }
            }

            const response = await fetch(`/api/properties/filter?${params}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Filter request failed');
            }

            const data = await response.json();
            
            if (data.html) {
                this.resultsContainer.innerHTML = data.html;
            }

            // Update URL without page reload
            const url = new URL(window.location);
            url.search = params.toString();
            window.history.pushState({}, '', url);

            this.updateResultsCount(data.total || 0);

        } catch (error) {
            console.error('Filter error:', error);
            this.showError('Ошибка при фильтрации результатов');
        } finally {
            this.showLoading(false);
        }
    }

    resetFilters() {
        this.form.reset();
        this.applyFilters();
        localStorage.removeItem('propertyFilters');
    }

    saveFilters() {
        const formData = new FormData(this.form);
        const filters = {};
        
        for (const [key, value] of formData.entries()) {
            if (value && value.trim()) {
                filters[key] = value;
            }
        }

        localStorage.setItem('propertyFilters', JSON.stringify(filters));
        
        // Show confirmation
        this.showMessage('Фильтры сохранены');
    }

    loadSavedFilters() {
        const saved = localStorage.getItem('propertyFilters');
        if (!saved) return;

        try {
            const filters = JSON.parse(saved);
            
            Object.entries(filters).forEach(([key, value]) => {
                const field = this.form.querySelector(`[name="${key}"]`);
                if (field) {
                    if (field.type === 'checkbox' || field.type === 'radio') {
                        field.checked = field.value === value;
                    } else {
                        field.value = value;
                    }
                }
            });

            // Apply saved filters
            setTimeout(() => this.applyFilters(), 100);
        } catch (error) {
            console.error('Error loading saved filters:', error);
        }
    }

    showLoading(show) {
        if (this.loadingIndicator) {
            this.loadingIndicator.style.display = show ? 'block' : 'none';
        }

        if (this.resultsContainer) {
            this.resultsContainer.style.opacity = show ? '0.5' : '1';
        }
    }

    showError(message) {
        if (this.resultsContainer) {
            this.resultsContainer.innerHTML = `
                <div class="alert alert-error">
                    ${message}
                </div>
            `;
        }
    }

    showMessage(message) {
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--color-success);
            color: white;
            padding: 12px 16px;
            border-radius: 4px;
            z-index: 9999;
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    updateResultsCount(count) {
        const counter = document.querySelector('[data-results-count]');
        if (counter) {
            counter.textContent = count;
        }

        const message = document.querySelector('[data-results-message]');
        if (message) {
            message.textContent = `Найдено ${count} объектов`;
        }
    }
}

// Initialize property components
document.addEventListener('DOMContentLoaded', () => {
    // Initialize map if container exists
    if (document.getElementById('property-map')) {
        new PropertyMap('property-map');
    }

    // Initialize comparison
    new PropertyComparison();

    // Initialize favorites
    new PropertyFavorites();

    // Initialize filters
    if (document.querySelector('[data-property-filter]')) {
        new PropertyFilter('[data-property-filter]');
    }
});

// Export classes for use in other modules
window.PropertyMap = PropertyMap;
window.PropertyComparison = PropertyComparison;
window.PropertyFavorites = PropertyFavorites;
window.PropertyFilter = PropertyFilter;