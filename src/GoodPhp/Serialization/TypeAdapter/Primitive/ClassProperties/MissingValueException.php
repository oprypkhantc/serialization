<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties;

use RuntimeException;
use Throwable;

class MissingValueException extends RuntimeException
{
	public function __construct(
		public readonly string $key,
		?Throwable $previous = null
	) {
		parent::__construct("Missing value for key {$key}", 0, $previous);
	}

	public static function rethrow(string $key, callable $callback): mixed
	{
		try {
			return $callback();
		} catch (MissingValueException $e) {
			throw new self($key . '.' . $e->key, $e->getPrevious());
		}
	}
}
