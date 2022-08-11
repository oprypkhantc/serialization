<?php

namespace Tests\Integration;

use DateTime;
use Exception;
use Generator;
use GoodPhp\Reflection\Type\NamedType;
use GoodPhp\Reflection\Type\PrimitiveType;
use GoodPhp\Reflection\Type\Special\NullableType;
use GoodPhp\Reflection\Type\Type;
use GoodPhp\Serialization\SerializerBuilder;
use GoodPhp\Serialization\TypeAdapter\Json\JsonTypeAdapter;
use GoodPhp\Serialization\TypeAdapter\Primitive\BuiltIn\Exceptions\UnexpectedValueTypeException;
use GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\PropertyMappingException;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use function TenantCloud\Standard\Optional\empty_optional;
use TenantCloud\Standard\Optional\Optional;
use function TenantCloud\Standard\Optional\optional;
use Tests\Stubs\BackedEnumStub;
use Tests\Stubs\ClassStub;
use Tests\Stubs\NestedStub;
use Tests\Stubs\ValueEnumStub;
use Throwable;

class JsonSerializationTest extends TestCase
{
	/**
	 * @dataProvider serializesProvider
	 */
	public function testSerializes(string|Type $type, mixed $data, string $expectedSerialized): void
	{
		$adapter = (new SerializerBuilder())
			->build()
			->adapter(JsonTypeAdapter::class, $type);

		self::assertSame($expectedSerialized, $adapter->serialize($data));
	}

	public function serializesProvider(): Generator
	{
		yield 'int' => [
			'int',
			123,
			'123',
		];

		yield 'float' => [
			'float',
			123.45,
			'123.45',
		];

		yield 'bool' => [
			'bool',
			true,
			'true',
		];

		yield 'string' => [
			'string',
			'text',
			'"text"',
		];

		yield 'nullable string' => [
			new NullableType(PrimitiveType::string()),
			'text',
			'"text"',
		];

		yield 'nullable string with null value' => [
			new NullableType(PrimitiveType::string()),
			null,
			'null',
		];

		yield 'DateTime' => [
			DateTime::class,
			new DateTime('2020-01-01 00:00:00'),
			'"2020-01-01T00:00:00.000+00:00"',
		];

		yield 'nullable DateTime' => [
			new NullableType(new NamedType(DateTime::class)),
			new DateTime('2020-01-01 00:00:00'),
			'"2020-01-01T00:00:00.000+00:00"',
		];

		yield 'nullable DateTime with null value' => [
			new NullableType(new NamedType(DateTime::class)),
			null,
			'null',
		];

		yield 'backed enum' => [
			BackedEnumStub::class,
			BackedEnumStub::ONE,
			'"one"',
		];

		yield 'value enum' => [
			ValueEnumStub::class,
			ValueEnumStub::$ONE,
			'"one"',
		];

		yield 'optional of value enum' => [
			new NamedType(Optional::class, new Collection([
				new NamedType(ValueEnumStub::class),
			])),
			optional(ValueEnumStub::$TWO),
			'"two"',
		];

		yield 'array of DateTime' => [
			PrimitiveType::array(
				new NamedType(DateTime::class)
			),
			[new DateTime('2020-01-01 00:00:00')],
			'["2020-01-01T00:00:00.000+00:00"]',
		];

		yield 'Collection of DateTime' => [
			new NamedType(
				Collection::class,
				new Collection([
					PrimitiveType::integer(),
					new NamedType(DateTime::class),
				])
			),
			new Collection([new DateTime('2020-01-01 00:00:00')]),
			'["2020-01-01T00:00:00.000+00:00"]',
		];

		yield 'ClassStub with all fields' => [
			new NamedType(
				ClassStub::class,
				new Collection([
					new NamedType(DateTime::class),
				])
			),
			new ClassStub(
				1,
				new NestedStub(),
				new DateTime('2020-01-01 00:00:00'),
				optional(123),
				123,
			),
			'{"primitive":1,"nested":{"field":"something"},"date":"2020-01-01T00:00:00.000+00:00","optional":123,"nullable":123}',
		];

		yield 'ClassStub with empty optional and null nullable' => [
			new NamedType(
				ClassStub::class,
				new Collection([
					new NamedType(DateTime::class),
				])
			),
			new ClassStub(
				1,
				new NestedStub(),
				new DateTime('2020-01-01 00:00:00'),
				empty_optional(),
				null,
			),
			'{"primitive":1,"nested":{"field":"something"},"date":"2020-01-01T00:00:00.000+00:00","nullable":null}',
		];
	}

	/**
	 * @dataProvider deserializesProvider
	 */
	public function testDeserializes(string|Type $type, mixed $expectedData, string $serialized): void
	{
		$adapter = (new SerializerBuilder())
			->build()
			->adapter(JsonTypeAdapter::class, $type);

		self::assertEquals($expectedData, $adapter->deserialize($serialized));
	}

