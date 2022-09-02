# Good PHP serialization

The concept is similar to Moshi, a Java/Kotlin serialization library - the least effort 
without sacrificing customizability, support for different formats or ease of use.

This is what it can serialize and deserialize out-of-the-box:

```php
/**
 * @template T1
 */
class Item
{
	/**
	 * @param Optional<int|null> $optional
	 * @param BackedEnumStub[] $array
	 * @param Collection<int, T1>
	 * @param T1 $generic
	 * @param NestedGeneric<int, T1> $nested
	 */
	public function __construct(
		// Scalars
		public readonly int $int,
		public readonly float $float,
		public readonly string $string,
		public readonly bool $bool,
		// Nullable and optional values
		public readonly ?string $nullableString,
		public readonly Optional $optional,
		// Custom property names
		#[SerializedName('two')] public readonly string $one,
		// Backed enums
		public readonly BackedEnumStub $backedEnum,
		// Generics and nested objects
		public readonly mixed $generic,
		public readonly NestedGenerics $nestedGeneric,
		// Arrays and Illuminate Collection of any type (even generics!)
		public readonly array $array,
		public readonly Collection $collection,
		// Dates
		public readonly DateTime $dateTime,
		public readonly Carbon $carbon,
	) {}
}
```

You can then convert it into a "primitive" (scalars and arrays of scalars) or JSON:

```php
$primitiveAdapter = $serializer->adapter(
	PrimitiveTypeAdapter::class, 
	new NamedType(Item::class, [new NamedType(Carbon::class)])
);
$primitiveAdapter->serialize(new Item(...)) // -> ['int' => 123, ...]

$jsonAdapter = $serializer->adapter(
	JsonTypeAdapter::class, 
	new NamedType(Item::class, [PrimitiveType::int()])
);
$jsonAdapter->deserialize('{"int": 123, ...}') // -> new Item(123, ...)
```

## Type mappers

You can easily define mappers for any formats the following way:

```php
(new SerializerBuilder())->addMapperLast(new DateTimeMapper())

final class DateTimeMapper
{
	#[MapTo(PrimitiveTypeAdapter::class)]
	public function to(DateTime $value): string
	{
		return $value->format(DateTimeInterface::RFC3339_EXTENDED);
	}

	#[MapFrom(PrimitiveTypeAdapter::class)]
	public function from(string $value): DateTime
	{
		return new DateTime($value);
	}
}
```

You can also do more advanced mappers (subset of types, generics, multiple mappers):

```php
final class TestMapper
{
	#[MapTo(PrimitiveTypeAdapter::class, new BaseTypeAcceptedByAcceptanceStrategy(BackedEnum::class))]
	public function to(BackedEnum $value): string|int
	{
		//
	}

	#[MapFrom(PrimitiveTypeAdapter::class, new BaseTypeAcceptedByAcceptanceStrategy(BackedEnum::class))]
	public function from(string|int $value, Type $type): BackedEnum
	{
		//
	}
	
	// 
	#[MapTo(PrimitiveTypeAdapter::class)]
	public function to(Optional $value, Type $type, Serializer $serializer): mixed
	{
		$valueAdapter = $serializer->adapter(PrimitiveTypeAdapter::class, $type->arguments[0]);

		return $valueAdapter->serialize($value->value());
	}
}
```

## Type adapter factories

Besides type mappers which satisfy most of the needs, you can use type adapter factories
to precisely control how each type is serialized. 

The idea is the following: when building a serializer, you add all of the factories you want
to use in order of priority:
```php
(new SerializerBuilder())
	->addMapperLast(new TestMapper()) // then this one
	->addFactoryLast(new TestFactory()) // and this one last
	->addFactory(new TestFactory()) // attempted first
```

A factory has the following signature:
```php
public function create(string $typeAdapterType, Type $type, array $attributes, Serializer $serializer): ?TypeAdapter
```
If you return `null`, the next factory is called. Otherwise, the returned type adapter is used.

This basic concept runs the serializer. Every type that is supported out-of-the-box also has
it's factory and can be overwritten just by doing `->addFactoryLast()`. Type mappers are
also just fancy adapter factories under the hood.

## Naming of keys

By default serializer preserves the naming of keys but there are ways to change this:
 - specify a custom global naming strategy (use one of the built in or write your own)
 - specify a naming strategy per-type using the `#[SerializedName]` attribute
 - specify a custom property name using that same `#[SerializedName]` attribute

Here's an example:
```php
(new SerializerBuilder())->namingStrategy(BuiltInNamingStrategy::SNAKE_CASE)

// Uses snake_case by default
class Item1 {
	public function __construct(
		public int $keyName, // appears as "key_name" in serialized data
		#[SerializedName('second_key')] public int $firstKey, // second_key
		#[SerializedName(BuiltInNamingStrategy::PASCAL_CASE)] public int $thirdKey, // THIRD_KEY
	) {}
}

// Uses PASCAL_CASE by default
#[SerializedName(BuiltInNamingStrategy::PASCAL_CASE)]
class Item2 {
	public function __construct(
		public int $keyName, // KEY_NAME
	) {}
}
```

## Required, nullable, optional and default values

By default if a property is missing in serialized payload:
 - nullable properties are just set to null
 - properties with a default value - use the default value
 - optional properties are set to an empty optional
 - any other throw an exception

Here's an example:
```php
// all keys missing -> throws for 'fifth' property
$adapter->deserialize([])

// only required property -> uses null, default values and optional
$adapter->deserialize(['fifth' => 123]);

// all properties -> fills all values
$adapter->deserialize(['first' => 123, 'second' => false, ...]);

class Item {
	/**
	 * @param Optional<int> $fourth
	 */
	public function __construct(
		public ?int $first, // set to null
		public bool $second = true, // set to true
		public Item $third = new Item(...), // set to Item instance
		public Optional $fourth, // set to empty optional
		public int $fifth, // required, throws if missing
	) {}
}
```

## Error handling

This is expected to be used with client-provided data, so good error descriptions is a must.
These are some of the errors you'll get:
 - Expected value of type 'int', but got 'string'
 - Expected value of type 'string', but got 'NULL'
 - Failed to parse time string (2020 dasd) at position 5 (d): The timezone could not be found in the database
 - Expected value of type 'string|int', but got 'boolean'
 - Expected one of [one, two], but got 'five'
 - Could not map item at key '1': Expected value of type 'string', but got 'NULL'
 - Could not map item at key '0': Expected value of type 'string', but got 'NULL' (and 1 more errors)."
 - Could not map property at path 'nested.field': Expected value of type 'string', but got 'integer'

Of course, all of these are just a chain of PHP exceptions with `previous` exceptions. Besides
those messages, you have all of the thrown exceptions with necessary information.

## More formats

You can add support for more formats (any you wish) with your own type adapters. 
All of the existing adapters are at your hands:

```php
interface XmlTypeAdapter extends TypeAdapter {}

final class FromPrimitiveXmlTypeAdapter implements XmlTypeAdapter
{
	public function __construct(
		private readonly PrimitiveTypeAdapter $primitiveDelegate,
	) {
	}

	public function serialize(mixed $value): mixed
	{
		return xml_encode($this->primitiveDelegate->serialize($value));
	}

	public function deserialize(mixed $value): mixed
	{
		return $this->primitiveDelegate->deserialize(xml_decode($value));
	}
}
```
