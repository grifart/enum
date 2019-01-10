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
 * @method static OrderState PROCESSING()
 * @method static OrderState FINISHED()
 * @method static OrderState CANCELLED()
 */
final class OrderState extends Enum
{

	use \Grifart\Enum\AutoInstances;

	protected const
		RECEIVED = 'received',
		PROCESSING = 'processing',
		FINISHED = 'finished',

		CANCELLED = 'cancelled';


	public function canDoTransition(OrderState $desiredState): bool
	{
		if ($this === self::RECEIVED()) {
			return $desiredState === self::PROCESSING() || $desiredState === self::CANCELLED();
		}

		if ($this === self::PROCESSING()) {
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
		OrderState::PROCESSING()
	)
);
Assert::true(
	OrderState::PROCESSING()->canDoTransition(
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
	OrderState::PROCESSING()->canDoTransition(
		OrderState::CANCELLED()
	)
);
Assert::false(
	OrderState::FINISHED()->canDoTransition(
		OrderState::CANCELLED()
	)
);









$state1 = OrderState::RECEIVED();
$state2 = OrderState::RECEIVED();
Assert::true($state1 === $state2);

$state3 = OrderState::PROCESSING();
Assert::true($state1 !== $state3);
Assert::true($state2 !== $state3);



