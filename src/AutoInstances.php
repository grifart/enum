<?php declare(strict_types=1);

namespace Grifart\Enum;

trait AutoInstances
{
	// todo: better define this interface
	abstract protected static function getConstantToScalar(): array;

	protected static function provideInstances(): array
	{
		$instances = [];
		foreach (static::getConstantToScalar() as $constantName => $primitiveValue) {
			$instances[$primitiveValue] = new static();
		}
		return $instances;
	}
}
