<?php

namespace CarriesCarsPhp\Tests\Unit;

use Brick\Money\Currency;
use Brick\Money\Money;
use CarriesCarsPhp\Domain\Model\Package;
use CarriesCarsPhp\Domain\ValueObject\Duration;
use CarriesCarsPhp\Domain\ValueObject\Mileage;
use PHPUnit\Framework\TestCase;

class PackageTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideDifferentValidCreationParameters
     */
    public function can_create_package(Duration $duration, Mileage $mileage, Money $price): void
    {
        $package = new Package($duration, $mileage, $price);
        $this->assertInstanceOf(Package::class, $package);

        $package = Package::create($duration, $mileage, $price);
        $this->assertInstanceOf(Package::class, $package);
    }

    /**
     * @test
     * @dataProvider provideDifferentValidCreationParameters
     */
    public function can_get_duration(Duration $duration, Mileage $mileage, Money $price): void
    {
        $package = new Package($duration, $mileage, $price);
        $this->assertInstanceOf(Duration::class, $package->getDuration());
        $this->assertEquals($duration, $package->getDuration());
    }

    /**
     * @test
     * @dataProvider provideDifferentValidCreationParameters
     */
    public function can_get_mileage(Duration $duration, Mileage $mileage, Money $price): void
    {
        $package = new Package($duration, $mileage, $price);
        $this->assertInstanceOf(Mileage::class, $package->getMileage());
        $this->assertEquals($mileage, $package->getMileage());
    }

    /**
     * @test
     * @dataProvider provideDifferentValidCreationParameters
     */
    public function can_get_price(Duration $duration, Mileage $mileage, Money $price): void
    {
        $package = new Package($duration, $mileage, $price);
        $this->assertInstanceOf(Money::class, $package->getPrice());
        $this->assertEquals($price, $package->getPrice());
    }

    public static function provideDifferentValidCreationParameters(): iterable
    {
        yield 'Three hours package' => [Duration::ofHours(3), Mileage::ofKilometers(75), Money::of(19, Currency::of('EUR'))];
        yield 'Six hours package' => [Duration::ofHours(6), Mileage::ofKilometers(125), Money::of(39, Currency::of('EUR'))];
        yield 'One day package' => [Duration::ofDays(1), Mileage::ofKilometers(200), Money::of(59, Currency::of('EUR'))];
        yield 'Three days package' => [Duration::ofDays(3), Mileage::ofKilometers(400), Money::of(95, Currency::of('EUR'))];
    }
}
