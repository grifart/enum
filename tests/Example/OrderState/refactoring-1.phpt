<?php declare(strict_types=1);

/**
 * This test shows implicit enum declaration.
 * @see README.md
 */

namespace Grifart\Enum\Example\OrderState\__test_refactoring_1;

require __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

final class InvalidTransitionException extends \RuntimeException {}



class OrderService
{

	public const STATE_RECEIVED = 'received';
	public const STATE_PROCESSING = 'processing';
	public const STATE_FINISHED = 'finished';
	public const STATE_CANCELLED = 'cancelled';

	public function canDoTransition(string $currentState, string $desiredState): bool
	{
		if ($currentState === $desiredState) {
			return TRUE;
		}

		switch ($currentState) {
			case self::STATE_RECEIVED:
				return $desiredState === self::STATE_PROCESSING || $desiredState === self::STATE_CANCELLED;
			case self::STATE_PROCESSING:
				return $desiredState === self::STATE_FINISHED;
			case self::STATE_FINISHED:
				return FALSE;
			case self::STATE_CANCELLED:
				return FALSE;
			default:
				throw new \LogicException('Should not happen: Unknown state');
		}
	}

}

$orderService = new OrderService();

// Standard order flow:
Assert::true(
	$orderService->canDoTransition(
		OrderService::STATE_RECEIVED,
		OrderService::STATE_PROCESSING
	)
);
Assert::true(
	$orderService->canDoTransition(
		OrderService::STATE_PROCESSING,
		OrderService::STATE_FINISHED
	)
);

// Cancellation order flow
Assert::true(
	$orderService->canDoTransition(
		OrderService::STATE_RECEIVED,
		OrderService::STATE_CANCELLED
	)
);

// Reflexivity test
Assert::true(
	$orderService->canDoTransition(
		OrderService::STATE_CANCELLED,
		OrderService::STATE_CANCELLED
	)
);




// --- NEGATIVE TESTS ---

// Invalid order flow
Assert::false(
	$orderService->canDoTransition(
		OrderService::STATE_RECEIVED,
		OrderService::STATE_FINISHED
	)
);
Assert::false(
	$orderService->canDoTransition(
		OrderService::STATE_PROCESSING,
		OrderService::STATE_CANCELLED
	)
);
Assert::false(
	$orderService->canDoTransition(
		OrderService::STATE_FINISHED,
		OrderService::STATE_CANCELLED
	)
);

// check for completely invalid arguments
Assert::exception(function () use ($orderService) {
	$orderService->canDoTransition('invalid', 'non-existing');
}, \LogicException::class);

