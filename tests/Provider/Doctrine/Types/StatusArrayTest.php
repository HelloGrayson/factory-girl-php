<?php

namespace FactoryGirl\Tests\Provider\Doctrine\Types;

use FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestCase;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\ConversionException;

Type::addType('statusarray', 'FactoryGirl\Provider\Doctrine\DBAL\Types\StatusArrayType');

class StatusArrayTest extends TestCase
{
    protected $platform;
    
    /**
     * @var \FactoryGirl\Provider\Doctrine\DBAL\Types\StatusArrayType
     */
    protected $type;

    public function setUp()
    {
        $this->platform = $this->getMockForAbstractClass('\Doctrine\DBAL\Platforms\AbstractPlatform');
        $this->type = Type::getType('statusarray');
    }

    /**
     * @test
     */
    public function getNameShouldReturnExpectedName()
    {
        $this->assertEquals('statusarray', $this->type->getName());
    }
    
    
    /**
     * @test
     */
    public function nullShouldAlwaysConvertToNull()
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform));
    }
    
    
    /**
     * @test
     */
    public function nonArrayOrNotNullShouldFailDatabaseConversion()
    {
        $this->expectException(ConversionException::class);

        $value = 'lussenhof';
        $this->type->convertToDatabaseValue($value, $this->platform);
    }
    
    
    public function provideStupidValues()
    {
        return array(
            array('//'),
            array('###'),
            array('lussenhofen%meister'),
        );
    }
    
    
    public function provideAcceptableValues()
    {
        return array(
            array(
                '[lussen.hofer];[lussen:meister];[1];[563]',
                array('lussen.hofer', 'lussen:meister', 1, 563),
            ),
            array(
                '[lussen.hofer]',
                array('lussen.hofer'),
            ),
        );
    }
    
    
    /**
     * @test
     * @dataProvider provideStupidValues
     */
    public function invalidCharactersShouldFailDatabaseConversion($stupidValue)
    {
        $this->expectException(ConversionException::class);

        $value = array($stupidValue);
        $this->type->convertToDatabaseValue($value, $this->platform);
    }
    
    
    /**
     * @test
     * @dataProvider provideAcceptableValues
     */
    public function acceptableCharactersShouldPassDatabaseConversionAndReturnExpectedSerialization($expectedSerialization, $acceptableValue)
    {
        $serialization = $this->type->convertToDatabaseValue($acceptableValue, $this->platform);
        $this->assertSame($expectedSerialization, $serialization);
    }
    
    
    public function provideSerializedValues()
    {
        return array(
            array(
                array('lussen', 'hofer', '645', 'meisten:lusdre', 'larva.lussutab.tussi'),
                '[lussen];[hofer];[645];[meisten:lusdre];[larva.lussutab.tussi]',
            )
        );
    }
    
    
    /**
     * @test
     * @dataProvider provideSerializedValues
     */
    public function valuesShouldDeserializeProperly($expected, $serialized)
    {
        $deserialized = $this->type->convertToPHPValue($serialized, $this->platform);
        $this->assertSame($expected, $deserialized);
    }
}
