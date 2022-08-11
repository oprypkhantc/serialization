<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\BuiltIn\Exceptions;

use RuntimeException;
use Throwable;

class CollectionItemMappingException extends RuntimeException
{
	public function __construct(
		public readonly string|int $key,
		?Throwable $previous = null
	) {
		parent::__construct("Could not map item at key '{$key}'", 0, $previous);
	}
}
