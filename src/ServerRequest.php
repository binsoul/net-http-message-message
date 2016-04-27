<?php

declare (strict_types = 1);

namespace BinSoul\Net\Http\Message;

use BinSoul\Net\Http\Message\Collection\HeaderCollection;
use BinSoul\Net\Http\Message\Collection\ParameterCollection;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Implements the PSR-7 ServerRequestInterface.
 *
 * {@inheritdoc}
 */
class ServerRequest extends Request implements ServerRequestInterface
{
    /** @var ParameterCollection */
    protected $attributes;
    /** @var ParameterCollection */
    protected $cookies;
    /** @var ParameterCollection */
    protected $query;
    /** @var ParameterCollection */
    protected $post;
    /** @var ParameterCollection */
    protected $server;
    /** @var UploadedFile[] */
    protected $files;

    /**
     * Constructs an instance of this class.
     *
     * @param string              $method   method of the request
     * @param UriInterface        $uri      URI of the request
     * @param StreamInterface     $body     body of the request
     * @param HeaderCollection    $headers  headers of the request
     * @param ParameterCollection $cookies  cookies of the request
     * @param ParameterCollection $query    query parameters of the request
     * @param ParameterCollection $post     post parameters of the request
     * @param ParameterCollection $server   server parameters of the request
     * @param array               $files    uploaded files for the request
     * @param string              $protocol HTTP protocol version of the request
     */
    public function __construct(
        string $method,
        UriInterface $uri,
        StreamInterface $body,
        HeaderCollection $headers = null,
        ParameterCollection $cookies = null,
        ParameterCollection $query = null,
        ParameterCollection $post = null,
        ParameterCollection $server = null,
        array $files = [],
        string $protocol = ''
    ) {
        parent::__construct($method, $uri, $body, $headers, $protocol);

        $this->attributes = new ParameterCollection();
        $this->cookies = $cookies !== null ? $cookies : new ParameterCollection();
        $this->query = $query !== null ? $query : new ParameterCollection();
        $this->post = $post !== null ? $post : new ParameterCollection();
        $this->server = $server !== null ? $server : new ParameterCollection();
        $this->files = $files;
    }

    public function getServerParams()
    {
        return $this->server->all();
    }

    public function getUploadedFiles()
    {
        return $this->files;
    }

    public function withUploadedFiles(array $files)
    {
        $result = clone $this;
        $result->files = $files;

        return $result;
    }

    public function getCookieParams()
    {
        return $this->cookies->all();
    }

    public function withCookieParams(array $cookies)
    {
        $result = clone $this;
        $result->cookies = new ParameterCollection($cookies);

        return $result;
    }

    public function getQueryParams()
    {
        return $this->query->all();
    }

    public function withQueryParams(array $query)
    {
        $result = clone $this;
        $result->query = new ParameterCollection($query);

        return $result;
    }

    public function getParsedBody()
    {
        return $this->post->all();
    }

    public function withParsedBody($data)
    {
        $result = clone $this;
        $result->post = is_array($data) ? new ParameterCollection($data) : $data;

        return $result;
    }

    public function getAttributes()
    {
        return $this->attributes->all();
    }

    public function getAttribute($name, $default = null)
    {
        return $this->attributes->get($name, $default);
    }

    public function withAttribute($name, $value)
    {
        $result = clone $this;
        $result->attributes->set($name, $value);

        return $result;
    }

    public function withoutAttribute($name)
    {
        $result = clone $this;
        $result->attributes->remove($name);

        return $result;
    }

    /**
     * Returns a new instance with the specified attributes.
     *
     * @param mixed $attributes attributes of the new instance
     *
     * @return self
     */
    public function withAttributes(array $attributes): self
    {
        $result = clone $this;
        $result->attributes = new ParameterCollection($attributes);

        return $result;
    }

    public function getMethod()
    {
        $method = parent::getMethod();

        return $method == '' ? 'GET' : strtoupper($method);
    }

    public function withMethod($method)
    {
        return parent::withMethod(strtoupper($method));
    }
}
