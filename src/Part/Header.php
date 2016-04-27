<?php

declare (strict_types = 1);

namespace BinSoul\Net\Http\Message\Part;

/**
 * Represents a single header line.
 */
class Header
{
    /**
     * Map of known headers.
     *
     * @var mixed[]
     */
    private static $headers = [
        'accept' => [
            'name' => 'Accept',
            'multiple' => true,
            'example' => 'text/plain',
        ],
        'accept-charset' => [
            'name' => 'Accept-Charset',
            'multiple' => true,
            'example' => 'utf-8',
        ],
        'accept-datetime' => [
            'name' => 'Accept-Datetime',
            'multiple' => false,
            'example' => 'Thu, 31 May 2007 20:35:00 GMT',
        ],
        'accept-encoding' => [
            'name' => 'Accept-Encoding',
            'multiple' => true,
            'example' => 'gzip, deflate',
        ],
        'accept-language' => [
            'name' => 'Accept-Language',
            'multiple' => true,
            'example' => 'en-US',
        ],
        'accept-patch' => [
            'name' => 'Accept-Patch',
            'multiple' => true,
            'example' => 'text/example;charset=utf-8',
        ],
        'accept-ranges' => [
            'name' => 'Accept-Ranges',
            'multiple' => false,
            'example' => 'bytes',
        ],
        'access-control-allow-headers' => [
            'name' => 'Access-Control-Allow-Headers',
            'multiple' => true,
            'example' => 'DNT,Pragma',
        ],
        'access-control-allow-methods' => [
            'name' => 'Access-Control-Allow-Methods',
            'multiple' => true,
            'example' => 'GET,POST',
        ],
        'access-control-allow-origin' => [
            'name' => 'Access-Control-Allow-Origin',
            'multiple' => false,
            'example' => '*',
        ],
        'age' => [
            'name' => 'Age',
            'multiple' => false,
            'example' => '12',
        ],
        'allow' => [
            'name' => 'Allow',
            'multiple' => true,
            'example' => 'GET, HEAD',
        ],
        'authorization' => [
            'name' => 'Authorization',
            'multiple' => false,
            'example' => 'Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==',
        ],
        'cache-control' => [
            'name' => 'Cache-Control',
            'multiple' => true,
            'example' => 'must-revalidate,max-age=3600',
        ],
        'connection' => [
            'name' => 'Connection',
            'multiple' => false,
            'example' => 'keep-alive',
        ],
        'content-disposition' => [
            'name' => 'Content-Disposition',
            'multiple' => false,
            'example' => 'attachment; filename="name.ext"',
        ],
        'content-encoding' => [
            'name' => 'Content-Encoding',
            'multiple' => false,
            'example' => 'gzip',
        ],
        'content-language' => [
            'name' => 'Content-Language',
            'multiple' => false,
            'example' => 'de',
        ],
        'content-length' => [
            'name' => 'Content-Length',
            'multiple' => false,
            'example' => '348',
        ],
        'content-location' => [
            'name' => 'Content-Location',
            'multiple' => false,
            'example' => '/index.html',
        ],
        'content-md5' => [
            'name' => 'Content-MD5',
            'multiple' => false,
            'example' => 'Q2hlY2sgSW50ZWdyaXR5IQ==',
        ],
        'content-range' => [
            'name' => 'Content-Range',
            'multiple' => false,
            'example' => 'bytes 21010-47021/47022',
        ],
        'content-type' => [
            'name' => 'Content-Type',
            'multiple' => false,
            'example' => 'application/x-www-form-urlencoded',
        ],
        'cookie' => [
            'name' => 'Cookie',
            'multiple' => false,
            'example' => '$Version=1; Skin=new;: standard',
        ],
        'date' => [
            'name' => 'Date',
            'multiple' => false,
            'example' => 'Tue, 15 Nov 1994 08:12:31 GMT',
        ],
        'dnt' => [
            'name' => 'DNT',
            'multiple' => false,
            'example' => '1',
        ],
        'etag' => [
            'name' => 'ETag',
            'multiple' => false,
            'example' => '"737060cd8c284d8af7ad3082f209582d"',
        ],
        'expect' => [
            'name' => 'Expect',
            'multiple' => false,
            'example' => '100-continue',
        ],
        'expires' => [
            'name' => 'Expires',
            'multiple' => false,
            'example' => 'Thu, 01 Dec 1994 16:00:00 GMT',
        ],
        'from' => [
            'name' => 'From',
            'multiple' => false,
            'example' => 'user@example.com',
        ],
        'front-end-https' => [
            'name' => 'Front-End-Https',
            'multiple' => false,
            'example' => 'on',
        ],
        'host' => [
            'name' => 'Host',
            'multiple' => false,
            'example' => 'en.wikipedia.org:80',
        ],
        'if-match' => [
            'name' => 'If-Match',
            'multiple' => false,
            'example' => '"737060cd8c284d8af7ad3082f209582d"',
        ],
        'if-modified-since' => [
            'name' => 'If-Modified-Since',
            'multiple' => false,
            'example' => 'Sat, 29 Oct 1994 19:43:31 GMT',
        ],
        'if-none-match' => [
            'name' => 'If-None-Match',
            'multiple' => false,
            'example' => '"737060cd8c284d8af7ad3082f209582d"',
        ],
        'if-range' => [
            'name' => 'If-Range',
            'multiple' => false,
            'example' => '"737060cd8c284d8af7ad3082f209582d"',
        ],
        'if-unmodified-since' => [
            'name' => 'If-Unmodified-Since',
            'multiple' => false,
            'example' => 'Sat, 29 Oct 1994 19:43:31 GMT',
        ],
        'last-modified' => [
            'name' => 'Last-Modified',
            'multiple' => false,
            'example' => 'Tue, 15 Nov 1994 12:45:26 GMT',
        ],
        'link' => [
            'name' => 'Link',
            'multiple' => false,
            'example' => 'Link: ; rel="alternate"',
        ],
        'location' => [
            'name' => 'Location',
            'multiple' => false,
            'example' => 'http://www.w3.org/pub/WWW/People.html',
        ],
        'max-forwards' => [
            'name' => 'Max-Forwards',
            'multiple' => false,
            'example' => '10',
        ],
        'origin' => [
            'name' => 'Origin',
            'multiple' => false,
            'example' => 'http://www.example-social-network.com',
        ],
        'p3p' => [
            'name' => 'P3P',
            'multiple' => false,
            'example' => 'CP="P3P policy!"',
        ],
        'pragma' => [
            'name' => 'Pragma',
            'multiple' => false,
            'example' => 'no-cache',
        ],
        'proxy-authenticate' => [
            'name' => 'Proxy-Authenticate',
            'multiple' => false,
            'example' => 'Basic',
        ],
        'proxy-authorization' => [
            'name' => 'Proxy-Authorization',
            'multiple' => false,
            'example' => 'Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==',
        ],
        'public-key-pins' => [
            'name' => 'Public-Key-Pins',
            'multiple' => false,
            'example' => 'max-age=2592000; pin-sha256="E9CZ9INDbd+2eRQozYqqbQ2yXLVKB9+xcprMF+44U1g=";',
        ],
        'range' => [
            'name' => 'Range',
            'multiple' => false,
            'example' => 'bytes=500-999',
        ],
        'referer' => [
            'name' => 'Referer',
            'multiple' => false,
            'example' => 'http://en.wikipedia.org/wiki/Main_Page',
        ],
        'refresh' => [
            'name' => 'Refresh',
            'multiple' => false,
            'example' => '5; url=http://www.w3.org/pub/WWW/People.html',
        ],
        'retry-after' => [
            'name' => 'Retry-After',
            'multiple' => false,
            'example' => 'Fri, 07 Nov 2014 23:59:59 GMT',
        ],
        'server' => [
            'name' => 'Server',
            'multiple' => false,
            'example' => 'Apache/2.4.1 (Unix)',
        ],
        'set-cookie' => [
            'name' => 'Set-Cookie',
            'multiple' => false,
            'example' => 'UserID=JohnDoe; Max-Age=3600; Version=1',
        ],
        'strict-transport-security' => [
            'name' => 'Strict-Transport-Security',
            'multiple' => false,
            'example' => 'max-age=16070400; includeSubDomains',
        ],
        'te' => [
            'name' => 'TE',
            'multiple' => true,
            'example' => 'trailers, deflate',
        ],
        'trailer' => [
            'name' => 'Trailer',
            'multiple' => true,
            'example' => 'Max-Forwards',
        ],
        'transfer-encoding' => [
            'name' => 'Transfer-Encoding',
            'multiple' => false,
            'example' => 'chunked',
        ],
        'upgrade' => [
            'name' => 'Upgrade',
            'multiple' => true,
            'example' => 'HTTP/2.0, SHTTP/1.3, IRC/6.9, RTA/x11',
        ],
        'user-agent' => [
            'name' => 'User-Agent',
            'multiple' => false,
            'example' => 'Mozilla/5.0 (X11; Linux x86_64) Firefox/21.0',
        ],
        'vary' => [
            'name' => 'Vary',
            'multiple' => true,
            'example' => 'Accept-Encoding,User-Agent',
        ],
        'via' => [
            'name' => 'Via',
            'multiple' => true,
            'example' => '1.0 fred, 1.1 example.com (Apache/1.1)',
        ],
        'warning' => [
            'name' => 'Warning',
            'multiple' => false,
            'example' => '199 Miscellaneous warning',
        ],
        'www-authenticate' => [
            'name' => 'WWW-Authenticate',
            'multiple' => false,
            'example' => 'Basic',
        ],
        'x-forwarded-for' => [
            'name' => 'X-Forwarded-For',
            'multiple' => true,
            'example' => 'client1, proxy1, proxy2',
        ],
        'x-forwarded-host' => [
            'name' => 'X-Forwarded-Host',
            'multiple' => false,
            'example' => 'en.wikipedia.org:80',
        ],
        'x-forwarded-proto' => [
            'name' => 'X-Forwarded-Proto',
            'multiple' => false,
            'example' => 'https',
        ],
        'x-http-method-override' => [
            'name' => 'X-Http-Method-Override',
            'multiple' => false,
            'example' => 'DELETE',
        ],
        'x-requested-with' => [
            'name' => 'X-Requested-With',
            'multiple' => false,
            'example' => 'XMLHttpRequest',
        ],
    ];

