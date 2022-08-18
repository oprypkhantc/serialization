<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties;

use GoodPhp\Reflection\Reflector\Reflection\ClassReflection;
use GoodPhp\Serialization\TypeAdapter\Exception\MultipleMappingException;
use GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Constructing\ObjectFactory;
use GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Property\BoundClassProperty;
use GoodPhp\Serialization\TypeAdapter\Primitive\PrimitiveTypeAdapter;
use Illuminate\Support\Collection;

/**
 * @template T
 */
final class ClassPropertiesPrimitiveTypeAdapter implements PrimitiveTypeAdapter
{
	/**
	 * @param Collection<int, BoundClassProperty> $properties
	 * @param ObjectFactory<T>                    $objectFactory
	 */
	public function __construct(
		private readonly Collection $properties,
		private readonly ClassReflection $reflection,
		private readonly ObjectFactory $objectFactory,
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function serialize(mixed $value): mixed
	{
		return MultipleMappingException::map(
			$this->properties,
			true,
			fn (BoundClassProperty $property) => PropertyMappingException::rethrow(
				$property,
				fn () => $property->serialize($value)
			)
		);
	}

	/**
	 * @inheritDoc
	 */
	public function deserialize(mixed $value): mixed
	{
		$data = MultipleMappingException::map(
			$this->properties,
			true,
			fn (BoundClassProperty $property) => PropertyMappingException::rethrow(
				$property,
				fn () => $property->deserialize($value)
			)
		);

		return $this->objectFactory->create($this->reflection, $data);
	}
}
