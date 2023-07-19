<?php

namespace CarriesCarsPhp\Tests\Integration;

use Brick\Money\Currency;
use Brick\Money\Money;
use CarriesCarsPhp\Domain\Model\Package;
use CarriesCarsPhp\Domain\Pricing\PricingEngine;
use CarriesCarsPhp\Domain\ValueObject\Duration;
use CarriesCarsPhp\Domain\ValueObject\Mileage;
use PHPUnit\Framework\TestCase;

class PricingEngineTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideDifferentDurationsAndPrices
     */
    public function calculate_price_charged_per_minute(
        Money $pricePerMinute,
        Duration $duration,
        Money $expectedPrice
    ): void {
        $pricingEngine = new PricingEngine();
        $actual = $pricingEngine->calculatePrice(duration: $duration, pricePerMinute: $pricePerMinute);
        $this->assertEquals($expectedPrice, $actual);
    }

    public static function provideDifferentDurationsAndPrices(): iterable
    {
        yield 'Duration of 1 minute with price of 0.30' => [
            Money::of(0.30, Currency::of('EUR')),
            Duration::ofMinutes(1),
            Money::of(0.30, Currency::of('EUR'))
        ];
        yield 'Duration of 3 minutes with price of 0.30' => [
            Money::of(0.30, Currency::of('EUR')),
            Duration::ofMinutes(3),
            Money::of(0.90, Currency::of('EUR'))
        ];
        yield 'Duration of 12 minutes with price of 0.23' => [
            Money::of(0.23, Currency::of('EUR')),
            Duration::ofMinutes(12),
            Money::of(2.76, Currency::of('EUR'))
        ];
    }

    /**
     * @test
     * @dataProvider provideDifferentDurationsAndPricesWithReservationTimes
     */
    public function calculate_price_charged_per_minute_with_extended_reservation_minutes(
        Money $pricePerMinute,
        Duration $duration,
        Money $reservationPricePerMinute,
        Duration $reservationDuration,
        Money $expectedPrice
    ): void {
        $pricingEngine = new PricingEngine();
        $reservationMoney = $pricingEngine->calculateExceededMinutesPrice(
            duration: $reservationDuration,
            pricePerMinute: $reservationPricePerMinute
        );

        $actual = $pricingEngine->calculatePrice(duration: $duration, pricePerMinute: $pricePerMinute);

        $this->assertEquals($expectedPrice, $actual->plus($reservationMoney));
    }

    public static function provideDifferentDurationsAndPricesWithReservationTimes(): iterable
    {
        yield 'Duration of 1 minute with price of 0.30 and reservation time of 20 minute with price of 0.09' => [
            Money::of(0.30, Currency::of('EUR')),
            Duration::ofMinutes(1),
            Money::of(0.09, Currency::of('EUR')),
            Duration::ofMinutes(1),
            Money::of(0.30, Currency::of('EUR'))
        ];
        yield 'Duration of 3 minutes with price of 0.30 and reservation time of 30 minute with price of 0.09' => [
            Money::of(0.30, Currency::of('EUR')),
            Duration::ofMinutes(3),
            Money::of(0.09, Currency::of('EUR')),
            Duration::ofMinutes(30),
            Money::of(1.80, Currency::of('EUR'))
        ];
        yield 'Duration of 12 minutes with price of 0.23 and reservation time of 25 minute with price of 0.05' => [
            Money::of(0.23, Currency::of('EUR')),
            Duration::ofMinutes(12),
            Money::of(0.05, Currency::of('EUR')),
            Duration::ofMinutes(40),
            Money::of(3.76, Currency::of('EUR'))
        ];
    }

    /**
     * @test
     * @dataProvider provideDifferentDurationsAndPricesWithExceededMileage
     */
    public function calculate_price_charged_per_minute_with_exceeded_mileages(
        Money $pricePerMinute,
        Duration $duration,
        Money $exceededMileagePrice,
        Mileage $mileage,
        Money $expectedPrice
    ): void {
        $pricingEngine = new PricingEngine();
        $exceededMileagePrice = $pricingEngine->calculateExceededMileagePrice($mileage, $exceededMileagePrice);

        $actual = $pricingEngine->calculatePrice(duration: $duration, pricePerMinute: $pricePerMinute);

        $this->assertEquals($expectedPrice, $actual->plus($exceededMileagePrice));
    }

    public static function provideDifferentDurationsAndPricesWithExceededMileage(): iterable
    {
        yield 'Duration of 1 minute with price of 0.30 and driven mileage of 240km with exceeded mileage cost of 0.19' => [
            Money::of(0.30, Currency::of('EUR')),
            Duration::ofMinutes(1),
            Money::of(0.19, Currency::of('EUR')),
            Mileage::ofKilometers(240),
            Money::of(0.30, Currency::of('EUR'))
        ];
        yield 'Duration of 3 minutes with price of 0.30 and driven mileage of 300km with exceeded mileage cost of 0.25' => [
            Money::of(0.30, Currency::of('EUR')),
            Duration::ofMinutes(3),
            Money::of(0.25, Currency::of('EUR')),
            Mileage::ofKilometers(300),
            Money::of(13.40, Currency::of('EUR'))
        ];
        yield 'Duration of 12 minutes with price of 0.23 and driven mileage of 500km with exceeded mileage cost of 0.01' => [
            Money::of(0.23, Currency::of('EUR')),
            Duration::ofMinutes(12),
            Money::of(0.01, Currency::of('EUR')),
            Mileage::ofKilometers(500),
            Money::of(5.26, Currency::of('EUR'))
        ];
    }

    /**
     * @test
     * @dataProvider provideDifferentPackagesWithVariousExceededOptions
     * @dataProvider provideDifferentPackagesWithVariousExceededOptionsAndAdditionalPackages
     */
    public function calculate_price_per_package(
        Package $package,
        Duration $actualDuration,
        Mileage $actualMileage,
        Money $pricePerMinute,
        Money $pricePerKilometer,
        Money $expectedPrice
    ): void {
        $pricingEngine = new PricingEngine();
        $actual = $pricingEngine->calculatePriceWithPackage(
            $actualDuration,
            $actualMileage,
            $pricePerMinute,
            $pricePerKilometer,
            $package
        );
        $this->assertEquals($expectedPrice, $actual);
    }

    public static function provideDifferentPackagesWithVariousExceededOptions(): iterable
    {
        yield 'Three hours package with no exceeded minutes or kilometers' => [
            Package::create(Duration::ofHours(3), Mileage::ofKilometers(75), Money::of(19, Currency::of('EUR'))),
            Duration::ofMinutes(120),
            Mileage::ofKilometers(60),
            Money::of(0.19, Currency::of('EUR')),
            Money::of(0.30, Currency::of('EUR')),
            Money::of(19, Currency::of('EUR'))
        ];
        yield 'Six hours package with 1 exceeded hour and 25 exceeded kilometers' => [
            Package::create(Duration::ofHours(6), Mileage::ofKilometers(125), Money::of(39, Currency::of('EUR'))),
            Duration::ofHours(7),
            Mileage::ofKilometers(150),
            Money::of(0.19, Currency::of('EUR')),
            Money::of(0.30, Currency::of('EUR')),
            Money::of(57.90, Currency::of('EUR'))
        ];
        yield 'One day package with 2 exceeded hours and 100 exceeded kilometers' => [
            Package::create(Duration::ofDays(1), Mileage::ofKilometers(200), Money::of(59, Currency::of('EUR'))),
            Duration::ofHours(26),
            Mileage::ofKilometers(300),
            Money::of(0.19, Currency::of('EUR')),
            Money::of(0.30, Currency::of('EUR')),
            Money::of(111.80, Currency::of('EUR'))
        ];
        yield 'Three day package with no exceeded hours and 26 exceeded kilometers with a price of 0.05' => [
            Package::create(Duration::ofDays(3), Mileage::ofKilometers(400), Money::of(95, Currency::of('EUR'))),
            Duration::ofHours(15),
            Mileage::ofKilometers(426),
            Money::of(0.19, Currency::of('EUR')),
            Money::of(0.05, Currency::of('EUR')),
            Money::of(96.30, Currency::of('EUR'))
        ];
    }

    public static function provideDifferentPackagesWithVariousExceededOptionsAndAdditionalPackages(): iterable
    {
        yield 'Three hours package with no exceeded minutes or kilometers with no additional packages' => [
            Package::create(Duration::ofHours(3), Mileage::ofKilometers(75), Money::of(19, Currency::of('EUR'))),
            Duration::ofMinutes(120),
            Mileage::ofKilometers(60),
            Money::of(0.19, Currency::of('EUR')),
            Money::of(0.30, Currency::of('EUR')),
            Money::of(19, Currency::of('EUR'))
        ];
        yield 'Three hours package with no exceeded minutes or kilometers with two additional packages' => [
            Package::create(Duration::ofHours(3), Mileage::ofKilometers(75), Money::of(19, Currency::of('EUR')), [
                Package::create(Duration::ofHours(1), Mileage::ofKilometers(25), Money::of(5, Currency::of('EUR'))),
                Package::create(Duration::ofHours(1), Mileage::ofKilometers(25), Money::of(5, Currency::of('EUR')))
            ]),
            Duration::ofMinutes(120),
            Mileage::ofKilometers(60),
            Money::of(0.19, Currency::of('EUR')),
            Money::of(0.30, Currency::of('EUR')),
            Money::of(29, Currency::of('EUR'))
        ];
        yield 'Three hours package with no exceeded minutes or kilometers with two additional different packages' => [
            Package::create(Duration::ofHours(3), Mileage::ofKilometers(75), Money::of(19, Currency::of('EUR')), [
                Package::create(Duration::ofHours(4), Mileage::ofKilometers(30), Money::of(25, Currency::of('EUR'))),
                Package::create(Duration::ofHours(2), Mileage::ofKilometers(500), Money::of(9, Currency::of('EUR')))
            ]),
            Duration::ofMinutes(120),
            Mileage::ofKilometers(60),
            Money::of(0.19, Currency::of('EUR')),
            Money::of(0.30, Currency::of('EUR')),
            Money::of(53, Currency::of('EUR'))
        ];
    }
}
