<?php

namespace CarriesCarsPhp\Domain\Model;

use Brick\Money\Money;
use CarriesCarsPhp\Domain\ValueObject\Duration;
use CarriesCarsPhp\Domain\ValueObject\Mileage;

class Package
{
    /**
     * @param Duration $duration
     * @param Mileage $mileage
     * @param Money $price
     * @param array<Package> $packages
     */
    private function __construct(
        private readonly Duration $duration,
        private readonly Mileage $mileage,
        private readonly Money $price,
        private readonly array $packages = [],
    ) {
    }

    public static function create(Duration $duration, Mileage $mileage, Money $price, array $packages = []): self
    {
        return new self($duration, $mileage, $price, $packages);
    }

    public function getDuration(): Duration
    {
        return $this->duration;
    }

    public function getMileage(): Mileage
    {
        return $this->mileage;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    /*
     * @return array<Package>
     */
    public function getPackages(): array
    {
        return $this->packages;
    }
}
