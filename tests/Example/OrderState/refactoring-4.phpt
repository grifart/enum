<?php declare(strict_types=1);

/**
 * Separate instance for each value
 * @see README.md
 */

namespace Grifart\Enum\Example\OrderState\__test_refactoring_4;

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

	protected const
		RECEIVED = 'received',
		PREPARING = 'preparing',
		FINISHED = 'finished',

		CANCELLED = 'cancelled'; // domain logic: can be cancelled before preparation is started



	/** @var string[] */
	private $nextAllowedStates = [];

	/**
	 * @param string[] $nextAllowedStates
	 */
	protected function __construct(array $nextAllowedStates)
	{
		$this->nextAllowedStates = $nextAllowedStates;
	}

	public function canDoTransition(OrderState $nextState): bool
	{
		return \in_array($nextState->getScalarValue(), $this->nextAllowedStates, TRUE);
	}


	/** @return self[] */
	final protected static function provideInstances(): array
	{
		// please not that we cannot reference self::PREPARING()
		// as it returns class instance, and this will call provideInstances()
		// again and you will get infinite loop.

		return [
			self::RECEIVED => new self([self::PREPARING, self::CANCELLED]),
			self::PREPARING => new self([self::FINISHED]),
			self::FINISHED => new self([]),
			self::CANCELLED => new self([]),
		];
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

