<?php

namespace BinSoul\Test\Net\Http\Message\Part;

use BinSoul\Net\Http\Message\Part\Header;

class HeaderTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructor()
    {
        $header = new Header('X-Foo', '');
        $this->assertEquals('X-Foo', $header->getName());
        $this->assertEquals([], $header->getValuesAsArray());
        $this->assertEquals('', $header->getValuesAsString());

        $values = ['foo', 'bar'];
        $header = new Header('X-Foo', $values);
        $this->assertEquals($values, $header->getValuesAsArray());

        $header = new Header('foo', 'foo, bar');
        $this->assertEquals($values, $header->getValuesAsArray());
    }

    public function test_can_add_values()
    {
        $header = new Header('X-Foo', '');

        $values = ['foo', 'bar'];
        $header->addValue($values);
        $this->assertEquals($values, $header->getValuesAsArray());

        $values = ['foo', 'bar', 'baz'];
        $header->addValue('baz');
        $this->assertEquals($values, $header->getValuesAsArray());

        $values = ['foo', 'bar', 'baz', 'qux'];
        $header->addValue('foo,qux');
        $this->assertEquals($values, $header->getValuesAsArray());
    }

    public function test_returns_registered_names()
    {
        $this->assertEquals('DNT', Header::getRegisteredName('Dnt'));
        $this->assertEquals('Content-MD5', Header::getRegisteredName('content-md5'));
        $this->assertEquals('X-Foo', Header::getRegisteredName('X-Foo'));
    }

    public function test_return_multiple()
    {
        $this->assertFalse(Header::hasMultipleValues('Dnt'));
        $this->assertTrue(Header::hasMultipleValues('Accept-Language'));
        $this->assertTrue(Header::hasMultipleValues('X-Foo'));
    }
}
