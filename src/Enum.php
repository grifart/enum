<?php declare(strict_types=1);

namespace Grifart\Enum;

/**
 * Enum
 *
 * Three concepts:
 * - constant name = used to refer to enum value
 * - value = the enum instance
 * - scalar value = scalar value of enum, must be unique, used for serialization
 */
abstract class Enum
{

	protected function __construct() {}

	abstract public static function provideInstances(): array;

	/**
	 * @return string[]|int[]
	 */
	private static function getPrimitiveValues(): array
	{
		return (new \ReflectionClass(static::class))->getConstants();
	}

	public static function __callStatic(string $constantName, array $arguments)
	{
		\assert(empty($arguments));

		return InstanceRegister::get(
				static::class,
				function(): Meta {
					return Meta::from(
						static::class,
						static::getPrimitiveValues(),
						static::provideInstances()
					);
				}
			)->getValueForConstantName($constantName);

	}

}
