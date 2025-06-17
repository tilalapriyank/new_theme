jQuery(document).ready(function($){
  // Open modal and load product details
  $(document).on('click', '.quick-view-btn', function(){
    $('#quick-view-content').html('<div class="flex justify-center items-center min-h-[300px]"><div class="loader border-4 border-[#FF3A5E] border-t-transparent rounded-full w-12 h-12 animate-spin"></div></div>');
    $('#quick-view-modal').fadeIn();

    var product_id = $(this).data('product_id');
    $.ajax({
      url: quickview_ajax.ajax_url,
      type: 'POST',
      data: {
        action: 'load_quick_view',
        product_id: product_id
      },
      success: function(response){
        $('#quick-view-content').html(response);
      }
    });
  });

  // Close modal
  $(document).on('click', '.close-modal', function(){
    $('#quick-view-modal').fadeOut();
  });

  // Optional: Close modal when clicking outside the modal content
  $(document).on('click', '#quick-view-modal', function(e){
    if ($(e.target).is('#quick-view-modal')) {
      $('#quick-view-modal').fadeOut();
    }
  });

  // Attribute selection
  $(document).on('click', '.size-btn, .color-btn', function(){
    var $btn = $(this);
    var attr = $btn.data('attribute_name');
    $btn.closest('.flex-wrap').find('[data-attribute_name="'+attr+'"]').removeClass('bg-[#FF3A5E] text-white border-[#FF3A5E]');
    $btn.addClass('bg-[#FF3A5E] text-white border-[#FF3A5E]');
    $btn.closest('form').find('input[name="'+attr+'"], select[name="'+attr+'"]').val($btn.data('attribute_value')).trigger('change');
    $btn.closest('form').data(attr, $btn.data('attribute_value'));
  });

  // Quantity
  $(document).on('click', '.quantity-minus', function(){
    var $input = $(this).siblings('.quantity-input');
    var val = parseInt($input.val()) || 1;
    if(val > 1) $input.val(val-1);
  });
  $(document).on('click', '.quantity-plus', function(){
    var $input = $(this).siblings('.quantity-input');
    var val = parseInt($input.val()) || 1;
    $input.val(val+1);
  });

  // Add to Cart
  // [REMOVED] Custom quick view add-to-cart logic. File intentionally left blank to disable custom quick view JS.
}); 