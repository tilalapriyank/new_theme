jQuery(document).ready(function($) {
    const newsletterForm = $('#newsletter-form');
    
    if (newsletterForm.length) {
        newsletterForm.on('submit', function(e) {
            e.preventDefault();
            
            const email = $(this).find('input[name="email"]').val();
            const submitButton = $(this).find('button[type="submit"]');
            const originalButtonText = submitButton.html();
            
            // Disable button and show loading state
            submitButton.prop('disabled', true).html(`
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Subscribing...
            `);
            
            // Send AJAX request
            $.ajax({
                url: newsletterAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'newsletter_subscription',
                    email: email,
                    nonce: newsletterAjax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        newsletterForm.html(`
                            <div class="text-center py-4">
                                <svg class="mx-auto h-12 w-12 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <p class="mt-2 text-white">${response.data}</p>
                            </div>
                        `);
                    } else {
                        // Show error message
                        alert(response.data);
                        submitButton.prop('disabled', false).html(originalButtonText);
                    }
                },
                error: function() {
                    // Show error message
                    alert('An error occurred. Please try again later.');
                    submitButton.prop('disabled', false).html(originalButtonText);
                }
            });
        });
    }
}); 