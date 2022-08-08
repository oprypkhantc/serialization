<?php

namespace GoodPhp\Serialization;

use GoodPhp\Reflection\Type\NamedType;
use GoodPhp\Reflection\Type\Type;
use GoodPhp\Serialization\TypeAdapter\Registry\TypeAdapterRegistry;
use GoodPhp\Serialization\TypeAdapter\TypeAdapter;
use GoodPhp\Serialization\TypeAdapter\TypeAdapterFactory;
use Psr\Container\ContainerInterface;

final class Serializer
{
	public function __construct(
		private readonly TypeAdapterRegistry $typeAdapterRegistry,
		public readonly ContainerInterface $reflection,
	) {
	}

	/**
	 * @template T
	 * @template A of TypeAdapter<T>
	 *
	 * @param class-string<T>      $typeAdapterType
	 * @param Type|class-string<T> $type
	 *
	 * @return A
	 */
	public function adapter(string $typeAdapterType, Type|string $type, array $attributes = [], TypeAdapterFactory $skipPast = null): TypeAdapter
	{
		if (is_string($type)) {
			$type = new NamedType($type);
		}

		return $this->typeAdapterRegistry->forType($typeAdapterType, $this, $type, $attributes, $skipPast);
	}
}
