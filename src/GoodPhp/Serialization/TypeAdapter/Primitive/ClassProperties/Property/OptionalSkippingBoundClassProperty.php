<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Property;

use GoodPhp\Reflection\Reflector\Reflection\PropertyReflection;
use GoodPhp\Reflection\Type\NamedType;
use GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\MissingValueException;
use function TenantCloud\Standard\Optional\empty_optional;
use TenantCloud\Standard\Optional\Optional;

/**
 * Skips {@see Optional} fields if their value is missing.
 *
 * @template T
 */
class OptionalSkippingBoundClassProperty implements BoundClassProperty
{
	public function __construct(
		private readonly BoundClassProperty $delegate,
		private readonly PropertyReflection $reflection,
	) {
	}

	public static function wrap(PropertyReflection $reflection, BoundClassProperty $property): BoundClassProperty
	{
		if (!$reflection->type() instanceof NamedType || $reflection->type()->name !== Optional::class) {
			return $property;
		}

		return new self(
			delegate: $property,
			reflection: $reflection,
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
		$value = $this->reflection->get($object);

		return $value->hasValue() ?
			$this->delegate->serialize($object) :
			[];
	}

	/**
	 * @inheritDoc
	 */
	public function deserialize(array $data): array
	{
		try {
			return $this->delegate->deserialize($data);
		} catch (MissingValueException) {
			// It looks like MissingValueException might come from a nested ClassProperties adapter, but that's not the case:
			// if a nested adapter throws it, it will be re-thrown as a PropertyMappingException, meaning this catch only
			// works for a missing value on this nesting level, which exactly what we want.

			return [
				$this->reflection->name() => empty_optional(),
			];
		}
	}
}
