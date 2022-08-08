<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods;

use Closure;
use GoodPhp\Reflection\Reflector\Reflection\MethodReflection;
use GoodPhp\Reflection\Type\Type;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\Acceptance\AcceptanceStrategy;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\Acceptance\BaseTypeEqualsAcceptanceStrategy;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\MapperMethodTypeSubstiter\MapperMethodTypeSubstituterFactory;
use Webmozart\Assert\Assert;

final class MapperMethodFactory
{
	public function __construct(
		private readonly MapperMethodTypeSubstituterFactory $mapperMethodTypeSubstituterFactory
	) {
	}

	/**
	 * @param Closure(MethodReflection): Type $methodValueType
	 */
	public function create(
		MethodReflection $methodReflection,
		Closure $methodValueType,
		?AcceptanceStrategy $acceptanceStrategy,
		object $adapter,
		MapperMethodsPrimitiveTypeAdapterFactory $factory
	): MapperMethod {
		Assert::minCount($methodReflection->parameters(), 1);
		Assert::notNull($methodReflection->returnType());

		$valueParameter = $methodReflection->parameters()[0];

		Assert::notNull($valueParameter->type());

		return new MapperMethod(
			$valueParameter->type(),
			$methodReflection->returnType(),
			$adapter,
			$methodValueType,
			$this->mapperMethodTypeSubstituterFactory->fromReflection($methodReflection, $methodValueType),
			$acceptanceStrategy ?? BaseTypeEqualsAcceptanceStrategy::get(),
			$factory,
		);
	}
}
