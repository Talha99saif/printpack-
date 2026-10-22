<?php
declare(strict_types=1);
namespace App\Services;
final class PricingService {
    /** Pricing formula: (base + customization) * quantity + estimated delivery. */
    public static function quote(float $basePrice, int $quantity, float $customization = 0, float $distanceKm = 0): array {
        $quantity = max(1, min($quantity, 100000));
        $distanceKm = max(0, $distanceKm);
        $subtotal = ($basePrice + max(0, $customization)) * $quantity;
        $delivery = 180 + ($distanceKm * 28); // Example PKR rate; configure for the actual courier contract.
        return ['quantity' => $quantity, 'subtotal' => round($subtotal, 2), 'delivery' => round($delivery, 2), 'total' => round($subtotal + $delivery, 2)];
    }
}
