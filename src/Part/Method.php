<?php

declare (strict_types = 1);

namespace BinSoul\Net\Http\Message\Part;

/**
 * Represents the HTTP method of a request.
 */
class Method
{
    /**
     * The GET method requests a representation of the specified resource.
     * Requests using GET should only retrieve data and should have no other effect.
     */
    const GET = 'GET';
    /**
     * The HEAD method asks for a response identical to that of a GET request, but without the response body.
     */
    const HEAD = 'HEAD';

    /**
     * The POST method requests that the server accept the entity enclosed in the request as a new subordinate
     * of the web resource identified by the URI.
     */
    const POST = 'POST';

    /**
     * The PUT method requests that the enclosed entity be stored under the supplied URI.
     */
    const PUT = 'PUT';

    /**
     * The PATCH method applies partial modifications to a resource.
     */
    const PATCH = 'PATCH';

    /**
     * The DELETE method deletes the specified resource.
     */
    const DELETE = 'DELETE';

    /**
     * The OPTIONS method returns the HTTP methods that the server supports for the specified URL.
     */
    const OPTIONS = 'OPTIONS';

    /**
     * The TRACE method echoes the received request so that a client can see what (if any) changes or additions
     * have been made by intermediate servers.
     */
    const TRACE = 'TRACE';

    /**
     * The CONNECT method converts the request connection to a transparent TCP/IP tunnel.
     */
    const CONNECT = 'CONNECT';

    /** @var string */
    private $name;

    /**
     * Constructs an instance of this class.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Returns the name of the method.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
