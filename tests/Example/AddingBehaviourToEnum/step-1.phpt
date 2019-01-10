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

}

$monday = DayOfWeek::MONDAY();

function nextDay(DayOfWeek $dayOfWeek): DayOfWeek
{
	switch ($dayOfWeek) {
		case DayOfWeek::MONDAY():
			return DayOfWeek::TUESDAY();

		case DayOfWeek::TUESDAY():
			// ...

	}
	throw new \LogicException('should not happen');
}

\Tester\Assert::same(DayOfWeek::TUESDAY(), nextDay($monday));