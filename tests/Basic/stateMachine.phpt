<?php declare(strict_types=1);
require __DIR__ . '/../bootstrap.php';

/**
 * @method static StateMachine STATE_A()
 * @method static StateMachine STATE_B()
 */
abstract class StateMachine extends \Grifart\Enum\Enum
{
	protected const STATE_A = 'a';

	protected const STATE_B = 'b';

	abstract public function canDoTransitionTo(StateMachine $targetState): bool;

	protected static function provideInstances(): array
	{
		return [
			new class(self::STATE_A) extends StateMachine
			{
				public function canDoTransitionTo(StateMachine $targetState): bool
				{
					return $targetState === StateMachine::STATE_B();
				}
			},
			new class(self::STATE_B) extends StateMachine
			{
				public function canDoTransitionTo(StateMachine $targetState): bool
				{
					return $targetState === StateMachine::STATE_A();
				}
			},
		];
	}
}

/** @noinspection SuspiciousBinaryOperationInspection */
\Tester\Assert::true(StateMachine::STATE_A() === StateMachine::STATE_A());
\Tester\Assert::false(StateMachine::STATE_A() === StateMachine::STATE_B());
/** @noinspection SuspiciousBinaryOperationInspection */
\Tester\Assert::true(StateMachine::STATE_B() === StateMachine::STATE_B());

\Tester\Assert::true(StateMachine::STATE_A()->canDoTransitionTo(StateMachine::STATE_B()));
\Tester\Assert::true(StateMachine::STATE_B()->canDoTransitionTo(StateMachine::STATE_A()));
\Tester\Assert::false(StateMachine::STATE_A()->canDoTransitionTo(StateMachine::STATE_A()));
\Tester\Assert::false(StateMachine::STATE_B()->canDoTransitionTo(StateMachine::STATE_B()));

