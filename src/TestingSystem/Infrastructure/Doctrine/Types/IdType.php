<?php

declare(strict_types=1);

namespace App\TestingSystem\Infrastructure\Doctrine\Types;

use App\TestingSystem\Domain\Model\Id;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Webmozart\Assert\Assert;

/**
 * @psalm-suppress all
 */
final class IdType extends Type
{
    public const NAME = 'common__id';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        Assert::isInstanceOf($value, Id::class);

        /* @var \App\TestingSystem\Domain\Model\Id $value */

        return $platform->hasNativeGuidType()
            ? $value->toRfc4122()
            : $value->toBinary()
        ;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Id
    {
        if ($value === null) {
            return null;
        }

        if (!is_string($value)) {
            throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', 'string', Id::class]);
        }

        try {
            return Id::fromString($value);
        } catch (InvalidArgumentException $e) {
            throw ConversionException::conversionFailed($value, $this->getName(), $e);
        }
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        if ($platform->hasNativeGuidType()) {
            return $platform->getGuidTypeDeclarationSQL($column);
        }

        return $platform->getBinaryTypeDeclarationSQL([
            'length' => '16',
            'fixed'  => true,
        ]);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
