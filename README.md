# grifart/enum

Enumeration value object. Enumerate values and behaviour with type-safety.

Repositories [gitlab.grifart.cz](https://gitlab.grifart.cz/jkuchar1/grifart-enum)
and [github.com](https://github.com/grifart/enum).
[![pipeline status](https://gitlab.grifart.cz/jkuchar1/grifart-enum/badges/master/pipeline.svg)](https://gitlab.grifart.cz/jkuchar1/grifart-enum/commits/master)

Sponsored by [grifart.com](https://grifart.com).

## Introduction

Enums represent predefined set of values. The available values are defined statically by each enum class. Each value is represented by an instance of this class in a flyweight manner.

- This enum allows you to add individual behaviour for every enum value (as in Java). This allows you to transform your `switch`es/`if`s into more readable composition. (see example bellow)
- Checks enum annotations if phpdoc-declared methods are properly declared (will generate docblock for you when not specified or incorrect)
- `===`, `==` and usage of `switch`es is supported
- string or integer scalar keys are supported
- Easily access scalar value of enum `DayOfWeek::MONDAY()->toScalar()` or `(string) DayOfWeek::MONDAY()`

Also includes:

- It is type safe. By annotating your enumeration type, you are guaranteed that there will be no other values then you declared. `function translateTo(DayOfWeek $day) { ...`
- You can get a list of all the possible values `Enum::getAvailableValues()`

## Installation

```bash
composer require grifart/enum
```

This library uses [**semantic versioning 2.0**](https://semver.org/spec/v2.0.0.html).
You can safely use `^` constrain in you `composer.json`.

## Requirements

This library requires PHP 7.1 and later.

## Project status & release process

While this library is still under development, it is well tested and should be stable enough to use in production environments.

The current releases are numbered 0.x.y. When a non-breaking change is introduced (adding new methods, optimizing existing code, etc.), y is incremented.

When a breaking change is introduced, a new 0.x version cycle is always started.

It is therefore safe to lock your project to a given release cycle, such as 0.1.*.

If you need to upgrade to a newer release cycle, check the release history for a list of changes introduced by each further 0.x.0 version.

## Overview

### Static methods

- fromScalar() - returns enum instance (value) for given scalar
- getAvailableValues() - returns all values for given type
- provideInstances() - implement to return enum instances or automatically implemented by `Grifart\Enum\AutoInstances` trait.

### Instance methods

- toScalar() - return scalar value identifier
- equals() - returns true if the same enum value is passed
- scalarEquals() - returns true if passed scalar value is equal to current value

### Simplest enumeration

```php
/**
 * @method static DayOfWeek MONDAY()
 * @method static DayOfWeek TUESDAY()
 */
final class DayOfWeek extends \Grifart\Enum\Enum
{
    use Grifart\Enum\AutoInstances;
    private const MONDAY = 'monday';
    private const TUESDAY = 'tuesday';
}

$monday = DayOfWeek::MONDAY();
function process(DayOfWeek $day): void { /* ... */ }
````

### Values with behaviour

This way conditions can be replaced by composition. [full code example](tests/Example/LoyaltyProgramExample/example.phpt)

```php
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
````

### Migrating from class constants <small>[[source code](tests/Example/MigratingLegacyCode/readme.md)]</small>

This guide show how to migrate from classes with constants to `\Grifart\Enum` in few simple steps. [Continue to example](tests/Example/MigratingLegacyCode/readme.md)

### Adding behaviour to values <small>[[source code](tests/Example/AddingBehaviourToEnum/readme.md)]</small>

This guide show how to slowly add behaviour to enum values. Step by step. [Continue to example](tests/Example/AddingBehaviourToEnum/readme.md)

### Complex showcase: order lifecycle tracking <small>[[source code](tests/Example/OrderState/readme.md)]</small>

This example contains 5 ways of implementing order state. [Continue to example](tests/Example/OrderState/readme.md).

## Big thanks

- [David Grudl](https://github.com/dg) for making [Nette Tester](https://github.com/nette/tester)
- [Onřej Mirtes](https://github.com/ondrejmirtes) for making [PHP Stan](https://github.com/phpstan/phpstan).

## More reading

- [consistence/consistence enum](https://github.com/consistence/consistence/blob/master/docs/Enum/enums.md)
- [myclabs/php-enum](https://github.com/myclabs/php-enum)
- [Java enum](https://docs.oracle.com/javase/tutorial/java/javaOO/enum.html) (see planet example)
- [great talk about "crafting wicked domain models" from Jimmy Bogard](https://vimeo.com/43598193)
- [more my notes on DDD topic](https://gitlab.grifart.cz/jkuchar1/eventsourcing-cqrs-simple-app/blob/master/README.md)



