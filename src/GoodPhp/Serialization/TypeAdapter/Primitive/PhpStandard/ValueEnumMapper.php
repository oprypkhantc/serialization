<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\PhpStandard;

use GoodPhp\Reflection\Type\Type;
use GoodPhp\Serialization\TypeAdapter\Primitive\BuiltIn\Exceptions\UnexpectedEnumValueException;
use GoodPhp\Serialization\TypeAdapter\Primitive\BuiltIn\Exceptions\UnexpectedValueTypeException;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\Acceptance\BaseTypeAcceptedByAcceptanceStrategy;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\MapFrom;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\MapTo;
use GoodPhp\Serialization\TypeAdapter\Primitive\PrimitiveTypeAdapter;
use TenantCloud\Standard\Enum\ValueEnum;
use TenantCloud\Standard\Enum\ValueNotFoundException;

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
	 */
	#[MapTo(PrimitiveTypeAdapter::class, new BaseTypeAcceptedByAcceptanceStrategy(ValueEnum::class))]
	public function to(ValueEnum $value): string|int
	{
		return $value->value();
	}

	/**
	 * @template TEnumValue
	 * @template TEnum of ValueEnum<TEnumValue>
	 *
	 * @return TEnum
	 */
	#[MapFrom(PrimitiveTypeAdapter::class, new BaseTypeAcceptedByAcceptanceStrategy(ValueEnum::class))]
	public function from(mixed $value, Type $type): ValueEnum
	{
		$enumClass = $type->name;

		// If given a non-string-int value, just throw an exception without the value as to not clutter it with possibly huge values.
		if (!is_string($value) && !is_int($value)) {
			throw new UnexpectedValueTypeException($value, ['string', 'int']);
		}

		try {
			return $enumClass::fromValue($value);
		} catch (ValueNotFoundException) {
			throw new UnexpectedEnumValueException($value, $enumClass::values());
		}
	}
}
