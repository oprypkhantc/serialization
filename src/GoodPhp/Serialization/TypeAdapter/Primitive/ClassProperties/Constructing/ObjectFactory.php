<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Constructing;

use GoodPhp\Reflection\Reflector\Reflection\ClassReflection;

interface ObjectFactory
{
	/**
	 * @template T
	 *
	 * @param ClassReflection<T>   $reflection
	 * @param array<string, mixed> $data
	 *
	 * @return T
	 */
	public function create(ClassReflection $reflection, array $data): object;
}
