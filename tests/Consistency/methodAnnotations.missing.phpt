<?php declare(strict_types=1);
require __DIR__ . '/../bootstrap.php';

final class MethodAnnotationsMissing extends \Grifart\Enum\Enum
{
	use Grifart\Enum\AutoInstances;

	protected const STATE_A = 'a';

	protected const STATE_B = 'b';
}

\Tester\Assert::exception(
	function () {
		MethodAnnotationsMissing::fromScalar('a');
	},
	\Grifart\Enum\UsageException::class,
	"You have forgotten to add @method annotations for enum 'MethodAnnotationsMissing'. Documentation block should contain:\n"
	. "/**\n"
	. " * @method static MethodAnnotationsMissing STATE_A()\n"
	. " * @method static MethodAnnotationsMissing STATE_B()\n"
	. " */"
);
