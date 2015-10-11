<?php

namespace BinSoul\Net\Http\Message\Collection;

/**
 * Provides methods to handle a collection of request headers.
 */
class HeaderCollection
{
    /**
     * List of headers which are known to be single values.
     *
     * @var string[]
     */
    private static $knownSingleValues = [
        'user-agent',
    ];

    /** @var string[] */
    protected $headers = [];

    /**
     * Constructs an instance of this class.
     *
     * @param string[]|string[][] $headers initial header lines
     */
    public function __construct(array $headers = [])
    {
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }

    /**
     * Returns all values of all headers as array indexed by header name.
     *
     * @return string[][]
     */
    public function all()
    {
        $result = [];
        foreach ($this->headers as $header) {
            $result[$header['name']] = $header['data'];
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
    public function has($name)
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
    public function get($name, $default = null)
    {
        $name = strtolower($name);
        if (!array_key_exists($name, $this->headers)) {
            return $default;
        }

        return implode(',', $this->headers[$name]['data']);
    }

    /**
     * Returns the value of a header as an array.
     *
     * @param string  $name    name of the header
     * @param mixed[] $default return value if the header doesn't exist
     *
     * @return mixed[]
     */
    public function getValues($name, array $default = [])
    {
        $name = strtolower($name);
        if (!array_key_exists($name, $this->headers)) {
            return $default;
        }

        return $this->headers[$name]['data'];
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
    public function set($name, $value, $replace = true)
    {
        $key = strtolower($name);

        if (in_array($key, self::$knownSingleValues)) {
            $values = [$value];
        } else {
            $values = $this->prepareValues($value);
        }

        if ($replace == true || !isset($this->headers[$key])) {
            $this->headers[$key] = [
                'name' => $name,
                'data' => array_values(array_unique($values)),
            ];
        } else {
            $this->headers[$key]['data'] = array_values(
                array_unique(
                    array_merge($this->headers[$key]['data'], $values)
                )
            );
        }
    }

    /**
     * Removes a header.
     *
     * @param string $name case-insensitive name of the header
     */
    public function remove($name)
    {
        $key = strtolower($name);
        unset($this->headers[$key]);
    }

    /**
     * Splits comma-separated strings into an array and removes whitespaces and empty values.
     *
     * @param string|string[] $value
     *
     * @return string[]
     */
    private function prepareValues($value)
    {
        $values = $value;
        if (!is_array($value)) {
            $values = explode(',', (string) $value);
        }

        foreach (array_keys($values) as $key) {
            $values[$key] = trim($values[$key]);

            if ($values[$key] == '') {
                unset($values[$key]);
            }
        }

        return $values;
    }
}
