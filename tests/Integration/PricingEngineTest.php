<?php

namespace CarriesCarsPhp\Tests\Integration;

use Brick\Money\Currency;
use Brick\Money\Money;
use CarriesCarsPhp\CarRental\Pricing\PricingEngine;
use CarriesCarsPhp\CarRental\ValueObject\Duration;
use PHPUnit\Framework\TestCase;

class PricingEngineTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideDifferentDurationsAndPrices
     */
    public function calculate_price_charged_per_minute(Money $pricePerMinute, Duration $duration, Money $expectedPrice): void
    {
        $pricingEngine = new PricingEngine();
        $actual = $pricingEngine->calculatePrice(duration: $duration, pricePerMinute: $pricePerMinute);
        $this->assertEquals($expectedPrice, $actual);
    }

    public static function provideDifferentDurationsAndPrices(): iterable
    {
        yield 'Duration of 1 minute with price of 0.30' => [Money::of(0.30, Currency::of('EUR')), Duration::ofMinutes(1), Money::of(0.30, Currency::of('EUR'))];
        yield 'Duration of 3 minutes with price of 0.30' => [Money::of(0.30, Currency::of('EUR')), Duration::ofMinutes(3), Money::of(0.90, Currency::of('EUR'))];
        yield 'Duration of 12 minutes with price of 0.23' => [Money::of(0.23, Currency::of('EUR')), Duration::ofMinutes(12), Money::of(2.76, Currency::of('EUR'))];
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
    ) {
        $pricingEngine = new PricingEngine();
        $reservationMoney = $pricingEngine->calculateExceededMinutesPrice(duration: $reservationDuration, pricePerMinute: $reservationPricePerMinute);

        $actual = $pricingEngine->calculatePrice(duration: $duration, pricePerMinute: $pricePerMinute);

        $this->assertEquals($expectedPrice, $actual->plus($reservationMoney));
    }

    public static function provideDifferentDurationsAndPricesWithReservationTimes(): iterable
    {
        yield 'Duration of 1 minute with price of 0.30 and reservation time of 1 minute with price of 0.09' => [
            Money::of(0.30, Currency::of('EUR')),
            Duration::ofMinutes(1),
            Money::of(0.09, Currency::of('EUR')),
            Duration::ofMinutes(1),
            Money::of(0.39, Currency::of('EUR'))
        ];
        yield 'Duration of 3 minutes with price of 0.30 and reservation time of 10 minute with price of 0.09' => [
            Money::of(0.30, Currency::of('EUR')),
            Duration::ofMinutes(3),
            Money::of(0.09, Currency::of('EUR')),
            Duration::ofMinutes(10),
            Money::of(1.80, Currency::of('EUR'))
        ];
        yield 'Duration of 12 minutes with price of 0.23 and reservation time of 20 minute with price of 0.05' => [
            Money::of(0.23, Currency::of('EUR')),
            Duration::ofMinutes(12),
            Money::of(0.05, Currency::of('EUR')),
            Duration::ofMinutes(20),
            Money::of(3.76, Currency::of('EUR'))
        ];
    }

}
