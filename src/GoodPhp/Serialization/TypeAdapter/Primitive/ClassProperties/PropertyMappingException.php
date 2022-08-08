<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties;

use Exception;
use GoodPhp\Reflection\Reflector\Reflection\PropertyReflection;
use RuntimeException;
use Throwable;

class PropertyMappingException extends RuntimeException
{
	public function __construct(
		Throwable $previous,
		public readonly string $path
	) {
		parent::__construct("Could not map property at path '{$this->path}'", 0, $previous);
	}

	public static function rethrow(PropertyReflection $property, callable $callback): mixed
	{
		try {
			return $callback();
		} catch (PropertyMappingException $e) {
			throw new self($e->getPrevious(), $property->name() . '.' . $e->path);
		} catch (Exception $e) {
			throw new self($e, $property->name());
		}
	}
}
