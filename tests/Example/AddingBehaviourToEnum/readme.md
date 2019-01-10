# Adding behaviour to enum

Let's **refactor** following existing code:

[step-1.phpt](step-1.phpt)

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


[step-2.phpt](step-2.phpt)

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
			new class(self::MONDAY) extends DayOfWeek
			{
				public function nextDay(): DayOfWeek
				{
					return DayOfWeek::TUESDAY();
				}
			},

			new class(self::TUESDAY) extends DayOfWeek
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
