<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods;

use GoodPhp\Reflection\Type\Type;
use GoodPhp\Serialization\Serializer;
use GoodPhp\Serialization\TypeAdapter\Primitive\PrimitiveTypeAdapter;
use Webmozart\Assert\Assert;

final class MapperMethodsPrimitiveTypeAdapter implements PrimitiveTypeAdapter
{
	public function __construct(
		private readonly ?MapperMethod $toMapper,
		private readonly ?MapperMethod $fromMapper,
		private readonly ?PrimitiveTypeAdapter $fallbackDelegate,
		private readonly Type $type,
		private readonly Serializer $serializer,
	) {
		// Make sure there's either both mappers or one of the mappers and a fallback.
		Assert::true($this->toMapper || $this->fallbackDelegate);
		Assert::true($this->fromMapper || $this->fallbackDelegate);
	}

	/**
	 * @inheritDoc
	 */
	public function serialize(mixed $value): mixed
	{
		return $this->toMapper ?
			$this->toMapper->invoke($this->serializer, $this->type, $value) :
			$this->fallbackDelegate->serialize($value);
	}

	/**
	 * @inheritDoc
	 */
	public function deserialize(mixed $value): mixed
	{
		return $this->fromMapper ?
			$this->fromMapper->invoke($this->serializer, $this->type, $value) :
			$this->fallbackDelegate->deserialize($value);
	}
}
