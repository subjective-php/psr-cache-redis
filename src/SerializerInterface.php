<?php

namespace Chadicus\Psr\SimpleCache;

/**
 * Contract for object responsible for serializing and unserializing data for caching.
 */
interface SerializerInterface
{
    /**
     * Unserializes cached data into the original state.
     *
     * @param mixed $data The data to unserialize.
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException Thrown if the given value cannot be unserialized.
     */
    public function unserialize($data);

    /**
     * Serializes the given data for storage in caching.
     *
     * @param mixed $value The data to serialize for caching.
     *
     * @return mixed The result of serializing the given $data.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException Thrown if the given value cannot be serialized for caching.
     */
    public function serialize($value);
}
