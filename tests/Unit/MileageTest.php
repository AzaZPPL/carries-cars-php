<?php

namespace CarriesCarsPhp\Tests\Unit;

use CarriesCarsPhp\Domain\ValueObject\Duration;
use CarriesCarsPhp\Domain\ValueObject\Mileage;
use PHPUnit\Framework\TestCase;

class MileageTest extends TestCase
{
    /** @test */
    public function mileage_should_be_at_least_one_kilometer(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Mileage::ofKilometers(0);
    }

    /**
     * @test
     * @dataProvider provideDifferentValidDurations
     */
    public function convert_from_and_to_text(int $kilometers): void
    {
        $mileage = Mileage::ofKilometers($kilometers);
        $this->assertEquals($mileage->length, $kilometers);
    }

    public static function provideDifferentValidDurations(): iterable
    {
        yield [1];
        yield [2];
        yield [100];
    }

}
