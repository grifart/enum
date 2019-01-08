<?php  declare(strict_types=1);
namespace MigratingOldCode;

require __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

class OrderState {
	public const NEW = 'new';
	public const PROCESSING = 'processing';
}

$state = OrderState::NEW;

$result = '';
switch ($state) {
	// your business logic
	case OrderState::NEW:
		$result = 'new';
		break;
	case OrderState::PROCESSING:
		$result = 'processing';
		break;
}

Assert::same('new', $result);
