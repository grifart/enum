<?php declare(strict_types=1);
require __DIR__ . '/../bootstrap.php';

/**
 * @method static EqualsState NEW()
 * @method static EqualsState ACTIVE()
 */
class EqualsState extends \Grifart\Enum\Enum
{
	use Grifart\Enum\AutoInstances;

	protected const NEW = 'new';

	protected const ACTIVE = 'active';
}

\Tester\Assert::true(EqualsState::NEW()->equals(EqualsState::NEW()));
\Tester\Assert::false(EqualsState::NEW()->equals(EqualsState::ACTIVE()));

\Tester\Assert::true(EqualsState::NEW()->scalarEquals('new'));
\Tester\Assert::false(EqualsState::NEW()->scalarEquals('active'));
