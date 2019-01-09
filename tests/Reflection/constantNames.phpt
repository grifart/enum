<?php declare(strict_types=1);
require __DIR__ . '/../bootstrap.php';

/**
 * @method static ReflectionConstantNames NEW()
 * @method static ReflectionConstantNames ACTIVE()
 */
final class ReflectionConstantNames extends \Grifart\Enum\Enum
{
	use Grifart\Enum\AutoInstances;

	protected const NEW = 'new';

	protected const ACTIVE = 'active';
}

\Tester\Assert::same('NEW', ReflectionConstantNames::NEW()->getConstantName());
\Tester\Assert::same('ACTIVE', ReflectionConstantNames::ACTIVE()->getConstantName());
