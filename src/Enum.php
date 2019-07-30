<?php

namespace Rcrdortiz\Enum;

use Rcrdortiz\Enum\Exception\IllegalArgumentException;
use Rcrdortiz\Enum\Exception\InvalidEnumConstantDefaultValue;
use ReflectionClass;
use ReflectionException;

abstract class Enum
{
    /**
     * @var array
     */
    private static $constantsCache = [];

    /**
     * @var string
     */
    private $type;

    /**
     * @var mixed
     */
    private $value;

    /**
     * Enum constructor.
     *
     * @param $type
     * @param $value
     */
    final private function __construct(string $type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return string
     */
    final public function __toString(): string
    {
        return $this->type();
    }

    /**
     * @return mixed
     */
    final public function let()
    {
        return $this->value ?? $this->type;
    }

    /**
     * @return string
     */
    final public function type(): string
    {
        return $this->type;
    }

    /**
     * @param $type
     * @param $arguments
     *
     * @return static
     * @throws IllegalArgumentException
     * @throws InvalidEnumConstantDefaultValue
     */
    final public static function __callStatic(string $type, $arguments): self
    {
        try {
            $constants = self::getConstants();
            if (array_key_exists($type, $constants)) {
                $assignedValue = $arguments[0] ?? $constants[$type];

                return new static($constants[$type], $assignedValue);
            }
        } catch (ReflectionException $e) {
        }

        throw new IllegalArgumentException($type);
    }

    /**
     * @param string $type
     *
     * @param self $default
     * @return static
     * @throws InvalidEnumConstantDefaultValue
     * @throws ReflectionException
     */
    final public static function valueOf(?string $type, self $default = null): self
    {
        return self::valueOfWithData($type, null, $default);
    }

    /**
     * @param string $type
     *
     * @param null $data
     * @param static $default
     * @return static
     * @throws InvalidEnumConstantDefaultValue
     * @throws ReflectionException
     */
    final public static function valueOfWithData(?string $type, $data = null, self $default = null): self
    {
        $constants = self::getConstants();
        $instanceType = array_search($type, $constants);

        if (!$instanceType && $default) {
            $instanceType = array_search($default->type(), $constants);
        }

        return static::{$instanceType ?: $type}($data);
    }

    /**
     * @return array
     * @throws ReflectionException
     * @throws InvalidEnumConstantDefaultValue
     */
    final public static function values(): array
    {
        $values = [];
        $constants = self::getConstants();
        foreach ($constants as $constant => $value) {
            $values[] = static::{$constant}();
        }

        return $values;
    }

    /**
     * @return array
     * @throws ReflectionException
     * @throws InvalidEnumConstantDefaultValue
     */
    final private static function getConstants()
    {
        $key = static::class;
        if (!isset(self::$constantsCache[$key])) {
            $constants = (new ReflectionClass($key))->getConstants();
            self::checkConstantsDefaultValuesAreStrings($constants);
            self::$constantsCache[$key] = $constants;
        }

        return self::$constantsCache[$key];
    }

    /**
     * @param array $constants
     *
     * @throws InvalidEnumConstantDefaultValue
     */
    final private static function checkConstantsDefaultValuesAreStrings(array $constants)
    {
        foreach ($constants as $constant => $value) {
            if (!is_string($value)) {
                throw new InvalidEnumConstantDefaultValue($constant, static::class);
            }
        }
    }
}