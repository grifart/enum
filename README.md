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

## Let code speak: individual behaviour for each value

Let's **refactor** following existing code:

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
    // ...

}

$monday = DayOfWeek::MONDAY();

function nextDay(DayOfWeek $dayOfWeek): DayOfWeek
{
	switch($dayOfWeek) {
		case DayOfWeek::MONDAY():
			return DayOfWeek::TUESDAY();
    
        case DayOfWeek::TUESDAY():
			// ...
	
	}
    throw new ShouldNotHappenException();
}

$tuesday = nextDay($monday);
```

Look at function `nextDay()`, it is really concerned with `DayOfWeek` and nothing else. It probably should be part of `DayOfWeek`. Let's try to push behaviour down.

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
    // ...

    public function nextDay(): self
    {
    	
        switch($this) {
            case self::MONDAY():
                return self::TUESDAY();
        
            case self::TUESDAY():
                // ...
        
        }
        throw new ShouldNotHappenException();
    }
    
}
```

Cool, lets try to use this enum in our application:

```php
$monday = DayOfWeek::MONDAY();
$tuesday = $monday->nextDay();
```

Public API now looks much better! Asking monday, what is the next day and it knows answer. ✅

However I'm still worried about code of `nextDay()` function.

- ❌ There are still ugly `if`s which are hard to read.
- ❌ I have to worry about case when **someone adds new value** to this enum and forgets to update `nextDay()` method.

Both of these can be solved by **using composition** instead of just value and switches.

What if every value of enum would be separate class? Then we can write behaviour for each day individually making code very simple. And if someone adds new value, type-system will force him to add all required behaviour. And `grifart/enum` makes this easy, you do not have declare separate class for every value, just use anonymous classes.

```php
/**
 * @method static DayOfWeek MONDAY()
 * @method static DayOfWeek TUESDAY()
 */
abstract class DayOfWeek extends \Grifart\Enum\Enum
{

	protected const MONDAY = 'monday';
	protected const TUESDAY = 'tuesday';
	// ...

	abstract public function nextDay(): self;


	/** @return static[] */
	protected static function provideInstances(): array
	{
		return [
			self::MONDAY => new class extends DayOfWeek
			{
				public function nextDay(): DayOfWeek
				{
					return DayOfWeek::TUESDAY();
				}
			},

			self::TUESDAY => new class extends DayOfWeek
			{
				public function nextDay(): DayOfWeek
				{
					return DayOfWeek::WEDNESDAY();
				}
			},
		];
	}
}
```

Now type-system knows that every enum value must have method `nextDay()` with return type of self ✅. Please note that this is completely internal thing - **public API haven't changed**! And we got rid of all `if`s and `switch`es ✅.

This approach is very useful when one wants to implement anything state-machine related (see tests for more examples, they are simple and easy to read). 

More use cases:
- order state (new, in progress, delivering, delivered) and relations between them
- day of week
- tracking life-cycle

## Migrating legacy code

This guide show how to migrate from classes with constants to `\Grifart\Enum` with ease. [Continue to example](tests/Example/MigratingLegacyCode/readme.md)

## Big thanks

- [David Grudl](https://github.com/dg) for making [Nette Tester](https://github.com/nette/tester)
- [Onřej Mirtes](https://github.com/ondrejmirtes) for making [PHP Stan](https://github.com/phpstan/phpstan).

## More reading

- [consistence/consistence enum](https://github.com/consistence/consistence/blob/master/docs/Enum/enums.md)
- [myclabs/php-enum](https://github.com/myclabs/php-enum)
- [Java enum](https://docs.oracle.com/javase/tutorial/java/javaOO/enum.html) (see planet example)
- [great talk about "crafting wicked domain models" from Jimmy Bogard](https://vimeo.com/43598193)
- [more my notes on DDD topic](https://gitlab.grifart.cz/jkuchar1/eventsourcing-cqrs-simple-app/blob/master/README.md)



