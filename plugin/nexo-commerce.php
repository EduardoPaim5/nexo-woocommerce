<?php
/**
 * Plugin Name: Nexo Commerce
 * Description: Frete por região e valor mínimo, gateway de demonstração e proteção contra envio de e-mails da loja fictícia.
 * Version: 1.0.0
 * Author: Eduardo dos Santos Paim
 * Requires PHP: 8.1
 * Requires Plugins: woocommerce
 * Text Domain: nexo-commerce
 * License: GPL-2.0-or-later
 */
defined('ABSPATH') || exit;
require_once __DIR__ . '/src/ShippingPolicy.php';

// This plugin is intentionally for a portfolio sandbox, not a live merchant.
add_filter('pre_wp_mail', '__return_true');
add_filter('woocommerce_enable_order_notes_field', '__return_false');
add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, false);
    }
});
add_action('woocommerce_shipping_init', function () {
    if (class_exists('Nexo_Shipping_Method')) {
        return;
    }
    class Nexo_Shipping_Method extends WC_Shipping_Method
    {
        public function __construct($instance_id = 0)
        {
            $this->id = 'nexo_regional';
            $this->instance_id = absint($instance_id);
            $this->method_title = 'Nexo: frete regional';
            $this->method_description = 'Frete fixo com gratuidade por estado e subtotal após descontos.';
            $this->supports = ['shipping-zones', 'instance-settings', 'instance-settings-modal'];
            $this->instance_form_fields = [
                'title' => ['title' => 'Nome exibido', 'type' => 'text', 'default' => 'Entrega demonstrativa'],
                'threshold' => ['title' => 'Mínimo para frete grátis (R$)', 'type' => 'price', 'default' => '350', 'description' => 'Subtotal de produtos após cupons, sem impostos ou frete.'],
                'base_cost' => ['title' => 'Frete padrão (R$)', 'type' => 'price', 'default' => '24.90'],
                'states' => ['title' => 'Estados com gratuidade', 'type' => 'multiselect', 'options' => WC()->countries->get_states('BR'), 'default' => ['RS', 'SC', 'PR', 'SP', 'RJ', 'MG', 'ES']],
            ];
            $this->init_settings();
            $this->enabled = 'yes';
            $this->title = $this->get_option('title', 'Entrega demonstrativa');
            add_action('woocommerce_update_options_shipping_' . $this->id, [$this, 'process_admin_options']);
        }
        public function validate_price_field($key, $value)
        {
            $clean = wc_format_decimal($value);
            if (!is_numeric($clean) || (float) $clean < 0) {
                throw new Exception('Informe um valor monetário igual ou maior que zero.');
            }
            return $clean;
        }
        public function calculate_shipping($package = [])
        {
            $destination = $package['destination'] ?? [];
            $subtotal = 0;
            foreach ($package['contents'] ?? [] as $item) {
                if (isset($item['data']) && $item['data']->needs_shipping()) {
                    $subtotal += (int) round((float) ($item['line_total'] ?? 0) * 100);
                }
            }
            $valid_states = array_keys(WC()->countries->get_states('BR'));
            $states = array_values(array_intersect((array) $this->get_option('states', []), $valid_states));
            $cost = \Nexo\Commerce\ShippingPolicy::cost(
                (string) ($destination['country'] ?? ''),
                (string) ($destination['state'] ?? ''),
                $subtotal,
                max(0, (int) round((float) $this->get_option('threshold', 350) * 100)),
                max(0, (int) round((float) $this->get_option('base_cost', 24.90) * 100)),
                $states
            );
            if ($cost === null) {
                return;
            }
            $this->add_rate(['id' => $this->get_rate_id(), 'label' => $cost === 0 ? 'Entrega demonstrativa grátis' : $this->title, 'cost' => $cost / 100, 'package' => $package]);
        }
    }
});
add_filter('woocommerce_shipping_methods', function ($methods) {
    $methods['nexo_regional'] = 'Nexo_Shipping_Method';
    return $methods;
});

add_action('plugins_loaded', function () {
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }
    class Nexo_Demo_Gateway extends WC_Payment_Gateway
    {
        public function __construct()
        {
            $this->id = 'nexo_demo';
            $this->method_title = 'Nexo: pedido de demonstração';
            $this->method_description = 'Simula um pedido sem credenciais financeiras nem cobrança.';
            $this->title = 'Simular pedido (sem cobrança)';
            $this->description = 'Use somente dados fictícios. Não informe cartão, CPF ou endereço real. Nenhum produto será enviado.';
            $this->enabled = 'yes';
            $this->has_fields = false;
            $this->supports = ['products'];
        }
        public function process_payment($order_id)
        {
            $order = wc_get_order($order_id);
            if (!$order) {
                wc_add_notice('Não foi possível localizar o pedido. Atualize a página e tente novamente.', 'error');
                return ['result' => 'failure'];
            }
            // WooCommerce handles stock reduction idempotently for the order.
            if (!$order->has_status(['on-hold', 'processing', 'completed'])) {
                $order->update_status('on-hold', 'Pedido fictício de portfólio. Nenhuma cobrança ou entrega.');
                wc_reduce_stock_levels($order_id);
            }
            WC()->cart->empty_cart();
            return ['result' => 'success', 'redirect' => $this->get_return_url($order)];
        }
    }
});
add_filter('woocommerce_payment_gateways', function ($gateways) {
    $gateways[] = 'Nexo_Demo_Gateway';
    return $gateways;
});
add_filter('woocommerce_available_payment_gateways', function ($gateways) {
    return isset($gateways['nexo_demo']) ? ['nexo_demo' => $gateways['nexo_demo']] : [];
});
add_action('woocommerce_before_checkout_form', function () {
    echo '<p class="nexo-demo-notice">Esta é uma demonstração. Preencha com dados fictícios, como nome “Pessoa Teste” e e-mail “teste@example.com”.</p>';
}, 5);
add_filter('woocommerce_checkout_fields', function ($fields) {
    if (isset($fields['billing']['billing_phone'])) {
        $fields['billing']['billing_phone']['required'] = false;
    }
    unset($fields['billing']['billing_company'], $fields['shipping']['shipping_company']);
    return $fields;
});
add_filter('woocommerce_thankyou_order_received_text', function () {
    return 'Seu pedido de demonstração foi registrado. Nenhum valor foi cobrado e não haverá entrega.';
});
