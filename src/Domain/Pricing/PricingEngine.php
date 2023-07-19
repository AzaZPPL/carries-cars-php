<?php

namespace CarriesCarsPhp\Domain\Pricing;

use Brick\Money\Money;
use CarriesCarsPhp\Domain\Model\Package;
use CarriesCarsPhp\Domain\ValueObject\Duration;
use CarriesCarsPhp\Domain\ValueObject\Mileage;

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

    public function calculatePriceWithPackage(
        Duration $actualDuration,
        Mileage $actualMileage,
        Money $pricePerMinute,
        Money $pricePerKilometer,
        Package $package
    ): Money {
        $totalPrice = $package->getPrice();

        $extraMinutesPrice = $this->calculateExceededMinutesPrice(
            duration: $actualDuration,
            pricePerMinute: $pricePerMinute,
            default: $package->getDuration()->length
        );

        $extraMileagePrice = $this->calculateExceededMileagePrice(
            mileage: $actualMileage,
            pricePerKilometer: $pricePerKilometer,
            default: $package->getMileage()->length
        );

        return $totalPrice->plus($extraMinutesPrice)->plus($extraMileagePrice);
    }
}
