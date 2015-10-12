<?php

namespace BinSoul\Net\Http\Message\Part;

/**
 * Represents the HTTP status code of a response.
 */
class Status
{
    /**
     * Map of standard HTTP status code/reason phrases.
     *
     * @var string[]
     */
    private static $phrases = [
        // information
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        // success
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        // redirection
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        // client error
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        // server error
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    /** @var int */
    private $code;

    /**
     * Constructs an instance of this class.
     *
     * @param int $code
     */
    public function __construct($code)
    {
        $this->code = $this->filterStatus($code);
    }

    /**
     * Returns the code.
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Returns the phrase for the code.
     *
     * @return string
     */
    public function getPhrase()
    {
        return isset(self::$phrases[$this->code]) ? self::$phrases[$this->code] : '';
    }

    /**
     * @param int $code
     *
     * @return bool
     */
    public static function isValid($code)
    {
        return (int) $code >= 100 && (int) $code <= 599;
    }

    /**
     * Validates the given HTTP status code and returns an integer if correct.
     *
     * @param mixed $code code to validate
     *
     * @throws \InvalidArgumentException
     *
     * @return int
     */
    private function filterStatus($code)
    {
        if (!is_numeric($code)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Status code "%s" is not numeric.',
                    is_scalar($code) ? $code : gettype($code)
                )
            );
        }

        $result = (int) $code;
        if (!self::isValid($result)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid status code "%s".',
                    $result
                )
            );
        }

        return $result;
    }
}
