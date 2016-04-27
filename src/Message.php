<?php

declare (strict_types = 1);

namespace BinSoul\Net\Http\Message;

use BinSoul\Net\Http\Message\Collection\HeaderCollection;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Implements the PSR-7 MessageInterface.
 *
 * {@inheritdoc}
 */
class Message implements MessageInterface
{
    /** @var string */
    protected $protocol;
    /** @var HeaderCollection */
    protected $headers;
    /** @var StreamInterface */
    protected $body;

    /**
     * Constructs an instance of this class.
     *
     * @param StreamInterface  $body     body of the message
     * @param HeaderCollection $headers  headers of the message
     * @param string           $protocol HTTP protocol version of the message
     */
    public function __construct(StreamInterface $body, HeaderCollection $headers = null, string $protocol = '1.1')
    {
        $this->protocol = $protocol;
        $this->body = $body;
        $this->headers = $headers !== null ? $headers : new HeaderCollection();
    }

    public function getProtocolVersion()
    {
        return $this->protocol;
    }

    public function withProtocolVersion($version)
    {
        $result = clone $this;
        $result->protocol = $version;

        return $result;
    }

    public function getHeaders()
    {
        return $this->headers->all();
    }

    public function hasHeader($name)
    {
        return $this->headers->has($name);
    }

    public function getHeader($name)
    {
        return $this->headers->getValues($name);
    }

    public function getHeaderLine($name)
    {
        $value = $this->getHeader($name);
        if (count($value) == 0) {
            return '';
        }

        return implode(',', $value);
    }

    public function withHeader($name, $value)
    {
        $result = clone $this;
        $result->headers->set($name, $value, true);

        return $result;
    }

    public function withAddedHeader($name, $value)
    {
        if (!$this->hasHeader($name)) {
            return $this->withHeader($name, $value);
        }

        $result = clone $this;
        $result->headers->set($name, $value, false);

        return $result;
    }

    public function withoutHeader($name)
    {
        if (!$this->hasHeader($name)) {
            return clone $this;
        }

        $result = clone $this;
        $result->headers->remove($name);

        return $result;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        $result = clone $this;
        $result->body = $body;

        return $result;
    }
}
