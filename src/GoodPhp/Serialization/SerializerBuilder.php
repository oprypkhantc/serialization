<?php

namespace GoodPhp\Serialization;

use GoodPhp\Reflection\ReflectionBuilder;
use GoodPhp\Reflection\Reflector\Reflector;
use GoodPhp\Reflection\Type\Type;
use GoodPhp\Serialization\TypeAdapter\Json\FromPrimitiveJsonTypeAdapterFactory;
use GoodPhp\Serialization\TypeAdapter\Primitive\ArrayMapper;
use GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\ClassPropertiesPrimitiveTypeAdapterFactory;
use GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Naming\BuiltInNamingStrategy;
use GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Naming\NamingStrategy;
use GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Naming\SerializedNameAttributeNamingStrategy;
use GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\ObjectClassFactory;
use GoodPhp\Serialization\TypeAdapter\Primitive\Date\DateTimeMapper;
use GoodPhp\Serialization\TypeAdapter\Primitive\Illuminate\CollectionMapper;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\MapperMethodFactory;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\MapperMethodsPrimitiveTypeAdapterFactoryFactory;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\MapperMethodTypeSubstiter\MapperMethodTypeSubstituterFactory;
use GoodPhp\Serialization\TypeAdapter\Primitive\Passthrough\PassthroughPrimitiveTypeAdapterFactory;
use GoodPhp\Serialization\TypeAdapter\Primitive\ValueEnumMapper;
use GoodPhp\Serialization\TypeAdapter\Registry\Factory\FactoryTypeAdapterRegistryBuilder;
use GoodPhp\Serialization\TypeAdapter\TypeAdapter;
use GoodPhp\Serialization\TypeAdapter\TypeAdapterFactory;
use Psr\Container\ContainerInterface;

final class SerializerBuilder
{
	private FactoryTypeAdapterRegistryBuilder $typeAdapterRegistryBuilder;

	private ContainerInterface $reflection;

	private ?NamingStrategy $namingStrategy;

	public function __construct(
		?NamingStrategy $namingStrategy = null,
		?ContainerInterface $reflection = null
	) {
		$this->reflection = $reflection ?? (new ReflectionBuilder())->build();
		$this->namingStrategy = $namingStrategy;

		$this->typeAdapterRegistryBuilder = new FactoryTypeAdapterRegistryBuilder(
			new MapperMethodsPrimitiveTypeAdapterFactoryFactory(
				$this->reflection->get(Reflector::class),
				new MapperMethodFactory(
					new MapperMethodTypeSubstituterFactory(),
				),
			),
		);
	}

	public function addFactory(TypeAdapterFactory $factory): self
	{
		$this->typeAdapterRegistryBuilder->addFactory($factory);

		return $this;
	}

	public function addMapper(object $adapter): self
	{
		$this->typeAdapterRegistryBuilder->addMapper($adapter);

		return $this;
	}

	/**
	 * @param class-string<object> $attribute
	 */
	public function add(string $typeAdapterType, Type $type, string $attribute, TypeAdapter $adapter): self
	{
		$this->typeAdapterRegistryBuilder->add($typeAdapterType, $type, $attribute, $adapter);

		return $this;
	}

	public function addFactoryLast(TypeAdapterFactory $factory): self
	{
		$this->typeAdapterRegistryBuilder->addFactoryLast($factory);

		return $this;
	}

	public function addMapperLast(object $adapter): self
	{
		$this->typeAdapterRegistryBuilder->addMapperLast($adapter);

		return $this;
	}

	/**
	 * @param class-string<object> $attribute
	 */
	public function addLast(string $typeAdapterType, Type $type, string $attribute, TypeAdapter $adapter): self
	{
		$this->typeAdapterRegistryBuilder->addLast($typeAdapterType, $type, $attribute, $adapter);

		return $this;
	}

	public function build(): Serializer
	{
		$this->typeAdapterRegistryBuilder
			->addMapperLast(new DateTimeMapper())
			->addMapperLast(new CollectionMapper())
			->addMapperLast(new ArrayMapper())
			->addMapperLast(new ValueEnumMapper())
			->addFactoryLast(new PassthroughPrimitiveTypeAdapterFactory())
			->addFactoryLast(new ClassPropertiesPrimitiveTypeAdapterFactory(
				new SerializedNameAttributeNamingStrategy($this->namingStrategy ?? BuiltInNamingStrategy::PRESERVING),
				new ObjectClassFactory(),
			))
			->addFactoryLast(new FromPrimitiveJsonTypeAdapterFactory());

		return new Serializer(
			$this->typeAdapterRegistryBuilder->build(),
			$this->reflection
		);
	}
}
