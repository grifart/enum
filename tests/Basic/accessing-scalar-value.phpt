<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

/**
 * @method static EnumString VALUE1()
 * @method static EnumString VALUE2()
 */
class EnumString extends \Grifart\Enum\Enum
{
	use Grifart\Enum\AutoInstances;

	protected const VALUE1 = 'value1';
	protected const VALUE2 = 'value2';
}

\Tester\Assert::same('value1', EnumString::VALUE1()->toScalar());
\Tester\Assert::same('value1', (string) EnumString::VALUE1());

\Tester\Assert::same('value2', EnumString::VALUE2()->toScalar());
\Tester\Assert::same('value2', (string) EnumString::VALUE2());


/**
 * @method static EnumInt VALUE1()
 * @method static EnumInt VALUE2()
 */
class EnumInt extends \Grifart\Enum\Enum
{
	use Grifart\Enum\AutoInstances;

	protected const VALUE1 = 1;
	protected const VALUE2 = 2;
}

\Tester\Assert::same(1, EnumInt::VALUE1()->toScalar());
\Tester\Assert::same('1', (string) EnumInt::VALUE1());

\Tester\Assert::same(2, EnumInt::VALUE2()->toScalar());
\Tester\Assert::same('2', (string) EnumInt::VALUE2());
