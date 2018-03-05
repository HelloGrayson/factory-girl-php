<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use FactoryGirl\Provider\Doctrine\FieldDef;
use Doctrine\ORM\Mapping;

class PersistingTest extends TestCase
{
    /**
     * @test
     */
    public function automaticPersistCanBeTurnedOn()
    {
        $this->factory->defineEntity('SpaceShip', ['name' => 'Zeta']);

        $this->factory->persistOnGet();
        $ss = $this->factory->get('SpaceShip');
        $this->em->flush();

        $this->assertNotNull($ss->getId());
        $this->assertSame($ss, $this->em->find('FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity\SpaceShip', $ss->getId()));
    }

    /**
     * @test
     */
    public function doesNotPersistByDefault()
    {
        $this->factory->defineEntity('SpaceShip', ['name' => 'Zeta']);
        $ss = $this->factory->get('SpaceShip');
        $this->em->flush();

        $this->assertNull($ss->getId());
        $q = $this->em
            ->createQueryBuilder()
            ->select('ss')
            ->from('FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity\SpaceShip', 'ss')
            ->getQuery();
        $this->assertEmpty($q->getResult());
    }

    /**
     * @test
     */
    public function doesNotPersistEmbeddableWhenAutomaticPersistingIsTurnedOn()
    {
        $mappingClasses = [
            Mapping\Embeddable::class,
            Mapping\Embedded::class,
        ];

        foreach ($mappingClasses as $mappingClass) {
            if (!class_exists($mappingClass)) {
                $this->markTestSkipped('Doctrine Embeddable feature not available');
            }
        }

        $this->factory->defineEntity('Name', [
            'first' => FieldDef::sequence(static function () {
                $values = [
                    null,
                    'Doe',
                    'Smith',
                ];

                return $values[array_rand($values)];
            }),
            'last' => FieldDef::sequence(static function () {
                $values = [
                    null,
                    'Jane',
                    'John',
                ];

                return $values[array_rand($values)];
            }),
        ]);

        $this->factory->defineEntity('Commander', [
            'name' => FieldDef::reference('Name'),
        ]);

        $this->factory->persistOnGet();

        /** @var TestEntity\Commander $commander */
        $commander = $this->factory->get('Commander');

        $this->assertInstanceOf(TestEntity\Commander::class, $commander);
        $this->assertInstanceOf(TestEntity\Name::class, $commander->name());

        $this->em->flush();
    }
}
