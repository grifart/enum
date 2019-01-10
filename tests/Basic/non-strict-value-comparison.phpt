<?php
declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @method static Enum1 VALUE1()
 * @method static Enum1 VALUE2()
 */
final class Enum1 extends \Grifart\Enum\Enum
{
	use Grifart\Enum\AutoInstances;
	protected const VALUE1 = 'value1';
	protected const VALUE2 = 'value2';
}

$val1 = Enum1::VALUE1();
$val2 = Enum1::VALUE2();

/** intentionally non-strict @noinspection TypeUnsafeComparisonInspection PhpNonStrictObjectEqualityInspection */
Assert::true($val1 == Enum1::VALUE1());
/** intentionally non-strict @noinspection TypeUnsafeComparisonInspection PhpNonStrictObjectEqualityInspection */
Assert::true($val2 == Enum1::VALUE2());

/** intentionally non-strict @noinspection TypeUnsafeComparisonInspection PhpNonStrictObjectEqualityInspection */
Assert::true($val1 != Enum1::VALUE2());
/** intentionally non-strict @noinspection TypeUnsafeComparisonInspection PhpNonStrictObjectEqualityInspection */
Assert::true($val2 != Enum1::VALUE1());


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
Assert::same(1, $switchResult);


// Check enums are handled well in in_array() function
Assert::true(
	in_array(Enum1::VALUE1(), [ Enum1::VALUE1(), 'no-match' ], true),
	'List of enums contains item - strict check'
);

Assert::true(
	in_array(Enum1::VALUE1(), [ Enum1::VALUE1(), 'no-match' ], false),
	'List of enums contains item - loose check'
);

Assert::false(
	in_array(Enum1::VALUE1(), [ Enum1::VALUE2(), 'no-match' ], true),
	'List of enums doesnt contain item - strict check'
);

Assert::false(
	in_array(Enum1::VALUE1(), [ Enum1::VALUE2(), 'no-match' ], false),
	'List of enums doesnt contain item - loose check'
);


/**
 * @method static Enum2 ONE()
 * @method static Enum2 TWO()
 * @method static Enum2 THREE()
 */
final class Enum2 extends \Grifart\Enum\Enum
{
	use Grifart\Enum\AutoInstances;
	protected const ONE = 'value1';
	protected const TWO = 'value2';
	protected const THREE = 'value3';
}

// array_intersect
$first_array = [Enum2::ONE(), Enum2::TWO()];
$second_array = [Enum2::TWO(), Enum2::THREE()];
Assert::equal([Enum2::TWO()], array_values(array_intersect($first_array, $second_array)));
Assert::equal([Enum2::TWO()], array_values(array_intersect($second_array, $first_array)));
Assert::equal($first_array, array_values(array_intersect($first_array, $first_array)));
