// [REMOVED] Custom product details add-to-cart logic. File intentionally left blank to disable custom product details JS.

document.addEventListener('DOMContentLoaded', function() {
    // Quantity buttons functionality
    const quantityInput = document.querySelector('.quantity-input');
    const minusButton = document.querySelector('.quantity-minus');
    const plusButton = document.querySelector('.quantity-plus');

    if (quantityInput && minusButton && plusButton) {
        minusButton.addEventListener('click', function() {
            const currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });

        plusButton.addEventListener('click', function() {
            const currentValue = parseInt(quantityInput.value);
            quantityInput.value = currentValue + 1;
        });
    }

    // Product image gallery
    const mainImage = document.querySelector('.aspect-h-1.aspect-w-1 img');
    const galleryImages = document.querySelectorAll('.grid img');

    if (mainImage && galleryImages.length > 0) {
        galleryImages.forEach(img => {
            img.addEventListener('click', function() {
                // Swap the main image with the clicked gallery image
                const tempSrc = mainImage.src;
                mainImage.src = this.src;
                this.src = tempSrc;

                // Update active state
                galleryImages.forEach(galleryImg => {
                    galleryImg.parentElement.classList.remove('ring-2', 'ring-[#FF3A5E]');
                });
                this.parentElement.classList.add('ring-2', 'ring-[#FF3A5E]');
            });
        });
    }

    // Variable product attribute selection
    const attributeInputs = document.querySelectorAll('input[type="radio"][name^="attribute_"]');
    
    if (attributeInputs.length > 0) {
        attributeInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Remove active state from all labels in the same group
                const group = this.closest('fieldset');
                group.querySelectorAll('label').forEach(label => {
                    label.classList.remove('ring-2', 'ring-[#FF3A5E]');
                });
                
                // Add active state to selected label
                this.closest('label').classList.add('ring-2', 'ring-[#FF3A5E]');
            });
        });
    }

    // Add to cart form submission
    const addToCartForm = document.querySelector('form');
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(wc_add_to_cart_params.ajax_url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const message = document.createElement('div');
                    message.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                    message.textContent = 'Product added to cart successfully!';
                    document.body.appendChild(message);
                    
                    // Remove message after 3 seconds
                    setTimeout(() => {
                        message.remove();
                    }, 3000);

                    // Update cart count if available
                    if (data.cart_count) {
                        const cartCount = document.querySelector('.cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.cart_count;
                        }
                    }
                } else {
                    // Show error message
                    const message = document.createElement('div');
                    message.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                    message.textContent = data.error || 'Failed to add product to cart';
                    document.body.appendChild(message);
                    
                    // Remove message after 3 seconds
                    setTimeout(() => {
                        message.remove();
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
});

document.addEventListener('alpine:init', () => {
    Alpine.data('productDetails', () => ({
        quantity: 1,
        selectedSize: null,
        selectedColor: null,
        isAddingToCart: false,
        message: '',

        init() {
            // Initialize any product-specific data
            this.$watch('quantity', (value) => {
                if (value < 1) this.quantity = 1;
            });
        },

        addToCart() {
            if (!this.selectedSize || !this.selectedColor) {
                this.message = 'Please select both size and color';
                return;
            }

            this.isAddingToCart = true;
            this.message = '';

            const productData = {
                id: this.$el.dataset.productId,
                name: this.$el.dataset.productName,
                price: parseFloat(this.$el.dataset.productPrice),
                image: this.$el.dataset.productImage,
                url: this.$el.dataset.productUrl,
                quantity: this.quantity,
                size: this.selectedSize,
                color: this.selectedColor
            };

            // Add to cart store
            this.$store.cart.addItem(productData);
            
            // Show success message
            this.message = 'Added to cart successfully!';
            this.isAddingToCart = false;

            // Reset form after 2 seconds
            setTimeout(() => {
                this.message = '';
                this.quantity = 1;
            }, 2000);
        },

        selectSize(size) {
            this.selectedSize = size;
            this.message = '';
        },

        selectColor(color) {
            this.selectedColor = color;
            this.message = '';
        }
    }));
}); 