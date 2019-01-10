<?php

require __DIR__ . '/../../bootstrap.php';

/**
 * @method static DayOfWeek MONDAY()
 * @method static DayOfWeek TUESDAY()
 */
abstract class DayOfWeek extends \Grifart\Enum\Enum
{

	protected const MONDAY = 'monday';
	protected const TUESDAY = 'tuesday';
	// ...

	abstract public function nextDay(): self;


	/** @return static[] */
	protected static function provideInstances(): array
	{
		return [
			new class(self::MONDAY) extends DayOfWeek
			{
				public function nextDay(): DayOfWeek
				{
					return DayOfWeek::TUESDAY();
				}
			},

			new class(self::TUESDAY) extends DayOfWeek
			{
				public function nextDay(): DayOfWeek
				{
					return DayOfWeek::WEDNESDAY();
				}
			},
		];
	}
}

\Tester\Assert::same(
	DayOfWeek::TUESDAY(),
	DayOfWeek::MONDAY()->nextDay()
);