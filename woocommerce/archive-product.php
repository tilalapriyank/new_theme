<?php
/**
 * Complete WooCommerce Filter System
 * Replace your existing archive-product.php with this code
 */

defined('ABSPATH') || exit;

// Remove any existing filter hooks to avoid conflicts
remove_all_actions('pre_get_posts');

get_header();
?>

<main id="main-content" class="py-12 md:pt-32 md:pb-16" x-data="{ mobileFiltersOpen: false }">
    <div class="container mx-auto md:px-32">
        <!-- Page Header -->
        <div class="mb-8">
            <?php if (is_product_category()) : ?>
                <?php
                $current_category = get_queried_object();
                $category_name = $current_category->name;
                $category_description = $current_category->description;
                ?>
                <h1 class="text-3xl font-bold mb-2"><?php echo esc_html($category_name); ?></h1>
                <?php if ($category_description) : ?>
                    <p class="text-gray-600 mb-4"><?php echo esc_html($category_description); ?></p>
                <?php endif; ?>
            <?php else : ?>
                <h1 class="text-3xl font-bold mb-2">Shop All Products</h1>
            <?php endif; ?>
            
            <div class="flex items-center text-sm text-gray-500">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-[#ed1c24]">Home</a>
                <span class="mx-2">/</span>
                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="hover:text-[#ed1c24]">Shop</a>
                <?php if (is_product_category()) : ?>
                    <span class="mx-2">/</span>
                    <span><?php echo esc_html($category_name); ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Mobile Filter Toggle -->
            <div class="lg:hidden w-full mb-4">
                <button
                    @click="mobileFiltersOpen = !mobileFiltersOpen"
                    class="w-full flex items-center justify-between border border-gray-300 rounded-md px-4 py-2 bg-white"
                >
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filters
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform" :class="mobileFiltersOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>

            <!-- Filters Sidebar -->
            <div class="lg:w-1/4" :class="mobileFiltersOpen ? 'block' : 'hidden lg:block'">
                <div class="bg-white rounded-lg border border-gray-200 p-6 sticky top-24">
                    <h3 class="font-bold mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        Filter Products
                    </h3>

                    <!-- Active Filters Display -->
                    <div id="active-filters" class="mb-4"></div>

                    <!-- Categories Filter -->
                    <?php if (!is_product_category()) : ?>
                    <div class="mb-6 border-t border-gray-200 pt-4">
                        <h4 class="font-medium mb-3">Categories</h4>
                        <div class="space-y-2 max-h-48 overflow-y-auto" id="categories-filter">
                            <!-- Categories will be loaded here -->
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Price Range Filter -->
                    <div class="mb-6 border-t border-gray-200 pt-4">
                        <h4 class="font-medium mb-3">Price Range</h4>
                        <div class="px-2">
                            <div id="price-slider"></div>
                            <div class="flex items-center justify-between mt-2">
                                <span id="price-min-label" class="text-sm">$0</span>
                                <span id="price-max-label" class="text-sm">$1000</span>
                            </div>
                        </div>
                    </div>

                    <!-- Attributes Filters -->
                    <div id="attributes-filters">
                        <!-- Size Filter -->
                        <div class="mb-6 border-t border-gray-200 pt-4">
                            <h4 class="font-medium mb-3">Size</h4>
                            <div class="grid grid-cols-3 gap-2" id="size-filter">
                                <!-- Sizes will be loaded here -->
                            </div>
                        </div>

                        <!-- Color Filter -->
                        <div class="mb-6 border-t border-gray-200 pt-4">
                            <h4 class="font-medium mb-3">Color</h4>
                            <div class="grid grid-cols-4 gap-2" id="color-filter">
                                <!-- Colors will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <!-- Filter Actions -->
                    <div class="border-t border-gray-200 pt-4">
                        <button id="clear-filters" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-4 py-2 rounded-md mb-2">
                            Clear All Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Products Area -->
            <div class="lg:w-3/4">
                <!-- Sort and Results Info -->
                <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
                    <div id="results-info" class="text-sm text-gray-500">
                        Loading products...
                    </div>
                    <div class="flex items-center gap-4">
                        <select id="sort-by" class="border border-gray-300 rounded-md px-3 py-2 bg-white">
                            <option value="menu_order">Default sorting</option>
                            <option value="popularity">Sort by popularity</option>
                            <option value="rating">Sort by average rating</option>
                            <option value="date">Sort by latest</option>
                            <option value="price">Sort by price: low to high</option>
                            <option value="price-desc">Sort by price: high to low</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Products will be loaded here -->
                </div>

                <!-- Loading Spinner -->
                <div id="loading-spinner" class="hidden col-span-full flex justify-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#ed1c24]"></div>
                </div>

                <!-- Pagination -->
                <div id="pagination-container" class="flex justify-center mt-12">
                    <!-- Pagination will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-md opacity-0 transition-opacity duration-300 z-50">
    Product added to cart
</div>

<!-- Include Required Libraries -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.css">
<script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js"></script>

<script>
class WooCommerceFilters {
    constructor() {
        this.filters = {
            categories: [],
            min_price: 0,
            max_price: 1000,
            sizes: [],
            colors: [],
            orderby: 'menu_order',
            paged: 1
        };
        this.isLoading = false;
        this.priceSlider = null;
        this.init();
    }

    init() {
        this.loadInitialData();
        this.setupEventListeners();
        this.loadProducts();
    }

    async loadInitialData() {
        try {
            // Load filter options
            const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'get_filter_options',
                    nonce: '<?php echo wp_create_nonce('filter_nonce'); ?>'
                })
            });

            const data = await response.json();
            if (data.success) {
                this.setupPriceSlider(data.data.price_range.min, data.data.price_range.max);
                this.renderCategories(data.data.categories);
                this.renderAttributes(data.data.attributes);
            }
        } catch (error) {
            console.error('Error loading filter data:', error);
        }
    }

    setupPriceSlider(min, max) {
        const slider = document.getElementById('price-slider');
        if (!slider || this.priceSlider) return;

        this.filters.min_price = min;
        this.filters.max_price = max;

        this.priceSlider = noUiSlider.create(slider, {
            start: [min, max],
            connect: true,
            range: {
                'min': min,
                'max': max
            },
            format: {
                to: function (value) { return Math.round(value); },
                from: function (value) { return Number(value); }
            }
        });

        this.priceSlider.on('update', (values) => {
            document.getElementById('price-min-label').textContent = '$' + values[0];
            document.getElementById('price-max-label').textContent = '$' + values[1];
            this.filters.min_price = values[0];
            this.filters.max_price = values[1];
        });

        this.priceSlider.on('change', () => {
            this.loadProducts();
        });
    }

    renderCategories(categories) {
        const container = document.getElementById('categories-filter');
        if (!container) return;

        container.innerHTML = categories.map(cat => `
            <div class="flex items-center">
                <input type="checkbox" id="cat-${cat.slug}" value="${cat.slug}" 
                       class="category-filter rounded border-gray-300 text-[#ed1c24] focus:ring-[#ed1c24]">
                <label for="cat-${cat.slug}" class="ml-2 text-sm flex-grow cursor-pointer">
                    ${cat.name}
                </label>
                <span class="text-xs text-gray-500">(${cat.count})</span>
            </div>
        `).join('');
    }

    renderAttributes(attributes) {
        // Render sizes
        const sizeContainer = document.getElementById('size-filter');
        if (sizeContainer && attributes.sizes) {
            sizeContainer.innerHTML = attributes.sizes.map(size => `
                <label class="size-option cursor-pointer">
                    <input type="checkbox" value="${size.slug}" class="size-filter sr-only">
                    <span class="block text-center py-2 px-3 border border-gray-300 rounded text-sm hover:border-[#ed1c24] transition-colors">
                        ${size.name}
                    </span>
                </label>
            `).join('');
        }

        // Render colors
        const colorContainer = document.getElementById('color-filter');
        if (colorContainer && attributes.colors) {
            colorContainer.innerHTML = attributes.colors.map(color => `
                <label class="color-option cursor-pointer" title="${color.name}">
                    <input type="checkbox" value="${color.slug}" class="color-filter sr-only">
                    <div class="flex flex-col items-center gap-1">
                        <div class="w-8 h-8 rounded-full border-2 border-gray-300 hover:border-[#ed1c24] transition-colors"
                             style="background-color: ${color.value || color.slug}"></div>
                        <span class="text-xs">${color.name}</span>
                    </div>
                </label>
            `).join('');
        }
    }

    setupEventListeners() {
        // Category filters
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('category-filter')) {
                this.updateArrayFilter('categories', e.target.value, e.target.checked);
                this.loadProducts();
            }
        });

        // Attribute filters
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('size-filter')) {
                this.updateArrayFilter('sizes', e.target.value, e.target.checked);
                this.toggleFilterOption(e.target.closest('.size-option'));
                this.loadProducts();
            }
            if (e.target.classList.contains('color-filter')) {
                this.updateArrayFilter('colors', e.target.value, e.target.checked);
                this.toggleFilterOption(e.target.closest('.color-option'));
                this.loadProducts();
            }
        });

        // Sort change
        document.getElementById('sort-by').addEventListener('change', (e) => {
            this.filters.orderby = e.target.value;
            this.filters.paged = 1;
            this.loadProducts();
        });

        // Clear filters
        document.getElementById('clear-filters').addEventListener('click', () => {
            this.clearAllFilters();
        });

        // Pagination (event delegation)
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('page-link')) {
                e.preventDefault();
                const page = parseInt(e.target.dataset.page);
                if (page) {
                    this.filters.paged = page;
                    this.loadProducts();
                }
            }
        });
    }

    updateArrayFilter(filterKey, value, isChecked) {
        if (isChecked) {
            if (!this.filters[filterKey].includes(value)) {
                this.filters[filterKey].push(value);
            }
        } else {
            this.filters[filterKey] = this.filters[filterKey].filter(item => item !== value);
        }
        this.filters.paged = 1; // Reset to first page
    }

    toggleFilterOption(element) {
        if (element) {
            element.classList.toggle('selected');
        }
    }

    async loadProducts() {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.showLoading();

        try {
            const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'filter_products',
                    nonce: '<?php echo wp_create_nonce('filter_nonce'); ?>',
                    filters: JSON.stringify(this.filters),
                    category_slug: '<?php echo is_product_category() ? get_queried_object()->slug : ''; ?>'
                })
            });

            const data = await response.json();
            if (data.success) {
                this.renderProducts(data.data.products);
                this.updateResultsInfo(data.data.found_posts);
                this.renderPagination(data.data.pagination);
                this.updateActiveFilters();
                this.updateURL();
            } else {
                console.error('Filter error:', data.data);
            }
        } catch (error) {
            console.error('Error loading products:', error);
        } finally {
            this.isLoading = false;
            this.hideLoading();
        }
    }

    renderProducts(productsHtml) {
        document.getElementById('products-grid').innerHTML = productsHtml;
        this.initializeProductEvents();
    }

    initializeProductEvents() {
        // Re-initialize add to cart buttons
        document.querySelectorAll('.ajax-add-to-cart').forEach(button => {
            button.addEventListener('click', this.handleAddToCart.bind(this));
        });
    }

    handleAddToCart(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const button = e.currentTarget;
        const productId = button.dataset.product_id;
        const quantity = button.dataset.quantity || 1;
        
        // Show loading state
        button.disabled = true;
        const textElement = button.querySelector('.add-to-cart-text');
        const originalText = textElement.textContent;
        textElement.textContent = 'Adding...';
        
        // Add to cart
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'woocommerce_add_to_cart',
                product_id: productId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            
            // Success
            textElement.textContent = 'Added!';
            this.showToast('Product added to cart!', 'success');
            
            // Reset after 2 seconds
            setTimeout(() => {
                button.disabled = false;
                textElement.textContent = originalText;
            }, 2000);
        })
        .catch(error => {
            console.error('Add to cart error:', error);
            button.disabled = false;
            textElement.textContent = originalText;
            this.showToast('Error adding product to cart', 'error');
        });
    }

    updateResultsInfo(foundPosts) {
        document.getElementById('results-info').textContent = `${foundPosts} products found`;
    }

    renderPagination(paginationHtml) {
        document.getElementById('pagination-container').innerHTML = paginationHtml;
    }

    updateActiveFilters() {
        const container = document.getElementById('active-filters');
        let filtersHtml = '';

        // Add active filters
        Object.keys(this.filters).forEach(key => {
            if (Array.isArray(this.filters[key]) && this.filters[key].length > 0) {
                this.filters[key].forEach(value => {
                    filtersHtml += `
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-[#ed1c24] text-white">
                            ${this.getFilterLabel(key, value)}
                            <button type="button" class="ml-2 text-white hover:text-gray-200" 
                                    onclick="wooFilters.removeFilter('${key}', '${value}')">×</button>
                        </span>
                    `;
                });
            }
        });

        container.innerHTML = filtersHtml;
    }

    getFilterLabel(key, value) {
        const labels = {
            categories: 'Category',
            sizes: 'Size',
            colors: 'Color'
        };
        return `${labels[key] || key}: ${value}`;
    }

    removeFilter(key, value) {
        if (Array.isArray(this.filters[key])) {
            this.filters[key] = this.filters[key].filter(item => item !== value);
            
            // Update UI
            const checkbox = document.querySelector(`input[value="${value}"]`);
            if (checkbox) {
                checkbox.checked = false;
                const option = checkbox.closest('.size-option, .color-option');
                if (option) option.classList.remove('selected');
            }
            
            this.loadProducts();
        }
    }

    clearAllFilters() {
        // Reset filters
        this.filters.categories = [];
        this.filters.sizes = [];
        this.filters.colors = [];
        this.filters.paged = 1;
        
        // Reset price slider
        if (this.priceSlider) {
            this.priceSlider.reset();
        }
        
        // Reset UI
        document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        document.querySelectorAll('.selected').forEach(el => el.classList.remove('selected'));
        document.getElementById('sort-by').value = 'menu_order';
        
        this.loadProducts();
    }

    updateURL() {
        const params = new URLSearchParams();
        
        Object.keys(this.filters).forEach(key => {
            if (Array.isArray(this.filters[key]) && this.filters[key].length > 0) {
                params.append(key, this.filters[key].join(','));
            } else if (typeof this.filters[key] === 'string' && this.filters[key] !== 'menu_order') {
                params.append(key, this.filters[key]);
            }
        });
        
        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.history.replaceState({}, '', newUrl);
    }

    showLoading() {
        document.getElementById('loading-spinner').classList.remove('hidden');
        document.getElementById('products-grid').style.opacity = '0.5';
    }

    hideLoading() {
        document.getElementById('loading-spinner').classList.add('hidden');
        document.getElementById('products-grid').style.opacity = '1';
    }

    showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = `fixed top-4 right-4 text-white px-6 py-3 rounded-md transition-opacity duration-300 z-50 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        }`;
        toast.style.opacity = '1';
        
        setTimeout(() => {
            toast.style.opacity = '0';
        }, 3000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.wooFilters = new WooCommerceFilters();
});
</script>

