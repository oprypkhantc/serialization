<?php

namespace GoodPhp\Serialization\TypeAdapter;

use GoodPhp\Reflection\Type\Type;
use GoodPhp\Serialization\Serializer;

/**
 * @template T of TypeAdapter
 */
interface TypeAdapterFactory
{
	/**
	 * @param class-string<TypeAdapter> $typeAdapterType
	 * @param object[]                  $attributes
	 *
	 * @return T|null
	 */
	public function create(string $typeAdapterType, Type $type, array $attributes, Serializer $serializer);
}
