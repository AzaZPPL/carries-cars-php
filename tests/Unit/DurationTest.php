<?php

namespace CarriesCarsPhp\Tests\Unit;

use CarriesCarsPhp\Domain\ValueObject\Duration;
use PHPUnit\Framework\TestCase;

class DurationTest extends TestCase
{
    /** @test */
    public function duration_should_be_at_least_one_minute(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Duration::ofMinutes(0);
    }

    /**
     * @test
     * @dataProvider provideDifferentValidDurations
     */
    public function convert_from_and_to_text(int $duration): void
    {
        $duration = Duration::ofMinutes($duration);
        $this->assertEquals($duration, Duration::fromString($duration->toString()));
    }

    public static function provideDifferentValidDurations(): iterable
    {
        yield [1];
        yield [2];
        yield [100];
    }

    public function duration_from_hours_converts_to_minutes(): void
    {
        $duration = Duration::ofHours(1);
        $this->assertEquals(60, $duration->length);
    }

    public function duration_from_days_converts_to_minutes(): void
    {
        $duration = Duration::ofDays(1);
        $this->assertEquals(1440, $duration->length);
    }
}
