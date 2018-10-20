# grifart/enum

repositories: [GRIFART GitLab](https://gitlab.grifart.cz/jkuchar1/grifart-enum), [GitHub](https://github.com/grifart/enum)

Enums represent predefined set of values. The available values are defined statically by each enum class. Each value is represented by an instance of this class in a flyweight manner.

- This enum allows you to add individal behaviour to every enum value (as in Java). This allows you to transform your `switch`es/`if`s into more readable composition. (see example bellow)
- Checks enum annotations if phpdoc-declared methods are properly declared (will generate docblock for you in exception)

Common with other enum implementations:

- You can type-hint: `function setCurrentDay(DayOfWeek $day) {`
- You can get a list of all the possible values

## Let code speak: individual behaviour for each value

Let's **refactor** following existing code:

```php
/**
 * @method static DayOfWeek MONDAY()
 * @method static DayOfWeek THURESDAY()
 */
final class DayOfWeek extends \Grifart\Enum\Enum
{
    use Grifart\Enum\AutoInstances;

    private const MONDAY = 'monday';
    private const THURESDAY = 'thuresday';
    // ...

}

$monday = DayOfWeek::MONDAY();

function nextDay(DayOfWeek $dayOfWeek): DayOfWeek
{
    if($dayOfWeek === DayOfWeek::MONDAY()) {
        return DayOfWeek::THURESDAY();
    } else if (...) {
        ...
    }

    throw new ShouldNotHappendException();
}

$thuresday = nextDay($monday);
```

Look at function `nextDay()`, it is really concerned with `DayOfWeek` and nothing else. It probably should be part of `DayOfWeek`. Let's try to push behaviour down.

```php
/**
 * @method static DayOfWeek MONDAY()
 * @method static DayOfWeek THURESDAY()
 */
final class DayOfWeek extends \Grifart\Enum\Enum
{
    use Grifart\Enum\AutoInstances;

    private const MONDAY = 'monday';
    private const THURESDAY = 'thuresday';
    // ...

    public function nextDay(): self
    {
        if($this === self::MONDAY()) {
            return self::THURESDAY();
        } else if (...) {
            ...
        }

        throw new ShouldNotHappendException();
    }
    
}
```

Cool, lets try to use this enum in our application:

```php
$monday = DayOfWeek::MONDAY();
$thuresday = $monday->nextDay();
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
 * @method static DayOfWeek THURESDAY()
 */
abstract class DayOfWeek extends \Grifart\Enum\Enum
{

	protected const MONDAY = 'monday';
	protected const THURESDAY = 'thuresday';
    // ...

    public abstract function nextDay(): self;

    
    /** @return static[] */
	protected static function provideInstances(): array
	{
		return [
			self::MONDAY => new class extends DayOfWeek
			{
				public function nextDay(): DayOfWeek
				{
					return DayOfWeek::THURESDAY();
				}
			},

			self::THURESDAY => new class extends DayOfWeek
			{
				public function nextDay(): DayOfWeek
				{
					return return DayOfWeek::WEDNESDAY();
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

## More reading

- [consistence/consistence enum](https://github.com/consistence/consistence/blob/master/docs/Enum/enums.md)
- [myclabs/php-enum](https://github.com/myclabs/php-enum)
- [Java enum](https://docs.oracle.com/javase/tutorial/java/javaOO/enum.html) (see planet example)
- [great talk about "crafting wicked domain models" from Jimmy Bogard](https://vimeo.com/43598193)
- [more my notes on DDD topic](https://gitlab.grifart.cz/jkuchar1/eventsourcing-cqrs-simple-app/blob/master/README.md)



