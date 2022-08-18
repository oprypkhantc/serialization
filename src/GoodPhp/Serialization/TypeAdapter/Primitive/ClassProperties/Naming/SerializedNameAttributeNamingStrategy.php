<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Naming;

use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class SerializedNameAttributeNamingStrategy implements NamingStrategy
{
	public function __construct(private readonly NamingStrategy $fallback)
	{
	}

	public function translate(string $name, Collection $attributes, Collection $classAttributes): string
	{
		/** @var SerializedName|null $attribute */
		$attribute = $attributes->first(fn (object $attribute) => $attribute instanceof SerializedName);

		if (!$attribute) {
			/** @var SerializedName|null $attribute */
			$attribute = $classAttributes->first(fn (object $attribute) => $attribute instanceof SerializedName);

			Assert::true(!$attribute || $attribute->nameOrStrategy instanceof NamingStrategy, 'Class applied #[SerializedName] must provide a naming strategy rather than a string name.');
		}

		if (!$attribute) {
			return $this->fallback->translate($name, $attributes, $classAttributes);
		}

		if ($attribute->nameOrStrategy instanceof NamingStrategy) {
			return $attribute->nameOrStrategy->translate($name, $attributes, $classAttributes);
		}

		return $attribute->nameOrStrategy;
	}
}
