<?php declare(strict_types=1);

use Grifart\Enum\MissingValueDeclarationException;

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

\Tester\Assert::false(
	EnumMixedKeys::VALUE_INT() === EnumMixedKeys::VALUE_STRING()
);
\Tester\Assert::false(EnumMixedKeys::VALUE_INT() == EnumMixedKeys::VALUE_STRING());

\Tester\Assert::false(
	EnumMixedKeys::VALUE_INT()->equalsScalarValue(
		EnumMixedKeys::VALUE_STRING()->getScalarValue()
	)
);
