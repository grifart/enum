<?php declare(strict_types=1);

namespace Grifart\Enum;

abstract class Enum
{

	protected function __construct() {}

	abstract public static function provideInstances(): array;

	// Primitive values
	/**
	 * @return string[]|int[]
	 */
	public static function getPrimitiveValues(): array
	{
		$classReflection = new \ReflectionClass(static::class);

		// todo: are really all valid values?
		return $classReflection->getConstants();
	}

	public static function getPrimitiveValueForConstantName(string $key): string
	{
		// todo: error handling
		return static::getPrimitiveValues()[$key];
	}

	private static function getConstantsNames(): array
	{
		return array_keys(self::getPrimitiveValues());
	}

	// Enum instances

	/** @var array|null */
	protected static $instances = [];

	public static function __callStatic(string $constantName, array $arguments)
	{
		\assert(empty($arguments));

		/** @var string|\Grifart\Enum\Enum $constantName */
		return static::getValueForPrimitive(
			self::getPrimitiveValueForConstantName($constantName)
		);
	}

	private static function getInstancesForClass(string $forClass)
	{
		if (!isset(static::$instances[$forClass])) {
			static::$instances[$forClass] = static::provideInstances();
			self::checkAnnotations();
		}
		return static::$instances[$forClass];
	}

	/**
	 * @param string $primitive
	 * @return static
	 */
	public static function getValueForPrimitive(string $primitive) {
		$instances = self::getInstancesForClass(static::class);

		assert(isset($instances[$primitive])); // FIXME: better message
		assert($instances[$primitive] instanceof static);
		return $instances[$primitive];
	}



	private static function getConstantNameForPrimitiveValue($primitiveValue): string
	{
		return \array_search($primitiveValue, static::getPrimitiveValues(), TRUE);
	}

	/**
	 * @return int|string
	 */
	public function getPrimitiveValue()
	{
		return array_search($this, self::getInstancesForClass(static::class), TRUE);
	}

















	// Checking of definition consistency

	private static function checkAnnotations(): void
	{
		$enumReflection = new \ReflectionClass(static::class);

		self::checkCallStaticAnnotations($enumReflection);
		self::checkAllInstancesProvided($enumReflection->getName());
	}

	private static function checkCallStaticAnnotations(\ReflectionClass $enumReflection): void
	{
		$docBlock = $enumReflection->getDocComment();
		$className = $enumReflection->getShortName();
		if ($docBlock === FALSE) {
			$docBlock = '';
		}

		$missingAnnotations = [];
		foreach (static::getConstantsNames() as $constantName) {
			$desiredAnnotation = "@method static $className $constantName()";
			if (stripos($docBlock, $desiredAnnotation) === false) {
				$missingAnnotations[] = $desiredAnnotation;
			}
		}

		if(!empty($missingAnnotations)) {
			$properDoc = "/**\n * " . implode("\n * ", $missingAnnotations) . "\n */\n";
			throw new \LogicException("You forgotten to add @method annotations for enum {$enumReflection->getName()}. Missing ones are: \n$properDoc");
		}

		// todo: @method annotations without constants
	}

	private static function checkAllInstancesProvided(string $className): void
	{
		// todo: instances without constants
		$instances = self::getInstancesForClass($className);
		foreach(static::getPrimitiveValues() as $primitiveValue) {
			$constantName = static::getConstantNameForPrimitiveValue($primitiveValue);
			if (!isset($instances[$primitiveValue])) {
				throw new \LogicException("You have forgotten to provide instance for $constantName.");
			}
		}
	}


}
