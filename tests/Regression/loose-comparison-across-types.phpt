<?php declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

/**
 * @method static Enum1 VALUE()
 */
class Enum1 extends \Grifart\Enum\Enum
{
	use Grifart\Enum\AutoInstances;

	private const VALUE = 'value';
}

/**
 * @method static Enum2 VALUE()
 */
class Enum2 extends \Grifart\Enum\Enum
{
	use Grifart\Enum\AutoInstances;

	private const VALUE = 'value';
}

\Tester\Assert::true(Enum1::VALUE()->toScalar() === Enum2::VALUE()->toScalar());

// everything same, but type of value is different
\Tester\Assert::false(Enum1::VALUE() === Enum2::VALUE());
/** @noinspection PhpNonStrictObjectEqualityInspection TypeUnsafeComparisonInspection */
\Tester\Assert::false(Enum1::VALUE() == Enum2::VALUE());