	public function deserializesProvider(): Generator
	{
		yield 'int' => [
			'int',
			123,
			'123',
		];

		yield 'float' => [
			'float',
			123.45,
			'123.45',
		];

		yield 'bool' => [
			'bool',
			true,
			'true',
		];

		yield 'string' => [
			'string',
			'text',
			'"text"',
		];

		yield 'nullable string' => [
			new NullableType(PrimitiveType::string()),
			'text',
			'"text"',
		];

		yield 'nullable string with null value' => [
			new NullableType(PrimitiveType::string()),
			null,
			'null',
		];

		yield 'DateTime' => [
			DateTime::class,
			new DateTime('2020-01-01 00:00:00'),
			'"2020-01-01T00:00:00.000+00:00"',
		];

		yield 'nullable DateTime' => [
			new NullableType(new NamedType(DateTime::class)),
			new DateTime('2020-01-01 00:00:00'),
			'"2020-01-01T00:00:00.000+00:00"',
		];

		yield 'nullable DateTime with null value' => [
			new NullableType(new NamedType(DateTime::class)),
			null,
			'null',
		];

		yield 'backed enum' => [
			BackedEnumStub::class,
			BackedEnumStub::ONE,
			'"one"',
		];

		yield 'value enum' => [
			ValueEnumStub::class,
			ValueEnumStub::$ONE,
			'"one"',
		];

		yield 'optional of value enum' => [
			new NamedType(Optional::class, new Collection([
				new NamedType(ValueEnumStub::class),
			])),
			optional(ValueEnumStub::$TWO),
			'"two"',
		];

		yield 'array of DateTime' => [
			PrimitiveType::array(
				new NamedType(DateTime::class)
			),
			[new DateTime('2020-01-01 00:00:00')],
			'["2020-01-01T00:00:00.000+00:00"]',
		];

		yield 'Collection of DateTime' => [
			new NamedType(
				Collection::class,
				new Collection([
					PrimitiveType::integer(),
					new NamedType(DateTime::class),
				])
			),
			new Collection([new DateTime('2020-01-01 00:00:00')]),
			'["2020-01-01T00:00:00.000+00:00"]',
		];

		yield 'ClassStub with all fields' => [
			new NamedType(
				ClassStub::class,
				new Collection([
					new NamedType(DateTime::class),
				])
			),
			new ClassStub(
				1,
				new NestedStub(),
				new DateTime('2020-01-01 00:00:00'),
				optional(123),
				123,
			),
			'{"primitive":1,"nested":{"field":"something"},"date":"2020-01-01T00:00:00.000+00:00","optional":123,"nullable":123}',
		];

		yield 'ClassStub with empty optional and null nullable' => [
			new NamedType(
				ClassStub::class,
				new Collection([
					new NamedType(DateTime::class),
				])
			),
			new ClassStub(
				1,
				new NestedStub(),
				new DateTime('2020-01-01 00:00:00'),
				empty_optional(),
				null,
			),
			'{"primitive":1,"nested":{"field":"something"},"date":"2020-01-01T00:00:00.000+00:00","nullable":null}',
		];

		yield 'ClassStub with the least default fields' => [
			new NamedType(
				ClassStub::class,
				new Collection([
					new NamedType(DateTime::class),
				])
			),
			new ClassStub(
				1,
				new NestedStub(),
				new DateTime('2020-01-01 00:00:00'),
				empty_optional(),
				null,
			),
			'{"primitive":1,"nested":{},"date":"2020-01-01T00:00:00.000+00:00","nullable":null}',
		];
	}

	/**
	 * @dataProvider deserializesWithAnExceptionProvider
	 */
	public function testDeserializesWithAnException(Throwable $expectedException, string|NamedType $type, string $serialized): void
	{
		$adapter = (new SerializerBuilder())
			->build()
			->adapter(JsonTypeAdapter::class, $type);

		try {
			$adapter->deserialize($serialized);

			self::fail('Expected exception to be thrown, got none.');
		} catch (Throwable $e) {
			self::assertEquals($expectedException, $e);
		}
	}

	public function deserializesWithAnExceptionProvider(): Generator
	{
		yield [
			new Exception('Failed to parse time string (doesnt look like a date) at position 0 (d): The timezone could not be found in the database'),
			DateTime::class,
			'"doesnt look like a date"',
		];

		yield [
			new PropertyMappingException('primitive', new UnexpectedValueTypeException('1', PrimitiveType::integer())),
			new NamedType(
				ClassStub::class,
				new Collection([
					new NamedType(DateTime::class),
				])
			),
			'{"primitive":"1","nested":{"field":"something"},"date":"2020-01-01T00:00:00.000+00:00"}',
		];

		yield [
			new PropertyMappingException('nested.field', new UnexpectedValueTypeException(123, PrimitiveType::string())),
			new NamedType(
				ClassStub::class,
				new Collection([
					new NamedType(DateTime::class),
				])
			),
			'{"primitive":1,"nested":{"field":123},"date":"2020-01-01T00:00:00.000+00:00"}',
		];
	}
}
