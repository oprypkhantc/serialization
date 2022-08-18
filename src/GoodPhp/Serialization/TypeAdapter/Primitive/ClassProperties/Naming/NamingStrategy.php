<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Naming;

use Illuminate\Support\Collection;

interface NamingStrategy
{
	public function translate(string $name, Collection $attributes, Collection $classAttributes): string;
}