    /** @var string */
    private $name;

    /** @var string[] */
    private $values;

    /**
     * Constructs an instance of this class.
     *
     * @param string          $name
     * @param string|string[] $value
     */
    public function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->values = $this->prepareValues($value);
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the values as a comma separated string.
     *
     * @return string
     */
    public function getValuesAsString(): string
    {
        return implode(',', $this->values);
    }

    /**
     * Returns the values as an array.
     *
     * @return string[]
     */
    public function getValuesAsArray(): array
    {
        return $this->values;
    }

    /**
     * Adds the given values to the internal list.
     *
     * @param string|string[] $value
     */
    public function addValue($value)
    {
        $this->values = array_values(
            array_unique(
                array_merge($this->values, $this->prepareValues($value))
            )
        );
    }

    /**
     * Returns the registered name of a header.
     *
     * @param string $name
     *
     * @return string
     */
    public static function getRegisteredName(string $name): string
    {
        $key = strtolower($name);

        return isset(self::$headers[$key]) ? self::$headers[$key]['name'] : $name;
    }

    /**
     * Checks if the header can contain multiple values separated by comma.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function hasMultipleValues(string $name): bool
    {
        $key = strtolower($name);

        return isset(self::$headers[$key]) ? self::$headers[$key]['multiple'] : true;
    }

    /**
     * Splits comma-separated strings into an array and removes whitespaces and empty values.
     *
     * @param string|string[] $value
     *
     * @return string[]
     */
    private function prepareValues($value): array
    {
        if (!self::hasMultipleValues($this->name)) {
            $values = is_array($value) ? $value : [trim($value)];
        } elseif (!is_array($value)) {
            $values = explode(',', (string) $value);
        } else {
            $values = $value;
        }

        foreach (array_keys($values) as $key) {
            $values[$key] = trim($values[$key]);

            if ($values[$key] == '') {
                unset($values[$key]);
            }
        }

        return array_values(array_unique($values));
    }
}
