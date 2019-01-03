<?php declare(strict_types=1);

use Grifart\Enum\MissingValueValueException;

require __DIR__ . '/../bootstrap.php';

/**
 * @method static Enum1 VALUE()
 */
class Enum1 extends \Grifart\Enum\Enum
{
	use Grifart\Enum\AutoInstances;
	protected const VALUE = 'value';
}

\Tester\Assert::exception(
	function () {
		Enum1::fromScalar('non-existing');
	},
	MissingValueValueException::class,
	"There is no value for enum 'Enum1' and scalar value 'non-existing'."
);
