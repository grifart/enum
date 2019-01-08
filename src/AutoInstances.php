<?php declare(strict_types=1);

namespace Grifart\Enum;

trait AutoInstances
{
	// todo: better define this interface
	abstract protected static function getConstantToScalar(): array;

	/** @param string|int $scalar */
	abstract public function __construct($scalar);

	protected static function provideInstances(): array
	{
		$instances = [];
		foreach (static::getConstantToScalar() as $constantName => $primitiveValue) {
			$instances[] = new static($primitiveValue);
		}
		return $instances;
	}
}
