<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Property;

/**
 * @template T of object
 */
interface BoundClassProperty
{
	public function serializedName(): string;

	/**
	 * @param T $object
	 */
	public function serialize(object $object): array;

	/**
	 * @param T $into
	 */
	public function deserialize(array $data, object $into): void;
}
