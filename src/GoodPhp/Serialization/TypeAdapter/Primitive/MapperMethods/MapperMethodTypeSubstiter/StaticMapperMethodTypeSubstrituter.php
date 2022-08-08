<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\MapperMethodTypeSubstiter;

use GoodPhp\Reflection\Reflector\Reflection\MethodReflection;
use GoodPhp\Reflection\Type\Type;

final class StaticMapperMethodTypeSubstrituter implements MapperMethodTypeSubstituter
{
	public function __construct(private readonly MethodReflection $reflection)
	{
	}

	public function resolve(Type $valueType): MethodReflection
	{
		return $this->reflection;
	}
}
