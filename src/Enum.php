<?php declare(strict_types=1);

namespace Grifart\Enum;

use Grifart\Enum\Internal\InstanceRegister;
use Grifart\Enum\Internal\Meta;
use PHPStan\Reflection\ClassReflection;

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
	protected function __construct()
	{
	}

	/**
	 * Provide values for given enum.
	 * @return array
	 */
	abstract protected static function provideInstances(): array;

	/**
	 * @return string[]|int[]
	 */
	protected static function getConstantToScalar(): array
	{ // todo: move this to Meta?
		return (new \ReflectionClass(static::class))->getConstants();
	}

	/**
	 * Builds enumeration from its scalar value.
	 * @return static
	 */
	public static function fromScalar($scalar): Enum
	{
		return self::getMeta()->getValueForScalar($scalar);
	}

	/**
	 * Provides access to values using ::CONSTANT_NAME() interface.
	 * @return static
	 */
	public static function __callStatic(string $constantName, array $arguments): Enum
	{
		\assert(empty($arguments));

		return self::getMeta()->getValueForConstantName($constantName);
	}

	private static function getMeta(): Meta
	{
		return InstanceRegister::get(
			static::getEnumClassName(),
			function (): Meta {
				return Meta::from(
					static::getEnumClassName(),
					static::getConstantToScalar(),
					static::provideInstances()
				);
			}
		);
	}

	private static function getEnumClassName(): string
	{
		$ref = new \ReflectionClass(static::class);
		if ($ref->isAnonymous()) { // anonymous objects are used for values
			$ref = $ref->getMethod('provideInstances')->getDeclaringClass();
		}

		return $ref->getName();
	}

	/**
	 * Provides scalar representation of enum value.
	 * @return int|string
	 */
	public function getScalarValue()
	{
		return self::getMeta()->getScalarForValue($this);
	}

	/**
	 * Retrieves constant name that is used to access enum value.
	 *
	 * Note: do not depend on this values, as it can change anytime. This value can be
	 * subject of refactorings of user-defined enums.
	 */
	public function getConstantName(): string
	{
		return self::getMeta()->getConstantNameForScalar(
			self::getMeta()->getScalarForValue($this)
		);
	}

	/**
	 * @param mixed $that the other object we are comparing to
	 * @return bool if current value equals to the other value
	 * If value is non-enum value, returns false (as they are also not equal).
	 */
	public function equals($that): bool
	{
		return $this === $that;
	}

	/**
	 * @param int|string $theOtherScalarValue
	 * @return bool true if current scalar representation of value equals to given scalar value
	 */
	public function equalsScalarValue($theOtherScalarValue): bool
	{
		return self::getMeta()->getScalarForValue($this) === $theOtherScalarValue;
	}
}
