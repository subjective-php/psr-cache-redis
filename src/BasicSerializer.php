<?php

namespace Chadicus\Psr\SimpleCache;

/**
 * Uses native php serialize functions for serializing data.
 */
final class BasicSerializer implements SerializerInterface
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
    public function unserialize($data)
    {
        if (!is_string($data)) {
            throw new \InvalidArgumentException('$data must be a string');
        }

        return unserialize($data);
    }

    /**
     * Serializes the given data for storage in caching.
     *
     * @param mixed $value The data to serialize for caching.
     *
     * @return mixed The result of serializing the given $data.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException Thrown if the given value cannot be serialized for caching.
     */
    public function serialize($value)
    {
        return serialize($value);
    }
}
