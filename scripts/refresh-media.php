<?php
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
foreach (['NEXO-A01' => 'headphones.webp', 'NEXO-M01' => 'keyboard.webp', 'NEXO-A02' => 'earbuds.webp', 'NEXO-M02' => 'mouse.webp', 'NEXO-M03' => 'deskmat.webp', 'NEXO-C01' => 'hub.webp', 'NEXO-A03' => 'speaker.webp', 'NEXO-M04' => 'stand.webp'] as $sku => $image) {
    $product = wc_get_product(wc_get_product_id_by_sku($sku));
    $old = $product->get_image_id();
    $temp = wp_tempnam($image);
    copy('/opt/nexo-assets/' . $image, $temp);
    $attachment = media_handle_sideload(['name' => $image, 'tmp_name' => $temp], $product->get_id(), $product->get_name());
    if (is_wp_error($attachment)) throw new RuntimeException($attachment->get_error_message());
    update_post_meta($attachment, '_wp_attachment_image_alt', $product->get_name() . ' (imagem ilustrativa)');
    $product->set_image_id($attachment);
    $product->update_meta_data('_nexo_image_revision', 'studio-v2');
    $product->save();
    if ($old) wp_delete_attachment($old, true);
}
echo "Fotografias do catálogo atualizadas.\n";
