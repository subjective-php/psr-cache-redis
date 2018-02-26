<?php

namespace Chadicus\Psr\SimpleCache;

use Chadicus\Psr\SimpleCache\Serializer\BasicSerializer;
use Chadicus\Psr\SimpleCache\Serializer\SerializerInterface;
use DateInterval;
use DateTime;
use Predis\ClientInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * A PSR-16 implementation which stores data in a RedisDB collection.
 */
final class RedisCache implements CacheInterface
{
    use KeyValidatorTrait;
    use TTLValidatorTrait;

    /**
     * Redis client for storing cache.
     *
     * @var ClientInterface
     */
    private $client;

    /**
     * The object responsible for serializing data to and from redis values.
     *
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Construct a new instance of RedisCache.
     *
     * @param ClientInterface     $client     Redis client for storing cache.
     * @param SerializerInterface $serializer Object responsible for serializing data to and from redis values.
     */
    public function __construct(ClientInterface $client, SerializerInterface $serializer = null)
    {
        $this->client = $client;
        $this->serializer = $serializer ?: new BasicSerializer();
    }

    /**
     * Fetches a value from the cache.
     *
     * @param string $key     The unique key of this item in the cache.
     * @param mixed  $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     *
     * @throws InvalidArgumentException Thrown if the $key string is not a legal value.
     */
    public function get($key, $default = null)//@codingStandardsIgnoreLine Interface does not define type-hints or return
    {
        $this->validateKey($key);

        $cached = $this->client->get($key);
        if ($cached !== null) {
            return $this->serializer->unserialize($cached);
        }

        return $default;
    }

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param iterable $keys    A list of keys that can obtained in a single operation.
     * @param mixed    $default Default value to return for keys that do not exist.
     *
     * @return array List of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
     *
     * @throws InvalidArgumentException Thrown if the $key string is not a legal value.
     */
    public function getMultiple($keys, $default = null)//@codingStandardsIgnoreLine Interface does not define type-hints or return
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }

        return $result;
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string                    $key   The key of the item to store.
     * @param mixed                     $value The value of the item to store, must be serializable.
     * @param null|integer|DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
     *                                         the driver supports TTL then the library may set a default value
     *                                         for it or let the driver take care of that.
     *
     * @return boolean True on success and false on failure.
     *
     * @throws InvalidArgumentException Thrown if the $key string is not a legal value.
     */
    public function set($key, $value, $ttl = null)//@codingStandardsIgnoreLine Interface does not define type-hints or return
    {
        $this->validateKey($key);
        $this->validateTTL($ttl);
        if ($this->client->set($key, $this->serializer->serialize($value))->getPayload() !== 'OK') {
            return false;
        }

        $this->setExpires($key, $ttl);

        return true;
    }

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     *
     * @param iterable                  $values A list of key => value pairs for a multiple-set operation.
     * @param null|integer|DateInterval $ttl    Optional. The TTL value of this item. If no value is sent and
     *                                          the driver supports TTL then the library may set a default value
     *                                          for it or let the driver take care of that.
     *
     * @return boolean True on success and false on failure.
     *
     * @throws InvalidArgumentException Thrown if $values is neither an array nor a Traversable,
     *                                  or if any of the $values are not a legal value.
     */
    public function setMultiple($values, $ttl = null)//@codingStandardsIgnoreLine Interface does not define type-hints or return
    {
        $keys = array_keys($values);
        array_walk($keys, [$this, 'validateKey']);
        $this->validateTTL($ttl);
        foreach ($values as $key => $value) {
            if (!$this->set($key, $value, $ttl)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key The unique cache key of the item to delete.
     *
     * @return boolean True if the item was successfully removed. False if there was an error.
     *
     * @throws InvalidArgumentException Thrown if the $key string is not a legal value.
     */
    public function delete($key)//@codingStandardsIgnoreLine Interface does not define type-hints or return
    {
        return $this->deleteMultiple([$key]);
    }

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param iterable $keys A list of string-based keys to be deleted.
     *
     * @return boolean True if the items were successfully removed. False if there was an error.
     *
     * @throws InvalidArgumentException Thrown if $keys is neither an array nor a Traversable,
     *                                  or if any of the $keys are not a legal value.
     */
    public function deleteMultiple($keys)//@codingStandardsIgnoreLine Interface does not define type-hints
    {
        array_walk($keys, [$this, 'validateKey']);
        return $this->client->del($keys) === count($keys);
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return boolean True on success and false on failure.
     */
    public function clear()//@codingStandardsIgnoreLine Interface does not define type-hints or return
    {
        $this->client->flushall();
        return true;
    }

    /**
     * Determines whether an item is present in the cache.
     *
     * NOTE: It is recommended that has() is only to be used for cache warming type purposes
     * and not to be used within your live applications operations for get/set, as this method
     * is subject to a race condition where your has() will return true and immediately after,
     * another script can remove it making the state of your app out of date.
     *
     * @param string $key The cache item key.
     *
     * @return boolean
     *
     * @throws InvalidArgumentException Thrown if the $key string is not a legal value.
     */
    public function has($key) //@codingStandardsIgnoreLine  Interface does not define type-hints
    {
        $this->validateKey($key);
        return (bool)$this->client->exists($key);
    }

    /**
     * Converts the given time to live value to a DataTime instance;
     *
     * @param string $key The cache item key.
     * @param mixed  $ttl The time-to-live value to validate.
     *
     * @return void
     */
    private function setExpires(string $key, $ttl)
    {
        if ($ttl === null) {
            return;
        }

        if (is_int($ttl)) {
            $ttl = DateInterval::createFromDateString("{$ttl} seconds");
        }

        $this->client->expireat($key, (new DateTime())->add($ttl)->getTimestamp());
    }
}
