<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\MapperMethodTypeSubstiter;

use Closure;
use GoodPhp\Reflection\Reflector\Reflection\MethodReflection;
use GoodPhp\Reflection\Type\Type;

final class MapperMethodTypeSubstituterFactory
{
	/**
	 * @param Closure(MethodReflection): Type $methodValueType
	 */
	public function fromReflection(MethodReflection $reflection, Closure $methodValueType): MapperMethodTypeSubstituter
	{
		return new StaticMapperMethodTypeSubstrituter($reflection);

		return $reflection->typeParameters()->isEmpty() ?
			new StaticMapperMethodTypeSubstrituter($reflection) :
			new GenericMapperMethodTypeSubstituter($reflection, $methodValueType);
	}
}
