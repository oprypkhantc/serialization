<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\Passthrough;

use GoodPhp\Reflection\Type\Combinatorial\UnionType;
use GoodPhp\Reflection\Type\PrimitiveType;
use GoodPhp\Reflection\Type\Type;
use GoodPhp\Reflection\Type\TypeComparator;
use GoodPhp\Serialization\Serializer;
use GoodPhp\Serialization\TypeAdapter\Primitive\PrimitiveTypeAdapter;
use GoodPhp\Serialization\TypeAdapter\TypeAdapterFactory;
use Illuminate\Support\Collection;

final class PassthroughPrimitiveTypeAdapterFactory implements TypeAdapterFactory
{
	private readonly PassthroughPrimitiveTypeAdapter $adapter;

	public function __construct()
	{
		$this->adapter = new PassthroughPrimitiveTypeAdapter();
	}

	/**
	 * @inheritDoc
	 */
	public function create(string $typeAdapterType, Type $type, array $attributes, Serializer $serializer)
	{
		//  || !$this->primitiveType->accepts($type)
		if (
			$typeAdapterType !== PrimitiveTypeAdapter::class ||
			!$serializer->reflection->get(TypeComparator::class)->accepts(
				new UnionType(new Collection([
					PrimitiveType::integer(),
					PrimitiveType::string(),
					PrimitiveType::boolean(),
					PrimitiveType::float(),
				])),
				$type
			)
		) {
			return null;
		}

		return $this->adapter;
	}
}
