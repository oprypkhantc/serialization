<?php

namespace GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods;

use Closure;
use GoodPhp\Reflection\Reflector\Reflection\FunctionParameterReflection;
use GoodPhp\Reflection\Reflector\Reflection\MethodReflection;
use GoodPhp\Reflection\Type\NamedType;
use GoodPhp\Reflection\Type\Type;
use GoodPhp\Serialization\Serializer;
use GoodPhp\Serialization\TypeAdapter\Primitive\BuiltIn\Exceptions\UnexpectedValueTypeException;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\Acceptance\AcceptanceStrategy;
use GoodPhp\Serialization\TypeAdapter\Primitive\MapperMethods\MapperMethodTypeSubstiter\MapperMethodTypeSubstituter;
use GoodPhp\Serialization\TypeAdapter\Primitive\PrimitiveTypeAdapter;
use TypeError;
use Webmozart\Assert\Assert;

/**
 * @template TIn
 * @template TOut
 */
final class MapperMethod
{
	/**
	 * @param Closure(MethodReflection): Type $methodValueType
	 */
	public function __construct(
		public readonly Type $in,
		public readonly Type $out,
		private readonly object $adapter,
		public readonly Closure $methodValueType,
		public readonly MapperMethodTypeSubstituter $typeSubstituter,
		public readonly AcceptanceStrategy $acceptanceStrategy,
		private readonly MapperMethodsPrimitiveTypeAdapterFactory $mapperMethodsTypeAdapterFactory,
	) {
	}

	public function invoke(Serializer $serializer, Type $type, mixed $value): mixed
	{
		$reflection = $this->typeSubstituter->resolve($type);

		$map = [
			MapperMethodsPrimitiveTypeAdapterFactory::class => $this->mapperMethodsTypeAdapterFactory,
			Serializer::class                               => $serializer,
			Type::class                                     => $type,
		];

		try {
			return $reflection->invokeStrict(
				$this->adapter,
				$value,
				...$reflection
					->parameters()
					->slice(1)
					->map(function (FunctionParameterReflection $parameter) use ($map) {
						Assert::isInstanceOf($parameter->type(), NamedType::class);
						Assert::keyExists($map, $parameter->type()->name);

						return $map[$parameter->type()->name];
					})
			);
		} catch (TypeError $e) {
			if (!str_contains($e->getMessage(), 'Argument #1')) {
				throw $e;
			}

			throw new UnexpectedValueTypeException($value, $reflection->parameters()->first()->type());
		}

//		$injected = [];
//		$parameters = $reflection->parameters()->slice(1);
//
//		/** @var FunctionParameterReflection $typeParameter */
//		$typeParameter = $parameters[0] ?? null;
//		if ($typeParameter && $typeParameter->type() instanceof NamedType && is_a($typeParameter->type()->name, Type::class, true)) {
//			$parameters = $parameters->slice(1);
//			$injected[] = $type;
//		}
//
//		foreach ($parameters as $parameter) {
//			$injected[] = $serializer->adapter(
//				typeAdapterType: PrimitiveTypeAdapter::class,
//				type: $parameter->type()->getTypes()[0], // todo: uhm?
//				attributes: $parameter->attributes()->all(),
//				skipPast: $type->equals($this->out) ? $this->mapperMethodsTypeAdapterFactory : null,
//			);
//		}
//
//		return $reflection->invoke($this->adapter, $value, ...$injected);
	}
}
