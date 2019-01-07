<?php declare(strict_types=1);

namespace Grifart\Enum\Internal;

use Grifart\Enum\Enum;
use Grifart\Enum\MissingValueDeclarationException;

final class Meta
{
	/** @var string */
	private $class;

	/** @var array<string,int|string> */
	private $constantToScalar;

	/** @var array<int|string,Enum> */
	private $scalarToValue;

	/**
	 * @param string $class
	 * @param array<string,string|int> $constantToScalar
	 * @param Enum[] $values
	 */
	private function __construct(string $class, array $constantToScalar, array $values)
	{
		$this->class = $class;
		$this->constantToScalar = $constantToScalar;
		$this->scalarToValue = $this->buildScalarToValueMapping($values); // requires constantToScalar to be already set!
	}

	/**
	 * @param Enum[] $values
	 * @return array<string|int,Enum>
	 */
	private function buildScalarToValueMapping(array $values): array {
		$scalarToValues = [];
		foreach($values as $value) {
			$scalar = $value->getScalar();
			if (isset($scalarToValues[$scalar])) {
				throw new \LogicException('You have provided duplicated values scalar names.');
			}
			if(!$this->hasConstantForScalar($scalar)) {
				throw new \LogicException("Provided instance contains scalar value '$scalar'. But no corresponding constant of enum was found.");
			}
			$scalarToValues[$scalar] = $value;

		}
		return $scalarToValues;
	}

	/**
	 * @param string $class
	 * @param array<string,string|int> $constantToScalar
	 * @param Enum[] $values
	 * @return self
	 */
	public static function from(string $class, array $constantToScalar, array $values): self
	{
		return new self($class, $constantToScalar, $values);
	}

	public function getClass(): string
	{
		return $this->class;
	}

	/**
	 * @return string[]
	 */
	public function getConstantNames(): array
	{
		return \array_keys($this->constantToScalar);
	}

	/**
	 * @return string[]|int[]
	 */
	public function getScalarValues(): array
	{
		return \array_values($this->constantToScalar);
	}

	/**
	 * @return Enum[]
	 */
	public function getValues(): array
	{
		return \array_values($this->scalarToValue);
	}

	/**
	 * @param string $constantName
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
	 * @param string|int $scalarValue
	 */
	public function hasValueForScalar($scalarValue): bool
	{
		return isset($this->scalarToValue[$scalarValue]);
	}

	/**
	 * @param string|int $scalarValue
	 */
	public function getConstantNameForScalar($scalarValue): string
	{
		$result = \array_search($scalarValue, $this->constantToScalar, true);
		if ($result === false) {
			throw new \LogicException("Could not find constant name for $scalarValue.");
		}
		return $result;
	}

	/**
	 * @return int|string
	 */
	public function getScalarForValue(Enum $enum)
	{
		$result = \array_search($enum, $this->scalarToValue, true);
		if ($result === false) {
			throw new \LogicException("Could not find scalar value given value.");
		}
		return $result;
	}

	/**
	 * @param int|string $scalar
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
	 * @param string|int $scalar
	 */
	private function hasConstantForScalar($scalar): bool
	{
		return \in_array($scalar, $this->constantToScalar, TRUE);
	}
}
