<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Property;

use GoodPhp\Reflection\Reflector\Reflection\PropertyReflection;
use GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\MissingValueException;

/**
 * Skips fields with a default value if their value is missing.
 *
 * @template T
 */
class DefaultValueSkippingBoundClassProperty implements BoundClassProperty
{
	public function __construct(
		private readonly BoundClassProperty $delegate,
	) {
	}

	public static function wrap(PropertyReflection $reflection, BoundClassProperty $property): BoundClassProperty
	{
		if (!$reflection->hasDefaultValue()) {
			return $property;
		}

		return new self(
			delegate: $property,
		);
	}

	public function serializedName(): string
	{
		return $this->delegate->serializedName();
	}

	/**
	 * @inheritDoc
	 */
	public function serialize(object $object): array
	{
		return $this->delegate->serialize($object);
	}

	/**
	 * @inheritDoc
	 */
	public function deserialize(array $data, object $into): void
	{
		try {
			$this->delegate->deserialize($data, $into);
		} catch (MissingValueException) {
		}
	}
}
