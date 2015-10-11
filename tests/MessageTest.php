<?php

namespace BinSoul\Test\Http\Message;

use BinSoul\Net\Http\Message\Collection\HeaderCollection;
use BinSoul\Net\Http\Message\Message;
use Psr\Http\Message\StreamInterface;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    /** @var Message */
    private $defaultMessage;

    public function setUp()
    {
        /** @var StreamInterface $body */
        $body = $this->getMock(StreamInterface::class);

        $this->defaultMessage = new Message($body, new HeaderCollection(), '1.1');
    }

    public function test_default_constructor()
    {
        /** @var StreamInterface $body */
        $body = $this->getMock(StreamInterface::class);

        $message = new Message($body);
        $this->assertNotEmpty($message->getProtocolVersion());
        $this->assertTrue(is_array($message->getHeaders()));
        $this->assertSame($body, $message->getBody());
    }

    public function test_withProtocolVersion_returns_instance_with_new_version()
    {
        $new = $this->defaultMessage->withProtocolVersion('1.0');
        $this->assertNotSame($this->defaultMessage, $new);
        $this->assertEquals('1.0', $new->getProtocolVersion());
    }

    public function test_withBody_returns_instance_with_new_body()
    {
        /** @var StreamInterface $body */
        $body = $this->getMock(StreamInterface::class);

        $new = $this->defaultMessage->withBody($body);

        $this->assertNotSame($this->defaultMessage, $new);
        $this->assertSame($body, $new->getBody());
    }

    public function test_withHeader_returns_instance_with_new_header()
    {
        $new = $this->defaultMessage->withHeader('Foo', ['Bar', 'Baz']);
        $this->assertNotSame($this->defaultMessage, $new);
        $this->assertEquals(['Bar', 'Baz'], $new->getHeader('Foo'));
    }

    public function test_withAddedHeader_returns_instance_with_new_header()
    {
        $new = $this->defaultMessage->withAddedHeader('Foo', 'Bar');
        $this->assertEquals(['Bar'], $new->getHeader('Foo'));

        $new = $this->defaultMessage->withHeader('Foo', ['Bar', 'Baz']);
        $new = $new->withAddedHeader('Foo', 'Qux');
        $this->assertNotSame($this->defaultMessage, $new);
        $this->assertEquals(['Bar', 'Baz', 'Qux'], $new->getHeader('Foo'));

        $new = $new->withAddedHeader('Foo', 'Qux');
        $this->assertEquals(['Bar', 'Baz', 'Qux'], $new->getHeader('Foo'));
    }

    public function test_withoutHeader_returns_instance_without_header()
    {
        $new = $this->defaultMessage->withHeader('X-Foo', ['Bar', 'Baz'])->withHeader('X-Bar', ['Baz', 'Qux']);

        $new = $new->withoutHeader('X-Qux');
        $this->assertNotSame($this->defaultMessage, $new);
        $this->assertEquals(['X-Foo' => ['Bar', 'Baz'], 'X-Bar' => ['Baz', 'Qux']], $new->getHeaders());

        $new = $new->withoutHeader('X-Foo');
        $this->assertNotSame($this->defaultMessage, $new);
        $this->assertEquals(['X-Bar' => ['Baz', 'Qux']], $new->getHeaders());

        $new = $new->withoutHeader('x-bar');
        $this->assertEquals([], $new->getHeaders());
    }

    public function headerValues()
    {
        return [
            [['Bar', 'Baz'], ['Bar', 'Baz'], 'Bar,Baz'],
            [['Bar', 'Baz', ''], ['Bar', 'Baz'], 'Bar,Baz'],
            [['Bar', 'Baz', 'Baz'], ['Bar', 'Baz'], 'Bar,Baz'],
            ['Bar,Baz', ['Bar', 'Baz'], 'Bar,Baz'],
            ['Bar , Baz', ['Bar', 'Baz'], 'Bar,Baz'],
            ['Bar , Baz , ', ['Bar', 'Baz'], 'Bar,Baz'],
            ['Bar , Baz , Baz', ['Bar', 'Baz'], 'Bar,Baz'],
        ];
    }

    /**
     * @dataProvider headerValues
     */
    public function test_getHeader_returns_values_as_array($values, $expectedArray)
    {
        $new = $this->defaultMessage->withHeader('X-Foobar', $values);
        $this->assertEquals($expectedArray, $new->getHeader('X-Foobar'));
    }

    /**
     * @dataProvider headerValues
     */
    public function test_getHeaderLine_returns_values_as_string($values, $expectedArray, $expectedString)
    {
        $new = $this->defaultMessage->withHeader('X-BarBaz', $values);
        $this->assertEquals($expectedString, $new->getHeaderLine('X-BarBaz'));
    }

    public function test_keeps_case_from_first_registration()
    {
        $new = $this->defaultMessage->withHeader('X-Foo', 'Foo')->withAddedHeader('x-foo', 'Bar');
        $this->assertEquals(['X-Foo' => ['Foo', 'Bar']], $new->getHeaders());
    }

    public function test_hasHeader_returns_correct_presence()
    {
        $this->assertFalse($this->defaultMessage->hasHeader('X-Foobar'));

        $new = $this->defaultMessage->withHeader('X-Foobar', 'Bar,baz');
        $this->assertTrue($new->hasHeader('X-Foobar'));

        $new = $this->defaultMessage->withHeader('X-BarBaz', '');
        $this->assertTrue($new->hasHeader('X-BarBaz'));
    }

    public function test_returns_empty_values_for_missing_header()
    {
        $this->assertSame([], $this->defaultMessage->getHeader('X-Foo-Bar'));
        $this->assertEmpty($this->defaultMessage->getHeaderLine('X-Foo-Bar'));
    }
}
