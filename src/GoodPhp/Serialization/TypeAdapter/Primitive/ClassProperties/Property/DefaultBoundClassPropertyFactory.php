<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Property;

use GoodPhp\Reflection\Reflector\Reflection\PropertyReflection;
use GoodPhp\Serialization\Serializer;

class DefaultBoundClassPropertyFactory implements BoundClassPropertyFactory
{
	public function create(PropertyReflection $property, string $serializedName, string $typeAdapterType, Serializer $serializer): BoundClassProperty
	{
		return OptionalSkippingBoundClassProperty::wrap(
			DefaultValueSkippingBoundClassProperty::wrap(
				DirectlyBoundClassProperty::from($property, $serializedName, $typeAdapterType, $serializer)
			)
		);
	}
}
