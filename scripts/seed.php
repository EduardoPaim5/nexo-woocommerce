<?php
defined('ABSPATH') || exit;
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

function nexo_page($slug, $title, $content) {
    $existing = get_page_by_path($slug);
    return wp_insert_post(['ID' => $existing ? $existing->ID : 0, 'post_title' => $title, 'post_name' => $slug, 'post_content' => $content, 'post_status' => 'publish', 'post_type' => 'page']);
}
$shop = nexo_page('loja', 'Todos os produtos', '');
$cart = nexo_page('carrinho', 'Seu carrinho', '[woocommerce_cart]');
$checkout = nexo_page('finalizar-compra', 'Finalizar pedido de demonstração', '[woocommerce_checkout]');
$account = nexo_page('minha-conta', 'Minha conta', '[woocommerce_my_account]');
$home = nexo_page('inicio', 'Nexo', '');
update_option('woocommerce_shop_page_id', $shop);
update_option('woocommerce_cart_page_id', $cart);
update_option('woocommerce_checkout_page_id', $checkout);
update_option('woocommerce_myaccount_page_id', $account);
update_option('show_on_front', 'page');
update_option('page_on_front', $home);
update_option('blogdescription', 'Objetos para trabalhar, ouvir e aproveitar seu espaço.');
update_option('timezone_string', 'America/Sao_Paulo');
update_option('woocommerce_currency', 'BRL');
update_option('woocommerce_price_decimal_sep', ',');
update_option('woocommerce_price_thousand_sep', '.');
update_option('woocommerce_currency_pos', 'left_space');
update_option('woocommerce_default_country', 'BR:RS');
update_option('woocommerce_default_customer_address', 'base');
update_option('woocommerce_allowed_countries', 'specific');
update_option('woocommerce_specific_allowed_countries', ['BR']);
update_option('woocommerce_ship_to_countries', 'specific');
update_option('woocommerce_specific_ship_to_countries', ['BR']);
update_option('woocommerce_calc_taxes', 'no');
update_option('woocommerce_enable_coupons', 'yes');
update_option('woocommerce_enable_guest_checkout', 'yes');
update_option('woocommerce_enable_signup_and_login_from_checkout', 'no');
update_option('woocommerce_enable_signup_from_checkout', 'no');
update_option('woocommerce_checkout_phone_field', 'optional');
update_option('woocommerce_ship_to_destination', 'billing_only');
update_option('woocommerce_coming_soon', 'no');
update_option('woocommerce_store_pages_only', 'no');
update_option('woocommerce_enable_reviews', 'no');
update_option('woocommerce_allow_tracking', 'no');
update_option('woocommerce_checkout_privacy_policy_text', 'Use apenas dados fictícios neste projeto de demonstração. Consulte a [privacy_policy] para entender o funcionamento do ambiente.');
update_option('woocommerce_task_list_hidden', 'yes');
update_option('woocommerce_onboarding_profile', ['completed' => true]);
update_option('woocommerce_permalinks', ['product_base' => 'produto', 'category_base' => 'categoria-produto', 'tag_base' => 'tag-produto']);

$about = '<p>A Nexo é uma loja fictícia criada por Eduardo dos Santos Paim como projeto pessoal de portfólio. Produtos, preços e estoque são ilustrativos. Não fazemos vendas ou entregas.</p><h2>O que você pode testar</h2><ul><li>Navegação, busca e categorias do catálogo.</li><li>Produtos simples e variações de cor.</li><li>Carrinho, quantidades, cupom NEXO10 e checkout com dados fictícios.</li><li>Frete grátis a partir de R$ 350 após descontos, no Sul e Sudeste. Demais situações têm frete demonstrativo de R$ 24,90.</li></ul><h2>Implementação</h2><p>Tema próprio em PHP, CSS e JavaScript sobre WordPress/WooCommerce. O plugin Nexo Commerce implementa uma política de frete configurável e um gateway de pedidos simulados. A regra de frete tem testes independentes. O catálogo usa produtos e pedidos reais do WooCommerce, sem processar pagamentos.</p><h2>Sobre o autor</h2><p>Eduardo é estudante de Engenharia de Software na UNIPAMPA e desenvolve aplicações com PHP/Laravel. Este projeto amplia seu portfólio para o ecossistema WordPress.</p><p><a href="https://github.com/EduardoPaim5">GitHub de Eduardo</a> | <a href="https://www.linkedin.com/in/eduardo-ds-paim/">LinkedIn</a></p><h2>Créditos</h2><p>Fotografias de produto e ambiente: Unsplash. Fontes: Manrope (SIL Open Font License). Os objetos fotografados ilustram produtos fictícios, sem vínculo com fabricantes.</p>';
$about = str_replace('Fotografias de produto e ambiente: Unsplash.', 'Imagens conceituais dos produtos geradas com IA para esta coleção. Fotografia de ambiente: Unsplash.', $about);
nexo_page('sobre-o-projeto', 'Uma loja para demonstrar código.', $about);
$privacy = nexo_page('privacidade', 'Privacidade da demonstração', '<p>Este ambiente é um projeto de portfólio, sem vendas reais. Use somente nome, e-mail e endereço fictícios no checkout.</p><p>O WooCommerce usa cookies de sessão para manter o carrinho. Pedidos de demonstração ficam no banco local para permitir a validação do fluxo. O plugin bloqueia o envio de e-mails e nenhum dado financeiro é solicitado.</p><p>Não há ferramentas de publicidade ou analytics adicionadas pelo projeto. Antes de uma publicação pública, o responsável deve definir proteção contra abuso, prazo de exclusão de pedidos e regras de privacidade do ambiente hospedado.</p>');
update_option('wp_page_for_privacy_policy', $privacy);
foreach (['audio' => 'Áudio', 'mesa' => 'Mesa e organização', 'acessorios' => 'Acessórios'] as $slug => $name) {
    if (!term_exists($slug, 'product_cat')) wp_insert_term($name, 'product_cat', ['slug' => $slug]);
}

