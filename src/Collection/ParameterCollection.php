<?php

declare (strict_types = 1);

namespace BinSoul\Net\Http\Message\Collection;

/**
 * Provides methods to handle a collection of request parameters.
 *
 * Parameters should be scalar values or arrays of scalar values.
 *
 * To simplify working with nested arrays all methods support a special parameter name syntax.
 *
 * Given a parameter named "foo"
 * <code>
 * [
 *     'bar' => [
 *         'key1' => 'value1',
 *         'key2' => 'value2',
 *     ],
 *     'qux' => 'test',
 * ]
 * </code>
 *
 * $collection->get('foo[bar]') will return:
 * <code>
 * [
 *     'key1' => 'value1',
 *     'key2' => 'value2',
 * ]
 * </code>
 *
 * and $collection->get('foo[bar][key1]') will return:
 * <code>
 * 'value1'
 * </code>
 */
class ParameterCollection
{
    /** @var mixed[] */
    protected $parameters;

    /**
     * Constructs an instance of this class.
     *
     * @param mixed[] $parameters initial parameter data
     */
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * Returns all values of all header lines as array indexed by parameter name.
     *
     * @return mixed[]
     */
    public function all(): array
    {
        return $this->parameters;
    }

    /**
     * Checks if a parameter exists.
     *
     * @param string $name name of the parameter
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        $parts = explode('[', $name);
        $lastKey = trim(array_pop($parts), ']');
        $current = &$this->parameters;
        foreach ($parts as $part) {
            $target = trim($part, ']');
            if (!is_array($current) || !array_key_exists($target, $current)) {
                return false;
            }

            $current = &$current[$target];
        }

        if (!array_key_exists($lastKey, $current)) {
            return false;
        }

        return true;
    }

    /**
     * Returns the value of a parameter.
     *
     * @param string $name    name of the parameter
     * @param mixed  $default return value if the parameter doesn't exist
     *
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        $parts = explode('[', $name);
        $lastKey = trim(array_pop($parts), ']');
        $current = &$this->parameters;
        foreach ($parts as $part) {
            $target = trim($part, ']');
            if (!is_array($current) || !array_key_exists($target, $current)) {
                return $default;
            }

            $current = &$current[$target];
        }

        if (!array_key_exists($lastKey, $current)) {
            return $default;
        }

        return $current[$lastKey];
    }

    /**
     * Sets the value of a parameter.
     *
     * @param string $name  name of the parameter
     * @param mixed  $value value of the parameter
     */
    public function set(string $name, $value)
    {
        $parts = explode('[', $name);
        $lastKey = trim(array_pop($parts), ']');
        $current = &$this->parameters;
        foreach ($parts as $part) {
            $target = trim($part, ']');
            if (!array_key_exists($target, $current)) {
                $current[$target] = [];
            }

            $current = &$current[$target];
        }

        $current[$lastKey] = $value;
    }

    /**
     * Removes a parameter.
     *
     * @param string $name name of the parameter
     */
    public function remove(string $name)
    {
        $parts = explode('[', $name);
        $lastKey = trim(array_pop($parts), ']');
        $current = &$this->parameters;
        foreach ($parts as $part) {
            $target = trim($part, ']');
            if (!array_key_exists($target, $current)) {
                return;
            }

            $current = &$current[$target];
        }

        unset($current[$lastKey]);
    }
}
