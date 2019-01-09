<?php declare(strict_types=1);


use Grifart\Enum\UsageException;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @method static ForgottenConstructorCall VALUE1()
 */
abstract class ForgottenConstructorCall extends \Grifart\Enum\Enum
{
	private const VALUE1 = 1;

	protected static function provideInstances(): array
	{
		return [
			new class extends ForgottenConstructorCall {
				/** @noinspection PhpMissingParentConstructorInspection */
				protected function __construct() {
					// no parent call
				}
			},
		];
	}
}

Assert::exception(
	function() {
		ForgottenConstructorCall::VALUE1();
	},
	UsageException::class,
	'Parent constructor has not been called while constructing one of ForgottenConstructorCall enum values.'
);
