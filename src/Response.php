<?php

declare (strict_types = 1);

namespace BinSoul\Net\Http\Message;

use BinSoul\Net\Http\Message\Part\Status;
use BinSoul\Net\Http\Message\Collection\HeaderCollection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Implements the PSR-7 ResponseInterface.
 *
 * {@inheritdoc}
 */
class Response extends Message implements ResponseInterface
{
    /**
     * HTTP status code.
     *
     * @var Status
     */
    private $status;

    /**
     * HTTP reason phrase which overwrites the default phrase.
     *
     * @var string
     */
    private $phrase = '';

    /**
     * Constructs an instance of this class.
     *
     * @param StreamInterface       $body    body of the response
     * @param int                   $status  HTTP status code of the response
     * @param HeaderCollection|null $headers headers of the response
     */
    public function __construct(StreamInterface $body, int $status = 200, HeaderCollection $headers = null)
    {
        parent::__construct($body, $headers);

        $this->status = new Status($status);
    }

    public function getStatusCode()
    {
        return $this->status->getCode();
    }

    public function getReasonPhrase()
    {
        return $this->phrase == '' ? $this->status->getPhrase() : $this->phrase;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $result = clone $this;
        $result->status = new Status($code);
        $result->phrase = $reasonPhrase;

        return $result;
    }
}
