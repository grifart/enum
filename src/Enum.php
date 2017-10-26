<?php declare(strict_types=1);

namespace Grifart\Enum;

abstract class Enum
{

	protected function __construct() {}

	abstract public static function provideInstances(): array;

	public static function getValues(): array
	{
		$classReflection = new \ReflectionClass(static::class);

		// todo: are really all valid values?
		return $classReflection->getConstants();
	}

	public static function getValue(string $key): string
	{
		// todo: error handling
		return static::getValues()[$key];
	}

	/** @var array|null */
	protected static $instances;

	public static function __callStatic(string $name, array $arguments)
	{
		\assert(empty($arguments));

		$value = static::getValue($name);
		if (static::$instances === NULL) {
			static::$instances = static::provideInstances();
		}
		assert(isset(static::$instances[$value])); // FIXME
		assert(static::$instances[$value] instanceof static);
		return static::$instances[$value];
	}
}
