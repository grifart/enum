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

	abstract protected static function provideInstances(): array;

	/**
	 * @return string[]|int[]
	 */
	protected static function getPrimitiveValues(): array
	{ // todo: move this to Meta?
		return (new \ReflectionClass(static::class))->getConstants();
	}

	/** @return static */
	public static function fromScalar($scalar): Enum
	{
		return self::getMeta()->getValueForScalar($scalar);
	}

	private static function getMeta(): Meta
	{
		return InstanceRegister::get(
			static::class,
			function(): Meta {
				return Meta::from(
					static::class,
					static::getPrimitiveValues(),
					static::provideInstances()
				);
			}
		);
	}

	public static function __callStatic(string $constantName, array $arguments)
	{
		\assert(empty($arguments));

		return self::getMeta()->getValueForConstantName($constantName);

	}

	public function getScalarValue()
	{
		return self::getMeta()->getScalarForValue($this);
	}

	public function equals($that): bool
	{
		return $this === $that;
	}

	public function equalsScalarValue($otherScalarValue): bool
	{
		return self::getMeta()->getScalarForValue($this) === $otherScalarValue;
	}

}
