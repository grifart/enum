<?php declare(strict_types=1);

namespace Tests\Grifart\Enum\Consistency;

use Grifart\Enum\UsageException;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

class UndecidedEnum extends \Grifart\Enum\Enum
{
	protected static function provideInstances(): array
	{
		return [];
	}
}

Assert::exception(
	function () {
		UndecidedEnum::getAvailableValues();
	},
	UsageException::class,
	"Enum root class must be either abstract or final.\n"
	. "Final is used when one type is enough for all enum instance values.\n"
	. 'Abstract is used when enum values are always instances of child classes of enum root class.'
);
