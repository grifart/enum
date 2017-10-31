<?php declare(strict_types=1);

namespace Grifart\Enum;

final class InstanceRegister
{

	/** @var \Grifart\Enum\Meta */
	private static $instances = [];

	public static function get(string $enumClass, callable $registrator = null): Meta
	{
		if (!isset(self::$instances[$enumClass]) && $registrator !== null) {
			self::register($registrator());
		}
		return self::$instances[$enumClass];
	}

	public static function register(Meta $meta): void
	{
		ConsistencyChecker::checkAnnotations($meta);
		self::$instances[$meta->getClass()] = $meta;
	}

}
