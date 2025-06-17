jQuery(document).ready(function($) {
    // Blog search functionality
    let searchTimeout;
    $('#blog-search').on('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = $(this).val();
        
        searchTimeout = setTimeout(function() {
            filterPosts(searchTerm);
        }, 500);
    });

    // Category filter functionality
    $('[data-category]').on('click', function() {
        const category = $(this).data('category');
        
        // Update active state
        $('[data-category]').removeClass('bg-white shadow-sm').addClass('bg-transparent');
        $(this).addClass('bg-white shadow-sm').removeClass('bg-transparent');
        
        filterPosts('', category);
    });

    // Newsletter form submission
    $('#newsletter-form').on('submit', function(e) {
        e.preventDefault();
        const email = $(this).find('input[type="email"]').val();
        
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
                    alert('Thank you for subscribing!');
                    $('#newsletter-form')[0].reset();
                } else {
                    alert(response.data || 'An error occurred. Please try again.');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });

    // Function to filter posts
    function filterPosts(searchTerm = '', category = 'all') {
        $.ajax({
            url: blogAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'filter_blog_posts',
                search: searchTerm,
                category: category,
                nonce: blogAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Update featured post if exists
                    if (response.data.featured) {
                        updateFeaturedPost(response.data.featured);
                    }
                    
                    // Update latest posts
                    $('#latest-posts').html(response.data.posts);
                }
            }
        });
    }

    // Function to update featured post
    function updateFeaturedPost(post) {
        const featuredHtml = `
            <div class="mb-16">
                <div class="grid md:grid-cols-2 gap-8 items-center bg-gray-50 rounded-2xl overflow-hidden">
                    <div class="aspect-square md:aspect-auto md:h-full relative flex items-center justify-center">
                        <img src="${post.thumbnail}" alt="${post.title}" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6 md:p-10 md:pr-12">
                        <span class="inline-block mb-4 px-3 py-1 bg-[#FF3A5E] text-white text-xs font-semibold rounded-full">Featured</span>
                        <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-4">${post.title}</h2>
                        <p class="text-gray-600 mb-6 text-lg">${post.excerpt}</p>
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 rounded-full overflow-hidden relative">
                                    <img src="${post.author_avatar}" alt="${post.author}" class="w-full h-full object-cover">
                                </div>
                                <span class="text-gray-700 font-medium">${post.author}</span>
                            </div>
                            <div class="flex items-center gap-1 text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <span>${post.date}</span>
                            </div>
                        </div>
                        <a href="${post.link}" class="inline-flex items-center px-4 py-2 bg-[#FF3A5E] hover:bg-[#FF3A5E]/90 text-white rounded-md font-medium">
                            Read Article 
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        `;
        
        $('.featured-post-container').html(featuredHtml);
    }
}); 