// [REMOVED] Custom add-to-cart and filter JS. File intentionally left blank to disable custom shop JS.

// Shop functionality
function addToCart(productId) {
    // Add to cart functionality
    const formData = new FormData();
    formData.append('action', 'add_to_cart');
    formData.append('product_id', productId);
    formData.append('quantity', 1);

    fetch(wc_add_to_cart_params.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = data.cart_count;
            }
            
            // Show success message
            const message = document.createElement('div');
            message.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg';
            message.textContent = 'Product added to cart!';
            document.body.appendChild(message);
            
            setTimeout(() => {
                message.remove();
            }, 3000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function addToWishlist(productId) {
    // Add to wishlist functionality
    showToast('Product added to wishlist');
}

function showToast(message) {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.textContent = message;
        toast.classList.remove('opacity-0');
        toast.classList.add('opacity-100');
        
        setTimeout(() => {
            toast.classList.remove('opacity-100');
            toast.classList.add('opacity-0');
        }, 3000);
    }
}

// Mobile filters toggle
document.addEventListener('DOMContentLoaded', function() {
    // Mobile Filters Toggle
    const filterButton = document.querySelector('.mobile-filters-button');
    const sidebar = document.querySelector('.shop-sidebar');
    const closeButton = document.querySelector('.close-filters');

    if (filterButton && sidebar) {
        filterButton.addEventListener('click', () => {
            sidebar.classList.remove('hidden');
        });

        if (closeButton) {
            closeButton.addEventListener('click', () => {
                sidebar.classList.add('hidden');
            });
        }
    }

    // Quick View Functionality
    const quickViewButtons = document.querySelectorAll('.quick-view');
    quickViewButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const productData = JSON.parse(button.dataset.product);
            openQuickView(productData);
        });
    });
});

function openQuickView(productData) {
    // Create modal HTML
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.innerHTML = `
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                <div class="absolute right-0 top-0 pr-4 pt-4">
                    <button type="button" class="close-quick-view rounded-md bg-white text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <div class="aspect-square w-full overflow-hidden rounded-lg bg-gray-100 mb-4">
                            <img src="${productData.image}" alt="${productData.name}" class="h-full w-full object-cover object-center">
                        </div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900">${productData.name}</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">${productData.category}</p>
                            <p class="mt-1 text-lg font-medium text-gray-900">${productData.price}</p>
                        </div>
                        <div class="mt-4">
                            <button type="button" class="add-to-cart-quick-view inline-flex w-full justify-center rounded-md border border-transparent bg-[#FF3A5E] px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-[#FF3A5E]/90 focus:outline-none focus:ring-2 focus:ring-[#FF3A5E] focus:ring-offset-2 sm:text-sm" data-product-id="${productData.id}">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Add modal to page
    document.body.appendChild(modal);

    // Handle close button
    const closeButton = modal.querySelector('.close-quick-view');
    closeButton.addEventListener('click', () => {
        modal.remove();
    });

    // Handle add to cart
    const addToCartButton = modal.querySelector('.add-to-cart-quick-view');
    addToCartButton.addEventListener('click', () => {
        const productId = addToCartButton.dataset.productId;
        addToCart(productId);
    });
} 