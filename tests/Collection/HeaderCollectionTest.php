<?php

namespace BinSoul\Test\Net\Http\Message\Collection;

use BinSoul\Net\Http\Message\Collection\HeaderCollection;

class HeaderCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function test_all()
    {
        $headers = [];
        $collection = new HeaderCollection();
        $this->assertSame($headers, $collection->all());

        $headers = ['123' => ['456'], 'abc' => ['def']];
        $collection = new HeaderCollection(['123' => '456', 'abc' => 'def']);
        $this->assertSame($headers, $collection->all());

        $headers = ['123' => ['456'], 'abc' => ['def'], 'foo' => ['bar']];
        $collection->set('foo', 'bar');
        $this->assertSame($headers, $collection->all());
    }

    public function test_get()
    {
        $collection = new HeaderCollection();
        $this->assertEquals(null, $collection->get('foo'));

        $collection = new HeaderCollection();
        $this->assertEquals('bar', $collection->get('foo', 'bar'));

        $headers = ['123' => '456', 'abc' => 'def'];
        $collection = new HeaderCollection($headers);
        $this->assertEquals('def', $collection->get('abc'));

        $headers = ['a' => 'test', 'b' => 'foo, bar'];
        $collection = new HeaderCollection($headers);
        $this->assertEquals('foo,bar', $collection->get('b'));
    }

    public function test_getValues()
    {
        $collection = new HeaderCollection();
        $this->assertEquals([], $collection->getValues('foo'));

        $collection = new HeaderCollection();
        $this->assertEquals(['bar'], $collection->get('foo', ['bar']));

        $headers = ['123' => '456', 'abc' => 'def'];
        $collection = new HeaderCollection($headers);
        $this->assertEquals(['def'], $collection->getValues('abc'));

        $headers = ['a' => 'test', 'b' => 'foo, bar'];
        $collection = new HeaderCollection($headers);
        $this->assertEquals(['foo', 'bar'], $collection->getValues('b'));
    }

    public function test_set()
    {
        $collection = new HeaderCollection();
        $collection->set('foo', 'bar');
        $this->assertEquals('bar', $collection->get('foo'));

        $headers = ['123' => '456', 'abc' => 'def'];
        $collection = new HeaderCollection($headers);
        $collection->set('abc', 'foo', true);
        $this->assertEquals('foo', $collection->get('abc'));
        $collection->set('abc', 'bar', false);
        $this->assertEquals('foo,bar', $collection->get('abc'));

        $headers = ['123' => '456', 'abc' => 'def'];
        $collection = new HeaderCollection($headers);
        $collection->set('abc', ['foo', 'bar'], true);
        $this->assertEquals('foo,bar', $collection->get('abc'));
        $collection->set('abc', 'baz', false);
        $this->assertEquals('foo,bar,baz', $collection->get('abc'));
    }

    public function test_has()
    {
        $collection = new HeaderCollection();
        $this->assertFalse($collection->has('test'));

        $headers = ['123' => '456', 'abc' => 'def'];
        $collection = new HeaderCollection($headers);
        $this->assertTrue($collection->has('abc'));
        $this->assertFalse($collection->has('foo'));
    }

    public function test_remove()
    {
        $headers = ['123' => '456,789', 'abc' => 'def'];
        $collection = new HeaderCollection($headers);
        $collection->remove('abc');
        $this->assertSame(['123' => ['456', '789']], $collection->all());
    }

    public function test_preserves_known_single_values()
    {
        $userAgent = 'Mozilla/5.0 (KHTML, like Gecko) Chrome/45.0.2454.101';
        $collection = new HeaderCollection(['User-Agent' => $userAgent]);
        $this->assertEquals($userAgent, $collection->get('User-Agent'));
        $this->assertCount(1, $collection->getValues('User-Agent'));
    }
}
