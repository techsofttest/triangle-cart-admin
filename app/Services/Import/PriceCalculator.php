<?php

namespace App\Services\Import;

class PriceCalculator
{
    /**
     * Calculate the selling price from buying price, GST percentage, and margin percentage.
     *
     * Formula:
     *   price_after_gst = buying_price × (1 + gst / 100)
     *   selling_price   = price_after_gst × (1 + margin / 100)
     *
     * @param float $buyingPrice
     * @param float $gstPercent
     * @param float $marginPercent
     * @return float Rounded to 2 decimal places
     */
    public function calculate(float $buyingPrice, float $gstPercent, float $marginPercent): float
    {
        $priceAfterGst = $buyingPrice * (1 + $gstPercent / 100);
        $sellingPrice = $priceAfterGst * (1 + $marginPercent / 100);

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
