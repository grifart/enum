<?php declare(strict_types=1);

namespace Grifart\Enum;

trait AutoInstances
{

	// todo: better define this interface
	abstract protected static function getPrimitiveValues(): array;


	protected static function provideInstances(): array {
		$instances = [];
		foreach(static::getPrimitiveValues() as $constantName => $primitiveValue) {
			$instances[$primitiveValue] = new static();
		}
		return $instances;
	}

}
