<?php
declare(strict_types=1);
namespace Nexo\Commerce;

/** Pure policy: discounts already deducted; monetary calculations use cents. */
final class ShippingPolicy
{
    public static function cost(string $country, string $state, int $subtotal, int $threshold, int $base, array $eligibleStates): ?int
    {
        if ($country !== 'BR') {
            return null;
        }
        if ($subtotal < 0 || $threshold < 0 || $base < 0) {
            throw new \InvalidArgumentException('Valores monetários não podem ser negativos.');
        }
        return in_array($state, $eligibleStates, true) && $subtotal >= $threshold ? 0 : $base;
    }
}
