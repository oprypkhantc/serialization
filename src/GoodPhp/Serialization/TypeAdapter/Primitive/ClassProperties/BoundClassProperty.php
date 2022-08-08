<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties;

use GoodPhp\Reflection\Reflector\Reflection\PropertyReflection;
use GoodPhp\Serialization\TypeAdapter\TypeAdapter;

/**
 * @template T of object
 */
final class BoundClassProperty
{
	public function __construct(
		public readonly PropertyReflection $reflection,
		private readonly TypeAdapter $typeAdapter,
		public readonly string $serializedName,
	) {
	}

	/**
	 * @param T $object
	 */
	public function serialize(object $object): mixed
	{
		return $this->typeAdapter->serialize(
			$this->reflection->get($object)
		);
	}

	/**
	 * @param T $into
	 *
	 * @return mixed
	 */
	public function deserialize(mixed $from, object $into): void
	{
		$this->reflection->set(
			$into,
			$this->typeAdapter->deserialize($from)
		);
	}
}
