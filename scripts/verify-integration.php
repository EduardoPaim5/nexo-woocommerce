<?php
defined('ABSPATH') || exit;
function nexo_assert($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
    echo 'PASS: ' . $message . PHP_EOL;
}
WC()->shipping()->get_shipping_methods();
$product = wc_get_product(wc_get_product_id_by_sku('NEXO-M01'));
$cases = [['RS', 349.99, 24.90], ['RS', 350, 0], ['SP', 400, 0], ['BA', 400, 24.90]];
foreach ($cases as [$state, $subtotal, $expected]) {
    $method = new Nexo_Shipping_Method(99999);
    // Instance defaults are deliberately explicit for the test fixture.
    $method->instance_settings = ['title' => 'Entrega demonstrativa', 'threshold' => '350', 'base_cost' => '24.90', 'states' => ['RS','SP']];
    $method->calculate_shipping(['destination' => ['country' => 'BR','state' => $state], 'contents' => [['data' => $product, 'line_total' => $subtotal, 'quantity' => 1]]]);
    $rates = $method->rates;
    $rate = reset($rates);
    nexo_assert($rate && abs((float) $rate->get_cost() - $expected) < 0.001, "WooCommerce shipping: $state / $subtotal = $expected");
}
if (!WC()->cart) wc_load_cart();
$gateway = new Nexo_Demo_Gateway();
$stock = $product->get_stock_quantity();
$order = wc_create_order();
$order->add_product($product, 1);
$order->set_payment_method('nexo_demo');
$order->calculate_totals();
try {
    $gateway->process_payment($order->get_id());
    $gateway->process_payment($order->get_id());
    $after = wc_get_product($product->get_id())->get_stock_quantity();
    nexo_assert($after === $stock - 1, 'gateway retry reduces stock only once');
    nexo_assert(wc_get_order($order->get_id())->has_status('on-hold'), 'demo order is on-hold, not marked as paid');
} finally {
    wc_increase_stock_levels($order->get_id());
    $order->delete(true);
}
