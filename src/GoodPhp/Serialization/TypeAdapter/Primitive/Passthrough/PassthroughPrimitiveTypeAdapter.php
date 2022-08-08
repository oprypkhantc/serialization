<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\Passthrough;

use GoodPhp\Serialization\TypeAdapter\Primitive\PrimitiveTypeAdapter;

final class PassthroughPrimitiveTypeAdapter implements PrimitiveTypeAdapter
{
	/**
	 * @inheritDoc
	 */
	public function serialize(mixed $value): mixed
	{
		return $value;
	}

	/**
	 * @inheritDoc
	 */
	public function deserialize(mixed $value): mixed
	{
		return $value;
	}
}
