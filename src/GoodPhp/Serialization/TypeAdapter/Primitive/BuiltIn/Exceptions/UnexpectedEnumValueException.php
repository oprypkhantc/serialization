<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\BuiltIn\Exceptions;

use RuntimeException;
use Throwable;

class UnexpectedEnumValueException extends RuntimeException
{
	public function __construct(
		public readonly string|int $value,
		public readonly array $expectedValues,
		?Throwable $previous = null
	) {
		parent::__construct(
			'Expected one of [' .
				implode(', ', $this->expectedValues) .
				"], but got '{$this->value}'",
			0,
			$previous
		);
	}
}
