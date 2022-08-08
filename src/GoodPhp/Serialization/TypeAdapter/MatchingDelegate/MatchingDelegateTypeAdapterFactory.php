<?php

namespace GoodPhp\Serialization\TypeAdapter\MatchingDelegate;

use Closure;
use GoodPhp\Reflection\Type\Type;
use GoodPhp\Reflection\Type\TypeComparator;
use GoodPhp\Serialization\Serializer;
use GoodPhp\Serialization\TypeAdapter\TypeAdapter;
use GoodPhp\Serialization\TypeAdapter\TypeAdapterFactory;

final class MatchingDelegateTypeAdapterFactory implements TypeAdapterFactory
{
	/**
	 * @param Type $type
	 * @param TypeAdapter|(Closure(Type, array<object> $attributes, Serializer $serializer): TypeAdapter) $adapter
	 */
	public function __construct(
		private readonly string $typeAdapterType,
		private readonly Type $type,
		private readonly string $attribute,
		private readonly TypeAdapter|Closure $adapter,
	) {
	}

	public function create(string $typeAdapterType, Type $type, array $attributes, Serializer $serializer): ?TypeAdapter
	{
		if ($typeAdapterType !== $this->typeAdapterType) {
			return null;
		}

		if (!$serializer->reflection->get(TypeComparator::class)->accepts($this->type, $type)) {
			return null;
		}

		if (!array_filter($attributes, fn (object $attribute) => is_a($attribute, $this->attribute, true))) {
			return null;
		}

		return $this->adapter instanceof Closure ? ($this->adapter)($type, $attributes, $serializer) : $this->adapter;
	}
}
