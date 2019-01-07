<?php declare(strict_types=1);

namespace Grifart\Enum;

use Grifart\Enum\Internal\InstanceRegister;
use Grifart\Enum\Internal\Meta;

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
	 * Provide values for given enum, never call this method directly.
	 * @return static[]
	 */
	abstract protected static function provideInstances(): array;

	/**
	 * @return static[] Order and array keys are not guaranteed.
	 * For further value introspection use returned enum instances.
	 */
	final public static function getAvailableValues(): array
	{
		return self::getMeta()->getValues();
	}

	/**
	 * @return string[]|int[]
	 */
	protected static function getConstantToScalar(): array
	{
		try {
			return (new \ReflectionClass(static::class))
				->getConstants();
		} catch (\ReflectionException $e) {
			throw new ReflectionFailedException($e);
		}
	}

	/**
	 * Builds enumeration from its scalar value.
	 * @param string|int $scalar
	 * @return static
	 * @throws MissingValueDeclarationException if there is no value for given scalar
	 */
	public static function fromScalar($scalar): Enum
	{
		return self::getMeta()->getValueForScalar($scalar);
	}

	/**
	 * Provides access to values using ::CONSTANT_NAME() interface.
	 * @return static
	 * @throws MissingValueDeclarationException
	 */
	public static function __callStatic(string $constantName, array $arguments): Enum
	{
		\assert(\count($arguments) === 0);

		$value = self::getMeta()->getValueForConstantName($constantName);
		if($value === NULL) {
			throw new \Error('Call to undefined method ' . static::class . '::' . $constantName . '(). Please check that you have provided constant, annotation and value.');
		}
		return $value;
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
		try {
			$ref = new \ReflectionClass(static::class);
			if ($ref->isAnonymous()) { // anonymous objects are used for values
				$ref = $ref->getMethod('provideInstances')->getDeclaringClass();
			}
		} catch (\ReflectionException $e) {
			throw new ReflectionFailedException($e);
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
	 * @param Enum $that the other object we are comparing to
	 * @return bool if current value equals to the other value
	 * If value is non-enum value, returns false (as they are also not equal).
	 */
	public function equals(Enum $that): bool
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
