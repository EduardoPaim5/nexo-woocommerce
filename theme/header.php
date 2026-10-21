<?php defined('ABSPATH') || exit; ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width,initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?>
<a class="skip" href="#main">Pular para o conteúdo</a>
<div class="demo-strip">Loja demonstrativa de portfólio. Nenhuma compra será cobrada.<a href="<?php echo esc_url(home_url('/sobre-o-projeto/')); ?>">Conheça o projeto</a></div>
<header class="site-header"><div class="wrap header-inner">
<?php nexo_brand(); ?>
<nav id="main-nav" class="main-nav" aria-label="Navegação principal">
<a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Todos os produtos</a>
<a href="<?php echo esc_url(home_url('/categoria-produto/audio/')); ?>">Áudio</a>
<a href="<?php echo esc_url(home_url('/categoria-produto/mesa/')); ?>">Sua mesa</a>
</nav>
<div class="header-tools">
<form class="search-form" role="search" action="<?php echo esc_url(home_url('/')); ?>" method="get"><label class="screen-reader-text" for="product-search">Buscar produtos</label><input id="product-search" type="search" name="s" placeholder="O que você procura?" value="<?php echo esc_attr(get_search_query()); ?>"><input type="hidden" name="post_type" value="product"><button class="icon-button" type="submit" aria-label="Buscar"><?php echo nexo_icon('search'); ?></button></form>
<a class="icon-button cart-link" href="<?php echo esc_url(wc_get_cart_url()); ?>" aria-label="Ver carrinho"><?php echo nexo_icon('bag'); ?><span class="cart-count"><?php echo absint(WC()->cart ? WC()->cart->get_cart_contents_count() : 0); ?></span></a>
<button class="icon-button menu-toggle" type="button" aria-label="Abrir menu" aria-expanded="false" aria-controls="main-nav"><?php echo nexo_icon('menu'); ?></button>
</div></div></header>
