<?php

namespace BinSoul\Test\Net\Http\Message;

use BinSoul\Net\Http\Message\Collection\HeaderCollection;
use BinSoul\Net\Http\Message\Response;
use Psr\Http\Message\StreamInterface;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /** @var Response */
    private $defaultResponse;

    public function setUp()
    {
        /** @var StreamInterface $body */
        $body = $this->getMock(StreamInterface::class);

        $this->defaultResponse = new Response($body);
    }

    public function test_default_constructor()
    {
        /** @var StreamInterface $body */
        $body = $this->getMock(StreamInterface::class);

        $response = new Response($body);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getReasonPhrase());
    }

    public function test_constructor()
    {
        /** @var StreamInterface $body */
        $body = $this->getMock(StreamInterface::class);
        $headers = ['X-Foobar' => ['Bar', 'Baz']];

        $response = new Response(
            $body,
            410,
            new HeaderCollection($headers)
        );

        $this->assertSame($body, $response->getBody());
        $this->assertEquals(['Bar', 'Baz'], $response->getHeader('X-Foobar'));
        $this->assertEquals(410, $response->getStatusCode());
    }

    public function test_withStatus_returns_instance_with_new_status()
    {
        $new = $this->defaultResponse->withStatus(301);
        $this->assertNotSame($this->defaultResponse, $new);
        $this->assertEquals(301, $new->getStatusCode());
        $this->assertEquals('Moved Permanently', $new->getReasonPhrase());

        $new = $this->defaultResponse->withStatus(410, 'foo');
        $this->assertNotSame($this->defaultResponse, $new);
        $this->assertEquals(410, $new->getStatusCode());
        $this->assertEquals('foo', $new->getReasonPhrase());

        $new = $new->withStatus(301);
        $this->assertNotSame($this->defaultResponse, $new);
        $this->assertEquals(301, $new->getStatusCode());
        $this->assertEquals('Moved Permanently', $new->getReasonPhrase());
    }

    public function invalidStatus()
    {
        return [
            [99],
            [600],
            ['abc'],
            [null],
            [[]],
        ];
    }

    /**
     * @dataProvider invalidStatus
     * @expectedException \InvalidArgumentException
     */
    public function test_withStatus_raises_exception_for_invalid_code($testCode)
    {
        $this->defaultResponse->withStatus($testCode);
    }
}
