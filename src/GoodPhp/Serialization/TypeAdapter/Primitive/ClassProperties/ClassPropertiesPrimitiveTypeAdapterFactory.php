<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties;

use GoodPhp\Reflection\Reflector\Reflection\ClassReflection;
use GoodPhp\Reflection\Reflector\Reflection\PropertyReflection;
use GoodPhp\Reflection\Type\NamedType;
use GoodPhp\Reflection\Type\Type;
use GoodPhp\Serialization\Serializer;
use GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Naming\NamingStrategy;
use GoodPhp\Serialization\TypeAdapter\Primitive\PrimitiveTypeAdapter;
use GoodPhp\Serialization\TypeAdapter\TypeAdapterFactory;
use TenantCloud\Standard\Optional\Optional;

final class ClassPropertiesPrimitiveTypeAdapterFactory implements TypeAdapterFactory
{
	public function __construct(
		private readonly NamingStrategy $namingStrategy,
		private readonly ObjectClassFactory $objectClassFactory,
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
			fn () => $this->objectClassFactory->create($reflection->qualifiedName()),
			$reflection->properties()->map(function (PropertyReflection $property) use ($serializer, $typeAdapterType, $attributes) {
				$attributes = $property->attributes()->all();
				$serializedName = $this->namingStrategy->translate($property->name(), $attributes);

				return PropertyMappingException::rethrow($serializedName, fn () => new BoundClassProperty(
					reflection: $property,
					typeAdapter: $serializer->adapter(
						$typeAdapterType,
						$property->type(),
						$attributes
					),
					serializedName: $serializedName,
					optional: $property->type() instanceof NamedType && $property->type()->name === Optional::class,
					hasDefaultValue: $property->hasDefaultValue(),
				));
			})
		);
	}
}
