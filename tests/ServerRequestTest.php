<?php

namespace BinSoul\Test\Net\Http\Message;

use BinSoul\Net\Http\Message\Collection\HeaderCollection;
use BinSoul\Net\Http\Message\Collection\ParameterCollection;
use BinSoul\Net\Http\Message\ServerRequest;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class ServerRequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var ServerRequest */
    private $defaultRequest;

    public function setUp()
    {
        /** @var UriInterface $uri */
        $uri = $this->getMock(UriInterface::class);
        /** @var StreamInterface $body */
        $body = $this->getMock(StreamInterface::class);

        $this->defaultRequest = new ServerRequest('get', $uri, $body);
    }

    public function test_default_constructor()
    {
        /** @var UriInterface $uri */
        $uri = $this->getMock(UriInterface::class);
        /** @var StreamInterface $body */
        $body = $this->getMock(StreamInterface::class);

        $request = new ServerRequest('get', $uri, $body);
        $this->assertEquals([], $request->getAttributes());
        $this->assertEquals([], $request->getCookieParams());
        $this->assertEquals([], $request->getParsedBody());
        $this->assertEquals([], $request->getQueryParams());
        $this->assertEquals([], $request->getServerParams());
        $this->assertEquals([], $request->getUploadedFiles());
    }

    public function test_constructor()
    {
        /** @var UriInterface $uri */
        $uri = $this->getMock(UriInterface::class);
        /** @var StreamInterface $body */
        $body = $this->getMock(StreamInterface::class);

        $headers = ['X-Foobar' => ['Bar', 'Baz']];
        $cookies = ['Cookie' => 'Value'];
        $server = ['Server' => 'Value'];
        $query = ['Query' => 'Value'];
        $post = ['Post' => 'Value'];
        $files = ['File' => 'Value'];

        $request = new ServerRequest(
            'Post',
            $uri,
            $body,
            new HeaderCollection($headers),
            new ParameterCollection($cookies),
            new ParameterCollection($query),
            new ParameterCollection($post),
            new ParameterCollection($server),
            $files,
            '1.0'
        );

        $this->assertSame($uri, $request->getUri());
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('1.0', $request->getProtocolVersion());
        $this->assertSame($body, $request->getBody());
        $this->assertEquals(['Bar', 'Baz'], $request->getHeader('X-Foobar'));
        $this->assertEquals($cookies, $request->getCookieParams());
        $this->assertEquals($server, $request->getServerParams());
        $this->assertEquals($query, $request->getQueryParams());
        $this->assertEquals($post, $request->getParsedBody());
        $this->assertEquals($files, $request->getUploadedFiles());
    }

    public function test_withUploadedFiles_returns_instance_with_new_files()
    {
        $new = $this->defaultRequest->withUploadedFiles(['foo' => 'bar']);
        $this->assertNotSame($this->defaultRequest, $new);
        $this->assertEquals(['foo' => 'bar'], $new->getUploadedFiles());
    }

    public function test_withCookieParams_returns_instance_with_new_cookies()
    {
        $new = $this->defaultRequest->withCookieParams(['foo' => 'bar']);
        $this->assertNotSame($this->defaultRequest, $new);
        $this->assertEquals(['foo' => 'bar'], $new->getCookieParams());
    }

    public function test_withQueryParams_returns_instance_with_new_query()
    {
        $new = $this->defaultRequest->withQueryParams(['foo' => 'bar']);
        $this->assertNotSame($this->defaultRequest, $new);
        $this->assertEquals(['foo' => 'bar'], $new->getQueryParams());
    }

    public function test_withParsedBody_returns_instance_with_new_post()
    {
        $new = $this->defaultRequest->withParsedBody(['foo' => 'bar']);
        $this->assertNotSame($this->defaultRequest, $new);
        $this->assertEquals(['foo' => 'bar'], $new->getParsedBody());
    }

    public function test_withAttributes_returns_instance_with_new_attributes()
    {
        $new = $this->defaultRequest->withAttributes(['foo' => 'bar']);
        $this->assertNotSame($this->defaultRequest, $new);
        $this->assertEquals(['foo' => 'bar'], $new->getAttributes());
    }

    public function test_withAttribute_returns_instance_with_new_attribute()
    {
        $new = $this->defaultRequest->withAttribute('foo', 'bar');
        $this->assertNotSame($this->defaultRequest, $new);
        $this->assertEquals(['foo' => 'bar'], $new->getAttributes());
    }

    public function test_withoutAttribute_returns_instance_without_attribute()
    {
        $request = $this->defaultRequest->withAttribute('foo', 'bar')->withAttribute('baz', 'qux');

        $new = $request->withoutAttribute('foo');
        $this->assertNotSame($request, $new);
        $this->assertEquals(['baz' => 'qux'], $new->getAttributes());
    }

    public function test_getAttribute_returns_value()
    {
        $request = $this->defaultRequest->withAttribute('foo', 'bar')->withAttribute('baz', 'qux');

        $this->assertNull($request->getAttribute('missing'));
        $this->assertEquals('abc', $request->getAttribute('missing', 'abc'));
        $this->assertEquals('bar', $request->getAttribute('foo'));
    }

    public function test_withMethod_returns_instance_with_new_uppercase_method()
    {
        $new = $this->defaultRequest->withMethod('foo');
        $this->assertNotSame($this->defaultRequest, $new);
        $this->assertEquals('FOO', $new->getMethod());
    }

    public function test_getMethod_returns_uppercase_method()
    {
        /** @var UriInterface $uri */
        $uri = $this->getMock(UriInterface::class);
        /** @var StreamInterface $body */
        $body = $this->getMock(StreamInterface::class);

        $request = new ServerRequest('foo', $uri, $body);
        $this->assertEquals('FOO', $request->getMethod());
    }
}
