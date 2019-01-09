<?php declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

/**
 * @method static EnumMixedKeys VALUE_STRING()
 * @method static EnumMixedKeys VALUE_INT()
 */
class EnumMixedKeys extends \Grifart\Enum\Enum
{
	use Grifart\Enum\AutoInstances;

	private const VALUE_STRING = '1';
	private const VALUE_INT = 1;
}

\Tester\Assert::exception(
	function() {
		// first access triggers initial enum validation
		EnumMixedKeys::VALUE_INT();
	},
	\Grifart\Enum\UsageException::class,
	'Mixed types of scalar value. Keys must either all string or all int.'
);
