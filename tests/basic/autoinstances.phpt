<?php declare(strict_types=1);
require __DIR__ . '/../bootstrap.php';

/**
 * @method static OrderState NEW()
 * @method static OrderState ACTIVE()
 * @method static OrderState DELIVERED()
 */
class OrderState extends \Grifart\Enum\Enum {

	use Grifart\Enum\AutoInstances;

	protected const NEW = 'new';
	protected const ACTIVE = 'active';
	protected const DELIVERED = 'delivered';

}

\Tester\Assert::same(OrderState::NEW(), OrderState::NEW());
\Tester\Assert::same(OrderState::ACTIVE(), OrderState::ACTIVE());
\Tester\Assert::same(OrderState::DELIVERED(), OrderState::DELIVERED());
\Tester\Assert::notSame(OrderState::NEW(), OrderState::DELIVERED());
\Tester\Assert::notSame(OrderState::ACTIVE(), OrderState::DELIVERED());

\Tester\Assert::same('active', OrderState::ACTIVE()->getScalarValue());
\Tester\Assert::same(OrderState::ACTIVE(), OrderState::fromScalar('active'));
