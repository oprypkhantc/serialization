<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties;

use Closure;
use GoodPhp\Serialization\TypeAdapter\Primitive\PrimitiveTypeAdapter;
use Illuminate\Support\Collection;

/**
 * @template T
 */
final class ClassPropertiesPrimitiveTypeAdapter implements PrimitiveTypeAdapter
{
	/**
	 * @param Closure(): T                        $newInstance
	 * @param Collection<int, BoundClassProperty> $properties
	 */
	public function __construct(
		private readonly Closure $newInstance,
		private readonly Collection $properties,
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function serialize(mixed $value): mixed
	{
		return $this->properties
			->mapWithKeys(
				fn (BoundClassProperty $property) => PropertyMappingException::rethrow(
					$property->reflection,
					fn () => $property->serialize($value)
				)
			)
			->toArray();
	}

	/**
	 * @inheritDoc
	 */
	public function deserialize(mixed $value): mixed
	{
		$object = ($this->newInstance)();

		foreach ($this->properties as $property) {
			PropertyMappingException::rethrow($property->reflection, function () use ($object, $property, $value) {
				$property->deserialize($value, $object);
			});
		}

		return $object;
	}
}
