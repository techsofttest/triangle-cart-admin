<?php

namespace App\Services\Import;

class PriceCalculator
{
    /**
     * Calculate the selling price from buying price, margin percentage and GST.
     * Match the Filament admin form calculation formula.
     *
     * Formula:
     *   price_after_tax = buying_price * (1 + tax / 100)
     *   selling_price = price_after_tax * (1 + margin / 100)
     *
     * @param float $buyingPrice
     * @param float $marginPercent
     * @param float $gstPercent
     * @return float Rounded to 2 decimal places
     */
    public function calculate(float $buyingPrice, float $marginPercent, float $gstPercent = 0.0): float
    {
        $priceAfterTax = $buyingPrice * (1 + $gstPercent / 100);
        $sellingPrice = $priceAfterTax * (1 + $marginPercent / 100);

        return round($sellingPrice, 2);
    }

    /**
     * Parse GST string from Excel (e.g. "10%", "10", "0.1") to a numeric percentage value.
     *
     * @param mixed $value
     * @return float
     */
    public function parseGst(mixed $value): float
    {
        if (is_null($value) || $value === '') {
            return 0.0;
        }

        $cleaned = str_replace('%', '', trim((string) $value));

        return (float) $cleaned;
    }
}