$items = [
 ['NEXO-A01','Headphone Studio One','audio','399.00','349.00','headphones.webp','variable'],
 ['NEXO-M01','Teclado Forma 75','mesa','289.00','','keyboard.webp','simple'],
 ['NEXO-A02','Fone Pocket Buds','audio','199.00','','earbuds.webp','simple'],
 ['NEXO-M02','Mouse Orbit','mesa','129.00','','mouse.webp','simple'],
 ['NEXO-M03','Deskmat Essencial','mesa','119.00','','deskmat.webp','simple'],
 ['NEXO-C01','Hub Conecta USB-C','acessorios','179.00','','hub.webp','simple'],
 ['NEXO-A03','Caixa de Som Mono','audio','249.00','','speaker.webp','simple'],
 ['NEXO-M04','Suporte Eleva','mesa','159.00','','stand.webp','simple'],
];
$product_copy = require '/opt/nexo/product-copy.php';
foreach ($items as $i => [$sku,$title,$category,$regular,$sale,$image,$type]) {
    $id = wc_get_product_id_by_sku($sku);
    $product = $id ? wc_get_product($id) : ($type === 'variable' ? new WC_Product_Variable() : new WC_Product_Simple());
    $product->set_name($title);
    $product->set_slug(sanitize_title($title));
    $product->set_sku($sku);
    $product->set_status('publish');
    $product->set_catalog_visibility('visible');
    $copy = $product_copy[$sku];
    $product->set_description($copy['description'] . '<p class="nexo-concept-disclaimer">Modelo conceitual criado para este portfólio. Imagens e características são ilustrativas; o produto não está à venda.</p>');
    $product->set_short_description($copy['caption']);
    $product->update_meta_data('_nexo_caption', $copy['caption']);
    $product->update_meta_data('_nexo_use', $copy['use']);
    $product->set_menu_order($i);
    $product->set_reviews_allowed(false);
    $product->set_weight('0.5');
    $term = get_term_by('slug', $category, 'product_cat');
    $product->set_category_ids([$term->term_id]);
    if ($type === 'simple') {
        $product->set_regular_price($regular);
        $product->set_sale_price($sale);
        $product->set_manage_stock(true);
        if (!$id) $product->set_stock_quantity(30);
    } else {
        $attribute = new WC_Product_Attribute();
        $attribute->set_name('Cor');
        $attribute->set_options(['Grafite', 'Areia']);
        $attribute->set_visible(true);
        $attribute->set_variation(true);
        $product->set_attributes([$attribute]);
        $product->set_default_attributes(['cor' => 'Grafite']);
    }
    $id = $product->save();
    if (!$product->get_image_id() || $product->get_meta('_nexo_image_revision') !== 'studio-v2') {
        $old_image = $product->get_image_id();
        $temp = wp_tempnam($image);
        copy('/opt/nexo-assets/' . $image, $temp);
        $media = media_handle_sideload(['name' => $image, 'tmp_name' => $temp], $id, $title);
        if (is_wp_error($media)) throw new RuntimeException($media->get_error_message());
        update_post_meta($media, '_wp_attachment_image_alt', $title . ' (imagem ilustrativa)');
        $product->set_image_id($media);
        $product->update_meta_data('_nexo_image_revision', 'studio-v2');
        $product->save();
        if ($old_image) wp_delete_attachment($old_image, true);
    }
    if ($type === 'variable' && count($product->get_children()) === 0) {
        foreach (['Grafite', 'Areia'] as $color) {
            $variation = new WC_Product_Variation();
            $variation->set_parent_id($id);
            $variation->set_attributes(['cor' => $color]);
            $variation->set_regular_price($regular);
            $variation->set_sale_price($sale);
            $variation->set_manage_stock(true);
            $variation->set_stock_quantity(20);
            $variation->save();
        }
        WC_Product_Variable::sync($id);
    }
}
if (!wc_get_coupon_id_by_code('NEXO10')) {
    $coupon = new WC_Coupon();
    $coupon->set_code('NEXO10');
    $coupon->set_discount_type('percent');
    $coupon->set_amount(10);
    $coupon->set_description('Cupom de demonstração: 10% de desconto.');
    $coupon->save();
}
$zone_id = get_option('nexo_demo_shipping_zone');
if (!$zone_id) {
    $zone = new WC_Shipping_Zone();
    $zone->set_zone_name('Brasil: demonstração');
    $zone->add_location('BR', 'country');
    $zone->save();
    $zone->add_shipping_method('nexo_regional');
    update_option('nexo_demo_shipping_zone', $zone->get_id());
}
flush_rewrite_rules();
echo 'Nexo: catálogo, páginas, cupom e frete configurados.' . PHP_EOL;
