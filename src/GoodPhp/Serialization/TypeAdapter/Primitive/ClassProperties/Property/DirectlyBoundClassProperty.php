<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Property;

use GoodPhp\Reflection\Reflector\Reflection\PropertyReflection;
use GoodPhp\Serialization\Serializer;
use GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\MissingValueException;
use GoodPhp\Serialization\TypeAdapter\TypeAdapter;
use Illuminate\Support\Arr;

/**
 * Directly binds a property to a specific key in the data one-to-one.
 *
 * @template T of object
 */
final class DirectlyBoundClassProperty implements BoundClassProperty
{
	public function __construct(
		private readonly PropertyReflection $reflection,
		private readonly TypeAdapter $typeAdapter,
		public readonly string $serializedName,
	) {
	}

	public static function from(PropertyReflection $reflection, string $serializedName, string $typeAdapterType, Serializer $serializer): self
	{
		return new self(
			reflection: $reflection,
			typeAdapter: $serializer->adapter(
				$typeAdapterType,
				$reflection->type(),
				$reflection->attributes()->all(),
			),
			serializedName: $serializedName
		);
	}

	public function serializedName(): string
	{
		return $this->serializedName;
	}

	/**
	 * @inheritDoc
	 */
	public function serialize(object $object): array
	{
		return [
			$this->serializedName => $this->typeAdapter->serialize(
				$this->reflection->get($object)
			),
		];
	}

	/**
	 * @inheritDoc
	 */
	public function deserialize(array $data): array
	{
		if (!Arr::has($data, $this->serializedName)) {
			throw new MissingValueException();
		}

		return [
			$this->reflection->name() => $this->typeAdapter->deserialize(
				$data[$this->serializedName]
			),
		];
	}
}
