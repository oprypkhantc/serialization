<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\PhpStandard;

use GoodPhp\Reflection\Type\Type;
use GoodPhp\Serialization\Serializer;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\MapFrom;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\MapTo;
use GoodPhp\Serialization\TypeAdapter\Primitive\PrimitiveTypeAdapter;
use TenantCloud\Standard\Optional\Optional;
use function TenantCloud\Standard\Optional\optional;

class OptionalMapper
{
	/**
	 * @template TValue
	 *
	 * @param Optional<TValue> $value
	 *
	 * @return TValue
	 */
	#[MapTo(PrimitiveTypeAdapter::class)]
	public function to(Optional $value, Type $type, Serializer $serializer): mixed
	{
		$valueAdapter = $serializer->adapter(PrimitiveTypeAdapter::class, $type->arguments[0]);

		return $valueAdapter->serialize($value->value());
	}

	/**
	 * @template TValue
	 *
	 * @param TValue $value
	 *
	 * @return Optional<TValue>
	 */
	#[MapFrom(PrimitiveTypeAdapter::class)]
	public function from(mixed $value, Type $type, Serializer $serializer): Optional
	{
		$valueAdapter = $serializer->adapter(PrimitiveTypeAdapter::class, $type->arguments[0]);

		return optional($valueAdapter->deserialize($value));
	}
}
