<?php

declare(strict_types=1);

namespace App\TestingSystem\Domain\Model;

use Symfony\Component\Uid\UuidV4;
use Webmozart\Assert\Assert;

readonly class Id
{
    final private function __construct(
        private UuidV4 $value,
    ) {
    }

    public function __toString(): string
    {
        return $this->value->toRfc4122();
    }

    public static function fromString(string $value): static
    {
        Assert::stringNotEmpty($value);

        return new static(UuidV4::fromString($value));
    }

    public static function fromBinary(string $value): static
    {
        Assert::stringNotEmpty($value);

        return new static(UuidV4::fromBinary($value));
    }

    public static function next(): static
    {
        return new static(new UuidV4());
    }

    public function isEqual(self $anotherId): bool
    {
        return $anotherId instanceof static && $this->value->equals($anotherId->value);
    }

    public function toBinary(): string
    {
        return $this->value->toBinary();
    }

    public function toRfc4122(): string
    {
        return $this->value->toRfc4122();
    }

    public function toString(): string
    {
        return $this->toRfc4122();
    }
}
