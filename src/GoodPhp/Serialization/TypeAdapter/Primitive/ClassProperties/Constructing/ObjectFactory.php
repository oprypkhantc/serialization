<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Constructing;

use GoodPhp\Reflection\Reflector\Reflection\ClassReflection;

interface ObjectFactory
{
	/**
	 * @template T
	 *
	 * @param ClassReflection<T> $reflection
	 *
	 * @return T
	 */
	public function create(ClassReflection $reflection): object;
}
