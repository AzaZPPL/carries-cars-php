<?php

namespace CarriesCarsPhp\Domain\ValueObject;

final class Duration
{
    public function __construct(public readonly int $length)
    {
        if ($this->length < 1) {
            throw new \InvalidArgumentException('Sorry, Duration should be at least one minute.');
        }
    }

    public static function ofMinutes(int $length): self
    {
        return new self($length);
    }

    public static function fromString($string): self
    {
        return new self((int)$string);
    }

    public function toString(): string
    {
        return (string)$this->length;
    }
}
