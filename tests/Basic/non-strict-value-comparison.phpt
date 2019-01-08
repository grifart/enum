<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

/**
 * @method static Enum1 VALUE1()
 * @method static Enum1 VALUE2()
 */
class Enum1 extends \Grifart\Enum\Enum
{
	use Grifart\Enum\AutoInstances;
	protected const VALUE1 = 'value1';
	protected const VALUE2 = 'value2';
}

$val1 = Enum1::VALUE1();
$val2 = Enum1::VALUE2();

/** intentionally non-strict @noinspection TypeUnsafeComparisonInspection PhpNonStrictObjectEqualityInspection */
\Tester\Assert::true($val1 == Enum1::VALUE1());
/** intentionally non-strict @noinspection TypeUnsafeComparisonInspection PhpNonStrictObjectEqualityInspection */
\Tester\Assert::true($val2 == Enum1::VALUE2());

/** intentionally non-strict @noinspection TypeUnsafeComparisonInspection PhpNonStrictObjectEqualityInspection */
\Tester\Assert::true($val1 != Enum1::VALUE2());
/** intentionally non-strict @noinspection TypeUnsafeComparisonInspection PhpNonStrictObjectEqualityInspection */
\Tester\Assert::true($val2 != Enum1::VALUE1());


$switchResult = 0;
switch ($val1) {
	case Enum1::VALUE1():
		$switchResult = 1;
		break;
	case Enum1::VALUE2():
		$switchResult = 2;
		break;
	default:
		$switchResult = 3;
		break;
}
\Tester\Assert::same(1, $switchResult);
