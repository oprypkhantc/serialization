<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Property;

use GoodPhp\Reflection\Reflector\Reflection\PropertyReflection;

/**
 * @template T of object
 */
interface BoundClassProperty
{
	public function reflection(): PropertyReflection;

	public function serializedName(): string;

	/**
	 * @param T $object
	 */
	public function serialize(object $object): array;

	/**
	 * @param T $into
	 */
	public function deserialize(array $data): array;
}
