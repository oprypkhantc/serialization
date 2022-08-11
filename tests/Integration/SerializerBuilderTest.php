<?php

namespace Tests\Integration;

use GoodPhp\Reflection\Type\PrimitiveType;
use GoodPhp\Serialization\SerializerBuilder;
use GoodPhp\Serialization\TypeAdapter\Json\JsonTypeAdapter;
use PHPUnit\Framework\TestCase;

class SerializerBuilderTest extends TestCase
{
	public function testBuildsADefaultWorkingSerializer(): void
	{
		$this->expectNotToPerformAssertions();

		$serializer = (new SerializerBuilder())
			->build();

		$serializer->adapter(JsonTypeAdapter::class, PrimitiveType::integer());
		$serializer->adapter(JsonTypeAdapter::class, PrimitiveType::float());
		$serializer->adapter(JsonTypeAdapter::class, PrimitiveType::boolean());
		$serializer->adapter(JsonTypeAdapter::class, PrimitiveType::string());
		$serializer->adapter(JsonTypeAdapter::class, PrimitiveType::array(PrimitiveType::string(), PrimitiveType::integer()));
	}
}
