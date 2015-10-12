<?php

namespace BinSoul\Net\Http\Message;

use BinSoul\Net\Http\Message\Collection\HeaderCollection;
use BinSoul\Net\Http\Message\Part\Method;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Implements the PSR-7 RequestInterface.
 *
 * {@inheritdoc}
 */
class Request extends Message implements RequestInterface
{
    /** @var Method */
    protected $method;
    /** @var string */
    protected $target;
    /* @var UriInterface */
    protected $uri;

    /**
     * Constructs an instance of this class.
     *
     * @param string           $method   method of the request
     * @param UriInterface     $uri      request URI
     * @param StreamInterface  $body     body of the request
     * @param HeaderCollection $headers  headers of the request
     * @param string           $protocol HTTP protocol version of the request
     */
    public function __construct(
        $method,
        UriInterface $uri,
        StreamInterface $body,
        HeaderCollection $headers = null,
        $protocol = ''
    ) {
        $this->method = new Method($method);
        $this->uri = $uri;

        $headers = $headers !== null ? $headers : new HeaderCollection();
        if (!$headers->has('Host') && $this->uri->getHost() != '') {
            $headers->set('Host', $this->buildHost($this->uri));
        }

        parent::__construct($body, $headers, $protocol);
    }

    public function getRequestTarget()
    {
        if ($this->target != '') {
            return $this->target;
        }

        $target = $this->uri->getPath();
        if ($this->uri->getQuery() != '') {
            $target .= '?'.$this->uri->getQuery();
        }

        if ($target == '') {
            $target = '/';
        }

        return $target;
    }

    public function withRequestTarget($requestTarget)
    {
        $result = clone $this;
        $result->target = $requestTarget;

        return $result;
    }

    public function getMethod()
    {
        return $this->method->getName();
    }

    public function withMethod($method)
    {
        $result = clone $this;
        $result->method = new Method($method);

        return $result;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $result = clone $this;
        $result->uri = $uri;

        if ($preserveHost && $this->hasHeader('Host')) {
            return $result;
        }

        if ($uri->getHost() == '') {
            return $result->withoutHeader('Host');
        }

        return $result->withHeader('Host', $this->buildHost($uri));
    }

    /**
     * Returns the host header.
     *
     * @param UriInterface $uri
     *
     * @return string
     */
    private function buildHost(UriInterface $uri)
    {
        return $uri->getHost().($uri->getPort() !== null ? ':'.$uri->getPort() : '');
    }
}
