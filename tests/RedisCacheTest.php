<?php

namespace SubjectivePHPTest\Psr\SimpleCache;

use DateTime;
use SubjectivePHP\Psr\SimpleCache\RedisCache;
use Predis\Client;

/**
 * @coversDefaultClass \SubjectivePHP\Psr\SimpleCache\RedisCache
 * @covers ::__construct
 * @covers ::<private>
 */
final class RedisCacheTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Client
     */
    private $predis;

    /**
     * @var RedisCache
     */
    private $cache;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->predis = new Client();
        $this->predis->flushall();
        $this->cache = new RedisCache($this->predis);
    }

    /**
     * @test
     * @covers ::get
     *
     * @return void
     */
    public function get()
    {
        $dateTime = new DateTime();
        $this->predis->set('foo', serialize($dateTime));
        $this->assertEquals($dateTime, $this->cache->get('foo'));
    }

    /**
     * @test
     * @covers ::get
     *
     * @return void
     */
    public function getKeyNotFound()
    {
        $default = new \StdClass();
        $this->assertSame($default, $this->cache->get('foo', $default));
    }

    /**
     * @test
     * @covers ::getMultiple
     *
     * @return void
     */
    public function getMultple()
    {
        $default = new \StdClass();
        $dateTime = new \DateTime();
        $exception = new \RuntimeException();
        $this->predis->set('foo', serialize($dateTime));
        $this->predis->set('bar', serialize($exception));
        $actual = $this->cache->getMultiple(['foo', 'baz', 'bar'], $default);
        $this->assertEquals($dateTime, $actual['foo']);
        $this->assertSame($default, $actual['baz']);
        $this->assertEquals($exception, $actual['bar']);
    }

    /**
     * @test
     * @covers ::set
     *
     * @return void
     */
    public function set()
    {
        $dateTime = new \DateTime();
        $this->assertTrue($this->cache->set('foo', $dateTime, 3600));
        $ttl = $this->predis->ttl('foo');
        $this->assertSame(serialize($dateTime), $this->predis->get('foo'));
        $this->assertGreaterThan(3598, $ttl);
        $this->assertLessThanOrEqual(3600, $ttl);
    }

    /**
     * @test
     * @covers ::set
     *
     * @return void
     */
    public function setFails()
    {
        $mockPredis = $this->getMockBuilder('\\Predis\\ClientInterface')->getMock();
        $mockPredis->expects($this->once())->method('__call')->willReturn(new \Predis\Response\Status('Not OK'));
        $cache = new RedisCache($mockPredis);
        $this->assertFalse($cache->set('foo', new \DateTime(), 3600));
    }

    /**
     * @test
     * @covers ::setMultiple
     *
     * @return void
     */
    public function setMultple()
    {
        $dateTime = new \DateTime();
        $exception = new \RuntimeException();
        $this->assertTrue($this->cache->setMultiple(['foo' => $dateTime, 'bar' => $exception]));
        $this->assertSame(serialize($dateTime), $this->predis->get('foo'));
        $this->assertSame(serialize($exception), $this->predis->get('bar'));
    }

    /**
     * @test
     * @covers ::setMultiple
     *
     * @return void
     */
    public function setMultpleFails()
    {
        $passStatus = new \Predis\Response\Status('OK');
        $failStatus = new \Predis\Response\Status('Not OK');

        $mockPredis = $this->getMockBuilder('\\Predis\\ClientInterface')->getMock();
        $mockPredis->expects($this->exactly(3))->method('__call')->withConsecutive(
            [$this->equalTo('set'), $this->anything()],
            [$this->equalTo('expireat'), $this->anything()],
            [$this->equalTo('set'), $this->anything()]
        )->will($this->onConsecutiveCalls($passStatus, true, $failStatus));
        $cache = new RedisCache($mockPredis);
        $this->assertFalse(
            $cache->setMultiple(
                ['foo' => new \DateTime(), 'bar' => new \DateTime()],
                \DateInterval::createFromDateString('1 day')
            )
        );
    }

    /**
     * @test
     * @covers ::delete
     *
     * @return void
     */
    public function delete()
    {
        $dateTime = new DateTime();
        $this->predis->set('foo', serialize($dateTime));
        $this->cache->delete('foo');
        $this->assertSame(0, $this->predis->exists('foo'));
    }

    /**
     * @test
     * @covers ::deleteMultiple
     *
     * @return void
     */
    public function deleteMultiple()
    {
        $this->predis->set('foo', 'foo');
        $this->predis->set('bar', 'bar');
        $this->predis->set('baz', 'baz');

        $this->cache->deleteMultiple(['foo', 'bar']);
        $this->assertSame(0, $this->predis->exists('foo'));
        $this->assertSame(0, $this->predis->exists('bar'));
        $this->assertSame(1, $this->predis->exists('baz'));
    }

    /**
     * @test
     * @covers ::clear
     *
     * @return void
     */
    public function clear()
    {
        $this->predis->set('foo', 'foo');
        $this->predis->set('bar', 'bar');
        $this->predis->set('baz', 'baz');

        $this->cache->clear();
        $this->assertSame(0, $this->predis->exists('foo'));
        $this->assertSame(0, $this->predis->exists('bar'));
        $this->assertSame(0, $this->predis->exists('baz'));
    }

    /**
     * @test
     * @covers ::has
     *
     * @return void
     */
    public function has()
    {
        $this->predis->set('foo', 'foo');
        $this->assertTrue($this->cache->has('foo'));
        $this->assertFalse($this->cache->has('bar'));
    }
}
