<?php
declare(strict_types=1);

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

\Tester\Assert::true($val1 === Enum1::VALUE1());
\Tester\Assert::true($val2 === Enum1::VALUE2());

\Tester\Assert::true($val1 !== Enum1::VALUE2());
\Tester\Assert::true($val2 !== Enum1::VALUE1());

