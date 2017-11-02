<?php declare(strict_types=1);

namespace Tests\Grifart\Enum\Consistency;

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
			self::STATE_A => new class extends MissingInstanceEnum
			{
			},
		];
	}
}

$message = 'You have forgotten to provide instance for STATE_B.';
Assert::exception(function () {
	MissingInstanceEnum::STATE_A();
}, \LogicException::class, $message);

Assert::exception(function () {
	MissingInstanceEnum::STATE_B();
}, \LogicException::class, $message);

Assert::exception(function () {
	MissingInstanceEnum::fromScalar('a');
}, \LogicException::class, $message);

Assert::exception(function () {
	MissingInstanceEnum::fromScalar('b');
}, \LogicException::class, $message);