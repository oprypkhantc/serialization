<?php

namespace Tests\Stubs;

use GoodPhp\Serialization\TypeAdapter\Primitive\ClassProperties\Naming\SerializedName;
use TenantCloud\Standard\Optional\Optional;

/**
 * @template T
 */
class ClassStub
{
	/**
	 * @param T             $generic
	 * @param Optional<int> $optional
	 */
	public function __construct(
		public int $primitive,
		public NestedStub $nested,
		#[SerializedName('date')]
		public mixed $generic,
		public Optional $optional,
		public ?int $nullable,
	) {
	}
}
