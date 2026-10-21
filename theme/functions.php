<?php
defined('ABSPATH') || exit;
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('woocommerce', ['thumbnail_image_width' => 520, 'single_image_width' => 900, 'product_grid' => ['default_columns' => 4, 'default_rows' => 3]]);
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
});
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('nexo', get_stylesheet_uri(), ['woocommerce-general'], filemtime(get_stylesheet_directory() . '/style.css'));
    wp_enqueue_script('nexo', get_theme_file_uri('/assets/store.js'), [], filemtime(get_theme_file_path('/assets/store.js')), true);
});
add_filter('loop_shop_columns', fn() => 4);
add_action('wp_head', function () {
    echo '<link rel="icon" type="image/svg+xml" href="' . esc_url(get_theme_file_uri('/assets/nexo-mark.svg')) . '">';
});
add_filter('loop_shop_per_page', fn() => 12);
add_action('woocommerce_checkout_before_order_review_heading', function () { echo '<div class="nexo-review">'; });
add_action('woocommerce_checkout_after_order_review', function () { echo '</div>'; });
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
    $fragments['span.cart-count'] = '<span class="cart-count">' . absint(WC()->cart ? WC()->cart->get_cart_contents_count() : 0) . '</span>';
    return $fragments;
});
add_action('woocommerce_after_shop_loop_item_title', function () {
    global $product;
    $caption = $product->get_meta('_nexo_caption');
    if ($caption) echo '<p class="nexo-product-caption">' . esc_html($caption) . '</p>';
}, 7);
add_action('woocommerce_single_product_summary', function () {
    global $product;
    $use = $product->get_meta('_nexo_use');
    if ($use) echo '<div class="nexo-product-note">' . nexo_mark() . '<div><strong>Um lugar na sua rotina.</strong><p>' . esc_html($use) . '</p></div></div>';
}, 35);
function nexo_shopping_path(): void {
    if (is_order_received_page()) return;
    $checkout = is_checkout();
    echo '<nav class="nexo-shopping-path" aria-label="Etapas do pedido"><a href="' . esc_url(wc_get_cart_url()) . '"' . (!$checkout ? ' aria-current="step"' : '') . '>Seu carrinho</a>' . nexo_icon('arrow') . '<a href="' . esc_url(wc_get_checkout_url()) . '"' . ($checkout ? ' aria-current="step"' : '') . '>Finalizar pedido</a></nav>';
}
add_action('woocommerce_before_cart', 'nexo_shopping_path', 5);
add_action('woocommerce_before_checkout_form', 'nexo_shopping_path', 3);
add_action('woocommerce_shop_loop_item_title', function () {
    global $product;
    $terms = get_the_terms($product->get_id(), 'product_cat');
    if ($terms && !is_wp_error($terms)) {
        echo '<div class="product-category-caption">' . esc_html($terms[0]->name) . '</div>';
    }
}, 5);
function nexo_icon(string $name): string {
    $paths = [
        'search' => '<circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/>',
        'bag' => '<path d="M5 7h14l1 14H4L5 7Z"/><path d="M8 8V6a4 4 0 0 1 8 0v2"/>',
        'arrow' => '<path d="M4 12h16m-6-6 6 6-6 6"/>',
        'menu' => '<path d="M3 6h18M3 12h18M3 18h18"/>',
        'truck' => '<path d="M2 5h12v12H2zM14 9h4l4 4v4h-8"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="18" r="2"/>',
        'shield' => '<path d="m12 2 8 4v6c0 5-8 10-8 10S4 17 4 12V6l8-4Z"/><path d="m8 12 3 3 5-6"/>',
        'box' => '<path d="m3 7 9-5 9 5v11l-9 4-9-4V7Zm0 0 9 5 9-5M12 12v10M7 4l10 5"/>',
    ];
    return '<svg viewBox="0 0 24 24" aria-hidden="true">' . ($paths[$name] ?? $paths['arrow']) . '</svg>';
}
function nexo_brand(): void {
    echo '<a class="brand" href="' . esc_url(home_url('/')) . '" aria-label="Nexo, página inicial">nexo' . nexo_mark() . '</a>';
}
function nexo_mark(): string {
    return '<svg class="nexo-mark" viewBox="0 0 40 40" aria-hidden="true" fill="none"><rect x="4" y="4" width="21" height="21" rx="7" stroke="currentColor" stroke-width="3.5"/><rect x="15" y="15" width="21" height="21" rx="7" stroke="currentColor" stroke-width="3.5"/></svg>';
}
