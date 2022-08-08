<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties;

use ReflectionClass;

final class ObjectClassFactory
{
	/**
	 * @template T
	 *
	 * @param class-string<T> $className
	 *
	 * @return T
	 */
	public function create(string $className): object
	{
		return (new ReflectionClass($className))->newInstanceWithoutConstructor();
	}
}
