<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\MapperMethodTypeSubstiter;

use Closure;
use GoodPhp\Reflection\Reflector\Reflection\MethodReflection;
use GoodPhp\Reflection\Type\Template\TypeParameterMap;
use GoodPhp\Reflection\Type\Type;

final class GenericMapperMethodTypeSubstituter implements MapperMethodTypeSubstituter
{
	/**
	 * @param Closure(MethodReflection): Type $methodValueType
	 */
	public function __construct(private readonly MethodReflection $reflection, private readonly Closure $methodValueType)
	{
	}

	public function resolve(Type $valueType): MethodReflection
	{
//		$typeMap = new TypeParameterMap([]);
//		$typeMap = $typeMap->union(
//			($this->methodValueType)($this->reflection)->inferTemplateTypes($valueType)
//		);

		return $this->reflection;
//		return $this->reflection->withTemplateTypeMap(
//			($this->methodValueType)($this->reflection)->inferTemplateTypes($valueType)
//		);
	}
}
