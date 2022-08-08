<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\Acceptance;

use GoodPhp\Reflection\Type\NamedType;
use GoodPhp\Reflection\Type\Type;
use GoodPhp\Reflection\Util\IsSingleton;
use GoodPhp\Serialization\Serializer;

class BaseTypeEqualsAcceptanceStrategy implements AcceptanceStrategy
{
	use IsSingleton;

	public function accepts(Type $mapperType, Type $type, Serializer $serializer): bool
	{
		return $mapperType instanceof NamedType &&
			$type instanceof NamedType &&
			$mapperType->name === $type->name;
	}
}
