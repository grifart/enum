<?php declare(strict_types=1);

namespace Tests\Grifart\Enum\Consistency;

use Grifart\Enum\UsageException;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @method static MissingInstanceEnum STATE_A()
 * @method static MissingInstanceEnum STATE_B()
 */
class MissingInstanceEnum extends \Grifart\Enum\Enum
{
	protected const STATE_A = 'a';
	protected const STATE_B = 'b';

	protected static function provideInstances(): array
	{
		return [
			new class(self::STATE_A) extends MissingInstanceEnum
			{
			},
		];
	}
}

$message = 'You have forgotten to provide instance for STATE_B.';
Assert::exception(function () {
	MissingInstanceEnum::STATE_A();
}, UsageException::class, $message);

Assert::exception(function () {
	MissingInstanceEnum::STATE_B();
}, UsageException::class, $message);

Assert::exception(function () {
	MissingInstanceEnum::fromScalar('a');
}, UsageException::class, $message);

Assert::exception(function () {
	MissingInstanceEnum::fromScalar('b');
}, UsageException::class, $message);