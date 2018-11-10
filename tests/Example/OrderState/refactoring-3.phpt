<?php declare(strict_types=1);

/**
 * Logic moved into enum
 * @see README.md
 */

namespace Grifart\Enum\Example\__test_refactoring_3;

require __DIR__ . '/../../bootstrap.php';

use Grifart\Enum\Enum;
use Tester\Assert;

final class InvalidTransitionException extends \RuntimeException {}

/**
 * @method static OrderState RECEIVED()
 * @method static OrderState PREPARING()
 * @method static OrderState FINISHED()
 * @method static OrderState CANCELLED()
 */
final class OrderState extends Enum
{

	use \Grifart\Enum\AutoInstances;

	protected const
		RECEIVED = 'received',
		PREPARING = 'preparing',
		FINISHED = 'finished',

		CANCELLED = 'cancelled';


	public function canDoTransition(OrderState $desiredState): bool
	{
		if ($this === self::RECEIVED()) {
			return $desiredState === self::PREPARING() || $desiredState === self::CANCELLED();
		}

		if ($this === self::PREPARING()) {
			return $desiredState === self::FINISHED();
		}

		if ($this === self::FINISHED()) {
			return FALSE;
		}

		if ($this === self::CANCELLED()) {
			return FALSE;
		}

		throw new \LogicException('Should not happen: Unknown state');
	}

}

// Standard order flow:
Assert::true(
	OrderState::RECEIVED()->canDoTransition(
		OrderState::PREPARING()
	)
);
Assert::true(
	OrderState::PREPARING()->canDoTransition(
		OrderState::FINISHED()
	)
);

// Cancellation order flow
Assert::true(
	OrderState::RECEIVED()->canDoTransition(
		OrderState::CANCELLED()
	)
);

// Non-reflexivity test
Assert::false(
	OrderState::CANCELLED()->canDoTransition(
		OrderState::CANCELLED()
	)
);




// --- NEGATIVE TESTS ---

// Invalid order flow
Assert::false(
	OrderState::RECEIVED()->canDoTransition(
		OrderState::FINISHED()
	)
);
Assert::false(
	OrderState::PREPARING()->canDoTransition(
		OrderState::CANCELLED()
	)
);
Assert::false(
	OrderState::FINISHED()->canDoTransition(
		OrderState::CANCELLED()
	)
);

