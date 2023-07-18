<?php

namespace CarriesCarsPhp\CarRental\ValueObject;

final class Duration
{
    public function __construct(public readonly int $length)
    {
        if($this->length < 1) {
            throw new \InvalidArgumentException('Sorry, Duration should be at least one minute.');
        }
    }
    public static function ofMinutes(int $length): self
    {
        return new self($length);
    }

    public static function fromString($string): self
    {
        return new self((int) $string);
    }

    public function toString(): string
    {
        return (string) $this->length;
    }

    public function extend(Duration|int $duration): self
    {
        if ($duration instanceof Duration) {
            $duration = $duration->length;
        }

        return new self($this->length + $duration);
    }

}
