<?php

namespace Szogyenyid\PhpBuilder;

use Exception;

class BuilderException extends Exception
{
    public static function invalidMethodName(string $methodName): self
    {
        return new self("Builder method names must start with \"with\". Invalid method name: $methodName");
    }

    public static function notSettable(string $property, string $class): self
    {
        return new self("No property with name \"$$property\" found in class $class");
    }
}
