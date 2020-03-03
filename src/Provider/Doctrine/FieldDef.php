<?php

namespace FactoryGirl\Provider\Doctrine;

/**
 * Contains static methods to define fields as sequences, references etc.
 */
class FieldDef
{
    /**
     * Defines a field to be a string based on an incrementing integer.
     *
     * This is typically used to generate unique names such as usernames.
     *
     * The parameter may be a function that receives a counter value
     * each time the entity is created or it may be a string.
     *
     * If the parameter is a string string containing "%d" then it will be
     * replaced by the counter value. If the string does not contain "%d"
     * then the number is simply appended to the parameter.
     *
     * @param callable|string $funcOrString The function or pattern to generate a value from.
     * @param int $firstNum The first number to use.
     * @return callable
     */
    public static function sequence($funcOrString, $firstNum = 1)
    {
        $n = $firstNum - 1;
        if (is_callable($funcOrString)) {
            return function () use (&$n, $funcOrString) {
                $n++;
                return call_user_func($funcOrString, $n);
            };
        }

        if (strpos($funcOrString, '%d') !== false) {
            return function () use (&$n, $funcOrString) {
                $n++;
                return str_replace('%d', $n, $funcOrString);
            };
        }

        return function () use (&$n, $funcOrString) {
            $n++;
            return $funcOrString . $n;
        };
    }

    /**
     * @return DateIntervalHelper
     */
    public static function past()
    {
        return new DateIntervalHelper(new \DateTime(), true);
    }

    /**
     * @return DateIntervalHelper
     */
    public static function future()
    {
        return new DateIntervalHelper(new \DateTime(), false);
    }

    /**
     * Defines a field to `get()` a named entity from the factory.
     *
     * The normal semantics of `get()` apply.
     * Normally this means that the field gets a fresh instance of the named
     * entity. If a singleton has been defined, `get()` will return that.
     *
     * @param string $name The name of the entity to get.
     * @return callable
     */
    public static function reference($name)
    {
        return function (FixtureFactory $factory) use ($name) {
            return $factory->get($name);
        };
    }

    /**
     * Defines a field to `get()` a collection of named entities from the factory.
     *
     * The normal semantics of `get()` apply.
     *
     * Normally this means that the field gets a fresh instance of the named
     * entity. If a singleton has been defined, a collection with a single instance will be returned.
     *
     * @param string $name
     * @param int $numberOfInstances
     *
     * @throws \InvalidArgumentException
     * @return callable
     */
    public static function references($name, $numberOfInstances = 1)
    {
        if ($numberOfInstances < 1) {
            throw new \InvalidArgumentException('Can only get >= 1 instances');
        }

        return function (FixtureFactory $factory) use ($name, $numberOfInstances) {
            return $factory->getList(
                $name,
                [],
                $numberOfInstances
            );
        };
    }
}
