// product-filter.js - AJAX filtering that bypasses Elementor
// Save this as js/product-filter.js in your theme

jQuery(document).ready(function($) {
    console.log('=== AJAX FILTER SYSTEM LOADED ===');
    
    // Check if we have filter parameters in URL
    const urlParams = new URLSearchParams(window.location.search);
    const hasFilters = urlParams.has('min_price') || urlParams.has('max_price') || 
                      urlParams.has('filter_pa_size') || urlParams.has('filter_pa_color') || 
                      urlParams.has('product_cat');
    
    if (hasFilters) {
        console.log('Filters detected in URL, applying AJAX filter...');
        applyAjaxFilter();
    }
    
    // Monitor form submissions
    $('#shop-filters-form').on('submit', function(e) {
        e.preventDefault();
        console.log('Filter form submitted, applying AJAX filter...');
        applyAjaxFilter();
    });
    
    function applyAjaxFilter() {
        console.log('Starting AJAX filter...');
        
        // Show loading state
        showLoadingState();
        
        // Get filter data from URL or form
        const filters = getFilterData();
        console.log('Filter data:', filters);
        
        // Make AJAX request
        $.ajax({
            url: product_filter_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'filter_products',
                nonce: product_filter_ajax.nonce,
                min_price: filters.min_price,
                max_price: filters.max_price,
                sizes: filters.sizes,
                colors: filters.colors,
                categories: filters.categories
            },
            success: function(response) {
                console.log('AJAX Response:', response);
                
                if (response.success) {
                    // Replace product grid with filtered results
                    replaceProductGrid(response.data.html);
                    
                    // Update product count
                    updateProductCount(response.data.count);
                    
                    // Show success message
                    showFilterMessage(response.data.count, response.data.filters_applied);
                    
                    console.log(`✅ Filter applied successfully: ${response.data.count} products found`);
                } else {
                    console.error('❌ Filter failed:', response);
                    showErrorMessage();
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX Error:', error);
                showErrorMessage();
            },
            complete: function() {
                hideLoadingState();
            }
        });
    }
    
    function getFilterData() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Handle URL-encoded comma-separated values
        const sizeParam = urlParams.get('filter_pa_size');
        const colorParam = urlParams.get('filter_pa_color');
        const categoryParam = urlParams.get('product_cat');
        
        return {
            min_price: urlParams.get('min_price') || '',
            max_price: urlParams.get('max_price') || '',
            sizes: sizeParam ? decodeURIComponent(sizeParam).split(',').map(s => s.trim()) : [],
            colors: colorParam ? decodeURIComponent(colorParam).split(',').map(c => c.trim()) : [],
            categories: categoryParam ? decodeURIComponent(categoryParam).split(',').map(cat => cat.trim()) : []
        };
    }
    
    function replaceProductGrid(newHtml) {
        // Find the product grid container
        const gridSelectors = [
            '.grid.grid-cols-2', // Your current grid
            '.products',
            '.woocommerce-products',
            '.product-grid',
            '[class*="grid"][class*="cols"]'
        ];
        
        let gridContainer = null;
        for (let selector of gridSelectors) {
            gridContainer = document.querySelector(selector);
            if (gridContainer) {
                console.log(`Found product grid: ${selector}`);
                break;
            }
        }
        
        if (gridContainer) {
            // If we have new HTML, replace content
            if (newHtml.trim()) {
                gridContainer.innerHTML = newHtml;
            } else {
                // Show "no products found" message
                gridContainer.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500">No products found matching your criteria.</p>
                        <a href="${window.location.pathname}" class="text-[#ed1c24] hover:underline mt-2 inline-block">Clear all filters</a>
                    </div>
                `;
            }
        } else {
            console.warn('Could not find product grid container');
        }
    }
    
    function updateProductCount(count) {
        // Update the product count display
        $('.woocommerce-result-count, .product-count').each(function() {
            $(this).text(count + ' products');
        });
        
        // Also update any custom count elements
        $('p:contains("products")').each(function() {
            const text = $(this).text();
            if (text.match(/\d+\s+products?/)) {
                $(this).text(count + ' products');
            }
        });
    }
    
    function showLoadingState() {
        // Add loading overlay to product grid
        const gridContainer = document.querySelector('.grid.grid-cols-2, .products');
        if (gridContainer) {
            gridContainer.style.position = 'relative';
            gridContainer.style.opacity = '0.5';
            
            // Add loading spinner
            const loader = document.createElement('div');
            loader.id = 'ajax-loader';
            loader.innerHTML = `
                <div style="
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background: white;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    z-index: 1000;
                ">
                    <div style="
                        width: 40px;
                        height: 40px;
                        border: 4px solid #f3f3f3;
                        border-top: 4px solid #ed1c24;
                        border-radius: 50%;
                        animation: spin 1s linear infinite;
                        margin: 0 auto 10px;
                    "></div>
                    <div>Filtering products...</div>
                </div>
            `;
            gridContainer.appendChild(loader);
        }
    }
    
    function hideLoadingState() {
        const gridContainer = document.querySelector('.grid.grid-cols-2, .products');
        if (gridContainer) {
            gridContainer.style.opacity = '1';
            const loader = document.getElementById('ajax-loader');
            if (loader) {
                loader.remove();
            }
        }
    }
    
    function showFilterMessage(count, filters) {
        // Create or update filter status message
        let statusEl = document.getElementById('ajax-filter-status');
        if (!statusEl) {
            statusEl = document.createElement('div');
            statusEl.id = 'ajax-filter-status';
            statusEl.style.cssText = `
                background: #e8f5e8;
                border: 1px solid #4caf50;
                color: #2e7d32;
                padding: 12px;
                margin: 15px 0;
                border-radius: 6px;
                font-weight: 500;
            `;
            
            // Insert before product grid
            const productGrid = document.querySelector('.grid.grid-cols-2, .products');
            if (productGrid) {
                productGrid.parentNode.insertBefore(statusEl, productGrid);
            }
        }
        
        let message = `✅ Found ${count} products`;
        const activeFilters = [];
        
        if (filters.sizes && filters.sizes.length > 0) {
            activeFilters.push(`Size: ${filters.sizes.join(', ')}`);
        }
        if (filters.colors && filters.colors.length > 0) {
            activeFilters.push(`Color: ${filters.colors.join(', ')}`);
        }
        if (filters.min_price || filters.max_price) {
            const priceRange = `$${filters.min_price || '0'} - $${filters.max_price || '∞'}`;
            activeFilters.push(`Price: ${priceRange}`);
        }
        
        if (activeFilters.length > 0) {
            message += ` matching: ${activeFilters.join(' | ')}`;
        }
        
        statusEl.textContent = message;
    }
    
    function showErrorMessage() {
        let statusEl = document.getElementById('ajax-filter-status');
        if (!statusEl) {
            statusEl = document.createElement('div');
            statusEl.id = 'ajax-filter-status';
            statusEl.style.cssText = `
                background: #ffebee;
                border: 1px solid #f44336;
                color: #c62828;
                padding: 12px;
                margin: 15px 0;
                border-radius: 6px;
                font-weight: 500;
            `;
            
            const productGrid = document.querySelector('.grid.grid-cols-2, .products');
            if (productGrid) {
                productGrid.parentNode.insertBefore(statusEl, productGrid);
            }
        }
        
        statusEl.textContent = '❌ Error applying filters. Please try again.';
    }
});

// Add CSS for loading animation
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);