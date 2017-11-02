<?php declare(strict_types=1);

namespace Grifart\Enum\Internal;

use Grifart\Enum\Internal\ConsistencyChecker;
use Grifart\Enum\Internal\Meta;

final class InstanceRegister
{

	/** @var \Grifart\Enum\Internal\Meta[] */
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
	    // check consistency of enum when assertions are enabled
		assert(
            (function() use ($meta): bool {
                ConsistencyChecker::checkAnnotations($meta);
                return true;
            })()
        );
		self::$instances[$meta->getClass()] = $meta;
	}

}
