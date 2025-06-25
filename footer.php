<!-- Search Button -->
<button class="action-btn search-btn" type="button" aria-label="Search">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"></circle>
        <path d="m21 21-4.3-4.3"></path>
    </svg>
</button>

<!-- Search Popup Modal -->
<div id="product-search-modal" class="search-modal" style="display:none;">
  <div class="search-modal-content">
    <div class="search-modal-header">
      <div class="search-modal-title">Search Products</div>
      <button class="close-search-modal" aria-label="Close">&times;</button>
    </div>
    <div class="search-bar" style="position:relative; border:2px solid #e74c3c; border-radius:12px; background:#fff; box-shadow:none; padding:0;">
      <input type="text" id="product-search-input" placeholder="Search for products..." autocomplete="off" style="height:56px;">
      <button id="product-search-btn" aria-label="Search" style="height:56px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.3-4.3"></path>
        </svg>
      </button>
    </div>
    <div id="popular-searches" class="popular-searches"></div>
    <div class="search-modal-body">
      <div class="popular-products-title">Popular Products</div>
      <div class="trending-products">
        <!-- Products will be loaded here via JavaScript -->
      </div>
    </div>
  </div>
</div>

<!-- Improved CSS for popup design and scrollable results -->
<style>
.search-modal {
  position: fixed; z-index: 9999; left: 0; top: 0; width: 100vw; height: 100vh;
  background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center;
}
.search-modal-content {
  background: #fff;
  border-radius: 16px;
  padding: 32px 28px 28px 28px;
  width: 95%;
  max-width: 480px;
  position: relative;
  box-shadow: 0 8px 32px rgba(0,0,0,0.18);
  max-height: 90vh;
  display: flex;
  flex-direction: column;
}
.search-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 18px;
  padding-top: 8px;
}
.search-modal-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111;
  margin-left: 8px;
  letter-spacing: 0.01em;
}
.close-search-modal {
  font-size: 2rem;
  background: none;
  border: none;
  color: #222;
  cursor: pointer;
  margin: 0 4px 0 0;
  padding: 0;
  line-height: 1;
}
.search-bar {
  display: flex;
  position: relative;
  margin-bottom: 24px;
  border: 1.5px solid #e5e5e5;
  border-radius: 12px;
  background: #fff;
  box-shadow: none;
  padding: 0;
}
.search-bar input {
  flex: 1;
  padding: 16px 18px;
  border: none;
  border-radius: 12px 0 0 12px;
  font-size: 1.1rem;
  outline: none;
  background: #fff;
  color: #222;
  font-weight: 400;
  transition: border 0.2s;
  box-shadow: none;
  height: 56px;
}
.search-bar input::placeholder {
  color: #bcbcbc;
  opacity: 1;
  font-weight: 400;
}
.search-bar button {
  background: #e74c3c;
  border: none;
  border-radius: 0 12px 12px 0;
  color: #fff;
  padding: 0 22px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
  font-size: 1.2rem;
  height: 56px;
}
.search-bar button:hover {
  background: #c0392b;
}
.popular-searches > div:first-child {
  font-weight: 600;
  margin-bottom: 10px;
  color: #222;
}
.popular-tags {
  display: flex;
  gap: 12px;
  margin-bottom: 28px;
  flex-wrap: wrap;
}
.popular-tag {
  background: #f1f1f1;
  border: none;
  border-radius: 18px;
  padding: 9px 22px;
  cursor: pointer;
  font-size: 1.05rem;
  color: #333;
  transition: background 0.2s;
  font-weight: 500;
}
.popular-tag:hover {
  background: #e0e0e0;
}
.trending-products {
  margin-bottom: 0;
}
.trending-products .trending-title {
  display: flex;
  align-items: center;
  gap: 7px;
  font-weight: 700;
  color: #222;
  font-size: 1.13rem;
  margin-bottom: 18px;
}
.trending-products .trending-title .trending-icon {
  color: #e74c3c;
  font-size: 1.2em;
}
.search-result-item {
  display: flex; align-items: center; gap: 14px; margin-bottom: 16px;
  text-decoration: none; color: #222; background: none; border: none;
  transition: background 0.15s;
  padding: 4px 2px; border-radius: 8px;
}
.search-result-item:hover {
  background: #f8f8f8;
}
.search-result-item img {
  min-width: 48px; min-height: 48px; width: 48px; height: 48px;
}
````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````.search-results {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  z-index: 1001;
  background: #fff;
  border: 1px solid #eee;
  border-top: none;
  max-height: 220px;
  overflow-y: auto;
  box-shadow: 0 8px 24px rgba(0,0,0,0.08);
  margin: 0;
  padding: 0;
  border-radius: 0 0 10px 10px;
  display: none;
}````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````
@media (max-width: 600px) {
  .search-modal-content {
    padding: 18px 6px 12px 6px;
    max-width: 98vw;
  }
  .search-bar input { font-size: 1rem; }
  .search-result-item img { width: 38px; height: 38px; min-width: 38px; min-height: 38px; }
  .search-result-item div > div:first-child { font-size: 0.98rem; }
  .pp-product img { width: 38px; height: 38px; min-width: 38px; min-height: 38px; }
  .pp-title { font-size: 0.98rem; }
}
.pp-product {
  display: flex;
  align-items: center;
  gap: 18px;
  margin-bottom: 22px;
  text-decoration: none;
  color: #222;
  background: none;
  border: none;
  padding: 0;
  border-radius: 8px;
  transition: background 0.15s;
}
.pp-product:hover {
  background: #f8f8f8;
}
.pp-product img {
  width: 48px;
  height: 48px;
  object-fit: cover;
  border-radius: 8px;
  background: #f1f1f1;
  flex-shrink: 0;
}
.pp-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.pp-title {
  font-weight: 700;
  font-size: 1.08rem;
  margin-bottom: 2px;
}
.pp-cat {
  color: #888;
  font-size: 0.98rem;
  font-weight: 400;
  margin-bottom: 2px;
}
.pp-price {
  color: #e74c3c;
  font-weight: 700;
  font-size: 1.08rem;
}
.popular-products-title {
  font-weight: 700;
  font-size: 1.13rem;
  color: #222;
  margin-bottom: 18px;
  margin-top: 10px;
  letter-spacing: 0.01em;
}
</style>

