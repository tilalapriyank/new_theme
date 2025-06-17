<?php
/**
 * Product Category Widget (Static)
 *
 * Usage Instructions:
 * 1. Place this file in your theme or plugin directory.
 * 2. Include it in your desired template with:
 *    include_once get_template_directory() . '/ProductCategoryWidget.php';
 *    (Adjust the path as needed.)
 * 3. This widget displays four static product categories as cards, matching the provided design.
 */
?>
<style>
.product-category-widget-section {
  margin-bottom: 2.5rem;
}
.product-category-widget-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.product-category-widget-title {
  font-weight: 600;
  margin-bottom: 0.5rem;
  font-size: 1.5rem;
  color: #222;
}
.product-category-widget-link {
  font-size: 1rem;
  color: #222;
  text-decoration: none;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 4px;
}
.product-category-widget-grid {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 2rem;
}
@media (min-width: 640px) {
  .product-category-widget-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media (min-width: 900px) {
  .product-category-widget-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}
.product-category-widget-card {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
  background: linear-gradient(180deg, #e3e3e3 0%, #bdbdbd 100%);
  min-height: 320px;
  display: flex;
  align-items: flex-end;
  box-shadow: 0 2px 12px rgba(0,0,0,0.10);
  transition: box-shadow 0.3s;
}
.product-category-widget-card:hover {
  box-shadow: 0 6px 24px rgba(0,0,0,0.18);
}
.product-category-widget-image {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  object-fit: cover;
  opacity: 0.7;
  z-index: 1;
  transition: transform 0.4s cubic-bezier(.4,0,.2,1);
}
.product-category-widget-card:hover .product-category-widget-image {
  transform: scale(1.07);
}
.product-category-widget-gradient {
  position: absolute;
  left: 0; bottom: 0; width: 100%; height: 100%;
  background: linear-gradient(180deg, rgba(0,0,0,0.00) 60%, rgba(0,0,0,0.70) 100%);
  z-index: 2;
  transition: background 0.4s;
}
.product-category-widget-content {
  position: relative;
  z-index: 3;
  padding: 0 0 32px 24px;
  color: #fff;
}
.product-category-widget-card-title {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  letter-spacing: -0.5px;
}
.product-category-widget-shop-link {
  display: inline-flex;
  align-items: center;
  font-size: 1.1rem;
  color: #fff;
  text-decoration: none;
  font-weight: 400;
  transition: color 0.2s;
  opacity: 0.9;
}
.product-category-widget-shop-link:hover {
  color: #FF3A5E;
}
.product-category-widget-shop-link svg {
  margin-left: 4px;
  width: 18px;
  height: 18px;
}
</style>
<section class="product-category-widget-section">
  <div class="product-category-widget-header">
    <h3 class="product-category-widget-title">Shop by Category</h3>
    <a href="#" class="product-category-widget-link">
      View All Categories
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
    </a>
  </div>
  <ul class="product-category-widget-grid">
    <li>
      <a href="#" class="product-category-widget-card">
        <img src="https://via.placeholder.com/400x400?text=Jackets" alt="Jackets" class="product-category-widget-image" loading="lazy" />
        <div class="product-category-widget-gradient"></div>
        <div class="product-category-widget-content">
          <div class="product-category-widget-card-title">Jackets</div>
          <span class="product-category-widget-shop-link">
            Shop Now
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </span>
        </div>
      </a>
    </li>
    <li>
      <a href="#" class="product-category-widget-card">
        <img src="https://via.placeholder.com/400x400?text=Hoodies" alt="Hoodies" class="product-category-widget-image" loading="lazy" />
        <div class="product-category-widget-gradient"></div>
        <div class="product-category-widget-content">
          <div class="product-category-widget-card-title">Hoodies</div>
          <span class="product-category-widget-shop-link">
            Shop Now
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </span>
        </div>
      </a>
    </li>
    <li>
      <a href="#" class="product-category-widget-card">
        <img src="https://via.placeholder.com/400x400?text=Harnesses" alt="Harnesses" class="product-category-widget-image" loading="lazy" />
        <div class="product-category-widget-gradient"></div>
        <div class="product-category-widget-content">
          <div class="product-category-widget-card-title">Harnesses</div>
          <span class="product-category-widget-shop-link">
            Shop Now
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </span>
        </div>
      </a>
    </li>
    <li>
      <a href="#" class="product-category-widget-card">
        <img src="https://via.placeholder.com/400x400?text=Accessories" alt="Accessories" class="product-category-widget-image" loading="lazy" />
        <div class="product-category-widget-gradient"></div>
        <div class="product-category-widget-content">
          <div class="product-category-widget-card-title">Accessories</div>
          <span class="product-category-widget-shop-link">
            Shop Now
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </span>
        </div>
      </a>
    </li>
  </ul>
</section> 