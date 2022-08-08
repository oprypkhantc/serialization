<?php

namespace GoodPhp\Serialization\TypeAdapter\Registry\Factory;

use GoodPhp\Reflection\Type\Type;
use GoodPhp\Serialization\TypeAdapter\MatchingDelegate\MatchingDelegateTypeAdapterFactory;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\MapperMethodsPrimitiveTypeAdapterFactoryFactory;
use GoodPhp\Serialization\TypeAdapter\TypeAdapter;
use GoodPhp\Serialization\TypeAdapter\TypeAdapterFactory;

final class FactoryTypeAdapterRegistryBuilder
{
	/** @var TypeAdapterFactory[] */
	private array $factories = [];

	public function __construct(
		private readonly MapperMethodsPrimitiveTypeAdapterFactoryFactory $mapperMethodsTypeAdapterFactoryFactory,
	) {
	}

	public function addFactory(TypeAdapterFactory $factory): self
	{
		array_unshift($this->factories, $factory);

		return $this;
	}

	public function addMapper(object $adapter): self
	{
		return $this->addFactory($this->mapperMethodsTypeAdapterFactoryFactory->create($adapter));
	}

	/**
	 * @param class-string<object> $attribute
	 */
	public function add(string $typeAdapterType, Type $type, string $attribute, TypeAdapter $adapter): self
	{
		return $this->addFactory(new MatchingDelegateTypeAdapterFactory($typeAdapterType, $type, $attribute, $adapter));
	}

	public function addFactoryLast(TypeAdapterFactory $factory): self
	{
		$this->factories[] = $factory;

		return $this;
	}

	public function addMapperLast(object $adapter): self
	{
		return $this->addFactoryLast($this->mapperMethodsTypeAdapterFactoryFactory->create($adapter));
	}

	/**
	 * @param class-string<object> $attribute
	 */
	public function addLast(string $typeAdapterType, Type $type, string $attribute, TypeAdapter $adapter): self
	{
		return $this->addFactoryLast(new MatchingDelegateTypeAdapterFactory($typeAdapterType, $type, $attribute, $adapter));
	}

	public function build(): FactoryTypeAdapterRegistry
	{
		return new FactoryTypeAdapterRegistry($this->factories);
	}
}