<style>
/* Price Slider Styling */
#price-slider .noUi-target {
    background: #f3f4f6;
    border-radius: 999px;
    border: none;
    box-shadow: none;
    height: 8px;
}

#price-slider .noUi-connect {
    background: #ed1c24;
    border-radius: 999px;
}

#price-slider .noUi-horizontal .noUi-handle {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #fff;
    border: 4px solid #ed1c24;
    box-shadow: 0 2px 8px rgba(237, 28, 36, 0.10);
    top: -8px;
    cursor: pointer;
}

#price-slider .noUi-handle:after,
#price-slider .noUi-handle:before {
    display: none;
}

/* Filter Option Styling */
.size-option.selected span,
.color-option.selected {
    border-color: #ed1c24 !important;
    background-color: #fef2f2;
    color: #ed1c24;
}

.color-option.selected .rounded-full {
    border-color: #ed1c24 !important;
    border-width: 3px;
}

/* Loading Animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Add to Cart Button States */
.ajax-add-to-cart:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Pagination Styling */
.page-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px 12px;
    margin: 0 2px;
    text-decoration: none;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    color: #374151;
    transition: all 0.2s;
    cursor: pointer;
}

.page-link:hover,
.page-link.current {
    background-color: #ed1c24;
    border-color: #ed1c24;
    color: white;
}

/* Toast Notification */
#toast {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    font-weight: 500;
}
</style>