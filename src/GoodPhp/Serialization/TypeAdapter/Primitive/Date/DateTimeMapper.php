<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\Date;

use DateTime;
use DateTimeInterface;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\MapFrom;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\MapTo;
use GoodPhp\Serialization\TypeAdapter\Primitive\PrimitiveTypeAdapter;

final class DateTimeMapper
{
	#[MapTo(PrimitiveTypeAdapter::class)]
	public function to(DateTime $value): string
	{
		return $value->format(DateTimeInterface::RFC3339_EXTENDED);
	}

	#[MapFrom(PrimitiveTypeAdapter::class)]
	public function from(string $value): DateTime
	{
		return new DateTime($value);
	}
}
