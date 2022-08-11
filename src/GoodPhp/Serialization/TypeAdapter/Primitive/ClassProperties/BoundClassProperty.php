<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties;

use GoodPhp\Reflection\Reflector\Reflection\PropertyReflection;
use GoodPhp\Serialization\TypeAdapter\TypeAdapter;
use Illuminate\Support\Arr;
use function TenantCloud\Standard\Optional\empty_optional;

/**
 * @template T of object
 */
final class BoundClassProperty
{
	public function __construct(
		public readonly PropertyReflection $reflection,
		private readonly TypeAdapter $typeAdapter,
		public readonly string $serializedName,
		public readonly bool $optional,
		public readonly bool $hasDefaultValue,
	) {
	}

	/**
	 * @param T $object
	 */
	public function serialize(object $object): array
	{
		$value = $this->reflection->get($object);

		if ($this->optional && !$value->hasValue()) {
			return [];
		}

		return [
			$this->serializedName => $this->typeAdapter->serialize(
				$this->reflection->get($object)
			),
		];
	}

	/**
	 * @param T $into
	 */
	public function deserialize(array $data, object $into): void
	{
		if (!Arr::has($data, $this->serializedName)) {
			if ($this->optional) {
				$this->reflection->set($into, empty_optional());

				return;
			}

			if ($this->hasDefaultValue) {
				return;
			}

			throw new MissingValueException($this->serializedName);
		}

		$deserialized = MissingValueException::rethrow(
			$this->serializedName,
			fn () => $this->typeAdapter->deserialize(
				$data[$this->serializedName]
			)
		);

		$this->reflection->set($into, $deserialized);
	}
}
