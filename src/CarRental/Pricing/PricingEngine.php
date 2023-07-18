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
}
