# Migrating legacy code to `\Grifart\Enum`

This is step-by-step guide how to migrate you legacy code to `\Grifart\Enum`.

We will start with non-type safe enum represented by class with constants. [[full source code](step0.phpt)]

```php
class OrderState {
	public const NEW = 'new';
	public const PROCESSING = 'processing';
}
```

Our business logic is this:

```php
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
```

## Step 1: add new type-safe API [[source](step1.phpt)]

This is done by

- extending `\Grifart\Enum\Enum` class
- by automatically implementing enum values by including `use \Grifart\Enum\AutoInstances;` trait
- and by adding magic methods annotations

There is not backward incompatible change introduced. And now you can use new APIs!


```php
/**
 * @method static OrderState NEW()
 * @method static OrderState PROCESSING()
 */
class OrderState extends \Grifart\Enum\Enum {
	use \Grifart\Enum\AutoInstances;
	public const NEW = 'new';
	public const PROCESSING = 'processing';
}
```

## Step 2: Migrating existing code to new API [[source](step2.phpt)]

Migrating old code to new API is usually easy, just add parenthesis `()` when you access value.

```php
$state = OrderState::NEW();

$result = '';
switch ($state) {
	// your business logic
	case OrderState::NEW():
		$result = 'new';
		break;
	case OrderState::PROCESSING():
		$result = 'processing';
		break;
}

Assert::same('new', $result);
```

Please note, that you will need to handle some cases manually as `OrderState::NEW()` returns object, enum instance, not a string.

#### Removing old API

So when you are finally ready to remove old API, just change constant visibility to `private`.

```php
/**
 * @method static OrderState NEW()
 * @method static OrderState PROCESSING()
 */
class OrderState extends \Grifart\Enum\Enum {
	use \Grifart\Enum\AutoInstances;
	private const NEW = 'new';
	private const PROCESSING = 'processing';
}
```

## Step 3: Enjoy new features [[source](step3.phpt)]

Now, when you decided that you what to move your business logic inside enum declaration. You are now free to do so. And there are many more options, see other examples.


