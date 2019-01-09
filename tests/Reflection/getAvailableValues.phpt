<?php declare(strict_types=1);
require __DIR__ . '/../bootstrap.php';

/**
 * @method static AvailableValuesEnum NEW()
 * @method static AvailableValuesEnum ACTIVE()
 */
final class AvailableValuesEnum extends \Grifart\Enum\Enum
{
	use Grifart\Enum\AutoInstances;

	protected const NEW = 'new';

	protected const ACTIVE = 'active';
}

\Tester\Assert::same(
	AvailableValuesEnum::getAvailableValues(),
	[AvailableValuesEnum::NEW(), AvailableValuesEnum::ACTIVE()]
);
