<?php

namespace App\Mapping;

/**
 * Represents the base class of a mapper.
 *
 * Mappers are needed to convert raw (entity) objects to a given DTO (Data Transfer Object).
 *
 * In my case I need them to represent database entities to frontend interfaces
 * which helps with the JSON naming policies since my tables use PascalCase but frontend expects camelCase.
 */
abstract class Mapper
{
    /**
     * Converts a single object.
     */
    abstract function mapSingle(mixed $object);
    /**
     * Converts an array of objects.
     */
    abstract function mapArray(array $objects);
}