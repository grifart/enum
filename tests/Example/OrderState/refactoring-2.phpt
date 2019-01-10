<?php declare(strict_types=1);

/**
 * @see README.md
 */

namespace Grifart\Enum\Example\OrderState\__test_refactoring_2;

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

}

class OrderService
{

	public function canDoTransition(OrderState $currentState, OrderState $desiredState): bool
	{

		if ($currentState === OrderState::RECEIVED()) {
			return $desiredState === OrderState::PROCESSING() || $desiredState === OrderState::CANCELLED();
		}

		if ($currentState === OrderState::PROCESSING()) {
			return $desiredState === OrderState::FINISHED();
		}

		if ($currentState === OrderState::FINISHED()) {
			return FALSE;
		}

		if ($currentState === OrderState::CANCELLED()) {
			return FALSE;
		}

		throw new \LogicException('Should not happen: Unknown state');
	}

}

$orderService = new OrderService();

// Standard order flow:
Assert::true(
	$orderService->canDoTransition(
		OrderState::RECEIVED(),
		OrderState::PROCESSING()
	)
);
Assert::true(
	$orderService->canDoTransition(
		OrderState::PROCESSING(),
		OrderState::FINISHED()
	)
);

// Cancellation order flow
Assert::true(
	$orderService->canDoTransition(
		OrderState::RECEIVED(),
		OrderState::CANCELLED()
	)
);

// Reflexivity test
Assert::false(
	$orderService->canDoTransition(
		OrderState::CANCELLED(),
		OrderState::CANCELLED()
	)
);




// --- NEGATIVE TESTS ---

// Invalid order flow
Assert::false(
	$orderService->canDoTransition(
		OrderState::RECEIVED(),
		OrderState::FINISHED()
	)
);
Assert::false(
	$orderService->canDoTransition(
		OrderState::PROCESSING(),
		OrderState::CANCELLED()
	)
);
Assert::false(
	$orderService->canDoTransition(
		OrderState::FINISHED(),
		OrderState::CANCELLED()
	)
);

