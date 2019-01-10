<?php

require __DIR__ . '/../../bootstrap.php';

/**
 * @method static DayOfWeek MONDAY()
 * @method static DayOfWeek TUESDAY()
 */
final class DayOfWeek extends \Grifart\Enum\Enum
{
	use Grifart\Enum\AutoInstances;

	private const MONDAY = 'monday';
	private const TUESDAY = 'tuesday';

	// ...

	public function nextDay(): self
	{

		switch ($this) {
			case self::MONDAY():
				return self::TUESDAY();

			case self::TUESDAY():
				// ...

		}
		throw new \LogicException('should never happen');
	}

}

\Tester\Assert::same(
	DayOfWeek::TUESDAY(),
	DayOfWeek::MONDAY()->nextDay()
);