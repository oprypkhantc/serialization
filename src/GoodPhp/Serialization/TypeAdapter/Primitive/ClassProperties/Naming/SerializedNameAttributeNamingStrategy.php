<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Naming;

use Illuminate\Support\Arr;

class SerializedNameAttributeNamingStrategy implements NamingStrategy
{
	public function __construct(private readonly NamingStrategy $fallback)
	{
	}

	public function translate(string $name, array $attributes): string
	{
		/** @var SerializedName|null $attribute */
		$attribute = Arr::first($attributes, fn (object $attribute) => $attribute instanceof SerializedName);

		if (!$attribute) {
			return $this->fallback->translate($name, $attributes);
		}

		if ($attribute->nameOrStrategy instanceof NamingStrategy) {
			return $attribute->nameOrStrategy->translate($name, $attributes);
		}

		return $attribute->nameOrStrategy;
	}
}
