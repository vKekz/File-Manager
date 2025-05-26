<?php

namespace Contracts;

/**
 * Objects implementing JsonDeserializable can override the method to be deserialized from a JSON string.
 */
interface JsonDeserializableInterface
{
    /**
     * Deserializes the JSON string to a given object.
     */
    public static function deserialize(string $json): object;
}