<?php

namespace CarriesCarsPhp\Domain\Pricing;

use Brick\Money\Money;
use CarriesCarsPhp\Domain\ValueObject\Mileage;
use CarriesCarsPhp\Domain\ValueObject\Duration;

class PricingEngine
{
    public function calculatePrice(Duration $duration, Money $pricePerMinute): Money
    {
        return $pricePerMinute->multipliedBy($duration->length);
    }

    public function calculateExceededMinutesPrice(Duration $duration, Money $pricePerMinute, int $default = 20): Money
    {
        $extraDuration = max(0, $duration->length - $default);

        return $pricePerMinute->multipliedBy($extraDuration);
    }

    public function calculateExceededMileagePrice(Mileage $mileage, Money $pricePerKilometer, int $default = 250): Money
    {
        $extraMileage = max(0, $mileage->length - $default);

        return $pricePerKilometer->multipliedBy($extraMileage);
    }
}
