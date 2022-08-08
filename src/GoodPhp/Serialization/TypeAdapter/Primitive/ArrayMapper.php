<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive;

use GoodPhp\Reflection\Type\Type;
use GoodPhp\Serialization\Serializer;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\MapFrom;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\MapTo;

final class ArrayMapper
{
	/**
	 * @template T
	 *
	 * @param array<T> $value
	 *
	 * @return array<mixed>
	 */
	#[MapTo(PrimitiveTypeAdapter::class)]
	public function to(array $value, Type $type, Serializer $serializer): array
	{
		$itemAdapter = $serializer->adapter(PrimitiveTypeAdapter::class, $type->arguments[1]);

		return array_map(fn ($item) => $itemAdapter->serialize($item), $value);
	}

	/**
	 * @template T
	 *
	 * @param array<mixed> $value
	 *
	 * @return array<T>
	 */
	#[MapFrom(PrimitiveTypeAdapter::class)]
	public function from(array $value, Type $type, Serializer $serializer): array
	{
		$itemAdapter = $serializer->adapter(PrimitiveTypeAdapter::class, $type->arguments[1]);

		return array_map(fn ($item) => $itemAdapter->deserialize($item), $value);
	}
}
