<?php

declare(strict_types=1);

namespace FactoryGirl\Provider\Doctrine;

final class EntityDefinitionUnavailable extends \OutOfRangeException implements Exception
{
    public static function for(string $name): self
    {
        return new self(sprintf(
            'An entity definition for name "%s" is not available.',
            $name
        ));
    }
}
