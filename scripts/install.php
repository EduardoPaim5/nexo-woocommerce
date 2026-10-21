<?php
define('WP_INSTALLING', true);
require_once '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
add_filter('pre_wp_mail', '__return_true');
if (!is_blog_installed()) {
    // Password travels inside the container through a private local file, not CLI arguments.
    $env = parse_ini_file('/opt/nexo-env');
    wp_install('Nexo | Seu espaço, seu ritmo', 'eduardo', 'portfolio@example.com', false, '', $env['ADMIN_PASSWORD']);
}
update_option('home', 'http://localhost:8092');
update_option('siteurl', 'http://localhost:8092');
update_option('blog_public', 0);
