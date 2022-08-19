<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Property;

use Closure;
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
		private readonly Closure $defaultValue,
	) {
	}

	public static function wrap(BoundClassProperty $property): BoundClassProperty
	{
		if ($property->reflection()->hasDefaultValue()) {
			return new self($property, fn () => $property->reflection()->defaultValue());
		}

		if ($property->reflection()->promotedParameter() && $property->reflection()->promotedParameter()->hasDefaultValue()) {
			return new self($property, fn () => $property->reflection()->promotedParameter()->defaultValue());
		}

		return $property;
	}

	public function reflection(): PropertyReflection
	{
		return $this->delegate->reflection();
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
	public function deserialize(array $data): array
	{
		try {
			return $this->delegate->deserialize($data);
		} catch (MissingValueException) {
			return [
				$this->reflection()->name() => ($this->defaultValue)(),
			];
		}
	}
}
