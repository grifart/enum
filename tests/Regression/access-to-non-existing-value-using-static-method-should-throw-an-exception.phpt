<?php declare(strict_types=1);

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
		/** Intentionally calling non-existing method. @noinspection PhpUndefinedMethodInspection */
		Enum1::NON_EXISTING();
	},
	Error::class,
	'Call to undefined method Enum1::NON_EXISTING(). Please check that you have provided constant, annotation and value.'
);
