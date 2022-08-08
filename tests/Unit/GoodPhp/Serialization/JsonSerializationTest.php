<?php

namespace Tests\Unit\GoodPhp\Serialization;

use DateTime;
use GoodPhp\Reflection\Type\NamedType;
use GoodPhp\Reflection\Type\PrimitiveType;
use GoodPhp\Serialization\SerializerBuilder;
use GoodPhp\Serialization\TypeAdapter\Json\JsonTypeAdapter;
use GoodPhp\Serialization\TypeAdapter\TypeAdapter;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Tests\Unit\GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Stubs\ClassStub;
use Tests\Unit\GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Stubs\NestedStub;
use Tests\Unit\GoodPhp\Serialization\TypeAdapter\Primitive\ValueEnum\ValueEnumStub;

class JsonSerializationTest extends TestCase
{
	public function testDate(): void
	{
		$adapter = (new SerializerBuilder())
			->build()
			->adapter(JsonTypeAdapter::class, DateTime::class);

		$this->assertSerializesAndDeserializes(
			$adapter,
			new DateTime('2020-01-01 00:00:00'),
			'"2020-01-01T00:00:00.000+00:00"',
		);
	}

	public function testObject(): void
	{
		$adapter = (new SerializerBuilder())
			->build()
			->adapter(
				JsonTypeAdapter::class,
				new NamedType(
					ClassStub::class,
					new Collection([new NamedType(DateTime::class)])
				)
			);

		$obj = new ClassStub(1, new NestedStub(), new DateTime('2020-01-01 00:00:00'));

		$this->assertSerializesAndDeserializes(
			$adapter,
			$obj,
			'{"primitive":1,"nested":{"field":"something"},"date":"2020-01-01T00:00:00.000+00:00"}',
		);
	}

	public function testArray(): void
	{
		$adapter = (new SerializerBuilder())
			->build()
			->adapter(
				JsonTypeAdapter::class,
				PrimitiveType::array(
					new NamedType(DateTime::class)
				)
			);

		$this->assertSerializesAndDeserializes(
			$adapter,
			[new DateTime('2020-01-01 00:00:00')],
			'["2020-01-01T00:00:00.000+00:00"]',
		);
	}

	public function testCollection(): void
	{
		$adapter = (new SerializerBuilder())
			->build()
			->adapter(
				JsonTypeAdapter::class,
				new NamedType(
					Collection::class,
					new Collection([
						PrimitiveType::integer(),
						new NamedType(DateTime::class),
					])
				)
			);

		$this->assertSerializesAndDeserializes(
			$adapter,
			new Collection([new DateTime('2020-01-01 00:00:00')]),
			'["2020-01-01T00:00:00.000+00:00"]',
		);
	}

	public function testValueEnum(): void
	{
		$adapter = (new SerializerBuilder())
			->build()
			->adapter(
				JsonTypeAdapter::class,
				ValueEnumStub::class,
			);

		$this->assertSerializesAndDeserializes(
			$adapter,
			ValueEnumStub::$ONE,
			'"one"',
		);
		$this->assertSerializesAndDeserializes(
			$adapter,
			ValueEnumStub::$TWO,
			'"two"',
		);
	}

	private function assertSerializesAndDeserializes(TypeAdapter $adapter, mixed $deserialized, mixed $serialized): void
	{
		self::assertSame($serialized, $adapter->serialize($deserialized));
		self::assertEquals($deserialized, $adapter->deserialize($serialized));
	}
}
