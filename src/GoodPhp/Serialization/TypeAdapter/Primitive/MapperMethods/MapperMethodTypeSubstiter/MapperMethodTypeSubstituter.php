<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\MapperMethodTypeSubstiter;

use GoodPhp\Reflection\Reflector\Reflection\MethodReflection;
use GoodPhp\Reflection\Type\Type;

interface MapperMethodTypeSubstituter
{
	public function resolve(Type $valueType): MethodReflection;
}
