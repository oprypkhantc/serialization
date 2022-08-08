<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive;

use GoodPhp\Reflection\Type\Type;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\Acceptance\BaseTypeAcceptedByAcceptanceStrategy;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\MapFrom;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\MapTo;
use TenantCloud\Standard\Enum\ValueEnum;

/**
 * {@see ValueEnum}.
 */
final class ValueEnumMapper
{
	/**
	 * @template TEnumValue
	 * @template TEnum of ValueEnum<TEnumValue>
	 *
	 * @param TEnum                            $value
	 * @param PrimitiveTypeAdapter<TEnumValue> $valueAdapter
	 *
	 * @return string|int
	 */
	#[MapTo(PrimitiveTypeAdapter::class, new BaseTypeAcceptedByAcceptanceStrategy(ValueEnum::class))]
	public function to(ValueEnum $value): mixed
	{
		return $value->value();
	}

	/**
	 * @template TEnumValue
	 * @template TEnum of ValueEnum<TEnumValue>
	 *
	 * @param string|int $value
	 *
	 * @return TEnum
	 */
	#[MapFrom(PrimitiveTypeAdapter::class, new BaseTypeAcceptedByAcceptanceStrategy(ValueEnum::class))]
	public function from(string|int $value, Type $type): ValueEnum
	{
		$enumClass = $type->name;

		return $enumClass::fromValue($value);
	}
}
