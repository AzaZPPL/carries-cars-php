<?php

namespace CarriesCarsPhp\Domain\ValueObject;

final class Mileage
{
    public function __construct(public readonly int $length)
    {
        if ($this->length < 1) {
            throw new \InvalidArgumentException('Sorry, Mileage should be at least one kilometer.');
        }
    }

    public static function ofKilometers(int $length): self
    {
        return new self($length);
    }
}