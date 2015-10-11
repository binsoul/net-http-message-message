<?php

namespace BinSoul\Test\Net\Http\Message;

use BinSoul\Net\Http\Message\Collection\HeaderCollection;
use BinSoul\Net\Http\Message\Request;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var Request */
    private $defaultRequest;

    /**
     * @param string $uri
     *
     * @return UriInterface
     */
    private function buildUri($uri)
    {
        if ($uri != '') {
            $parts = parse_url($uri);
        } else {
            $parts = [];
        }

        $parts = array_merge(
            [
                'scheme' => '',
                'host' => '',
                'port' => null,
                'user' => '',
                'pass' => '',
                'path' => '',
                'query' => '',
                'fragment' => '',
            ],
            $parts
        );

        $hostPort = $parts['port'] != '' ? $parts['host'].':'.$parts['port'] : $parts['host'];
        $userPass = $parts['user'] != '' ? $parts['user'].':'.$parts['pass'].'@' : '';

        $uri = $this->getMock(UriInterface::class);
        $uri->expects($this->any())->method('getScheme')->willReturn($parts['scheme']);
        $uri->expects($this->any())->method('getHost')->willReturn($parts['host']);
        $uri->expects($this->any())->method('getPort')->willReturn($parts['port']);
        $uri->expects($this->any())->method('getPath')->willReturn($parts['path']);
        $uri->expects($this->any())->method('getQuery')->willReturn($parts['query']);
        $uri->expects($this->any())->method('getFragment')->willReturn($parts['fragment']);
        $uri->expects($this->any())->method('getAuthority')->willReturn($userPass.$hostPort);
        $uri->expects($this->any())->method('getUserInfo')->willReturn(trim($userPass, '@'));

        return $uri;
    }

    public function setUp()
    {
        /** @var UriInterface $uri */
        $uri = $this->getMock(UriInterface::class);
        /** @var StreamInterface $body */
        $body = $this->getMock(StreamInterface::class);

        $this->defaultRequest = new Request('get', $uri, $body, new HeaderCollection(), '1.1');
    }

    public function test_default_constructor()
    {
        /** @var UriInterface $uri */
        $uri = $this->getMock(UriInterface::class);
        /** @var StreamInterface $body */
        $body = $this->getMock(StreamInterface::class);

        $request = new Request('get', $uri, $body);
        $this->assertEquals('get', $request->getMethod());
        $this->assertSame($uri, $request->getUri());
        $this->assertEquals('/', $request->getRequestTarget());
    }

    public function test_constructor()
    {
        /** @var UriInterface $uri */
        $uri = $this->getMock(UriInterface::class);
        /** @var StreamInterface $body */
        $body = $this->getMock(StreamInterface::class);

        $headers = ['X-Foobar' => ['Bar', 'Baz']];

        $request = new Request('Post', $uri, $body, new HeaderCollection($headers), '1.0');

        $this->assertSame($uri, $request->getUri());
        $this->assertEquals('Post', $request->getMethod());
        $this->assertEquals('1.0', $request->getProtocolVersion());
        $this->assertSame($body, $request->getBody());
        $this->assertEquals(['Bar', 'Baz'], $request->getHeader('X-Foobar'));
    }

    public function test_withMethod_returns_instance_with_new_method()
    {
        $new = $this->defaultRequest->withMethod('post');
        $this->assertNotSame($this->defaultRequest, $new);
        $this->assertEquals('post', $new->getMethod());
    }

    public function test_withRequestTarget_returns_instance_with_new_target()
    {
        $new = $this->defaultRequest->withRequestTarget('*');
        $this->assertNotSame($this->defaultRequest, $new);
        $this->assertEquals('*', $new->getRequestTarget());
    }

    public function test_withUri_returns_instance_with_new_uri()
    {
        /** @var UriInterface $uri */
        $uri = $this->getMock(UriInterface::class);

        $new = $this->defaultRequest->withUri($uri);
        $this->assertNotSame($this->defaultRequest, $new);
        $this->assertSame($uri, $new->getUri());
    }

    public function requestTargets()
    {
        return [
            ['http://example.com', '/'],
            ['http://example.com/foo', '/foo'],
            ['http://example.com/foo/bar', '/foo/bar'],
            ['http://example.com/foo/?bar=baz', '/foo/?bar=baz'],
            ['http://example.com/?bar=baz', '/?bar=baz'],
            ['http://example.com?bar=baz', '?bar=baz'],
        ];
    }

    /**
     * @dataProvider requestTargets
     */
    public function test_getRequestTarget_returns_correct_target($uri, $expectedTarget)
    {
        $new = $this->defaultRequest->withUri($this->buildUri($uri));
        $this->assertEquals($expectedTarget, $new->getRequestTarget());
    }

    public function test_has_host_when_uri_present()
    {
        /** @var StreamInterface $body */
        $body = $this->getMock(StreamInterface::class);

        $request = new Request('get', $this->buildUri(''), $body);
        $this->assertEquals([], $request->getHeaders());

        $request = new Request('get', $this->buildUri('http://example.com/foobar'), $body);
        $this->assertEquals(['Host' => ['example.com']], $request->getHeaders());

        $request = new Request('get', $this->buildUri('http://example.com:8080/foobar'), $body);
        $this->assertEquals(['Host' => ['example.com:8080']], $request->getHeaders());
    }

    public function test_withUri_handles_preservehost()
    {
        /** @var StreamInterface $body */
        $body = $this->getMock(StreamInterface::class);

        $request = new Request('get', $this->buildUri(''), $body);
        $request->withUri($this->buildUri('http://example.com/foobar'), false);
        $this->assertEquals(['Host' => ['example.com']], $request->getHeaders());

        $request = new Request('get', $this->buildUri(''), $body);
        $request->withUri($this->buildUri('http://example.com/foobar'), true);
        $this->assertEquals(['Host' => ['example.com']], $request->getHeaders());

        $request = new Request('get', $this->buildUri('http://example.com/foobar'), $body);
        $request->withUri($this->buildUri(''), false);
        $this->assertEquals([], $request->getHeaders());

        $request = new Request('get', $this->buildUri('http://example.com/foobar'), $body);
        $request->withUri($this->buildUri(''), true);
        $this->assertEquals(['Host' => ['example.com']], $request->getHeaders());

        $request = new Request('get', $this->buildUri('http://example.com/foobar'), $body);
        $request->withUri($this->buildUri('http://foobar.com'), true);
        $this->assertEquals(['Host' => ['example.com']], $request->getHeaders());

        $request = new Request('get', $this->buildUri('http://example.com/foobar'), $body);
        $request->withUri($this->buildUri('http://foobar.com'), false);
        $this->assertEquals(['Host' => ['foobar.com']], $request->getHeaders());
    }
}
