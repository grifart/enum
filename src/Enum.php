<?php declare(strict_types=1);

namespace Grifart\Enum;

use Grifart\Enum\Internal\InstanceRegister;
use Grifart\Enum\Internal\Meta;

/**
 * Enumeration class with support for strong-typing support and behaviour-rich values.
 *
 * Three basic concepts:
 * - **value**    = the enum instance
 * - **scalar**   = scalar identifier of enum value; typically used in persistence layer to refer to particular value
 * - **constant** = each value has associated class constant, which is used to refer to value from code.
 *      Constant name is used to generate static method for each of them. Constants are therefore usually not public.
 */
abstract class Enum
{

	/**
	 * Provide values for given enum, never call this method directly.
	 * @return self[]
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
	 * @return array<string,string|int>
	 */
	protected static function getConstantToScalar(): array
	{
		try {
			return (new \ReflectionClass(static::class))->getConstants();
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

		$value = self::getMeta(FALSE)->getValueForConstantName($constantName);
		if($value === NULL) {
			throw new \Error('Call to undefined method ' . static::class . '::' . $constantName . '(). Please check that you have provided constant, annotation and value.');
		}
		return $value;
	}

	private static function getMeta(bool $checkIfAccessingRootDirectly = true): Meta
	{
		$rootClass = static::getRootClass();
		if ($checkIfAccessingRootDirectly && $rootClass !== static::class) {
			throw new UsageException(
				'You have accessed static enum method on non-root class '
				. "('$rootClass' is a root class)"
			);
		}

		return InstanceRegister::get(
			$rootClass,
			function () use ($rootClass): Meta {
				return Meta::from(
					$rootClass,
					static::getConstantToScalar(),
					static::provideInstances()
				);
			}
		);
	}

	private static function getRootClass(): string
	{
		try {
			return (new \ReflectionClass(static::class))
				->getMethod('provideInstances')
				->getDeclaringClass()
				->getName();

		} catch (\ReflectionException $e) {
			throw new ReflectionFailedException($e);
		}
	}



	// -------- INSTANCE IMPLEMENTATION ---------

	/** @var int|string */
	private $scalarValue;

	/**
	 * @param int|string $scalarValue
	 */
	protected function __construct($scalarValue)
	{
		$this->scalarValue = $scalarValue;
	}

	/**
	 * Returns scalar representation of enum value.
	 * @return int|string
	 */
	public function toScalar()
	{
		return $this->scalarValue;
	}

	public function __toString(): string
	{
		// as enum does not allow mixed key types (all must be int or all string),
		// we can safely convert integers to strings without worrying introducing
		// value conflicts
		return (string) $this->toScalar();
	}

	/**
	 * Retrieves constant name that is used to access enum value.
	 *
	 * @internal Do not depend on this values, as it can change anytime. This value can be
	 * subject of refactorings of user-defined enums.
	 */
	public function getConstantName(): string
	{
		return $this::getMeta(FALSE)->getConstantNameForScalar(
			$this->toScalar()
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
	public function scalarEquals($theOtherScalarValue): bool
	{
		return $this->toScalar() === $theOtherScalarValue;
	}
}
