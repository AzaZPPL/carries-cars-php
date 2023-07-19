<?php

namespace CarriesCarsPhp\Domain\Model;

use Brick\Money\Money;
use CarriesCarsPhp\Domain\ValueObject\Duration;
use CarriesCarsPhp\Domain\ValueObject\Mileage;

class Package
{
    public function __construct(private readonly Duration $duration, private readonly Mileage $mileage, private readonly Money $price)
    {
    }

    public static function create(Duration $duration, Mileage $mileage, Money $price): self
    {
        return new self($duration, $mileage, $price);
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
}
