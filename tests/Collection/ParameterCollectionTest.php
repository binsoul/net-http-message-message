<?php

namespace BinSoul\Test\Net\Http\Message\Collection;

use BinSoul\Net\Http\Message\Collection\ParameterCollection;

class ParameterCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function test_all()
    {
        $expected = [];
        $collection = new ParameterCollection();
        $this->assertSame($expected, $collection->all());

        $expected = [1 => 'test', 2 => 'test'];
        $collection = new ParameterCollection($expected);
        $this->assertSame($expected, $collection->all());

        $expected = [1 => 'test', 2 => 'test', 3 => 'test'];
        $collection->set(3, 'test');
        $this->assertSame($expected, $collection->all());
    }

    public function test_get()
    {
        $collection = new ParameterCollection();
        $this->assertEquals(null, $collection->get('test'));

        $collection = new ParameterCollection();
        $this->assertEquals(1, $collection->get('test', 1));

        $expected = [1 => 'test', 2 => 'test'];
        $collection = new ParameterCollection($expected);
        $this->assertEquals('test', $collection->get(2));
        $this->assertEquals(null, $collection->get(3));

        $expected = ['a' => 'test', 'b' => 'test'];
        $collection = new ParameterCollection($expected);
        $this->assertEquals('test', $collection->get('b'));
        $this->assertEquals(null, $collection->get('c'));

        $expected = ['a' => 'test', 'b' => ['c' => ['d' => 'test']]];
        $collection = new ParameterCollection($expected);
        $this->assertEquals(['c' => ['d' => 'test']], $collection->get('b'));
        $this->assertEquals(['d' => 'test'], $collection->get('b[c]'));
        $this->assertEquals('test', $collection->get('b[c][d]'));

        $this->assertEquals(null, $collection->get('b[missing][d]'));
    }

    public function test_set()
    {
        $collection = new ParameterCollection();
        $collection->set('a', 'test');
        $this->assertEquals('test', $collection->get('a'));
        $this->assertEquals(1, count($collection->all()));

        $expected = [1 => 'test', 2 => 'test'];
        $collection = new ParameterCollection($expected);
        $collection->set(1, 'abc');
        $this->assertEquals('abc', $collection->get(1));
        $collection->set('a', 'abc');
        $this->assertEquals('abc', $collection->get('a'));
        $collection->set('b', ['c' => ['d' => 'test']]);
        $this->assertEquals(['c' => ['d' => 'test']], $collection->get('b'));
        $collection->set('b[c][e]', 'abc');
        $this->assertEquals('abc', $collection->get('b[c][e]'));

        $collection->set('b[missing][e]', 'abc');
        $this->assertEquals('abc', $collection->get('b[missing][e]'));
    }

    public function test_has()
    {
        $collection = new ParameterCollection();
        $this->assertFalse($collection->has('test'));

        $expected = [1 => 'test', 2 => 'test'];
        $collection = new ParameterCollection($expected);
        $this->assertTrue($collection->has(1));
        $this->assertFalse($collection->has(3));

        $expected = ['a' => 'test', 'b' => ['c' => ['d' => 'test']]];
        $collection = new ParameterCollection($expected);
        $this->assertTrue($collection->has('b'));
        $this->assertTrue($collection->has('b[c]'));
        $this->assertTrue($collection->has('b[c][d]'));
        $this->assertFalse($collection->has('b[c][e]'));
        $this->assertFalse($collection->has('b[e]'));
        $this->assertFalse($collection->has('b[e][g]'));

        $this->assertFalse($collection->has('b[missing][g]'));
    }

    public function test_remove()
    {
        $data = [1 => 'test', 2 => 'test'];
        $collection = new ParameterCollection($data);
        $collection->remove(2);
        $this->assertSame([1 => 'test'], $collection->all());

        $data = ['a' => 'test', 'b' => ['c' => ['d' => 'test']]];
        $collection = new ParameterCollection($data);
        $collection->remove('b[c][d]');
        $this->assertSame(['a' => 'test', 'b' => ['c' => []]], $collection->all());

        $collection->remove('b[missing][e]');
        $this->assertSame(['a' => 'test', 'b' => ['c' => []]], $collection->all());
    }
}
