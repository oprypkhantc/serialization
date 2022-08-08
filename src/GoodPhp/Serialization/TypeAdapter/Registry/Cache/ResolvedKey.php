<?php

namespace GoodPhp\Serialization\TypeAdapter\Registry\Cache;

use Ds\Hashable;
use GoodPhp\Reflection\Type\Type;
use GoodPhp\Serialization\TypeAdapter\TypeAdapterFactory;
use RuntimeException;

final class ResolvedKey implements Hashable
{
	/**
	 * @param object[] $attributes
	 */
	public function __construct(
		public readonly string $typeAdapterType,
		public readonly Type $type,
		public readonly array $attributes,
		public readonly ?TypeAdapterFactory $skipPast
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function hash()
	{
		throw new RuntimeException('Not implemented.');
	}

	/**
	 * @inheritDoc
	 *
	 * @param ResolvedKey $obj
	 */
	public function equals($obj): bool
	{
		// Non-strict attributes comparison intended, should be safe.
		/* @noinspection TypeUnsafeComparisonInspection */
		return $this->typeAdapterType === $obj->typeAdapterType &&
			$this->type->equals($obj->type) &&
			$this->attributes == $obj->attributes &&
			$this->skipPast === $obj->skipPast;
	}
}
