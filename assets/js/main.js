/**
 * EarnForex WP Theme - Main JavaScript
 *
 * @package EarnForex_WP
 * @since 1.0.0
 */

(function() {
    'use strict';

    // DOM Elements
    const menuToggle = document.querySelector('.menu-toggle');
    const headerMenu = document.querySelector('.header__menu_content');
    const body = document.body;

    // Mobile Menu Toggle
    if (menuToggle && headerMenu) {
        menuToggle.addEventListener('click', function() {
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', !isExpanded);
            headerMenu.classList.toggle('active');
            body.style.overflow = isExpanded ? '' : 'hidden';
        });

        // Close menu on link click
        headerMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    menuToggle.setAttribute('aria-expanded', 'false');
                    headerMenu.classList.remove('active');
                    body.style.overflow = '';
                }
            });
        });

        // Close menu on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && headerMenu.classList.contains('active')) {
                menuToggle.setAttribute('aria-expanded', 'false');
                headerMenu.classList.remove('active');
                body.style.overflow = '';
                menuToggle.focus();
            }
        });
    }

    // Expanding Search
    const searchInputs = document.querySelectorAll('.expanding-search__input, .expanding-search__field input');
    searchInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.width = '280px';
        });
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.style.width = '200px';
            }
        });
    });

    // Submenu accessibility
    document.querySelectorAll('.menu__item').forEach(item => {
        const link = item.querySelector('.menu__link');
        const submenu = item.querySelector('.menu__submenu');

        if (submenu && link) {
            // Keyboard navigation
            link.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    item.classList.toggle('menu__item--open');
                }
            });
        }
    });

    // Broker Archive: Filter & Sort
    const brokerFilter = document.getElementById('brokers-filter');
    const filterToggle = brokerFilter ? brokerFilter.querySelector('.brokers-filter__toggle') : null;
    const filterPanel = document.getElementById('filter-panel');
    const filterCheckboxes = document.querySelectorAll('.brokers-filter__list input[type="checkbox"]');
    const filterReset = document.querySelector('.brokers-filter__reset');
    const sortingBtns = document.querySelector('.brokers-list__sorting-btns');
    const sortingOpenBtn = document.querySelector('.brokers-list__sorting-open-btn');
    const sortingCloseBtn = document.querySelector('.brokers-list__sorting-close-btn');
    const sortingList = document.getElementById('sorting-list');
    const sortingItems = document.querySelectorAll('.brokers-list__sorting-item[data-sort]');
    const loadMoreBtn = document.querySelector('.brokers-list__load-more button');

    // Filter Toggle (Mobile)
    if (filterToggle && filterPanel) {
        filterToggle.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
            filterPanel.hidden = isExpanded;
        });
    }

    // Filter Checkboxes
    let currentFilters = {
        category: '',
        features: [],
        sort: 'date',
        page: 1
    };

    filterCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const name = this.name;
            const value = this.value;

            if (name === 'category') {
                currentFilters.category = this.checked ? value : '';
            } else if (name === 'feature[]') {
                if (this.checked) {
                    currentFilters.features.push(value);
                } else {
                    currentFilters.features = currentFilters.features.filter(f => f !== value);
                }
            }
            currentFilters.page = 1;
            fetchBrokers();
        });
    });

    // Filter Reset
    if (filterReset) {
        filterReset.addEventListener('click', function() {
            filterCheckboxes.forEach(cb => cb.checked = false);
            currentFilters = { category: '', features: [], sort: 'date', page: 1 };
            fetchBrokers();
        });
    }

    // Sorting Toggle
    if (sortingOpenBtn && sortingList) {
        sortingOpenBtn.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
            sortingList.hidden = isExpanded;
            if (sortingCloseBtn) sortingCloseBtn.hidden = isExpanded;
        });
    }

    if (sortingCloseBtn && sortingList) {
        sortingCloseBtn.addEventListener('click', function() {
            sortingOpenBtn.setAttribute('aria-expanded', 'false');
            sortingList.hidden = true;
            this.hidden = true;
        });
    }

    // Sorting Items
    sortingItems.forEach(item => {
        item.addEventListener('click', function() {
            currentFilters.sort = this.dataset.sort;
            currentFilters.page = 1;
            fetchBrokers();

            // Update UI
            sortingItems.forEach(i => i.classList.remove('--asc', '--desc'));
            this.classList.add(currentFilters.sort.includes('desc') ? '--desc' : '--asc');

            if (sortingList) sortingList.hidden = true;
            if (sortingOpenBtn) sortingOpenBtn.setAttribute('aria-expanded', 'false');
            if (sortingCloseBtn) sortingCloseBtn.hidden = true;
        });
    });

    // Load More
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page) || 2;
            const maxPages = parseInt(this.dataset.maxPages) || 1;
            currentFilters.page = page;
            this.disabled = true;
            this.textContent = efp_ajax.loading_text || 'Loading...';

            fetchBrokers(true).then(hasMore => {
                this.disabled = false;
                if (hasMore && page < maxPages) {
                    this.dataset.page = page + 1;
                    this.textContent = efp_ajax.load_more_text || 'Load More Brokers';
                } else {
                    this.style.display = 'none';
                }
            });
        });
    }

    // AJAX Fetch Brokers
    function fetchBrokers(append = false) {
        return new Promise((resolve) => {
            const formData = new FormData();
            formData.append('action', 'efp_filter_brokers');
            formData.append('nonce', efp_ajax.nonce);
            formData.append('page', currentFilters.page);
            formData.append('sort', currentFilters.sort);
            if (currentFilters.category) formData.append('category', currentFilters.category);
            currentFilters.features.forEach(f => formData.append('feature[]', f));

            fetch(efp_ajax.ajax_url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const grid = document.querySelector('.brokers-list__grid');
                    if (grid) {
                        if (append) {
                            grid.insertAdjacentHTML('beforeend', data.data.html);
                        } else {
                            grid.innerHTML = data.data.html;
                        }
                    }
                    resolve(data.data.has_more);
                } else {
                    resolve(false);
                }
            })
            .catch(() => resolve(false));
        });
    }

    // Broker Search Autocomplete
    const brokerSearchInput = document.getElementById('broker-search');
    const headerSearchInput = document.getElementById('header-search');
    let searchDebounce = null;

    function setupSearchAutocomplete(input) {
        if (!input) return;

        const resultsContainer = document.createElement('div');
        resultsContainer.className = 'search-autocomplete';
        resultsContainer.setAttribute('role', 'listbox');
        resultsContainer.style.cssText = 'position:absolute;top:100%;left:0;right:0;background:white;border:1px solid var(--color-border);border-radius:var(--radius-md);box-shadow:var(--shadow-xl);z-index:100;max-height:300px;overflow-y:auto;display:none;';
        input.parentNode.style.position = 'relative';
        input.parentNode.appendChild(resultsContainer);

        input.addEventListener('input', function() {
            clearTimeout(searchDebounce);
            const query = this.value.trim();

            if (query.length < 2) {
                resultsContainer.style.display = 'none';
                return;
            }

            searchDebounce = setTimeout(() => {
                searchBrokers(query, resultsContainer);
            }, 300);
        });

        input.addEventListener('focus', function() {
            if (this.value.trim().length >= 2) {
                resultsContainer.style.display = 'block';
            }
        });

        document.addEventListener('click', (e) => {
            if (!input.contains(e.target) && !resultsContainer.contains(e.target)) {
                resultsContainer.style.display = 'none';
            }
        });
    }

    function searchBrokers(query, container) {
        const formData = new FormData();
        formData.append('action', 'efp_search_brokers');
        formData.append('nonce', efp_ajax.nonce);
        formData.append('search', query);

        fetch(efp_ajax.ajax_url, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                container.innerHTML = data.data.map(broker => `
                    <a href="${broker.url}" class="search-autocomplete__item" role="option" style="display:flex;align-items:center;gap:12px;padding:12px 16px;text-decoration:none;color:inherit;transition:background var(--transition-fast);">
                        ${broker.logo ? `<img src="${broker.logo}" alt="" width="32" height="32" style="border-radius:var(--radius-md);">` : ''}
                        <div style="flex:1;">
                            <div style="font-weight:500;">${broker.title}</div>
                            ${broker.rating ? `<div style="font-size:0.75rem;color:var(--color-primary);">${broker.rating} / 5</div>` : ''}
                        </div>
                    </a>
                `).join('');
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        });
    }

    setupSearchAutocomplete(brokerSearchInput);
    setupSearchAutocomplete(headerSearchInput);

    // Smooth Scroll for Anchor Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                target.focus({ preventScroll: true });
            }
        });
    });

    // Intersection Observer for Animations
    const animatedElements = document.querySelectorAll('.brokers-list__card, .post-card, .tool-card, .section-block');
    const animationObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                animationObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        animationObserver.observe(el);
    });

    // Lazy Load Images
    if ('loading' in HTMLImageElement.prototype) {
        document.querySelectorAll('img[loading="lazy"]').forEach(img => {
            img.loading = 'lazy';
        });
    } else {
        // Fallback for older browsers
        const lazyImages = document.querySelectorAll('img[data-src]');
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
        lazyImages.forEach(img => imageObserver.observe(img));
    }

    // Table Responsive Wrapper
    document.querySelectorAll('.wp-block-table table, table:not([class])').forEach(table => {
        if (!table.parentElement.classList.contains('table-responsive')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive';
            wrapper.style.overflowX = 'auto';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {

    // User Menu Dropdown Toggle
    const userMenuTrigger = document.querySelector('.user-menu__trigger');
    const userMenu = document.querySelector('.user-menu');
    
    if (userMenuTrigger && userMenu) {
        userMenuTrigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            userMenu.classList.toggle('active');
            userMenuTrigger.setAttribute('aria-expanded', 
                userMenuTrigger.getAttribute('aria-expanded') === 'true' ? 'false' : 'true');
        });
        
        // Close on outside click
        document.addEventListener('click', function(e) {
            if (!userMenu.contains(e.target)) {
                userMenu.classList.remove('active');
                if (userMenuTrigger) {
                    userMenuTrigger.setAttribute('aria-expanded', 'false');
                }
            }
        });
        
        // Close on escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && userMenu.classList.contains('active')) {
                userMenu.classList.remove('active');
                if (userMenuTrigger) {
                    userMenuTrigger.setAttribute('aria-expanded', 'false');
                    userMenuTrigger.focus();
                }
            }
        });
    }

        // Trigger custom event for other scripts
        document.dispatchEvent(new CustomEvent('efp:ready'));
    });

})();