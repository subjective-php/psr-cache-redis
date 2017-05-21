<?php

namespace ChadicusTest\Psr\SimpleCache;

use Chadicus\Psr\SimpleCache\BasicSerializer;

/**
 * @coversDefaultClass \Chadicus\Psr\SimpleCache\BasicSerializer
 * @covers ::<private>
 */
final class BasicSerializerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     * @covers ::unserialize
     *
     * @return void
     */
    public function unserializeBasicUse()
    {
        $serializer = new BasicSerializer();
        $this->assertSame([1, 2, 3], $serializer->unserialize(serialize([1, 2, 3])));
    }

    /**
     * @test
     * @covers ::unserialize
     * @expectedException \InvalidArgumentException
     *
     * @return void
     */
    public function unserializeInvalidData()
    {
        (new BasicSerializer())->unserialize([1, 2, 3]);
    }

    /**
     * @test
     * @covers ::serialize
     *
     * @return void
     */
    public function serializeBasicUse()
    {
        $serializer = new BasicSerializer();
        $this->assertSame(serialize([1, 2, 3]), $serializer->serialize([1, 2, 3]));
    }
}
