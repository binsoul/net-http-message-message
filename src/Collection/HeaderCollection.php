<?php

declare (strict_types = 1);

namespace BinSoul\Net\Http\Message\Collection;

use BinSoul\Net\Http\Message\Part\Header;

/**
 * Provides methods to handle a collection of request headers.
 */
class HeaderCollection
{
    /** @var Header[] */
    protected $headers = [];

    /**
     * Constructs an instance of this class.
     *
     * @param string[]|string[][] $headers initial header lines
     */
    public function __construct(array $headers = [])
    {
        foreach ($headers as $key => $values) {
            $this->set((string) $key, $values);
        }
    }

    /**
     * Returns all values of all headers as array indexed by header name.
     *
     * @return string[][]
     */
    public function all(): array
    {
        $result = [];
        foreach ($this->headers as $header) {
            $result[$header->getName()] = $header->getValuesAsArray();
        }

        return $result;
    }

    /**
     * Checks if a header exists name.
     *
     * @param string $name case-insensitive name of the header
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return array_key_exists(strtolower($name), $this->headers);
    }

    /**
     * Returns the value of a header as string.
     *
     * Multiple values are separated by comma.
     *
     * @param string $name    case-insensitive name of the header
     * @param mixed  $default return value if the header doesn't exist
     *
     * @return string
     */
    public function get(string $name, $default = null)
    {
        $key = strtolower($name);
        if (!array_key_exists($key, $this->headers)) {
            return $default;
        }

        return $this->headers[$key]->getValuesAsString();
    }

    /**
     * Returns the value of a header as an array.
     *
     * @param string  $name    name of the header
     * @param mixed[] $default return value if the header doesn't exist
     *
     * @return mixed[]
     */
    public function getValues(string $name, array $default = []): array
    {
        $key = strtolower($name);
        if (!array_key_exists($key, $this->headers)) {
            return $default;
        }

        return $this->headers[$key]->getValuesAsArray();
    }

    /**
     * Sets the value of a header.
     *
     * Values given as string are split into an array of values. The separator for multiple values is comma.
     *
     * @param string         $name    case-insensitive name of the header
     * @param string|mixed[] $value   value of the header
     * @param bool           $replace replace the value or append to exiting values
     */
    public function set(string $name, $value, bool $replace = true)
    {
        $key = strtolower($name);
        if ($replace || !array_key_exists($key, $this->headers)) {
            $this->headers[$key] = new Header($name, $value);
        } else {
            $this->headers[$key]->addValue($value);
        }
    }

    /**
     * Removes a header.
     *
     * @param string $name case-insensitive name of the header
     */
    public function remove(string $name)
    {
        $key = strtolower($name);
        unset($this->headers[$key]);
    }
}
