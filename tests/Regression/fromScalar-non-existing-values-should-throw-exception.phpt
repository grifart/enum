<?php declare(strict_types=1);
require __DIR__ . '/../bootstrap.php';

/**
 * @method static Enum1 VALUE()
 */
class Enum1 extends \Grifart\Enum\Enum
{
	use Grifart\Enum\AutoInstances;
	protected const VALUE = 'value';
}

Enum1::fromScalar('non-existing');
