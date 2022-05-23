<?php declare(strict_types=1);

namespace Grifart\Enum\Internal;

use Grifart\Enum\Enum;
use Grifart\Enum\MissingValueDeclarationException;
use Grifart\Enum\ReflectionFailedException;
use Grifart\Enum\UsageException;

/**
 * @template TEnum of \Grifart\Enum\Enum
 * @phpstan-type TScalarValue int|string
 * @phpstan-type TConstantName string
 */
final class Meta
{
	/** @var class-string<TEnum> */
	private $class;

	/** @var array<TConstantName,TScalarValue> */
	private $constantToScalar;

	/** @var array<TScalarValue,TEnum> */
	private $scalarToValue;

	/**
	 * @param class-string<TEnum> $class
	 * @param array<TConstantName,TScalarValue> $constantToScalar
	 * @param TEnum[] $values
	 */
	private function __construct(string $class, array $constantToScalar, array $values)
	{
		$this->class = $class;
		$this->constantToScalar = $constantToScalar;
		$this->scalarToValue = $this->buildScalarToValueMapping($values); // requires constantToScalar to be already set!
	}

	/**
	 * @param Enum[] $values
	 * @phpstan-param TEnum[] $values
	 * @return array<TScalarValue,TEnum>
	 */
	private function buildScalarToValueMapping(array $values): array {
		$scalarToValues = [];

		// check type of all scalar values
		$keyType = null;
		foreach($values as $value) {
			$scalar = $value->toScalar();
			if ($keyType === NULL) {
				$keyType = \gettype($scalar);
			}
			if ($keyType !== \gettype($scalar)) {
				throw new UsageException('Mixed types of scalar value. Keys must either all string or all int.');
			}
		}

		foreach($values as $value) {
			$scalar = $value->toScalar();



			if (isset($scalarToValues[$scalar])) {
				throw new UsageException('You have provided duplicated scalar values.');
			}
			if(!$this->hasConstantForScalar($scalar)) {
				throw new UsageException("Provided instance contains scalar value '$scalar'. But no corresponding constant was found.");
			}
			$scalarToValues[$scalar] = $value;

		}
		return $scalarToValues;
	}

	/**
	 * @param class-string<TEnum> $class
	 * @param array<TConstantName,TScalarValue> $constantToScalar
	 * @param TEnum[] $values
	 * @return self<TEnum>
	 */
	public static function from(string $class, array $constantToScalar, array $values): self
	{
		return new self($class, $constantToScalar, $values);
	}

	/**
	 * @return class-string<TEnum>
	 */
	public function getClass(): string
	{
		return $this->class;
	}

	/**
	 * @return \ReflectionClass<TEnum>
	 */
	public function getClassReflection(): \ReflectionClass
	{
		try {
			return new \ReflectionClass($this->getClass());
		} catch (\ReflectionException $e) {
			throw new ReflectionFailedException($e);
		}
	}

	/**
	 * @return TConstantName[]
	 */
	public function getConstantNames(): array
	{
		return \array_keys($this->constantToScalar);
	}

	/**
	 * @return array<int, TScalarValue>
	 */
	public function getScalarValues(): array
	{
		return \array_values($this->constantToScalar);
	}

	/**
	 * @return TEnum[]
	 */
	public function getValues(): array
	{
		return \array_values($this->scalarToValue);
	}

	/**
	 * @param TConstantName $constantName
	 * @return ?TEnum
	 * @throws MissingValueDeclarationException
	 */
	public function getValueForConstantName($constantName): ?Enum
	{
		if(!isset($this->constantToScalar[$constantName])) {
			return NULL;
		}
		$scalar = $this->constantToScalar[$constantName];
		return $this->getValueForScalar($scalar);
	}

	/**
	 * @param TScalarValue $scalarValue
	 */
	public function hasValueForScalar($scalarValue): bool
	{
		return isset($this->scalarToValue[$scalarValue]);
	}

	/**
	 * @param TScalarValue $scalarValue
	 */
	public function getConstantNameForScalar($scalarValue): string
	{
		$result = \array_search($scalarValue, $this->constantToScalar, true);
		if ($result === false) {
			throw new UsageException("Could not find constant name for $scalarValue.");
		}
		return $result;
	}

	/**
	 * @return TScalarValue
	 */
	public function getScalarForValue(Enum $enum)
	{
		$result = \array_search($enum, $this->scalarToValue, true);
		if ($result === false) {
			throw new UsageException("Could not find scalar for given instance.");
		}
		return $result;
	}

	/**
	 * @param TScalarValue $scalar
	 * @return TEnum
	 * @throws MissingValueDeclarationException if there is no value for given scalar
	 */
	public function getValueForScalar($scalar): Enum
	{
		if (!isset($this->scalarToValue[$scalar])) {
			throw new MissingValueDeclarationException("There is no value for enum '{$this->class}' and scalar value '$scalar'.");
		}
		return $this->scalarToValue[$scalar];
	}

	/**
	 * @param TScalarValue $scalar
	 */
	private function hasConstantForScalar($scalar): bool
	{
		return \in_array($scalar, $this->constantToScalar, TRUE);
	}
}
