<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Constructing;

use GoodPhp\Reflection\Reflector\Reflection\ClassReflection;
use GoodPhp\Reflection\Reflector\Reflection\PropertyReflection;

final class NoConstructorPropertySetObjectFactory implements ObjectFactory
{
	/**
	 * @inheritDoc
	 */
	public function create(ClassReflection $reflection, array $data): object
	{
		$object = $reflection->newInstanceWithoutConstructor();

		foreach ($data as $key => $value) {
			$property = $reflection->properties()->first(fn (PropertyReflection $property) => $key === $property->name());

			$property->set($object, $value);
		}

		return $object;
	}
}
