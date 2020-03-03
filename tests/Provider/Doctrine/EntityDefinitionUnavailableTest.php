<?php

declare(strict_types=1);

namespace FactoryGirl\Tests\Provider\Doctrine;

use FactoryGirl\Provider\Doctrine\EntityDefinitionUnavailable;
use PHPUnit\Framework;

/**
 * @covers \FactoryGirl\Provider\Doctrine\EntityDefinitionUnavailable
 */
final class EntityDefinitionUnavailableTest extends Framework\TestCase
{
    public function testForReturnsException(): void
    {
        $name = 'foo';

        $exception = EntityDefinitionUnavailable::for($name);

        self::assertInstanceOf(\OutOfRangeException::class, $exception);

        $message = sprintf(
            'An entity definition for name "%s" is not available.',
            $name
        );

        self::assertSame($message, $exception->getMessage());
    }
}
