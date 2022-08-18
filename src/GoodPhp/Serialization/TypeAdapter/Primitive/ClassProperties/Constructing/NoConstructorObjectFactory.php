<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Constructing;

use GoodPhp\Reflection\Reflector\Reflection\ClassReflection;

final class NoConstructorObjectFactory implements ObjectFactory
{
	/**
	 * @inheritDoc
	 */
	public function create(ClassReflection $reflection): object
	{
		return $reflection->newInstanceWithoutConstructor();
	}
}
