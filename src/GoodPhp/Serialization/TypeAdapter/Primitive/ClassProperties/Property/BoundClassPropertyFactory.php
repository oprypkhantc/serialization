<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Property;

use GoodPhp\Reflection\Reflector\Reflection\PropertyReflection;
use GoodPhp\Serialization\Serializer;

interface BoundClassPropertyFactory
{
	public function create(PropertyReflection $property, string $serializedName, string $typeAdapterType, Serializer $serializer): BoundClassProperty;
}
