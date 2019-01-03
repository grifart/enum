<?php declare(strict_types=1);
require __DIR__ . '/../bootstrap.php';

// REGRESSION FOR:
// LogicException: You have forgotten to add @method annotations for enum 'class@anonymous /Users/jkuchar1/Documents/www/grifart-enum/tests/Reflection/constantNames.inherited.phpt0x10b8681bc'. Documentation block should contain:
///**
// * @method static class@anonymous /Users/jkuchar1/Documents/www/grifart-enum/tests/Reflection/constantNames.inherited.phpt0x10b8681bc NEW()
// * @method static class@anonymous /Users/jkuchar1/Documents/www/grifart-enum/tests/Reflection/constantNames.inherited.phpt0x10b8681bc ACTIVE()
// */



/**
 * @method static ReflectionConstantNames2 NEW()
 * @method static ReflectionConstantNames2 ACTIVE()
 */
abstract class ReflectionConstantNames2 extends \Grifart\Enum\Enum
{
	protected const NEW = 'new';

	protected const ACTIVE = 'active';

	protected static function provideInstances(): array
	{
		return [
			new class(self::NEW) extends ReflectionConstantNames2 {},
			new class(self::ACTIVE) extends ReflectionConstantNames2 {},
		];
	}
}

\Tester\Assert::same('new', ReflectionConstantNames2::NEW()->getScalarValue());
\Tester\Assert::same('active', ReflectionConstantNames2::ACTIVE()->getScalarValue());
