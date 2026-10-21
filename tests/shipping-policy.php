<?php
declare(strict_types=1);
require __DIR__ . '/../plugin/src/ShippingPolicy.php';
use Nexo\Commerce\ShippingPolicy;
$cases = [
    ['RS below threshold', 'BR', 'RS', 34999, 2490],
    ['RS exact threshold', 'BR', 'RS', 35000, 0],
    ['SP above threshold', 'BR', 'SP', 50000, 0],
    ['BA is not eligible', 'BR', 'BA', 50000, 2490],
    ['foreign destination', 'US', 'CA', 50000, null],
    ['unknown state', 'BR', '', 50000, 2490],
    ['empty cart', 'BR', 'RS', 0, 2490],
    ['coupon reduces subtotal', 'BR', 'RS', 34000, 2490],
];
foreach ($cases as [$name, $country, $state, $subtotal, $expected]) {
    $actual = ShippingPolicy::cost($country, $state, $subtotal, 35000, 2490, ['RS', 'SP']);
    if ($actual !== $expected) {
        throw new RuntimeException($name . ': expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
    echo "PASS: $name\n";
}
try {
    ShippingPolicy::cost('BR', 'RS', -1, 35000, 2490, ['RS']);
    throw new RuntimeException('Negative monetary value accepted');
} catch (InvalidArgumentException $e) {
    echo "PASS: negative amount rejected\n";
}
