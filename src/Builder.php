<?php

namespace Szogyenyid\PhpBuilder;

use ReflectionClass;

/**
 * This trait allows you to automagically create a Builder (Design Pattern) for your class.
 */
trait Builder
{
    /**
     * This method returns a builder object. It can be used with all of your class' properties by calling the
     * "with" + PropertyName methods, e.g. `withName()` to set `$name` property of your class.
     */
    public static function builder()
    {
        return new class ($outerClass = static::class) {
            private object $instance;
            private string $outerClass;
            public function __construct(string $outerClass)
            {
                $this->outerClass = $outerClass;
                $this->reset();
            }
            /**
             * Instantiate a new instance of the class to be built.
             *
             * @return void
             */
            public function reset(): void
            {
                $class = $this->outerClass;
                $this->instance = new $class();
            }
            /**
             * Build and return the built instance of the class.
             *
             * @return object
             */
            public function build(): object
            {
                return $this->instance;
            }
            /**
             * This method is called when a method is called on the builder object.
             * It will try to find a setter method for the attribute and call it with the given value.
             *
             * @param string $name
             * @param array $arguments
             * @return self
             */
            public function __call($name, $arguments)
            {
                if (strpos($name, 'with') !== 0) {
                    throw BuilderException::invalidMethodName($name);
                }
                $property = lcfirst(str_replace('with', '', $name));
                $r = new ReflectionClass($this->instance);
                foreach ($r->getProperties() as $prop) {
                    if ($prop->getName() === $property) {
                        $prop->setAccessible(true);
                        $prop->setValue($this->instance, $arguments[0]);
                        return $this;
                    }
                }
                throw BuilderException::notSettable($property, $this->outerClass);
            }
        };
    }
}
