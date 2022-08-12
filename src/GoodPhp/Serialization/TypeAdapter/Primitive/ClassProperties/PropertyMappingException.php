<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties;

use Exception;
use GoodPhp\Reflection\Reflector\Reflection\PropertyReflection;
use GoodPhp\Serialization\TypeAdapter\Exception\CollectionItemMappingException;
use RuntimeException;
use Throwable;

class PropertyMappingException extends RuntimeException
{
	public function __construct(
		public readonly string $path,
		Throwable $previous,
	) {
		parent::__construct("Could not map property at path '{$path}': {$previous->getMessage()}", 0, $previous);
	}

	public static function rethrow(PropertyReflection $property, callable $callback): mixed
	{
		try {
			return $callback();
		} catch (PropertyMappingException $e) {
			throw new self($property->name() . '.' . $e->path, $e->getPrevious());
		} catch (CollectionItemMappingException $e) {
			throw new self($property->name() . '.' . $e->key, $e->getPrevious());
		} catch (Exception $e) {
			throw new self($property->name(), $e);
		}
	}
}
