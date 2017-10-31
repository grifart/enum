<?php declare(strict_types=1);

namespace Grifart\Enum;

final class ConsistencyChecker
{

	public static function checkAnnotations(Meta $enumMeta): void
	{
		$enumReflection = new \ReflectionClass($enumMeta->getClass());

		self::checkCallStaticAnnotations($enumMeta, $enumReflection);
		self::checkAllInstancesProvided($enumMeta, $enumReflection->getName());
	}

	private static function checkCallStaticAnnotations(Meta $enumMeta, \ReflectionClass $enumReflection): void
	{
		$docBlock = $enumReflection->getDocComment();
		$className = $enumReflection->getShortName();
		if ($docBlock === FALSE) {
			$docBlock = '';
		}

		$missingAnnotations = [];
		foreach ($enumMeta->getConstantNames() as $constantName) {
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

	private static function checkAllInstancesProvided(Meta $enumMeta, string $className): void
	{
		// todo: instances without constants

		$instances = $enumMeta->getValues();
		foreach($enumMeta->getScalarValues() as $scalarValue) {
			if (!$enumMeta->hasValueForScalar($scalarValue)) {
				$constantName = $enumMeta->getConstantNameForScalar($scalarValue);
				throw new \LogicException("You have forgotten to provide instance for $constantName.");
			}
		}
	}
}
