<?php declare(strict_types=1);

namespace TestFullClasses;

use Grifart\Enum\UsageException;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @method static FullClassesAsValuesEnum VALUE1()
 * @method static FullClassesAsValuesEnum VALUE2()
 */
abstract class FullClassesAsValuesEnum extends \Grifart\Enum\Enum
{
	private const VALUE1 = 1;
	private const VALUE2 = 2;

	protected static function provideInstances(): array
	{
		return [
			new Value1(self::VALUE1),
			new Value2(self::VALUE2),
		];
	}
}
final class Value1 extends FullClassesAsValuesEnum { }
class Value2 extends FullClassesAsValuesEnum { }

// Standard APIs:
Assert::equal(FullClassesAsValuesEnum::VALUE1()->toScalar(), 1);
Assert::equal(FullClassesAsValuesEnum::VALUE2()->toScalar(), 2);

Assert::type(Value1::class, FullClassesAsValuesEnum::VALUE1());
Assert::type(Value2::class, FullClassesAsValuesEnum::VALUE2());

Assert::same(
	[
		FullClassesAsValuesEnum::VALUE1(),
		FullClassesAsValuesEnum::VALUE2()
	],
	FullClassesAsValuesEnum::getAvailableValues()
);

Assert::same(FullClassesAsValuesEnum::VALUE1(), FullClassesAsValuesEnum::fromScalar(1));


// ## Wrong usage & edge-cases

// wrong usage:
$expectNonRootAccess = function(callable $fn) {
	Assert::exception(
		$fn,
		UsageException::class,
		"You have accessed static enum method on non-root class ('TestFullClasses\\FullClassesAsValuesEnum' is a root class)"
	);
};
$expectNonRootAccess(function () {
	Value1::getAvailableValues();
});
$expectNonRootAccess(function () {
	Value1::fromScalar('1');
});
//$expectNonRootAccess(function () {
//	Value1::VALUE1();
//});
//$expectNonRootAccess(function () {
//	Value1::VALUE2();
//});
Assert::type(Value1::class, Value1::VALUE1());
Assert::type(Value1::class, Value2::VALUE1());
Assert::type(Value2::class, Value1::VALUE2());
Assert::type(Value2::class, Value2::VALUE2());

// valid edge case: this is valid and accesses registers the same way as calls above
Assert::same(
	'VALUE1',
	FullClassesAsValuesEnum::VALUE1()
		->getConstantName()
);

