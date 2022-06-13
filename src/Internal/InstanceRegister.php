<?php declare(strict_types=1);

namespace Grifart\Enum\Internal;

use Grifart\Enum\Enum;

/**
 * Keeps track of all enum instances organized by enum root classes.
 */
final class InstanceRegister
{
	/** @var Meta<Enum>[] */
	private static $instances = [];

	/**
	 * @template TEnum of Enum
	 * @param class-string<TEnum> $enumClass
	 * @param callable():Meta<TEnum> $registrar
	 * @return Meta<TEnum>
	 */
	public static function get(string $enumClass, callable $registrar): Meta
	{
		if (!isset(self::$instances[$enumClass])) {
			self::register($enumClass, $registrar());
		}
		/** @var Meta<TEnum> $meta */
		$meta = self::$instances[$enumClass];
		return $meta;
	}

	/**
	 * @template TEnum of \Grifart\Enum\Enum
	 * @param class-string<TEnum> $className
	 * @param Meta<TEnum> $meta
	 * @return void
	 */
	public static function register(string $className, Meta $meta): void
	{
		\assert($meta->getClass() === $className, 'Provided Meta object is for different enum class that was originally registered.');

		// check consistency of enum when assertions are enabled (typically non-production code)
		// @phpstan-ignore-next-line as "Call to function assert() with true will always evaluate to true." is intentional
		assert(
			(function () use ($meta): bool {
				ConsistencyChecker::checkAnnotations($meta);
				return true;
			})()
		);
		self::$instances[$className] = $meta;
	}
}
