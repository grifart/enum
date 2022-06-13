<?php declare(strict_types=1);

namespace Grifart\Enum;

/**
 * Allows you to use you defined constants automatically as enum value.
 * Without explicitly implementing each enum value.
 *
 * @phpstan-type TScalarValue string|int
 */
trait AutoInstances
{
	abstract protected static function getConstantToScalar(): array;

	/** @param TScalarValue $scalar */
	abstract public function __construct($scalar);

	protected static function provideInstances(): array
	{
		$instances = [];
		foreach (static::getConstantToScalar() as $scalarValue) {
			$instances[] = new static($scalarValue);
		}
		return $instances;
	}
}
