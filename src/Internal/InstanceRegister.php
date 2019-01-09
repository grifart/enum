<?php declare(strict_types=1);

namespace Grifart\Enum\Internal;

/**
 * Keeps track of all enum instances organized by enum root classes.
 */
final class InstanceRegister
{
	/** @var \Grifart\Enum\Internal\Meta[] */
	private static $instances = [];

	public static function get(string $enumClass, callable $registrar): Meta
	{
		if (!isset(self::$instances[$enumClass])) {
			self::register($registrar());
		}
		return self::$instances[$enumClass];
	}

	public static function register(Meta $meta): void
	{
		// check consistency of enum when assertions are enabled (typically non-production code)
		assert(
			(function () use ($meta): bool {
				ConsistencyChecker::checkAnnotations($meta);
				return true;
			})()
		);
		self::$instances[$meta->getClass()] = $meta;
	}
}
