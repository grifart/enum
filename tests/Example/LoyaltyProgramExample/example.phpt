<?php

/**
 * example from loyalty program domain
 * presented by Jimmy Bogard - https://vimeo.com/43598193
 */

namespace ValuesWithBehaviourExample;

require __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

/**
 * Type of offer - e.g. "Summer sale in Shop A"
 */
interface OfferType {
	public function name(): string;
	public function beginDate(): ?\DateTimeImmutable; // when offer starts
	public function expirationType(): ExpirationType;
	public function daysValid(): int;
}

/**
 * Loyalty program member
 */
interface Member {
	public function id(): int;
}

/**
 * Each offer must be namely assigned to member
 */
interface Offer {
	public function assignedAt(): \DateTimeImmutable; // when ticket is assigned to client
	public function type(): OfferType;
	public function assignedTo(): Member;
}

/**
 * Type of offer expiration.
 * - FIXED - expires for all member at once,
 *           after days set in offer type (counting from
 * - ASSIGNMENT - expires after
 *
 * @method static ExpirationType ASSIGNMENT()
 * @method static ExpirationType FIXED()
 */
abstract class ExpirationType extends \Grifart\Enum\Enum
{
	protected const ASSIGNMENT = 'assignment';
	protected const FIXED = 'fixed';

	abstract public function computeExpiration(Offer $offer): \DateTimeImmutable;

	protected static function provideInstances() : array {
		return [
			new class(self::ASSIGNMENT) extends ExpirationType {
				public function computeExpiration(Offer $offer): \DateTimeImmutable {
					return $offer->assignedAt()
						->modify('+' . $offer->type()->daysValid() . ' days');
				}
			},
			new class(self::FIXED) extends ExpirationType {
				public function computeExpiration(Offer $offer): \DateTimeImmutable {
					$beginDate = $offer->type()->beginDate();
					\assert($beginDate !== NULL);
					return $beginDate->modify('+' . $offer->type()->daysValid() . ' days');
				}
			},
		];
	}
}


// just checking if it compiles
Assert::type(ExpirationType::class, ExpirationType::ASSIGNMENT());