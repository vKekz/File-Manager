<?php

namespace Core\Utilities;

abstract class Deserializer
{
    public static function fromArray(array $array)
    {
        $className = get_called_class();
        $classInstance = new $className();

        $properties = get_class_vars($className);
        foreach ($array as $key => $value)
        {
            if (!property_exists($classInstance, $key))
            {
                continue;
            }

            $classInstance->{$key} = $value;
        }

        return $classInstance;
    }
}