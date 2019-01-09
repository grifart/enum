<?php declare(strict_types=1);

namespace Grifart\Enum\Internal;

use Grifart\Enum\UsageException;

/**
 * Checks if registering enum does not contain error.
 */
final class ConsistencyChecker
{
	public static function checkAnnotations(Meta $enumMeta): void
	{
		self::checkCallStaticAnnotations($enumMeta);
		self::checkAllInstancesProvided($enumMeta);
	}

	private static function checkCallStaticAnnotations(Meta $enumMeta): void
	{
		$enumReflection = $enumMeta->getClassReflection();

		$docBlock = $enumReflection->getDocComment();
		$className = $enumReflection->getShortName();
		if ($docBlock === false) {
			$docBlock = '';
		}

		$missingAnnotations = [];
		foreach ($enumMeta->getConstantNames() as $constantName) {
			$desiredAnnotation = "@method static $className $constantName()";
			if (stripos($docBlock, $desiredAnnotation) === false) {
				$missingAnnotations[] = $desiredAnnotation;
			}
		}

		if (\count($missingAnnotations) !== 0) {
			$properDoc = "/**\n * " . implode("\n * ", $missingAnnotations) . "\n */\n";
			throw new UsageException("You have forgotten to add @method annotations for enum '{$enumReflection->getName()}'. Documentation block should contain: \n$properDoc");
		}
		// todo: @method annotations without constants
	}

	private static function checkAllInstancesProvided(Meta $enumMeta): void
	{
		// todo: instances without constants

		foreach ($enumMeta->getScalarValues() as $scalarValue) {
			if (!$enumMeta->hasValueForScalar($scalarValue)) {
				$constantName = $enumMeta->getConstantNameForScalar($scalarValue);
				throw new UsageException("You have forgotten to provide instance for $constantName.");
			}
		}
	}
}
