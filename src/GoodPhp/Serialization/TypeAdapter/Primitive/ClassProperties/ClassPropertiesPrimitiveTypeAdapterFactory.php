<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties;

use GoodPhp\Reflection\Reflector\Reflection\ClassReflection;
use GoodPhp\Reflection\Reflector\Reflection\PropertyReflection;
use GoodPhp\Reflection\Type\NamedType;
use GoodPhp\Reflection\Type\Type;
use GoodPhp\Serialization\Serializer;
use GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Constructing\ObjectFactory;
use GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Naming\NamingStrategy;
use GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Property\BoundClassPropertyFactory;
use GoodPhp\Serialization\TypeAdapter\Primitive\PrimitiveTypeAdapter;
use GoodPhp\Serialization\TypeAdapter\TypeAdapterFactory;

final class ClassPropertiesPrimitiveTypeAdapterFactory implements TypeAdapterFactory
{
	public function __construct(
		private readonly NamingStrategy $namingStrategy,
		private readonly ObjectFactory $objectFactory,
		private readonly BoundClassPropertyFactory $boundClassPropertyFactory,
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function create(string $typeAdapterType, Type $type, array $attributes, Serializer $serializer)
	{
		if ($typeAdapterType !== PrimitiveTypeAdapter::class || !$type instanceof NamedType) {
			return null;
		}

		$reflection = $serializer->reflector->forNamedType($type);

		if (!$reflection instanceof ClassReflection) {
			return null;
		}

		return new ClassPropertiesPrimitiveTypeAdapter(
			$reflection->properties()->map(function (PropertyReflection $property) use ($reflection, $serializer, $typeAdapterType) {
				$serializedName = $this->namingStrategy->translate($property->name(), $property->attributes(), $reflection->attributes());

				return PropertyMappingException::rethrow($serializedName, fn () => $this->boundClassPropertyFactory->create(
					property: $property,
					serializedName: $serializedName,
					typeAdapterType: $typeAdapterType,
					serializer: $serializer
				));
			}),
			$reflection,
			$this->objectFactory,
		);
	}
}
