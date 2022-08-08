<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Naming;

use Illuminate\Support\Str;

enum BuiltInNamingStrategy implements NamingStrategy
{
	public function translate(string $name, array $attributes): string
	{
		return match ($this) {
			self::PRESERVING  => $name,
			self::CAMEL_CASE  => Str::camel($name),
			self::SNAKE_CASE  => Str::snake($name),
			self::PASCAL_CASE => Str::studly($name),
		};
	}
	case PRESERVING;
	case CAMEL_CASE;
	case SNAKE_CASE;
	case PASCAL_CASE;
}
