<?php

namespace CarriesCarsPhp\CarRental\Pricing;

use Brick\Money\Money;
use CarriesCarsPhp\CarRental\ValueObject\Duration;

class PricingEngine
{
    public function calculatePrice(Duration $duration, Money $pricePerMinute): Money
    {
        return $pricePerMinute->multipliedBy($duration->length);
    }

    public function calculateExceededMinutesPrice(Duration $duration, Money $pricePerMinute): Money
    {
        $extraDuration = max(0, $duration->length - 20);

        return $pricePerMinute->multipliedBy($extraDuration->length);
    }
}