<!-- Inline jQuery for demonstration (move to your JS file for production) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
jQuery(document).ready(function($) {
  $('.search-btn').on('click', function() {
    $('#product-search-modal').show();
    loadPopularSearches();
    loadPopularProducts();
  });

  $('.close-search-modal').on('click', function() {
    $('#product-search-modal').hide();
    $('#search-results').html('');
    $('#product-search-input').val('');
  });

  // Redirect to WooCommerce search results page on button click
  $('#product-search-btn').on('click', function(e) {
    e.preventDefault();
    var query = $('#product-search-input').val();
    if (query.length > 1) {
      window.location.href = '/?s=' + encodeURIComponent(query) + '&post_type=product';
    }
  });

  function loadPopularSearches() {
    $.get('/wp-json/custom/v1/popular-searches', function(data) {
      var html = '<div>Popular Searches</div>';
      html += '<div class="popular-tags">';
      $.each(data, function(i, tag) {
        html += '<button class="popular-tag" data-term="' + tag.name + '">' + tag.name + '</button>';
      });
      html += '</div>';
      $('#popular-searches').html(html);
      $('.popular-tag').on('click', function() {
        $('#product-search-input').val($(this).data('term')).trigger('input');
      });
    });
  }

  // Function to load popular products via AJAX
  function loadPopularProducts() {
    // Try to use WooCommerce v3 API for real images (requires API keys)
    var wcApiUrl = '/wp-json/wc/v3/products?per_page=3&orderby=date&consumer_key=ck_31f4b7c46b0268d07ba38a90bd56da7c577fbc73&consumer_secret=cs_34e9b1554beabef2ee5ddce0161d3f1f36d217bc';
    $.get(wcApiUrl, function(data) {
      var html = '';
      if (data.length > 0) {
        $.each(data, function(i, product) {
          html += '<a class="pp-product" href="' + product.permalink + '">' +
            '<img src="' + (product.images && product.images[0] ? product.images[0].src : 'https://via.placeholder.com/48x48?text=Img') + '" alt="' + product.name + '" />' +
            '<div class="pp-info">' +
              '<div class="pp-title">' + product.name + '</div>' +
              (product.categories && product.categories.length ? '<div class="pp-cat">' + product.categories.map(function(cat){return cat.name;}).join(', ') + '</div>' : '') +
              (product.price ? '<div class="pp-price">$' + product.price + '</div>' : '') +
            '</div>' +
          '</a>';
        });
      } else {
        html += '<div>No products found</div>';
      }
      $('.trending-products').html(html);
    }).fail(function() {
      // Fallback to placeholder images if v3 API is not available
      $.get('/wp-json/wp/v2/product?per_page=3&orderby=date', function(data) {
        var html = '';
        if (data.length > 0) {
          $.each(data, function(i, product) {
            html += '<a class="pp-product" href="' + product.link + '">' +
              '<img src="https://via.placeholder.com/48x48?text=Img" alt="' + product.title.rendered + '" />' +
              '<div class="pp-info">' +
                '<div class="pp-title">' + product.title.rendered + '</div>' +
              '</div>' +
            '</a>';
          });
        } else {
          html += '<div>No products found</div>';
        }
        $('.trending-products').html(html);
      });
    });
  }
});
</script>