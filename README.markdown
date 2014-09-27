Factory Girl in PHP
===================

[![Build Status](https://secure.travis-ci.org/breerly/factory-girl-php.png?branch=master)](http://travis-ci.org/breerly/factory-girl-php)

A PHP port of Thoughtbot's Ruby [Factory Girl](https://github.com/thoughtbot/factory_girl). Based on a fork of [xi-doctrine](https://github.com/xi-project/xi-doctrine).


FactoryGirl FixtureFactory
--------------

`FactoryGirl\Provider\Doctrine\FixtureFactory` provides convenient creation of Doctrine entities in tests. If you're familiar with [FactoryGirl](https://github.com/thoughtbot/factory_girl) for Ruby, then this is essentially the same thing for Doctrine/PHP.

### Motivation ###

Many web applications have non-trivial database structures with lots of dependencies between tables. A component of such an application might deal with entities from only one or two tables, but the entities may depend on a complex entity graph to be useful or pass validation.

For instance, a `User` may be a member of a `Group`, which is part of an `Organization`, which in turn depends on five different tables describing who-knows-what about the organization. You are writing a component that change's the user's password and are currently uninterested in groups, organizations and their dependencies. How do you set up your test?

1. Do you create all dependencies for `Organization` and `Group` to get a valid `User` in your `setUp()`? No, that would be horribly tedious and verbose.
2. Do you make a shared fixture for all your tests that includes an example organization with satisifed dependencies? No, that would make the fixture extremely fragile.
3. Do you use mock objects? Sure, where practical. In many cases, however, the code you're testing interacts with the entities in such a complex way that mocking them sufficiently is impractical.

`FixtureFactory` is a middle ground between *(1)* and *(2)*. You specify how to generate your entities and their dependencies in one central place but explicitly create them in your tests, overriding only the fields you want.

### Tutorial ###

We'll assume you have a base class for your tests that arranges a fresh `EntityManager` connected to a minimally initialized blank test database. A simple factory setup looks like this.

```php
<?php
use FactoryGirl\Provider\Doctrine\FixtureFactory,
    FactoryGirl\Provider\Doctrine\FieldDef;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $factory;

    public function setUp()
    {
        // ... (set up a blank database and $this->entityManager) ...

        $this->factory = new FixtureFactory($this->entityManager);
        $this->factory->setEntityNamespace('What\Ever'); // If applicable

        // Define that users have names like user_1, user_2, etc.,
        // that they are not administrators by default and
        // that they point to a Group entity.
        $this->factory->defineEntity('User', array(
            'username' => FieldDef::sequence("user_%d"),
            'administrator' => false,
            'group' => FieldDef::reference('Group')
        ));

        // Define a Group to just have a unique name as above.
        // The order of the definitions does not matter.
        $this->factory->defineEntity('Group', array(
            'name' => FieldDef::sequence("group_%d")
        ));


        // If you want your created entities to be saved by default
        // then do the following. You can selectively re-enable or disable
        // this behavior in each test as well.
        // It's recommended to only enable this in tests that need it.
        // In any case, you'll need to call flush() yourself.
        //$this->factory->persistOnGet();
    }
}
```

Now you can easily get entities and override fields relevant to your test case like this.

```php
<?php
class UserServiceTest extends TestCase
{
    // ...

    public function testChangingPasswords()
    {
        $user = $this->factory->get('User', array(
            'name' => 'John'
        ));
        $this->service->changePassword($user, 'xoo');
        $this->assertSame($user, $this->service->authenticateUser('john', 'xoo'));
    }
}
```

### Singletons ###

Sometimes your entity has a dependency graph with several references to some entity type. For instance, the application may have a concept of a "current organization" with users, groups, products, categories etc. belonging to an organization. By default `FixtureFactory` would create a new `Organization` each time one is needed, which is not always what you want. Sometimes you'd like each new entity to point to one shared `Organization`.

Your first reaction should be to avoid situations like this and specify the shared entity explicitly when you can't. If that isn't feasible for whatever reason, `FixtureFactory` allows you to make an entity a *singleton*. If a singleton exists for a type of entity then `get()` will return that instead of creating a new instance.

```php
<?php
class SomeTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->org = $this->factory->getAsSingleton('Organization');
    }

    public function testSomething()
    {
        $user1 = $this->factory->get('User');
        $user2 = $this->factory->get('User');

        // now $user1->getOrganization() === $user2->getOrganization() ...
    }
}
```

It's highly recommended to create singletons only in the setups of individual test classes and *NOT* in the base class of your tests.

### Advanced ###

You can give an 'afterCreate' callback to be called after an entity is created and its fields are set. Here you can, for instance, invoke the entity's constructor, since `FixtureFactory` doesn't do that by default.

```php
<?php
$factory->defineEntity('User', array(
    'username' => FieldDef::sequence("user_%d"),
), array(
    'afterCreate' => function(User $user, array $fieldValues) {
        $user->__construct($fieldValues['username']);
    }
));
```

### API reference ###

```php
<?php

// Defining entities
$factory->defineEntity('EntityName', array(
    'simpleField' => 'constantValue',

    'generatedField' => function($factory) { return ...; },

    'sequenceField1' => FieldDef::sequence('name-%d'), // name-1, name-2, ...
    'sequenceField2' => FieldDef::sequence('name-'),   // the same
    'sequenceField3' => FieldDef::sequence(function($n) { return "name-$n"; }),

    'referenceField' => FieldDef::reference('OtherEntity')
), array(
    'afterCreate' => function($entity, $fieldValues) {
        // ...
    }
));

// Getting an entity (new or singleton)
$factory->get('EntityName', array('field' => 'value'));

// Singletons
$factory->getAsSingleton('EntityName', array('field' => 'value'));
$factory->setSingleton('EntityName', $entity);
$factory->unsetSingleton('EntityName');

// Configuration
$this->factory->setEntityNamespace('What\Ever');  // Default: empty
$this->factory->persistOnGet();                   // Default: don't persist
$this->factory->persistOnGet(false);
```

### Miscellaneous ###

- `FixtureFactory` and `FieldDef` are designed to be subclassable.
- With bidirectional one-to-many associations, the collection on the 'one'
  side will get updated as long as you've remembered to specify the
  `inversedBy` attribute in your mapping.

### Development ###

#### Tests ####

You need to have `PHPUnit` installed globally.

```shell
composer install --global phpunit/phpunit
```

The composer packages must be installed with

```
composer install --prefer-source
```
