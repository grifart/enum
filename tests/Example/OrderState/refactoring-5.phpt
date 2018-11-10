<?php declare(strict_types=1);
/**
 * Separate class implementation for each value
 * @see README.md
 */
namespace Grifart\Enum\Example\OrderState\__test_refactoring_5;

require __DIR__ . '/../../bootstrap.php';

use Grifart\Enum\Enum;
use Tester\Assert;

final class InvalidTransitionException extends \RuntimeException {}

class Order
{
	/** @var string|null */
	private $employee = 'employee';

	/** @var OrderState */
	private $state;

	public function __construct() {
		$this->state = OrderState::RECEIVED();
	}

	public function unassignEmployee(): void {
		$this->employee = null;
	}

	public function getEmployee(): ?string {
		return $this->employee;
	}

	/**
	 * @throws InvalidTransitionException
	 */
	public function changeState(OrderState $desiredState): void
	{
		$this->state =
			$this->state->doTransition($this, $desiredState);
	}
}

/**
 * @method static OrderState RECEIVED()
 * @method static OrderState PREPARING()
 * @method static OrderState FINISHED()
 * @method static OrderState CANCELLED()
 */
abstract class OrderState extends Enum {

	protected const
		RECEIVED = 'received',
		PREPARING = 'preparing',
		FINISHED = 'finished',

		CANCELLED = 'cancelled'; // domain logic: can be cancelled before preparation is started

	/**
	 * @throws InvalidTransitionException
	 */
	final public function doTransition(Order $order, OrderState $desiredState): self
	{
		if ($desiredState !== $this && !$this->canDoTransition($desiredState)) {
			throw new InvalidTransitionException();
		}
		$desiredState->onActivation($order);
		return $desiredState;
	}


	abstract public function canDoTransition(OrderState $nextState): bool;

	protected function onActivation(Order $order): void { /* override me */}


	/** @return self[] */
	final protected static function provideInstances(): array
	{
		return [
			self::RECEIVED => new class extends OrderState {

				public function canDoTransition(OrderState $nextState): bool
				{
					return $nextState === $this::PREPARING() || $nextState === $this::CANCELLED();
				}

			},


			self::PREPARING => new class extends OrderState {

				public function canDoTransition(OrderState $nextState): bool
				{
					return $nextState === $this::FINISHED();
				}

			},


			self::FINISHED => new class extends OrderState {

				public function canDoTransition(OrderState $nextState): bool
				{
					return FALSE;
				}

				protected function onActivation(Order $order): void
				{
					$order->unassignEmployee();
				}

			},


			self::CANCELLED => new class extends OrderState {

				public function canDoTransition(OrderState $nextState): bool
				{
					return FALSE;
				}

				protected function onActivation(Order $order): void
				{
					$order->unassignEmployee();
				}
			},
		];
	}
}

// Standard order flow:
(function() {
	$order = new Order();
	Assert::same('employee', $order->getEmployee());
	$order->changeState(OrderState::PREPARING());
	Assert::same('employee', $order->getEmployee());
	$order->changeState(OrderState::FINISHED());
	Assert::null($order->getEmployee());
})();

// Cancellation order flow
(function() {
	$order = new Order();
	Assert::same('employee', $order->getEmployee());
	$order->changeState(OrderState::CANCELLED());
	Assert::null($order->getEmployee());
})();


// --- NEGATIVE TESTS ---

// Invalid order flow
Assert::exception(function () {
	$order = new Order();
	$order->changeState(OrderState::FINISHED()); // not allowed
}, InvalidTransitionException::class);

Assert::exception(function () {
	$order = new Order();
	$order->changeState(OrderState::PREPARING());
	$order->changeState(OrderState::CANCELLED()); // not allowed
}, InvalidTransitionException::class);

Assert::exception(function () {
	$order = new Order();
	$order->changeState(OrderState::PREPARING());
	$order->changeState(OrderState::FINISHED());

	$order->changeState(OrderState::CANCELLED()); // not allowed
}, InvalidTransitionException::class);



