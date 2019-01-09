<?php  declare(strict_types=1);
namespace MigratingOldCode;

require __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

/**
 * @method static OrderState NEW()
 * @method static OrderState PROCESSING()
 */
final class OrderState extends \Grifart\Enum\Enum {
	use \Grifart\Enum\AutoInstances;
	private const NEW = 'new';
	private const PROCESSING = 'processing';

	public function doBusinessLogic(): string
	{
		switch ($this) {
			// your business logic
			case self::NEW():
				return 'new';
				break;
			case self::PROCESSING():
				return 'processing';
				break;
		}
		throw new \LogicException('should never happen');
	}
}

$state = OrderState::NEW();
$result = $state->doBusinessLogic();
Assert::same('new', $result);
