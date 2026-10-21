<?php defined('ABSPATH') || exit; get_header(); ?>
<main id="main" class="wrap content-area"><?php if (have_posts()): while (have_posts()): the_post(); ?><article class="<?php echo (is_cart() || is_checkout() || is_account_page()) ? 'commerce-page' : 'about-copy'; ?>"><h1><?php the_title(); ?></h1><?php the_content(); ?></article><?php endwhile; else: ?><h1>Não encontramos essa página.</h1><p>Você pode continuar explorando o catálogo.</p><a class="button" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Ver produtos</a><?php endif; ?></main>
<?php get_footer(); ?>
